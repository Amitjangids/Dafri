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
use App\DbaTransaction;
use App\GetInTouch;
use App\Support;
use App\Contact;
use App\Walletlimit;
use App\InvitedUser;
use App\WithdrawRequest;
use App\InactiveAmount;
use App\Agentlimit;
use App\CryptoDeposit;
use App\DbaDeposit;
use App\CryptoWithdraw;
use App\DbaWithdraw;
use App\ManualDeposit;
use App\ManualWithdraw;
use App\Notification;
use App\Referalcode;
use App\ReferralCommission;
use App\AgentsTransactionLimit;
use App\Models\Country;
use App\Models\CryptoCurrency;
use App\Models\Ngnexchange;
use App\Models\WalletLimitUser;
use App\Models\GiftCard;
use App\Models\GiftAirtimeSetting;
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

    public function total_fee_collection(Request $request)
    {
    $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-user-wallet');
    if ($isPermitted == false) {
           $pageTitle = 'Not Permitted';
           $activetab = 'actchangeusername';
           return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
    }

    $pageTitle = 'Total Fee Collections';
    $activetab = 'acttotalfee';

    if ($request->has('date') && $request->get('date')) {
        $dateArr = explode("/", $request->get('date'));
        $to = $dateArr[0];
        $from =  date('Y-m-d',strtotime($dateArr[1] . ' +1 days'));
        $toDate = $dateArr[0];
        $frmDate = $dateArr[1];
    } else {
        $transCalDays = 30;
        $chkTransDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $transCalDays . ' days'));
        $to = $chkTransDate;
        $from =  date('Y-m-d', strtotime(date('Y-m-d') . ' +1 days'));
        $toDate = $chkTransDate;
        $frmDate = date('Y-m-d');
    }

    global $currencyList;
    $fees_collection=array();
    foreach($currencyList as $currency)
    {
        //to calculate sender fess
        $sender_fees_total=Transaction::where('sender_currency', $currency)->where('id','>=','8184')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

        //to calculate receiver fees 
        $receiver_fees_total=Transaction::where('receiver_currency', $currency)->where('id','>=','8184')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

        //to calculate fee before date 21 december 2021 which don't have online payment transaction type
        $sender_fees_before_22_desc=Transaction::where('currency', $currency)->where('id','<=','8183')->where('trans_for', 'not like', '%ONLINE_PAYMENT%')->whereBetween('created_at', array($to, $from))->sum('transactions.fees');

        //to calculate sender fee before 8183 which have online payment transaction type
        $sender_fees_before_22_desc_online=Transaction::where('sender_currency', $currency)->where('id','<=','8183')->where('trans_for', 'like', '%ONLINE_PAYMENT%')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

         //to calculate receiver fee before 8183 which have online payment transaction type
         $receiver_fees_before_22_desc_online=Transaction::where('receiver_currency', $currency)->where('id','<=','8183')->where('trans_for', 'like', '%ONLINE_PAYMENT%')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');



        $total_fees=$sender_fees_total+$receiver_fees_total+$sender_fees_before_22_desc+$sender_fees_before_22_desc_online+$receiver_fees_before_22_desc_online;
      
        $fees_collection[$currency]=$total_fees;    
    }

    return view('admin.users.total_fee_collection', ['title' => $pageTitle, $activetab => 1,'allrecords'=>$fees_collection,'toDate' => $toDate, 'frmDate' => $frmDate]);

    }

    public function index(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        DB::enableQueryLog();
        $countrList = Country::getCountryList();
        $pageTitle = 'Manage Personal Users';
        $activetab = 'actusers';
        $query = new User();
        $query = $query->sortable();
        //$query = $query->where('user_type', 'Personal');
        $role = 'Agent';
        $query = $query->orWhere(function ($q) use ($role) {
            $q->where('user_type', 'Personal')
                    ->orWhere(function($q) use ($role){
                $q = $q->where('user_type', $role)->where('first_name', '!=', '');
            });
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
        //DB::enableQueryLog();
        //$users = $query->orWhere('user_type','Agent')->orderBy('id', 'DESC')->paginate(20);
        $users = $query->orderBy('id', 'DESC')->paginate(20);
        $users_count = $query->orderBy('id', 'DESC')->count();
        //dd(DB::getQueryLog());
//echo '<pre>';print_r($users);exit;
        if ($request->ajax()) {
            return view('elements.admin.users.index', ['allrecords' => $users,'users_count'=>$users_count]);
        }
        return view('admin.users.index', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $users,'countrList'=>$countrList,'users_count'=>$users_count]);
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
                return Redirect::to('/admin/users/add')->withErrors($validator)->withInput();
            } else {
                unset($input['phone_number']);
//                unset($input['contryCode']);

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
                                $convr_fee_name =$host_currency!="NGN" && $userInfo->currency!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';
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
                                    $billing_desc .= "<br>Amount " . $trans->currency . " " . $trans->amount . " and Conversion rate " . $convertedCurrArr[1] . " = " . $user_currency . ' ' . $user_invited_amount . '##Conversion Fee : '.$user_currency.' ' .$receiver_fees.$receiver_feed_description;
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

        if ($currency == 'NGN') {
            $exchange = Ngnexchange::where('id', 1)->first();

            $val = $exchange->usd_value;
            $total = $val * $amount;
            return $total;
        } else {
            $apikey = CURRENCY_CONVERT_API_KEY;
            if ($currency == 'EURO') {
                $query = "USD_EUR";
            } else {
                $query = "USD_" . $currency;
            }
            $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;

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
                //   'profile_image' => 'mimes:jpeg,png,jpg',
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

                $trans_exist = Transaction::where('user_id', $recordInfo->id)->orWhere('receiver_id', $recordInfo->id)->count();  
                if($trans_exist!=0)
                {
                if($input['currency']!=$recordInfo->currency)
                {
                $trans = Transaction::where('user_id', $recordInfo->id)->where('status',2)->count();  
                if($trans > 0)
                {
                Session::flash('error_message', "You cannot change the currency because user have some pending transaction.");
                return Redirect::to('/admin/users/edit/' . $slug); 
                }

                $deposit_amount=$recordInfo->wallet_amount;
                $convr_fee_name = 'CONVERSION_FEE';
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
                $input['updated_currency_date']=date('Y-m-d H:i:s');

                $refrence_id = time() . rand() . Session::get('user_id');
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
                "created_at" => date('Y-m-d H:i:s',time() + 10),
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

            $is_esixt=User::where('slug', $slug)->first();
            // if($is_esixt->is_kyc_done==1 || $is_esixt->is_kyc_done==2)
            // {
            //     Session::flash('error_message', "Kyc status is already updated");
            //     return Redirect::to('admin/users/kycdetail/'.$slug); 
            // }
            User::where('slug', $slug)->update(array('is_kyc_done' => '1','back_identity_status'=>'1','identity_status'=>'1','selfie_status'=>'1','address_status'=>'1', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

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

            $is_esixt=User::where('slug', $slug)->first();
            // if($is_esixt->is_kyc_done==1 || $is_esixt->is_kyc_done==2)
            // {
            //     Session::flash('error_message', "Kyc status is already updated");
            //     return Redirect::to('admin/users/kycdetail/'.$slug);
            // }

            User::where('slug', $slug)->update(array('is_kyc_done' => '2','back_identity_status'=>'2','identity_status'=>'2','selfie_status'=>'2','address_status'=>'2', 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')));

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
            if ($currency == 'NGN') {
                $exchange = Ngnexchange::where('id', 1)->first();
                $to = strtolower('USD');
                $var = $to . '_value';

                $val = $exchange->$var;
                $total = $amount / $val;
                return $total;
            } else {
                $apikey = CURRENCY_CONVERT_API_KEY;

                if ($currency == 'EURO') {
                    $query = "EUR_USD";
                } else {
                    $query = $currency . "_USD";
                }
                $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
                //"https://free.currconv.com/api/v7/convert?q=".$query."&compact=ultra&apiKey=".$apikey	
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
                if ($user_wallet_usd < 250) {
                    Session::flash('error_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > USD 250.");
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
            $agent = Agent::where('id', $slug)->first();
            $recordInfo = User::where('id', $agent->user_id)->first();
            $user_wallet_usd = $this->fetchCurrencyRate($recordInfo->currency, $recordInfo->wallet_amount);
            if ($user_wallet_usd < 250) {
                Session::flash('error_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > USD 250.");
                return Redirect::to('admin/users/bank-agent-request');
            } 
            
            if ($recordInfo->is_kyc_done != 1) {
                Session::flash('error_message', "Your request not accepted as your KYC is not completed.");
                return Redirect::to('admin/users/bank-agent-request');
            }

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

                $trans_exist = WithdrawRequest::where('agent_id', $slug)->where('req_type', 'Agent')->orderBy('id', 'DESC')->count();  
                if($trans_exist > 0)
                {
                 Session::flash('error_message', "You cannot disapprove the agent because agent have pending transacion");
                 return Redirect::to('/admin/users/disapproveAgent/' . $slug);   
                }

                Agent::where("id", $slug)->update(['is_approved' => 2, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
                $userID = Agent::where("id", $slug)->first();
               // Agent::where("id", $slug)->delete();

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

                Agent::where("id", $slug)->delete();
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
            $transCalDays = 365;
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
        $trans = $query->orderBy("updated_at", "DESC")->paginate(25);
//        echo '<pre>';print_r($trans);exit;
       // dd(DB::getQueryLog());
        if ($request->ajax()) {
            return view('elements.admin.users.transactionLists', ['allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'userInfo' => $userInfo, 'admin' => $admin]);
        }

        return view('admin.users.transactionLists', ['title' => $pageTitle, 'slug' => $slug, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'userInfo' => $userInfo, 'admin' => $admin]);
    }

    public function wallet_summary(Request $request, $slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'view-transaction-report');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $admin = Admin::all();
        $pageTitle = 'DBACash Back.';
        $activetab = 'actdbacash';
        $userInfo = User::where("slug", $slug)->first();
        return view('admin.users.wallet_summary', ['title' => $pageTitle, 'slug' => $slug, $activetab => 1,'userInfo' => $userInfo,'adminInfo'=>$admin]);
    }

    public function dbatransactionLists(Request $request, $slug = null) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'view-transaction-report');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $userInfo = User::where("slug", $slug)->first();

        DB::enableQueryLog();
        $pageTitle = 'DBA Transaction Report';
        $activetab = 'actdbatransactionreport';
        $query = new DbaTransaction();
        $admin = Admin::all();
        $query = $query->sortable();
        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');

            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'like', '%' . $keyword . '%')->orWhere('business_name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('Receiver', function ($q) use ($keyword) {
                            $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%");
                        });
            });
        } else {
            $keyword = '';
        }

        if ($request->has('date') && $request->get('date')) {
            
            $dateArr = explode("/", $request->get('date'));
            $to = $dateArr[0] . " 00:00:00";
            $from = $dateArr[1] . " 23:59:59";
            $query = $query->where(function ($q) use ($to, $from) {
                $q->whereBetween("created_at", array($to, $from));
            });
            $toDate = $dateArr[0];
            $frmDate = $dateArr[1];
        } else {
            $transCalDays = 365;
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

        $trans = $query->orderBy("updated_at", "DESC")->paginate(25);
        if ($request->ajax()) {
            return view('elements.admin.users.dbatransactionLists', ['allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'userInfo' => $userInfo, 'admin' => $admin]);
        }     

        return view('admin.users.dbatransactionLists', ['title' => $pageTitle, 'slug' => $slug, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'userInfo' => $userInfo, 'admin' => $admin]);
    }   


    public function manageGARequest(Request $request,$slug) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'gift-card-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';  
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        DB::enableQueryLog();

        if($slug=="giftcard")
        {
        $pageTitle = 'GiftCard Request';
        $activetab = 'actgiftcardreq';
        }
        else{
        $pageTitle = 'Top Up Request';
        $activetab = 'acttopuprequest';   
        }

        $query = new Transaction();
        $query = $query->sortable();
        if($slug=="giftcard")
        {
        $query=$query->where(function ($q) {
        $q->where('trans_for','GIFT CARD');
        $q->orWhere('trans_for','GIFT CARD(Refund)');
        });
        }
        else{
        $query=$query->where(function ($q) {
        $q->where('trans_for','Mobile Top-up');
        $q->orWhere('trans_for','Mobile-TopUp(Refund)');
        });    
        }

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('Receiver', function ($q) use ($keyword) {
                            $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%")->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        });
            });
        } else {
            $keyword = '';
        }

        if ($request->has('date') && $request->get('date')) {
            $dateArr = explode("/", $request->get('date'));
            $to = $dateArr[0] . " 00:00:00";
            $from = $dateArr[1] . " 23:59:59";
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

        if ($request->has('transaction_id') && $request->get('transaction_id')) {
            $transaction_id = $request->get('transaction_id');
            if ($transaction_id != '-1') {
                $query = $query->where(function ($q) use ($transaction_id) {
                $q->orWhere("id", $transaction_id)->orWhere("refrence_id", $transaction_id);
                });
            }
        } else {
            $transaction_id = '';
        }

        if ($request->has('trans_for') && $request->get('trans_for')) {
            $trans_for = $request->get('trans_for');
            if ($trans_for != '-1') {
                $query = $query->where(function ($q) use ($trans_for) {

                 
                    $q->orWhere("trans_for",'like', $trans_for);
                });
            }
        } else {
            $trans_for = '';
        }

        $trans = $query->orderBy("id", "DESC")->paginate(25);
        $admin = Admin::all();

        $groupedResources = Transaction::select(['trans_for', 'trans_for'])
        ->groupBy('trans_for')->orderBy("trans_for", "ASC")->get()->toarray();
        

      //echo "<pre>";  print_r($groupedResources); die;
        if ($request->ajax()) {
            return view('elements.admin.users.manageGAtransReport', ['allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword,'transaction_id'=>$transaction_id, 'admin' => $admin,'groupedResources'=>$groupedResources,'trans_for'=>$trans_for]);
        }

        return view('admin.users.manageGARequest', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword,'transaction_id'=>$transaction_id, 'admin' => $admin,'groupedResources'=>$groupedResources,'trans_for'=>$trans_for,'slug'=>$slug]);
    }




    public function transactionReport(Request $request) {
        $time_start = microtime(true);
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
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
//                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%");
                        })
                        ->orWhereHas('Receiver', function ($q) use ($keyword) {
                            //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                            $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%")->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
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


        if ($request->has('transaction_id') && $request->get('transaction_id')) {
            $transaction_id = $request->get('transaction_id');
            if ($transaction_id != '-1') {
                $query = $query->where(function ($q) use ($transaction_id) {

                 
                    $q->orWhere("id", $transaction_id)->orWhere("refrence_id", $transaction_id);
                });
            }
        } else {
            $transaction_id = '';
        }
        if ($request->has('trans_for') && $request->get('trans_for')) {
            $trans_for = $request->get('trans_for');
            if ($trans_for != '-1') {
                $query = $query->where(function ($q) use ($trans_for) {

                 
                    $q->orWhere("trans_for",'like', $trans_for);
                });
            }
        } else {
            $trans_for = '';
        }

        $trans = $query->orderBy("id", "DESC")->paginate(25);
        $admin = Admin::all();


        $groupedResources = Transaction::select(['trans_for', 'trans_for'])
        ->groupBy('trans_for')->where('trans_for','!=','Withdraw##2')->orderBy("trans_for", "ASC")->get()->toarray();
        
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
      //echo "<pre>";  print_r($groupedResources); die;
        if ($request->ajax()) {
            return view('elements.admin.users.transReport', ['execution_time'=>$execution_time,'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword,'transaction_id'=>$transaction_id, 'admin' => $admin,'groupedResources'=>$groupedResources,'trans_for'=>$trans_for]);
        }

        return view('admin.users.transactionReport', ['execution_time'=>$execution_time,'title' => $pageTitle, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword,'transaction_id'=>$transaction_id, 'admin' => $admin,'groupedResources'=>$groupedResources,'trans_for'=>$trans_for]);
    }



    

    public function dbatransactionReport(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'view-transaction-report');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        DB::enableQueryLog();
        $pageTitle = 'DBA Transaction Report';
        $activetab = 'actdbatransactionreport';
        $query = new DbaTransaction();
        $query = $query->sortable();

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
           // echo $keyword; die;
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('Receiver', function ($q) use ($keyword) {
                            $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'LIKE', "%" . $keyword . "%")->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        });
            });
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

        if ($request->has('transaction_id') && $request->get('transaction_id')) {
            $transaction_id = $request->get('transaction_id');
            if ($transaction_id != '-1') {
                $query = $query->where(function ($q) use ($transaction_id) {

                 
                    $q->orWhere("id", $transaction_id)->orWhere("refrence_id", $transaction_id);
                });
            }
        } else {
            $transaction_id = '';
        }

        $trans = $query->orderBy("id", "DESC")->paginate(25);
        $admin = Admin::all();
        //dd(DB::getQueryLog());
        if ($request->ajax()) {
            return view('elements.admin.users.dbatransReport', ['allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'admin' => $admin,'transaction_id'=>$transaction_id]);
        }

        return view('admin.users.dbatransactionReport', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $trans, 'toDate' => $toDate, 'frmDate' => $frmDate, 'currency' => $currency, 'keyword' => $keyword, 'admin' => $admin,'transaction_id'=>$transaction_id]);
    }

    public function exportCSV($keyword, $from_date,$to_date, $currency) {
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

        if ($from_date != "") {
           // $dateArr = explode(" - ", $dateRange);
            $to = $from_date . " 00:00:00";
            $from = $to_date . " 23:59:59";

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
        $line = array("Trans. ID", "Sender ", "Receiver ","Amount ", "Receiver Fees ","Sender Fees", "Trans. Type ", "Ref ID ", "Status ", "Date ");
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

            $amount = number_format($tran->amount, 2, '.', ',').' '.$tran->currency;
            if($tran->sender_fees=="0.0000000000" && $tran->receiver_fees=="0.0000000000")
            {
            $sender_fees=number_format($tran->fees,2,'.',',').' '.$tran->currency;    
            }
            else{
            $sender_fees=number_format($tran->sender_fees,2,'.',',').' '.$tran->sender_currency;       
            }

            if($tran->sender_fees=="0.0000000000" && $tran->receiver_fees=="0.0000000000")
            {
            $receiver_fees=number_format($tran->fees,2,'.',',').' '.$tran->currency; 
            }
            else{
            $receiver_fees=number_format($tran->receiver_fees,2,'.',',').' '.$tran->receiver_currency;       
            }



            $paymentTypArr = explode('##', $tran->trans_for);
            $paymentType = $paymentTypArr[0];
            if ($tran->refrence_id == 'na') {
                $refID = 'N/A';
            } else {
                $refID = $tran->refrence_id.' ';
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
            $line = array($TransId, $sender, $receiver,$amount, $receiver_fees,$sender_fees, $paymentType, '"'.$refID.'"', $status, $created_at);
            fputcsv($f, $line, $delimiter);
        }//Foreach loop end
        fseek($f, 0);
        //ob_end_clean();
        fpassthru($f);
        fclose($f);
        ob_flush();
        exit;
    }

    public function dbaexportCSV($keyword, $from_date,$to_date, $currency) {  
        DB::enableQueryLog();
        $query = new DbaTransaction(); 
        $query = $query->sortable();

        if ($keyword != "na") {
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('director_name', 'LIKE', "%" . $keyword . "%");
                });
            });
        }

        if ($from_date != "") {
           // $dateArr = explode(" - ", $dateRange);
            $to = $from_date . " 00:00:00";
            $from = $to_date . " 23:59:59";

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
        $line = array("Trans. ID", "Sender ", "Receiver ","Amount ", "Receiver Fees ","Sender Fees", "Trans. Type ", "Ref ID ", "Status ", "Date ");
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

            $amount = number_format($tran->amount, 2, '.', ',').' '.$tran->currency;
            if($tran->sender_fees=="0.0000000000" && $tran->receiver_fees=="0.0000000000")
            {
            $sender_fees=number_format($tran->fees,2,'.',',').' '.$tran->currency;    
            }
            else{
            $sender_fees=number_format($tran->sender_fees,2,'.',',').' '.$tran->sender_currency;       
            }

            if($tran->sender_fees=="0.0000000000" && $tran->receiver_fees=="0.0000000000")
            {
            $receiver_fees=number_format($tran->fees,2,'.',',').' '.$tran->currency; 
            }
            else{
            $receiver_fees=number_format($tran->receiver_fees,2,'.',',').' '.$tran->receiver_currency;       
            }



            $paymentTypArr = explode('##', $tran->trans_for);
            $paymentType = $paymentTypArr[0];
            if ($tran->refrence_id == 'na') {
                $refID = 'N/A';
            } else {
                $refID = $tran->refrence_id.' ';
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
            $line = array($TransId, $sender, $receiver,$amount, $receiver_fees,$sender_fees, $paymentType, '"'.$refID.'"', $status, $created_at);
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
        $query = $query->where('fees_for', 1)->orderBy('fee_name','ASC');

        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');

            $query = $query->where(function ($q) use ($keyword) {
                $q = $q->where('fee_name', 'LIKE', "%" . $keyword . "%")->orWhere('fee_value', 'LIKE', "%" . $keyword . "%")->orderBy('fee_name','ASC');
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
                $fees=Fee::where('id', $slug)->first();
                $user_type=$fees->fees_for;
                Fee::where('id', $slug)->update(['fee_value' => $input['fee_value'], 'edited_by' => Session::get('adminid')]);
                Session::flash('success_message', "Fees value updated successfully.");
                if($user_type==1)
                {
                return Redirect::to('admin/fees/list-fees');
                }
                else{
                return Redirect::to('admin/fees/list-merchant-fees');   
                }
            }
        }

        $fee = Fee::where('id', $slug)->first();
        if($fee->fees_for==1)
        {
        $activetab = 'actconfigurefees';
        }
        else{
        $activetab = 'actconfiguremerchantfees';
        }
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
        // echo"<pre>";print_r($input);die;
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
                } else if ($recordInfo->wallet_amount < 250) {
                    
                    Session::flash('error_message', "Your request not accepted as your wallet don't have sufficient balance, Wallet amount should be > 250.");
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
                        'id' => $slug,
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

    public function getintouch(Request $request) {

        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'get in touch');
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
            return view('elements.admin.users.getInTouchRequest', ['allrecords' => $requests]);
        }

        return view('admin.users.getintouch', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $requests]);
    }
   
    public function approvegethelp($slug) {
    GetInTouch::where('id', $slug)->update(['status'=>'Y']);
    Session::flash('success_message', "This request status has been completed now");
    return Redirect::to('admin/users/get-in-touch-request');
    }

    public function getintouchdelete($slug) {
    GetInTouch::where('id', $slug)->delete();
    Session::flash('success_message', "This request status has been deleted now");
    return Redirect::to('admin/users/get-in-touch-request');
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
                $user_name = $this->personal_short_name($user);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = $this->personal_short_name($user);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Agent' && $user->business_name != "") {
                $user_name = $this->business_short_name($user);
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
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Agent' && $user->business_name != "") {
                $user_name = $this->business_short_name($user);
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
        $exchange_second = Ngnexchange::where('id', 2)->first();
        $actual_value = Ngnexchange::where('id', 3)->first();

        $input = Input::all();
        if (!empty($input)) {
            // echo "<pre>";
            // print_r($input); die;
            global $currencyList; 
            $neArr = array();
            $customMessages=array();
            foreach($currencyList as $curency) {
                if($curency!='NGN') {
                $neArr[strtolower($curency).'_value'] = 'required|numeric';
                $neArr[strtolower($curency).'_val'] = 'required';
                $customMessages[strtolower($curency).'_value.required']= $curency.' to NGN field can\'t be left blank.';
                $customMessages[strtolower($curency).'_value.numeric']='Invalid '.$curency.' to NGN! User number only.';
                $customMessages[strtolower($curency).'_val.required']='NGN to '.$curency.' field can\'t be left blank.';
                }
            }
            $rules = $neArr;
            $customMessages =$customMessages;
            $validator = Validator::make($input, $rules, $customMessages);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/conversion')->withErrors($validator)->withInput();
            } else {
                $first_array=array();
                $second_array=array();
                foreach($currencyList as $curency) {
                    if($curency!='NGN') {

                      $other_currency_to_ngn=($actual_value[strtolower($curency).'_value']*$input['other_currency_to_ngn'])/100;
                      $after_value_other_currency_to_ngn=$actual_value[strtolower($curency).'_value']+($other_currency_to_ngn);
                      $first_array[strtolower($curency).'_value']=$after_value_other_currency_to_ngn;

                      $ngn_to_other_currency_rate=1/$actual_value[strtolower($curency).'_value'];
                      $ngn_to_other_currency=($ngn_to_other_currency_rate*$input['ngn_to_other_currency'])/100;
                      $after_value_ngn_to_other_currency_rate=$ngn_to_other_currency_rate+($ngn_to_other_currency);
                      $second_array[strtolower($curency).'_value']=number_format($after_value_ngn_to_other_currency_rate, 6, '.', '');
                    }
                }
                $first_array['edited_by'] = Session::get('adminid');
                $first_array['other_currency_to_ngn'] =$input['other_currency_to_ngn'];
                $first_array['ngn_to_other_currency'] =$input['ngn_to_other_currency'];
                $second_array['edited_by'] = Session::get('adminid');
                Ngnexchange::where('id', $exchange->id)->update($first_array);
                Ngnexchange::where('id', $exchange_second->id)->update($second_array);
                Session::flash('success_message', "Conversion details updated successfully.");
                return Redirect::to('admin/users/conversion');
            }
        }
        return view('admin.users.ngnconversion', ['title' => $pageTitle, $activetab => 1, 'exchange' => $exchange,'exchange_second'=>$exchange_second,'actual_value'=>$actual_value]);
    }

    public function dba_setting() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'conversion');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actconversion';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'DBA Setting';
        $activetab = 'actdbasetting';
        
        $user = User::where('id', 1)->first();

        $input = Input::all();
        if (!empty($input)) { 
        User::where('id',1)->update(['dba_aff_per'=>$input['dba_aff_per'],'dba_int_daily'=>$input['dba_int_daily'],'dba_int_60'=>$input['dba_int_60'],'dba_int_90'=>$input['dba_int_90'],'dba_int_180'=>$input['dba_int_180'],'dba_int_365'=>$input['dba_int_365']]);
        Session::flash('success_message', "Admin Setting Saved Successfully");
        return Redirect::to('admin/users/dba_setting');
        }

        return view('admin.users.dba_setting', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $user]);
    }


    public function giftairtime_setting() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'conversion');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actconversion';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Configure Gift Card / Airtime Setting';
        $activetab = 'giftdbasetting';
        
        $user = GiftAirtimeSetting::where('id', 1)->first();

        $input = Input::all();
        if (!empty($input)) { 
            GiftAirtimeSetting::where('id',1)->update(['limits_giftcard'=>$input['limits_giftcard'],'limits_airtime'=>$input['limits_airtime']]);
        Session::flash('success_message', "Gift Card / Airtime  Daily Limit Setting Updated Successfully");
        return Redirect::to('admin/users/giftairtime_setting');
        }

        return view('admin.users.giftairtime_setting', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $user]);
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
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });
                $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestCryptoDeposit', ['requests' => $requests]);
        }

        return view('admin.users.cryptoDepositRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function dbaDepositRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'dba-deposit-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'DBA Deposit Requests';
        $activetab = 'actdbadepositreq';
        $query = new DbaDeposit();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });	
                $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->where('req_type',0)->orderBy('id', 'DESC')->paginate(20);


        if ($request->ajax()) {
            return view('elements.admin.users.requestdbaDeposit', ['requests' => $requests]);
        }

        return view('admin.users.dbaDepositRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function dbaDepositCardRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'dba-deposit-request-card');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'DBA Deposit by Card';
        $activetab = 'actdbadepositreqcard';
        $query = new DbaDeposit();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    //$q = $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%".$keyword."%");
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });
                $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->where('req_type',1)->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestdbaDeposit', ['requests' => $requests]);
        }

        return view('admin.users.dbaDepositRequestCard', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }


    private function usdToUserCurrency($currency, $amount) {
        if($currency == 'NGN'){
            $exchange = Ngnexchange::where('id', 1)->first();
            
            $val = $exchange->usd_value;
            $total = $val * $amount;
            return $total;
        } else{
            $apikey = CURRENCY_CONVERT_API_KEY;
            if ($currency == 'EURO') {
                $query = "USD_EUR";
            } else {
                $query = "USD_" . $currency;
            }
            $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;

            $json = file_get_contents($curr_req);
            $obj = json_decode($json, true);
            $val = floatval($obj[$query]);
            $total = $val * $amount;
            return $total;
        }
    }

    public function updateCryptoDepositReqStatus($id, $status) {
        $req = CryptoDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
         //   return Redirect::to('/admin/users/crypto-deposit-request');
         return Redirect::back();
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
           // return Redirect::to('/admin/users/crypto-deposit-request');
           return Redirect::back();
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            $trans_record=Transaction::where('id', $req->trans_id)->first()->amount;
         //   $amount_cc = $this->usdToUserCurrency($user->currency, $req->amount);
            $deposit_amount =  $trans_record;
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
                $convr_fee_name = 'CONVERSION_FEE';
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
                $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
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
                    $convr_fee_name = 'CONVERSION_FEE';
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
                    $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
                }
            }
            
