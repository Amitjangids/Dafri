<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mail;
use Cookie;
use Session;
use Redirect;
use Input;
use Validator;
use DB;
use App\User;
use App\GetInTouch;
use App\Fee;
use App\Transaction;
use App\AgentsTransactionLimit;
use App\Agentlimit;
use App\Walletlimit;
use App\Models\Gig;
//use App\Models\User;
use App\Models\Myorder;
use App\Models\Ngnexchange;
use App\Models\WalletLimitUser;
use App\Formdba;
use App\Blog;
use App\Faq;
use App\Mail\SendMailable;
use App\WithdrawRequest;
use App\InactiveAmount;
use App\Notification;

class HomesController extends Controller {

    public function __construct() {
        parent::__construct();
    }
    
    public function index() {

        $pageTitle = 'Welcome';

        if (Session::has('user_id')) {
            return Redirect::to('/overview');
        }

        return view('homes.index', ['title' => $pageTitle]);
    }

    public function spend() {
        $pageTitle = 'Spend';
        return view('homes.spend', ['title' => $pageTitle]);
    }

    public function save() {
        $pageTitle = 'Save';
        return view('homes.save', ['title' => $pageTitle]);
    }

    public function budget() {
        $pageTitle = 'Budget';
        return view('homes.budget', ['title' => $pageTitle]);
    }

    public function borrow() {
        $pageTitle = 'Borrow';
        return view('homes.borrow', ['title' => $pageTitle]);
    }

    public function contact() {

        $pageTitle = 'Contact';
        $input = Input::all();
        if (!empty($input)) {

          //  print_r($input);  
           // die;

            $rules = array(
                'name' => 'required',
                //'subject' => 'required',
                'message' => 'required',
                'email' => 'required|email:filter',
            );
            $customMessages = [
                'name.required' => 'Help Description field can\'t be left blank',
                //'subject.required' => 'First name field can\'t be left blank',
                'message.required' => 'Last name field can\'t be left blank',
                'email.required' => 'Email field can\'t be left blank',
                'email.email' => 'Invalid Email',
            ];
            $validator = Validator::make($input, $rules, $customMessages);
            //$validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                $message = implode('<br>', $messages->all());

                Session::put('error_session_message', $message);
                return Redirect::to('contact')->withInput(Input::except('password'));
//                return Redirect::to('auth/help')->withErrors($validator)->withInput(Input::except('password')); 
            } else {
                $support = new GetInTouch([
                    'name' =>  $input['name'],
                    //'subject' => $input['subject'],
                    'message' => $input['message'],
                    'email' => $input['email'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $support->save();
            }
            Session::put('success_session_message', "The message has been received. Our team will get back to you shortly.");
            return Redirect::to('/contact');
        }
        return view('homes.contact', ['title' => $pageTitle]);
    }

    public function personalAccount() {

        $pageTitle = 'Personal Account';

        return view('homes.personalAccount', ['title' => $pageTitle]);
    }

    public function businessAccount() {

        $pageTitle = 'Business Account';
        $input = Input::all();

        return view('homes.businessAccount', ['title' => $pageTitle]);
    }

    public function about() {
        $pageTitle = 'About Dafribank';
        return view('homes.about', ['title' => $pageTitle]);
    }

    public function press() {
        $pageTitle = 'Press Release :: Dafribank';
        return view('homes.press', ['title' => $pageTitle]);
    }

    public function career() {
        $pageTitle = 'Career @ Dafribank';
        return view('homes.career', ['title' => $pageTitle]);
    }

    public function termsCondition() {
        $pageTitle = 'Terms & Consition';
        return view('homes.termsCondition', ['title' => $pageTitle]);
    }

    public function privacyPolicy() {
        $pageTitle = 'Privacy Policy';
        return view('homes.privacyPolicy', ['title' => $pageTitle]);
    }

    public function privateBanking() {
        $pageTitle = 'Private Banking';
        return view('homes.privateBanking', ['title' => $pageTitle]);
    }

    public function amlPolicy() {
        $pageTitle = 'AML Policy';
        return view('homes.amlPolicy', ['title' => $pageTitle]);
    }

    public function dafrixchange() {
        $pageTitle = 'DafriXchange';
        return view('homes.dafrixchange', ['title' => $pageTitle]);
    }

    public function merchatApi() {
        $pageTitle = 'Merchant API';
        return view('homes.merchatApi', ['title' => $pageTitle]);
    }

    public function dbaCurrency() {
        $pageTitle = 'DBA Currency';
        return view('homes.dbaCurrency', ['title' => $pageTitle]);
    }

    public function defiLoan() {
        $pageTitle = 'DeFi Loan';
        return view('homes.defiLoan', ['title' => $pageTitle]);
    }

    public function investorRelations() {
        $pageTitle = 'Investor Relations';
        return Redirect::to('/public/img/DAFRIBANK DIGITAL LTD VC PITCH DECK.pdf');
    }

    public function affiliate() {
        $pageTitle = 'Affiliate';
        return view('homes.affiliate', ['title' => $pageTitle]);
    }
    public function learningCenter() {
        $pageTitle = 'Learning-center';
        return view('homes.learning-center', ['title' => $pageTitle]);
    }

    public function faqs() {
        $pageTitle = 'Frequently asked questions @ Daribank';

        $faqs = Faq::orderBy('sort_order', 'ASC')->get();

        return view('homes.faqs', ['title' => $pageTitle, 'faqs' => $faqs]);
    }

    public function debitCards() {
        $pageTitle = 'Debit Cards';
        return view('homes.debitCards', ['title' => $pageTitle]);
    }

    public function cookieNotice() {
        $pageTitle = 'Cookies Policy';
        return view('homes.cookieNotice', ['title' => $pageTitle]);
    }

    public function products() {
        $pageTitle = 'What we Offer :: Products @ Dafribank';
        return view('homes.products', ['title' => $pageTitle]);
    }

    public function pressDetail($slug = null) {
        $pageTitle = 'Press Detail';
        return view('homes.' . $slug, ['title' => $pageTitle]);
    }

    public function blogs() {
        $pageTitle = 'Blogs @ Dafribank';
        $query = new Blog();
        $query = $query->leftJoin('categories','categories.id','blogs.category_id');
        $query = $query->sortable();

        $blogs = $query->orderBy("blogs.id", "DESC")->get(); 
        


        $getNews = Blog::leftJoin('categories','categories.id','blogs.category_id')->orderBy("blogs.id", "DESC")->where('category_id',3)->limit(4)->get(); 
        return view('homes.blogs', ['title' => $pageTitle, 'blogs' => $blogs,'getNews'=>$getNews]);
    }

    public function singleBlog($slug = null) {
// 	  echo $srchSlug = str_replace("-"," ",$slug);
        $blog = DB::table("blogs")->leftJoin('categories','categories.id','blogs.category_id')->select("blogs.*",'categories.name')->where("slug", $slug)->first();
// 	  echo '<pre>';print_R($blog);exit;
        $pageTitle = $blog->title;
        $date = date_create($blog->created_at);
        $blogDate = date_format($date, 'M d, Y');
        return view('homes.singleBlog', ['title' => $pageTitle, 'blog' => $blog, 'blogDate' => $blogDate]);
    }

    public function dbaApplication() {
        $pageTitle = 'DBA Application Form';

        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'fname' => 'required|max:20',
                'lname' => 'required',
                'contry' => 'required',
                'dob' => 'required',
                'age' => 'required',
                'addrs' => 'required',
                'phone' => 'required',
                'mobile' => 'required',
                'email' => 'required|email',
                'occupton' => 'required',
                'ttlBudgt' => 'required',
                'kinName' => 'required',
                'kinLname' => 'required',
                'kinCont' => 'required',
                'kinRelation' => 'required'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/dba-application')->withErrors($validator)->withInput(Input::except('password'));
            } else {
                $date = date_create($input['dob']);
                $dob = date_format($date, 'Y-m-d');
                $dba = new Formdba([
                    'first_name' => $input['fname'],
                    'last_name' => $input['lname'],
                    'country' => $input['contry'],
                    'dob' => $dob,
                    'age' => $input['age'],
                    'address' => $input['addrs'],
                    'phone' => $input['phone'],
                    'mobile' => $input['mobile'],
                    'email' => $input['email'],
                    'telegram_handle' => $input['telegram_handle'],
                    'alternative_number' => $input['alter_number'],
                    'occupation' => $input['occupton'],
                    'fund_source' => $input['fund_source'],
                    'total_budget' => $input['ttlBudgt'],
                    'kin_fname' => $input['kinName'],
                    'kin_lname' => $input['kinLname'],
                    'kin_contact' => $input['kinCont'],
                    'kin_relation' => $input['kinRelation'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $dba->save();

                $adminEmailId = "backendteam@nimbleappgenie.com";
                $emailTemplate = DB::table('emailtemplates')->where('id', 28)->first();
                $toRepArray = array('[!fname!]', '[!lname!]', '[!country!]', '[!dob!]', '[!age!]', '[!address!]', '[!phone!]', '[!mobile!]', '[!email!]', '[!telegram_handle!]', '[!alternative_number!]', '[!occupation!]', '[!fund_source!]', '[!ttl_budget!]', '[!kin_fname!]', '[!kin_lname!]', '[!kin_contact!]', '[!kin_relation!]');
                $fromRepArray = array($input['fname'], $input['lname'], $input['contry'], $input['dob'], $input['age'], $input['addrs'], $input['phone'], $input['mobile'], $input['email'], $input['telegram_handle'], $input['alter_number'], $input['occupton'], $input['fund_source'], $input['ttlBudgt'], $input['kinName'], $input['kinLname'], $input['kinCont'], $input['kinRelation']);
                $emailSubject = $emailTemplate->subject;
                $emailBody = str_replace($toRepArray, $fromRepArray, $emailTemplate->template);

                Mail::to($adminEmailId)->send(new SendMailable($emailBody, $emailSubject));

                $clientMsg = 'Hi ' . $input['fname'] . ',<br><br>Your DBA Application has been submitted and sent to Dafribank Team.<br>Team will contact you soon.';
                $clientSubject = "DBA Application Request";
                Mail::to($input['email'])->send(new SendMailable($clientMsg, $clientSubject));

                Session::flash('success_message', "Your DBA Application saved successfully, we will contact you soon.");
                return Redirect::to('/dba-application');
            }
        }


        return view('homes.dbaApplication', ['title' => $pageTitle]);
    }

    public function onLinePaymentcharges(Request $request) {
        // $input = Input::all();
        //     print_r($input);die;
        $order_id = $request->get('order_id');
        $order_amount = $request->get('order_amount');
        $merchant_id = $request->get('merchant_key');
        $url = $request->get('return_url');
        $currency_code = $request->get('currency_code');
//

        $base64Req = base64_encode($order_id . '###' . $order_amount . '###' . $merchant_id . '###' . $currency_code . '###' . $url);

        Session::put('reqStr', $base64Req);
        return redirect::to('online-payment');
    }

    public function setOnlinePayment($reqStr) {

//        $order_id = $request->get('order_id');
//        $order_amount = $request->get('order_amount');
//        $merchant_id = $request->get('user_id');
//        $url = $request->get('url');
//
        $rt = base64_decode($reqStr);
        echo $rt;
        die;
//        $base64Req = base64_encode($order_id . '###' . $order_amount . '###' . $merchant_id . '###' . $url);
        Session::put('reqStr', $reqStr);
        return redirect::to('online-payment');
    }

    public function otpScreen() {

        $pageTitle = 'OTP Screen';

        return view('homes.otpsceen', ['title' => $pageTitle]);
    }

    public function userCheck(Request $request) {
        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'email' => 'required|email',
                'password' => 'required'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                print_r($messages);
                die;
                $message = implode('<br>', $messages->all());
            } else {
                
            }
        }
    }

    public function sendEmailOTP() {
        
    }

    public function onLinePaymentchargesEpay(Request $request) {
        $order_amount = $request->get('order_amount');
        $merchant_id = $request->get('merchant_key');
        $slug = $request->get('slug');
        $currency_code = $request->get('currency_code');
        $base64Req = base64_encode($order_amount . '###' . $merchant_id . '###' . $currency_code . '###' . $slug);
        Session::put('epaymesession', $base64Req);
        return redirect::to('epayme');
    }

    public function onLinePaymentchargesEpayMerchant(Request $request) {
        $merchant_id = $request->get('merchant_key');
        $slug = $request->get('slug');
        $currency_code = $request->get('currency_code');
        $base64Req = base64_encode($merchant_id . '###' . $currency_code . '###' . $slug);
        Session::put('epaymemerchantsession', $base64Req);
        return redirect::to('epayme_merchant');
    }


    public function epayme() {
         $pageTitle = 'ePay Me';
         $req = base64_decode(Session::get('epaymesession'));
         $reqArr = explode('###', $req);
         $order_amount = $reqArr[0];
         $merchant_id = base64_decode($reqArr[1]);
         $currency_code = $reqArr[2];
         $slug =$reqArr[3]  ;
         $user= User::where('id',$merchant_id)->first();  
         $input = Input::all();
         if (!empty($input)) {
            $rules = array(
                'email' => 'required|email',
                'password' => 'required'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                $message = implode('<br>', $messages->all());
                Session::put('error_session_message', $message);
                return Redirect::to('/epayme')->withInput(Input::except('password'));
            } 
            else 
            {
                $userInfo = User::where('email', $input['email'])->first();
                if(empty($userInfo))
                {
                Session::put('error_session_message','Please provide a valid login detail');
                return Redirect::to('/epayme')->withInput(Input::except('password'));   
                }
                if ($user->id == $userInfo->id) {
                    Session::put('error_session_message', "Sender & Receiver can't be same.");
                    return Redirect::to('/epayme');
                }

                if (!empty($userInfo)) {
                    if (password_verify($input['password'], $userInfo->password)) {
                        if ($userInfo->otp_verify == 1 && $userInfo->is_verify == 1) {
                                $otp_codee = $this->generateNumericOTP(6);
                                $ty = User::where('id', $userInfo->id)->update([
                                    'verify_code' => $this->encpassword($otp_codee),
                                    'otp_time' => date('Y-m-d H:i:s')
                                ]);
                                //$this->sendEmailOTP($otp_codee); 
                                if ($userInfo->user_type == 'Personal') {
                                    $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                                } 
                                else if ($userInfo->user_type == 'Business') {
                                    if($userInfo->first_name!="")
                                    {
                                    $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);   
                                    }
                                    else{
                                    $uname = strtoupper($userInfo->business_name);
                                    }
                                } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name != "") {
                                    $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                                } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name == "") {
                                    $uname = strtoupper($userInfo->business_name);
                                }
                                $emailId = $userInfo->email;
                                $emailSubject = 'OTP For Payment';
                                $emailData['subjects'] = $emailSubject;
                                $emailData['emailId'] = $userInfo->email;
                                $emailData['otp'] = $otp_codee;
                                $emailData['userName'] = $uname;
                               // print_r($emailData); die; 
                                $ty = Mail::send('emails.paymentOTP', $emailData, function ($message)use ($emailData, $emailId) {
                                            $message->to($emailId, $emailId)
                                                    ->subject($emailData['subjects']);
                                        });
                                 Session::put('user_id', $userInfo->id);       
                                return redirect::to('/confirm-epayme-payment');

                        } else if ($userInfo->otp_verify == 0 || $userInfo->is_verify == 0) {
                            $error = 'Your account might have been temporarily disabled. Please contact us for more details.';
                        }
                    } else {
                        $error = 'Invalid email or password.';
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
                Session::put('error_session_message', $error);
                return Redirect::to('/epayme')->withInput(Input::except('password'));
            }

         }

         return view('homes.epayme', ['title' => $pageTitle,'order_amount'=>$order_amount,'currency_code'=>$currency_code,'user'=>$user]);
    }


