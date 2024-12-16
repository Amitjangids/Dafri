<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cookie;
use Session;
use Redirect;
use Input;
use Validator;
use DB;
use IsAdmin;
use App\User;
use App\Agent;
use App\Fee;
use App\Admin;
use App\Transaction;
use App\GetInTouch;
use App\Support;
use App\Contact;
use App\Walletlimit;
use App\WithdrawRequest;
use App\InactiveAmount;
use App\Agentlimit;
use App\CryptoDeposit;
use App\CryptoWithdraw;
use App\ManualDeposit;
use App\ManualWithdraw;
use App\Notification;
use App\AgentsTransactionLimit;
use App\Models\Country;
use App\Models\Ngnexchange;
use Mail;
use PDF;
use App\Mail\SendMailable;
use GuzzleHttp;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Psr7;
use DateTime;
use Illuminate\Support\Facades\Crypt;
use Ramsey\Uuid\Uuid;
use gnupg;


class UsersController extends Controller {

    public function __construct() {
        $this->middleware('is_adminlogin');
    }

    public function createApplicant($externalUserId) {
        // https://developers.sumsub.com/api-reference/#creating-an-applicant
        $requestBody = [
            'externalUserId' => $externalUserId
        ];

        $url = '/resources/applicants?levelName=basic-kyc-level';
        $request = new GuzzleHttp\Psr7\Request('POST', SUMSUB_TEST_BASE_URL . $url);
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody(Psr7\Utils::streamFor(json_encode($requestBody)));

        $responseBody = $this->sendHttpRequest($request, $url)->getBody();
        return json_decode($responseBody)->{'id'};
    }