//            echo $fees_amount;exit;
            //to check that user have Affiliate or not
            $referlCode = 'refid=' . $recordInfo->referral;
            $referrer = Referalcode::where('referal_link', $referlCode)->first();
            $admin_percentage=0;
            $refrlComm=0;
            $conversion_fees=0;
            if($user->currency=="USD")
            {
              $per_currency='na';  
              $total_amt=$fees_amount;  
              if ($recordInfo->referral != 'na') {
                $amountt = ($total_amt * 25) / 100;
                $refrlComm = $amountt;
             }
             $admin_percentage=$total_amt-$refrlComm;
            }
            else
            { 
             $converted_amt= $this->convertCurrency( $user->currency,'USD', $fees_amount);
            // print_r($converted_amt); die;
             $total_amt=explode("##",$converted_amt)[0];
             if($user->currency=='NGN')
             {
             $per_currency='Conversion Rate : 1 USD = '.explode("##",$converted_amt)[1].' '.$user->currency;
             }
             else{
             $per_currency='Conversion Rate : 1 '.$user->currency.' = '.explode("##",$converted_amt)[1].' USD';
             }
             if ($recordInfo->referral != 'na') {
                $referral_User=User::where('id',$referrer->user_id)->first();
                if ($referral_User->user_type == 'Personal') {
                    $convr_fee_name_ref = 'CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Business') {
                    $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name != "") {
                    $convr_fee_name_ref = 'CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name == "") {
                    $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                }
                $amountt = ($total_amt * 25) / 100;
                $fees_convr = Fee::where('fee_name', $convr_fee_name_ref)->first();
                $conversion_feet = $fees_convr->fee_value;
                $conversion_fees=$amountt * $conversion_feet / 100;
                $refrlComm = $amountt - $conversion_fees;
             }
             $admin_percentage=$total_amt-$refrlComm;
            }

            $admin_dollar_record= $this->convertCurrency( $user->currency,'USD', $deposit_amount);
            $deposit_amount_doller=explode("##",$admin_dollar_record)[0];
            $admin_wallet_balance=$deposit_amount_doller-$admin_percentage;

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_wallet_balance);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            $deposit_amount_total = $deposit_amount- $fees_amount;
            $amount_cc1 = $deposit_amount_total;
            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 
            $refrence_id = time() . rand() . $req->user_id;
            $real_value=$deposit_amount-$fees_amount;
            Transaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD','receiver_currency'=>$user->currency,"user_close_bal" => $user_wallet,
            "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>$per_currency]);

            //Mail Start
            if (!empty($referrer)) {
                $refComm = new ReferralCommission([ 
                    'user_id' => $req->user_id,
                    'referrer_id' => $referrer->user_id,
                    'amount' => $refrlComm,
                    'trans_id' => $req->trans_id,
                    'action' => 'CRYPTO DEPOSIT',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $refComm->save();
            }

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $amount_cc1 . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
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
            Mail::send('emails.cryptoDepositRequestCanclled', $emailData, function ($message)use ($emailData, $emailId) {
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
            Transaction::where('id', $req->trans_id)->update(['status' => $status,"user_close_bal" => $user->wallet_amount, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        CryptoDeposit::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
       // return Redirect::to('/admin/users/crypto-deposit-request');
        return Redirect::back();
    }


    public function updateDbaDepositReqStatus($id, $status) {
        $req = DbaDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
           // return Redirect::to('/admin/users/dba-deposit-request');
           return Redirect::back();
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            //return Redirect::to('/admin/users/dba-deposit-request');
            return Redirect::back();
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            $trans_record=DbaTransaction::where('id', $req->trans_id)->first()->amount;
            $deposit_amount =  $req->dba_amount;
            $main_amount =  $req->amount;
            if ($recordInfo->user_type == 'Personal') {
                if ($recordInfo->account_category == "Silver") {
                    $fee_name = 'DBA_DEPOSIT_SILVER';
                } else if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'DBA_DEPOSIT_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'DBA_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Private Wealth") {
                    $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                } else {
                    $fee_name = 'DBA_DEPOSIT_SILVER';
                }
                //$fee_name = 'CRYPTO_DEPOSITE';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Enterprises") {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                } else {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                }
                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                   
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

            $admin_wallet_balance=$deposit_amount-$fees_amount;

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_wallet_balance);
            User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);

            $deposit_amount_total = $deposit_amount- $fees_amount;
            $amount_cc1 = $deposit_amount_total;
            $user_wallet = $user->dba_wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 

            $refrence_id = time() . rand() . $req->user_id;
            $real_value=$deposit_amount-$fees_amount;
            DbaTransaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD','receiver_currency'=>$user->dba_currency,"user_close_bal" => $user_wallet,
            "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>'IP : ' . $this->get_client_ip()]);

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;
            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->dba_currency . ' ' . $amount_cc1 . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | DBA Deposit Request has been Completed';
            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['userName'] = strtoupper($user_name);
            $emailData['TransId'] = $TransId;
            $emailData['amount_cc'] = $user->dba_currency . ' ' . $amount_cc1;
            $emailData['currency'] = $user->dba_currency;
            $emailData['loginLnk'] = $loginLnk;

            Mail::send('emails.updateDbaDepositReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
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
            $emailSubject = 'DafriBank Digital | DBA Deposit Request Cancelled';

            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->dba_currency;
            Mail::send('emails.dbaDepositRequestCanclled', $emailData, function ($message)use ($emailData, $emailId) {
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
            DbaTransaction::where('id', $req->trans_id)->update(['status' => $status,"user_close_bal" => $user->dba_wallet_amount, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        DbaDeposit::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
      //  return Redirect::to('/admin/users/dba-deposit-request');
       return Redirect::back();
    }

    public function updateDbaDepositCardReqStatus($id, $status) {
        $req = DbaDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/dba-deposit-by-card');
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/dba-deposit-by-card');
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            $trans_record=DbaTransaction::where('id', $req->trans_id)->first()->amount;
            $deposit_amount =  $req->dba_amount;
            $main_amount =  $req->amount;
            if ($recordInfo->user_type == 'Personal') {
                if ($recordInfo->account_category == "Silver") {
                    $fee_name = 'DBA_DEPOSIT_SILVER';
                } else if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'DBA_DEPOSIT_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'DBA_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Private Wealth") {
                    $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                } else {
                    $fee_name = 'DBA_DEPOSIT_SILVER';
                }
                //$fee_name = 'CRYPTO_DEPOSITE';

                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else if ($recordInfo->user_type == 'Business') {
                if ($recordInfo->account_category == "Gold") {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                } else if ($recordInfo->account_category == "Platinum") {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                } else if ($recordInfo->account_category == "Enterprises") {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                } else {
                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                }
                $fees = Fee::where('fee_name', $fee_name)->first();
                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
            } else {
                if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                   
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                }
            }

            $admin_wallet_balance=$deposit_amount-$fees_amount;

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_wallet_balance);
            User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);

            $deposit_amount_total = $deposit_amount- $fees_amount;
            $amount_cc1 = $deposit_amount_total;
            $user_wallet = $user->dba_wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 

            $refrence_id = time() . rand() . $req->user_id;
            $real_value=$deposit_amount-$fees_amount;
            DbaTransaction::where('id', $req->trans_id)->update(['amount' => $main_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD','receiver_currency'=>$user->dba_currency,"user_close_bal" => $user_wallet,
            "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>'IP : ' . $this->get_client_ip()]);

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;
            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->dba_currency . ' ' . $amount_cc1 . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | DBA Deposit Request has been Completed';
            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

            $emailData['subject'] = $emailSubject;
            $emailData['userName'] = strtoupper($user_name);
            $emailData['TransId'] = $TransId;
            $emailData['amount_cc'] = $user->dba_currency . ' ' . $amount_cc1;
            $emailData['currency'] = $user->dba_currency;
            $emailData['loginLnk'] = $loginLnk;

            Mail::send('emails.updateDbaDepositReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
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
            $emailSubject = 'DafriBank Digital | DBA Deposit Request Cancelled';

            //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->dba_currency;
            Mail::send('emails.dbaDepositRequestCanclled', $emailData, function ($message)use ($emailData, $emailId) {
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
            DbaTransaction::where('id', $req->trans_id)->update(['status' => $status,"user_close_bal" => $user->dba_wallet_amount, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        DbaDeposit::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
        return Redirect::to('/admin/users/dba-deposit-by-card');
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
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
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
            $amount_cc1 = $deposit_amount_total;
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
         //  echo "hello"; die;
            $rules = array(
                'amount' => 'required|numeric|gt:0',
                'crypto_currency' => 'required',
                'blockchain_url' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-crypto-request/' . $id)->withErrors($validator)->withInput();
            } else { 

            if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
            }
        
            if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/crypto-deposit-request');
            }
            if($input['amount']==$req->amount)
            {
            $amount_cc=Transaction::where('id', $req->trans_id)->first()->amount;
            }
            else{
            $amount_cc = $this->usdToUserCurrency($user->currency, $input['amount']);
            }

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
                $convr_fee_name = 'CONVERSION_FEE';
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
                $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
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
                    $convr_fee_name = 'CONVERSION_FEE';
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
                    $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
                }
            }

            //to check that user have Affiliate or not
            $referlCode = 'refid=' . $recordInfo->referral;
            $referrer = Referalcode::where('referal_link', $referlCode)->first();
            $admin_percentage=0;
            $refrlComm=0;
            $conversion_fees=0;
            if($user->currency=="USD")
            {
              $per_currency='na';  
              $total_amt=$fees_amount;  
              if ($recordInfo->referral != 'na') {
                $amountt = ($total_amt * 25) / 100;
                $refrlComm = $amountt;
             }
             $admin_percentage=$total_amt-$refrlComm;
            }
            else
            { 
             $converted_amt= $this->convertCurrency( $user->currency,'USD', $fees_amount);
             //print_r($converted_amt); die;
             $total_amt=explode("##",$converted_amt)[0];

             if($user->currency=='NGN')
             {
             $per_currency='Conversion Rate : 1 USD = '.explode("##",$converted_amt)[1].' '.$user->currency;
             }
             else{
             $per_currency='Conversion Rate : 1 '.$user->currency.' = '.explode("##",$converted_amt)[1].' USD';
             }

             if ($recordInfo->referral != 'na') {
                $referral_User=User::where('id',$referrer->user_id)->first();
                if ($referral_User->user_type == 'Personal') {
                    $convr_fee_name_ref = 'CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Business') {
                    $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name != "") {
                    $convr_fee_name_ref = 'CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name == "") {
                    $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                }
                $amountt = ($total_amt * 25) / 100;
                $fees_convr = Fee::where('fee_name', $convr_fee_name_ref)->first();
                $conversion_feet = $fees_convr->fee_value;
                $conversion_fees=$amountt * $conversion_feet / 100;
                $refrlComm = $amountt - $conversion_fees;
             }
             $admin_percentage=$total_amt-$refrlComm;
            }
             
            $admin_dollar_record= $this->convertCurrency( $user->currency,'USD', $deposit_amount);
            $deposit_amount_doller=explode("##",$admin_dollar_record)[0];
            $admin_wallet_balance=$deposit_amount_doller-$admin_percentage;

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_wallet_balance);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            $deposit_amount_total = $deposit_amount- $fees_amount;
            $amount_cc1 = $deposit_amount_total;
            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 
            $refrence_id = time() . rand() . $req->user_id;
            $real_value=$deposit_amount-$fees_amount;
            Transaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD','receiver_currency'=>$user->currency,"user_close_bal" => $user_wallet,
            "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>$per_currency,'real_value'=>$real_value]);
            //Mail Start
            
            if (!empty($referrer)) {
                $refComm = new ReferralCommission([ 
                    'user_id' => $req->user_id,
                    'referrer_id' => $referrer->user_id,
                    'amount' => $refrlComm,
                    'trans_id' => $req->trans_id,
                    'action' => 'CRYPTO DEPOSIT',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $refComm->save();
            }

            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            }

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;
            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $deposit_amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
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

            CryptoDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'amount' => $input['amount'], 'crypto_currency' => $input['crypto_currency'], 'blockchain_url' => $input['blockchain_url'], 'updated_at' => date('Y-m-d H:i:s'),'status'=>1]);

            Session::flash('success_message', "Request details updated successfully.");
            return Redirect::to('admin/users/crypto-deposit-request');
            }
        }
        return view('admin.users.editCryptoReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
    }

   
    public function dba_currency_exchange($currency,$amount)
    {
        $apikey = 'b8bfec8c50ada6c66af06245ada3f286f121136e11f1bed4591d61439968b68b';
        $curr_req = "https://min-api.cryptocompare.com/data/price?fsym=".$currency."&tsyms=DBA&apiKey=" . $apikey;
        $json = file_get_contents($curr_req);
        $obj = json_decode($json, true);
        $val=$obj['DBA'];
        $total=$amount*$val;
        return $total . "##" . $val;

    }



    public function editDbaDepositReq($id) {
        $pageTitle = 'Edit Dba Deposit Request';
        $activetab = 'actdbadepositreq';

        $req = DbaDeposit::where('id', $id)->first();
        
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        $input = Input::all();
        if (!empty($input)) {
         //  echo "hello"; die;
            $rules = array(
                'amount' => 'required|numeric|gt:0',
                'crypto_currency' => 'required',
               // 'blockchain_url' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-dba-deposit-request/' . $id)->withErrors($validator)->withInput();
            } else { 

            if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/dba-deposit-request');
            }
        
            if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/dba-deposit-request');
            }
          
            if($req->req_type_currency=='USD')
            {

            if($input['amount']==$req->amount)
            {
            $amount_cc=DbaTransaction::where('id', $req->trans_id)->first()->amount;
            }
            else{ 
            $amt=$this->dba_currency_exchange('USD',$input['amount']);
            $amount_cc = explode("##",$amt)[0];
            }

            $deposit_amount = $amount_cc;
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            }

                if ($recordInfo->user_type == 'Personal') {
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    }
                    //$fee_name = 'CRYPTO_DEPOSITE';
    
                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Business') {
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    }
                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else {
                    if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                        if ($recordInfo->account_category == "Silver") {
                            $fee_name = 'DBA_DEPOSIT_SILVER';
                        } else if ($recordInfo->account_category == "Gold") {
                            $fee_name = 'DBA_DEPOSIT_GOLD';
                        } else if ($recordInfo->account_category == "Platinum") {
                            $fee_name = 'DBA_DEPOSIT_PLATINUM';
                        } else if ($recordInfo->account_category == "Private Wealth") {
                            $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                        } else {
                            $fee_name = 'DBA_DEPOSIT_SILVER';
                        }
    
                        $fees = Fee::where('fee_name', $fee_name)->first();
                        $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                    } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                       
                        if ($recordInfo->account_category == "Gold") {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                        } else if ($recordInfo->account_category == "Platinum") {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                        } else if ($recordInfo->account_category == "Enterprises") {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                        } else {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                        }
    
                        $fees = Fee::where('fee_name', $fee_name)->first();
                        $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                    }
                }
    
                $admin_wallet_balance=$deposit_amount-$fees_amount;
    
                $adminInfo = User::where('id', 1)->first();
                $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_wallet_balance);
               // User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);
    
                $deposit_amount_total = $deposit_amount- $fees_amount;
                $amount_cc1 = $deposit_amount_total;
                $user_wallet = $user->dba_wallet_amount + $deposit_amount_total;
                User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 
    
                $refrence_id = time() . rand() . $req->user_id;
                $real_value=$deposit_amount-$fees_amount;
                DbaTransaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD','receiver_currency'=>$user->dba_currency,"user_close_bal" => $user_wallet,
                "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>'IP : ' . $this->get_client_ip()]);

                 }
                 else{
                   
                    if($input['amount']==$req->amount)
                    {
                    $amount_cc=$req->amount;
                    $amount_dba=$req->dba_amount;
                    }
                    else{ 
                    $amt=$this->dba_currency_exchange($req->req_type_currency,$input['amount']);
                    $amount_cc=$input['amount'];
                    $amount_dba = explode("##",$amt)[0];
                    }

                    $deposit_amount = $amount_dba;
                    if ($user->user_type == 'Personal') {
                        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($user->user_type == 'Business') {
                        $user_name = $this->business_short_name($user);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                        $user_name = $this->business_short_name($user);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
        
                        if ($recordInfo->user_type == 'Personal') {
                            if ($recordInfo->account_category == "Silver") {
                                $fee_name = 'DBA_DEPOSIT_SILVER';
                            } else if ($recordInfo->account_category == "Gold") {
                                $fee_name = 'DBA_DEPOSIT_GOLD';
                            } else if ($recordInfo->account_category == "Platinum") {
                                $fee_name = 'DBA_DEPOSIT_PLATINUM';
                            } else if ($recordInfo->account_category == "Private Wealth") {
                                $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'DBA_DEPOSIT_SILVER';
                            }
                            //$fee_name = 'CRYPTO_DEPOSITE';
            
                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                        } else if ($recordInfo->user_type == 'Business') {
                            if ($recordInfo->account_category == "Gold") {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                            } else if ($recordInfo->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                            } else if ($recordInfo->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                            } else {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                            }
                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                        } else {
                            if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                                if ($recordInfo->account_category == "Silver") {
                                    $fee_name = 'DBA_DEPOSIT_SILVER';
                                } else if ($recordInfo->account_category == "Gold") {
                                    $fee_name = 'DBA_DEPOSIT_GOLD';
                                } else if ($recordInfo->account_category == "Platinum") {
                                    $fee_name = 'DBA_DEPOSIT_PLATINUM';
                                } else if ($recordInfo->account_category == "Private Wealth") {
                                    $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                                } else {
                                    $fee_name = 'DBA_DEPOSIT_SILVER';
                                }
            
                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                            } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                               
                                if ($recordInfo->account_category == "Gold") {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                                } else if ($recordInfo->account_category == "Platinum") {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                                } else if ($recordInfo->account_category == "Enterprises") {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                                } else {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                                }
            
                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                            }
                        }

                        $admin_wallet_balance=$deposit_amount-$fees_amount;
    
                        $adminInfo = User::where('id', 1)->first();
                        $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_wallet_balance);
                       // User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);
            
                        $deposit_amount_total = $deposit_amount- $fees_amount;
                        $amount_cc1 = $deposit_amount_total;
                        $user_wallet = $user->dba_wallet_amount + $deposit_amount_total;
                        User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 
            
                        $refrence_id = time() . rand() . $req->user_id;
                        $real_value=$deposit_amount-$fees_amount;
                        DbaTransaction::where('id', $req->trans_id)->update(['amount' => $amount_cc,'receiver_fees'=>$fees_amount,'sender_currency'=>'DBA','receiver_currency'=>$user->dba_currency,"user_close_bal" => $user_wallet,
                        "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>'IP : ' . $this->get_client_ip()]);
                 }
    
                $TransId = $req->trans_id;
                $emailId = $user->email;
                $userName = $user_name;
                $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->dba_currency . ' ' . $amount_cc1 . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                $emailSubject = 'DafriBank Digital | DBA Deposit Request has been Completed';
                //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
    
                $emailData['subject'] = $emailSubject;
                $emailData['userName'] = strtoupper($user_name);
                $emailData['TransId'] = $TransId;
                $emailData['amount_cc'] = $user->dba_currency . ' ' . $amount_cc1;
                $emailData['currency'] = $user->dba_currency;
                $emailData['loginLnk'] = $loginLnk;
    
                Mail::send('emails.updateDbaDepositReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
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

           
             DbaDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'amount' => $input['amount'], 'dba_amount' => $deposit_amount,'crypto_currency' => $input['crypto_currency'], 'blockchain_url' => $input['blockchain_url'], 'updated_at' => date('Y-m-d H:i:s'),'status'=>1]);

            Session::flash('success_message', "Request details updated successfully.");
            return Redirect::to('admin/users/dba-deposit-request');
            }
        }
        return view('admin.users.editDbaDepositReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
    }

    public function editDbaDepositCardReq($id) {
        $pageTitle = 'Edit DBA Deposit by Card';
        $activetab = 'actdbadepositreqcard';

        $req = DbaDeposit::where('id', $id)->first();
        
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        $input = Input::all();
        if (!empty($input)) {
         //  echo "hello"; die;
            $rules = array(
                'amount' => 'required|numeric|gt:0',
                'crypto_currency' => 'required',
               // 'blockchain_url' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-dba-deposit-card-request/' . $id)->withErrors($validator)->withInput();
            } else { 

            if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
            return Redirect::to('/admin/users/dba-deposit-by-card');
            }
        
            if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
            return Redirect::to('/admin/users/dba-deposit-by-card');
            }
          
            if($req->req_type_currency=='USD')
            {

            if($input['amount']==$req->amount)
            {
            $amount_cc=DbaTransaction::where('id', $req->trans_id)->first()->amount;
            }
            else{ 
            $amt=$this->dba_currency_exchange('USD',$input['amount']);
            $amount_cc = explode("##",$amt)[0];
            }

            $deposit_amount = $amount_cc;
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                $user_name = $this->business_short_name($user);
                $loginLnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                $loginLnk = HTTP_PATH . '/personal-login';
            }

                if ($recordInfo->user_type == 'Personal') {
                    if ($recordInfo->account_category == "Silver") {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    } else if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Private Wealth") {
                        $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                    } else {
                        $fee_name = 'DBA_DEPOSIT_SILVER';
                    }
                    //$fee_name = 'CRYPTO_DEPOSITE';
    
                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else if ($recordInfo->user_type == 'Business') {
                    if ($recordInfo->account_category == "Gold") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    } else if ($recordInfo->account_category == "Platinum") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                    } else if ($recordInfo->account_category == "Enterprises") {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                    } else {
                        $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                    }
                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                } else {
                    if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                        if ($recordInfo->account_category == "Silver") {
                            $fee_name = 'DBA_DEPOSIT_SILVER';
                        } else if ($recordInfo->account_category == "Gold") {
                            $fee_name = 'DBA_DEPOSIT_GOLD';
                        } else if ($recordInfo->account_category == "Platinum") {
                            $fee_name = 'DBA_DEPOSIT_PLATINUM';
                        } else if ($recordInfo->account_category == "Private Wealth") {
                            $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                        } else {
                            $fee_name = 'DBA_DEPOSIT_SILVER';
                        }
    
                        $fees = Fee::where('fee_name', $fee_name)->first();
                        $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                    } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                       
                        if ($recordInfo->account_category == "Gold") {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                        } else if ($recordInfo->account_category == "Platinum") {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                        } else if ($recordInfo->account_category == "Enterprises") {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                        } else {
                            $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                        }
    
                        $fees = Fee::where('fee_name', $fee_name)->first();
                        $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                    }
                }
    
                $admin_wallet_balance=$deposit_amount-$fees_amount;
    
                $adminInfo = User::where('id', 1)->first();
                $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_wallet_balance);
               // User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);
    
                $deposit_amount_total = $deposit_amount- $fees_amount;
                $amount_cc1 = $deposit_amount_total;
                $user_wallet = $user->dba_wallet_amount + $deposit_amount_total;
                User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 
    
                $refrence_id = time() . rand() . $req->user_id;
                $real_value=$deposit_amount-$fees_amount;
                DbaTransaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD','receiver_currency'=>$user->dba_currency,"user_close_bal" => $user_wallet,
                "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>'IP : ' . $this->get_client_ip()]);

                 }
                 else{
                   
                    if($input['amount']==$req->amount)
                    {
                    $amount_cc=$req->amount;
                    $amount_dba=$req->dba_amount;
                    }
                    else{ 
                    $amt=$this->dba_currency_exchange($req->req_type_currency,$input['amount']);
                    $amount_cc=$input['amount'];
                    $amount_dba = explode("##",$amt)[0];
                    }

                    $deposit_amount = $amount_dba;
                    if ($user->user_type == 'Personal') {
                        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($user->user_type == 'Business') {
                        $user_name = $this->business_short_name($user);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                        $user_name = $this->business_short_name($user);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
        
                        if ($recordInfo->user_type == 'Personal') {
                            if ($recordInfo->account_category == "Silver") {
                                $fee_name = 'DBA_DEPOSIT_SILVER';
                            } else if ($recordInfo->account_category == "Gold") {
                                $fee_name = 'DBA_DEPOSIT_GOLD';
                            } else if ($recordInfo->account_category == "Platinum") {
                                $fee_name = 'DBA_DEPOSIT_PLATINUM';
                            } else if ($recordInfo->account_category == "Private Wealth") {
                                $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'DBA_DEPOSIT_SILVER';
                            }
                            //$fee_name = 'CRYPTO_DEPOSITE';
            
                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                        } else if ($recordInfo->user_type == 'Business') {
                            if ($recordInfo->account_category == "Gold") {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                            } else if ($recordInfo->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                            } else if ($recordInfo->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                            } else {
                                $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                            }
                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                        } else {
                            if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
                                if ($recordInfo->account_category == "Silver") {
                                    $fee_name = 'DBA_DEPOSIT_SILVER';
                                } else if ($recordInfo->account_category == "Gold") {
                                    $fee_name = 'DBA_DEPOSIT_GOLD';
                                } else if ($recordInfo->account_category == "Platinum") {
                                    $fee_name = 'DBA_DEPOSIT_PLATINUM';
                                } else if ($recordInfo->account_category == "Private Wealth") {
                                    $fee_name = 'DBA_DEPOSIT_PRIVATE_WEALTH';
                                } else {
                                    $fee_name = 'DBA_DEPOSIT_SILVER';
                                }
            
                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                            } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
                               
                                if ($recordInfo->account_category == "Gold") {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                                } else if ($recordInfo->account_category == "Platinum") {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_PLATINUM';
                                } else if ($recordInfo->account_category == "Enterprises") {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_ENTERPRIS';
                                } else {
                                    $fee_name = 'MERCHANT_DBA_DEPOSIT_GOLD';
                                }
            
                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = ($deposit_amount * $fees->fee_value) / 100;
                            }
                        }

                        $admin_wallet_balance=$deposit_amount-$fees_amount;
    
                        $adminInfo = User::where('id', 1)->first();
                        $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_wallet_balance);
                       // User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);
            
                        $deposit_amount_total = $deposit_amount- $fees_amount;
                        $amount_cc1 = $deposit_amount_total;
                        $user_wallet = $user->dba_wallet_amount + $deposit_amount_total;
                        User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]); 
            
                        $refrence_id = time() . rand() . $req->user_id;
                        $real_value=$deposit_amount-$fees_amount;
                        DbaTransaction::where('id', $req->trans_id)->update(['amount' => $amount_cc,'receiver_fees'=>$fees_amount,'sender_currency'=>'DBA','receiver_currency'=>$user->dba_currency,"user_close_bal" => $user_wallet,
                        "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'real_value'=>$real_value,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>'IP : ' . $this->get_client_ip()]);
                 }
    
                $TransId = $req->trans_id;
                $emailId = $user->email;
                $userName = $user_name;
                $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your Crypto Currency deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->dba_currency . ' ' . $amount_cc1 . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                $emailSubject = 'DafriBank Digital | DBA Deposit Request has been Completed';
                //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
    
                $emailData['subject'] = $emailSubject;
                $emailData['userName'] = strtoupper($user_name);
                $emailData['TransId'] = $TransId;
                $emailData['amount_cc'] = $user->dba_currency . ' ' . $amount_cc1;
                $emailData['currency'] = $user->dba_currency;
                $emailData['loginLnk'] = $loginLnk;
    
                Mail::send('emails.updateDbaDepositReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
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

           
             DbaDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'amount' => $input['amount'], 'dba_amount' => $deposit_amount,'crypto_currency' => $input['crypto_currency'], 'blockchain_url' => $input['blockchain_url'], 'updated_at' => date('Y-m-d H:i:s'),'status'=>1]);

            Session::flash('success_message', "Request details updated successfully.");
            return Redirect::to('admin/users/dba-deposit-by-card');
            }
        }
        return view('admin.users.editDbaDepositCardReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
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
                $q->orWhereHas('User', function ($q) use ($keyword) {
                $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });
                $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('ref_number', 'like', '%' . $keyword . '%');
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
                'amount' => 'required|numeric|gt:0',
                'ref_number' => 'required',
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/users/edit-manual-request/' . $id)->withErrors($validator)->withInput();
            } else {  
                $req = ManualDeposit::where('id', $id)->first();

                if($req->status==1 || $req->status==3)
                {
                Session::flash('error_message', "Request has been already updated");
                return Redirect::to('/admin/users/manual-deposit-request');
                }
                else{
                
                ManualDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'amount' => $input['amount'], 'ref_number' => $input['ref_number'], 'updated_at' => date('Y-m-d H:i:s'),'status'=>1]); 

                $user = $recordInfo = User::where('id', $req->user_id)->first();
                $deposit_amount = $input['amount'];
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
                            $convr_fee_name = 'CONVERSION_FEE';
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
                            $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
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
                                $convr_fee_name = 'CONVERSION_FEE';
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
                                $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
                            }
                        }


                        $referlCode = 'refid=' . $recordInfo->referral;
                        $referrer = Referalcode::where('referal_link', $referlCode)->first();
                        $admin_percentage=0;
                        $refrlComm=0;
                        $conversion_fees=0;
                        if($user->currency=="USD")
                        {
                          $per_currency='na';
                          $total_amt=$fees_amount;  
                          if ($recordInfo->referral != 'na') {
                            $amountt = ($total_amt * 25) / 100;
                            $refrlComm = $amountt;
                         }
                         $admin_percentage=$total_amt-$refrlComm;
                        }
                        else
                        { 
                         $converted_amt= $this->convertCurrency( $user->currency,'USD', $fees_amount);
                         $total_amt=explode("##",$converted_amt)[0];
                         if($user->currency=='NGN')
                         {
                         $per_currency='Conversion Rate : 1 USD = '.explode("##",$converted_amt)[1].' '.$user->currency;
                         }
                         else{
                         $per_currency='Conversion Rate : 1 '.$user->currency.' = '.explode("##",$converted_amt)[1].' USD';
                         }
                         //to check that user have Affiliate or not
                         if ($recordInfo->referral != 'na') {
                            $referral_User=User::where('id',$referrer->user_id)->first();
                            if ($referral_User->user_type == 'Personal') {
                                $convr_fee_name_ref = 'CONVERSION_FEE';
                            } elseif ($referral_User->user_type == 'Business') {
                                $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                            } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name != "") {
                                $convr_fee_name_ref = 'CONVERSION_FEE';
                            } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name == "") {
                                $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                            }
                            $amountt = ($total_amt * 25) / 100;
                            $fees_convr = Fee::where('fee_name', $convr_fee_name_ref)->first();
                            $conversion_feet = $fees_convr->fee_value;
                            $conversion_fees=$amountt * $conversion_feet / 100;
                            $refrlComm = $amountt - $conversion_fees;
                         }
                         $admin_percentage=$total_amt-$refrlComm;
                        }
                        $admin_dollar_record= $this->convertCurrency( $user->currency,'USD', $deposit_amount);
                        $deposit_amount_doller=explode("##",$admin_dollar_record)[0];
                        $admin_wallet_balance=$deposit_amount_doller-$admin_percentage;
                        $adminInfo = User::where('id', 1)->first();
                        $admin_wallet = ($adminInfo->wallet_amount - $admin_wallet_balance);
                        User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

                        $deposit_amount_total = $deposit_amount- $fees_amount;
                        $user_wallet = $user->wallet_amount + $deposit_amount_total;
                        User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);


                     //to save the data into transaction table
                     $refrence_id = time() . rand() . $req->user_id;
                     $real_value=$deposit_amount-$fees_amount;
                     Transaction::where('id', $req->trans_id)->update(['amount' => $deposit_amount,'receiver_fees'=>$fees_amount,'sender_currency'=>'USD',"user_close_bal" => $user_wallet,
                     "receiver_close_bal" => $admin_wallet,"status" =>1,"refrence_id" => $refrence_id,'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>$per_currency,'receiver_currency'=>$user->currency,'real_value'=>$real_value]);

                     if (!empty($referrer)) {  
                         $refComm = new ReferralCommission([ 
                             'user_id' => $req->user_id,
                             'referrer_id' => $referrer->user_id,
                             'amount' => $refrlComm,
                             'trans_id' => $req->trans_id,
                             'action' => 'MANUAL_DEPOSIT',
                             'created_at' => date('Y-m-d H:i:s'),
                             'updated_at' => date('Y-m-d H:i:s')
                         ]);
                         $refComm->save();
                     }

                     if ($user->user_type == 'Personal') {
                        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($user->user_type == 'Business') {
                        $user_name = $this->business_short_name($user);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                        $user_name = $this->business_short_name($user);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }

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
                     
//                Session::flash('success_message', "Request details updated successfully.");
                return Redirect::to('admin/users/manual-deposit-request');
                // return Redirect::to('admin/users/change-manual-deposit-req-status/' . $id . '/1');
              }
            }
        }
        return view('admin.users.editManualReq', ['title' => $pageTitle, $activetab => 1, 'req' => $req]);
    }

    public function repeatManualReq($id) {
        $req = ManualDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();
        $input=input::all();
        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

//        $user_wallet = $user->wallet_amount + $req->amount;
//        User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
        
        
            $deposit_amount = $input['edit_amount'];
            if($deposit_amount==0)
            {
            Session::flash('error_message', "Amount should be grater then 0");
            if($input['page']=="deposit")
            {
            return Redirect::to('/admin/users/manual-deposit-request');
            }
            elseif($input['page']=="user")
            {
            return Redirect::to('/admin/users/transaction-list/'.$user->slug);
            }
            else{
            return Redirect::to('admin/reports/transaction-report');   
            }
            }
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

            $deposit_amount_total = $deposit_amount - $fees_amount;
            $user_wallet = $user->wallet_amount + $deposit_amount_total;
          //  echo $user_wallet;
         
            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount + $fees_amount);
            //echo "<br>Admin wallet".$admin_wallet; die;
          
        $trans = Transaction::where('id', $req->trans_id)->first();

        $refrence_id = time() . rand() . Session::get('user_id');
        $tran = new Transaction([
            "user_id" => $trans->user_id,
            "receiver_id" => 0,
            "amount" => $deposit_amount,
            "fees" => $fees_amount,
            "receiver_fees" => $fees_amount,
            "receiver_currency" => $trans->currency,
            "currency" => $trans->currency,
            "sender_currency" =>'USD',
            "trans_type" => 1, //Debit-Withdraw
            "trans_to" => 'Dafri_Wallet',
            "trans_for" => 'ManualDeposit',
            "user_close_bal" => $user_wallet,
            "receiver_close_bal" => $admin_wallet,
            "refrence_id" => $refrence_id,
            "real_value" => $deposit_amount_total,
            "billing_description" => 'Payment repeated by admin for transaction ID: ' . $req->trans_id,
            "status" => 1,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        ]);
        $tran->save();
        $TransId = $tran->id;

        $md = new ManualDeposit([
            'user_id' => $trans->user_id,
            'user_name' => $user_name,
            'user_email' => $recordInfo->email,
            'trans_id' => $TransId,
            'bank_name' => $req->bank_name,
            'currency' => $trans->currency,
            'amount' => $deposit_amount,
            'ref_number' => $user->account_number,
            'edited_by' => Session::get('adminid'), 
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $md->save();

        User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

        User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

        $emailId = $user->email;
        $userName = strtoupper($user_name);
        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual deposit request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $req->currency . ' ' . $req->amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
        $emailSubject = 'DafriBank Digital | Manual Deposit Request has been Completed';
        $emailData['subjects'] = $emailSubject;
        $emailData['userName'] = $userName;
        $emailData['emailId'] = $emailId;
        $emailData['TransId'] = $TransId;
        $emailData['loginLnk'] = $loginLnk;
        $emailData['amount'] = $deposit_amount;
        $emailData['currency'] = $user->currency;
        Mail::send('emails.repeatManualReq', $emailData, function ($message)use ($emailData, $emailId) {
            $message->to($emailId, $emailId)
                    ->subject($emailData['subjects']);
        });

        Session::flash('success_message', "Payment Repeated successfully.");
        if($input['page']=="deposit")
        {
        return Redirect::to('/admin/users/manual-deposit-request');
        }
        elseif($input['page']=="user")
        {
        return Redirect::to('/admin/users/transaction-list/'.$user->slug);
        }
        else{
        return Redirect::to('admin/reports/transaction-report');   
        }

        //Mail End
    }


    public function getToken() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://auth.reloadly.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                        "client_id":"' . AIRTIME_CLIENT_ID . '",
                        "client_secret":"' . AIRTIME_SECRET_KEY . '",
                        "grant_type":"client_credentials",
                        "audience":"' . AIRTIME_URL . '"
                    }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $jsonArr = json_decode($response);
        return $jsonArr->access_token;
    }

    public function getTokenGiftCard() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://auth.reloadly.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                        "client_id":"' . AIRTIME_CLIENT_ID . '",
                        "client_secret":"' . AIRTIME_SECRET_KEY . '",
                        "grant_type":"client_credentials",
                        "audience":"' . AIRTIME_GIFTCARD_URL . '"
                    }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $jsonArr = json_decode($response);
        return $jsonArr->access_token;
    }
    function generateRandomString1($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function airtimeReqUpdate($id, $status) {
    $req = Transaction::where('id', $id)->first();
    $user = $recordInfo = User::where('id', $req->user_id)->first();
      //to check that request has been updated or not
      if($req->status==1 || $req->status==3)
      {
      Session::flash('error_message', "Request already updated");
      return Redirect::back();
      }

      if($status==1)
      {
      $access_token=$this->getToken();
      $post_fields=$req->airtime_data; 
      $curl = curl_init();
      curl_setopt_array($curl, array(
          CURLOPT_URL => AIRTIME_URL . '/topups',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $post_fields,
          CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer ' . $access_token,
              'Accept: application/com.reloadly.topups-v1+json',
              'Content-Type: application/json'
          ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      $jsonArr = json_decode($response);
      if (isset($jsonArr->message) && !empty($jsonArr->message)) {
      $error = $jsonArr->message;    
      Session::flash('error_message',$error);
      return Redirect::back();
      }
      if($jsonArr->status=='SUCCESSFUL')
      {
      $airtime_data=json_decode($req->airtime_data);
      $recharged_number=$airtime_data->recipientPhone->number;
      $country_sort_name=$airtime_data->recipientPhone->countryCode;
      $countryData = Country::where('sortname', $country_sort_name)->first()->country_code;
      $recharged_number_with_code='+'.$countryData.$recharged_number;
      Transaction::where('id', $req->id)->update(["refrence_id" => $jsonArr->transactionId,'status' =>1, 'updated_at' => DB::raw('updated_at')]);
      
      $emailId = $recordInfo->email;
      if ($recordInfo->user_type == 'Personal') {
        $userName = strtoupper($recordInfo->first_name);
        $userFullName = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
        $receiverName = strtoupper($recordInfo->first_name);
        $loginLnk = HTTP_PATH . '/personal-login';
    } else if ($recordInfo->user_type == 'Business') {
        $userName = strtoupper($recordInfo->business_name);
        $userFullName = strtoupper($recordInfo->business_name);
        $receiverName = strtoupper($recordInfo->business_name);
        $loginLnk = HTTP_PATH . '/business-login';
    } else if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
        $userName = strtoupper($recordInfo->first_name);
        $userFullName = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
        $receiverName = strtoupper($recordInfo->first_name);
        $loginLnk = HTTP_PATH . '/personal-login';
    } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
        $userName = strtoupper($recordInfo->business_name);
        $userFullName = strtoupper($recordInfo->business_name);
        $receiverName = strtoupper($recordInfo->business_name);
        $loginLnk = HTTP_PATH . '/business-login';
    }
      $emailSubject = "DafriBank Digital | Mobile-Top Success";
      $emailData['subject'] = $emailSubject;
      $emailData['emailId'] = $emailId;
      $emailData['user_name'] = $userFullName;
      $emailData['number'] = $recharged_number_with_code;
      $emailData['ip'] = $this->get_client_ip();
      $emailData['loginLnk'] = $loginLnk;
      Mail::send('emails.mobile_topup_approve', $emailData, function ($message)use ($emailData, $emailId) {
          $message->to($emailId, $emailId)
                  ->subject($emailData['subject']);
      });
      $notif = new Notification([
          'user_id' => $recordInfo->id,
          'notif_subj' => $emailSubject,
          'notif_body' => $emailSubject,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
      ]);
      $notif->save();
      Session::flash('success_message', "Mobile-TopUp has been successfully.");
      return Redirect::back();

      }
      else{
      Session::flash('error_message', "Request not completed !");
      return Redirect::back();
      }
      }
      else{
      
        Transaction::where('id', $req->id)->update(['status' => $status, 'updated_at' =>DB::raw('updated_at')]);
        $tarns_req=Transaction::where('id', $req->id)->first();

        $sender_wallet = ($recordInfo->wallet_amount + $tarns_req->amount);

        $transdd = new Transaction([
            "user_id" =>$tarns_req->user_id,
            "receiver_id" => 0,
            "amount" => $tarns_req->amount,
            "fees" =>0,
            "currency" => $tarns_req->currency,
            "sender_fees" => 0,
            "sender_currency" => $tarns_req->sender_currency,
            "receiver_currency" => 'USD',
            "trans_type" => 1,
            "trans_to" => 'Dafri_Wallet',
            "trans_for" => 'Mobile-TopUp(Refund)',
            "refrence_id" => $tarns_req->id,
            "user_close_bal" => $sender_wallet,
            "real_value" => $tarns_req->amount,
            "billing_description" => 'IP : ' . $this->get_client_ip(),
            "status" => 1,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        ]);
        $transdd->save();

         User::where('id', $recordInfo->id)->update(['wallet_amount' => $sender_wallet]);

         $amount_admin_currency = $this->convertCurrency($recordInfo->currency, 'USD',$tarns_req->amount);
         $amount_admin_currencyArr = explode("##", $amount_admin_currency);
         $admin_amount = $amount_admin_currencyArr[0];

         $adminInfo = User::where('id', 1)->first();
         $admin_wallet = ($adminInfo->wallet_amount - $admin_amount);
         User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
        //Mail Start

        $TransId = $req->id;
        $emailId = $user->email;
        if ($recordInfo->user_type == 'Personal') {
            $userName = strtoupper($recordInfo->first_name);
            $userFullName = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
            $receiverName = strtoupper($recordInfo->first_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($recordInfo->user_type == 'Business') {
            $userName = strtoupper($recordInfo->business_name);
            $userFullName = strtoupper($recordInfo->business_name);
            $receiverName = strtoupper($recordInfo->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
            $userName = strtoupper($recordInfo->first_name);
            $userFullName = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
            $receiverName = strtoupper($recordInfo->first_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
            $userName = strtoupper($recordInfo->business_name);
            $userFullName = strtoupper($recordInfo->business_name);
            $receiverName = strtoupper($recordInfo->business_name);
            $loginLnk = HTTP_PATH . '/business-login';
        }
        $emailSubject = 'DafriBank Digital | Mobile-TopUp Request Cancelled';
        $emailData['subjects'] = $emailSubject;
        $emailData['userName'] = strtoupper($userName);
        $emailData['emailId'] = $emailId;
        $emailData['TransId'] = $TransId;
        $emailData['loginLnk'] = $loginLnk;
        Mail::send('emails.mobile_topup_reject', $emailData, function ($message)use ($emailData, $emailId) {
            $message->to($emailId, $emailId)
                    ->subject($emailData['subjects']);
        });
        //            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
        $notif = new Notification([
            'user_id' => $user->id,
            'notif_subj' => $emailSubject,
            'notif_body' => $emailSubject,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $notif->save();

        Session::flash('success_message', "Mobile-TopUp has been cancelled.");
        return Redirect::back();

      }
    }

    public function updategiftcardReqStatus($id, $status) {
        $req = Transaction::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();
        $giftcard = GiftCard::where('d_trans_id',$req->id)->orderBy('id','desc')->first();
        //to check that request has been updated or not
        if($req->status==1 || $req->status==3)
        {
        Session::flash('error_message', "Request already updated");
        //return Redirect::to('/admin/users/manual-deposit-request');
        return Redirect::back();
        }
      if ($user->user_type == 'Personal') {
        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
        $loginLnk = HTTP_PATH . '/personal-login';
    } else if ($user->user_type == 'Business') {
        $user_name = $this->business_short_name($user);
        $loginLnk = HTTP_PATH . '/business-login';
    } else if ($user->user_type == 'Agent' && $user->first_name == "") {
        $user_name = $this->business_short_name($user);
        $loginLnk = HTTP_PATH . '/business-login';
    } else if ($user->user_type == 'Agent' && $user->first_name != "") {
        $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
        $loginLnk = HTTP_PATH . '/personal-login';
    }
        if ($recordInfo->user_type == 'Personal') {
            $userName = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
        } else if ($recordInfo->user_type == 'Business') {
            $userName = strtoupper($recordInfo->business_name);
        } else if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
            $userName = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
        } else if ($recordInfo->user_type == 'Agent' and $recordInfo->business_name != "") {
            $userName = strtoupper($recordInfo->business_name);
        }
        $country=$recordInfo->country;
        $country_short_name=Country::where('name',$country)->first()->sortname;

        
      $curl = curl_init();
      $access_token=$this->getTokenGiftCard();

      $product_id=$giftcard->productId;
      $productName=$giftcard->productName;
      $countryCode=$giftcard->countryCode;
      $quantity=$giftcard->quantity;
      $unitPrice=$giftcard->unitPrice;
      $product_image_link=$giftcard->product_image_link;
    
      $customIdentifier=$this->generateRandomString1();
      $senderName=$userName;
      $recipientEmail=$recordInfo->email;
      $rcountryCode=$country_short_name; 
      if($recordInfo->country_code!="" && $recordInfo->phone!="")
      {
      $phoneNumber=preg_replace('/^\+?'.$recordInfo->country_code.'|\D/', '', ($recordInfo->phone));
      }
      else{
      $phoneNumber="";   
      }


      $total_amount=$req->amount;
      $currency_code='ZAR';  
      $total_amount_zar_arr = $this->convertCurrency($recordInfo->currency,$currency_code,$total_amount);
      $total_amount_zar=explode('##',$total_amount_zar_arr)[0];

      $con_rate=$this->convertCurrency($currency_code,$recordInfo->currency,$total_amount);
      $converstion_rate=explode('##',$con_rate)[1];

      if ($recordInfo->user_type == 'Personal') {
        if ($recordInfo->account_category == "Silver") {
            $fee_name = 'GIFT_CARD_SILVER';
        } else if ($recordInfo->account_category == "Gold") {
            $fee_name = 'GIFT_CARD_GOLD';
        } else if ($recordInfo->account_category == "Platinum") {
            $fee_name = 'GIFT_CARD_PLATINUM';
        } else if ($recordInfo->account_category == "Private Wealth") {
            $fee_name = 'GIFT_CARD_PRIVATE_WEALTH';
        } else {
            $fee_name = 'GIFT_CARD_SILVER';
        }
        $fees = Fee::where('fee_name', $fee_name)->first();
        $fees_amount = ($total_amount * $fees->fee_value) / 100;
    } else if ($recordInfo->user_type == 'Business') {
        if ($recordInfo->account_category == "Gold") {
            $fee_name = 'MERCHANT_GIFT_CARD_GOLD';
        } else if ($recordInfo->account_category == "Platinum") {
            $fee_name = 'MERCHANT_GIFT_CARD_PLATINUM';
        } else if ($recordInfo->account_category == "Enterprises") {
            $fee_name = 'MERCHANT_GIFT_CARD_ENTERPRIS';
        } else {
            $fee_name = 'MERCHANT_GIFT_CARD_GOLD';
        }
        $fees = Fee::where('fee_name', $fee_name)->first();
        $fees_amount = ($total_amount * $fees->fee_value) / 100;
    } else {
        if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name != "") {
            if ($recordInfo->account_category == "Silver") {
                $fee_name = 'GIFT_CARD_SILVER';
            } else if ($recordInfo->account_category == "Gold") {
                $fee_name = 'GIFT_CARD_GOLD';
            } else if ($recordInfo->account_category == "Platinum") {
                $fee_name = 'GIFT_CARD_PLATINUM';
            } else if ($recordInfo->account_category == "Private Wealth") {
                $fee_name = 'GIFT_CARD_PRIVATE_WEALTH';
            } else {
                $fee_name = 'GIFT_CARD_SILVER';
            }  
            $fees = Fee::where('fee_name', $fee_name)->first();
            $fees_amount = ($total_amount * $fees->fee_value) / 100;
        } else if ($recordInfo->user_type == 'Agent' and $recordInfo->first_name == "") {
            if ($recordInfo->account_category == "Gold") {
                $fee_name = 'MERCHANT_GIFT_CARD_GOLD';
            } else if ($recordInfo->account_category == "Platinum") {
                $fee_name = 'MERCHANT_GIFT_CARD_PLATINUM';
            } else if ($recordInfo->account_category == "Enterprises") {
                $fee_name = 'MERCHANT_GIFT_CARD_ENTERPRIS';
            } else {
                $fee_name = 'MERCHANT_GIFT_CARD_GOLD';
            }
            $fees = Fee::where('fee_name', $fee_name)->first();
            $fees_amount = ($total_amount * $fees->fee_value) / 100;
          }
        }

        $withdraw_amount_total=$total_amount+$fees_amount;
        $sender_wallet = ($recordInfo->wallet_amount + $withdraw_amount_total);
        
      if ($status == 1) {


        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => AIRTIME_GIFTCARD_URL.'/orders',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "productId":'.$product_id.',
            "countryCode": "'.$countryCode.'",
            "quantity": '.$quantity.',
            "unitPrice": '.$unitPrice.',
            "customIdentifier": "'.$customIdentifier.'",
            "senderName": "'.$userName.'",
            "recipientEmail": "'.$recordInfo->email.'",
            "recipientPhoneDetails": {
              "countryCode": "'.$rcountryCode.'",
              "phoneNumber": "'.$phoneNumber.'"
            }
        }',
        CURLOPT_HTTPHEADER => array(
          'Authorization:Bearer ' .$access_token,
          'Content-Type: application/json',
          'Accept: application/com.reloadly.giftcards-v1+json'
        ),
      ));
        
      $response = curl_exec($curl);
      curl_close($curl);
      $order_detail = json_decode($response);
      $checkstatus="1";


   
 if(isset($order_detail->status)){
if($order_detail->status=='SUCCESSFUL'){
               $total_amount_sms=0;
               if(isset($order_detail->smsFee)){
                    if($recordInfo->currency==$currency_code){  
                    $total_amount_sms=$order_detail->smsFee;
                    $total_amount=$total_amount+$total_amount_sms;
                    }
                    else
                    { 
                    $total_amount_sms=$order_detail->smsFee;     
                    $amt_array_sms = $this->convertCurrency($currency_code,$recordInfo->currency,$total_amount_sms);
                    $amt_array_explode_sms=explode('##',$amt_array_sms);
                    $total_amount_sms=$amt_array_explode_sms[0];
                    $total_amount=$total_amount+$total_amount_sms;
                    }
                    $withdraw_amount_total=$total_amount+$fees_amount;
                    $sms_fees_zar=$order_detail->smsFee; 
                 }

                 //to convert sms fees to user currency
                 $sender_wallet = ($recordInfo->wallet_amount - $total_amount_sms);

                
                if($recordInfo->currency!=$currency_code) {
                $billing_description= 'IP:' . $this->get_client_ip()."##Amount " . $currency_code . " " . ($total_amount_zar+$sms_fees_zar) . " and Conversion rate " . $converstion_rate ." = ".$recordInfo->currency.' '.$total_amount.'##SENDER_FEES :' . $recordInfo->currency .' '.$fees_amount;
                }else{
                $billing_description= 'IP:' . $this->get_client_ip().'##SENDER_FEES :' . $recordInfo->currency .' '.$fees_amount;   
                }

                Transaction::where('id', $req->id)->update(["amount"=>$total_amount,"refrence_id" => $order_detail->transactionId,'fees' => $fees_amount,'sender_real_value' => $withdraw_amount_total,  'status' => $checkstatus, 'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>$billing_description]);

                GiftCard::where('id', $giftcard->id)->update(["r_trans_id" => $order_detail->transactionId,'amount' => $order_detail->amount,'discount' =>$order_detail->discount, 'fee' => $order_detail->fee,
                "recipientEmail" =>$order_detail->recipientEmail,
                "customIdentifier" =>$order_detail->customIdentifier,
                "productId" => $order_detail->product->productId,
                "productName" => $order_detail->product->productName,
                "countryCode" => $order_detail->product->countryCode,
                "quantity" => $order_detail->product->quantity,
                "unitPrice" => $order_detail->product->unitPrice,
                "totalPrice" => $order_detail->product->totalPrice,
                "productCurrencyCode" =>$order_detail->product->currencyCode,
                "brandId" => $order_detail->product->brand->brandId,
                "brandName" => $order_detail->product->brand->brandName,
                "smsFee" => $order_detail->smsFee,
                "recipientPhone" =>$order_detail->recipientPhone,
                "product_image_link" => $product_image_link,
                "amount_user_currency" => $withdraw_amount_total,
                "user_currency" => $recordInfo->currency,
                "transactionCreatedTime" => $order_detail->transactionCreatedTime,
                "updated_at" => date('Y-m-d H:i:s')]);


                User::where('id',$user->id)->update(['wallet_amount' => $sender_wallet]);

                $amount_admin_currency = $this->convertCurrency($recordInfo->currency, 'USD',$total_amount_sms);
                $amount_admin_currencyArr = explode("##", $amount_admin_currency);
                $admin_amount = $amount_admin_currencyArr[0];
                $adminInfo = User::where('id', 1)->first();
                $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);
                User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);


                $trans_id=$order_detail->transactionId;
                $access_token=$this->getTokenGiftCard();
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => AIRTIME_GIFTCARD_URL . '/orders/transactions/'.$trans_id.'/cards',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization:Bearer ' . $access_token . '
                     Accept: application/com.reloadly.giftcards-v1+json'
                ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $giftDetail = json_decode($response);
                //  echo "<pre>";
                //  print_r($giftDetail);
                if(array_key_exists(0,$giftDetail))
                {
                    $cardNumber=[];
                    $pinCode=[];
                    $html='';
                    foreach($giftDetail as $value)
                    {
                        $cardNumber[] = $value->cardNumber;
                        $pinCode[]=$value->pinCode;
                    }
                  GiftCard::where('r_trans_id',$trans_id)->update(['cardNumber' => json_encode($cardNumber),'pinCode' =>  json_encode($pinCode)]);  
                }

            //Mail Start
            $TransId = $req->id;
            $emailId = $user->email;
            $userName = strtoupper($user_name);

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your gift card request with transaction ID ' . $TransId . ' has been processed successfully. Your can use gift card now.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Gift Card Request has been Completed';
            $emailData['subjects'] = $emailSubject;
            $emailData['user_name'] = $userName;
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = number_format($fees_amount, 2, '.', '');
            $emailData['currency'] = $user->currency;
            $emailData['name'] = $order_detail->product->productName;
            Mail::send('emails.gift_card_approve', $emailData, function ($message)use ($emailData, $emailId) {
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
    }
    else{
        Session::flash('error_message',$order_detail->message);
        return Redirect::back();
    }
    
        }

                if ($status == 3) {
                $withdraw_amount_total=$total_amount;
                $sender_wallet = ($recordInfo->wallet_amount + $withdraw_amount_total);
                Transaction::where('id', $req->id)->update(['status' => $status, 'updated_at' =>DB::raw('updated_at')]);
                $tarns_req=Transaction::where('id', $req->id)->first();
                $transdd = new Transaction([
                "user_id" =>$tarns_req->user_id,
                "receiver_id" => 0,
                "amount" => $tarns_req->amount,
                "fees" =>0,
                "currency" => $tarns_req->currency,
                "sender_fees" => 0,
                "sender_currency" => $tarns_req->sender_currency,
                "receiver_currency" => 'USD',
                "trans_type" => 1,
                "trans_to" => 'Dafri_Wallet',
                "trans_for" => 'GIFT CARD(Refund)',
                "refrence_id" => $tarns_req->id,
                "user_close_bal" => $sender_wallet,
                "real_value" => $tarns_req->amount,
                "billing_description" => 'IP : ' . $this->get_client_ip(),
                "status" => 1,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]);

            $transdd->save();

            GiftCard::where('d_trans_id',$tarns_req->id)->delete();

            User::where('id', $recordInfo->id)->update(['wallet_amount' => $sender_wallet]);
            $amount_admin_currency = $this->convertCurrency($recordInfo->currency, 'USD',$withdraw_amount_total);
            $amount_admin_currencyArr = explode("##", $amount_admin_currency);
            $admin_amount = $amount_admin_currencyArr[0];
            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
            //Mail Start

            $TransId = $req->id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: trans_id750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your gift card request with transaction ID ' . $TransId . ' has been cancelled.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Gift Card Request Cancelled';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = number_format($fees_amount, 2, '.', '');
            $emailData['currency'] = $user->currency;
            Mail::send('emails.gift_card_reject', $emailData, function ($message)use ($emailData, $emailId) {
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

        Session::flash('success_message', "Gift Card Request Updated successfully.");
       return Redirect::back();
    }


    public function updateManualDepositReqStatus($id, $status) {
        $req = ManualDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();
        
        //to check that request has been updated or not
        if($req->status==1 || $req->status==3)
        {
        Session::flash('error_message', "Request already updated");
        //return Redirect::to('/admin/users/manual-deposit-request');
        return Redirect::back();
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
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
                $convr_fee_name = 'CONVERSION_FEE';
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
                $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
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
                    $convr_fee_name = 'CONVERSION_FEE';
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
                    $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
                }
            }

            $admin_percentage=0;
            $refrlComm=0;
            $conversion_fees=0;
            if($user->currency=="USD")
            {
              $per_currency='na';  
              $total_amt=$fees_amount;  
              if ($recordInfo->referral != 'na' && $recordInfo->referral != '') {
                $referlCode = 'refid=' . $recordInfo->referral;
                $referrer = Referalcode::where('referal_link', $referlCode)->first(); 
                $amountt = ($total_amt * 25) / 100;
                $refrlComm = $amountt;
             }
             $admin_percentage=$total_amt-$refrlComm;
            }
            else
            { 

             $converted_amt= $this->convertCurrency( $user->currency,'USD', $fees_amount);
             $total_amt=explode("##",$converted_amt)[0];

             if($user->currency=='NGN')
             {
             $per_currency='Conversion Rate : 1 USD = '.explode("##",$converted_amt)[1].' '.$user->currency;
             }
             else{
             $per_currency='Conversion Rate : 1 '.$user->currency.' = '.explode("##",$converted_amt)[1].' USD';
             } 

             //to check that user have Affiliate or not
             $referlCode = 'refid=' . $recordInfo->referral;
             $referrer = Referalcode::where('referal_link', $referlCode)->first();
             if ($recordInfo->referral != 'na' && $recordInfo->referral != '') {
                $referral_User=User::where('id',$referrer->user_id)->first();
                if ($referral_User->user_type == 'Personal') {
                    $convr_fee_name_ref = 'CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Business') {
                    $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name != "") {
                    $convr_fee_name_ref = 'CONVERSION_FEE';
                } elseif ($referral_User->user_type == 'Agent' && $referral_User->first_name == "") {
                    $convr_fee_name_ref = 'MERCHANT_CONVERSION_FEE';
                }
                $amountt = ($total_amt * 25) / 100;
                $fees_convr = Fee::where('fee_name', $convr_fee_name_ref)->first();
                $conversion_feet = $fees_convr->fee_value;
                $conversion_fees=$amountt * $conversion_feet / 100;
                $refrlComm = $amountt - $conversion_fees;
             }
             $admin_percentage=$total_amt-$refrlComm;
            }
            
            $admin_dollar_record= $this->convertCurrency( $user->currency,'USD', $deposit_amount);
            $deposit_amount_doller=explode("##",$admin_dollar_record)[0];
            $admin_wallet_balance=$deposit_amount_doller-$admin_percentage;

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_wallet_balance);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            $deposit_amount_total = $deposit_amount- $fees_amount;
            $user_wallet = $user->wallet_amount + $deposit_amount_total;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

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
            $real_value=$deposit_amount-$fees_amount;
            Transaction::where('id', $req->trans_id)->update(["refrence_id" => $refrence_id,'fees' => $fees_amount,'receiver_fees' => $fees_amount, 'sender_currency' =>'USD', 'receiver_currency' =>$user->currency, 'user_close_bal' => $user_wallet, 'real_value' => $real_value, 'status' => $status, 'updated_at' => date('Y-m-d H:i:s'),'billing_description'=>$per_currency]);

         
            if (!empty($referrer)) {
                $refComm = new ReferralCommission([ 
                    'user_id' => $req->user_id,
                    'referrer_id' => $referrer->user_id,
                    'amount' => $refrlComm,
                    'trans_id' => $req->trans_id,
                    'action' => 'MANUAL_DEPOSIT',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $refComm->save();
            }
        }

        if ($status == 3) {
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: trans_id750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your manual deposit request with transaction ID ' . $TransId . ' has been cancelled.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
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

            Transaction::where('id', $req->trans_id)->update(["refrence_id" => $refrence_id,'user_close_bal' =>  $user->wallet_amount,'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        ManualDeposit::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");

       // return Redirect::to('/admin/users/manual-deposit-request');
       return Redirect::back();
    }

    public function updateManualDepositReqStatus_old($id, $status) {
        $req = ManualDeposit::where('id', $id)->first();
        $user = $recordInfo = User::where('id', $req->user_id)->first();

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
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


    public function globalWithdrawRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'global-withdraw-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'Global Pay Withdraw Requests';
        $activetab = 'actglobalwithdrawreq';
        $query = new ManualWithdraw();
        $query = $query->sortable();
        $query->where('withdraw_type','!=',0);
        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });
                });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestGlobalWithdraw', ['requests' => $requests]);
        }

        return view('admin.users.globalWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
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
        $query->where('withdraw_type',0);
        if ($request->has('keyword') && $request->get('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
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
       // echo $status; die;

        if($req->status==1 || $req->status==3)
        {
        Session::flash('error_message', "Request already updated");
      //  return Redirect::to('/admin/users/manual-withdraw-request');
      return Redirect::back();
        }

        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            Transaction::where('id', $req->trans_id)->update(['status' => 1,'updated_at' => DB::raw('updated_at')]);
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->where('trans_id', $req->trans_id)->delete();

            // $amount_admin_currency = $this->convertCurrency($user->currency, 'USD',  $req->amount);
            // $amount_admin_currencyArr = explode("##", $amount_admin_currency);
            // $admin_amount = $amount_admin_currencyArr[0];

            // $adminInfo = User::where('id', 1)->first();
            // $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);
            // User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
                    
            //Mail Start
            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual withdrawal request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $req->amount . '). has been debited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Outgoing Payment';
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
           // return Redirect::to('admin/users/manual-withdraw-request');
           return Redirect::back();
        } else if ($status == 3) {
            $tarns_req=Transaction::where('id', $req->trans_id)->first();
            $user_wallet = $user->wallet_amount + $tarns_req->amount;
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 3, 'updated_at' => date('Y-m-d H:i:s')]);
            Transaction::where('id', $req->trans_id)->update(['status' => 3, 'updated_at' => DB::raw('updated_at')]);
            // exit;
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->where('trans_id', $req->trans_id)->delete();

            $trans = new Transaction([
                "user_id" =>$tarns_req->user_id,
                "receiver_id" => 0,
                "amount" => $tarns_req->amount,
                "fees" =>0,
                "currency" => $tarns_req->currency,
                "sender_fees" => 0,
                "sender_currency" => $tarns_req->sender_currency,
                "receiver_currency" => 'USD',
                "trans_type" => 1,
                "trans_to" => 'Dafri_Wallet',
                "trans_for" => 'Manual Withdraw (Refund)',
                "refrence_id" => $tarns_req->id,
                "user_close_bal" => $user_wallet,
                "real_value" => $tarns_req->amount,
                "billing_description" => 'IP : ' . $this->get_client_ip(),
                "status" => 1,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]);
            $trans->save();

            $amount_admin_currency = $this->convertCurrency($user->currency, 'USD',  $req->amount);
            $amount_admin_currencyArr = explode("##", $amount_admin_currency);
            $admin_amount = $amount_admin_currencyArr[0];

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

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
           // return Redirect::to('admin/users/manual-withdraw-request');
           return Redirect::back();
        } else {
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 2, 'updated_at' => date('Y-m-d H:i:s')]);
            Session::flash('success_message', "Request status changed successfully.");
            //return Redirect::to('admin/users/manual-withdraw-request');
            return Redirect::back();
        }
    }


    public function changeGlobalReqStatus($id, $status) { //Manual Withdraw Request Status Change
        $req = ManualWithdraw::where('id', $id)->first();
        $user = User::where('id', $req->user_id)->first();
       // echo $status; die;

        if($req->status==1 || $req->status==3)
        {
        Session::flash('error_message', "Request already updated");
      //  return Redirect::to('/admin/users/manual-withdraw-request');
      return Redirect::back();
        }

      
    


        if ($user->user_type == 'Personal') {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = strtoupper($user->first_name) . " " . ucwords($user->last_name);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            Transaction::where('id', $req->trans_id)->update(['status' => 1,'updated_at' => DB::raw('updated_at')]);
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->where('trans_id', $req->trans_id)->delete();

            // $amount_admin_currency = $this->convertCurrency($user->currency, 'USD',  $req->amount);
            // $amount_admin_currencyArr = explode("##", $amount_admin_currency);
            // $admin_amount = $amount_admin_currencyArr[0];

            // $adminInfo = User::where('id', 1)->first();
            // $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);
            // User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
                    
            //Mail Start
            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations, your manual withdrawal request with transaction ID ' . $TransId . ' has been processed successfully. Your amount (' . $user->currency . ' ' . $req->amount . '). has been debited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Outgoing Payment';
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
           // return Redirect::to('admin/users/manual-withdraw-request');
           return Redirect::back();
        } else if ($status == 3) {
            $tarns_req=Transaction::where('id', $req->trans_id)->first();

            $user_wallet = $user->wallet_amount + $tarns_req->amount;
            //$user_wallet = $user->wallet_amount + $tarns_req->amount+$tarns_req->sender_fees;

            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 3, 'updated_at' => date('Y-m-d H:i:s')]);
            Transaction::where('id', $req->trans_id)->update(['status' => 3, 'updated_at' => DB::raw('updated_at')]);
            // exit;
            InactiveAmount::where('withdraw_req_id', $id)->where('user_id', $req->user_id)->where('trans_id', $req->trans_id)->delete();

            $trans_status='';
            if($req->withdraw_type==1)
            {
             $trans_status="Global Pay (Refund)";   
            }
            else if($req->withdraw_type==2)
            {
             $trans_status="3rd Party Pay (Refund)";   
            }

            $trans = new Transaction([
                "user_id" =>$tarns_req->user_id,
                "receiver_id" => 0,
                "amount" => $tarns_req->amount,
                "fees" =>0,
                "currency" => $tarns_req->currency,
                "sender_fees" => 0,
                "sender_currency" => $tarns_req->sender_currency,
                "receiver_currency" => 'USD',
                "trans_type" => 1,
                "trans_to" => 'Dafri_Wallet',
                "trans_for" =>$trans_status,
                "refrence_id" => $tarns_req->id,
                "user_close_bal" => $user_wallet,
                "real_value" => $tarns_req->amount,
                "billing_description" => 'IP : ' . $this->get_client_ip(),
                "status" => 1,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]);
            $trans->save();

            $amount_admin_currency = $this->convertCurrency($user->currency, 'USD',  $req->amount);
            $amount_admin_currencyArr = explode("##", $amount_admin_currency);
            $admin_amount = $amount_admin_currencyArr[0];

            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

            //Mail Start
            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Sorry, Your manual withdrawal request with transaction ID ' . $TransId . ' has been cancelled. Your amount (' . $user->currency . ' ' . $req->amount . '). has been credited to your DafriBank account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | Global Pay Withdrawal Request has been Declined';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;
            $emailData['amount'] = $req->amount;
            $emailData['currency'] = $user->currency;
            Mail::send('emails.changeManualReqStatusCancelGlobalPay', $emailData, function ($message)use ($emailData, $emailId) {
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
           // return Redirect::to('admin/users/manual-withdraw-request');
           return Redirect::back();
        } else {
            ManualWithdraw::where('id', $id)->update(['edited_by' => Session::get('adminid'), 'status' => 2, 'updated_at' => date('Y-m-d H:i:s')]);
            Session::flash('success_message', "Request status changed successfully.");
            //return Redirect::to('admin/users/manual-withdraw-request');
            return Redirect::back();
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
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
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
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
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
            $convr_fee_name=$recordInfo->currency!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';   
            $main_fee_name = 'WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Business') {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
            $convr_fee_name=$recordInfo->currency!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';   
            $main_fee_name = 'MERCHANT_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
            $activetab = 'actusers';
            $convr_fee_name=$recordInfo->currency!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';  
            $main_fee_name = 'WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
            $activetab = 'actmerchants';
            $convr_fee_name=$recordInfo->currency!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE'; 
            $main_fee_name = 'MERCHANT_WALLET_ADJUST_FEES';
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

                    //to calculate main fees
                    $fees_main = Fee::where('fee_name', $main_fee_name)->first();
                    $fees_main_value = $fees_main->fee_value;
                    $total_fees = ($input['amount'] * $fees_main_value) / 100;

                    if ($recordInfo->currency != 'USD') {
                        $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                        $conversion_fee_reciver = $fees_convr->fee_value;
                        $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
//                        echo '/';
                        $remainBal = $input['amount'] + $fee + $total_fees;
                        $amount_user_currency = $this->convertCurrency($recordInfo->currency,'USD',  $remainBal,'debit'); 
                        $convrsArr = explode("##", $amount_user_currency);
                        $adminBal = $convrsArr[0];
                        $billing_description = '<br>Debited By Admin<br>IP:' . $this->get_client_ip() . '##Amount '.$recordInfo->currency.' '.$input['amount'] .'##Reason: ' . $input['reason'].'##SENDER_FEES: '.$recordInfo->currency.' '.$total_fees.'##Conversion Fees = '.$recordInfo->currency.' '.$fee;
                    }
                    else{
                        $remainBal = $input['amount'] + $total_fees;
                        $amount_user_currency = $this->convertCurrency($recordInfo->currency,'USD',  $remainBal);                        
                        $convrsArr = explode("##", $amount_user_currency);
                        $adminBal = $convrsArr[0];
                        $billing_description = '<br>Debited By Admin<br>IP:' . $this->get_client_ip() . '##Amount '.$recordInfo->currency.' '.$input['amount'] .'##Reason: ' . 
                        $input['reason'].'##SENDER_FEES'.$recordInfo->currency.' '.$total_fees;
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
                        "fees" => ($fee+$total_fees),
                        "sender_fees" => ($fee+$total_fees),
                        "sender_currency"=>$recordInfo->currency,
                        "currency" => $recordInfo->currency,
                        "receiver_currency"=>'USD',
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "receiver_close_bal" => $admin_wallet,
                        "real_value" => $remainBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been debited with ' . $recordInfo->currency . ' ' . $input['amount'] . 'by paying fees '.$recordInfo->currency.' '.$fee.' for transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['fees'] = $fee+$total_fees;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['currency'] = $recordInfo->currency;
                    $emailData['wallet_balance'] = $user_wallet;
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
                   
                    //to calculate main fees
                    $fees_main = Fee::where('fee_name', $main_fee_name)->first();
                    $fees_main_value = $fees_main->fee_value;
                    $total_fees = ($input['amount'] * $fees_main_value) / 100;


                    $fee = 0;
                    $billing_description = '<br>Credited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $input['amount'] . '##Reason: ' . $input['reason'];
                    if ($recordInfo->currency != 'USD') {
                      //  $convr_fee_name = 'CONVERSION_FEE';
                        $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                        $conversion_fee_reciver = $fees_convr->fee_value;
                        $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
//                        echo '/';
                        $remainBal = $input['amount'] - ($fee+$total_fees);

                        $amount_user_currency = $this->convertCurrency($recordInfo->currency, 'USD',  $remainBal);                       
                        $convrsArr = explode("##", $amount_user_currency);
                        $adminBal = $convrsArr[0];

                        $billing_description = '<br>Credited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $recordInfo->currency.' '.$input['amount'].'##Reason: ' . $input['reason'].'##RECEIVER_FEES: '.$recordInfo->currency.' '.$total_fees.'##Conversion Fees RECEIVER = '.$recordInfo->currency.' '.$fee;
                    }
                    else{
                        $remainBal = $input['amount'] - $total_fees;
                        $amount_user_currency = $this->convertCurrency($recordInfo->currency, 'USD',  $remainBal);                       
                        $convrsArr = explode("##", $amount_user_currency);
                        $adminBal = $convrsArr[0];
                        $billing_description = '<br>Credited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $recordInfo->currency.' '.$input['amount'].'##Reason: ' . $input['reason'].'##RECEIVER_FEES: '.$recordInfo->currency.' '.($total_fees);
                    }
                    
//die;

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
                        "fees" => ($fee+$total_fees),
                        "sender_currency"=>'USD',
                        "receiver_fees"=>($fee+$total_fees),
                        "receiver_currency"=>$recordInfo->currency,
                        "currency" => $recordInfo->currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $admin->wallet_amount,
                        "receiver_close_bal" => $user_wallet,
                        "real_value" => $remainBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;
                    $wallet_balance=$recordInfo->wallet_amount;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been credited with ' . $recordInfo->currency . ' ' . $input['amount'] . ' transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['fee'] = $fee+$total_fees;
                    $emailData['currency'] = $recordInfo->currency;
                    $emailData['wallet_balance'] = $user_wallet;
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


    public function adjustBalanceFix($user_id) {
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
            $convr_fee_name = 'CONVERSION_FEE';
            $main_fee_name = 'WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Business') {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
            $main_fee_name = 'MERCHANT_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
            $activetab = 'actusers';
            $convr_fee_name = 'CONVERSION_FEE';
            $main_fee_name = 'WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_CONVERSION_FEE';
            $main_fee_name = 'MERCHANT_WALLET_ADJUST_FEES';
        }

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

                    //to calculate main fees
                    $total_fees=0;
                    $remainBal = $input['amount'] + $total_fees;
                    $amount_user_currency = $this->convertCurrency($recordInfo->currency,'USD',  $remainBal,'debit');                        
                    $convrsArr = explode("##", $amount_user_currency);
                    $adminBal = $convrsArr[0];
                    $billing_description = '<br>Debited By Admin<br>IP:' . $this->get_client_ip() . '##Amount '.$recordInfo->currency.' '.$input['amount'] .'##Reason: ' . 
                    $input['reason'];

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
                        "fees" => ($fee+$total_fees),
                        "sender_fees" => ($fee+$total_fees),
                        "sender_currency"=>$recordInfo->currency,
                        "currency" => $recordInfo->currency,
                        "receiver_currency"=>'USD',
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "receiver_close_bal" => $admin_wallet,
                        "real_value" => $remainBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been debited with ' . $recordInfo->currency . ' ' . $input['amount'] . 'by paying fees '.$recordInfo->currency.' '.$fee.' for transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['fees'] = $fee;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['currency'] = $recordInfo->currency;
                    $emailData['wallet_balance'] = $user_wallet;
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
                   
                    //to calculate main fees
                    $fee = 0;
                    $total_fees=0;
                    $remainBal = $input['amount'] - $total_fees;
                    $amount_user_currency = $this->convertCurrency($recordInfo->currency, 'USD',  $remainBal);                       
                    $convrsArr = explode("##", $amount_user_currency);
                    $adminBal = $convrsArr[0];
                    $billing_description = '<br>Credited By Admin<br>IP:' . $this->get_client_ip() . '##Amount ' . $recordInfo->currency.' '.$input['amount'].'##Reason: ' . $input['reason'];

                    $admin_wallet = $admin->wallet_amount - $adminBal;
                    User::where('id', 1)->update(['wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    $user_wallet = $recordInfo->wallet_amount + $remainBal;
                    User::where('id', $user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    $trans = new Transaction([
                        "user_id" => 1,
                        "receiver_id" => $user_id,
                        "amount" => $input['amount'],
                        "fees" => ($fee+$total_fees),
                        "sender_currency"=>'USD',
                        "receiver_fees"=>($fee+$total_fees),
                        "receiver_currency"=>$recordInfo->currency,
                        "currency" => $recordInfo->currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'W2W',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $admin->wallet_amount,
                        "receiver_close_bal" => $user_wallet,
                        "real_value" => $remainBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();
                    $TransId = $trans->id;

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;

                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been credited with ' . $recordInfo->currency . ' ' . $input['amount'] . ' transaction ID ' . $TransId . '.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $input['amount'];
                    $emailData['fee'] = $fee;
                    $emailData['currency'] = $recordInfo->currency;
                    $emailData['wallet_balance'] = $user_wallet;
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

        return view('admin.users.adjustFixBalance', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }


    public function adjustDbaBalance($user_id) {
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
            $convr_fee_name = 'DBA_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Business') {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_DBA_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
            $activetab = 'actusers';
            $convr_fee_name = 'DBA_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_DBA_WALLET_ADJUST_FEES';
        }

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
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $remainBal = $input['amount'];
                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                    $conversion_fee_reciver = $fees_convr->fee_value;
                    $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
                    $adminBal = $input['amount']+$fee;
                    
                    $billing_description = 'Debited By Admin##IP:' . $this->get_client_ip() . '##Reason: ' . $input['reason'].'##SENDER_FEES:'.$recordInfo->dba_currency.' '.$fee;

                    $user_wallet = $recordInfo->dba_wallet_amount - $adminBal;
                    $trans = new DbaTransaction([
                        "user_id" => $user_id,
                        "receiver_id" => 1,
                        "amount" => $input['amount'],
                        "fees" => $fee,
                        "sender_currency"=>$recordInfo->dba_currency,
                        "sender_fees"=>$fee,
                        "receiver_currency"=>'DBA',
                        "currency" => $recordInfo->dba_currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'DBA WALLET ADJUST (DEBIT)',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "real_value" => $adminBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                   $trans->save();
                   $TransId = $trans->id;
                //   $TransId=11;

                    $admin_wallet = $admin->dba_wallet_amount + $adminBal;
                    User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    User::where('id', $user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                  
                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['fees'] = $fee;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $adminBal;
                    $emailData['currency'] = $recordInfo->dba_currency;
                    $emailData['wallet_balance'] = $user_wallet;
                    Mail::send('emails.adjustBalanceDBADebit', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                   
                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailSubject,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                } else if ($input['wallet_action'] == "credit") {
                    $admin = User::where('id', 1)->first();
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $remainBal = $input['amount'];
                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                    $conversion_fee_reciver = $fees_convr->fee_value;
                    $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
                    $adminBal = $input['amount']-$fee;
                    
                    $billing_description = 'Credited By Admin##IP:' . $this->get_client_ip() . '##Reason: ' . $input['reason'].'##RECEIVER_FEES:'.$recordInfo->dba_currency.' '.$fee;

                    $user_wallet = $recordInfo->dba_wallet_amount + $adminBal;
                    $trans = new DbaTransaction([
                        "user_id" => 1,
                        "receiver_id" => $user_id,
                        "amount" => $input['amount'],
                        "fees" => $fee,
                        "sender_currency"=>'DBA',
                        "receiver_fees"=>$fee,
                        "receiver_currency"=>$recordInfo->dba_currency,
                        "currency" => $recordInfo->dba_currency,
                        "trans_type" => 1, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'DBA WALLET ADJUST (CREDIT)',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "real_value" => $adminBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                   $trans->save();
                   $TransId = $trans->id;

                  // $TransId=11;

                    $admin_wallet = $admin->dba_wallet_amount - $adminBal;
                    User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    User::where('id', $user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $adminBal;
                    $emailData['fee'] = $fee;
                    $emailData['currency'] = $recordInfo->dba_currency;
                    $emailData['wallet_balance'] = $user_wallet;
                    Mail::send('emails.adjustBalanceCreditDBA', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailSubject,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                }

                Session::flash('success_message', "Client DBA Balance Adjusted Successfully.");
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

        return view('admin.users.adjustDbaBalance', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }


    public function adjustDbaBalanceFix($user_id) {
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
            $convr_fee_name = 'DBA_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Business') {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_DBA_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
            $activetab = 'actusers';
            $convr_fee_name = 'DBA_WALLET_ADJUST_FEES';
        } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
            $activetab = 'actmerchants';
            $convr_fee_name = 'MERCHANT_DBA_WALLET_ADJUST_FEES';
        }

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
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $remainBal = $input['amount'];
                    // $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                    // $conversion_fee_reciver = $fees_convr->fee_value;
                    // $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
                    $fee=0;
                    $adminBal = $input['amount']+$fee;
                    
                    $billing_description = 'Debited By Admin##IP:' . $this->get_client_ip() . '##Reason: ' . $input['reason'];

                    $user_wallet = $recordInfo->dba_wallet_amount - $adminBal;
                    $trans = new DbaTransaction([
                        "user_id" => $user_id,
                        "receiver_id" => 1,
                        "amount" => $input['amount'],
                        "fees" => $fee,
                        "sender_currency"=>$recordInfo->dba_currency,
                        "sender_fees"=>$fee,
                        "receiver_currency"=>'DBA',
                        "currency" => $recordInfo->dba_currency,
                        "trans_type" => 2, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'DBA WALLET ADJUST (DEBIT)',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "real_value" => $adminBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                   $trans->save();
                   $TransId = $trans->id;
                //   $TransId=11;

                    $admin_wallet = $admin->dba_wallet_amount + $adminBal;
                    User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    User::where('id', $user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                  
                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['fees'] = $fee;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $adminBal;
                    $emailData['currency'] = $recordInfo->dba_currency;
                    $emailData['wallet_balance'] = $user_wallet;
                    Mail::send('emails.adjustBalanceDBADebit', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                   
                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailSubject,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                } else if ($input['wallet_action'] == "credit") {
                    $admin = User::where('id', 1)->first();
                    $refrence_id = time() . rand() . Session::get('user_id');
                    $remainBal = $input['amount'];
                    // $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                    // $conversion_fee_reciver = $fees_convr->fee_value;
                    // $fee = ($input['amount'] * $conversion_fee_reciver) / 100;
                    $fee=0;
                    $adminBal = $input['amount']-$fee;
                    
                    $billing_description = 'Credited By Admin##IP:' . $this->get_client_ip() . '##Reason: ' . $input['reason'].'##RECEIVER_FEES:'.$recordInfo->dba_currency.' '.$fee;

                    $user_wallet = $recordInfo->dba_wallet_amount + $adminBal;
                    $trans = new DbaTransaction([
                        "user_id" => 1,
                        "receiver_id" => $user_id,
                        "amount" => $input['amount'],
                        "fees" => $fee,
                        "sender_currency"=>'DBA',
                        "receiver_fees"=>$fee,
                        "receiver_currency"=>$recordInfo->dba_currency,
                        "currency" => $recordInfo->dba_currency,
                        "trans_type" => 1, //Debit
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'DBA WALLET ADJUST (CREDIT)',
                        "refrence_id" => $refrence_id,
                        "billing_description" => $billing_description,
                        "user_close_bal" => $user_wallet,
                        "real_value" => $adminBal,
                        "status" => 1,
                        "edited_by" => Session::get('adminid'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                   $trans->save();
                   $TransId = $trans->id;

                  // $TransId=11;

                    $admin_wallet = $admin->dba_wallet_amount - $adminBal;
                    User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    User::where('id', $user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                    //Mail Start
                    if ($recordInfo->user_type == 'Personal') {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    } else if ($recordInfo->user_type == 'Business') {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                        $user_name = $this->business_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/business-login';
                    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                        $user_name = $this->personal_short_name($recordInfo);
                        $loginLnk = HTTP_PATH . '/personal-login';
                    }
                    $emailId = $recordInfo->email;
                    $userName = $user_name;
                    $emailSubject = 'NotifyMe | DafriBank EFT';
                    $emailData['subjects'] = $emailSubject;
                    $emailData['userName'] = strtoupper($userName);
                    $emailData['emailId'] = $emailId;
                    $emailData['TransId'] = $TransId;
                    $emailData['loginLnk'] = $loginLnk;
                    $emailData['amount'] = $adminBal;
                    $emailData['fee'] = $fee;
                    $emailData['currency'] = $recordInfo->dba_currency;
                    $emailData['wallet_balance'] = $user_wallet;
                    Mail::send('emails.adjustBalanceCreditDBA', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subjects']);
                    });
                    $notif = new Notification([
                        'user_id' => $recordInfo->id,
                        'notif_subj' => $emailSubject,
                        'notif_body' => $emailSubject,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $notif->save();
                    //Mail End
                }

                Session::flash('success_message', "Client DBA Balance Adjusted Successfully.");
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

        return view('admin.users.adjustDbaFixBalance', ['title' => $pageTitle, $activetab => 1, 'recordInfo' => $recordInfo]);
    }


    

    private function myCurrencyConversionRate($merchant_currency, $user_currency, $amount) {
        $apikey = CURRENCY_CONVERT_API_KEY;

        $query = $merchant_currency . '_' . $user_currency;

        $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
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
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });
                $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestCryptoWithdraw', ['requests' => $requests]);
        }
//        echo '<pre>';print_r($requests);exit;

        return view('admin.users.cryptoWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
    }

    public function dbaWithdrawRequest(Request $request) {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'dba-withdraw-request');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }
        $pageTitle = 'DBA Withdraw Requests';
        $activetab = 'actdbawithdrawreq';
        $query = new DbaWithdraw();
        $query = $query->sortable();

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->orWhereHas('User', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                });
                $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
            });
        }

        $requests = $query->orderBy('id', 'DESC')->paginate(20);

        if ($request->ajax()) {
            return view('elements.admin.users.requestDbaWithdraw', ['requests' => $requests]);
        }
//        echo '<pre>';print_r($requests);exit;

        return view('admin.users.dbaWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
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
          //  return Redirect::to('/admin/users/crypto-deposit-request');
          return Redirect::back();
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
          //  return Redirect::to('/admin/users/crypto-deposit-request');
          return Redirect::back();
        }

        if ($user->user_type == 'Personal') {
            $user_name = $this->personal_short_name($user);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = $this->personal_short_name($user);
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

          //  $amount_cc = $this->usdToUserCurrency($user->currency, $req->amount);
            $tarns_req=Transaction::where('id', $req->trans_id)->first();
            $user_wallet = $user->wallet_amount + $tarns_req->amount;
           // $amount_cc = number_format($amount_cc, 2, '.', '');
            User::where('id', $req->user_id)->update(['wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

            $amount_admin_currency = $this->convertCurrency($tarns_req->currency,'USD',$tarns_req->amount);
            $amount_admin_currencyArr = explode("##", $amount_admin_currency);
            $admin_amount = $amount_admin_currencyArr[0];
            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->wallet_amount - $admin_amount);
            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);
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

            $trans = new Transaction([
                "user_id" =>$tarns_req->user_id,
                "receiver_id" => 0,
                "amount" => $tarns_req->amount,
                "fees" =>0,
                "currency" => $tarns_req->currency,
                "sender_fees" => 0,
                "sender_currency" => $tarns_req->sender_currency,
                "receiver_currency" => 'USD',
                "trans_type" => 1,
                "trans_to" => 'Dafri_Wallet',
                "trans_for" => 'CryptoWithdraw(Refund)',
                "refrence_id" => $tarns_req->id,
                "user_close_bal" => $user_wallet,
                "real_value" => $tarns_req->amount,
                "billing_description" => 'IP : ' . $this->get_client_ip(),
                "status" => 1,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]);
            $trans->save();

        }

        CryptoWithdraw::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
        Transaction::where('id', $req->trans_id)->update(['status' => $status,'updated_at' => DB::raw('updated_at')]);
       // return Redirect::to('/admin/users/crypto-withdraw-request');
       return Redirect::back();
    }

    public function updateDbaWithdrawReqStatus($id, $status) {
        $req = DbaWithdraw::where('id', $id)->first();
        $user = User::where('id', $req->user_id)->first();

        if ($req->status == 1) {
            Session::flash('success_message', "This request status is completed you can't change status.");
           // return Redirect::to('/admin/users/crypto-deposit-request');
           return Redirect::back();
        }

        if ($req->status == 3) {
            Session::flash('success_message', "This request status is cancelled you can't change status.");
           // return Redirect::to('/admin/users/crypto-deposit-request');
           return Redirect::back();
        }

        if ($user->user_type == 'Personal') {
            $user_name = $this->personal_short_name($user);
            $loginLnk = HTTP_PATH . '/personal-login';
        } else if ($user->user_type == 'Business') { 
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name == "") {
            $user_name = $this->business_short_name($user);
            $loginLnk = HTTP_PATH . '/business-login';
        } else if ($user->user_type == 'Agent' && $user->first_name != "") {
            $user_name = $this->personal_short_name($user);
            $loginLnk = HTTP_PATH . '/personal-login';
        }

        if ($status == 1) {

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
            Mail::send('emails.updatedbaWithdrawReqStatusCredit', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            }

        if ($status == 3) {  

            $tarns_req=DbaTransaction::where('id', $req->trans_id)->first();
            $user_wallet = $user->dba_wallet_amount + $tarns_req->amount;
            User::where('id', $req->user_id)->update(['dba_wallet_amount' => $user_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

            $admin_amount =  $tarns_req->amount;
            $adminInfo = User::where('id', 1)->first();
            $admin_wallet = ($adminInfo->dba_wallet_amount - $admin_amount);
            User::where('id', 1)->update(['dba_wallet_amount' => $admin_wallet]);
            //Mail Start

            $TransId = $req->trans_id;
            $emailId = $user->email;
            $userName = $user_name;

            $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Unfortunately, your Crypto Currency withdrawal request with transaction ID ' . $TransId . ' has been cancelled.<br><br>Your amount has been refunded to you account.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginLnk . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
            $emailSubject = 'DafriBank Digital | DBA Withdrawal Request Cancelled';
            $emailData['subjects'] = $emailSubject;
            $emailData['userName'] = strtoupper($userName);
            $emailData['emailId'] = $emailId;
            $emailData['TransId'] = $TransId;
            $emailData['loginLnk'] = $loginLnk;   
            $emailData['crypto_currency'] = $req->crypto_currency;
            Mail::send('emails.cancelDbaWithdrawReqStatus', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });  

            $trans = new DbaTransaction([
                "user_id" =>$tarns_req->user_id,
                "receiver_id" => 0,
                "amount" => $tarns_req->amount,
                "fees" =>0,
                "currency" => $tarns_req->currency,
                "sender_fees" => 0,
                "sender_currency" => $tarns_req->sender_currency,
                "receiver_currency" => 'DBA',
                "trans_type" => 1,
                "trans_to" => 'Dafri_Wallet',
                "trans_for" => 'DBA-WITHDRAW(REFUND)',
                "refrence_id" => $tarns_req->id,
                "user_close_bal" => $user_wallet,
                "real_value" => $tarns_req->amount,
                "billing_description" => 'IP : ' . $this->get_client_ip(),
                "status" => 1,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ]);
            $trans->save();
        }

        DbaWithdraw::where('id', $id)->update(['status' => $status, 'edited_by' => Session::get('adminid'), 'updated_at' => date('Y-m-d H:i:s')]);
        Session::flash('success_message', "Request status updated successfully.");
        DbaTransaction::where('id', $req->trans_id)->update(['status' => $status,'updated_at' => DB::raw('updated_at')]);
       // return Redirect::to('/admin/users/dba-withdraw-request');
       return Redirect::back();
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

    public function referralDetail(Request $request,$user_id)
    {
    $pageTitle = 'Affiliated Accounts';
    DB::enableQueryLog();
    $parent=User::where('id', $user_id)->first();
    $user_type=$parent->user_type;
    $activetab = $user_type=='Business' ? 'actmerchants' : 'actusers';
    $query = new User();
    $query = $query->sortable();
    if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('phone', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                //$q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
     //   DB::enableQueryLog();
        $users = $query->where('parent_id',$user_id)->orderBy('id', 'DESC')->paginate(20);
    //    dd(DB::getQueryLog());
        if ($request->ajax()) {
            return view('elements.admin.users.referralDetail', ['allrecords' => $users])->with('no', 1);
        }
        return view('admin.users.referralDetail', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $users,'user_id'=>$user_id]);

    }


    public function cryptoDebit(Request $request)
    {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        DB::enableQueryLog();
        $pageTitle = 'Manage Crypto Currency For Deposit';
        $activetab = 'actcryptodeposit';
        $query = new CryptoCurrency();
        $query = $query->sortable();
        $query=$query->Where('type',1);
        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->Where('name', 'like', '%' . $keyword . '%')->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }
        $users = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.users.crypto_debit', ['allrecords' => $users]);
        }
        return view('admin.users.crypto_debit', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $users]);

    }

    public function crypto_currency_delete($slug)
    {
        CryptoCurrency::where('id',$slug)->delete(); 
        Session::flash('success_message', "Crypto Currency has been deleted successfully");
        return Redirect::to('admin/users/crypto_debit');
    }


    public function cryptoDebitAdd(Request $request)
    {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        DB::enableQueryLog();
        $pageTitle = 'Manage Crypto Currency For Deposit';
        $activetab = 'actcryptodeposit';
        $input=Input::all();
        if (!empty($input)) {
        $rules = array(
        'name' => 'required',
        'address' => 'required',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) 
        {
        return Redirect::to('/admin/users/crypto_debit_add')->withErrors($validator)->withInput();
        } 
        else 
        {

        //to check duplicate entry 
        $cnt=CryptoCurrency::where('name',$input['name'])->where('type',$input['type'])->count();       
        if($cnt > 0)
        {
        Session::flash('error_message', "Duplicate entry not allowed");
        return Redirect::to('admin/users/crypto_debit'); 
        }
        $serialisedData['name'] = $input['name'];
        $serialisedData['address'] = $input['address'];
        $serialisedData['type'] = $input['type'];
        CryptoCurrency::insert($serialisedData); 
        Session::flash('success_message', "Crypto Currency has been added successfully for crypto deposit");
        return Redirect::to('admin/users/crypto_debit');
        }
        }
        return view('admin.users.crypto_debit_add', ['title' => $pageTitle, $activetab => 1]);

    }

    public function cryptoWithdrawCurrency(Request $request)
    {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        DB::enableQueryLog();
        $pageTitle = 'Manage Crypto Currency For WithDraw';
        $activetab = 'actcryptowithdraw';
        $query = new CryptoCurrency();
        $query = $query->sortable();
        $query=$query->Where('type',2);
        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query = $query->where(function ($q) use ($keyword) {
                $q->Where('name', 'like', '%' . $keyword . '%');
            });
        }
        $users = $query->orderBy('id', 'DESC')->paginate(20);
        if ($request->ajax()) {
            return view('elements.admin.users.crypto_withdraw_currency', ['allrecords' => $users]);
        }
        return view('admin.users.crypto_withdraw_currency', ['title' => $pageTitle, $activetab => 1, 'allrecords' => $users]);

    }

    public function crypto_withdraw_add(Request $request)
    {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-personal-user');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        DB::enableQueryLog();
        $pageTitle = 'Manage Crypto Currency For Deposit';
        $activetab = 'actcryptodeposit';
        $input=Input::all();
        if (!empty($input)) {
        $rules = array(
        'name' => 'required',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) 
        {
        return Redirect::to('/admin/users/crypto_withdraw_add')->withErrors($validator)->withInput();
        } 
        else 
        {

        //to check duplicate entry 
        $cnt=CryptoCurrency::where('name',$input['name'])->where('type',$input['type'])->count();       
        if($cnt > 0)
        {
        Session::flash('error_message', "Duplicate entry not allowed");
        return Redirect::to('admin/users/crypto_withdraw_currency'); 
        }
        $serialisedData['name'] = $input['name'];
        $serialisedData['type'] = $input['type'];
        CryptoCurrency::insert($serialisedData); 
        Session::flash('success_message', "Crypto Currency has been added successfully for crypto withdraw");
        return Redirect::to('admin/users/crypto_withdraw_currency');
        }
        }
        return view('admin.users.crypto_withdraw_add', ['title' => $pageTitle, $activetab => 1]);

    }

    public function crypto_withdraw_currency_delete($slug)
    {
        CryptoCurrency::where('id',$slug)->delete(); 
        Session::flash('success_message', "Crypto Currency has been deleted successfully");
        return Redirect::to('admin/users/crypto_withdraw_currency');
    }

    public function userDetail($slug)
    {
        $pageTitle = 'User Info';
        $activetab = 'actcryptodeposit';
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
        $query->where('slug',$slug);
        $users = $query->orderBy('id', 'DESC')->paginate(20);
        $recordInfo=User::where('slug',$slug)->first(); 
        if ($recordInfo->user_type == 'Personal') {
        return view('admin.users.userDetailPersonal', ['title' => $pageTitle, $activetab => 1,'allrecords'=>$users]);
        } 
        elseif ($recordInfo->user_type == 'Business') 
        {
        return view('admin.merchants.userDetailBusiness', ['title' => $pageTitle, $activetab => 1,'allrecords'=>$users]);
        }
        elseif ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") 
        {
        return view('admin.users.userDetailPersonal', ['title' => $pageTitle, $activetab => 1,'allrecords'=>$users]);
        }
        elseif ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") 
        {
        return view('admin.merchants.userDetailBusiness', ['title' => $pageTitle, $activetab => 1,'allrecords'=>$users]);
        }
    }

    public function personal_short_name($recordInfo)
    {

        $full_name_string=$recordInfo->first_name.' '.$recordInfo->last_name; 
        $exp_name=explode(" ",$full_name_string);
        $last_name=end($exp_name);
        $short_name="";
        for($i=0;$i < count($exp_name)-1; $i++)
        {
        if($i!=count($exp_name)-2 || count($exp_name)==2)
        {
        $short_name.=$exp_name[$i][0].".";
        }
        else{
        $short_name.=$exp_name[$i][0];
        }
        }
        $full_name=strtoupper($short_name." ".$last_name);
        return $full_name;   

    }

    public function business_short_name($recordInfo)
    {

        $full_name_string=trim($recordInfo->business_name); 
        $exp_name=explode(" ",$full_name_string);
        $last_name=end($exp_name);
        $short_name="";
        for($i=0;$i < count($exp_name)-1; $i++)
        {
        if($i!=count($exp_name)-2 || count($exp_name)==2)
        {

        $short_name.=$exp_name[$i][0].".";
        }
        else{
        $short_name.=$exp_name[$i][0];
        }
        }
        $full_name=strtoupper($short_name." ".$last_name);
        return $full_name;
    }



    private function convertCurrency($toCurrency, $frmCurrency, $amount,$debit=null) {
 
        if ($frmCurrency == 'NGN') {  
            $exchange = Ngnexchange::where('id', 1)->first();
            $to = strtolower($toCurrency);
            $var = $to . '_value';
            $val = $exchange->$var;
            $total = $amount * $val;
            return $total . "##" . $val;
        } else if ($toCurrency == 'NGN') {
            
            if($debit=='debit')
            {  
            $exchange = Ngnexchange::where('id', 2)->first();
            $to = strtolower($frmCurrency);
            $var = $to . '_value';
            $val = $exchange->$var;
            $total = $amount * $val;
            return $total . "##" . $val;
            }
            else{
            $exchange = Ngnexchange::where('id', 1)->first();
            $to = strtolower($frmCurrency);
            $var = $to . '_value';
            $val = $exchange->$var;
            $total = $amount / $val;
            return $total . "##" . $val;
            }
            } 
            else {
            $apikey = CURRENCY_CONVERT_API_KEY;
            $query = $toCurrency . "_" . $frmCurrency;
            $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
            $json = file_get_contents($curr_req);
            $obj = json_decode($json, true);
            $val = floatval($obj[$query]);
            $total = $val * $amount;
            return $total . "##" . $val;
        }
    }


            public function manualDepositRequestlist(Request $request, $slug = null) {
                $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-manual-deposit');
                if ($isPermitted == false) {
                    $pageTitle = 'Not Permitted';
                    $activetab = 'actchangeusername';
                    return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
                }

                $userInfo = User::where("slug", $slug)->first();
                $pageTitle = 'Manual Deposit Requests';
                $activetab = 'actmanualdepositreq';
                $query = new ManualDeposit();
                $query = $query->sortable();
        
                if ($request->has('keyword')) {
                    $keyword = $request->get('keyword');
                    $query = $query->where(function ($q) use ($keyword) {
                        $q->orWhereHas('User', function ($q) use ($keyword) {
                        $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        });
                        $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('ref_number', 'like', '%' . $keyword . '%');
                    });
                }
                $ussId = $userInfo->id;
                $query = $query->where(function ($q) use ($ussId) {
                    $q->where('user_id', $ussId);
                });
                $requests = $query->orderBy('id', 'DESC')->paginate(20);
        
                if ($request->ajax()) {
                    return view('elements.admin.users.requestManualDeposit', ['requests' => $requests]);
                }
        
                return view('admin.users.manualDepositRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
            }

            public function manualWithdrawRequestlist(Request $request, $slug = null) {
                $isPermitted = $this->validatePermission(Session::get('admin_role'), 'list-manual-withdraw');
                if ($isPermitted == false) {
                    $pageTitle = 'Not Permitted';
                    $activetab = 'actchangeusername';
                    return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
                }

                $userInfo = User::where("slug", $slug)->first();
                $pageTitle = 'Manual Withdraw Requests';
                $activetab = 'actmanualwithdrawreq';
                $query = new ManualWithdraw();
                $query = $query->sortable();
        
                if ($request->has('keyword') && $request->get('keyword')) {
                    $keyword = $request->get('keyword');
                    $query = $query->where(function ($q) use ($keyword) {
                        $q->orWhereHas('User', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        });
                        });
                }
                $ussId = $userInfo->id;
                $query = $query->where(function ($q) use ($ussId) {
                    $q->where('user_id', $ussId);
                });
                $requests = $query->orderBy('id', 'DESC')->paginate(20);
        
                if ($request->ajax()) {
                    return view('elements.admin.users.requestManualWithdraw', ['requests' => $requests]);
                }
        
                return view('admin.users.manualWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
            }


            public function cryptoDepositRequestlist(Request $request, $slug = null) {
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
                $userInfo = User::where("slug", $slug)->first();
                if ($request->has('keyword')) {
                    $keyword = $request->get('keyword');
                    $query = $query->where(function ($q) use ($keyword) {
                        $q->orWhereHas('User', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        });
                        $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
                    });
                }
                $ussId = $userInfo->id;
                $query = $query->where(function ($q) use ($ussId) {
                    $q->where('user_id', $ussId);
                });
                $requests = $query->orderBy('id', 'DESC')->paginate(20);
        
                if ($request->ajax()) {
                    return view('elements.admin.users.requestCryptoDeposit', ['requests' => $requests]);
                }
        
                return view('admin.users.cryptoDepositRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
            }

            public function cryptoWithdrawRequestlist(Request $request, $slug = null) {
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
                $userInfo = User::where("slug", $slug)->first();
                if ($request->has('keyword')) {
                    $keyword = $request->get('keyword');
                    $query = $query->where(function ($q) use ($keyword) {
                        $q->orWhereHas('User', function ($q) use ($keyword) {
                            $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%" . $keyword . "%")->orWhere('business_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%')->orWhere('account_number', 'like', '%' . $keyword . '%');
                        });
                        $q->orWhere('user_name', 'like', '%' . $keyword . '%')->orWhere('crypto_currency', 'like', '%' . $keyword . '%');
                    });
                } 
                $ussId = $userInfo->id;
                $query = $query->where(function ($q) use ($ussId) {
                    $q->where('user_id', $ussId);
                });
        
                $requests = $query->orderBy('id', 'DESC')->paginate(20);
        
                if ($request->ajax()) {
                    return view('elements.admin.users.requestCryptoWithdraw', ['requests' => $requests]);
                }
        //        echo '<pre>';print_r($requests);exit;
        
                return view('admin.users.cryptoWithdrawRequest', ['title' => $pageTitle, $activetab => 1, 'requests' => $requests]);
            }


            public function manageLimit($slug) {
                $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-user-wallet');
                if ($isPermitted == false) {
                    $pageTitle = 'Not Permitted';
                    $activetab = 'actchangeusername';
                    return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
                }
                $pageTitle = 'Manage Wallet Limit';
                $recordInfo = User::where("slug", $slug)->first();

                 //to check the user limit exist or not
                 $user_limit=WalletLimitUser::where('user_id',$recordInfo->id)->orderBy('id','desc')->first();
                //  echo "<pre>";
                //  print_r($user_limit); 

                $input = Input::all();
                if (!empty($input)) {
                           
                            if(!isset($user_limit->id))
                            {
                            $trans = new WalletLimitUser([
                                "user_id" => $recordInfo->id,
                                "daily_limit" => $input['daily_limit'],
                                "week_limit" => $input['week_limit'],
                                "month_limit" => $input['month_limit'],
                                "edited_by" => Session::get('adminid'),
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                            $trans->save();
                            }
                            else{
                                WalletLimitUser::where('id', $user_limit->id)->update([
                                "daily_limit" => $input['daily_limit'],
                                "week_limit" => $input['week_limit'],
                                "month_limit" => $input['month_limit'],
                                "edited_by" => Session::get('adminid'),
                            ]);
                            }
                            Session::flash('success_message', "Configure Transaction's Limit Successfully");
                            if ($recordInfo->user_type == 'Personal') {
                              return Redirect::to('admin/users');
                            } else if ($recordInfo->user_type == 'Business') {
                              return Redirect::to('admin/merchants');
                            } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
                              return Redirect::to('admin/users');
                            } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
                              return Redirect::to('admin/merchants');
                            }
                }

                return view('admin.users.manage_wallet_limit', ['title' => $pageTitle, 'recordInfo' => $recordInfo,'user_limit'=>$user_limit]);
            }


     public function downloadStatement(Request $request,$user_id) {   
     $isPermitted = $this->validatePermission(Session::get('admin_role'), 'edit-user-wallet');
     if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
     }
    $pageTitle = 'Manage Wallet Limit';
    $recordInfo = User::where('id', $user_id)->first();
    if ($request->has('dwnldStatmnt') && $request->get('dwnldStatmnt') == 'true') {
    $statement_period = $request->get('perdStatmnt');
    if ($statement_period == 'last_month') {
        $to = date('Y-m-d', strtotime(date('Y-m-d') . ' - 30 days'));
        $from = date('Y-m-d');
    } else if ($statement_period == 'last_3_month') {
        $to = date('Y-m-d', strtotime(date('Y-m-d') . ' - 90 days'));
        $from = date('Y-m-d');
    } else if ($statement_period == 'last_6_month') {
        $to = date('Y-m-d', strtotime(date('Y-m-d') . ' - 180 days'));
        $from = date('Y-m-d');
    } else if ($statement_period == 'last_1_yr') {
        $to = date('Y-m-d', strtotime(date('Y-m-d') . ' - 365 days'));
        $from = date('Y-m-d');
    } else if ($request->has('statement_date') && $request->get('statement_date') != "") {
        $statementDate = explode(" - ", $request->get('statement_date'));
        $mailToDate = $statementDate[0];
        $mailFromDate = $statementDate[1];
        $to = $statementDate[0];
        $from = $statementDate[1];
    }
    
//            echo '<pre>';print_r($statementDate);exit;
    $mailToDate = $to;
    $to = $to . ' 00:00:00';
    $is_currency_updated=0;
    $to_date=$to;
    if (isset($updated_currency_date) && $updated_currency_date != "") {
        if(strtotime($to) > strtotime($updated_currency_date))
        {
        $to=$to;   
        }
        else{
        $to = $updated_currency_date;
        }
        $is_currency_updated=1;
    }

    $mailFromDate = $from;
    $from = $from . ' 23:59:59';
    $query2 = new Transaction();
    $query2 = $query2->where(function ($q) use ($to, $from) {
        $q->where('created_at', '>=', $to)->where('created_at', '<=', $from);
        $q->where('status', 1);
    });

    $query2 = $query2->where(function ($query2)use ($user_id) { 
        $query2->where('user_id', $user_id)->orWhere('receiver_id', $user_id);
    });

    $query2 = $query2->orWhere(function ($query2)use ($to, $from,$user_id) {
        $query2->where([['trans_for', 'CryptoWithdraw'], ['user_id', $user_id], ['created_at', '>=', $to], ['created_at', '<=', $from]]);
        $query2->orWhere([['trans_for', 'Withdraw##Invite_New_User'], ['user_id', $user_id], ['created_at', '>=', $to], ['created_at', '<=', $from]]);
        $query2->orWhere([['trans_for', 'CryptoWithdraw(Refund)'], ['user_id', $user_id], ['created_at', '>=', $to], ['created_at', '<=', $from]]);
        $query2->orWhere([['trans_for', 'Manual Withdraw'], ['user_id', $user_id], ['created_at', '>=', $to], ['created_at', '<=', $from]]);
        $query2->orWhere([['trans_for', 'Withdraw##Agent'], ['user_id', $user_id], ['created_at', '>=', $to], ['created_at', '<=', $from]]);
    });
    //   DB::connection()->enableQueryLog();
    $transPDF = $query2->orderBy("updated_at", "ASC")->get();
    //  print_r($transPDF); die;
    //     $queries = DB::getQueryLog();
    //  echo '<pre>';print_r($queries);exit;
    //10-May-2021 Start
    if ($recordInfo->user_type == 'Personal') {
        $detl["name"] = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
        $detl["acc_type"] = "Personal";
    } else if ($recordInfo->user_type == 'Business') {
        $detl["name"] = strtoupper($recordInfo->business_name);
        $detl["acc_type"] = "Business";
    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
        $detl["name"] = strtoupper($recordInfo->business_name);
        $detl["acc_type"] = "Business";
    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
        $detl["name"] = strtoupper($recordInfo->first_name . ' ' . $recordInfo->last_name);
        $detl["acc_type"] = "Personal";
    }
    $detl["email"] = $recordInfo->email;
    $detl["acc_number"] = $recordInfo->account_number;
    $detl["acc_currency"] = $recordInfo->currency;
    $detl["acc_type"] = $recordInfo->account_category;
    $toDate = date_create($mailToDate);
    $mlToDate = date_format($toDate, 'm/d/Y');

    $frDate = date_create($mailFromDate);
    $mlFrDate = date_format($frDate, 'm/d/Y');

    $detl["statement_period"] = $mlToDate . " to " . $mlFrDate;
    $detl["addrs_line1"] = $recordInfo->addrs_line1;
    $detl["addrs_line2"] = $recordInfo->addrs_line2;
    //DB::enableQueryLog();
//                $ttlCrdtAmount_1 = Transaction::where('user_id', $user_id)->where('trans_type', 1)->whereBetween('created_at', array($to, $from))->sum('transactions.amount');
//                $ttlCrdtAmount_2 = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->sum('transactions.amount');

    $ttlCrdtAmount_1 = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlCrdtAmount_2 = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_for','!=', 'SWAP')->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');
   // echo $ttlCrdtAmount_2; die;

    $ttlCrdtCount_1 = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->whereBetween('created_at', array($to, $from))->count();

    $ttlCrdtCount_2 = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->count();

    $ttlCrdtAmount = $ttlCrdtAmount_1 + $ttlCrdtAmount_2;
    $ttlCrdtCount = $ttlCrdtCount_1 + $ttlCrdtCount_2;

    $ttlCrdtBank_1 = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'ManualDeposit')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlCrdtBankCnt_1 = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'ManualDeposit')->whereBetween('created_at', array($to, $from))->count();

    $Manual_deposit_fee = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'ManualDeposit')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    $Manual_deposit_fee_count = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'ManualDeposit')->whereBetween('created_at', array($to, $from))->count();

    $ttlCrdtBank = $ttlCrdtBank_1;
    $ttlCrdtBankCnt = $ttlCrdtBankCnt_1;

    //for crypto deposit
    $ttlCrdtCpt_1 = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'CryptoDeposit')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlCrdtCptCnt_1 = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'CryptoDeposit')->whereBetween('created_at', array($to, $from))->count();

    $crypto_deposit_fee = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'CryptoDeposit')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    $crypto_deposit_fee_count = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'CryptoDeposit')->whereBetween('created_at', array($to, $from))->count();


    //for merchant widraw
    $merchant_widraw_total = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Merchant_Withdraw')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $merchant_widraw_total_count = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Merchant_Withdraw')->whereBetween('created_at', array($to, $from))->count();

    $merchant_widraw_total_fees = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Merchant_Withdraw')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    //merchant widraw api sender side
    $merchant_widraw_api_total_sender = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Merchant_Withdraw')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');

    $merchant_widraw_api_total_sender_count = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Merchant_Withdraw')->whereBetween('created_at', array($to, $from))->count();

    $merchant_widraw_api_total_sender_fees = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Merchant_Withdraw')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');


    $ttlCrdtCpt = $ttlCrdtCpt_1;
    $ttlCrdtCptCnt = $ttlCrdtCptCnt_1;

    //for ozow eft
    $Ozow_fees = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', 'OZOW_EFT')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    $Ozow_fees_count = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', 'OZOW_EFT')->whereBetween('created_at', array($to, $from))->count();
    
    $ttlCrdtOzow = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'OZOW_EFT')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlCrdtOzowCnt = Transaction::where('user_id', $user_id)->where('status', 1)->where('trans_type', 1)->where('trans_for', 'OZOW_EFT')->whereBetween('created_at', array($to, $from))->count();

    //for agent withdraw
    $ttlAgentCrt_1 = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Withdraw##Agent')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');   
    
    $ttlAgentCrtCount_1 = Transaction::where('receiver_id', $user_id)->where('status', 1)->where('trans_type', 2)->where('trans_for', 'Withdraw##Agent')->whereBetween('created_at', array($to, $from))->count();

    $ttlOtherDeposit_1 = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', '!=', 'W2W')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlOtherDepositCount_1 = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', '!=', 'W2W')->whereBetween('created_at', array($to, $from))->count();


    //for fund transfer and credit from admin side
    $ttlCashDeposit_1 = Transaction::where('receiver_id', $user_id)->where('user_id',1)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'not like', '%##Wallet2Wallet%')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlCashDepositCount_1 = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('user_id',1)->where('billing_description', 'not like', '%##Wallet2Wallet%')->whereBetween('created_at', array($to, $from))->count();

    $W2W_admin_credit_fees = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('user_id',1)->where('billing_description', 'not like', '%##Wallet2Wallet%')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');


    //for fund transfer to normal user when user is receiver
    $ttlCashDeposit_1_fund_receiver = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%Wallet2Wallet Transfer%')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlCashDeposit_1_fund_receiver_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%Wallet2Wallet Transfer%')->whereBetween('created_at', array($to, $from))->count();

    $ttlCashDeposit_1_fund_receiver_fees = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%Wallet2Wallet Transfer%')->whereBetween('created_at', array($to, $from))->count();

    //for debit 

    $W2W_admin_debit_amount = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%<br>Debited By Admin%')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $W2W_admin_debit_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%<br>Debited By Admin%')->whereBetween('created_at', array($to, $from))->count();

    $W2W_admin_debit_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%<br>Debited By Admin%')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

    $W2W_admin_debit_fees_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('fees','!=', '0.0000000000')->where('billing_description', 'like', '%<br>Debited By Admin%')->whereBetween('created_at', array($to, $from))->count();

    $W2W_admin_credit_fees_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('fees','!=', '0.0000000000')->where('billing_description', 'not like', '%##Wallet2Wallet%')->whereBetween('created_at', array($to, $from))->count();

    $ttlCashDeposit_1_fund_transfer = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%##Wallet2Wallet%')->where('pay_by_agent', '0')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');
  //  print_r($ttlCashDeposit_1_fund_transfer); die;

    $ttlCashDepositCount_1_fund_transfer_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%##Wallet2Wallet%')->where('pay_by_agent', '0')->whereBetween('created_at', array($to, $from))->count();

    $W2W_admin_credit_fees_fund_transfer = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%##Wallet2Wallet%')->where('pay_by_agent', '0')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    $W2W_admin_credit_fees_count_fund_transfer = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'like', '%##Wallet2Wallet%')->where('pay_by_agent', '0')->whereBetween('created_at', array($to, $from))->count();

    //if fund transfer by agent
    $ttlCashDeposit_1_fund_transfer_agent = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('pay_by_agent', '1')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');
    // print_r($ttlCashDeposit_1_fund_transfer); die;

    $ttlCashDepositCount_1_fund_transfer_count_agent = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('pay_by_agent', '1')->whereBetween('created_at', array($to, $from))->count(); 

    $W2W_admin_credit_fees_fund_transfer_agent = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('pay_by_agent', '1')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    $W2W_admin_credit_fees_count_fund_transfer_agent = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('pay_by_agent', '1')->whereBetween('created_at', array($to, $from))->count();


    //to find out fund transfer to new user

    $fund_transfer_to_new_user = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Invite_New_User')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $fund_transfer_to_new_user_fees = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Invite_New_User')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

    $fund_transfer_to_new_user_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Invite_New_User')->whereBetween('created_at', array($to, $from))->count();

    //fees for sender side to fund transfer to new user
    $sender_fund_transfer_to_new_user = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Invite_New_User')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');

    $sender_fund_transfer_to_new_user_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Invite_New_User')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

    $sender_fund_transfer_to_new_user_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Invite_New_User')->whereBetween('created_at', array($to, $from))->count();


    //for fund transfer from one user to another when receiver is not admin
    $fund_transfer_debit = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('receiver_id', '!=', 1)->where('trans_for', '=', 'W2W')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');

    $fund_transfer_debit_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('receiver_id', '!=', 1)->where('trans_for', '=', 'W2W')->whereBetween('created_at', array($to, $from))->count();

    $fund_transfer_debit_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('receiver_id', '!=', 1)->where('trans_for', '=', 'W2W')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

    $ttlAccountDeposit_1 = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'not like', '%##Wallet2Wallet%')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlAccountDepositCount_1 = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->where('billing_description', 'not like', '%##Wallet2Wallet%')->whereBetween('created_at', array($to, $from))->count();

    $ttlAccountDebit_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlAccountDebitCount_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'W2W')->whereBetween('created_at', array($to, $from))->count();

    $ttlCashDebit_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Agent')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $Bank_Agent_dr_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Agent')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

    $ttlCashDebitCount_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Withdraw##Agent')->whereBetween('created_at', array($to, $from))->count();

    $ttlAccDebit_1 = Transaction::where('user_id', $user_id)->where('trans_for', 'Manual Withdraw')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');
    //   print_r($ttlAccDebit_1); die;
    //for online payment
    $ttlAccDebit_Online_payment = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'ONLINE_PAYMENT')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');
    // print_r($ttlAccDebit_Online_payment); die;

    $ttlAccDebit_Online_payment_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'ONLINE_PAYMENT')->whereBetween('created_at', array($to, $from))->count();

    $ttlAccDebit_Online_payment_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'ONLINE_PAYMENT')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');


    //online payment for receiver side
    $ttlAccCredit_Online_payment = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'ONLINE_PAYMENT')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');
    // print_r($ttlAccDebit_Online_payment); die;

    $ttlAccCredit_Online_payment_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'ONLINE_PAYMENT')->whereBetween('created_at', array($to, $from))->count();

    $ttlAccCredit_Online_payment_fees = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'ONLINE_PAYMENT')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');


    $ttlAccDebitCount_1 = Transaction::where('user_id', $user_id)->where('trans_for', 'Manual Withdraw')->whereBetween('created_at', array($to, $from))->count();

    $manual_withdraw_fees = Transaction::where('user_id', $user_id)->where('trans_for', 'Manual Withdraw')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');


    $ttlOtherDebit_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
                $query->where('trans_for', '=', 'Withdraw##Paypal')
                        ->orWhere('trans_for', '=', 'CryptoWithdraw');
            })->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

    $ttlOtherDebit_1_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
                $query->where('trans_for', '=', 'Withdraw##Paypal')
                        ->orWhere('trans_for', '=', 'CryptoWithdraw');
            })->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

    $crypto_withdraw_fee_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
                $query->where('trans_for', '=', 'Withdraw##Paypal')
                        ->orWhere('trans_for', '=', 'CryptoWithdraw');
            })->whereBetween('created_at', array($to, $from))->count();


    $ttlOtherDebitCount_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
                $query->where('trans_for', '=', 'Withdraw##Paypal')
                        ->orWhere('trans_for', '=', 'CryptoWithdraw');
            })->whereBetween('created_at', array($to, $from))->count();


    //to find out total amount of refund
    $total_refund_credit = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', 'like', '%(Refund)%')->whereBetween('created_at', array($to, $from))->sum('transactions.amount');

    $total_refund_credit_count = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', 'like', '%(Refund)%')->whereBetween('created_at', array($to, $from))->count();


    $ttlDbtAmount = $ttlCashDebit_1 + $ttlOtherDebit_1 + $ttlAccDebit_1 + $ttlAccountDebit_1 + $ttlAccDebit_Online_payment;
    $ttlDbtCount = $ttlCashDebitCount_1 + $ttlOtherDebitCount_1 + $ttlAccDebitCount_1 + $ttlAccountDebitCount_1;

    $ttlFee_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');
    $ttlFee_2 = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');
    $ttlFeeCount_1 = Transaction::where('user_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->count();
    $ttlFeeCount_2 = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($to, $from))->count();
    $ttlFee = $ttlFee_1 + $ttlFee_2;
    $ttlFeeCount = $ttlFeeCount_1 + $ttlFeeCount_2;

    //to calculate the converted amount count and fees
    $currency_conversion_count = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', 'Converted Amount')->whereBetween('created_at', array($to, $from))->count();

    $currency_conversion_amount = Transaction::where('user_id', $user_id)->where('trans_type', 1)->where('trans_for', 'Converted Amount')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