    public function epaymeMerchant() {
        $pageTitle = 'ePay Merchant';
        $req = base64_decode(Session::get('epaymemerchantsession'));
        $reqArr = explode('###', $req);
        $merchant_id = base64_decode($reqArr[0]);
        $currency_code = $reqArr[1];
        $slug =$reqArr[2]  ;
        $user= User::where('id',$merchant_id)->first();  
        $input = Input::all();
        if (!empty($input)) {
           $rules = array(
               'amount' => 'required',
               'email' => 'required|email',
               'password' => 'required'
           );
           $validator = Validator::make($input, $rules);
           if ($validator->fails()) {
               $messages = $validator->messages();
               $message = implode('<br>', $messages->all());
               Session::put('error_session_message', $message);
               return Redirect::to('/epayme_merchant')->withInput(Input::except('password'));
           } 
           else 
           {
               $userInfo = User::where('email', $input['email'])->first();
               if(empty($userInfo))
               {
               Session::put('error_session_message','Please provide a valid login detail');
               return Redirect::to('/epayme_merchant')->withInput(Input::except('password'));   
               }
               if ($user->id == $userInfo->id) {
                   Session::put('error_session_message', "Sender & Receiver can't be same.");
                   return Redirect::to('/epayme_merchant');
               }

               $amount = $input['amount'];
               $chkAmount = $this->fetchCurrencyRate($user->currency, $amount);
               if ($chkAmount < 5) {
                $user_currncy_250 = $this->myCurrencyRate1($user->currency, 5);
                $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                Session::put('error_session_message', "You can't send less than " . $user->currency . " " . $user_currncy_250 . ".");
                return Redirect::to('epayme_merchant');
            }

               if (!empty($userInfo)) {
                   if (password_verify($input['password'], $userInfo->password)) {
                       if ($userInfo->otp_verify == 1 && $userInfo->is_verify == 1) {
                               $otp_codee = $this->generateNumericOTP(6);
                               $ty = User::where('id', $userInfo->id)->update([
                                   'verify_code' => $this->encpassword($otp_codee),
                                   'otp_time' => date('Y-m-d H:i:s')
                               ]);
                               //$this->sendEmailOTP($otp_codee); 
                               if ($userInfo->user_type == 'Personal') {
                                   $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                               } 
                               else if ($userInfo->user_type == 'Business') {
                                   if($userInfo->first_name!="")
                                   {
                                   $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);   
                                   }
                                   else{
                                   $uname = strtoupper($userInfo->business_name);
                                   }
                               } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name != "") {
                                   $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                               } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name == "") {
                                   $uname = strtoupper($userInfo->business_name);
                               }
                               $emailId = $userInfo->email;
                               $emailSubject = 'OTP For Payment';
                               $emailData['subjects'] = $emailSubject;
                               $emailData['emailId'] = $userInfo->email;
                               $emailData['otp'] = $otp_codee;
                               $emailData['userName'] = $uname;
                              // print_r($emailData); die; 
                               $ty = Mail::send('emails.paymentOTP', $emailData, function ($message)use ($emailData, $emailId) {
                                           $message->to($emailId, $emailId)
                                                   ->subject($emailData['subjects']);
                                       });
                                Session::put('user_id', $userInfo->id);    
                                Session::put('epayme_merchant_amount', $amount);   
                               return redirect::to('/confirm-epayme-merchant-payment');

                       } else if ($userInfo->otp_verify == 0 || $userInfo->is_verify == 0) {
                           $error = 'Your account might have been temporarily disabled. Please contact us for more details.';
                       }
                   } else {
                       $error = 'Invalid email or password.';
                   }
               } else {
                   $error = 'Invalid email or password.';
               }
               Session::put('error_session_message', $error);
               return Redirect::to('/epayme_merchant')->withInput(Input::except('password'));
           }

        }

        return view('homes.merchant_epayme', ['title' => $pageTitle,'currency_code'=>$currency_code,'user'=>$user]);
   }

   public function confirmEpaymeMerchantPayment(Request $request) {
        
    $pageTitle = 'Confirm ePay Me Merchant Payment';
    $req = base64_decode(Session::get('epaymemerchantsession'));
    $order_amount = Session::get('epayme_merchant_amount');
    $reqArr = explode('###', $req);
    $merchant_id = base64_decode($reqArr[0]);
    $currency_code = $reqArr[1];
    $slug =$reqArr[2];
    $user_id=Session::get('user_id');
    $user= User::where('id',$merchant_id)->first();  
    $userInfo=User::where('id',$user_id)->first(); 
    $input = Input::all();
    if (!empty($input)) {

        global $currencyList;
        if(!in_array($currency_code,$currencyList))
        {
        Session::put('error_session_message', "Payment amount currency is not supported.");
        return Redirect::to('/epayme');
        }

        if(!isset($currency_code))
        {
            Session::put('error_session_message', "Payment amount currency is not found.");
            return Redirect::to('/epayme');  
        }

        if ($input['otp_code'] == '' || $input['otp_code1'] == '' || $input['otp_code2'] == '' || $input['otp_code3'] == '' || $input['otp_code4'] == '' || $input['otp_code5'] == '') {
            Session::put('error_session_message', 'You have entered invalid verification code.');
            return Redirect::to('/confirm-epayme-payment');
        } else {
            $otp_code = $input['otp_code'] . $input['otp_code1'] . $input['otp_code2'] . $input['otp_code3'] . $input['otp_code4'] . $input['otp_code5'];

            $dateChk = date('Y-m-d H:i', strtotime('+15 minutes', strtotime($userInfo->otp_time)));

            if (!password_verify($otp_code, $userInfo->verify_code) || date('Y-m-d H:i') > $dateChk) {
                Session::put('error_session_message', 'You have entered invalid verification code.');
                return Redirect::to('/confirm-epayme-merchant-payment');
            } else {

                if ($userInfo->is_kyc_done != 1 or $userInfo->is_verify != 1) { 
                    $chkAmount = $this->fetchCurrencyRate($currency_code, $order_amount);
                    if ($chkAmount > TRANS_LIMIT_BEFORE_KYC && $userInfo->currency != 'USD') {
                        $user_currncy_250 = $this->myCurrencyRate1($userInfo->currency, TRANS_LIMIT_BEFORE_KYC);
                        $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                        Session::put('error_session_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please upload your KYC.");
                        return Redirect::to('epayme_merchant');
                    }

                    $TotalWithdraw = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', 0)->where('trans_for', 'LIKE', 'Withdraw%')->whereIn('status', array(1, 2))->sum('amount');

                    $TotalDebitTrans = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', '!=', 0)->where('trans_type', 2)->where('status', 1)->sum('amount');

                    $TotalInactvAmnt = InactiveAmount::where('user_id', Session::get('user_id'))->sum('amount');

                    $ttlWithdrawAmount = $TotalWithdraw + $TotalDebitTrans + $TotalInactvAmnt + $order_amount;
                    $chkAmount = $this->fetchCurrencyRate($currency_code, $ttlWithdrawAmount);
                    if ($chkAmount >= TRANS_LIMIT_BEFORE_KYC) {
                        $user_currncy_250 = $this->myCurrencyRate1($userInfo->currency, TRANS_LIMIT_BEFORE_KYC);
                        $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                        Session::put('error_session_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please upload your KYC.");
                        return Redirect::to('epayme_merchant');
                    }
                }

                $transLimitFlag = $this->checkUserTransLimit(Session::get('user_id'), $userInfo->account_category, $userInfo->user_type, $order_amount);
                $transLimitArr = explode("###", $transLimitFlag);
                if ($transLimitArr[0] == 'false' or $transLimitArr[0] == false) {
                    Session::put('error_session_message', $transLimitArr[1]);
                    return Redirect::to('epayme_merchant');
                }

                $conversion_fees=0;
                if($currency_code == $userInfo->currency)
                { 
                $order_amount_in_currency = $order_amount;
                $conversion_rate=0;
                }
                else{   
                $amount_user_currency = $this->myCurrencyRate($currency_code, $userInfo->currency, $order_amount);
                $converArr = explode("###", $amount_user_currency);
                $conversion_rate=$converArr[0];
                $order_amount_in_currency=$converArr[1];
                } 

                if ($userInfo->wallet_amount > $order_amount_in_currency) {

                    if ($userInfo->user_type == 'Personal' || ($userInfo->user_type == 'Agent' && $userInfo->first_name != '')) {
                        $payeeName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);
                        $mail_dashboard_lnk_sender = HTTP_PATH . '/personal-login';
                        if ($userInfo->account_category == "Silver") {
                            $fee_name = 'EPAY_PAYMENT_SENDER';
                        } else if ($userInfo->account_category == "Gold") {
                            $fee_name = 'EPAY_PAYMENT_SENDER_GOLD';
                        } else if ($userInfo->account_category == "Platinum") {
                            $fee_name = 'EPAY_PAYMENT_SENDER_PLATINUM';
                        } else if ($userInfo->account_category == "Private Wealth") {
                            $fee_name = 'EPAY_PAYMENT_SENDER_PRIVATE_WEALTH';
                        } else {
                            $fee_name = 'EPAY_PAYMENT_SENDER';
                        }
                     $convr_fee_name = $userInfo->currency!="NGN" &&  $currency_code!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';
                    }
                    else if ($userInfo->user_type == 'Business' || ($userInfo->user_type == 'Agent' && $userInfo->first_name == '')) {
                        $payeeName = $userInfo->business_name;
                        $mail_dashboard_lnk_sender = HTTP_PATH . '/business-login';
                        if ($userInfo->account_category == "Gold") {
                            $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_GOLD';
                        } else if ($userInfo->account_category == "Platinum") {
                            $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_PLATINUM';
                        } else if ($userInfo->account_category == "Enterprises") {
                            $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_Enterpris';
                        } else {
                            $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_GOLD';
                        }
                        $convr_fee_name = $userInfo->currency!="NGN" && $currency_code!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';
                    }

                    $fees = Fee::where('fee_name', $fee_name)->first();
                    $fees_amount =  $order_amount_in_currency * $fees->fee_value / 100;
                    if($currency_code != $userInfo->currency)
                    { 
                        $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                        $conversion_feess = $fees_convr->fee_value;
                        $conversion_fees = ($order_amount_in_currency * $conversion_feess) / 100;
                    }

                    $total_sender_fees=$conversion_fees+$fees_amount;
                    $total_debited_amount_sender=$order_amount_in_currency+$total_sender_fees;
                    if ($userInfo->wallet_amount < $total_debited_amount_sender) 
                    {
                    Session::put('error_session_message', "The amount in your account can't cover " . $userInfo->currency . ' ' . $total_sender_fees . " fee for this transaction. Please try again with different amount.");
                    return Redirect::to('epayme_merchant');
                    }    

                    if ($user->user_type == 'Personal' || ($user->user_type == 'Agent' && $user->first_name != '')) {
                        $receiverName = strtoupper($user->first_name . " " . $user->last_name);
                        $mail_dashboard_lnk_receiver = HTTP_PATH . '/personal-login';
                        if ($user->account_category == "Silver") {
                            $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER';
                        } else if ($user->account_category == "Gold") {
                            $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER_GOLD';
                        } else if ($user->account_category == "Platinum") {
                            $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER_PLATINUM';
                        } else if ($user->account_category == "Private Wealth") {
                            $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER_PRIVATE_WEALTH';
                        } else {
                            $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER';
                        }
                    } else if ($user->user_type == 'Business' || ($user->user_type == 'Agent' && $user->first_name == '')) {
                        $receiverName = $user->business_name;
                        $mail_dashboard_lnk_receiver = HTTP_PATH . '/business-login';
                        if ($user->account_category == "Gold") {
                            $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_GOLD';
                        } else if ($user->account_category == "Platinum") {
                            $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_PLATINUM';
                        } else if ($user->account_category == "Enterprises") {
                            $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_Enterpr';
                        } else {
                            $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_GOLD';
                        }
                      }

                      $fees_receiver = Fee::where('fee_name', $fee_name_receiver)->first();
                      $fees_amount_receiver = $order_amount * $fees_receiver->fee_value / 100;
                      $total_credited_amount_receiver=$order_amount-$fees_amount_receiver;
                     
                      if ($conversion_fees != 0) {
                      $billing_description = 'IP:' . $this->get_client_ip() . '##Amount ' . $currency_code . ' ' . $order_amount . ' and Conversion rate ' . $conversion_rate .'=' .$userInfo->currency .' '.$order_amount_in_currency."##Conversion Fees = " . $userInfo->currency . ' ' . $conversion_fees . "##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver;
                      }
                      else
                      {
                      $billing_description = 'IP:' . $this->get_client_ip()."##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver;
                      }

                      $payee_wallet = $userInfo->wallet_amount - $total_debited_amount_sender;


                      $merchant_wallet = $user->wallet_amount + $total_credited_amount_receiver;

                      $refrence_id = time() . rand() . Session::get('user_id');
                      $trans = new Transaction([
                          "user_id" => $userInfo->id,
                          "receiver_id" => $user->id,
                          "amount" => $order_amount,
                          "fees" => $fees_amount_receiver,
                          "receiver_fees" => $fees_amount_receiver,
                          "sender_fees" => $total_sender_fees,
                          "sender_currency" => $userInfo->currency,
                          "receiver_currency" => $user->currency,
                          "currency" => $currency_code,
                          "trans_type" => 2, //Debit-Withdraw
                          "trans_to" => 'Dafri_Wallet',
                          "trans_for" => 'EPAY MERCHANT', 
                          "refrence_id" => $refrence_id,
                          "user_close_bal" => $payee_wallet,
                          "receiver_close_bal" => $merchant_wallet,
                          "real_value" =>$total_credited_amount_receiver,
                          "sender_real_value" =>$total_debited_amount_sender,
                          "billing_description" => $billing_description,
                          "status" => 1,
                          "created_at" => date('Y-m-d H:i:s'),
                          "updated_at" => date('Y-m-d H:i:s'),
                      ]);

                      $trans->save();

                      User::where('id', $userInfo->id)->update(['wallet_amount' => $payee_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                      User::where('id', $user->id)->update(['wallet_amount' => $merchant_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                      //admin fees from sender side
                      $amount_admin_currency = $this->convertCurrency($userInfo->currency,'USD', ($total_sender_fees));
                      $amount_admin_currencyArr = explode("##", $amount_admin_currency);
                      $admin_amount = $amount_admin_currencyArr[0];

                      //admin fees from receiver side
                      $amount_admin_currency1 = $this->convertCurrency($user->currency,'USD',($fees_amount_receiver));
                      $amount_admin_currencyArr1 = explode("##", $amount_admin_currency1);
                      $admin_amount1 = $amount_admin_currencyArr1[0];

                      $admin_amount = $admin_amount + $admin_amount1;
                      $adminInfo = User::where('id', 1)->first();
                      $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);

                      User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

                        $TransId = $trans->id;
                        //Receiver Mail Start
                        $emailId = $user->email;
                        $mailAmount = number_format($order_amount, 2, '.', ',');
                        $mailAmnt = explode(".", $mailAmount);
                        $emailSubject = "DafriBank Digital | Account has been credited with " . $currency_code. " " . $order_amount;
                        $emailData['subjects'] = $emailSubject;
                        $emailData['emailId'] = $emailId;
                        $emailData['mailAmnt'] = $mailAmnt;
                        $emailData['mailAmount'] = $order_amount;
                        $emailData['currency'] = $currency_code;
                        $emailData['receiver_currency'] = $user->currency;
                        $emailData['TransId'] = $TransId;
                        $emailData['fees_amount_receiver'] = $fees_amount_receiver;
                        $emailData['refrence_id'] = $refrence_id;
                        $emailData['receiverName'] = $receiverName;
                        $emailData['payeeName'] = $payeeName;
                        $emailData['mail_dashboard_lnk'] = $mail_dashboard_lnk_receiver;
                        Mail::send('emails.epayPaymentCredit', $emailData, function ($message)use ($emailData, $emailId) {
                            $message->to($emailId, $emailId)
                                    ->subject($emailData['subjects']);
                        });

                        
                        $notif = new Notification([
                            'user_id' => $user->id,
                            'notif_subj' => $emailSubject,
                            'notif_body' => $emailSubject,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $notif->save();

                        $emailId = $userInfo->email;
                        $emailData['fees_amount'] = $total_sender_fees;
                        $emailSubject = "DafriBank Digital | Account has been debited with " . $userInfo->currency . " " . $total_debited_amount_sender;
                        $emailData['mailAmnt'] = $order_amount;
                        $emailData['currency'] = $user->currency;
                        $emailData['subjects'] = $emailSubject;
                        $emailData['sender_currency'] = $userInfo->currency;
                        Mail::send('emails.epayPaymentDebit', $emailData, function ($message)use ($emailData, $emailId) {
                            $message->to($emailId, $emailId)
                                    ->subject($emailData['subjects']);
                        });

                        $notif = new Notification([
                            'user_id' => $userInfo->id,
                            'notif_subj' => $emailSubject,
                            'notif_body' => $emailSubject,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $notif->save();

                        Session::put('user_id', $userInfo->id);
                        Session::put('user_name', strtoupper($userInfo->first_name));
                        Session::put('email', $userInfo->email);
                        $b64TransID = base64_encode($TransId);
                        $b64RefID = base64_encode($refrence_id);
                        $base64Result = base64_encode($TransId . '###Success');
                        return Redirect::to('epay-payment-successfull');
                }
                else {
                    Session::put('error_session_message', 'Insufficient Balance!'); 
                    return Redirect::to('/epayme_merchant');
                }

            }
       }

    }

    return view('homes.confirmEpaymeMerchantPayment', ['title' => $pageTitle,'order_amount' => $order_amount, 'user_id' => $user_id,'user' => $user, 'userInfo' => $userInfo, 'currency_code' => $currency_code]);

}


    public function confirmEpaymePayment(Request $request) {
        
        $pageTitle = 'Confirm ePay Me Payment';
        $req = base64_decode(Session::get('epaymesession'));
        $reqArr = explode('###', $req);
        $order_amount = $reqArr[0];
        $merchant_id = base64_decode($reqArr[1]);
        $currency_code = $reqArr[2];
        $slug =$reqArr[3];
        $user_id=Session::get('user_id');
        $user= User::where('id',$merchant_id)->first(); 
        $userInfo=User::where('id',$user_id)->first(); 
        $input = Input::all();
        if (!empty($input)) {

            global $currencyList;
            if(!in_array($currency_code,$currencyList))
            {
            Session::put('error_session_message', "Payment amount currency is not supported.");
            return Redirect::to('/epayme');
            }

            if(!isset($currency_code))
            {
                Session::put('error_session_message', "Payment amount currency is not found.");
                return Redirect::to('/epayme');  
            }

            if ($input['otp_code'] == '' || $input['otp_code1'] == '' || $input['otp_code2'] == '' || $input['otp_code3'] == '' || $input['otp_code4'] == '' || $input['otp_code5'] == '') {
                Session::put('error_session_message', 'You have entered invalid verification code.');
                return Redirect::to('/confirm-epayme-payment');
            } else {
                $otp_code = $input['otp_code'] . $input['otp_code1'] . $input['otp_code2'] . $input['otp_code3'] . $input['otp_code4'] . $input['otp_code5'];

                $dateChk = date('Y-m-d H:i', strtotime('+15 minutes', strtotime($userInfo->otp_time)));

                if (!password_verify($otp_code, $userInfo->verify_code) || date('Y-m-d H:i') > $dateChk) {
                    Session::put('error_session_message', 'You have entered invalid verification code.');
                    return Redirect::to('/confirm-epayme-payment');
                } else {

                    if ($userInfo->is_kyc_done != 1 or $userInfo->is_verify != 1) { 
                        $chkAmount = $this->fetchCurrencyRate($currency_code, $order_amount);
                        if ($chkAmount > TRANS_LIMIT_BEFORE_KYC && $userInfo->currency != 'USD') {
                            $user_currncy_250 = $this->myCurrencyRate1($userInfo->currency, TRANS_LIMIT_BEFORE_KYC);
                            $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                            Session::put('error_session_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please upload your KYC.");
                            return Redirect::to('epayme');
                        }
    
                        $TotalWithdraw = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', 0)->where('trans_for', 'LIKE', 'Withdraw%')->whereIn('status', array(1, 2))->sum('amount');
    
                        $TotalDebitTrans = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', '!=', 0)->where('trans_type', 2)->where('status', 1)->sum('amount');

                        $TotalInactvAmnt = InactiveAmount::where('user_id', Session::get('user_id'))->sum('amount');

                        $ttlWithdrawAmount = $TotalWithdraw + $TotalDebitTrans + $TotalInactvAmnt + $order_amount;
                        $chkAmount = $this->fetchCurrencyRate($userInfo->currency, $ttlWithdrawAmount); 
                        if ($chkAmount >= TRANS_LIMIT_BEFORE_KYC) {
                            $user_currncy_250 = $this->myCurrencyRate1($userInfo->currency, TRANS_LIMIT_BEFORE_KYC);
                            $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                            Session::put('error_session_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please upload your KYC.");
                            return Redirect::to('epayme');
                        }
                    }

                    $transLimitFlag = $this->checkUserTransLimit(Session::get('user_id'), $userInfo->account_category, $userInfo->user_type, $order_amount);
                    $transLimitArr = explode("###", $transLimitFlag);
                    if ($transLimitArr[0] == 'false' or $transLimitArr[0] == false) {
                        Session::put('error_session_message', $transLimitArr[1]);
                        return Redirect::to('epayme');
                    }

                    $conversion_fees=0;
                    if($currency_code == $userInfo->currency)
                    { 
                    $order_amount_in_currency = $order_amount;
                    $conversion_rate=0;
                    }
                    else{   
                    $amount_user_currency = $this->myCurrencyRate($currency_code, $userInfo->currency, $order_amount);
                    $converArr = explode("###", $amount_user_currency);
                    $conversion_rate=$converArr[0];
                    $order_amount_in_currency=$converArr[1];
                    } 

                    if ($userInfo->wallet_amount > $order_amount_in_currency) {

                        if ($userInfo->user_type == 'Personal' || ($userInfo->user_type == 'Agent' && $userInfo->first_name != '')) {
                            $payeeName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);
                            $mail_dashboard_lnk_sender = HTTP_PATH . '/personal-login';
                            if ($userInfo->account_category == "Silver") {
                                $fee_name = 'EPAY_PAYMENT_SENDER';
                            } else if ($userInfo->account_category == "Gold") {
                                $fee_name = 'EPAY_PAYMENT_SENDER_GOLD';
                            } else if ($userInfo->account_category == "Platinum") {
                                $fee_name = 'EPAY_PAYMENT_SENDER_PLATINUM';
                            } else if ($userInfo->account_category == "Private Wealth") {
                                $fee_name = 'EPAY_PAYMENT_SENDER_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'EPAY_PAYMENT_SENDER';
                            }
                         $convr_fee_name = $userInfo->currency!="NGN" &&  $currency_code!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';
                        }
                        else if ($userInfo->user_type == 'Business' || ($userInfo->user_type == 'Agent' && $userInfo->first_name == '')) {
                            $payeeName = $userInfo->business_name;
                            $mail_dashboard_lnk_sender = HTTP_PATH . '/business-login';
                            if ($userInfo->account_category == "Gold") {
                                $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_GOLD';
                            } else if ($userInfo->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_PLATINUM';
                            } else if ($userInfo->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_Enterpris';
                            } else {
                                $fee_name = 'MERCHANT_EPAY_PAYMENT_SENDER_GOLD';
                            }
                            $convr_fee_name = $userInfo->currency!="NGN" && $currency_code!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';
                        }

                        $fees = Fee::where('fee_name', $fee_name)->first();
                        $fees_amount =  $order_amount_in_currency * $fees->fee_value / 100;
                        if($currency_code != $userInfo->currency)
                        { 
                            $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                            $conversion_feess = $fees_convr->fee_value;
                            $conversion_fees = ($order_amount_in_currency * $conversion_feess) / 100;
                        }

                        $total_sender_fees=$conversion_fees+$fees_amount;
                        $total_debited_amount_sender=$order_amount_in_currency+$total_sender_fees;
                        if ($userInfo->wallet_amount < $total_debited_amount_sender) 
                        {
                        Session::put('error_session_message', "The amount in your account can't cover " . $userInfo->currency . ' ' . $total_sender_fees . " fee for this transaction. Please try again with different amount.");
                        return Redirect::to('epayme');
                        }    

                        if ($user->user_type == 'Personal' || ($user->user_type == 'Agent' && $user->first_name != '')) {
                            $receiverName = strtoupper($user->first_name . " " . $user->last_name);
                            $mail_dashboard_lnk_receiver = HTTP_PATH . '/personal-login';
                            if ($user->account_category == "Silver") {
                                $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER';
                            } else if ($user->account_category == "Gold") {
                                $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER_PLATINUM';
                            } else if ($user->account_category == "Private Wealth") {
                                $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER_PRIVATE_WEALTH';
                            } else {
                                $fee_name_receiver = 'EPAY_PAYMENT_RECEIVER';
                            }
                        } else if ($user->user_type == 'Business' || ($user->user_type == 'Agent' && $user->first_name == '')) {
                            $receiverName = $user->business_name;
                            $mail_dashboard_lnk_receiver = HTTP_PATH . '/business-login';
                            if ($user->account_category == "Gold") {
                                $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_PLATINUM';
                            } else if ($user->account_category == "Enterprises") {
                                $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_Enterpr';
                            } else {
                                $fee_name_receiver = 'MERCHANT_EPAY_PAYMENT_RECEIVER_GOLD';
                            }
                          }

                          $fees_receiver = Fee::where('fee_name', $fee_name_receiver)->first();
                          $fees_amount_receiver = $order_amount * $fees_receiver->fee_value / 100;
                          $total_credited_amount_receiver=$order_amount-$fees_amount_receiver;
                         
                          if ($conversion_fees != 0) {
                          $billing_description = 'IP:' . $this->get_client_ip() . '##Amount ' . $currency_code . ' ' . $order_amount . ' and Conversion rate ' . $conversion_rate .'=' .$userInfo->currency .' '.$order_amount_in_currency."##Conversion Fees = " . $userInfo->currency . ' ' . $conversion_fees . "##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver;
                          }
                          else
                          {
                          $billing_description = 'IP:' . $this->get_client_ip()."##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver;
                          }

                          $payee_wallet = $userInfo->wallet_amount - $total_debited_amount_sender;


                          $merchant_wallet = $user->wallet_amount + $total_credited_amount_receiver;

                          $refrence_id = time() . rand() . Session::get('user_id');
                          $trans = new Transaction([
                              "user_id" => $userInfo->id,
                              "receiver_id" => $user->id,
                              "amount" => $order_amount,
                              "fees" => $fees_amount_receiver,
                              "receiver_fees" => $fees_amount_receiver,
                              "sender_fees" => $total_sender_fees,
                              "sender_currency" => $userInfo->currency,
                              "receiver_currency" => $user->currency,
                              "currency" => $currency_code,
                              "trans_type" => 2, //Debit-Withdraw
                              "trans_to" => 'Dafri_Wallet',
                              "trans_for" => 'EPAY ME', 
                              "refrence_id" => $refrence_id,
                              "user_close_bal" => $payee_wallet,
                              "receiver_close_bal" => $merchant_wallet,
                              "real_value" =>$total_credited_amount_receiver,
                              "sender_real_value" =>$total_debited_amount_sender,
                              "billing_description" => $billing_description,
                              "status" => 1,
                              "created_at" => date('Y-m-d H:i:s'),
                              "updated_at" => date('Y-m-d H:i:s'),
                          ]);

                          $trans->save();

                          User::where('id', $userInfo->id)->update(['wallet_amount' => $payee_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                          User::where('id', $user->id)->update(['wallet_amount' => $merchant_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                          //admin fees from sender side
                          $amount_admin_currency = $this->convertCurrency($userInfo->currency,'USD', ($total_sender_fees));
                          $amount_admin_currencyArr = explode("##", $amount_admin_currency);
                          $admin_amount = $amount_admin_currencyArr[0];

                          //admin fees from receiver side
                          $amount_admin_currency1 = $this->convertCurrency($user->currency,'USD',($fees_amount_receiver));
                          $amount_admin_currencyArr1 = explode("##", $amount_admin_currency1);
                          $admin_amount1 = $amount_admin_currencyArr1[0];

                          $admin_amount = $admin_amount + $admin_amount1;
                          $adminInfo = User::where('id', 1)->first();
                          $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);

                          User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

                            $TransId = $trans->id;
                            //Receiver Mail Start
                            $emailId = $user->email;
                            $mailAmount = number_format($order_amount, 2, '.', ',');
                            $mailAmnt = explode(".", $mailAmount);
                            $emailSubject = "DafriBank Digital | Account has been credited with " . $currency_code. " " . $order_amount;
                            $emailData['subjects'] = $emailSubject;
                            $emailData['emailId'] = $emailId;
                            $emailData['mailAmnt'] = $mailAmnt;
                            $emailData['mailAmount'] = $order_amount;
                            $emailData['currency'] = $currency_code;
                            $emailData['receiver_currency'] = $user->currency;
                            $emailData['TransId'] = $TransId;
                            $emailData['fees_amount_receiver'] = $fees_amount_receiver;
                            $emailData['refrence_id'] = $refrence_id;
                            $emailData['receiverName'] = $receiverName;
                            $emailData['payeeName'] = $payeeName;
                            $emailData['mail_dashboard_lnk'] = $mail_dashboard_lnk_receiver;
                            Mail::send('emails.epayPaymentCredit', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subjects']);
                            });

                            
                            $notif = new Notification([
                                'user_id' => $user->id,
                                'notif_subj' => $emailSubject,
                                'notif_body' => $emailSubject,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            $notif->save();

                            $emailId = $userInfo->email;
                            $emailData['fees_amount'] = $total_sender_fees;
                            $emailSubject = "DafriBank Digital | Account has been debited with " . $userInfo->currency . " " . $total_debited_amount_sender;
                            $emailData['mailAmnt'] = $order_amount;
                            $emailData['currency'] = $user->currency;
                            $emailData['subjects'] = $emailSubject;
                            $emailData['sender_currency'] = $userInfo->currency;
                            Mail::send('emails.epayPaymentDebit', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subjects']);
                            });

                            $notif = new Notification([
                                'user_id' => $userInfo->id,
                                'notif_subj' => $emailSubject,
                                'notif_body' => $emailSubject,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            $notif->save();

                            Session::put('user_id', $userInfo->id);
                            Session::put('user_name', strtoupper($userInfo->first_name));
                            Session::put('email', $userInfo->email);
                            $b64TransID = base64_encode($TransId);
                            $b64RefID = base64_encode($refrence_id);
                            $base64Result = base64_encode($TransId . '###Success');
                            return Redirect::to('epay-payment-successfull');
                    }
                    else {
                        Session::put('error_session_message', 'Insufficient Balance!'); 
                        return Redirect::to('/epayme');
                    }

                }
           }

        }

        return view('homes.confirmEpaymePayment', ['title' => $pageTitle,'order_amount' => $order_amount, 'user_id' => $user_id,'user' => $user, 'userInfo' => $userInfo, 'currency_code' => $currency_code]);



    }


    public function ozowLogin() {
        $pageTitle = 'ePay Me';
        $input = Input::all();
        if (!empty($input)) {
           $rules = array(
               'email' => 'required|email',
               'password' => 'required'
           );
           $validator = Validator::make($input, $rules);
           if ($validator->fails()) {
               $messages = $validator->messages();
               $message = implode('<br>', $messages->all());
               Session::put('error_session_message', $message);
               return Redirect::to('/ozow-login')->withInput(Input::except('password'));
           } 
           else 
           {
               $userInfo = User::where('email', $input['email'])->first();
               if(empty($userInfo))
               {
               Session::put('error_session_message','Please provide a valid login detail');
               return Redirect::to('/ozow-login')->withInput(Input::except('password'));   
               }

               if (!empty($userInfo)) {
                   if (password_verify($input['password'], $userInfo->password)) {
                       if ($userInfo->otp_verify == 1 && $userInfo->is_verify == 1) {
                               $otp_codee = $this->generateNumericOTP(6);
                               $ty = User::where('id', $userInfo->id)->update([
                                   'verify_code' => $this->encpassword($otp_codee),
                                   'otp_time' => date('Y-m-d H:i:s')
                               ]);
                               //$this->sendEmailOTP($otp_codee); 
                               if ($userInfo->user_type == 'Personal') {
                                   $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                               } 
                               else if ($userInfo->user_type == 'Business') {
                                   if($userInfo->first_name!="")
                                   {
                                   $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);   
                                   }
                                   else{
                                   $uname = strtoupper($userInfo->business_name);
                                   }
                               } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name != "") {
                                   $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                               } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name == "") {
                                   $uname = strtoupper($userInfo->business_name);
                               }
                               $emailId = $userInfo->email;
                               $emailSubject = 'OTP For Payment';
                               $emailData['subjects'] = $emailSubject;
                               $emailData['emailId'] = $userInfo->email;
                               $emailData['otp'] = $otp_codee;
                               $emailData['userName'] = $uname;
                              // print_r($emailData); die; 
                               $ty = Mail::send('emails.paymentOTP', $emailData, function ($message)use ($emailData, $emailId) {
                                           $message->to($emailId, $emailId)
                                                   ->subject($emailData['subjects']);
                                       });
                                Session::put('user_id', $userInfo->id);       
                               return redirect::to('/confirm-ozow-payment');

                       } else if ($userInfo->otp_verify == 0 || $userInfo->is_verify == 0) {
                           $error = 'Your account might have been temporarily disabled. Please contact us for more details.';
                       }
                   } else {
                       $error = 'Invalid email or password.';
                   }
               } else {
                   $error = 'Invalid email or password.';
               }
               Session::put('error_session_message', $error);
               return Redirect::to('/ozow-login')->withInput(Input::except('password'));
           }

        }

        return view('homes.ozoLogin', ['title' => $pageTitle]);
   }


   public function confirmOzowPayment()
   {   
        $pageTitle = 'Confirm ePay Me Payment';
        $user_id=Session::get('user_id');     
        $userInfo=User::where('id',$user_id)->first(); 
        $input = Input::all();
        if (!empty($input)) {
            if ($input['otp_code'] == '') {
                Session::put('error_session_message', 'You have entered invalid verification code.');
                return Redirect::to('/confirm-ozow-payment');
            } else {
                $otp_code = $input['otp_code'];

                $dateChk = date('Y-m-d H:i', strtotime('+15 minutes', strtotime($userInfo->otp_time)));
                if (!password_verify($otp_code, $userInfo->verify_code) || date('Y-m-d H:i') > $dateChk) {
                    Session::put('error_session_message', 'You have entered invalid verification code.');
                    return Redirect::to('/confirm-ozow-payment');
                } else {
                    $modified = date('Y-m-d H:i:s', time());
                    User::where('id', $userInfo->id)->update(array('last_login' => $modified));
                    Session::put('user_id', $userInfo->id);
                    Session::put('user_name', strtoupper($userInfo->first_name));
                    Session::put('email', $userInfo->email);
                    Session::forget('Userloginstatus');
                    return Redirect::to('/auth/add-fund');

        }
       }
     }
    return view('homes.confirmOzowPayment', ['title' => $pageTitle,'userInfo'=>$userInfo]);
   }


   public function  epaypaymentsuccessfull()
   {
    $pageTitle = 'Online Payment';
    return view('homes.epaypaymentsuccessfull', ['title' => $pageTitle]);
   }

    public function onlinePayment() {
        $pageTitle = 'Online Payment';

        //        $req = base64_decode($reqStr);
        $reqStr = Session::get('reqStr');
        $req = base64_decode(Session::get('reqStr'));

        $reqArr = explode('###', $req);
       // print_r($reqArr);die;
        $order_id = $reqArr[0];
        $order_amount = $reqArr[1];
        //$user_id = $reqArr[2];
        $merchant_key = $reqArr[2];
        $currency_code = $reqArr[3];
        $url = $reqArr[4];
        // $user ---> $merchant
        $user = User::where('api_key', $merchant_key)->where('user_type', 'Business')->first();
       // echo "<pre>";
       // print_r($user); die;
        $user_id = '';

        if (empty($user)) {
            Session::put('error_session_message', 'Invalid Merchant ID');
            $user_id = 'N/A';
            $user_name = 'N/A';
            //return redirect::to('online-payment/'.$base64Req);	
        } else {
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
            } else if ($user->user_type == 'Business') {
                $user_name = strtoupper($user->business_name);
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
            } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                $user_name = strtoupper($user->business_name);
            }
            $user_id = $user->id;
        }


        $input = Input::all();
        if (!empty($input)) {
            $rules = array(
                'email' => 'required|email',
                'password' => 'required'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                $message = implode('<br>', $messages->all());

                Session::put('error_session_message', $message);
                return Redirect::to('/online-payment')->withInput(Input::except('password'));
                //                return Redirect::to('/online-payment/'.$reqStr)->withErrors($validator)->withInput(Input::except('password'));
            } else {
                $userInfo = User::where('email', $input['email'])->first();
                if(empty($userInfo))
                {
                Session::put('error_session_message','Please provide a valid login detail');
                return Redirect::to('/online-payment')->withInput(Input::except('password'));   
                }
                if ($user->id == $userInfo->id) {
                    //                        Session::flash('error_message', "Sender & Receiver Should not be same.");
                    Session::put('error_session_message', "Sender & Receiver can't be same.");
                    return Redirect::to('/online-payment');
                }

                if (!empty($userInfo)) {
                    if (password_verify($input['password'], $userInfo->password)) {

                        if ($userInfo->otp_verify == 1 && $userInfo->is_verify == 1) {
                                $otp_codee = $this->generateNumericOTP(6);
                                $ty = User::where('id', $userInfo->id)->update([
                                    'verify_code' => $this->encpassword($otp_codee),
                                    'otp_time' => date('Y-m-d H:i:s')
                                ]);
                                //$this->sendEmailOTP($otp_codee); 
                                if ($userInfo->user_type == 'Personal') {
                                    $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                                } 
                                else if ($userInfo->user_type == 'Business') {
                                    if($userInfo->first_name!="")
                                    {
                                    $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);   
                                    }
                                    else{
                                    $uname = strtoupper($userInfo->business_name);
                                    }
                                } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name != "") {
                                    $uname = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                                } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name == "") {
                                    $uname = strtoupper($userInfo->business_name);
                                }
                                $emailId = $userInfo->email;
                                $emailSubject = 'OTP For Payment';
                                $emailData['subjects'] = $emailSubject;
                                $emailData['emailId'] = $userInfo->email;
                                $emailData['otp'] = $otp_codee;
                                $emailData['userName'] = $uname;
                               // print_r($emailData); die; 
                                $ty = Mail::send('emails.paymentOTP', $emailData, function ($message)use ($emailData, $emailId) {
                                            $message->to($emailId, $emailId)
                                                    ->subject($emailData['subjects']);
                                        });
                                //if ($user->currency == $userInfo->currency) {
                                if ($currency_code == $userInfo->currency) {
                                    if ($userInfo->user_type == "Personal") {
                                        $payeeName = $userInfo->first_name . " " . $userInfo->last_name;
                                    } else if ($userInfo->user_type == "Business") {
                                        $payeeName = $userInfo->business_name;
                                    } else if ($userInfo->user_type == "Agent" && $userInfo->first_name == "") {
                                        $payeeName = $userInfo->business_name;
                                    } else if ($userInfo->user_type == "Agent" && $userInfo->first_name != "") {
                                        $payeeName = $userInfo->first_name . " " . $userInfo->last_name;
                                    }
                                    
                                    if ($currency_code == $user->currency) {
                                        
                                        Session::put('user_request_amount', $order_amount);
                                    } else{
                                        $amount_receiver_currency = $this->myCurrencyRate($currency_code, $user->currency, $order_amount);
                                        //echo $amount_user_currency; exit;
                                        $converArr_receiver = explode("###", $amount_receiver_currency);
                                        Session::put('user_request_amount', $converArr_receiver[1]);
                                    }  
                                    
                                    
                                    Session::put('Online_payment_show_convsn_rate', true);
                                    //  Session::put('Online_payment_convrtd_amount', $amount_user_currency);
                                    Session::put('user_id', $userInfo->id);
                                    Session::put('merchant_id', $user->id);
                                    Session::put('reqStr', $reqStr);
                                    Session::put('user_currency', $userInfo->currency);
//                                    Session::put('user_request_amount', $order_amount);
                                    Session::put('merchant_currency', $currency_code);
                                    Session::forget('success_payment_message');

                                    return redirect::to('/confirm-online-payment');
                                } else {
                                    $amount_user_currency = $this->myCurrencyRate($currency_code, $userInfo->currency, $order_amount);
                                    //echo $amount_user_currency; exit;
                                    $converArr = explode("###", $amount_user_currency);

                                    if($currency_code!=$user->currency)
                                    {
                                    $amount_receiver_currency = $this->myCurrencyRate($currency_code, $user->currency, $order_amount);
                                    //echo $amount_user_currency; exit;
                                    $converArr_receiver = explode("###", $amount_receiver_currency);
                                    }
                                    else{
                                    $converArr_receiver[1]=$order_amount;
                                    }


                                    if ($userInfo->user_type == "Personal") {
                                        $payeeName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);

                                        if ($userInfo->account_category == "Silver") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER';
                                        } else if ($userInfo->account_category == "Gold") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER_GOLD';
                                        } else if ($userInfo->account_category == "Platinum") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER_PLATINUM';
                                        } else if ($userInfo->account_category == "Private Wealth") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER_PRIVATE_WEALTH';
                                        } else {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER';
                                        }

                                        $convr_fee_name = $userInfo->currency!="NGN" &&  $currency_code!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';

                                    } else if ($userInfo->user_type == "Business") {
                                        $payeeName = strtoupper($userInfo->business_name);

                                        if ($userInfo->account_category == "Gold") {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                        } else if ($userInfo->account_category == "Platinum") {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_PLATINUM';
                                        } else if ($userInfo->account_category == "Enterprises") {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_Enterpris';
                                        } else {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                        }
                                        $convr_fee_name = $userInfo->currency!="NGN" && $currency_code!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';

                                    } else if ($userInfo->user_type == "Agent" && $userInfo->first_name == "") {
                                        $payeeName = strtoupper($userInfo->business_name);
                                        if ($userInfo->account_category == "Gold") {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                        } else if ($userInfo->account_category == "Platinum") {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_PLATINUM';
                                        } else if ($userInfo->account_category == "Enterprises") {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_Enterpris';
                                        } else {
                                            $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                        }

                                        $convr_fee_name = $userInfo->currency!="NGN" && $currency_code!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';

                                    } else if ($userInfo->user_type == "Agent" && $userInfo->first_name != "") {
                                        $payeeName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);

                                        if ($userInfo->account_category == "Silver") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER';
                                        } else if ($userInfo->account_category == "Gold") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER_GOLD';
                                        } else if ($userInfo->account_category == "Platinum") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER_PLATINUM';
                                        } else if ($userInfo->account_category == "Private Wealth") {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER_PRIVATE_WEALTH';
                                        } else {
                                            $fee_name = 'ONLINE_PAYMENT_SENDER';
                                        }

                                        $convr_fee_name = $userInfo->currency!="NGN" &&  $currency_code!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';
                                    }

                                    $fees = Fee::where('fee_name', $fee_name)->first();
                                    $fees_amount_sender = ($converArr[1]* $fees->fee_value) / 100;

                                    //to check conversion fees
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;
                                    $conversion_amount = ($converArr[1] * $conversion_feet) / 100;

                                    $total_fees=number_format(($fees_amount_sender+$conversion_amount), 2, '.', '');


                                    Session::put('Online_payment_show_convsn_rate', true);
                                    Session::put('Online_payment_convrtd_amount', $amount_user_currency);
                                    Session::put('user_id', $userInfo->id);
                                    Session::put('merchant_id', $user->id);
                                    Session::put('reqStr', $reqStr);
                                    Session::put('user_currency', $userInfo->currency);
                                    Session::put('user_currency_amount', $converArr[1]);
                                    Session::put('user_request_amount', $converArr_receiver[1]);
                                    Session::put('receiver_currency', $user->currency);
                                    Session::put('merchant_currency', $currency_code);
                                    // Session::put('return_url', $url);
                                    //                                                  Session::put('success_session_message', "Dear ".$payeeName.", You are initiating a transaction of a different currency. So the amount ".$userInfo->currency ." ". $converArr[1]." will be deducted from your DafriBank account at the current conversion rate ".$converArr[0]);
                                    Session::put("success_payment_message", "Dear " . $payeeName . ", You are initiating a transaction of a different currency. So the amount " . $userInfo->currency . " " . ($total_fees+$converArr[1]) . " will be deducted from your DafriBank account at the current conversion rate " . $converArr[0].' and fee charged '.$total_fees);
//                                    Session::flash("success_message", "Dear " . $payeeName . ", You are initiating a transaction of a different currency. So the amount " . $userInfo->currency . " " . $converArr[1] . " will be deducted from your DafriBank account at the current conversion rate " . $converArr[0]);
                                    return redirect::to('/confirm-online-payment');
                                }

                                Session::put('user_id', $userInfo->id);
                                Session::put('user_name', ucwords($userInfo->first_name));
                                Session::put('email', $userInfo->email);
                                return Redirect::to('/overview');
                        } else if ($userInfo->otp_verify == 0 || $userInfo->is_verify == 0) {
                            $error = 'Your account might have been temporarily disabled. Please contact us for more details.';
                        }
                    } else {
                        $error = 'Invalid email or password.';
                    }
                } else {
                    $error = 'Invalid email or password.';
                }

                Session::put('error_session_message', $error);

                //return Redirect::to('/personal-login')->withInput(Input::except('password'));
                return Redirect::to('/online-payment')->withInput(Input::except('password'));
            }
        }