    public function sendHttpRequest($request, $url) {
        $client = new GuzzleHttp\Client();
        $ts = time();

        $request = $request->withHeader('X-App-Token', SUMSUB_APP_TOKEN);
        $request = $request->withHeader('X-App-Access-Sig', $this->createSignature($ts, $request->getMethod(), $url, $request->getBody()));
        $request = $request->withHeader('X-App-Access-Ts', $ts);

        // Reset stream offset to read body in `send` method from the start
        $request->getBody()->rewind();

//        try {
        $response = $client->send($request);
        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201) {
            echo "Error: " . $response->getStatusCode();
            exit;
        }
//        } catch (GuzzleHttp\Exception\GuzzleException $e) {
//            error_log($e);
//        }
//echo '<pre>';print_r($response);exit;
        return $response;
    }

    private function createSignature($ts, $httpMethod, $url, $httpBody) {
        return hash_hmac('sha256', $ts . strtoupper($httpMethod) . $url . $httpBody, SUMSUB_SECRET_KEY);
    }

    public function addDocument($applicantId, $imgURL, $idDocTyp, $country) {
        $countryCode = $this->getCountryCode($country);

        global $identityCardType;
        $idDocType = $identityCardType[$idDocTyp];
        $metadata = ['idDocType' => $idDocType, 'country' => $countryCode];
        $file = $imgURL;
//echo '<pre>';print_r($metadata);exit;
        $multipart = new MultipartStream([
            [
                "name" => "metadata",
                "contents" => json_encode($metadata)
            ],
            [
                'name' => 'content',
                'contents' => fopen($file, 'r')
            ],
        ]);

        $url = "/resources/applicants/" . $applicantId . "/info/idDoc";
        $request = new GuzzleHttp\Psr7\Request('POST', SUMSUB_TEST_BASE_URL . $url);
        $request = $request->withBody($multipart);

        return $this->sendHttpRequest($request, $url)->getHeader("X-Image-Id")[0];
    }

    private function generateQRCode($qrString, $user_id) {
        $output_file = 'uploads/qr-code/' . $user_id . '-qrcode-' . time() . '.png';
        $image = \QrCode::format('png')
                ->size(200)->errorCorrection('H')
                ->generate($qrString, base_path() . '/public/' . $output_file);
        //echo $output_file;exit;
        return $output_file;
    }

    public function index(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        DB::enableQueryLog();
        $pageTitle = 'Manage Personal Users';
        $activetab = 'actusers';
        $query = new User();
        $query = $query->sortable();
        //$query = $query->where('user_type', 'Personal');
        $role = 'Agent';
        $query = $query->orWhere(function ($q) use ($role) {
            $q->where('user_type', 'Personal')->orWhere('user_type', $role);
        });

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');

            if ($action == "Verify") {
                User::whereIn('id', $idList)->update(array('is_verify' => 1));
                Session::flash('success_message', "Records are activate successfully.");
            } else if ($action == "Unverify") {
                User::whereIn('id', $idList)->update(array('is_verify' => 0));
                Session::flash('success_message', "Records are deactivate successfully.");
            } else if ($action == "Delete") {
                User::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('phone', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                //$q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
        //DB::enableQueryLog();
        //$users = $query->orWhere('user_type','Agent')->orderBy('id', 'DESC')->paginate(20);
        $users = $query->orderBy('id', 'DESC')->paginate(20);
        //dd(DB::getQueryLog());

        if ($request->ajax()) {
            return view('elements.admin.users.index', ['allrecords' => $users]);
        }
        return view('admin.users.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $users]);
    }

    public function genrateAccNumber() {
        $adminInfo = DB::table('admins')->select('admins.last_account_number')->where('id', 1)->first();
        $accNumber = $adminInfo->last_account_number + 1;

        DB::table('admins')->where('id', 1)->update(['last_account_number' => $accNumber]);
        return $accNumber;
    }

    public function add() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'add-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Add Personal User';
        $activetab = 'actusers';

        $countrList = Country::getCountryList();
        $input = Input::all();

        if (!empty($input)) {
//            echo '<pre>';print_r($input);exit;
            $rules = array(
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'phone' => 'required|min:8|unique:users',
                'email' => 'required|email|unique:users',
                'addrs_line1' => 'required',
                'country' => 'required',
                'currency' => 'required',
                'identity_card_type' => 'required',
                'identity_image' => 'required|mimes:jpeg,png,jpg,pdf',
                'address_proof_type' => 'required',
                'address_document' => 'required|mimes:jpeg,png,jpg,pdf',
                'password' => 'required|min:8',
//                'confirm_password' => 'required|same:password',
                'image' => 'required|mimes:jpeg,png,jpg',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/add')->withErrors($validator)->withInput();
            } else {

                unset($input['phone_number']);
//                unset($input['contryCode']);

                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                    //$this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                    $input['image'] = $uploadedFileName;
                } else {
                    unset($input['image']);
                }

                if (Input::hasFile('identity_image')) {
                    $file = Input::file('identity_image');
                    $uploadedFileName = $this->uploadImage($file, DOCUMENT_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);
                    $input['identity_image'] = $uploadedFileName;
                } else {
                    unset($input['identity_image']);
                }

                if (Input::hasFile('address_document')) {
                    $file = Input::file('address_document');
                    $uploadedFileName = $this->uploadImage($file, DOCUMENT_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);
                    $input['address_document'] = $uploadedFileName;
                } else {
                    unset($input['address_document']);
                }


                $input['country_code'] = ucfirst(trim($input['contryCode']));
                unset($input['contryCode']);
                $input['first_name'] = ucfirst(strtolower($input['first_name']));
                $input['last_name'] = ucfirst(strtolower($input['last_name']));
                $serialisedData = $this->serialiseFormData($input);
                $serialisedData['slug'] = $this->createSlug($input['first_name'], 'users');
                $serialisedData['is_verify'] = 1;
                $serialisedData['otp_verify'] = 1;
                $serialisedData['user_type'] = 'Personal';
                $serialisedData['password'] = $this->encpassword($input['password']);

                $user_id = DB::getPdo()->lastInsertId();
                $accNum1 = time();
                $uidLength = 10 - strlen($user_id);

//                $uniqNum = $this->generateRandomNumber($uidLength);
//                $accountNumber = $uniqNum . ($user_id);

                $accountNumber = $this->genrateAccNumber();
                $serialisedData['account_number'] = $accountNumber;
                User::insert($serialisedData);

                $user_id = DB::getPdo()->lastInsertId();

                $userInfo = User::where('id', $user_id)->first();
                if (Input::hasFile('identity_image')) {


                    if ($userInfo->kyc_applicant_id == '') {
                        $applicantId = $this->createApplicant($userInfo->id);
                        User::where('id', $userInfo->id)->update(['kyc_applicant_id' => $applicantId, 'updated_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $applicantId = $userInfo->kyc_applicant_id;
                    }

                    $imgURL = DOCUMENT_FULL_UPLOAD_PATH . $input['identity_image'];
                    $idDocTyp = $input['identity_card_type'];
                    $country = $userInfo->country;
                    $imgID = $this->addDocument($applicantId, $imgURL, $idDocTyp, $country);
                }

//                $name = $input['first_name'];
//                $emailId = $input['email'];
                $new_password = $input['password'];
//
//                $emailTemplate = DB::table('emailtemplates')->where('id', 2)->first();
//                $toRepArray = array('[!email!]', '[!name!]', '[!username!]', '[!password!]', '[!HTTP_PATH!]', '[!SITE_TITLE!]');
//                $fromRepArray = array($emailId, $name, $name, $new_password, HTTP_PATH, SITE_TITLE);
//                $emailSubject = str_replace($toRepArray, $fromRepArray, $emailTemplate->subject);
//                $emailBody = str_replace($toRepArray, $fromRepArray, $emailTemplate->template);
//                Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                $emailId = $input['email'];
                $userName = strtoupper($input['first_name']);
                $userName1 = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);

//                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2"><span>Hey </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Welcome to DafriBank<br><br>Your account has been created successfully by admin<br><br>Details are below :<br><br><strong>Email Address:</strong> ' . $emailId . '<br><br><strong>Password:</strong> ' . $new_password . '<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© '.date("Y").' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Hey </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Welcome to DafriBank Digital<br><br>Digital Banking means banking on the go. Anytime, anywhere and we are delighted you chose us as your financial institution.<br><br>With the DafriBank Digital superior technology, bank with ease in a totally secure online environment. It\'s faster and cheaper than banking in a branch.<br><br>As a customer-centric bank, we are open to your feedback, hence, please do not hesitate to contact us anytime through our e-mail <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>.<br><br>Regards,<br>The DafriBank Digital Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/personal-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                $emailSubject = "Welcome Onboard";
//                Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                $emailSubject = "Welcome Onboard";
//                     $emailData['subjects'] = $emailSubject;
//                        $emailData['userName'] = $userName;
//                        $emailData['emailId'] = $emailId;
//                        Mail::send('emails.onBoarding2', $emailData, function ($message)use ($emailData, $emailId) {
//                        $message->to($emailId, $emailId)
//                                ->subject($emailData['subjects']);
//                         });

                $account_number = $userInfo->account_number;

                $detl = array();
                $detl["date"] = date("d/m/Y");
                $detl["userName"] = 'MR/MRS ' . $userName1;
                $detl["account_number"] = $account_number;
                $detl["acc_type"] = strtolower($userInfo->user_type);
                $detl["address"] = $userInfo->addrs_line1;
                $detl["address2"] = $userInfo->addrs_line2;

                view()->share(['detl' => $detl]);

                $customPaper = array(0, 0, 720, 1440);
                $pdf = PDF::loadView('emails.onBoardPdf')->setPaper($customPaper, 'portrait');
                //                        $pdf = PDF::loadView('fundtransferPdf');

                $emailData['subjects'] = $emailSubject;
                $emailData['userName'] = $userName;
                $emailData['emailId'] = $emailId;
                Mail::send('emails.onBoarding2', $emailData, function ($message)use ($emailData, $emailId, $pdf, $account_number) {
                    $message->to($emailId, $emailId)
                            ->subject($emailData['subjects'])
                            ->attachData($pdf->output(), $account_number . ".pdf");
                });

                if ($input['currency'] != 'USD') {
                    $currency_10_usd = $this->myCurrencyRate($input['currency'], 10);
                    $currency_10_usd = ceil($currency_10_usd);
                    $usdString = '(' . $input['currency'] . ' ' . $currency_10_usd . ') ';
                } else {
                    $usdString = '';
                }

                $bodyEmail = '';
                $bodyEmail .= '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Greetings</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Thank you for opening a DafriBank account. Please deposit a minimum of USD 10 ' . $usdString . 'within the next 10 days to ensure your account stays active. Visit and <a href="' . HTTP_PATH . '/business-login" target="_blank">sign in</a> to your DafriBank account to see the available funding methods.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                $subjectEmail = "Keep your DafriBank account active!";
                $emailData['subject'] = $subjectEmail;
                $emailData['userName'] = strtoupper($userName);
                $emailData['emailId'] = $emailId;
                $emailData['usdString'] = $usdString;
                Mail::send('emails.onBoarding', $emailData, function ($message)use ($emailData, $emailId) {
                    $message->to($emailId, $emailId)
                            ->subject($emailData['subject']);
                });
//                $subjectEmail = "Keep your DafriBank Account Active!";
//                Mail::to($emailId)->send(new SendMailable($bodyEmail, $subjectEmail, Null));

                User::where('email', $input['email'])->update(['edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

                Session::flash('success_message', "Personal user details saved successfully.");
                return Redirect::to('admin/users');
            }
        }
        return view('admin.users.add', ['title' => $pageTitle, $activetab => 1, 'countrList' => $countrList]);
    }

    private function myCurrencyRate($currency, $amount) {
        
        if($currency == 'NGN'){
            $exchange = Ngnexchange::where('id', 1)->first();
            
            $val = $exchange->usd_value;
            $total = $val * $amount;
            return $total;
        } else{
            $apikey = '1c5849e1679846c9ac66887bbdd4d76f';
            if ($currency == 'EURO') {
                $query = "USD_EUR";
            } else {
                $query = "USD_" . $currency;
            }
            $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;

            $json = file_get_contents($curr_req);
            $obj = json_decode($json, true);
            $val = floatval($obj[$query]);
            $total = $val * $amount;
            return $total;
        }        
    }

    private function generateRandomNumber($length) {
        $number = '1234567890';
        $numberLength = strlen($number);
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= $number[rand(0, $numberLength - 1)];
        }
        return $randomNumber;
    }

    public function edit($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Edit Personal User';
        $activetab = 'actusers';
        $countrList = Country::getCountryList();

        $recordInfo = User::where('slug', $slug)->first();
        if (empty($recordInfo)) {
            return Redirect::to('admin/users');
        }

        $input = Input::all();
        if (!empty($input)) {
//            echo '<pre>';print_r($input);exit;
            $rules = array(
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
//                'phone' => 'required|min:8|unique:users',
                'password' => 'sometimes|nullable|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[\W_]/',
                'country' => 'required',
                'currency' => 'required',
                'image' => 'mimes:jpeg,png,jpg',
            );
            $customMessages = [
                'password.regex' => 'Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return Redirect::to('/admin/users/edit/' . $slug)->withErrors($messages)->withInput();
            } else {
                DB::enableQueryLog();
                $userEmail = $input['email'];
                $userPhone = $input['phone'];
                $query = new User();

                $query = $query->where(function ($q) use ($userEmail, $userPhone) {
                    $q->where('email', $userEmail)->orWhere('phone', $userPhone);
                });

                /* $query = $query->where(function($q) use ($userPhone) {
                  $q->orWhere('phone', $userPhone);
                  }); */

                $isExists = $query->where('id', '!=', $recordInfo->id)->first();
                //dd(DB::getQueryLog());
                //$isExists = User::where('email',$input['email'])->orWhere('phone',$input['phone'])->where('id', '!=', $recordInfo->id)->first();

                if (!empty($isExists)) {
                    Session::flash('error_message', "Email/Phone already exists.");
                    return Redirect::to('/admin/users/edit/' . $slug);
                }
                if (Input::hasFile('identity_image')) {
                    $file = Input::file('identity_image');
                    $uploadedFileName = $this->uploadImage($file, DOCUMENT_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);
                    $input['identity_image'] = $uploadedFileName;
                } else {
                    unset($input['identity_image']);
                }

                if (Input::hasFile('address_document')) {
                    $file = Input::file('address_document');
                    $uploadedFileName = $this->uploadImage($file, DOCUMENT_FULL_UPLOAD_PATH);
//                    $this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);
                    $input['address_document'] = $uploadedFileName;
                } else {
                    unset($input['address_document']);
                }
                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                    //$this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                    $input['image'] = $uploadedFileName;
                    @unlink(PROFILE_FULL_UPLOAD_PATH . $recordInfo->profile_image);
                } else {
                    unset($input['image']);
                }

                if ($input['password']) {
                    $input['password'] = $this->encpassword($input['password']);
                } else {
                    unset($input['password']);
                }

                $input['edited_by'] = Session::get('adminid');
                $input['phone'] = $input['phone'];
                $input['first_name'] = ucfirst(strtolower($input['first_name']));
                $input['last_name'] = ucfirst(strtolower($input['last_name']));
                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                User::where('id', $recordInfo->id)->update($serialisedData);

                if (Input::hasFile('identity_image')) {
                    $user_id = $recordInfo->id;

                    $userInfo = User::where('id', $user_id)->first();

                    if ($userInfo->kyc_applicant_id == '') {
                        $applicantId = $this->createApplicant($userInfo->id);
                        User::where('id', $userInfo->id)->update(['kyc_applicant_id' => $applicantId, 'updated_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $applicantId = $userInfo->kyc_applicant_id;
                    }

                    $imgURL = DOCUMENT_FULL_UPLOAD_PATH . $input['identity_image'];
                    $idDocTyp = $input['identity_card_type'];
                    $country = $userInfo->country;
                    $imgID = $this->addDocument($applicantId, $imgURL, $idDocTyp, $country);
                }

                Agent::where('user_id', $recordInfo->id)->update(['email' => $userEmail]);
                Session::flash('success_message', "Personal user details updated successfully.");
                return Redirect::to('admin/users');
            }
        }
        return view('admin.users.edit', ['title' => $pageTitle, $activetab => 1, 'countrList' => $countrList, 'recordInfo' => $recordInfo]);
    }

    public function kycdetail($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'View User KYC Detail';
        $activetab = 'actusers';
        $userInfo = User::where('slug', $slug)->first();

        return view('admin.users.kycdetail', ['title' => $pageTitle, $activetab => 1, 'userInfo' => $userInfo]);
    }

    public function approvekyc($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        if ($slug) {
            User::where('slug', $slug)->update(array('is_kyc_done' => '1', 'is_verify' => '1', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

            $userInfo = DB::table('users')->where('slug', $slug)->first();

            $username = strtoupper($userInfo->first_name);
            $emailId = $userInfo->email;

            if (strtolower($userInfo->user_type) == "personal") {
                $lognLnk = HTTP_PATH . "/personal-login";
            } else {
                $lognLnk = HTTP_PATH . "/business-login";
            }

            $emailSubject = 'KYC information has been reviewed successfully';
            //$emailBody = 'Dear '.$username.',<br><br>We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank '.$userInfo->user_type.' account has now been approved. <a href="'.$lognLnk.'" target="_blank">Click here</a> to log in to your account.<br><br>We wish you an awesome banking experience with us.<br><br>Best regards,<br>The DafriBank Team';
            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400;color: #A2A2A2"><span>Dear </span> ' . $username . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank ' . $userInfo->user_type . ' account has now been approved. <a href="' . $lognLnk . '" target="_blank">Click here</a> to log in to your account.<br><br>We wish you an awesome banking experience with us.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['username'] = strtoupper($username);

            Mail::send('emails.kycReviewd', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });

            Session::flash('success_message', "User KYC approved successfully.");
            return Redirect::to('admin/users/kycdetail/' . $slug);
        }
    }

    public function declinekyc($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        if ($slug) {
            User::where('slug', $slug)->update(array('is_kyc_done' => '2', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

            $userInfo = DB::table('users')->where('slug', $slug)->first();

            $username = strtoupper($userInfo->first_name);
            $emailId = $userInfo->email;

            if (strtolower($userInfo->user_type) == "personal") {
                $lognLnk = HTTP_PATH . "/personal-login";
            } else {
                $lognLnk = HTTP_PATH . "/business-login";
            }

            $emailSubject = "Your KYC information was not approved";
            //$emailBody = "Dear ".$username."<br><br>Unfortunately, your KYC information was not approved. Please <a href='".$lognLnk."' target='_blank'>login</a> to your account and re-submit information in the sections that are marked as rejected. Once you do that, we will review the information and inform you via email about your status.<br><br>We look forward to receiving your new KYC information.<br><br>Best regards,<br>The DafriBank Team";
            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Dear </span> ' . $username . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your KYC information was not approved. Please <a href="' . $lognLnk . '" target="_blank">login</a> to your account and re-submit information in the sections that are marked as rejected. Once you do that, we will review the information and inform you via email about your status.<br><br>We look forward to receiving your new KYC information.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['username'] = strtoupper($username);

            Mail::send('emails.kycDeclined', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });

            Session::flash('success_message', "User KYC declined successfully.");
            return Redirect::to('admin/users/kycdetail/' . $slug);
        }
    }

    public function activate($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        /* if ($isPermitted == false) {
          $pageTitle = 'Not Permitted';
          $activetab = 'actchangeusername';
          return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);
          } */
        if ($slug && $isPermitted == true) {
            User::where('slug', $slug)->update(array('is_verify' => '1', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));
            return view('elements.admin.update_status', ['action' => 'admin/users/deactivate/' . $slug, 'status' => 1, 'id' => $slug]);
        } else {
            Session::flash('error_message', "You don't have permission to perform action on this page.");
            return Redirect::to('admin/users');
            //return view('elements.admin.update_status', ['action' => 'admin/users/activate/' . $slug, 'status' => 0, 'id' => $slug]);	
        }
    }

    public function deactivate($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        /* if ($isPermitted == false) {
          $pageTitle = 'Not Permitted';
          $activetab = 'actchangeusername';
          return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);
          } */
        if ($slug && $isPermitted == true) {
            User::where('slug', $slug)->update(array('is_verify' => '0'));
            return view('elements.admin.update_status', ['action' => 'admin/users/activate/' . $slug, 'status' => 0, 'id' => $slug]);
        } else {
            Session::flash('error_message', "You don't have permission to perform action on this page.");
            return Redirect::to('admin/users');
        }
    }

    /* public function delete($slug = null) {
      $isPermitted = $this->validatePermission(Session::get('admin_role'),'edit-personal-user');
      if ($isPermitted == false) {
      $pageTitle = 'Not Permitted';
      $activetab = 'actchangeusername';
      return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);
      }
      if ($slug) {
      User::where('slug', $slug)->delete();
      Session::flash('success_message', "Personal user details deleted successfully.");
      return Redirect::to('admin/users');
      }
      } */

      public function deleteimage($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        if ($slug) {
            $recordInfo = DB::table('users')->where('slug', $slug)->select('users.image')->first();
            User::where('slug', $slug)->update(array('image' => ''));
            @unlink(PROFILE_FULL_UPLOAD_PATH . $recordInfo->image);
            Session::flash('success_message', "Image deleted successfully.");
            return Redirect::to('admin/users/edit/' . $slug);
        }
    }

    public function deleteidentity($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        if ($slug) {
            $recordInfo = DB::table('users')->where('slug', $slug)->select('users.identity_image')->first();
            User::where('slug', $slug)->update(array('identity_image' => ''));
            @unlink(IDENTITY_FULL_UPLOAD_PATH . $recordInfo->identity_image);
            Session::flash('success_message', "Image deleted successfully.");
            return Redirect::to('admin/users/edit/' . $slug);
        }
    }

    private function fetchCurrencyRate($currency, $amount) {

        if ($currency != 'USD') {
            if ($currency = 'NGN') {
                $exchange = Ngnexchange::where('id', 1)->first();
                $to = strtolower('USD');
                $var = $to . '_value';

                $val = $exchange->$var;
                $total = $amount / $val;
                return $total;
            } else {
                $apikey = '1c5849e1679846c9ac66887bbdd4d76f';
                                 
                if ($currency == 'EURO') {
                    $query = "EUR_USD";
                } else {
                    $query = $currency . "_USD";
                }
                $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
                //"https://api.currconv.com/api/v7/convert?q=".$query."&compact=ultra&apiKey=".$apikey	
                $json = file_get_contents($curr_req);
                $obj = json_decode($json, true);
                //print_R($obj);
                $val = floatval($obj[$query]);
                $total = $val * $amount;
                return $total;
            }
        } else {
            return $amount;
        }
    }

    public function editAgentDetails($agent_id = null) {
        $pageTitle = 'Edit DafriBank Agent';
        $agent = Agent::where('id', $agent_id)->first();
        $recordInfo = User::where('id', $agent->user_id)->first();
        $input = Input::all();

//        echo '<pre>';print_r($agent);exit;
        if (!empty($input)) {
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'country' => 'required',
                'commission' => 'required|numeric',
                'min_amount' => 'required|numeric',
                'address' => 'required',
                'phone' => 'required|numeric',
                'payment_methods' => 'required',
                //'email' => 'required|email',
                'description' => 'required',
//                'profile_image' => 'max:10000|mimes:jpeg,png,jpg,gif'
            );
            $customMessages = [
                'first_name.required' => 'First name field can\'t be left blank',
                'last_name.required' => 'Last name field can\'t be left blank',
                'country.required' => 'Country name field can\'t be left blank',
                'commission.required' => 'Commission field can\'t be left blank',
                'min_amount.required' => 'Minimum Deposit field can\'t be left blank',
                'address.required' => 'Address field can\'t be left blank',
                'phone.required' => 'Phone number field can\'t be left blank',
                'payment_methods.required' => 'Payment Method field can\'t be left blank',
                'email.required' => 'Email field can\'t be left blank',
                'email.email' => 'Invalid Email!',
                'description.required' => 'Description field can\'t be left blank',
//                'profile_image.required' => 'Profile image field can\'t be left blank',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            //$validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                $message = implode('<br>', $messages->all());
//                echo '<pre>';print_r($input);exit;

                Session::put('error_session_message', $message);
//                return Redirect::to('users/editagent/' . $agent->id);
                return Redirect::to('users/editagent/' . $agent->id)->withErrors($validator)->withInput();
            } else {
                if ($input['commission'] < 2 or $input['commission'] > 9) {
                    Session::flash('error_message', "Commission rate should be between 2% to 9%.");
//                    Session::put('error_session_message', "Commission rate should be between 2% to 9%.");
                    return Redirect::to('admin/users/editagent/' . $agent->id);
                }
                $user_wallet_usd = $this->fetchCurrencyRate($recordInfo->currency, $recordInfo->wallet_amount);
                $isExists = Agent::where('user_id', Session::get('user_id'))->first();
//                if (!empty($isExists)) {
////                    Session::flash('error_message', "Your request already exists. We will update you soon.");
//                    Session::put('error_session_message', "Your request already exists. We will update you soon.");
//                    return Redirect::to('auth/edit-agent-details');
//                } else 
                if ($user_wallet_usd < 500) {
                    Session::flash('error_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > USD 500.");
//                    Session::put('error_session_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > USD 500.");
                    return Redirect::to('admin/users/editagent/' . $agent->id);
                } else if ($recordInfo->is_kyc_done != 1) {
                    Session::flash('error_message', "Your request not accepted as your KYC is not completed.");
//                    Session::put('error_session_message', "Your request not accepted as your KYC is not completed.");
                    return Redirect::to('admin/users/editagent/' . $agent->id);
                } else {
                    if (Input::hasFile('profile_image')) {
                        $file = Input::file('profile_image');
                        $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                        //$this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);
                        $profile_image = $uploadedFileName;
                        $input['profile_image'] = $profile_image;
                    } else {
                        unset($input['profile_image']);
                    }

                    $input['first_name'] = ucfirst(strtolower($input['first_name']));
                    $input['last_name'] = ucfirst(strtolower($input['last_name']));
//                      echo '<pre>';print_r($input);exit;
                    $serialisedData = $this->serialiseFormData($input, 1);
                    Agent::where('id', $agent->id)->update($serialisedData);
//                     echo '<pre>';print_r($serialisedData);exit;
                    Session::flash('success_message', "Agent request saved successfully. We will update you soon.");
//                    Session::put('success_session_message', "Agent request updated successfully.");
                    return Redirect::to('admin/users/editagent/' . $agent->id);
                }
            }
        }

        $countrList = Country::getCountryList();

        return view('admin.users.editAgentDetails', ['title' => $pageTitle, 'recordInfo' => $recordInfo, 'countrList' => $countrList, 'agent' => $agent]);
    }

    public function agentRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-agent-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Bank Agent Requests';
        $activetab = 'actbankagntreq';
        $query = new Agent();
        $query = $query->sortable();

        if ($request->has('chkRecordId') && $request->has('action')) {
            $idList = $request->get('chkRecordId');
            $action = $request->get('action');

            if ($action == "Verify") {
                Agent::whereIn('id', $idList)->update(array('is_approved' => 1));
                $getUserID = Agent::whereIn('id', $idList)->get();

                foreach ($getUserID as $userId) {
                    User::where('id', $userId->user_id)->update(array('user_type' => 'Agent'));
                }

                Session::flash('success_message', "Records are activate successfully.");
            } else if ($action == "Unverify") {
                Agent::whereIn('id', $idList)->update(array('is_approved' => 2));

                $getUserID = Agent::whereIn('id', $idList)->get();

                foreach ($getUserID as $userId) {
                    $user = User::where('id', $userId->user_id)->first();
                    if ($user->first_name != "") {
                        User::where('id', $userId->user_id)->update(array('user_type' => 'Personal'));
                    } else {
                        User::where('id', $userId->user_id)->update(array('user_type' => 'Business'));
                    }
                }

                Session::flash('success_message', "Records are deactivate successfully.");
            } else if ($action == "Delete") {
                Agent::whereIn('id', $idList)->delete();
                Session::flash('success_message', "Records are deleted successfully.");
            }
        }

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
            //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');
            $query = $query->where(function ($q) use ($keyword) {
                $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('email', 'LIKE', "%" . $keyword . "%");
            });
        }

        $agents = $query->orderBy('created_at', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.agentRequest', ['agents' => $agents]);
        }
        return view('admin.users.bankAgentRequest', ['title' => $pageTitle, $activetab => 1, 'agents' => $agents]);
    }

    public function approveAgent($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-agent-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        if ($slug) {
            Agent::where("id", $slug)->update(['is_approved' => 1, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

            $user = Agent::where("id", $slug)->first();

            User::where('id', $user->user_id)->update(['user_type' => 'Agent', 'updated_at' => date('Y-m-d H:i:s')]);
            $emailId = $user->email;
            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="150"></a> </td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hello</span> ' . $user->first_name . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Thank you for your interest. Your agent request has been approved.<br><br>Please re-login your account to avail the benefits of agent account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table> </body></html>';
            $emailSubject = "Your agent request has been approved";
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['username'] = strtoupper($user->first_name);

            Mail::send('emails.agentRequestApprove', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });

            Session::flash('success_message', "Request approved successfully.");
            return Redirect::to('admin/users/bank-agent-request');
        }
    }

    public function disapproveAgent($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-agent-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Disapprove Agent';
        $activetab = 'actbankagntreq';

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'reason' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/disapproveAgent/' . $slug)->withErrors($validator)->withInput();
            } else {
                //Agent::where("id",$slug)->update(['is_approved'=>2,'updated_at'=>date('Y-m-d H:i:s')]);
                Agent::where("id", $slug)->update(['is_approved' => 2, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                $userID = Agent::where("id", $slug)->first();
                Agent::where("id", $slug)->delete();

                $user = User::where("id", $userID->user_id)->first();
                if ($user->first_name != "") {
                    User::where('id', $userID->user_id)->update(['user_type' => 'Personal', 'updated_at' => date('Y-m-d H:i:s')]);
                } else {
                    User::where('id', $userID->user_id)->update(['user_type' => 'Business', 'updated_at' => date('Y-m-d H:i:s')]);
                }

                $emailId = $userID->email;

                $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="150"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hello</span> ' . $userID->first_name . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Thank you for your interest. Your agent request has been declined due to below reason:<br>Reason: ' . $input['reason'] . '<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                $emailSubject = "DafriBank Digital | Your Agent request has been declined";
//                Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                $emailData['subject'] = $emailSubject;
                $emailData['username'] = strtoupper($userID->first_name);
                $emailData['reason'] = $input['reason'];

                Mail::send('emails.agentRequestDecline', $emailData, function ($message)use ($emailData, $emailId) {
                    $message->to($emailId, $emailId)
                            ->subject($emailData['subject']);
                });
                Session::flash('success_message', "Request disapproved successfully.");
                return Redirect::to('admin/users/bank-agent-request');
            }
        }
        return view('admin.users.disapproveAgent', ['title' => $pageTitle, $activetab => 1]);
    }

    public function transactionLists(Request $request, $slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'view-transaction-report');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $userInfo = User::where("slug", $slug)->first();

        DB::enableQueryLog();
        $pageTitle = 'Transaction Report';
        $activetab = 'acttransactionreport';
        $query = new Transaction();
        $admin = Admin::all();
        $query = $query->sortable();
//        $query = $query->where('user_id',$userInfo->id)->orWhere('user_id',$userInfo->id);


        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');

            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                            //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'like', '%' . $keyword . '%')->orWhere('business_name', 'like', '%' . $keyword . '%');
//                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%");
                        })
                        ->orWhereHas('Receiver', function ($q) use ($keyword) {
                            //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                            $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%");
                        });
            });
        } else {
            $keyword = '';
        }

        if ($request->has('date') && $request->get('date')) {
            
            $dateArr = explode("/", $request->get('date'));
//            echo '<pre>';print_r($_GET);exit;
            //echo "Date Range: ".$request->get('date');
            $to = $dateArr[0] . " 00:00:00";
            $from = $dateArr[1] . " 23:59:59";
            //echo "To Date: ".$to." :: From Date: ".$from; exit;
            $query = $query->where(function ($q) use ($to, $from) {
                $q->whereBetween("created_at", array($to, $from));
            });

            $toDate = $dateArr[0];
            $frmDate = $dateArr[1];
        } else {
            $transCalDays = 30;
            $chkTransDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $transCalDays . ' days'));
            $to = $chkTransDate . " 00:00:00";
            $from = date('Y-m-d') . " 23:59:59";

            $query = $query->where(function ($q) use ($to, $from) {
                $q->whereBetween("created_at", array($to, $from));
            });
            $toDate = $chkTransDate;
            $frmDate = date('Y-m-d');
        }

        if ($request->has('srch_currency') && $request->get('srch_currency')) {
            $currency = $request->get('srch_currency');
            if ($currency != '-1') {
                $query = $query->where(function ($q) use ($currency) {
                    $q->where("currency", $currency);
                });
            }
        } else {
            $currency = 'all';
        }

        $ussId = $userInfo->id;
        $query = $query->where(function ($q) use ($ussId) {
            $q->where('user_id', $ussId)->orWhere('receiver_id', $ussId);
        });