//to calculate the exchange charge and count
$exchange_charge = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Exchange Charge')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

$exchange_charge_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Exchange Charge')->whereBetween('created_at', array($to, $from))->count();

//to calculate the mobile top up transactions
$mobile_topup_total = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Mobile Top-up')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

$mobile_topup_total_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Mobile Top-up')->whereBetween('created_at', array($to, $from))->count();

$mobile_topup_total_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'Mobile Top-up')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

//to calculate the dba cash 
$total_dba_cash_received = Transaction::where('receiver_id', $user_id)->where('trans_type', 1)->where('trans_for', 'DBA eCash')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

$total_dba_cash_received_fees = Transaction::where('receiver_id', $user_id)->where('trans_type', 1)->where('trans_for', 'DBA eCash')->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

$total_dba_cash_received_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 1)->where('trans_for', 'DBA eCash')->whereBetween('created_at', array($to, $from))->count();

 //to calculate the swap
 $total_dba_swap = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'SWAP')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

 $total_dba_swap_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'SWAP')->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');

 $total_dba_swap_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where('trans_for', 'SWAP')->whereBetween('created_at', array($to, $from))->count();

 //for epay me as a receiver
 $total_epay_me_received = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where('trans_for', 'EPAY ME')->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

 $total_epay_me_received=Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where(function ($query) {
    $query->where('trans_for', '=', 'EPAY ME')
            ->orWhere('trans_for', '=', 'EPAY MERCHANT');
})->whereBetween('created_at', array($to, $from))->sum('transactions.real_value');

 $total_epay_me_received_count = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where(function ($query) {
    $query->where('trans_for', 'EPAY ME') 
          ->orWhere('trans_for', '=', 'EPAY MERCHANT');
    })->whereBetween('created_at', array($to, $from))->count();

 $total_epay_me_received_fees = Transaction::where('receiver_id', $user_id)->where('trans_type', 2)->where(function ($query) {
 $query->where('trans_for', 'EPAY ME') 
       ->orWhere('trans_for', '=', 'EPAY MERCHANT');
 })->whereBetween('created_at', array($to, $from))->sum('transactions.receiver_fees');

 //for epay me as a sender
 $total_epay_me_sender = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
    $query->where('trans_for', 'EPAY ME') 
          ->orWhere('trans_for', '=', 'EPAY MERCHANT');
    })->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');

 $total_epay_me_sender_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
    $query->where('trans_for', 'EPAY ME') 
          ->orWhere('trans_for', '=', 'EPAY MERCHANT');
    })->whereBetween('created_at', array($to, $from))->count();

 $total_epay_me_sender_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
    $query->where('trans_for', 'EPAY ME') 
          ->orWhere('trans_for', '=', 'EPAY MERCHANT');
    })->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');;

    //for gift card
    $total_gift_card_sender = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
        $query->where('trans_for', 'GIFT CARD'); 
        })->whereBetween('created_at', array($to, $from))->sum('transactions.sender_real_value');

   $total_gift_card_sender_count = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
    $query->where('trans_for', 'GIFT CARD'); 
    })->whereBetween('created_at', array($to, $from))->count();

    $total_gift_card_sender_fees = Transaction::where('user_id', $user_id)->where('trans_type', 2)->where(function ($query) {
        $query->where('trans_for', 'GIFT CARD'); 
        })->whereBetween('created_at', array($to, $from))->sum('transactions.sender_fees');
        



    $detl["user_type"] = $recordInfo->user_type;