//echo $currency_code; die;
        return view('homes.onlinePayment', ['title' => $pageTitle, 'order_id' => $order_id, 'order_amount' => $order_amount, 'user_id' => $user_id, 'merchant_name' => $user_name, 'user' => $user, 'currency_code' => $currency_code]);
    }

    public function confirmOnlinePayment(Request $request) {
        $pageTitle = 'Confirm Online Payment';

        $req = base64_decode(Session::get('reqStr'));

        $reqArr = explode('###', $req);
        $order_id = $reqArr[0];
        $order_amount = $reqArr[1];
        $user_id = $reqArr[2];
        $url = $reqArr[4];
        $currency_code = $reqArr[3];  
        $user = User::where('api_key', $user_id)->first();

        if (empty($user)) {
            Session::put('error_session_message', 'Invalid Merchant ID');
            //		Session::flash('Invalid Merchant ID');
            $user_id = 'N/A';
            $user_name = 'N/A';
            //return redirect::to('online-payment/'.$base64Req);	
        } else {
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
                $payeeName = strtoupper($user->first_name . ' ' . $user->last_name);
                $mail_dashboard_lnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Business') {
                $user_name = strtoupper($user->business_name);
                $payeeName = strtoupper($user->business_name);
                $mail_dashboard_lnk = HTTP_PATH . '/business-login';
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->business_name);
                $payeeName = strtoupper($user->business_name);
                $mail_dashboard_lnk = HTTP_PATH . '/personal-login';
            } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                $user_name = strtoupper($user->business_name);
                $payeeName = strtoupper($user->business_name);
                $mail_dashboard_lnk = HTTP_PATH . '/business-login';
            }
        }

        $userInfo = User::where('id', Session::get('user_id'))->first();
        $input = Input::all();
        if (!empty($input)) {

            //print_r($input );die;
            global $currencyList;
            if(!in_array($currency_code,$currencyList))
            {
            Session::put('error_session_message', "Order amount currency is not supported.");
            return Redirect::to('/online-payment');
            }

            if(!isset($currency_code))
            {
                Session::put('error_session_message', "Order amount currency is not found.");
                return Redirect::to('/online-payment');  
            }

            if ($input['otp_code'] == '' || $input['otp_code1'] == '' || $input['otp_code2'] == '' || $input['otp_code3'] == '' || $input['otp_code4'] == '' || $input['otp_code5'] == '') {
                Session::put('error_session_message', 'You have entered invalid verification code.');
                return Redirect::to('/confirm-online-payment');
            } else {
                $otp_code = $input['otp_code'] . $input['otp_code1'] . $input['otp_code2'] . $input['otp_code3'] . $input['otp_code4'] . $input['otp_code5'];

                $dateChk = date('Y-m-d H:i', strtotime('+15 minutes', strtotime($userInfo->otp_time)));

                if (!password_verify($otp_code, $userInfo->verify_code) || date('Y-m-d H:i') > $dateChk) {
                    Session::put('error_session_message', 'You have entered invalid verification code.');
                    return Redirect::to('/confirm-online-payment');
                } else {
                    
                    if($user->api_enable=='N')
                    {
                    Session::put('error_session_message', "Merchant service is temporary disabled. Please try after some time.");
                    return Redirect::to('online-payment');  
                    }
                  
                    if ($userInfo->is_kyc_done != 1 or $userInfo->is_verify != 1) { 
                    $chkAmount = $this->fetchCurrencyRate($currency_code, $order_amount);
                    if ($chkAmount > TRANS_LIMIT_BEFORE_KYC && $userInfo->currency != 'USD') {
                        $user_currncy_250 = $this->myCurrencyRate1($userInfo->currency, TRANS_LIMIT_BEFORE_KYC);
                        
                        $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                        //                        Session::flash('error_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please update your KYC.");
                        Session::put('error_session_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please upload your KYC.");
                        return Redirect::to('online-payment');
                    }

                    //                    $TotalWithdraw = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', 0)->where('trans_for', 'LIKE', 'Withdraw%')->whereIn('status', array(1, 2))->get();

                    $TotalWithdraw = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', 0)->where('trans_for', 'LIKE', 'Withdraw%')->whereIn('status', array(1, 2))->sum('amount');

                    $TotalDebitTrans = Transaction::where('user_id', Session::get('user_id'))->where('receiver_id', '!=', 0)->where('trans_type', 2)->where('status', 1)->sum('amount');
                    //                    $TotalDebitTrans = Transaction::where('user_id', 1)->get();
                    //check Inactive Amount (Agent Request and paypal Request) Calc Start
                    $TotalInactvAmnt = InactiveAmount::where('user_id', Session::get('user_id'))->sum('amount');
                    //check Inactive Amount (Agent Request and paypal Request) Calc End

                    $ttlWithdrawAmount = $TotalWithdraw + $TotalDebitTrans + $TotalInactvAmnt + $order_amount;
                    $chkAmount = $this->fetchCurrencyRate($currency_code, $ttlWithdrawAmount);

//                                        echo "Ttl Withdraw Amount: ".$ttlWithdrawAmount." :: USD: ".$chkAmount; exit;

                    if ($chkAmount >= TRANS_LIMIT_BEFORE_KYC) {
                        $user_currncy_250 = $this->myCurrencyRate1($userInfo->currency, TRANS_LIMIT_BEFORE_KYC);
                        $user_currncy_250 = number_format($user_currncy_250, 2, '.', ',');
                        //                        Session::flash('error_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please update your KYC.");
                        Session::put('error_session_message', "You can't transfer more than " . $userInfo->currency . " " . $user_currncy_250 . ", please upload your KYC.");
                        return Redirect::to('online-payment');
                    }
                }


                $transLimitFlag = $this->checkUserTransLimit(Session::get('user_id'), $userInfo->account_category, $userInfo->user_type, $order_amount);

                $transLimitArr = explode("###", $transLimitFlag);
                //print_r($transLimitArr); exit;

                if ($transLimitArr[0] == 'false' or $transLimitArr[0] == false) {
                    //                    Session::flash('error_message', $transLimitArr[1]);
                    Session::put('error_session_message', $transLimitArr[1]);
                    return Redirect::to('online-payment');
                }

                    if ($currency_code == $userInfo->currency) {
                        if ($userInfo->wallet_amount > $order_amount) {

                            if ($userInfo->user_type == 'Personal' || ($userInfo->user_type == 'Agent' && $userInfo->first_name != '')) {
                                $payeeName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);
                                $mail_dashboard_lnk = HTTP_PATH . '/personal-login';
                                //Sender Fees Calc Start
                                if ($userInfo->account_category == "Silver") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER';
                                } else if ($userInfo->account_category == "Gold") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER_GOLD';
                                } else if ($userInfo->account_category == "Platinum") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER_PLATINUM';
                                } else if ($userInfo->account_category == "Private Wealth") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER_PRIVATE_WEALTH';
                                } else {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first(); 
                                $fees_amount = ($order_amount * $fees->fee_value) / 100;
                            } else if ($userInfo->user_type == 'Business' || ($userInfo->user_type == 'Agent' && $userInfo->first_name == '')) {
                                $payeeName = $userInfo->business_name;
                                $mail_dashboard_lnk = HTTP_PATH . '/business-login';
                                if ($userInfo->account_category == "Gold") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                } else if ($userInfo->account_category == "Platinum") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_PLATINUM';
                                } else if ($userInfo->account_category == "Enterprises") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_Enterpris';
                                } else {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = ($order_amount * $fees->fee_value) / 100;
                            } else {
                                $fees_amount = 0;
                                if ($userInfo->first_name == "") {
                                    $payeeName = strtoupper($userInfo->business_name);
                                    $mail_dashboard_lnk = HTTP_PATH . '/personal-login';
                                } else {
                                    $payeeName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);
                                    $mail_dashboard_lnk = HTTP_PATH . '/business-login';
                                }
                            }
                            //Sender Fees Calc Start
                            //Receiver Fees Calc Start

                            $merchant_amount = Session::get('user_request_amount');
                            if ($user->user_type == 'Personal' || ($user->user_type == 'Agent' && $user->first_name != '')) {
                                //$payeeName = $user->first_name." ".$user->last_name;
                                $receiverName = strtoupper($user->first_name . " " . $user->last_name);
                                $mail_dashboard_lnk_receiver = HTTP_PATH . '/personal-login';
                                //Receiver Fees Calc Start
                                if ($user->account_category == "Silver") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER';
                                } else if ($user->account_category == "Gold") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER_GOLD';
                                } else if ($user->account_category == "Platinum") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER_PLATINUM';
                                } else if ($user->account_category == "Private Wealth") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER_PRIVATE_WEALTH';
                                } else {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount_receiver = ($merchant_amount * $fees->fee_value) / 100;
                            } else if ($user->user_type == 'Business' || ($user->user_type == 'Agent' && $user->first_name == '')) {
                                //$payeeName = $user->director_name;
                                $receiverName = strtoupper($user->business_name);
                                $mail_dashboard_lnk_receiver = HTTP_PATH . '/business-login';
                                if ($user->account_category == "Gold") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_GOLD';
                                } else if ($user->account_category == "Platinum") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_PLATINU';
                                } else if ($user->account_category == "Enterprises") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_Enterpr';
                                } else {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_GOLD';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount_receiver = ($merchant_amount * $fees->fee_value) / 100;
                            } else {
                                $fees_amount_receiver = 0;
                                if ($user->first_name == "") {
                                    //  $payeeName = $user->director_name;   
                                    $receiverName = strtoupper($user->business_name);
                                    $mail_dashboard_lnk_receiver = HTTP_PATH . '/business-login';
                                } else {
                                    // $payeeName = $userInfo->first_name." ".$userInfo->last_name;  
                                    $receiverName = strtoupper($user->first_name . " " . $user->last_name);
                                    $mail_dashboard_lnk_receiver = HTTP_PATH . '/personal-login';
                                }
                            }
                            $conversion_fee_reciver = $conversion_fee_reciver_amount = 0;