//        $query = $query->where('user_id',$userInfo->id)->orWhere('receiver_id',$userInfo->id);
        $trans = $query->orderBy("id", "DESC")->paginate(25);
//        echo '<pre>';print_r($trans);exit;
        //dd(DB::getQueryLog());
        if ($request->ajax()) {
            return view('elements.admin.users.transactionLists', ['allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'userInfo' => $userInfo, 'admin' => $admin]);
        }

        return view('admin.users.transactionLists', ['title' => $pageTitle, 'slug' => $slug, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'userInfo' => $userInfo, 'admin' => $admin]);
    }

    public function transactionReport(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'view-transaction-report');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        DB::enableQueryLog();
        $pageTitle = 'Transaction Report';
        $activetab = 'acttransactionreport';
        $query = new Transaction();
        $query = $query->sortable();

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
           // echo $keyword; die;
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                            //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%');
//                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%");
                        })
                        ->orWhereHas('Receiver', function ($q) use ($keyword) {
                            //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                            $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%");
                        });
            });
//            $query = $query->where(function ($q) use ($keyword) {
//                $q->orWhereHas('Receiver', function ($q) use ($keyword) {
//                    //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
//                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%");
//                });
//            });
        } else {
            $keyword = '';
        }

        if ($request->has('date') && $request->get('date')) {
            $dateArr = explode("/", $request->get('date'));
          //  echo "Date Range: ".$request->get('date');
         //   print_r($dateArr);
            $to = $dateArr[0] . " 00:00:00";
            $from = $dateArr[1] . " 23:59:59";
            //echo "To Date: ".$to." :: From Date: ".$from; exit;
            $query = $query->where(function ($q) use ($to, $from) {
                $q->whereBetween("created_at", array($to, $from));
            });

            $toDate = $dateArr[0];
            $frmDate = $dateArr[1];
        } else {
            $transCalDays = 30;
            $chkTransDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $transCalDays . ' days'));
            $to = $chkTransDate . " 00:00:00";
            $from = date('Y-m-d') . " 23:59:59";

            $query = $query->where(function ($q) use ($to, $from) {
                $q->whereBetween("created_at", array($to, $from));
            });
            $toDate = $chkTransDate;
            $frmDate = date('Y-m-d');
        }

        if ($request->has('srch_currency') && $request->get('srch_currency')) {
            $currency = $request->get('srch_currency');
            if ($currency != '-1') {
                $query = $query->where(function ($q) use ($currency) {
                    $q->where("currency", $currency);
                });
            }
        } else {
            $currency = 'all';
        }

        $trans = $query->orderBy("id", "DESC")->paginate(25);
        $admin = Admin::all();
        //dd(DB::getQueryLog());
        if ($request->ajax()) {
            return view('elements.admin.users.transReport', ['allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'admin' => $admin]);
        }

        return view('admin.users.transactionReport', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'admin' => $admin]);
    }

    public function exportCSV($keyword, $dateRange, $currency) {
        DB::enableQueryLog();
        $query = new Transaction();
        $query = $query->sortable();

        if ($keyword != "na") {
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%");
                });
            });
        }

        if ($dateRange != "") {
            $dateArr = explode(" - ", $dateRange);
            $to = $dateArr[0] . " 00:00:00";
            $from = $dateArr[1] . " 23:59:59";

            $query = $query->where(function ($q) use ($to, $from) {
                $q->whereBetween("created_at", array($to, $from));
            });
        }

        if ($currency != "-1") {
            $query = $query->where(function ($q) use ($currency) {
                $q->where("currency", $currency);
            });
        }

        $trans = $query->orderBy("id", "DESC")->get();
        //dd(DB::getQueryLog());

        $delimiter = ",";
        $f = fopen('php://memory', 'w'); //Save report in csv
        header('Content-Type: application/csv');
        $filename = "transfer-report-" . date('d-m-Y H:i:s') . ".csv";
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        ob_start();
        $line = array("Trans. ID", "Sender", "Receiver", "Currency", "Amount", "Fees", "Trans. Type", "Ref ID", "Status", "Date");
        fputcsv($f, $line, $delimiter);
        //print_r($trans); //exit;
        foreach ($trans as $tran) {
            if ($tran->receiver_id > 0) {
                $res = $this->getUserByUserId($tran->receiver_id);
            } else {
                $res = $this->getUserByUserId($tran->user_id);
            }

            $userTyp = $this->getUserType($tran->user_id);
            if ($userTyp == false) {
                continue;
            }
            $TransId = $tran->id;
            //Sender Start
            if ($userTyp == 'Personal')
                $sender = isset($tran->User->first_name) ? ucwords(strtolower($tran->User->first_name)) . ' ' . ucwords(strtolower($tran->User->last_name)) : 'N/A';
            elseif ($userTyp == 'Business')
                $sender = $tran->User->director_name;
            elseif ($userTyp == 'Agent')
                $sender = isset($tran->User->first_name) ? ucwords(strtolower($tran->User->first_name)) . ' ' . ucwords(strtolower($tran->User->last_name)) : $tran->User->director_name;
            //Sender End
            //Receiver Start
            if ($res != false && $res->user_type == 'Personal')
                $receiver = strtoupper(strtolower($res->first_name)) . " " . ucwords(strtolower($res->last_name));
            elseif ($res != false && $res->user_type == 'Business')
                $receiver = strtoupper(strtolower($res->director_name));
            elseif ($res != false && $res->user_type == 'Agent' && $res->first_name != "")
                $receiver = strtoupper(strtolower($res->first_name)) . " " . ucwords(strtolower($res->last_name));
            elseif ($res != false && $res->user_type == 'Agent' && $res->director_name != "")
                $receiver = strtoupper(strtolower($res->director_name));
            else {
                $agent = $this->getAgentById($tran->receiver_id);
                if ($agent != false) {
                    $transFnm = strtoupper($agent->first_name);
                    $transLnm = strtoupper($agent->last_name);
                } else {
                    $transFnm = "N/A";
                    $transLnm = "";
                }
                $receiver = strtoupper(strtolower($transFnm)) . " " . strtoupper(strtolower($transLnm));
            }
            //Receiver End
            $currency = $tran->currency;
            $amount = number_format($tran->amount, 2, '.', ',');
            $fees = number_format($tran->fees, 2, '.', ',');
            $paymentTypArr = explode('##', $tran->trans_for);
            $paymentType = $paymentTypArr[0];
            if ($tran->refrence_id == 'na') {
                $refID = 'N/A';
            } else {
                $refID = $tran->refrence_id;
            }
            if ($tran->status == 1)
                $status = 'Success';
            elseif ($tran->status == 2)
                $status = 'Pending';
            elseif ($tran->status == 3)
                $status = 'Cancelled';
            elseif ($tran->status == 4)
                $status = 'Failed';
            elseif ($tran->status == 5)
                $status = 'Error';
            elseif ($tran->status == 6)
                $status = 'Abandoned';
            elseif ($tran->status == 7)
                $status = 'PendingInvestigation';

            $created_at = $tran->created_at->format('M d, Y h:i A');
            //"Trans. ID",Sender,Receiver,Currency,Amount,Fees,"Trans. Type","Ref ID",Status,Date
            $line = array($TransId, $sender, $receiver, $currency, $amount, $fees, $paymentType, $refID, $status, $created_at);
            fputcsv($f, $line, $delimiter);
        }//Foreach loop end
        fseek($f, 0);
        //ob_end_clean();
        fpassthru($f);
        fclose($f);
        ob_flush();
        exit;
    }

    private function getAgentById($agent_id) {
        $resultData = DB::table('agents')->select('agents.*')->where('id', '=', $agent_id)->first();
        if (!empty($resultData)) {
            return $resultData;
        } else {
            return false;
        }
    }

    private function getUserType($user_id) {
        $res = DB::table("users")->select("users.user_type")->where("id", $user_id)->first();
        if (!empty($res))
            return $res->user_type;
        else
            return false;
    }

    private function getUserByUserId($user_id) {
        $user = User::where('id', $user_id)->first();
        if (!empty($user)) {
            return $user;
        } else {
            return false;
        }
    }

    public function listFees(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-fees');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actconfigurefees';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        //echo "Role: ".Session::get('admin_role');
        $pageTitle = 'Configure Fees';
        $activetab = 'actconfigurefees';
        $query = new Fee();
        $query = $query->sortable();
        $query = $query->where('fees_for', 1);

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');

            $query = $query->where(function ($q) use ($keyword) {
                $q = $q->where('fee_name', 'LIKE', "%" . $keyword . "%")->orWhere('fee_value', 'LIKE', "%" . $keyword . "%");
            });
        } else {
            $keyword = '';
        }

        $fees = $query->orderBy("id", "ASC")->paginate(25);

        if ($request->ajax()) {
            return view('elements.admin.users.feesList', ['allrecords' => $fees, 'keyword' => $keyword]);
        }

        return view('admin.users.listFees', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $fees, 'keyword' => $keyword]);
    }

    public function listMerchantFees(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-fees');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actconfiguremerchantfees';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        //echo "Role: ".Session::get('admin_role');
        $pageTitle = 'Configure Merchant Fees';
        $activetab = 'actconfiguremerchantfees';
        $query = new Fee();
        $query = $query->sortable();
        $query = $query->where('fees_for', 2);

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');

            $query = $query->where(function ($q) use ($keyword) {
                $q = $q->where('fee_name', 'LIKE', "%" . $keyword . "%")->orWhere('fee_value', 'LIKE', "%" . $keyword . "%");
            });
        } else {
            $keyword = '';
        }

        $fees = $query->orderBy("id", "ASC")->paginate(25);

        if ($request->ajax()) {
            return view('elements.admin.users.feesListBusiness', ['allrecords' => $fees, 'keyword' => $keyword]);
        }

        return view('admin.users.listFeesBusiness', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $fees, 'keyword' => $keyword]);
    }

    public function editFees($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-fees');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Edit Fees';
        $activetab = 'actconfigurefees';
        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'fee_name' => 'required',
                'fee_value' => 'required',
            );
            $customMessages = [
                'fee_name.required' => 'Fees name field can\'t be left blank.',
                'fee_value.required' => 'Fees value field can\'t be left blank.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/fees/editFees/' . $slug)->withErrors($validator)->withInput();
            } else {
                Fee::where('id', $slug)->update(['fee_value' => $input['fee_value'], 'edited_by' => Session::get('adminid')]);
                Session::flash('success_message', "Fees value updated successfully.");
                return Redirect::to('admin/fees/list-fees');
            }
        }

        $fee = Fee::where('id', $slug)->first();
        return view('admin.users.editFees', ['title' => $pageTitle, $activetab => 1, 'fee' => $fee]);
    }

    public function adminAgentRequest($slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Create Agent Request';
        $activetab = 'actusers';
        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'country' => 'required',
                'commission' => 'required|numeric',
                'min_deposit' => 'required|numeric',
                'address' => 'required',
                'phone' => 'required|numeric',
                'payment_method' => 'required',
                'email' => 'required|email:filter',
                'desc' => 'required',
                'profile_image' => 'required|mimes:jpeg,png,jpg',
            );
            $customMessages = [
                'first_name.required' => 'First name field can\'t be left blank.',
                'last_name.required' => 'Last name field can\'t be left blank.',
                'country.required' => 'Country field can\'t be left blank.',
                'commission.required' => 'Commission field can\'t be left blank.',
                'commission.numeric' => 'Invalid Commission! Use number only.',
                'min_deposit.required' => 'Minimum Deposit/Withdrawal field can\'t be left blank.',
                'min_deposit.numeric' => 'Invalid Minimum Deposit/Withdrawal! Use number only.',
                'address.required' => 'Address field can\'t be left blank.',
                'phone.required' => 'Phone number field can\'t be left blank.',
                'phone.numeric' => 'Invalid Phone number! Use number only.',
                'payment_method.required' => 'Payment method field can\'t be left blank.',
                'desc.required' => 'Description field can\'t be left blank.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/admin-agent-request/' . $slug)->withErrors($validator)->withInput();
            } else {
                $recordInfo = User::where('id', $slug)->first();
                if ($input['commission'] < 2 or $input['commission'] > 9) {
                    Session::flash('error_message', "Commission rate should be between 2% to 9%.");
                    return Redirect::to('/admin/users/admin-agent-request/' . $slug);
                } else if ($recordInfo->wallet_amount < 500) {
                    Session::flash('error_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > 500.");
                    return Redirect::to('/admin/users/admin-agent-request/' . $slug);
                } else if ($recordInfo->is_kyc_done != 1) {
                    Session::flash('error_message', "Your request not accepted as your KYC is not completed.");
                    return Redirect::to('/admin/users/admin-agent-request/' . $slug);
                } else {
                    if (Input::hasFile('profile_image')) {
                        $file = Input::file('profile_image');
                        $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                        //$this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                        $profile_image = $uploadedFileName;
                    } else {
                        $profile_image = 'pro-img.jpg';
                    }
                    $input['first_name'] = ucfirst(strtolower($input['first_name']));
                    $input['last_name'] = ucfirst(strtolower($input['last_name']));
                    $agnt = new Agent([
                        'user_id' => $slug,
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'country' => $input['country'],
                        'commission' => $input['commission'],
                        'min_amount' => $input['min_deposit'],
                        'address' => $input['address'],
                        'phone' => trim($input['phone']),
                        'email' => $input['email'],
                        'payment_methods' => $input['payment_method'],
                        'description' => $input['desc'],
                        'profile_image' => $profile_image,
                        'is_approved' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $agnt->save();
                    $agent_id = $agnt->id;

                    User::where('id', $slug)->update(['user_type' => 'Agent', 'updated_at' => date('Y-m-d H:i:s')]);

                    Agent::where('id', $agent_id)->update(['edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

                    Session::flash('success_message', "Agent created successfully.");
                    return Redirect::to('admin/users');
                }
            }
        }
        $countrList = Country::getCountryList();
        $user = User::where('id', $slug)->first();
        return view('admin.users.agentRequest', ['title' => $pageTitle, $activetab => 1, 'user' => $user, 'countrList' => $countrList]);
    }

    public function supportRequest(Request $request) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-support');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Support Request';
        $activetab = 'actsupportreq';
        $query = new Support();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('support_type', 'like', '%' . $keyword . '%')->orWhere('support_txt', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestSupport', ['requests' => $requests]);
        }

        return view('admin.users.supportRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }
    public function getInTouchRequest(Request $request) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-support');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Get in touch Request';
        $activetab = 'actgetintouchreq';
        $query = new GetInTouch();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.getInTouchRequest', ['requests' => $requests]);
        }

        return view('admin.users.getInTouchRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function helpRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-help');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Help Request';
        $activetab = 'acthelpreq';
        $query = new Contact();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('support_txt', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.helpRequest', ['requests' => $requests]);
        }

        return view('admin.users.helpRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function paypalRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-paypal');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Paypal Requests';
        $activetab = 'actpaypalreq';
        $query = new WithdrawRequest();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('user_name', 'like', '%' . $keyword . '%')->orWhere('paypal_email', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->where('req_type', 'Paypal')->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.reqPaypal', ['requests' => $requests]);
        }

        return view('admin.users.paypalRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function changePaypalReqStatus($id, $status) {
        $req = WithdrawRequest::where('id', $id)->first();

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('admin/users/paypal-request');
        }

        $user = User::where('id', $req->user_id)->first();

        WithdrawRequest::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

        $transID = InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->first();

        Transaction::where('id', $transID->trans_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);

        if ($status == 3) {
            $user_wallet = $user->wallet_amount + $req->amount;

            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->delete();
            //Mail start
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name) . " " . strtoupper($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = strtoupper($user->business_name);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name) . " " . strtoupper($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Agent' && $user->business_name != "") {
                $user_name = strtoupper($user->business_name);
                $loginLnk = HTTP_PATH . '/business-login';
            }


            $TransId = $transID->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your paypal request with transaction ID ' . $TransId . ' has been cancelled. Your amount (' . $user->currency . ' ' . $req->amount . ') has been refunded to your account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Paypal Request Cancelled';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changePaypalReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End  		  
        }

        if ($status == 1) {
            //Mail start
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = strtoupper($user->business_name);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Agent' && $user->business_name != "") {
                $user_name = strtoupper($user->business_name);
                $loginLnk = HTTP_PATH . '/business-login';
            }

            $TransId = $transID->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your paypal request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $req->amount . ') has been credited to your paypal account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Paypal Request Completed';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changePaypalSTatusConfirmed', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End  
        }

        Session::flash('success_message', "Request status updated successfully.");
        return Redirect::to('/admin/users/paypal-request');
    }

    public function transLimit(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'trans-limit');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Configure Transactions Limit';
        $activetab = 'actconfigtranslimit';
        $query = new Walletlimit();
        $query = $query->sortable();

        $allrecords = $query->orderBy('category_for', 'ASC')->get();

        if ($request->ajax()) {
            return view('elements.admin.users.limitTrans', ['allrecords' => $allrecords]);
        }

        return view('admin.users.transLimit', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $allrecords]);
    }

    public function editTransLimit($slug) {
        $pageTitle = 'Edit Transactions Limit';
        $activetab = 'actconfigtranslimit';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'daily_limit' => 'required|numeric',
                'week_limit' => 'required|numeric',
                'month_limit' => 'required|numeric',
            );
            $customMessages = [
                'daily_limit.required' => 'Daily Limit field can\'t be left blank.',
                'daily_limit.numeric' => 'Invalid Daily Limit! User number only.', 'week_limit.required' => 'Week Limit field can\'t be left blank.',
                'week_limit.numeric' => 'Invalid Week Limit! User number only.', 'month_limit.required' => 'Month Limit field can\'t be left blank.',
                'month_limit.numeric' => 'Invalid Month Limit! User number only.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-transaction-limit/' . $slug)->withErrors($validator)->withInput();
            } else {
                Walletlimit::where('id', $slug)->update(['daily_limit' => $input['daily_limit'], 'week_limit' => $input['week_limit'], 'month_limit' => $input['month_limit'], 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

                Session::flash('success_message', "Transaction Limit updated successfully.");
                return Redirect::to('admin/users/transactions-limit');
            }
        }

        $limit = Walletlimit::where('id', $slug)->first();
        return view('admin.users.editTransLimit', ['title' => $pageTitle, $activetab => 1, 'limit' => $limit]);
    }

    public function agentLimit() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'agent-limit');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Edit Agent Withdraw Limit';
        $activetab = 'actconfigagentlimit';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'daily_limit' => 'required|numeric',
                    //'week_limit' => 'required|numeric',
                    //'month_limit' => 'required|numeric',
            );
            $customMessages = [
                'daily_limit.required' => 'Daily Limit field can\'t be left blank.',
                'daily_limit.numeric' => 'Invalid Daily Limit! User number only.', //'week_limit.required' => 'Week Limit field can\'t be left blank.',
                    //'week_limit.numeric' => 'Invalid Week Limit! User number only.','month_limit.required' => 'Month Limit field can\'t be left blank.',
                    //'month_limit.numeric' => 'Invalid Month Limit! User number only.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/agent-limit')->withErrors($validator)->withInput();
            } else {
                Agentlimit::where('id', 1)->update(['daily_limit' => $input['daily_limit'], 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);

                Session::flash('success_message', "Agent daily Limit updated successfully.");
                return Redirect::to('admin/users/agent-limit');
            }
        }

        $limit = Agentlimit::where('id', 1)->first();
        return view('admin.users.agentLimit', ['title' => $pageTitle, $activetab => 1, 'limit' => $limit]);
    }
    
    public function ngnconversion() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'conversion');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actconversion';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Edit Conversion';
        $activetab = 'actconversion';
        
        $exchange = Ngnexchange::where('id', 1)->first();

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'usd_value' => 'required|numeric',
                'gbp_value' => 'required|numeric',
                'zar_value' => 'required|numeric',
                'bwp_value' => 'required|numeric',
                'nad_value' => 'required|numeric',
                'szl_value' => 'required|numeric',
                'kes_value' => 'required|numeric',
                'eur_value' => 'required|numeric',
                'php_value' => 'required|numeric',
                'kwd_value' => 'required|numeric',
                'idr_value' => 'required|numeric',
            );
            $customMessages = [
                'usd_value.required' => 'USD to NGN field can\'t be left blank.',
                'usd_value.numeric' => 'Invalid USD to NGN! User number only.', 
                'gbp_value.required' => 'GBP to NGN field can\'t be left blank.',
                'gbp_value.numeric' => 'Invalid GBP to NGN! User number only.', 
                'zar_value.required' => 'ZAR to NGN field can\'t be left blank.',
                'zar_value.numeric' => 'Invalid ZAR to NGN! User number only.', 
                'bwp_value.required' => 'BWP to NGN field can\'t be left blank.',
                'bwp_value.numeric' => 'Invalid BWP to NGN! User number only.', 
                'nad_value.required' => 'NAD to NGN field can\'t be left blank.',
                'nad_value.numeric' => 'Invalid NAD to NGN! User number only.', 
                'szl_value.required' => 'SZL to NGN field can\'t be left blank.',
                'szl_value.numeric' => 'Invalid SZL to NGN! User number only.', 
                'kes_value.required' => 'KES to NGN field can\'t be left blank.',
                'kes_value.numeric' => 'Invalid KES to NGN! User number only.', 
                'eur_value.required' => 'EURO to NGN field can\'t be left blank.',
                'eur_value.numeric' => 'Invalid EURO to NGN! User number only.', 
                'php_value.required' => 'PHP to NGN field can\'t be left blank.',
                'php_value.numeric' => 'Invalid PHP to NGN! User number only.', 
                'kwd_value.required' => 'KWD to NGN field can\'t be left blank.',
                'kwd_value.numeric' => 'Invalid KWD to NGN! User number only.', 
                'idr_value.required' => 'IDR to NGN field can\'t be left blank.',
                'idr_value.numeric' => 'Invalid IDR to NGN! User number only.', 
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/conversion')->withErrors($validator)->withInput();
            } else {
                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
                Ngnexchange::where('id', $exchange->id)->update($serialisedData);

                Session::flash('success_message', "Conversion details updated successfully.");
                return Redirect::to('admin/users/conversion');
            }
        }

        return view('admin.users.ngnconversion', ['title' => $pageTitle, $activetab => 1, 'exchange' => $exchange]);
    }

    public function cryptoDepositRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-crypto-deposit');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Crypto Deposit Requests';
        $activetab = 'actcryptodepositreq';
        $query = new CryptoDeposit();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestCryptoDeposit', ['requests' => $requests]);
        }

        return view('admin.users.cryptoDepositRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    private function usdToUserCurrency($currency, $amount) {
        $apikey = '1c5849e1679846c9ac66887bbdd4d76f';
        if ($currency != 'USD') {
            if ($currency == 'EURO') {
                $query = "USD_EUR";
            } else {
                $query = "USD_" . $currency;
            }
            $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;

            $json = file_get_contents($curr_req);
            $obj = json_decode($json, true);
            //print_R($obj);
            $val = floatval($obj[$query]);
            $total = $val * $amount;
            return $total;
        } else {
            return $amount;
        }
    }

    public function updateCryptoDepositReqStatus($id, $status) {
        $req = CryptoDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {

            $amount_cc = $this->usdToUserCurrency($user->currency, $req->amount);

            $deposit_amount = $amount_cc;
            if ($recordInfo->user_type == 'Personal') {

                if ($recordInfo->account_category == "Silver") {
                    $fee_name = 'CRYPTO_DEPOSITE';
                } else if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'CRYPTO_DEPOSITE_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'CRYPTO_DEPOSITE_PLATINUM';
                } else if ($recordInfo->account_category == "Private Wealth") {
                    $fee_name = 'CRYPTO_DEPOSITE_PRIVATE_WEALTH';
                } else {
                    $fee_name = 'CRYPTO_DEPOSITE';
                }
                //$fee_name = 'CRYPTO_DEPOSITE';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MERCHANT_CRYPTO_DEPOSITE_PLATINUM';
                } else if ($recordInfo->account_category == "Enterprises") {
                    $fee_name = 'MERCHANT_CRYPTO_DEPOSITE_ENTERPRIS';
                } else {
                    $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';
                }

                // $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    //$fee_name = 'CRYPTO_DEPOSITE';
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'CRYPTO_DEPOSITE';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'CRYPTO_DEPOSITE_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'CRYPTO_DEPOSITE_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'CRYPTO_DEPOSITE_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'CRYPTO_DEPOSITE';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                    //$fee_name = 'MERCHANT_CRYPTO_DEPOSITE';
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_CRYPTO_DEPOSITE_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_CRYPTO_DEPOSITE_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

            //                    echo $fees_amount;
            $deposit_amount_total = $amount_cc - $fees_amount;
            //            exit;

            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            $amount_cc1 = number_format($deposit_amount_total, 2, '.', '');
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);


            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount + $fees_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            Transaction::where('id', $req->trans_id)->update(['fees' => $fees_amount, "user_close_bal" => $user_wallet, "receiver_close_bal" => 0.00, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $amount_cc . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Crypto Deposit Request has been Completed';
            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['userName'] = strtoupper($user_name);
            $emailData['TransId'] = $TransId;
            $emailData['amount_cc'] = $user->currency . ' ' . $amount_cc1;
            $emailData['currency'] = $user->currency;
            $emailData['loginLnk'] = $loginLnk;

            Mail::send('emails.updateCryptoDepositReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });

            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End
        }

        if ($status == 3) {
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = strtoupper($user_name);

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been cancelled.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Crypto Deposit Request Cancelled';

            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changePaypalReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End  
            Transaction::where('id', $req->trans_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        CryptoDeposit::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
        return Redirect::to('/admin/users/crypto-deposit-request');
    }

    public function updateCryptoDepositReqStatus_old($id, $status) {
        $req = CryptoDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {

            $amount_cc = $this->usdToUserCurrency($user->currency, $req->amount);

            $deposit_amount = $amount_cc;
            if ($recordInfo->user_type == 'Personal') {
                $fee_name = 'CRYPTO_DEPOSITE';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    $fee_name = 'CRYPTO_DEPOSITE';

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($user->user_type == 'Agent' and $user->business_name != "") {
                    $fee_name = 'MERCHANT_CRYPTO_DEPOSITE';

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

//                    echo $fees_amount;
            $deposit_amount_total = $amount_cc - $fees_amount;
//            exit;

            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            $amount_cc1 = number_format($deposit_amount_total, 2, '.', '');
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);


            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount + $fees_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            Transaction::where('id', $req->trans_id)->update(['fees' => $fees_amount, "user_close_bal" => $user_wallet, "receiver_close_bal" => 0.00, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = strtoupper($user_name);

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $amount_cc . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Crypto Deposit Request has been Completed';
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['userName'] = $user_name;
            $emailData['TransId'] = $TransId;
            $emailData['amount_cc'] = $user->currency . ' ' . $amount_cc1;
            $emailData['currency'] = $user->currency;
            $emailData['loginLnk'] = $loginLnk;

            Mail::send('emails.updateCryptoDepositReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });

            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End
        }

        if ($status == 3) {
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = strtoupper($user_name);

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been cancelled.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Crypto Deposit Request Cancelled';

//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changePaypalReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End  
            Transaction::where('id', $req->trans_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        CryptoDeposit::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
        return Redirect::to('/admin/users/crypto-deposit-request');
    }

    public function editCryptoReq($id) {
        $pageTitle = 'Edit Crypto Request';
        $activetab = 'actcryptodepositreq';

        $req = CryptoDeposit::where('id', $id)->first();
        
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'amount' => 'required|numeric',
                'crypto_currency' => 'required',
                'blockchain_url' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-crypto-request/' . $id)->withErrors($validator)->withInput();
            } else {
                
                $amount_cc = $this->usdToUserCurrency($user->currency, $input['amount']);

            $deposit_amount = $amount_cc;
            
                CryptoDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'amount' => $input['amount'], 'crypto_currency' => $input['crypto_currency'], 'blockchain_url' => $input['blockchain_url'], 'updated_at' => date('Y-m-d H:i:s')]);
                Transaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'real_value'=>$deposit_amount, 'updated_at' => date('Y-m-d H:i:s')]);
                Session::flash('success_message', "Request details updated successfully.");
                return Redirect::to('admin/users/crypto-deposit-request');
            }
        }
        return view('admin.users.editCryptoReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
    }

    public function manualDepositRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-manual-deposit');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Manual Deposit Requests';
        $activetab = 'actmanualdepositreq';
        $query = new ManualDeposit();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('user_name', 'like', '%' . $keyword . '%')->orWhere('ref_number', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestManualDeposit', ['requests' => $requests]);
        }

        return view('admin.users.manualDepositRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function editManualReq($id) {
        $pageTitle = 'Edit Manual Request';
        $activetab = 'actmanualdepositreq';

        $req = ManualDeposit::where('id', $id)->first();
        
        

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'amount' => 'required|numeric',
                'ref_number' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-manual-request/' . $id)->withErrors($validator)->withInput();
            } else {
                if ($req->amount == $input['amount']) {
                    $updateAmnt = $input['amount'];
                } else if ($req->amount > $input['amount']) {
                    
                    
            
                    /* $updateAmnt = $req->amount - $input['amount'];
                      $user = User::where('id',$req->user_id)->first();
                      $user_wallet = $user->wallet_amount + $updateAmnt;
                      User::where('id',$req->user_id)->update(['wallet_amount'=>$user_wallet,'updated_at'=>date('Y-m-d H:i:s')]); */
                    Transaction::where('id', $req->trans_id)->update(['amount' => $input['amount'], 'updated_at' => date('Y-m-d H:i:s')]);
                } else if ($req->amount < $input['amount']) {
                    /* $updateAmnt = $input['amount'] - $req->amount;
                      $user = User::where('id',$req->user_id)->first();
                      $user_wallet = $user->wallet_amount - $updateAmnt;
                      User::where('id',$req->user_id)->update(['wallet_amount'=>$user_wallet,'updated_at'=>date('Y-m-d H:i:s')]); */
                    Transaction::where('id', $req->trans_id)->update(['amount' => $input['amount'], 'updated_at' => date('Y-m-d H:i:s')]);
                }

                ManualDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'amount' => $input['amount'], 'ref_number' => $input['ref_number'], 'updated_at' => date('Y-m-d H:i:s')]);
//                Session::flash('success_message', "Request details updated successfully.");
//                return Redirect::to('admin/users/manual-deposit-request');
                return Redirect::to('admin/users/change-manual-deposit-req-status/' . $id . '/1');
            }
        }
        return view('admin.users.editManualReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
    }

    public function repeatManualReq($id) {
        $req = ManualDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

//        $user_wallet = $user->wallet_amount + $req->amount;
//        User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
        
        
        $deposit_amount = $req->amount;
            if ($recordInfo->user_type == 'Personal') {
                //$fee_name = 'MANUAL_DEPOSIT';
                if ($recordInfo->account_category == "Silver") {
                    $fee_name = 'MANUAL_DEPOSIT';
                } else if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MANUAL_DEPOSIT_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MANUAL_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Private Wealth") {
                    $fee_name = 'MANUAL_DEPOSIT_PRIVATE_WEALTH';
                } else {
                    $fee_name = 'MANUAL_DEPOSIT';
                }

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                //$fee_name = 'MERCHANT_MANUAL_DEPOSIT';

                if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Enterprises") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT_ENTERPRIS';
                } else {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                }

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    //$fee_name = 'MANUAL_DEPOSIT';
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'MANUAL_DEPOSIT';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MANUAL_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MANUAL_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'MANUAL_DEPOSIT_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'MANUAL_DEPOSIT';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                    //$fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

            $deposit_amount_total = $req->amount - $fees_amount;

            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount + $fees_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

        //Mail Start

        $TransId = $req->trans_id;
        $emailId = $user->email;
        $userName = strtoupper($user_name);

        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $req->currency . ' ' . $req->amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
        $emailSubject = 'DafriBank Digital | Manual Deposit Request has been Completed';
        $emailData['subjects'] = $emailSubject;
        $emailData['userName'] = $userName;
        $emailData['emailId'] = $emailId;
        $emailData['TransId'] = $TransId;
        $emailData['loginLnk'] = $loginLnk;
        $emailData['amount'] = $req->amount;
        $emailData['currency'] = $user->currency;
        Mail::send('emails.repeatManualReq', $emailData, function ($message)use ($emailData, $emailId) {
            $message->to($emailId, $emailId)
                    ->subject($emailData['subjects']);
        });
//        Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));


        $trans = Transaction::where('id', $req->trans_id)->first();

        $refrence_id = time() . rand() . Session::get('user_id');
        $tran = new Transaction([
            "user_id" => $trans->user_id,
            "receiver_id" => 0,
            "amount" => $trans->amount,
            "fees" => $fees_amount,
            "receiver_fees" => $fees_amount,
            "currency" => $trans->currency,
            "trans_type" => 1, //Debit-Withdraw
            "trans_to" => 'Dafri_Wallet',
            "trans_for" => 'ManualDeposit',
            "refrence_id" => $refrence_id,
            "billing_description" => 'Payment repeated by admin for transaction ID: ' . $req->trans_id,
            "status" => 1,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        ]);
        $tran->save();
        $TransId = $tran->id;

        Session::flash('success_message', "Payment Repeated successfully.");
        return Redirect::to('/admin/users/manual-deposit-request');

        //Mail End
    }

    public function updateManualDepositReqStatus($id, $status) {
        $req = ManualDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {

            $deposit_amount = $req->amount;
            if ($recordInfo->user_type == 'Personal') {
                //$fee_name = 'MANUAL_DEPOSIT';
                if ($recordInfo->account_category == "Silver") {
                    $fee_name = 'MANUAL_DEPOSIT';
                } else if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MANUAL_DEPOSIT_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MANUAL_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Private Wealth") {
                    $fee_name = 'MANUAL_DEPOSIT_PRIVATE_WEALTH';
                } else {
                    $fee_name = 'MANUAL_DEPOSIT';
                }

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                //$fee_name = 'MERCHANT_MANUAL_DEPOSIT';

                if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Enterprises") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT_ENTERPRIS';
                } else {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                }

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    //$fee_name = 'MANUAL_DEPOSIT';
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'MANUAL_DEPOSIT';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MANUAL_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MANUAL_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'MANUAL_DEPOSIT_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'MANUAL_DEPOSIT';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                    //$fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_MANUAL_DEPOSIT';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

            $deposit_amount_total = $req->amount - $fees_amount;

            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount + $fees_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = strtoupper($user_name);

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $req->currency . ' ' . $req->amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Manual Deposit Request has been Completed';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = number_format($deposit_amount_total, 2, '.', '');
            $emailData['currency'] = $user->currency;
            Mail::send('emails.updateManualDepositReqStatus1', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End
            
            $refrence_id = time() . rand() . $req->user_id;

            Transaction::where('id', $req->trans_id)->update(["refrence_id" => $refrence_id,'fees' => $fees_amount, 'user_close_bal' => $user_wallet, 'real_value' => $req->amount, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        if ($status == 3) {
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your manual deposit request with transaction ID ' . $TransId . ' has been cancelled.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Manual Deposit Request Cancelled';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.updateManualDepositReqStatus2', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End  
            
            $refrence_id = time() . rand() . $req->user_id;

            Transaction::where('id', $req->trans_id)->update(["refrence_id" => $refrence_id,'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        ManualDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");

        return Redirect::to('/admin/users/manual-deposit-request');
    }

    public function updateManualDepositReqStatus_old($id, $status) {
        $req = ManualDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {

            $deposit_amount = $req->amount;
            if ($recordInfo->user_type == 'Personal') {
                $fee_name = 'MANUAL_DEPOSIT';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                $fee_name = 'MERCHANT_MANUAL_DEPOSIT';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    $fee_name = 'MANUAL_DEPOSIT';

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($user->user_type == 'Agent' and $user->business_name != "") {
                    $fee_name = 'MERCHANT_MANUAL_DEPOSIT';

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

            $deposit_amount_total = $req->amount - $fees_amount;

            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount + $fees_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $req->currency . ' ' . $req->amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Manual Deposit Request has been Completed';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = number_format($deposit_amount_total, 2, '.', '');
            $emailData['currency'] = $user->currency;
            Mail::send('emails.updateManualDepositReqStatus1', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End

            Transaction::where('id', $req->trans_id)->update(['fees' => $fees_amount, 'user_close_bal' => $user_wallet, 'real_value' => $req->amount, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        if ($status == 3) {
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your manual deposit request with transaction ID ' . $TransId . ' has been cancelled.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Manual Deposit Request Cancelled';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.updateManualDepositReqStatus2', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End  

            Transaction::where('id', $req->trans_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        ManualDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");

        return Redirect::to('/admin/users/manual-deposit-request');
    }

    public function manualWithdrawRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-manual-withdraw');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Manual Withdraw Requests';
        $activetab = 'actmanualwithdrawreq';
        $query = new ManualWithdraw();
        $query = $query->sortable();

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('user_name', 'like', '%' . $keyword . '%')->orWhereHas('User', function ($q) use ($keyword) {
                    //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                    $q->where('account_id', 'like', '%' . $keyword . '%');
//                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%");
                });
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestManualWithdraw', ['requests' => $requests]);
        }

        return view('admin.users.manualWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function changeManualReqStatus($id, $status) { //Manual Withdraw Request Status Change
        $req = ManualWithdraw::where('id', $id)->first();
        $user = User::where('id', $req->user_id)->first();

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            Transaction::where('id', $req->trans_id)->update(['status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->where('trans_id', $req->trans_id)->delete();

            //Mail Start
            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual withdrawal request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $req->amount . '). has been debited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Manual Withdrawal Request has been Completed';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changeManualReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End
            Session::flash('success_message', "Request status changed successfully.");
            return Redirect::to('admin/users/manual-withdraw-request');
        } else if ($status == 3) {
            $user_wallet = $user->wallet_amount + $req->amount;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 3, 'updated_at' => date('Y-m-d H:i:s')]);
            Transaction::where('id', $req->trans_id)->update(['status' => 3, 'updated_at' => date('Y-m-d H:i:s')]);
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->where('trans_id', $req->trans_id)->delete();

            //Mail Start
            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Sorry, Your manual withdrawal request with transaction ID ' . $TransId . ' has been cancelled. Your amount (' . $user->currency . ' ' . $req->amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Manual Withdrawal Request has been Declined';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changeManualReqStatusCancel', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $notif = new Notification([
                'user_id' => $user->id,
                'notif_subj' => $emailSubject,
                'notif_body' => $emailBody,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $notif->save();
            //Mail End

            Session::flash('success_message', "Request status changed successfully.");
            return Redirect::to('admin/users/manual-withdraw-request');
        } else {
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 2, 'updated_at' => date('Y-m-d H:i:s')]);
            Session::flash('success_message', "Request status changed successfully.");
            return Redirect::to('admin/users/manual-withdraw-request');
        }
    }

    private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function adjustBalance_old($user_id) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-user-wallet');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Adjust User Wallet';

        $recordInfo = User::where('id', $user_id)->first();
        if ($recordInfo->user_type == 'Personal') {
            $activetab = 'actusers';
        } else if ($recordInfo->user_type == 'Business') {
            $activetab = 'actmerchants';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
            $activetab = 'actusers';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
            $activetab = 'actmerchants';
        }

        /* if(!empty($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == "https://www.nimbleappgenie.live/dafri/admin/users") {
          $activetab = 'actusers';
          }
          elseif (!empty($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == "https://www.nimbleappgenie.live/dafri/admin/merchants") {
          $activetab = 'actmerchants';
          }
          else {
          $activetab = 'actusers';
          } */

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'wallet_action' => 'required',
                'amount' => 'required|numeric',
                'reason' => 'required',
            );

            $customMessages = [
                'wallet_amount.required' => 'Invalid Transaction Type',
                'amount.required' => 'Amount field can\'t be left blank.',
                'amount.numeric' => 'Invalid Amount',
                'reason.required' => 'Reason field can\'t be left blank.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);

            if ($validator->fails()) {
                return Redirect::to('/admin/users/adjust-client-wallet/' . $user_id)->withErrors($validator)->withInput();
            } else {
                if ($input['amount'] > 9999999999999) {
                    Session::flash('error_message', 'Invalid Amount Value!');
                    return Redirect::to('/admin/users/adjust-client-wallet/' . $user_id);
                }

                if ($input['wallet_action'] == "debit") {
                    $admin = User::where('id', 1)->first();
                    $user_wallet = $recordInfo->wallet_amount - $input['amount'];
                    User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $trans = new Transaction([
                        "user_id" => $user_id,
                        "receiver_id" => 1,
                        "amount" => $input['amount'],
                        "fees" => 0,
                        "currency" => $recordInfo->currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $input['reason'] . '##AdminBalanceAdjust##Wallet2Wallet Transfer##IP:' . $this->get_client_ip(),
                        "user_close_bal" => $user_wallet,
                        "receiver_close_bal" => $admin->wallet_amount,
                        "real_value" => $input['amount'],
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been debited with ' . $recordInfo->currency . ' ' . $input['amount'] . ' transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'DafriBank Digital | Account Balance Adjusted By DafriBank Digital';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['currency'] = $recordInfo->currency;
                    Mail::send('emails.adjustBalance', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                    //                    Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailBody,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                } else if ($input['wallet_action'] == "credit") {
                    $admin = User::where('id', 1)->first();
                    $user_wallet = $recordInfo->wallet_amount + $input['amount'];
                    User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $trans = new Transaction([
                        "user_id" => 1,
                        "receiver_id" => $user_id,
                        "amount" => $input['amount'],
                        "fees" => 0,
                        "currency" => $recordInfo->currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $input['reason'] . '<br>AdminBalanceAdjust<br>IP:' . $this->get_client_ip(),
                        "user_close_bal" => $admin->wallet_amount,
                        "receiver_close_bal" => $user_wallet,
                        "real_value" => $input['amount'],
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been credited with ' . $recordInfo->currency . ' ' . $input['amount'] . ' transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'DafriBank Digital | Account Balance Adjusted By DafriBank Digital';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['currency'] = $recordInfo->currency;
                    Mail::send('emails.adjustBalanceCredit', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                    //                    Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailBody,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                }

                Session::flash('success_message', "Client Balance Adjusted Successfully.");
                if ($recordInfo->user_type == 'Personal') {
                    return Redirect::to('/admin/users');
                } else if ($recordInfo->user_type == 'Business') {
                    return Redirect::to('/admin/merchants');
                } else if ($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "") {
                    return Redirect::to('/admin/merchants');
                } else if ($recordInfo->user_type == 'Agent' && $recordInfo->business_name == "") {
                    return Redirect::to('/admin/users');
                }
            }
        }

        return view('admin.users.adjustBalance', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }

    public function adjustBalance($user_id) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-user-wallet');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Adjust User Wallet';

        $recordInfo = User::where('id', $user_id)->first();
        if ($recordInfo->user_type == 'Personal') {
            $activetab = 'actusers';
        } else if ($recordInfo->user_type == 'Business') {
            $activetab = 'actmerchants';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
            $activetab = 'actusers';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
            $activetab = 'actmerchants';
        }

        /* if(!empty($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == "https://www.nimbleappgenie.live/dafri/admin/users") {
          $activetab = 'actusers';
          }
          elseif (!empty($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == "https://www.nimbleappgenie.live/dafri/admin/merchants") {
          $activetab = 'actmerchants';
          }
          else {
          $activetab = 'actusers';
          } */

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'wallet_action' => 'required',
                'amount' => 'required|numeric',
                'reason' => 'required',
            );

            $customMessages = [
                'wallet_amount.required' => 'Invalid Transaction Type',
                'amount.required' => 'Amount field can\'t be left blank.',
                'amount.numeric' => 'Invalid Amount',
                'reason.required' => 'Reason field can\'t be left blank.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);

            if ($validator->fails()) {
                return Redirect::to('/admin/users/adjust-client-wallet/' . $user_id)->withErrors($validator)->withInput();
            } else {
                if ($input['amount'] > 9999999999999) {
                    Session::flash('error_message', 'Invalid Amount Value!');
                    return Redirect::to('/admin/users/adjust-client-wallet/' . $user_id);
                }

                if ($input['wallet_action'] == "debit") {
                    
                    $remainBal = $input['amount'];
                    $adminBal = $input['amount'];

                    $fee = 0;
                    
                    $billing_description = '<br>Debited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $input['amount'] . '##Reason: ' . $input['reason'];
                    if ($recordInfo->currency != 'USD') {
                        $convr_fee_name = 'CONVERSION_FEE';
                        $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                        $conversion_fee_reciver = $fees_convr->fee_value;
                        $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
//                        echo '/';
                        $remainBal = $input['amount'] + $fee;

                        $amount_user_currency = $this->myCurrencyConversionRate($recordInfo->currency,'USD',  $remainBal);
//                          print_r($amount_user_currency);die;
                        $convrsArr = explode("###", $amount_user_currency);

                        $adminBal = $convrsArr[1];

                        $billing_description = '<br>Debited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $input['amount'] . ' and Conversion rate ' . $convrsArr[0] . '=' . $convrsArr[1] . "##Conversion Fees = " . $conversion_fee_reciver . '##Reason: ' . $input['reason'];
                    }
                    
                    $admin = User::where('id', 1)->first();
                    $admin_wallet = $admin->wallet_amount + $adminBal;
                    User::where('id', 1)->update(['wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    
                    $user_wallet = $recordInfo->wallet_amount - $remainBal;
                    User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $trans = new Transaction([
                        "user_id" => $user_id,
                        "receiver_id" => 1,
                        "amount" => $input['amount'],
                        "fees" => $fee,
                        "sender_fees" => $fee,
                        "currency" => $recordInfo->currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "receiver_close_bal" => $admin_wallet,
                        "real_value" => $input['amount'],
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been debited with ' . $recordInfo->currency . ' ' . $input['amount'] . ' transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'DafriBank Digital | Account Balance Adjusted By DafriBank Digital';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['currency'] = $recordInfo->currency;
                    Mail::send('emails.adjustBalance', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                    //                    Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailBody,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                } else if ($input['wallet_action'] == "credit") {
                    $admin = User::where('id', 1)->first();

                    $refrence_id = time() . rand() . Session::get('user_id');

                    $remainBal = $input['amount'];
                    $adminBal = $input['amount'];

                    $fee = 0;
                    $billing_description = '<br>Credited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $input['amount'] . '##Reason: ' . $input['reason'];
                    if ($recordInfo->currency != 'USD') {
                        $convr_fee_name = 'CONVERSION_FEE';
                        $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                        $conversion_fee_reciver = $fees_convr->fee_value;
                        $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
//                        echo '/';
                        $remainBal = $input['amount'] - $fee;

                        $amount_user_currency = $this->myCurrencyConversionRate($recordInfo->currency, 'USD',  $remainBal);
//                          print_r($amount_user_currency);die;
                        $convrsArr = explode("###", $amount_user_currency);

                        $adminBal = $convrsArr[1];

                        $billing_description = '<br>Credited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $input['amount'] . ' and Conversion rate ' . $convrsArr[0] . '=' . $remainBal . "##Conversion Fees = " . $conversion_fee_reciver . '##Reason: ' . $input['reason'];
                    }
//                    echo '/';
//                    echo ($remainBal-$fee);
//                    echo '/';

                    $admin_wallet = $admin->wallet_amount - $adminBal;
                    User::where('id', 1)->update(['wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    $user_wallet = $recordInfo->wallet_amount + $remainBal;
                    User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

//exit;

                    $trans = new Transaction([
                        "user_id" => 1,
                        "receiver_id" => $user_id,
                        "amount" => $input['amount'],
                        "fees" => $fee,
                        "currency" => $recordInfo->currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $admin->wallet_amount,
                        "receiver_close_bal" => $user_wallet,
                        "real_value" => $input['amount'],
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = strtoupper($recordInfo->business_name);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been credited with ' . $recordInfo->currency . ' ' . $input['amount'] . ' transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'DafriBank Digital | Account Balance Adjusted By DafriBank Digital';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['fee'] = $fee;
                    $emailData['currency'] = $recordInfo->currency;
                    Mail::send('emails.adjustBalanceCredit', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                    //                    Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailBody,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                }

                Session::flash('success_message', "Client Balance Adjusted Successfully.");
                if ($recordInfo->user_type == 'Personal') {
                    return Redirect::to('/admin/users');
                } else if ($recordInfo->user_type == 'Business') {
                    return Redirect::to('/admin/merchants');
                } else if ($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "") {
                    return Redirect::to('/admin/merchants');
                } else if ($recordInfo->user_type == 'Agent' && $recordInfo->business_name == "") {
                    return Redirect::to('/admin/users');
                }
            }
        }

        return view('admin.users.adjustBalance', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }

    private function myCurrencyConversionRate($merchant_currency, $user_currency, $amount) {
        $apikey = '1c5849e1679846c9ac66887bbdd4d76f';

        $query = $merchant_currency . '_' . $user_currency;

        $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
        //https://free.currconv.com/api/v7/convert?q=USD_ZAR&compact=ultra&apiKey=5c638446397b3588a3c6
        $json = file_get_contents($curr_req);
        $obj = json_decode($json, true);
        $val = floatval($obj[$query]);
        $total = $val * $amount;
        return $val . "###" . $total;
    }

    public function cryptoWithdrawRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-crypto-withdraw');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Crypto Withdraw Requests';
        $activetab = 'actcryptowithdrawreq';
        $query = new CryptoWithdraw();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                //$q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%")->orWhere('email', 'like', '%' . $keyword . '%');	
                $q->where('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestCryptoWithdraw', ['requests' => $requests]);
        }
//        echo '<pre>';print_r($requests);exit;

        return view('admin.users.cryptoWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function editCryptoWithdrawReq($id) {
        $pageTitle = 'Edit Crypto Withdraw Request';
        $activetab = 'actcryptowithdrawreq';

        $req = CryptoWithdraw::where('id', $id)->first();

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'amount' => 'required|numeric',
                'crypto_currency' => 'required',
                'payout_addrs' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-crypto-withdraw-request/' . $id)->withErrors($validator)->withInput();
            } else {
                CryptoWithdraw::where('id', $id)->update(['amount' => $input['amount'], 'crypto_currency' => $input['crypto_currency'], 'payout_addrs' => $input['payout_addrs'], 'updated_at' => date('Y-m-d H:i:s')]);
                Transaction::where('id', $req->trans_id)->update(['amount' => $input['amount'], 'updated_at' => date('Y-m-d H:i:s')]);
                Session::flash('success_message', "Request details updated successfully.");
                return Redirect::to('admin/users/crypto-withdraw-request');
            }
        }
        return view('admin.users.editCryptoWithdrawReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
    }

    public function updateCryptoWithdrawReqStatus($id, $status) {
        $req = CryptoWithdraw::where('id', $id)->first();
        $user = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = strtoupper($user->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            /* $amount_cc = $this->usdToUserCurrency($user->currency,$req->amount);
              $user_wallet = $user->wallet_amount + $amount_cc;
              $amount_cc = number_format($amount_cc,2,'.','');
              User::where('id',$req->user_id)->update(['wallet_amount'=>$user_wallet,'updated_at'=>date('Y-m-d H:i:s')]); */

            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency withdrawal request with transaction ID ' . $TransId . ' has been processed successfully. Your Crypto Currency (' . $req->crypto_currency . ') has been credited to your Trust Wallet account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Crypto Withdrawal Request has been Completed';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['crypto_currency'] = $req->crypto_currency;
//                        $emailData['amount'] = $input['amount'];
//                        $emailData['currency'] = $recordInfo->currency;
            Mail::send('emails.updateCryptoWithdrawReqStatusCredit', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            //Mail End
        }

        if ($status == 3) {
            $amount_cc = $this->usdToUserCurrency($user->currency, $req->amount);
            $user_wallet = $user->wallet_amount + $amount_cc;
            $amount_cc = number_format($amount_cc, 2, '.', '');
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your Crypto Currency withdrawal request with transaction ID ' . $TransId . ' has been cancelled.<br><br>Your amount has been refunded to you account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Crypto Withdrawal Request Cancelled';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['crypto_currency'] = $req->crypto_currency;
            Mail::send('emails.updateCryptoWithdrawReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            //Mail End  
        }

        CryptoWithdraw::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
        Transaction::where('id', $req->trans_id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        return Redirect::to('/admin/users/crypto-withdraw-request');
    }

    public function setAgentTransLimit($user_id) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'individual-agent-limit');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Edit Agent Withdraw Limit';
        $activetab = 'actusers';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'daily_limit' => 'required|numeric',
                    //'week_limit' => 'required|numeric',
                    //'month_limit' => 'required|numeric',
            );
            $customMessages = [
                'daily_limit.required' => 'Daily Limit field can\'t be left blank.',
                'daily_limit.numeric' => 'Invalid Daily Limit! User number only.', //'week_limit.required' => 'Week Limit field can\'t be left blank.',
                    //'week_limit.numeric' => 'Invalid Week Limit! User number only.','month_limit.required' => 'Month Limit field can\'t be left blank.',
                    //'month_limit.numeric' => 'Invalid Month Limit! User number only.',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/set-agent-trans-limit/' . $user_id)->withErrors($validator)->withInput();
            } else {
                $isExists = AgentsTransactionLimit::where('agent_id', $user_id)->first();
                if (!empty($isExists)) {
                    AgentsTransactionLimit::where('agent_id', $user_id)->update(['trans_limit' => $input['daily_limit'], 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                    Agent::where('id', $user_id)->update(["edited_by" => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                } else {
                    $agent = Agent::where('id', $user_id)->first();
                    $atl = new AgentsTransactionLimit([
                        'user_id' => $agent->user_id,
                        'agent_id' => $agent->id,
                        'trans_limit' => $input['daily_limit'],
                        'edited_by' => Session::get('adminid'),
                        'create_date' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $atl->save();
                    Agent::where('user_id', $user_id)->update(["edited_by" => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                    Session::flash('success_message', "Agent Transaction Limit Set!");
                    return Redirect::to('/admin/users/bank-agent-request');
                }

                Session::flash('success_message', "Agent daily Limit updated successfully.");
                return Redirect::to('admin/users/bank-agent-request');
            }
        }

        $agentLimit = AgentsTransactionLimit::where('agent_id', $user_id)->first();
        if (!empty($agentLimit)) {
            $transLimit = $agentLimit->trans_limit;
        } else {
            $limit = Agentlimit::where('id', 1)->first();
            $transLimit = $limit->daily_limit;
        }
        return view('admin.users.setAgentTransLimit', ['title' => $pageTitle, $activetab => 1, 'limit' => $transLimit]);
    }

    public function agentList(Request $request) {
        
    }

}

?>