//                echo $ttlDbtAmount;exit;
    $detl["ttlCrdtAmount"] = $ttlCrdtAmount;
    $detl["ttlDbtAmount"] = $ttlDbtAmount;
    $detl["ttlDbtCount"] = $ttlDbtCount;
    $detl["ttlCrdtCount"] = $ttlCrdtCount;
    $detl["currency_conversion_count"] = $currency_conversion_count;
    $detl["currency_conversion_amount"] = $currency_conversion_amount;

    //for gift card 
    $detl["total_gift_card_sender"] = $total_gift_card_sender;
    $detl["total_gift_card_sender_count"] = $total_gift_card_sender_count;
    $detl["total_gift_card_sender_fees"] = $total_gift_card_sender_fees;

    //for epay me received
    $detl["total_epay_me_received"] = $total_epay_me_received;
    $detl["total_epay_me_received_count"] = $total_epay_me_received_count;
    $detl["total_epay_me_received_fees"] = $total_epay_me_received_fees;

    //for epay me as a sender
    $detl["total_epay_me_sender"] = $total_epay_me_sender;
    $detl["total_epay_me_sender_count"] = $total_epay_me_sender_count;    
    $detl["total_epay_me_sender_fees"] = $total_epay_me_sender_fees;    

    //for the dba cash 
    $detl["total_dba_cash_received"] = $total_dba_cash_received;
    $detl["total_dba_cash_received_fees"] = $total_dba_cash_received_fees;
    $detl["total_dba_cash_received_count"] = $total_dba_cash_received_count;

    //for the dba swap 
    $detl["total_dba_swap"] = $total_dba_swap;
    $detl["total_dba_swap_fees"] = $total_dba_swap_fees;
    $detl["total_dba_swap_count"] = $total_dba_swap_count;


    //for fund transfer when receiver is normal user
    $detl["ttlCashDeposit_1_fund_receiver"] = $ttlCashDeposit_1_fund_receiver;
    $detl["ttlCashDeposit_1_fund_receiver_count"] = $ttlCashDeposit_1_fund_receiver_count;
    $detl["ttlCashDeposit_1_fund_receiver_fees"] = $ttlCashDeposit_1_fund_receiver_fees;


    //for exchange charge
    $detl["exchange_charge"] = $exchange_charge;
    $detl["exchange_charge_count"] = $exchange_charge_count;

    //for moble topup
    $detl["mobile_topup_total"] = $mobile_topup_total;
    $detl["mobile_topup_total_count"] = $mobile_topup_total_count;
    $detl["mobile_topup_total_fees"] = $mobile_topup_total_fees;

    //fund transfer to new user
    $detl["fund_transfer_to_new_user"] = $fund_transfer_to_new_user;
    $detl["fund_transfer_to_new_user_fees"] = $fund_transfer_to_new_user_fees;
    $detl["fund_transfer_to_new_user_count"] = $fund_transfer_to_new_user_count;

    //fees for fund transfer to new user for sender side
    $detl["sender_fund_transfer_to_new_user"] = $sender_fund_transfer_to_new_user;
    $detl["sender_fund_transfer_to_new_user_fees"] = $sender_fund_transfer_to_new_user_fees;
    $detl["sender_fund_transfer_to_new_user_count"] = $sender_fund_transfer_to_new_user_count;

    //for fund transfer when receiver is not admin
    $detl["fund_transfer_debit"] = $fund_transfer_debit;
    $detl["fund_transfer_debit_count"] = $fund_transfer_debit_count;
    $detl["fund_transfer_debit_fees"] = $fund_transfer_debit_fees;

    //for online payment for sender side
    $detl["ttlAccDebit_Online_payment"] = $ttlAccDebit_Online_payment;
    $detl["ttlAccDebit_Online_payment_count"] = $ttlAccDebit_Online_payment_count;
    $detl["ttlAccDebit_Online_payment_fees"] = $ttlAccDebit_Online_payment_fees;

    //online payment for receiver side
    $detl["ttlAccCredit_Online_payment"] = $ttlAccCredit_Online_payment;
    $detl["ttlAccCredit_Online_payment_count"] = $ttlAccCredit_Online_payment_count;
    $detl["ttlAccCredit_Online_payment_fees"] = $ttlAccCredit_Online_payment_fees;

    //for refund
    $detl["total_refund_credit"] = $total_refund_credit;
    $detl["total_refund_credit_count"] = $total_refund_credit_count;

    //for merchant withdraw
    $detl["merchant_widraw_total"] = $merchant_widraw_total;
    $detl["merchant_widraw_total_count"] = $merchant_widraw_total_count;
    $detl["merchant_widraw_total_fees"] = $merchant_widraw_total_fees;

    //for merchant withdraw sender side fees
    $detl["merchant_widraw_api_total_sender"] = $merchant_widraw_api_total_sender;
    $detl["merchant_widraw_api_total_sender_count"] = $merchant_widraw_api_total_sender_count;
    $detl["merchant_widraw_api_total_sender_fees"] = $merchant_widraw_api_total_sender_fees;

    $detl["ttlCrdtBankCnt"] = $ttlCrdtBankCnt;
    $detl["ttlCrdtBank"] = $ttlCrdtBank;
    $detl["Manual_deposit_fee"] = $Manual_deposit_fee;
    $detl["Manual_deposit_fee_count"] = $Manual_deposit_fee_count;
    $detl["manual_withdraw_fees"] = $manual_withdraw_fees;
    $detl["ttlCrdtCptCnt"] = $ttlCrdtCptCnt;
    $detl["ttlCrdtCpt"] = $ttlCrdtCpt;
    $detl["crypto_deposit_fee"] = $crypto_deposit_fee + $ttlOtherDebit_1_fees;
    $detl["crypto_deposit_fee_count"] = $crypto_deposit_fee_count;
    $detl["crypto_withdraw_fee_count"] = $crypto_withdraw_fee_count;
    $detl["ttlCrdtOzowCnt"] = $ttlCrdtOzowCnt;
    $detl["ttlCrdtOzow"] = $ttlCrdtOzow;
    $detl["ttlAgentCrtCnt"] = $ttlAgentCrtCount_1;
    $detl["ttlAgentCrt"] = $ttlAgentCrt_1;