//                            $merchant_amount = $order_amount;
                            if ($currency_code != $user->currency) {
                                $convr_fee_name = $currency_code!="NGN" && $user->currency!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';
                                $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                $conversion_fee_reciver = $fees_convr->fee_value;
                                // print_r($fees_convr);
                                // die;
//                                Session::get('user_request_amount').'/';
                                
                                $conversion_fee_reciver_amount = ($merchant_amount * $conversion_fee_reciver) / 100;
                                $receiver_side_conversion_rate= $this->convertCurrency($currency_code,$user->currency,$order_amount);
                                $receiver_side_conversion_rate_total= explode("##", $receiver_side_conversion_rate)[0];
                                $receiver_side_conversion_rate_single= explode("##", $receiver_side_conversion_rate)[1];

                                $billing_description = 'IP:' . $this->get_client_ip() .'##Received Amount '.$currency_code . ' ' . $order_amount . ' and Conversion rate '.$receiver_side_conversion_rate_single.'='.$user->currency.' '.$receiver_side_conversion_rate_total."##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver . "##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##Conversion Fees RECEIVER = " . $user->currency . ' ' . $conversion_fee_reciver_amount; 
                                $fees_amount_receiver = $fees_amount_receiver + $conversion_fee_reciver_amount;
                            } else {
                                $billing_description = 'IP:' . $this->get_client_ip() . "##RECEIVER_FEES : " . $userInfo->currency . ' ' . $fees_amount_receiver . "##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount;
                            }


                            //Receiver Fees Calc End    


                            if ($userInfo->wallet_amount < $order_amount + $fees_amount) 
                            {
                            Session::put('error_session_message', "Insufficient Balance.");
                            return Redirect::to('online-payment');
                            }

                            $payee_wallet = $userInfo->wallet_amount - ($order_amount + $fees_amount);

                            User::where('id', $userInfo->id)->update(['wallet_amount' => $payee_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                            $merchant_wallet = $user->wallet_amount + ($merchant_amount - $fees_amount_receiver);
                            User::where('id', $user->id)->update(['wallet_amount' => $merchant_wallet, 'updated_at' => date('Y-m-d H:i:s')]);


                            $amount_admin_currency = $this->convertCurrency($userInfo->currency,'USD', ($fees_amount));
                            $amount_admin_currencyArr = explode("##", $amount_admin_currency);
                            $admin_amount = $amount_admin_currencyArr[0];
                            $admin_converstion_rate = $amount_admin_currencyArr[1];

                            $amount_admin_currency1 = $this->convertCurrency($user->currency,'USD',($fees_amount_receiver));
                            $amount_admin_currencyArr1 = explode("##", $amount_admin_currency1);
                            $admin_amount1 = $amount_admin_currencyArr1[0];
                            $admin_converstion_rate1 = $amount_admin_currencyArr1[1];

                            $admin_amount = $admin_amount + $admin_amount1;

                            $adminInfo = User::where('id', 1)->first();
                            $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);
                            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

//                            $adminInfo = User::where('id', 1)->first();
//
//                            $admin_wallet = $adminInfo->wallet_amount + ($fees_amount + $fees_amount_receiver);
//                            User::where('id', 1)->update(['wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                            $refrence_id = $order_id;
                            $trans = new Transaction([
                                "user_id" => $userInfo->id,
                                "receiver_id" => $user->id,
                                "amount" => $order_amount,
                                "fees" => ($fees_amount + $fees_amount_receiver),
                                "receiver_fees" => ($fees_amount_receiver),
                                "sender_fees" => $fees_amount,
                                "sender_currency" => $userInfo->currency,
                                "receiver_currency" => $user->currency,
                                "currency" => $currency_code,
                                "trans_type" => 2, //Debit-Withdraw
                                "trans_to" => 'Dafri_Wallet',
                                "trans_for" => 'ONLINE_PAYMENT',
                                "refrence_id" => $refrence_id,
                                "billing_description" => $billing_description,
                                "user_close_bal" => $payee_wallet,
                                "receiver_close_bal" => $merchant_wallet,
                                "real_value" => $merchant_amount - $fees_amount_receiver,
                                "sender_real_value" =>$order_amount + $fees_amount,
                                "status" => 1,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                            $trans->save();
                            $TransId = $trans->id;
                            //Receiver Mail Start
                            $emailId = $user->email;
                            $mailAmount = number_format($order_amount, 2, '.', ',');
                            $mailAmnt = explode(".", $mailAmount);
                            $emailSubject = "DafriBank Digital | Account has been credited with " . $currency_code. " " . $order_amount;
                            $emailData['subjects'] = $emailSubject;
                            $emailData['emailId'] = $emailId;
                            $emailData['mailAmnt'] = $mailAmnt;
                            $emailData['mailAmount'] = $order_amount;
                            $emailData['currency'] = $currency_code;
                            $emailData['receiver_currency'] = $user->currency;
                            $emailData['TransId'] = $TransId;
                            $emailData['fees_amount_receiver'] = $fees_amount_receiver;
                            $emailData['refrence_id'] = $refrence_id;
                            $emailData['receiverName'] = $receiverName;
                            $emailData['payeeName'] = $payeeName;
                            $emailData['mail_dashboard_lnk'] = $mail_dashboard_lnk_receiver;
                            Mail::send('emails.onlinePaymentCredit', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subjects']);
                            });
                            
                            $notif = new Notification([
                                'user_id' => $user->id,
                                'notif_subj' => $emailSubject,
                                'notif_body' => $emailSubject,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            $notif->save();
                            
                            //                                        Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                            //Receiver Mail End
                            //Sender Mail Start
                            $emailId = $userInfo->email;
                            $emailData['fees_amount'] = $fees_amount;
                            $emailSubject = "DafriBank Digital | Account has been debited with " . $currency_code . " " . $order_amount;
                            $emailData['mailAmnt'] = $order_amount;
                            $emailData['currency'] = $currency_code;
                            $emailData['subjects'] = $emailSubject;
                            $emailData['sender_currency'] = $userInfo->currency;
                            Mail::send('emails.onlinePaymentDebit', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subjects']);
                            });
                            
                            $notif = new Notification([
                                'user_id' => $userInfo->id,
                                'notif_subj' => $emailSubject,
                                'notif_body' => $emailSubject,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            $notif->save();

                            //                                        Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                            //Sender Mail End

                            Session::put('user_id', $userInfo->id);
                            Session::put('user_name', strtoupper($userInfo->first_name));
                            Session::put('email', $userInfo->email);
                            //return Redirect::to('/overview');
                            $b64TransID = base64_encode($TransId);
                            $b64RefID = base64_encode($refrence_id);

                            //                                        return Redirect::to('auth/transfer-success/' . $b64TransID . '/' . $b64RefID);
                            $base64Result = base64_encode($TransId . '###Success');
                            return Redirect::to($url . '/' . $base64Result);
                        } else {
                            Session::put('error_session_message', 'Insufficient Balance!');
                            //                        Session::flash('error_message','Insufficient Balance!');  
                            return Redirect::to('/online-payment');
                        }
                    } else {

                        if ($userInfo->wallet_amount >= Session::get('user_currency_amount')) {
                            //SENDER FEES CALC START
                            if ($userInfo->user_type == 'Personal' || ($userInfo->user_type == 'Agent' && $userInfo->first_name != '')) {
                                $receiverName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);
                                $mail_dashboard_lnk_receiver = HTTP_PATH . '/personal-login';
                                if ($userInfo->account_category == "Silver") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER';
                                } else if ($userInfo->account_category == "Gold") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER_GOLD';
                                } else if ($userInfo->account_category == "Platinum") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER_PLATINUM';
                                } else if ($userInfo->account_category == "Private Wealth") {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER_PRIVATE_WEALTH';
                                } else {
                                    $fee_name = 'ONLINE_PAYMENT_SENDER';
                                }
                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = (Session::get('user_currency_amount') * $fees->fee_value) / 100;
                                $convr_fee_name = $userInfo->currency!="NGN" &&  $currency_code!="NGN" ? 'CONVERSION_FEE' : 'NGN_CONVERSION_FEE';
                            } else if ($userInfo->user_type == 'Business' || ($userInfo->user_type == 'Agent' && $userInfo->first_name == '')) {
                                $receiverName = $userInfo->business_name;
                                $mail_dashboard_lnk_receiver = HTTP_PATH . '/business-login';
                                if ($userInfo->account_category == "Gold") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                } else if ($userInfo->account_category == "Platinum") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_PLATINUM';
                                } else if ($userInfo->account_category == "Enterprises") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_Enterpris';
                                } else {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_SENDER_GOLD';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount = (Session::get('user_currency_amount') * $fees->fee_value) / 100;
                                $convr_fee_name = $userInfo->currency!="NGN" && $currency_code!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';
                            } else {
                                $fees_amount = 0;
                                if ($userInfo->first_name != "") {
                                    $receiverName = strtoupper($userInfo->first_name . " " . $userInfo->last_name);
                                    $mail_dashboard_lnk_receiver = HTTP_PATH . '/personal-login';
                                } else {
                                    $receiverName = $userInfo->director_name;
                                    $mail_dashboard_lnk_receiver = HTTP_PATH . '/business-login';
                                }
                            }
                           // print_r($fees); 
                            //SENDER FEES CALC END
                            //RECEIVER FEES CALC START
                            if ($user->user_type == 'Personal' || ($user->user_type == 'Agent' && $user->first_name != '')) {
                                if ($user->account_category == "Silver") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER';
                                } else if ($user->account_category == "Gold") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER_GOLD';
                                } else if ($user->account_category == "Platinum") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER_PLATINUM';
                                } else if ($user->account_category == "Private Wealth") {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER_PRIVATE_WEALTH';
                                } else {
                                    $fee_name = 'ONLINE_PAYMENT_RECEIVER';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first();
                                $fees_amount_receiver = (Session::get('user_request_amount') * $fees->fee_value) / 100;
                            } else if ($user->user_type == 'Business' || ($user->user_type == 'Agent' && $user->first_name == '')) {
                                if ($user->account_category == "Gold") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_GOLD';
                                } else if ($user->account_category == "Platinum") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_PLATINU';
                                } else if ($user->account_category == "Enterprises") {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_Enterpr';
                                } else {
                                    $fee_name = 'MERCHANT_ONLINE_PAYMENT_RECEIVER_GOLD';
                                }

                                $fees = Fee::where('fee_name', $fee_name)->first();
                               // print_r($fees);
                                $fees_amount_receiver = (Session::get('user_request_amount') * $fees->fee_value) / 100;
                            } else {
                                $fees_amount_receiver = 0;
                            }
                        //    echo 'User Amount : '.Session::get('user_currency_amount');
                        //    echo '<br>';
                        //    echo 'Receiver Amount : '.Session::get('user_request_amount');
                        //    echo '<br>';
                           
                        //    echo 'Receiver Fee : '.$fees_amount_receiver;
                        //    echo '<br>';
                        //    echo 'Sender Fee : '.$fees_amount;
                        //    echo '<br>';
                        //    die;

                            
                            $conversion_fee = 0;
                            $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                            $conversion_fee = $fees_convr->fee_value;
                            // die;
                            $conversion_fee_amount = (Session::get('user_currency_amount') * $conversion_fee) / 100;
                            $conversion__receiver_fee_amount = 0;
                            if ($currency_code != $user->currency) {
                                $convr_fee_name = $user->currency!="NGN" && $currency_code!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';
                                $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                $conversion_feess = $fees_convr->fee_value;
                                $conversion__receiver_fee_amount = (Session::get('user_request_amount') * $conversion_feess) / 100;
                            }
