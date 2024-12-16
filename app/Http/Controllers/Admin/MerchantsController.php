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
use App\Transaction;
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
use App\InvitedUser;



class MerchantsController extends Controller {

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

        return $output_file;
    }

    public function index(Request $request) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        //DB::enableQueryLog();

        $pageTitle = 'Manage Business Users';

        $activetab = 'actmerchants';

        $query = new User();

        $query = $query->sortable();
        $countrList = Country::getCountryList();


        $role = 'Agent';

//        $query = $query->orWhere(function($q) use ($role) {
//
//            $q->where('user_type', 'Business')->orWhere('user_type', $role);
//        });
        
        $query = $query->orWhere(function ($q) use ($role) {
            $q->where('user_type', 'Business')
                    ->orWhere(function($q) use ($role){
                $q = $q->where('user_type', $role)->where('business_name','!=', '');
            });
        });

        $query = $query->where('id', '!=', 1);



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

            $query = $query->where(function($q) use ($keyword) {

                $q->where('director_name', 'like', '%' . $keyword . '%')->orWhere('business_name', 'LIKE', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('phone', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
            });
        }
        
        if ($request->has('status') && $request->get('status') != '') {
            $status = $request->get('status');
            if($status == 0){
                $query = $query->where(function($q) use ($status) {
                    $q->where('is_kyc_done', 0)->where('identity_image','!=','');
                });
            } else if($status == 3){
                $query = $query->where(function($q) use ($status) {
                    $q->where('is_kyc_done', 0)->whereNull('identity_image');
                });
            } else{
                $query = $query->where(function($q) use ($status) {
                    $q->where('is_kyc_done', $status);
                });
            }
            
        }

        if ($request->has('country') && $request->get('country') != '') {
            $country = $request->get('country');
            $query = $query->where(function($q) use ($country) {
                $q->where('country', $country);
            });
            }



        //$merchants = $query->orWhere('user_type','Agent')->orderBy('id', 'DESC')->paginate(20);

        $merchants = $query->orderBy('id', 'DESC')->paginate(20);
        $users_count = $query->orderBy('id', 'DESC')->count();
        //dd(DB::getQueryLog());

        if ($request->ajax()) {

            return view('elements.admin.merchants.index', ['allrecords' => $merchants,'users_count'=>$users_count]);
        }

        return view('admin.merchants.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $merchants,'countrList'=>$countrList,'users_count'=>$users_count]);
    }

    public function genrateAccNumber() {

        $adminInfo = DB::table('admins')->select('admins.last_account_number')->where('id', 1)->first();

        $accNumber = $adminInfo->last_account_number + 1;



        DB::table('admins')->where('id', 1)->update(['last_account_number' => $accNumber]);

        return $accNumber;
    }

    public function add() {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'add-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Add Business User';

        $activetab = 'actmerchants';



        $countrList = Country::getCountryList();

        $input = Input::all();

        if (!empty($input)) {
          //  echo "<pre>";
           // print_r($input); 
           // die;

            $rules = array(
                'business_name' => 'required|max:50',
                'director_name' => 'required|max:50',
                'phone' => 'required|min:8|unique:users',
                'email' => 'required|email|unique:users',
                'country' => 'required',
                'currency' => 'required',
                // 'identity_card_type' => 'required',
                // 'identity_image' => 'required|mimes:jpeg,png,jpg,pdf',
                // 'address_proof_type' => 'required',
                // 'address_document' => 'required|mimes:jpeg,png,jpg,pdf',
                'password' => 'required|min:8',
//                'confirm_password' => 'required|same:password',
                'image' => 'required|mimes:jpeg,png,jpg',
                // 'profile_image' => 'required|mimes:jpeg,png,jpg',
            );

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {

                return Redirect::to('/admin/merchants/add')->withErrors($validator)->withInput();
            } else {



                unset($input['phone_number']);

                $input['country_code'] = ucfirst(trim($input['contryCode']));

                unset($input['contryCode']);

                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                    //$this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                    $input['profile_image'] = $uploadedFileName;
                } else {
                    unset($input['profile_image']);
                }

                if (Input::hasFile('image')) {

                    $file = Input::file('image');

                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);

//                    $this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);

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



                if (Input::hasFile('registration_document')) {

                    $file = Input::file('registration_document');

                    $uploadedFileName = $this->uploadImage($file, DOCUMENT_FULL_UPLOAD_PATH);

//                    $this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);

                    $input['registration_document'] = $uploadedFileName;
                } else {

                    unset($input['registration_document']);
                }

                $input['business_name'] = ucfirst(strtolower($input['business_name']));

                $input['director_name'] = ucfirst(strtolower($input['director_name']));

                $serialisedData = $this->serialiseFormData($input);

                $serialisedData['slug'] = $this->createSlug($input['business_name'], 'users');

                $serialisedData['is_verify'] = 1;

                $serialisedData['otp_verify'] = 1;
                $serialisedData['account_category'] = 'Gold';

                $serialisedData['user_type'] = 'Business';

                $serialisedData['password'] = $this->encpassword($input['password']);

//                User::insert($serialisedData);



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

                $userInfo = User::where('id', $user_id)->first();
                //for invited user
                $invtdUsrs = InvitedUser::where('Invite_email',$input['email'])->where('status', 0)->orderBy('id', 'DESC')->get();
                if (!empty($invtdUsrs)) {
                    foreach ($invtdUsrs as $invtdUsr) {
                        if (!empty($invtdUsr)) {
                            $fees_amount = 0;
                            $receiver_feed_description='';
                            $host = User::where('id', $invtdUsr->host_id)->first();
                            if($host->user_type=="Agent")
                            {
                                if (strtolower(trim($host->currency)) != strtolower(trim($userInfo->currency))) {
                                    $host_currency = trim($host->currency);
                                    $user_currency = trim($userInfo->currency);
                                    $amount = $invtdUsr->amount;   
                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $amount);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $userAmount = $convertedCurrArr[0];
                                }
                                else{
                                    $userAmount =  $invtdUsr->amount;  
                                }
                                $user=$userInfo;
                                if ($user->user_type == 'Personal') {
                                    if ($user->account_category == "Silver") {
                                        $fee_name = 'AGENT_DEPOSITE_REQUEST_SILVER';
                                    } else if ($user->account_category == "Gold") {
                                        $fee_name = 'AGENT_DEPOSITE_REQUEST_GOLD';
                                    } else if ($user->account_category == "Platinum") {
                                        $fee_name = 'AGENT_DEPOSITE_REQUEST_PLATINUM';
                                    } else if ($user->account_category == "Private Wealth") {
                                        $fee_name = 'AGENT_DEPOSITE_REQUEST_PRIVATE_WEALTH';
                                    } else {
                                        $fee_name = 'AGENT_DEPOSITE_REQUEST_SILVER';
                                    }
                                    $fees_convr = Fee::where('fee_name', $fee_name)->first();
                                    $fee_value = $fees_convr->fee_value;
                                    $fees_amount = ($userAmount * $fee_value) / 100;
                                } else if ($user->user_type == 'Business') {
                                    if ($user->account_category == "Gold") {
                                        $fee_name = 'MERCHANT_AGENT_DEPOSITE_REQUEST_GOLD';
                                    } else if ($user->account_category == "Platinum") {
                                        $fee_name = 'MERCHANT_AGENT_DEPOSITE_REQUEST_PLATINUM';
                                    } else if ($user->account_category == "Enterprises") {
                                        $fee_name = 'MERCHANT_AGENT_DEPOSITE_REQUEST_ENTERPRI';
                                    } else {
                                        $fee_name = 'MERCHANT_AGENT_DEPOSITE_REQUEST_GOLD';
                                    }
    
                                    $fees_convr = Fee::where('fee_name', $fee_name)->first();
                                    $fee_value = $fees_convr->fee_value;
                                    $fees_amount = ($userAmount * $fee_value) / 100;
                                }

                                $receiver_feed_description='##RECEIVER_FEES :'.$userInfo->currency.' '.$fees_amount;
                            }
                            
                            if (strtolower(trim($host->currency)) != strtolower(trim($userInfo->currency))) {
                                $host_currency = trim($host->currency);
                                $user_currency = trim($userInfo->currency);
                                $amount = $invtdUsr->amount;
                                $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $amount);
                                $convertedCurrArr = explode('##', $convertedCurrArr);
                                $user_invited_amount = $convertedCurrArr[0];
                                $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
                                $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                $conversion_feet = $fees_convr->fee_value;
                                $receiver_fees = ($user_invited_amount * $conversion_feet) / 100;
                                $user_invited_amount1 = $user_invited_amount - $receiver_fees-$fees_amount;

                                $user = User::where('id', $user_id)->first();
                                $user_wallet = $user->wallet_amount + $user_invited_amount1;
                                User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                                //to credit receiver fees in admin wallet
                                $admin_percentage = $this->convertCurrency($user_currency, 'USD', $conversion_feet+$fees_amount);
                                $admin_fees = explode('##', $admin_percentage)[0];
                                $adminInfo = User::where('id', 1)->first();
                                $admin_wallet = ($adminInfo->wallet_amount + $admin_fees);
                                User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

                                $trans = Transaction::where('id', $invtdUsr->trans_id)->first();
                                if (!empty($trans)) {
                                    $billing_desc = $trans->billing_description;
                                    $billing_desc .= "<br>AmountConersation: " . $trans->currency . " " . $trans->amount . " X " . $convertedCurrArr[1] . " = " . $user_currency . ' ' . $user_invited_amount . '##Conversion Fee : '.$user_currency.' ' .$receiver_fees.$receiver_feed_description;
                                    Transaction::where('id', $invtdUsr->trans_id)->update(['receiver_id' => $user_id, 'billing_description' => $billing_desc, 'receiver_fees' => $receiver_fees+$fees_amount, 'receiver_currency' => $user_currency, 'real_value' => $user_invited_amount1, 'receiver_close_bal' => $user_wallet, 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                                }
                            } else {
                                $host_currency = trim($host->currency);
                                $user_currency = trim($userInfo->currency);
                                $amount = $invtdUsr->amount;
                                $user_wallet = $userInfo->wallet_amount + $invtdUsr->amount-$fees_amount;
                                User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                                //to credit receiver fees in admin wallet
                                $admin_percentage = $this->convertCurrency($user_currency, 'USD',$fees_amount);
                                $admin_fees = explode('##', $admin_percentage)[0];
                                $adminInfo = User::where('id', 1)->first();
                                $admin_wallet = ($adminInfo->wallet_amount + $admin_fees);
                                User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
                                $trans = Transaction::where('id', $invtdUsr->trans_id)->first();
                                if (!empty($trans)) {
                                    $billing_desc = $trans->billing_description;
                                    $billing_desc .= $receiver_feed_description;
                                    Transaction::where('id', $invtdUsr->trans_id)->update(['receiver_id' => $user_id, 'receiver_currency' => $userInfo->currency,'billing_description' => $billing_desc,'receiver_fees' =>$fees_amount, 'real_value' => $amount, 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                                }
                            }

                            InvitedUser::where('id', $invtdUsr->id)->update(['status' => 1]);
                        }
                    }
                }

                if(Input::hasFile('identity_image')){
                    

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



                User::where('email', $input['email'])->update(['edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);



//                $name = $input['director_name'];
//                $emailId = $input['email'];

                $new_password = $input['password'];

//
//                $emailTemplate = DB::table('emailtemplates')->where('id', 2)->first();
//                $toRepArray = array('[!email!]', '[!name!]', '[!username!]', '[!password!]', '[!HTTP_PATH!]', '[!SITE_TITLE!]');
//                $fromRepArray = array($emailId, $name, $name, $new_password, HTTP_PATH, SITE_TITLE);
//                $emailSubject = str_replace($toRepArray, $fromRepArray, $emailTemplate->subject);
//                $emailBody = str_replace($toRepArray, $fromRepArray, $emailTemplate->template);
//                Mail::to($emailId)->send(new SendMailable($emailBody,$emailSubject, Null));



                $emailId = $input['email'];

                $userName = strtoupper($input['business_name']);



//                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2"><span>Hey </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Welcome to DafriBank<br><br>Your account has been created successfully by admin<br><br>Details are below :<br><br><strong>Email Address:</strong> ' . $emailId . '<br><br><strong>Password:</strong> ' . $new_password . '<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© '.date("Y").' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';

                $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Hey </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Welcome to DafriBank Digital<br><br>Digital Banking means banking on the go. Anytime, anywhere and we are delighted you chose us as your financial institution.<br><br>With the DafriBank Digital superior technology, bank with ease in a totally secure online environment. It\'s faster and cheaper than banking in a branch.<br><br>As a customer-centric bank, we are open to your feedback, hence, please do not hesitate to contact us anytime through our e-mail <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>.<br><br>Regards,<br>The DafriBank Digital Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/personal-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';

                $emailSubject = "Welcome Onboard";

//                $emailData['subjects'] = $emailSubject;
//
//                $emailData['userName'] = $userName;
//
//                $emailData['emailId'] = $emailId;
//
//                Mail::send('emails.onBoarding2', $emailData, function ($message)use ($emailData, $emailId) {
//
//                    $message->to($emailId, $emailId)
//                            ->subject($emailData['subjects']);
//                });
                
                $account_number = $userInfo->account_number;
                    
                    $detl = array();
                    $detl["date"] = date("d/m/Y");
                    $detl["userName"] = $userName;
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

//                    Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));



                if ($input['currency'] != 'USD') {

                    $currency_10_usd = $this->myCurrencyRate($input['currency'], 10);

                    $currency_10_usd = ceil($currency_10_usd);

                    $usdString = '(' . $input['currency'] . ' ' . $currency_10_usd . ') ';
                } else {

                    $usdString = '';
                }



                $bodyEmail = '';

                $bodyEmail .= '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Greetings</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Thank you for opening a DafriBank account. Please deposit a minimum of USD 10 ' . $usdString . 'within the next 10 days to ensure your account stays active. Visit and <a href="' . HTTP_PATH . '/business-login" target="_blank">sign in</a> to your DafriBank account to see the available funding methods.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';



                $subjectEmail = "Keep your DafriBank Account Active!";

                $emailData['subject'] = $subjectEmail;

                $emailData['userName'] = $userName;

                $emailData['emailId'] = $emailId;

                $emailData['usdString'] = $usdString;

                Mail::send('emails.onBoarding', $emailData, function ($message)use ($emailData, $emailId) {

                    $message->to($emailId, $emailId)
                            ->subject($emailData['subject']);
                });



//                    Mail::to($emailId)->send(new SendMailable($bodyEmail, $subjectEmail, Null));



                Session::flash('success_message', "Business user details saved successfully.");

                return Redirect::to('admin/merchants');
            }
        }

        return view('admin.merchants.add', ['title' => $pageTitle, $activetab => 1, 'countrList' => $countrList]);
    }

    private function myCurrencyRate($currency, $amount) {
        
        if($currency == 'NGN'){
            $exchange = Ngnexchange::where('id', 2)->first();
            
            $to = strtolower($currency);
            $var = $to.'_value';            
            
            $val = $exchange->$var;
            $total = $val * $amount;
            return $total;
        } else{
            $apikey = CURRENCY_CONVERT_API_KEY;
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

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }



        $pageTitle = 'Edit Business User';

        $activetab = 'actmerchants';

        $countrList = Country::getCountryList();



        $recordInfo = User::where('slug', $slug)->first();

        if (empty($recordInfo)) {

            return Redirect::to('admin/merchants');
        }



        $input = Input::all();

        if (!empty($input)) {



            $rules = array(
                'business_name' => 'required|max:50',
                'director_name' => 'required|max:50',
//                'email' => 'required|email',
                'password' => 'sometimes|nullable|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[\W_]/',
                'country' => 'required',
                'currency' => 'required',
                // 'profile_image' => 'mimes:jpeg,png,jpg',
            );

            $customMessages = [
                'password.regex' => 'Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.',
            ];

            $validator = Validator::make($input, $rules, $customMessages);

            if ($validator->fails()) {

                $messages = $validator->messages();

                return Redirect::to('/admin/merchants/edit/' . $slug)->withErrors($messages)->withInput();
            } else {



                DB::enableQueryLog();

                $userEmail = $input['email'];
                $userPhone = $input['phone'];
                $query = new User();
                $query = $query->where(function ($q) use ($userEmail, $userPhone) {
                    $q->where('email', $userEmail)->orWhere('phone', $userPhone);
                });
                $isExists = $query->where('id', '!=', $recordInfo->id)->first();

                //dd(DB::getQueryLog());

                if (!empty($isExists)) {

                    Session::flash('error_message', "Email/Phone already exists.");

                    return Redirect::to('/admin/merchants/edit/' . $slug);
                }

                $trans_exist = Transaction::where('user_id', $recordInfo->id)->orWhere('receiver_id', $recordInfo->id)->count();  
                if($trans_exist!=0)
                {
                if($input['currency']!=$recordInfo->currency)
                {

                $trans = Transaction::where('user_id', $recordInfo->id)->where('status',2)->count();    
                if($trans > 0)
                {
                Session::flash('error_message', "You cannot change the currency because user have some pending transaction.");
                return Redirect::to('/admin/merchants/edit/' . $slug); 
                }
                
                $deposit_amount=$recordInfo->wallet_amount;
                $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
                $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                $fees_amount = ($deposit_amount * $fees_convr->fee_value) / 100;
                $actual_amount = $deposit_amount-$fees_amount;
                $converted_amt= $this->convertCurrency($recordInfo->currency,$input['currency'], $actual_amount);
                //total amount which will added into user wallet
                $total_amt=explode("##",$converted_amt)[0];
                $convertedCurrArr=explode("##",$converted_amt)[1];
                User::where('id', $recordInfo->id)->update(['wallet_amount' => $total_amt, 'updated_at' => date('Y-m-d H:i:s')]);

                // to add fees into admin wallet amount
                $admin_converted_amt= $this->convertCurrency($recordInfo->currency,'USD', $fees_amount);
                $total_admin_fee=explode("##",$admin_converted_amt)[0];
                $adminInfo = User::where('id', 1)->first();
                $admin_wallet = ($adminInfo->wallet_amount + $total_admin_fee);
                User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
                $refrence_id = time() . rand() . Session::get('user_id');

                $input['updated_currency_date']=date('Y-m-d H:i:s');

                $billing_description = "<br>Amount Conersation: " . $recordInfo->currency . " " . $actual_amount . " X " . $convertedCurrArr . " = ".$input['currency'].' ' . $total_amt . '##Conversion Fee : '.$recordInfo->currency.' '.$fees_amount;
                $trans = new Transaction([
                "user_id" => $recordInfo->id,
                "receiver_id" => 0,
                "amount" => $total_amt,
                "fees" => $fees_amount,
                "sender_fees" => $fees_amount,
                "sender_currency"=>$recordInfo->currency,
                "currency" => $input['currency'],
                "receiver_currency"=>'USD',
                "trans_type" => 1, //credit
                "trans_to" => 'Dafri_Wallet',
                "trans_for" => 'Converted Amount',
                "refrence_id" => $refrence_id,
                "billing_description" => $billing_description,
                "user_close_bal" => $total_amt,
                "receiver_close_bal" => $admin_wallet,
                "real_value"=>$total_amt,
                "status" => 1,
                "edited_by" => Session::get('adminid'),
                "created_at" =>date('Y-m-d H:i:s',time() + 10),
                "updated_at" => date('Y-m-d H:i:s',time() + 10),
                 ]);
                 $trans->save();

                }

               }

                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                    //$this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                    $input['profile_image'] = $uploadedFileName;
                } else {
                    unset($input['profile_image']);
                }


                if (Input::hasFile('image')) {

                    $file = Input::file('image');

                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);

//                    $this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);

                    $input['image'] = $uploadedFileName;

                    @unlink(PROFILE_FULL_UPLOAD_PATH . $recordInfo->image);
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



                if (Input::hasFile('registration_document')) {

                    $file = Input::file('registration_document');

                    $uploadedFileName = $this->uploadImage($file, DOCUMENT_FULL_UPLOAD_PATH);

//                    $this->resizeImage($uploadedFileName, DOCUMENT_FULL_UPLOAD_PATH, DOCUMENT_SMALL_UPLOAD_PATH, DOCUMENT_MW, DOCUMENT_MH);

                    $input['registration_document'] = $uploadedFileName;
                } else {

                    unset($input['registration_document']);
                }



                if ($input['password']) {

                    $input['password'] = $this->encpassword($input['password']);
                } else {

                    unset($input['password']);
                }

                $input['business_name'] = ucfirst(strtolower($input['business_name']));

                $input['director_name'] = ucfirst(strtolower($input['director_name']));

                $input['edited_by'] = Session::get('adminid');

                $input['phone'] = $input['phone'];
                unset($input['image']);

                $serialisedData = $this->serialiseFormData($input, 1); //send 1 for edit
//echo '<pre>';print_r($serialisedData);exit;
                User::where('id', $recordInfo->id)->update($serialisedData);
                
                if(Input::hasFile('identity_image')){
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



                Session::flash('success_message', "Business user details updated successfully.");

                return Redirect::to('admin/merchants');
            }
        }

        return view('admin.merchants.edit', ['title' => $pageTitle, $activetab => 1, 'countrList' => $countrList, 'recordInfo' => $recordInfo]);
    }

    public function kycdetail($slug = null) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'View User KYC Detail';

        $activetab = 'actmerchants';

        $userInfo = User::where('slug', $slug)->first();



        return view('admin.merchants.kycdetail', ['title' => $pageTitle, $activetab => 1, 'userInfo' => $userInfo]);
    }

    public function approvekyc($slug = null) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        if ($slug) {

            $is_esixt=User::where('slug', $slug)->first();
            // if($is_esixt->is_kyc_done==1 || $is_esixt->is_kyc_done==2)
            // {
            //     Session::flash('error_message', "Kyc status is already updated");
            //     return Redirect::to('admin/merchants/kycdetail/'.$slug);
            // }

            User::where('slug', $slug)->update(array('is_kyc_done' => '1','back_identity_status'=>'1','identity_status'=>'1','selfie_status'=>'1','address_status'=>'1', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));



            $userInfo = DB::table('users')->where('slug', $slug)->first();



            $username = strtoupper($userInfo->business_name);

            $emailId = $userInfo->email;



            if (strtolower($userInfo->user_type) == "personal") {

                $lognLnk = HTTP_PATH . "/personal-login";
            } else {

                $lognLnk = HTTP_PATH . "/business-login";
            }



            $emailSubject = 'KYC information has been reviewed successfully';

            //$emailBody = 'Dear '.$username.',<br><br>We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank '.$userInfo->user_type.' account has now been approved. Click <a href="'.$lognLnk.'" target="_blank">here</a> to log in to your account.<br><br>We wish you an awesome banking experience with us.<br><br>Best regards,<br>The DafriBank Team';

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="150"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Dear </span> ' . $username . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank ' . $userInfo->user_type . ' account has now been approved. Click <a href="' . $lognLnk . '" target="_blank">here</a> to log in to your account.<br><br>We wish you an awesome banking experience with us.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E ">Have questions or help ? Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page.</p></td></tr></table></td></tr></tbody></table></body></html>';



            $emailData['subject'] = $emailSubject;

            $emailData['username'] = strtoupper($username);



            Mail::send('emails.kycReviewd', $emailData, function ($message)use ($emailData, $emailId) {

                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });

//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            //echo $emailBody; exit;



            Session::flash('success_message', "User KYC approved successfully.");

            return Redirect::to('admin/merchants/kycdetail/' . $slug);
        }
    }

    public function declinekyc($slug = null) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        if ($slug) {

            $is_esixt=User::where('slug', $slug)->first();
            // if($is_esixt->is_kyc_done==1 || $is_esixt->is_kyc_done==2)
            // {
            //     Session::flash('error_message', "Kyc status is already updated");
            //     return Redirect::to('admin/merchants/kycdetail/'.$slug);
            // }

            User::where('slug', $slug)->update(array('is_kyc_done' => '2','back_identity_status'=>'2','identity_status'=>'2','selfie_status'=>'2','address_status'=>'2', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

            $userInfo = DB::table('users')->where('slug', $slug)->first();



            $username = strtoupper($userInfo->business_name);

            $emailId = $userInfo->email;



            if (strtolower($userInfo->user_type) == "personal") {

                $lognLnk = HTTP_PATH . "/personal-login";
            } else {

                $lognLnk = HTTP_PATH . "/business-login";
            }



            $emailSubject = "your KYC information was not approved";

            $emailBody = "Dear " . $username . ",<br><br>Unfortunately, your KYC information was not approved. Please <a href='" . $lognLnk . "' target='_blank'>login</a> to your account and re-submit information in the sections that are marked as rejected. Once you do that, we will review the information and inform you via email about your status.<br><br>We look forward to receiving your new KYC information.<br><br>Best regards,<br>The DafriBank Team";

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="150"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Dear </span> ' . $username . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your KYC information was not approved. Please <a href="' . $lognLnk . '" target="_blank">login</a> to your account and re-submit information in the sections that are marked as rejected. Once you do that, we will review the information and inform you via email about your status.<br><br>We look forward to receiving your new KYC information.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E ">Have questions or help ? Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page.</p></td></tr></table></td></tr></tbody></table></body></html>';

            $emailData['subject'] = $emailSubject;

            $emailData['username'] = $username;



            Mail::send('emails.kycDeclined', $emailData, function ($message)use ($emailData, $emailId) {

                $message->to($emailId, $emailId)
                        ->subject($emailData['subject']);
            });



//                        Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));



            Session::flash('success_message', "User KYC declined successfully.");

            return Redirect::to('admin/merchants/kycdetail/' . $slug);
        }
    }

    public function activate($slug = null) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        /* if ($isPermitted == false) {

          $pageTitle = 'Not Permitted';

          $activetab = 'actchangeusername';

          return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);

          } */

        if ($slug && $isPermitted == true) {

            User::where('slug', $slug)->update(array('is_verify' => '1', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

            return view('elements.admin.update_status', ['action' => 'admin/merchants/deactivate/' . $slug, 'status' => 1, 'id' => $slug]);
        } else {
            
        }
    }

    public function deactivate($slug = null) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        if ($slug && $isPermitted == true) {

            User::where('slug', $slug)->update(array('is_verify' => '0', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

            return view('elements.admin.update_status', ['action' => 'admin/merchants/activate/' . $slug, 'status' => 0, 'id' => $slug]);
        }
    }

    /* public function delete($slug = null) {

      if ($slug) {

      User::where('slug', $slug)->delete();

      Session::flash('success_message', "Business user details deleted successfully.");

      return Redirect::to('admin/merchants');

      }

      } */

    public function deleteimage($slug = null) {

        if ($slug) {

            $recordInfo = DB::table('users')->where('slug', $slug)->select('users.profile_image')->first();

            User::where('slug', $slug)->update(array('profile_image' => ''));

            @unlink(PROFILE_FULL_UPLOAD_PATH . $recordInfo->profile_image);

            Session::flash('success_message', "Image deleted successfully.");

            return Redirect::to('admin/merchants/edit/' . $slug);
        }
    }

    public function deleteidentity($slug = null) {

        if ($slug) {

            $recordInfo = DB::table('users')->where('slug', $slug)->select('users.identity_image')->first();

            User::where('slug', $slug)->update(array('identity_image' => ''));

            @unlink(IDENTITY_FULL_UPLOAD_PATH . $recordInfo->identity_image);

            Session::flash('success_message', "Image deleted successfully.");

            return Redirect::to('admin/users/edit/' . $slug);
        }
    }

    public function agentRequest($slug) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');

        if ($isPermitted == false) {

            $pageTitle = 'Not Permitted';

            $activetab = 'actchangeusername';

            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }



        $pageTitle = 'Create Agent Request';

        $activetab = 'actmerchants';

        $input = Input::all();

        if (!empty($input)) {

            $rules = array(
                'director_name' => 'required',
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
                'director_name.required' => 'First name field can\'t be left blank.',
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

                return Redirect::to('/admin/merchants/agentRequest/' . $slug)->withErrors($validator)->withInput();
            } else {

                $recordInfo = User::where('id', $slug)->first();

                if ($input['commission'] < 2 or $input['commission'] > 9) {

                    Session::flash('error_message', "Commission rate should be between 2% to 9%.");

                    return Redirect::to('/admin/merchants/agentRequest/' . $slug);
                } else if ($recordInfo->wallet_amount < 250) {

                    Session::flash('error_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > 250.");

                    return Redirect::to('/admin/merchants/agentRequest/' . $slug);
                } else if ($recordInfo->is_kyc_done != 1) {

                    Session::flash('error_message', "Your request not accepted as your KYC is not completed.");

                    return Redirect::to('/admin/merchants/agentRequest/' . $slug);
                } else {

                    if (Input::hasFile('profile_image')) {

                        $file = Input::file('profile_image');

                        $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);

                        //$this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);

                        $profile_image = $uploadedFileName;
                    } else {

                        $profile_image = 'pro-img.jpg';
                    }



                    $agnt = new Agent([
                        "id" => $slug,
                        'user_id' => $slug,
                        'first_name' => $input['director_name'],
                        'last_name' => $input['last_name'],
                        'country' => $input['country'],
                        'commission' => $input['commission'],
                        'min_amount' => $input['min_deposit'],
                        'address' => $input['address'],
                        'phone' => $input['phone'],
                        'email' => $input['email'],
                        'payment_methods' => $input['payment_method'],
                        'description' => $input['desc'],
                        'profile_image' => $profile_image,
                        'is_approved' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $agnt->save();

                    User::where('id', $slug)->update(['user_type' => 'Agent', 'updated_at' => date('Y-m-d H:i:s')]);

                    Session::flash('success_message', "Agent created successfully.");

                    return Redirect::to('admin/merchants');
                }
            }
        }

        $countrList = Country::getCountryList();

        $user = User::where('id', $slug)->first();

        return view('admin.merchants.agentRequest', ['title' => $pageTitle, $activetab => 1, 'user' => $user, 'countrList' => $countrList]);
    }

    public function apiActivate($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');
        /* if ($isPermitted == false) {
          $pageTitle = 'Not Permitted';
          $activetab = 'actchangeusername';
          return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);
          } */

        $useerr = User::where('slug', $slug)->first();
        // print_r($useerr->api_key);die;
        if (!empty($useerr->api_key)) {
            $apikey = $useerr->api_key;
        } else {
            $apikey = $this->generate_string(12);
        }


        if ($slug && $isPermitted == true) {
            User::where('slug', $slug)->update(array('api_enable' => 'Y', 'api_key' => $apikey, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));
            return view('elements.admin.update_apistatus', ['action' => 'admin/merchants/api-deactivate/' . $slug, 'status' => 1, 'id' => $slug, 'apikey' => $apikey]);
        } else {
            
        }
    }

    public function apiDeactivate($slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-business-user');
        if ($slug && $isPermitted == true) {
            User::where('slug', $slug)->update(array('api_enable' => 'N', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));
            return view('elements.admin.update_apistatus', ['action' => 'admin/merchants/api-activate/' . $slug, 'status' => 0, 'id' => $slug]);
        }
    }

    private function generate_string($strength = 12) {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    private function convertCurrency($toCurrency, $frmCurrency, $amount) {
        //        echo $toCurrency;
        //        echo $frmCurrency;
        //        echo $amount;
                if ($frmCurrency == 'NGN') {
                    $exchange = Ngnexchange::where('id', 1)->first();
                    $to = strtolower($toCurrency);
                    $var = $to . '_value';
        
                    $val = $exchange->$var;
                    $total = $val * $amount;
                    return $total . "##" . $val;
                } else if ($toCurrency == 'NGN') {
                    $exchange = Ngnexchange::where('id', 2)->first();
                    $to = strtolower($frmCurrency);
                    $var = $to . '_value';
        
                    $val = $exchange->$var;
                    $total = $amount * $val;
                    return $total . "##" . $val;
                } else {
                    $apikey = CURRENCY_CONVERT_API_KEY;
                    $query = $toCurrency . "_" . $frmCurrency;
                    $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
        
                    $json = file_get_contents($curr_req);
                    $obj = json_decode($json, true);
                    $val = floatval($obj[$query]);
                    $total = $val * $amount;
                    return $total . "##" . $val;
                }
            }


}

?>