//                $detl["ttlDepositCrtCnt"] = $ttlDepositCrtCount_1;
//                $detl["ttlDepositCrt"] = number_format($ttlDepositCrt_1, 2, '.', ',');

    $detl["ttlTrans"] = Count($transPDF);

    $detl["ttlCashDeposit"] = $ttlCashDeposit_1;

    $detl["W2W_admin_debit_amount"] = $W2W_admin_debit_amount;
    $detl["W2W_admin_debit_count"] = $W2W_admin_debit_count;
    $detl["W2W_admin_debit_fees"] = $W2W_admin_debit_fees;
    $detl["W2W_admin_debit_fees_count"] = $W2W_admin_debit_fees_count;
    $detl["W2W_admin_credit_fees"] = $W2W_admin_credit_fees;
    $detl["W2W_admin_credit_fees_count"] = $W2W_admin_credit_fees_count;
    $detl["ttlCashDeposit_1_fund_transfer"] = $ttlCashDeposit_1_fund_transfer;
    $detl["ttlCashDepositCount_1_fund_transfer_count"] = $ttlCashDepositCount_1_fund_transfer_count;
    $detl["ttlCashDeposit_1_fund_transfer_agent"] = $ttlCashDeposit_1_fund_transfer_agent;
    $detl["ttlCashDepositCount_1_fund_transfer_count_agent"] = $ttlCashDepositCount_1_fund_transfer_count_agent;
    $detl["W2W_admin_credit_fees_fund_transfer"] = $W2W_admin_credit_fees_fund_transfer;
    $detl["W2W_admin_credit_fees_count_fund_transfer"] = $W2W_admin_credit_fees_count_fund_transfer;
    $detl["W2W_admin_credit_fees_fund_transfer_agent"] = $W2W_admin_credit_fees_fund_transfer_agent;
    $detl["W2W_admin_credit_fees_count_fund_transfer_agent"] = $W2W_admin_credit_fees_count_fund_transfer_agent;
    
    $detl["Ozow_fees_count"] = $Ozow_fees_count;
    $detl["Ozow_fees"] = $Ozow_fees;


    $detl["ttlOtherDeposit"] = $ttlOtherDeposit_1;
    $detl["ttlOtherDepositCount"] = $ttlOtherDepositCount_1;
    $detl["ttlCashDepositCount"] = $ttlCashDepositCount_1;
    $detl["ttlAccountDeposit"] = $ttlAccountDeposit_1;
    $detl["ttlAccountDepositCount"] = $ttlAccountDepositCount_1;
    $detl["ttlAccountDebit"] = $ttlAccountDebit_1;
    $detl["ttlAccountDebitCount"] = $ttlAccountDebitCount_1;
    $detl["ttlAccDebit"] = $ttlAccDebit_1;
    $detl["ttlAccDebitCount"] = $ttlAccDebitCount_1;
    $detl["ttlCashDebit"] = $ttlCashDebit_1;
    $detl["Bank_Agent_dr_fees"] = $Bank_Agent_dr_fees;
    $detl["ttlCashDebitCount"] = $ttlCashDebitCount_1;
    $detl["ttlOtherDebit"] = $ttlOtherDebit_1;
    $detl["ttlOtherDebitCount"] = $ttlOtherDebitCount_1;
    $detl["ttlFee"] = $ttlFee;
    $detl["ttlFeeCount"] = $ttlFeeCount;
    $detl["user_id"] = $recordInfo->id;
    //10-May-2021 End
    view()->share(['transPDF' => $transPDF, 'detl' => $detl]);
    if (Count($transPDF) > 0) {
        $customPaper = array(0, 0, 720, 1440);
        $pdf = PDF::loadView('transactionPdf')->setPaper($customPaper, 'portrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        //$pdf = PDF::loadView('transactionPdf');
        if ($recordInfo->user_type == "Personal") {
            $user_name = $recordInfo->first_name . ' ' . $recordInfo->last_name;
        } else if ($recordInfo->user_type == "Business") {
            $user_name = $recordInfo->business_name;
        } else if ($recordInfo->user_type == "Agent" && $recordInfo->first_name == "") {
            $user_name = $recordInfo->business_name;
        } else if ($recordInfo->user_type == "Agent" && $recordInfo->first_name != "") {
            $user_name = $recordInfo->first_name . ' ' . $recordInfo->last_name;
        }
        return $pdf->download($user_name.'-report.pdf');
        // $bodyEmail = 'As per your request, please check attached statement for period ' . $mailToDate . ' to ' . $mailFromDate . '.';
        // $subjectEmail = 'e-Statement DafriBank - Digital Bank of Africa';
        // $data["email"] = 'vishnu.kumawat@nimbleappgenie.com';
        // $data["title"] = $subjectEmail;
        // $data["body"] = $bodyEmail;
        // $data["mailToDate"] = $mailFromDate;
        // $data["mailFromDate"] = $mailToDate;
        // $data["heading"] = 'Hey ' . $detl["name"] . ',';
        // Mail::send('statementMail', $data, function ($message)use ($data, $pdf) {
        //     $message->to($data["email"], $data["email"])
        //             ->subject($data["title"])
        //             ->attachData($pdf->output(), "statement.pdf");
        // });
        // Session::flash('success_message', "e-Statement successfully sent to your registered email.");
        // if ($recordInfo->user_type == "Personal") 
        // {
        //    return Redirect::to('admin/users');
        // } else if ($recordInfo->user_type == "Business") {
        //     return Redirect::to('admin/merchants');
        // } else if ($recordInfo->user_type == "Agent" && $recordInfo->first_name == "") {
        //     return Redirect::to('admin/merchants');
        // } else if ($recordInfo->user_type == "Agent" && $recordInfo->first_name != "") {
        //     return Redirect::to('admin/users');
        // }

    } else {
        if($is_currency_updated==1 && $to_date <= $updated_currency_date)
        {
        Session::flash('error_message', "You can not download statement as your currency was diffferent in the selected period.");
        }
        else{
        Session::flash('error_message', "Sorry no transaction found for given date range.");     
        }
    }

    if ($recordInfo->user_type == 'Personal') {
        return Redirect::to('admin/users');
    } else if ($recordInfo->user_type == 'Business') {
        return Redirect::to('admin/merchants');
    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name == "") {
        return Redirect::to('admin/merchants');
    } else if ($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "") {
        return Redirect::to('admin/users');
    }


  }
}

}

?>