//echo 'Conversion Fee : '.$conversion_fee_amount;
//                            echo '<br>';
//echo 'Conversion Receiver Fee : '.$conversion__receiver_fee_amount;
//                            echo '<br>';
//exit;
                            //RECEIVER FEES CALC END

                            if ($userInfo->wallet_amount < (Session::get('user_currency_amount') + $fees_amount + $conversion_fee_amount)) 
                            {
                            Session::put('error_session_message', "Insufficient Balance.");
                            return Redirect::to('online-payment');
                            } 



                            $payee_wallet = $userInfo->wallet_amount - (Session::get('user_currency_amount') + $fees_amount + $conversion_fee_amount);



                            User::where('id', $userInfo->id)->update(['wallet_amount' => $payee_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                            $merchant_wallet = $user->wallet_amount + (Session::get('user_request_amount') - $fees_amount_receiver - $conversion__receiver_fee_amount);
                            User::where('id', $user->id)->update(['wallet_amount' => $merchant_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                            $amount_admin_currency = $this->convertCurrency($userInfo->currency,'USD',($fees_amount + $conversion_fee_amount));
                            $amount_admin_currencyArr = explode("##", $amount_admin_currency);
                            $admin_amount = $amount_admin_currencyArr[0];
                            $admin_converstion_rate = $amount_admin_currencyArr[1];

                            $amount_admin_currency1 = $this->convertCurrency($user->currency,'USD',($fees_amount_receiver + $conversion__receiver_fee_amount));
                            $amount_admin_currencyArr1 = explode("##", $amount_admin_currency1);
                            $admin_amount1 = $amount_admin_currencyArr1[0];
                            $admin_converstion_rate1 = $amount_admin_currencyArr1[1];

                            $admin_amount = $admin_amount + $admin_amount1;

                            $adminInfo = User::where('id', 1)->first();
                            $admin_wallet = ($adminInfo->wallet_amount + $admin_amount);
                            User::where('id', 1)->update(['wallet_amount' => $admin_wallet]);

//                            $adminInfo = User::where('id', 1)->first();
//
//
//                            $admin_wallet = $adminInfo->wallet_amount + ($fees_amount + $fees_amount_receiver + $conversion_fee_amount + $conversion__receiver_fee_amount);
//                            User::where('id', 1)->update(['wallet_amount' => $admin_wallet, 'updated_at' => date('Y-m-d H:i:s')]);

                            $convrsArr = explode("###", Session::get('Online_payment_convrtd_amount'));

                            $conversion_fee_reciver = $conversion_fee_reciver_amount = $conversion_fee_sender_amount = 0;

                            $conversion_fee_reciver_amount = $conversion__receiver_fee_amount;
                            $receiver_side_conversion_rate= $this->convertCurrency($currency_code,$user->currency,$order_amount);
                            $receiver_side_conversion_rate_total= explode("##", $receiver_side_conversion_rate)[0];
                            $receiver_side_conversion_rate_single= explode("##", $receiver_side_conversion_rate)[1];
                            if ($conversion__receiver_fee_amount != 0) {
                                $billing_description = 'IP:' . $this->get_client_ip() . '##Sent Amount ' . $currency_code . ' ' . $order_amount . ' and Conversion rate ' . $convrsArr[0] .'=' .$userInfo->currency .' '.$convrsArr[1].'##Received Amount '.$currency_code . ' ' . $order_amount . ' and Conversion rate '.$receiver_side_conversion_rate_single.'x'.$order_amount.'='.$user->currency.' '.$receiver_side_conversion_rate_total."##Conversion Fees = " . $userInfo->currency . ' ' . $conversion_fee_amount . "##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver . "##Conversion Fees RECEIVER = " . $user->currency . ' ' . $conversion_fee_reciver_amount;
                            } else {
                                $billing_description = 'IP:' . $this->get_client_ip() .'##Sent Amount ' . $currency_code . ' ' . $order_amount . ' and Conversion rate ' . $convrsArr[0] .'=' . $userInfo->currency .' '.$convrsArr[1].'##Received Amount '.$currency_code . ' ' . $order_amount . ' and Conversion rate '.$receiver_side_conversion_rate_single.'x'.$order_amount.'='.$user->currency.' '.$receiver_side_conversion_rate_total."##Conversion Fees = " . $userInfo->currency . ' ' . $conversion_fee_amount . "##SENDER_FEES : " . $userInfo->currency . ' ' . $fees_amount . "##RECEIVER_FEES : " . $user->currency . ' ' . $fees_amount_receiver;
                            }
                           // echo $billing_description; die;
                            //  fees_amount ---->  sender fee  //  conversion_fee_amount  --> sender side fee 
                            $refrence_id = $order_id;
                            $trans = new Transaction([
                                "user_id" => $userInfo->id,
                                "receiver_id" => $user->id,
                                "amount" => $order_amount,
                                "fees" => ($fees_amount + $conversion_fee_amount + $fees_amount_receiver + $conversion_fee_reciver_amount),
                                "receiver_fees" => ($fees_amount_receiver + $conversion_fee_reciver_amount),
                                "sender_fees" => $conversion_fee_amount + $fees_amount,
                                "sender_currency" => $userInfo->currency,
                                "receiver_currency" => $user->currency,
                                "currency" => $currency_code,
                                "trans_type" => 2, //Debit-Withdraw
                                "trans_to" => 'Dafri_Wallet',
                                "trans_for" => 'ONLINE_PAYMENT', 
                                "refrence_id" => $refrence_id,
                                "user_close_bal" => $payee_wallet,
                                "receiver_close_bal" => $merchant_wallet,
                                "real_value" =>(Session::get('user_request_amount') - $fees_amount_receiver - $conversion__receiver_fee_amount),
                                "sender_real_value" =>(Session::get('user_currency_amount') + $fees_amount + $conversion_fee_amount),
                                "billing_description" => $billing_description,
                                "status" => 1,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                            $trans->save();
                            $TransId = $trans->id;
                            //Receiver Mail Start
                            $emailId = $user->email;
//                            $mailAmount = number_format(Session::get('user_currency_amount'), 2, '.', ',');
                            $mailAmount = Session::get('user_currency_amount');
                            $mailAmnt = explode(".", $mailAmount);

                            $emailSubject = "DafriBank Digital | Account has been credited with " . $currency_code. " " . $order_amount;
                            $emailData['subjects'] = $emailSubject;
                            $emailData['emailId'] = $emailId;
                            $emailData['mailAmnt'] = number_format($order_amount, 2, '.', ',');
                            $emailData['currency'] = $currency_code;
                            $emailData['receiver_currency'] = $user->currency;
                            $emailData['TransId'] = $TransId;
                            $emailData['mailAmount'] =number_format($order_amount, 2, '.', ',');
                            $emailData['fees_amount_receiver'] = $fees_amount_receiver + $conversion_fee_reciver_amount;
                            $emailData['refrence_id'] = $refrence_id;
                            $emailData['receiverName'] = $payeeName;
                            $emailData['payeeName'] = $receiverName;
                            $emailData['mail_dashboard_lnk'] = $mail_dashboard_lnk_receiver;
                            Mail::send('emails.onlinePaymentCredit', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subjects']);
                            });
                            
                            $notif = new Notification([
                                'user_id' => $user->id,
                                'notif_subj' => $emailSubject,
                                'notif_body' => $emailSubject,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            $notif->save();
                            
                            //                      Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                            //Receiver Mail End
                            //Sender Mail Start
                            $emailId = $userInfo->email;

                            $emailSubject = "DafriBank Digital | Account has been debited with " . $currency_code . " " . $order_amount;
                            $emailData['mailAmnt'] = number_format($order_amount, 2, '.', ',');
                            $emailData['currency'] = $currency_code;
                            $emailData['sender_currency'] = $userInfo->currency;
                            $emailData['subjects'] = $emailSubject;
                            $emailData['fees_amount'] = $conversion_fee_amount + $fees_amount;
                            Mail::send('emails.onlinePaymentDebit', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subjects']);
                            });
                            
                            $notif = new Notification([
                                'user_id' => $userInfo->id,
                                'notif_subj' => $emailSubject,
                                'notif_body' => $emailSubject,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            $notif->save();
                            //                Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
                            //Sender Mail End

                            $b64TransID = base64_encode($TransId);
                            $b64RefID = base64_encode($refrence_id);
                            $base64Result = base64_encode($TransId . '###Success');
                            return Redirect::to($url . '/' . $base64Result);
                            //                return Redirect::to('auth/online-payment-success/' . $b64TransID . '/' . $b64RefID);
                        } else {
                            Session::put('error_session_message', 'Insufficient Balance!');
                            //      Session::flash("error_message","Insufficient Balance!");
                            return Redirect::to('/online-payment');
                        }
                    }
                }
            }
        }

        return view('homes.confirmOnlinePayment', ['title' => $pageTitle, 'order_id' => $order_id, 'order_amount' => $order_amount, 'user_id' => $user_id, 'merchant_name' => $user_name, 'user' => $user, 'userInfo' => $userInfo, 'currency_code' => $currency_code]);
    }

//    public function paymentsuccess($reqStr) {
//        $req = base64_decode($reqStr);
//
//        $reqArr = explode('###', $req);
//        if (isset($reqArr[0])) {
//            $order_id = $reqArr[0];
//        }
//        if (isset($reqArr[1])) {
//            $order_amount = $reqArr[1];
//        }
//
//        $postData = array(
//            'transaction_id' => $order_id,
//        );
//
//        $ch = curl_init();
//
//
//        curl_setopt_array($ch, array(
//            CURLOPT_URL => 'https://www.dafribank.com/api-transaction-detail',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => $postData,
//            CURLOPT_FOLLOWLOCATION => true
//        ));
//
//        $output = curl_exec($ch);
//        $response = json_decode($output);
//
//        echo 'Payment completed for order ID : ' . $response->order_id . ' and transaction ID : ' . $response->transaction_id;
//        exit;
//    }
    
    public function paymentsuccess($reqStr){
        $req = base64_decode($reqStr);

        $reqArr = explode('###', $req);
        $order_id = $reqArr[0];
        if (isset($reqArr[1])) {
            $order_amount = $reqArr[1];
        }
        
//         $make_call = callAPI('POST', 'https://www.dafribank.com/api-transaction-detail', $order_id);
// $response = json_decode($make_call, true);

// echo '<pre>';print_r($response);exit;
      echo 'Payment completed for Transaction ID : '.$order_id;
    }

    private function myCurrencyRate($merchant_currency, $user_currency, $amount) {
        if($user_currency == 'NGN'){
            $exchange = Ngnexchange::where('id', 2)->first();
            
            $to = strtolower($merchant_currency);
            $var = $to.'_value';            
            
            $val = $exchange->$var;
            $total = $amount / $val;
            return $val . "###" . $total;
        } else if($merchant_currency == 'NGN'){
            $exchange = Ngnexchange::where('id', 1)->first();
            $to = strtolower($user_currency);
            $var = $to.'_value';            
            
            $val = $exchange->$var;
            $total = $amount / $val;
            return $val . "###" . $total;
        } else{
            $apikey = CURRENCY_CONVERT_API_KEY;

            $query = $merchant_currency . '_' . $user_currency;

            $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
            //https://free.currconv.com/api/v7/convert?q=USD_ZAR&compact=ultra&apiKey=5c638446397b3588a3c6
            $json = file_get_contents($curr_req);
            $obj = json_decode($json, true);
            //print_r($obj);
            $val = floatval($obj[$query]);
            $total = $val * $amount;
            return $val . "###" . $total;
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

    public function merchatWithdrawal(Request $request) {
        $merchant_id = $request->get('merchant_key');
        $user_email = $request->get('user_email');
        $amount = $request->get('amount');
        $remark = $request->get('remark');
        $return_url = $request->get('return_url');
        $order_id = $request->get('order_id');
        
        $base64Req = base64_encode($merchant_id . '###' . $user_email . '###' . $amount . '###' . $remark . '###' . $return_url . '###' . $order_id);
        Session::put('reqwithdrawStr', $base64Req);
        return redirect::to('withdraw-payment');
    }

    public function withdrawPayment() {
        $pageTitle = 'Withdraw Request';

        $reqwithdrawStr = Session::get('reqwithdrawStr');
        $req = base64_decode($reqwithdrawStr);

        $reqArr = explode('###', $req);
        $merchant_id = $reqArr[0];
        $user_email = $reqArr[1];
        $amount = $reqArr[2];
        $remark = $reqArr[3];
        $return_url = $reqArr[4];
        $order_id = $reqArr[5];

        $user_name = $order_amount = $user_id = $user = '';
        $merchant_user = User::where('api_key', $merchant_id)->first();
        
        if(empty($amount)){
            $amount = 0;
        } else{
            if($amount > 0){
                $amount = (abs($amount));
            } else{
                $amount = 0;
            }
        }
        
        if(empty($order_id)){
            $order_id = 'N/A';
        }

        if (empty($merchant_user)) {
//            Session::flash('error_message', 'Invalid Merchant ID');
            $user_id = 'N/A';
            $user_name = 'N/A';
        } else {
            if ($merchant_user->user_type == 'Personal') {
                $merchant_name = strtoupper($merchant_user->first_name . ' ' . $merchant_user->last_name);
            } else if ($merchant_user->user_type == 'Business') {
                $merchant_name = strtoupper($merchant_user->business_name);
            } else if ($merchant_user->user_type == 'Agent' && $merchant_user->first_name != "") {
                $merchant_name = strtoupper($merchant_user->first_name . ' ' . $merchant_user->last_name);
            } else if ($merchant_user->user_type == 'Agent' && $merchant_user->first_name == "") {
                $merchant_name = strtoupper($merchant_user->business_name);
            }
        }

        $user = User::where('email', $user_email)->orWhere('account_number', $user_email)->first();
        //print_r($user);die;
        if (empty($user)) {
//            Session::flash('error_message', 'Invalid User ID');
            $user_id = 'N/A';
            $user_name = 'N/A';
        } else {
            if ($user->user_type == 'Personal') {
                $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
            } else if ($user->user_type == 'Business') {
                $user_name = strtoupper($user->business_name);
            } else if ($user->user_type == 'Agent' && $user->first_name != "") {
                $user_name = strtoupper($user->first_name . ' ' . $user->last_name);
            } else if ($user->user_type == 'Agent' && $user->first_name == "") {
                $user_name = strtoupper($user->business_name);
            }
        }

//        if ($merchant_user->account_category == "Gold") {
//            $fee_name = 'MERCHANT_API_WITHDRAW_FEE_GOLD';
//        } else if ($merchant_user->account_category == "Platinum") {
//            $fee_name = 'MERCHANT_API_WITHDRAW_FEE_PLATINUM';
//        } else if ($merchant_user->account_category == "Enterprises") {
//            $fee_name = 'MERCHANT_API_WITHDRAW_FEE_ENTERPRISE';
//        } else {
//            $fee_name = 'MERCHANT_API_WITHDRAW_FEE_GOLD';
//        }



        // echo $merchant_user->account_category;
        // echo $merchant_user->account_category;
        $input = Input::all();
        if (!empty($input['submit'])) {

            if($merchant_user->api_enable=='N')
            {
            Session::put('error_session_message', "Merchant service is temporary disabled. Please try after some time.");
            return Redirect::to('withdraw-payment');  
            }


            //to calculate the merchant side fee 
              //to calculate the % of merchant side
              if ($merchant_user->account_category == "Gold") {
                $fee_name = 'MERCHANT_API_WITHDRAW_FEE_GOLD';
            } else if ($merchant_user->account_category == "Platinum") {
                $fee_name = 'MERCHANT_API_WITHDRAW_FEE_PLATINUM';
            } else if ($merchant_user->account_category == "Enterprises") {
                $fee_name = 'MERCHANT_API_WITHDRAW_FEE_ENTERPRISE';
            } else {
                $fee_name = 'MERCHANT_API_WITHDRAW_FEE_GOLD';
            }
           
            $widrawal_amt = 0;
            $conversion_fee_amount=0;
            if ($user->currency != $merchant_user->currency) {
                $convr_fee_name=$merchant_user->currency!="NGN" && $user->currency!="NGN" ? 'MERCHANT_CONVERSION_FEE' : 'NGN_MERCHANT_CONVERSION_FEE';
                $amount_agent_currency = $this->convertCurrency($user->currency, $merchant_user->currency, $amount);
                $amount_agent_currencyArr = explode("##", $amount_agent_currency);
                $agent_amount = $amount_agent_currencyArr[0];
                $agent_converstion_rate = $amount_agent_currencyArr[1];
                $widrawal_amt = $agent_amount;
                $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                $conversion_fee = $fees_convr->fee_value;
                $conversion_fee_amount = ($widrawal_amt * $conversion_fee) / 100;
             } else {
                 $widrawal_amt = $amount;
             }

             $fees = Fee::where('fee_name', $fee_name)->first();
             $fees_amount = ($widrawal_amt * $fees->fee_value) / 100;
             $senderAmount = $fees_amount + $conversion_fee_amount;

            $conversion_fee_amount = 0;
            /* if ($user->currency == $merchant_user->currency) {

              $fees = Fee::where('fee_name', $fee_name)->first();
              $fees_amount = ($amount * $fees->fee_value) / 100;
              $total_amount = $amount + $fees_amount;
              } else {
              $amount_user_currency = $this->myCurrencyRate($merchant_user->currency, $user->currency, $amount);
              $converArr = explode("###", $amount_user_currency);
              $conert_amount = $converArr[1];

              $fees = Fee::where('fee_name', $fee_name)->first();
              $conversion_fee_amount = $conert_amount;

              $fees_amount = ($amount * $fees->fee_value) / 100;
              $total_amount = $amount + $fees_amount;
              } */
//            if ($merchant_user->wallet_amount >= $amount) {
                $usrWallet = $merchant_user->wallet_amount;

                $remarkMsg = $remark;

                $remarkMsg1 = '';
                if ($remarkMsg != '') {
                    $remarkMsg1 = 'Payout Instructions : ' . $remarkMsg;
                }

                $refrence_id = $order_id;
                $trans = new Transaction([
                    "user_id" => $merchant_user->id,
                    "receiver_id" => $user->id,
                    "amount" => $amount,
                    "fees" => 0.00,
                    "receiver_fees" => 0.00,
                    "sender_fees" => 0.00,
                    "currency" => $user->currency,
                    "trans_type" => 2,
                    "trans_to" => 'Dafri_Wallet',
                    "trans_for" => 'Merchant_Withdraw',
                    "refrence_id" => $refrence_id,
                    "user_close_bal" => $usrWallet,
                    "receiver_close_bal"=>$user->wallet_amount,
                    "real_value" => $amount,
                    "billing_description" => $remarkMsg1,
                    "status" => 2,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
              //  print_r($trans); die;
                $trans->save();    

                $emailSubject = "DafriBank Digital | Withdrawal request received for amount " . $user->currency . ' ' . $amount;
                $notif = new Notification([
                    'user_id' => $merchant_user->id,
                    'notif_subj' => $emailSubject,
                    'notif_body' => $emailSubject,
                    'created_at' => date('Y-m-d H:i:s'),   
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $notif->save(); 

                $TransId = $trans->id;

                $wrq = new WithdrawRequest([
                    'user_id' => $user->id,
                    'req_type' => 'Merchant',
                    'user_name' => $user_name,
                    'agent_id' => $merchant_user->id,
                    'amount' => $amount,
                    'remark' => $remarkMsg,
                    'fees'=>$senderAmount,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $wrq->save();
                $withdrawReqID = $wrq->id;

                $inactvAmnt = new InactiveAmount([
                    'user_id' => $user->id,
                    'withdraw_req_id' => $withdrawReqID,
                    'trans_id' => $TransId,
                    'amount' => $amount,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $inactvAmnt->save();

                $userInfo = $user;
                if ($userInfo->user_type == 'Personal') {
                    $user_name = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                } else if ($userInfo->user_type == 'Business') {
                    $user_name = strtoupper($userInfo->business_name);
                } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name != "") {
                    $user_name = strtoupper($userInfo->first_name . ' ' . $userInfo->last_name);
                } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name == "") {
                    $user_name = strtoupper($userInfo->business_name);
                }

                $uname = strtoupper(trim($user_name));

//                if ($userInfo->user_type == 'Business') {
//                    $uname = strtoupper(trim($userInfo->business_name));
//                } else {
//                    $uname = strtoupper(trim($userInfo->first_name) . ' ' . trim($userInfo->last_name));
//                }
                $emailId = $merchant_user->email;
                //$emailSubject = 'OTP For Payment';                              
                $emailSubject = 'DafriBank Digital | Merchant Withdrawal Request Received';
                $emailData['subjects'] = $emailSubject;

                $emailData['amount'] = $user->currency . ' ' . $amount;
                $emailData['user_name'] = $uname;
                $emailData['userName'] = strtoupper($merchant_name);
                $emailData['TransId'] = $TransId;

                Mail::send('emails.apirequestsend', $emailData, function ($message)use ($emailData, $emailId) {
                    $message->to($emailId, $emailId)
                            ->subject($emailData['subjects']);
                });
                
                $notif = new Notification([
                    'user_id' => $user->id,
                    'notif_subj' => $emailSubject,
                    'notif_body' => $emailSubject,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $notif->save();

                $base64Result = base64_encode($TransId);
                return Redirect::to($return_url . '/' . $base64Result);
//            } else {
//                Session::put('error_session_message', "Merchant Don't have sufficient balance.");
//            }
        }



        return view('homes.withdrawPayment', ['title' => $pageTitle, 'order_id' => $order_id, 'amount' => $amount, 'user_id' => $user_id, 'user_name' => $user_name, 'merchant_user' => $merchant_user, 'merchant_name' => $merchant_name, 'remark' => $remark, 'user' => $user]);
    }

    public function apiTransactionDetail(Request $request) {
        $post = $request->all();
        $traction_id = $post['transaction_id'];
        $traction_responce = Transaction::where('id', $traction_id)->first();
        $trans_type = $status = '';
        if ($traction_responce->trans_type == '1') {
            $trans_type = 'Credit';
        } else if ($traction_responce->trans_type == '2') {
            $trans_type = 'Debit';
        } else if ($traction_responce->trans_type == '3') {
            $trans_type = 'Topup';
        } else if ($traction_responce->trans_type == '4') {
            $trans_type = 'Request';
        }
        //  1=Success;2=PENDING;3=Cancelled;4=Failed;5=Error;6=Abandoned;7=PendingInvestigation 
        if ($traction_responce->status == '1') {
            $status = 'Success';
        } else if ($traction_responce->status == '2') {
            $status = 'Pending';
        } else if ($traction_responce->status == '3') {
            $status = 'Cancelled';
        } else if ($traction_responce->status == '4') {
            $status = 'Failed';
        } else if ($traction_responce->status == '5') {
            $status = 'Error';
        } else if ($traction_responce->status == '6') {
            $status = 'Abandoned';
        } else if ($traction_responce->status == '7') {
            $status = 'PendingInvestigation';
        } else {
            
        }


        $transaction_final = array();
        $transaction_final['transaction_id'] = $traction_responce->id;
        $transaction_final['order_id'] = $traction_responce->refrence_id;  
        //$transaction_final['amount'] = $traction_responce->amount;
        //$transaction_final['fees'] = $traction_responce->fees;
        //$transaction_final['receiver_fees'] = $traction_responce->receiver_fees != '' ? $traction_responce->receiver_fees:0.00;
        //$transaction_final['sender_fees'] = $traction_responce->sender_fees != '' ? $traction_responce->sender_fees:0.00;
        //$transaction_final['currency'] = $traction_responce->currency;
        //$transaction_final['trans_type'] = $trans_type;
        // $transaction_final['billing_description'] = $traction_responce->billing_description;
        //$transaction_final['reference_note'] =$traction_responce->reference_note != '' ? $traction_responce->reference_note:'';
        $transaction_final['status'] = $status;
        $transaction_final['date_time'] = date('d-m-Y H:i:s', strtotime($traction_responce->created_at));


        echo json_encode($transaction_final);
        die;
    }

    public function refToId(Request $request) {
        $post = $request->all();
        $ref_id = $post['ref_id'];
        $transaction_id = Transaction::where('refrence_id', $ref_id)->value('id');
        if(!$transaction_id){
             return "no transaction";
        }
        return $transaction_id;

    }



    public function resentVerifyOtpAPI(Request $request) {
        if (!empty($request->has('user_id'))) {
            $otp = mt_rand(100000, 999999);
            $verifyCode = $this->encpassword($otp);
            User::where('id', $request->user_id)->update(['verify_code' => $verifyCode, 'otp_time' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $userInfo = User::where('id', $request->user_id)->first();

            $emailId = $userInfo->email;
            // $emailSubject = 'DafriBank Digital - OTP Requested';
            // $emailData['subject'] = $emailSubject;
            // if ($userInfo->user_type == 'Business') {
            //     $uname = strtoupper(trim($userInfo->business_name));
            // }
            //  else {
            //     $uname = strtoupper(trim($userInfo->first_name) . ' ' . trim($userInfo->last_name));
            // }
            if ($userInfo->user_type == 'Personal') {
                $uname = strtoupper($userInfo->first_name) . " " . ucwords($userInfo->last_name);
            } else if ($userInfo->user_type == 'Business') {
                $uname = strtoupper($userInfo->business_name);
            } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name == "") {
                $uname = strtoupper($userInfo->business_name);
            } else if ($userInfo->user_type == 'Agent' && $userInfo->first_name != "") {
                $uname = strtoupper($userInfo->first_name) . " " . ucwords($userInfo->last_name);
            }


            // $id = TWILIO_ID;
            // $token = TWILIO_TOKEN;
            // $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
            // $from = TWILIO_NUMBER;
            // $to = $request->phone;
            // //$body = 'Dear User, Use '.$otp.' to verify your phone number.';
            // $body = 'Dear Customer, ' . $otp . ' is your One Time Password to authenticate your login to DafriBank, valid for next 15 min. Don\'t share it with anyone for security reasons.';
            // $data = array(
            //     'From' => $from,
            //     'To' => $to,
            //     'Body' => $body,
            // );
            // $post = http_build_query($data);
            // //            echo '<pre>';print_r($post);exit;
            // $x = curl_init($url);
            // curl_setopt($x, CURLOPT_POST, true);
            // curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
            // curl_setopt($x, CURLOPT_POSTFIELDS, $post);
            // $y = curl_exec($x);
            // curl_close($x);
            //            $otp = mt_rand(100000, 999999);
            //            $verifyCode = $this->encpassword($otp);
            // $emailData['otp'] = $otp;
            // User::where('id', $userInfo->id)->update(array('verify_code' => $verifyCode));
            $emailSubject = 'OTP For Payment';
            $emailData['subjects'] = $emailSubject;
            $emailData['emailId'] = $userInfo->email;
            $emailData['otp'] = $otp;
            $emailData['userName'] = strtoupper($uname);

            Mail::send('emails.paymentOTP', $emailData, function ($message)use ($emailData, $emailId) {
                $message->to($emailId, $emailId)
                        ->subject($emailData['subjects']);
            });
            //            echo '<pre>';print_r($y);exit;
            //echo $emailId;
            echo 1;
            //echo $y.' Phone: '.$request->phone;
            exit;
        }
    }
    
    // private function convertCurrency($toCurrency, $frmCurrency, $amount) {
    //     $apikey = CURRENCY_CONVERT_API_KEY;
    //     $query = $toCurrency . "_" . $frmCurrency;
    //     $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;

    //     $json = file_get_contents($curr_req);
    //     $obj = json_decode($json, true);
    //     $val = floatval($obj[$query]);
    //     $total = $val * $amount;
    //     return $total . "##" . $val;
    // }

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
                    $curr_req = "https://free.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
        
                    $json = file_get_contents($curr_req);
                    $obj = json_decode($json, true);
                    $val = floatval($obj[$query]);
                    $total = $val * $amount;
                    return $total . "##" . $val;
                }
            }


  private function checkUserTransLimit($user_id, $user_category, $user_type, $amount) {
        if ($user_type == 'Personal') {
            $cateFor = 1;
        } else if ($user_type == 'Business') {
            $cateFor = 2;
        }

        $user = User::where('id', $user_id)->first();
        if($user_type!='Agent')
        { 
        $getLimit=WalletLimitUser::where('user_id',$user_id)->orderBy('id','desc')->first();
        if(!isset($getLimit->id))
        {   
        $getLimit = Walletlimit::where('account_category', $user_category)->where('category_for', $cateFor)->first();
        }
        $dateTrans = date('Y-m-d');
        $startDate = $dateTrans . ' 00:00:00';
        $endDate = $dateTrans . ' 23:59:59';
        //DB::enableQueryLog(); 
        $DailyTrans = Transaction::where('user_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($startDate, $endDate))->sum('amount');
        $DailyTrans = $DailyTrans + $amount;
        $DailyTransUSD = $this->fetchCurrencyRate($user->currency, $DailyTrans);
        if ($DailyTransUSD > $getLimit->daily_limit) {
            return "false###Sorry, Your daily spending limit is over.";
        }

        $mondaydt = date('Y-m-d', strtotime('monday this week'));
        $sundaydt = date('Y-m-d', strtotime('sunday this week'));
        $thisModay = $mondaydt . ' 00:00:00';
        $thisSunday = $sundaydt . ' 23:59:59';
        $WeekTrans = Transaction::where('user_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($thisModay, $thisSunday))->sum('amount');
        $WeekTrans = $WeekTrans + $amount;
        $WeekTransUSD = $this->fetchCurrencyRate($user->currency, $WeekTrans);
        if ($WeekTransUSD > $getLimit->week_limit) {
            return "false###Sorry, Your weekly spending limit is over.";
        }

        $monthStartDate = date('Y-m-01');
        $monthStartDate = $monthStartDate . ' 00:00:00';
        $monthEndDate = date('Y-m-t');
        $monthEndDate = $monthEndDate . ' 23.59:59';
        $MonthTrans = Transaction::where('user_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($monthStartDate, $monthEndDate))->sum('amount');
        $MonthTrans = $MonthTrans + $amount;
        $MonthTransUSD = $this->fetchCurrencyRate($user->currency, $MonthTrans);
        if ($MonthTransUSD > $getLimit->month_limit) {
            return "false###Sorry, Your monthly spending limit is over.";
        }
        }
        elseif($user_type=='Agent')
        {
        $getLimit=WalletLimitUser::where('user_id',$user_id)->orderBy('id','desc')->first();
        if(!isset($getLimit->id))
        {    
        $getLimit = AgentsTransactionLimit::where('agent_id', $user_id)->first();
        }

        if(!empty($getLimit))
        {
        $daily_limit=$getLimit->trans_limit;
        }
        else{
        $limit = Agentlimit::where('id', 1)->first();
        $daily_limit = $limit->daily_limit;
        }

        $dateTrans = date('Y-m-d');
        $startDate = $dateTrans . ' 00:00:00';
        $endDate = $dateTrans . ' 23:59:59';
        //DB::enableQueryLog(); 
        $DailyTrans = Transaction::where('user_id', $user_id)->where('trans_type', 2)->whereBetween('created_at', array($startDate, $endDate))->sum('amount');
        $DailyTrans = $DailyTrans + $amount;
        $DailyTransUSD = $this->fetchCurrencyRate($user->currency, $DailyTrans);
        if ($DailyTransUSD > $daily_limit) {
            return "false###Sorry, Your daily spending limit is over.";
        }
        }

        return true;
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


    private function myCurrencyRate1($currency, $amount) {

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

    public function get_content_blog()
    {

        $activetab="test";
        $data = file_get_contents("https://medium.com/@dafribank");
        $html_sourcecode_get = htmlentities($data);
        return view('homes.get_content_blog', ['title' =>'test', 'page_heading' =>'test', $activetab => 1,'html'=>$html_sourcecode_get]);
    }


}

?>