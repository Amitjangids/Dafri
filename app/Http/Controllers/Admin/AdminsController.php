<?php 
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Cookie;
use Session;
use Redirect;
use Input;
//use Request;
use Validator;
use App\Transaction;
use DB;
use IsAdmin;
use App\Role;
use App\Permission;
use App\User;
use App\Models\Country;
use App\Admin;
use Mail;
use Carbon\Carbon;
use App\Mail\SendMailable;
use App\DbaTransaction;

class AdminsController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->middleware('adminlogedin', ['only' => ['login', 'forgotPassword']]);
        $this->middleware('is_adminlogin', ['except' => ['logout', 'login','forgotPassword']]);     
    }

    public function login() { 
        $pageTitle = 'Admin Login';
        $input = Input::all();
        if (!empty($input)) {           
            $rules = array(
                'username' => 'required',
                'password' => 'required',
                'g-recaptcha-response' => 'required'
            );  
            $validator = Validator::make($input, $rules);             
            if ($validator->fails()) {
                return Redirect::to('/admin/login')->withErrors($validator)->withInput(Input::except('password'));
            }else { 
               $adminInfo = DB::table('admins')->where('username', $input['username'])->first();               
               if (!empty($adminInfo)) {
                    if (password_verify($input['password'], $adminInfo->password)) {
                        if ($adminInfo->status == 0) {
                            $error = 'Your account got temporary disabled.';
                        }else{
                            if (isset($input['remember']) && $input['remember'] == '1') {
                                Cookie::queue('admin_username', $adminInfo->username, time() + 60 * 60 * 24 * 7, "/");
                                Cookie::queue('admin_password', $input['password'], time() + 60 * 60 * 24 * 7, "/");
                                Cookie::queue('admin_remember', '1', time() + 60 * 60 * 24 * 100, "/");
                            } else {
                                Cookie::queue('admin_username', '', time() + 60 * 60 * 24 * 7, "/");
                                Cookie::queue('admin_password', '', time() + 60 * 60 * 24 * 7, "/");
                                Cookie::queue('admin_remember', '', time() + 60 * 60 * 24 * 7, "/");
                            }                            
                            Session::put('adminid', $adminInfo->id);
                            Session::put('admin_username', $adminInfo->username);
                            Session::put('admin_role', $adminInfo->role_id);
                            $usertype = 'Subadmin';
                            if($adminInfo->id == 1){
                                $usertype = 'Admin';
                            }                            
                            
                            Session::put('admin_usertype', $usertype);
                            return Redirect::to('admin/admins/dashboard');
                        }
                    } else {
                        $error = 'Invalid username or password.';
                    }
               }else{
                   $error = 'Invalid username or password.';
               }               
               //return Redirect::to('/admin/login')->withErrors($error)->withInput(Request::except('password'));
               return Redirect::to('/admin/login')->withErrors($error)->withInput(Input::except('password'));
            }
        }
        return view('admin.admins.login', ['title' => $pageTitle]);
    }
    
    public function forgotPassword() { 
        $pageTitle = 'Admin Forgot Password';
        $input = Input::all();
        if (!empty($input)) {           
            $rules = array(
                'email' => 'required|email'
            );            
            $validator = Validator::make($input, $rules);             
            if ($validator->fails()) {
                return Redirect::to('/admin/admins/forgot-password')->withErrors($validator);
            } else {                
               $adminInfo = DB::table('admins')->where('email', $input['email'])->first();               
               if (!empty($adminInfo)) {
                    $plainPassword  = $this->getRandString(8);
                    $new_password = $this->encpassword($plainPassword);
                    DB::table('admins')->where('id', $adminInfo->id)->update(array('password' => $new_password));

                    $username = $adminInfo->username;
                    $emailId =  $adminInfo->email;
                    /*$emailTemplate = DB::table('emailtemplates')->where('id', 1)->first();
                    $toRepArray = array('[!email!]', '[!username!]', '[!password!]', '[!HTTP_PATH!]', '[!SITE_TITLE!]');
                    $fromRepArray = array($emailId, $username, $plainPassword, HTTP_PATH, SITE_TITLE);
                    $emailSubject = str_replace($toRepArray, $fromRepArray, $emailTemplate->subject);
                    $emailBody = str_replace($toRepArray, $fromRepArray, $emailTemplate->template);*/
					$emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="'.HTTP_PATH.'/public/img/dafribank-logo-black.png" width="150"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Dear</span> Admin,</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">You are receiving this email because we received a password reset request for your DafrrBank Admin Account.<br><br>Please find your new password below:<br><br>Email Address: '.$emailId.'<br>Username: '.$username.'<br>Password: '.$plainPassword.'<br><br><a href="'.HTTP_PATH.'/admin/admins/login">Click Here To Login</a>.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="'.HTTP_PATH.'/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><a href="'.HTTP_PATH.'/business-login" style="color: #1381D0; text-decoration: none; font-size: 18px">Head to your dashboard </a> to see more information on this payment<p style="font-size: 16px; color:#8E8E8E ">Have questions or help ? Call 011 568 5053 or visit our <a href="'.HTTP_PATH.'/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page.</p></td></tr></table></td></tr></tbody></table></body></html>';
					$emailSubject = 'Admin Forgot Password';
                     $emailData['subject'] = $emailSubject;                    
                     $emailData['username'] = $adminInfo->username;                    
                     $emailData['emailId'] = $adminInfo->email;                    
                     $emailData['plainPassword'] = $plainPassword;                    
//                    Mail::to($emailId)->send(new SendMailable($emailBody,$emailSubject,Null));
                    Mail::send('admin.email.forgotpassword', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subject']);
                    });

                    Session::flash('success_message', "A new password has been sent to your email address.");
                    return Redirect::to('admin/admins/login');
               }else{
                   $error = 'Invalid email address, please enter correct email address.';
               }               
               return Redirect::to('/admin/admins/forgot-password')->withErrors($error);
            }
        }
        return view('admin.admins.forgotPassword', ['title' => $pageTitle]);
    }
    
    public function logout() {
        session_start();
        session_destroy();
        Session::forget('adminid');
        Session::save();
        Session::flash('success_message', "Logout successfully.");
        return Redirect::to('admin/admins/login');
    }

    public function dashboard() {
        $pageTitle = 'Admin Dashboard'; 
        $dadhboardData = array();    
        $query = new User();
        $query1 = new User();
        $role = 'Agent';
        $query = $query->orWhere(function ($q) use ($role) {
            $q->where('user_type', 'Personal')
                    ->orWhere(function($q) use ($role){
                $q = $q->where('user_type', $role)->where('first_name', '!=', '');
            });
        });
        $query1 = $query1->orWhere(function ($q) use ($role) {
            $q->where('id','!=', 1)->where('user_type', 'Business')
                    ->orWhere(function($q) use ($role){
                $q = $q->where('user_type', $role)->where('business_name','!=', '');
            });
        });

        $last_7_days = date('Y-m-d', strtotime("-1 week"));
        $last_month = date('Y-m-d', strtotime("-1 month"));
        $to=date('Y-m-d');

        //to find out the total dba swap
        $total_dba_credit_swap_count = DbaTransaction::where(function ($q){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->where('trans_for','SWAP');
            })->sum('real_value');  

    

        //to find out the total dba swap weekly
        DB::enableQueryLog();
        $total_dba_credit_swap_weekly_count = DbaTransaction::where(function ($q) use($to,$last_7_days){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->whereDate('created_at','>=',$last_7_days);
            $q->whereDate('created_at','<=',$to);
            $q->where('trans_for','SWAP');
            })->sum('real_value');
         //   dd(DB::getQueryLog());  

         $total_dba_credit_swap_monthly_count = DbaTransaction::where(function ($q) use($to,$last_month){ 
             $q->whereIn('status',[1,7]);
             $q->where('trans_type', 1);
             $q->whereDate('created_at','>=',$last_month);
             $q->whereDate('created_at','<=',$to);
             $q->where('trans_for','SWAP');
             })->sum('real_value');
          //   dd(DB::getQueryLog());  

        //total available dba
        $total_dba_wallet= User::where(function ($q){ 
            $q->where('id','!=', 1);
            })->sum('dba_wallet_amount');    
         
        //total dba hold    
        $total_dba_hold_wallet= User::where(function ($q){ 
            $q->where('id','!=', 1);
            })->sum('dba_hold_wallet_amount');   
            
            
        $total_dba_in_wallet=number_format($total_dba_wallet+$total_dba_hold_wallet, 2, '.', '');
        $total_dba_credit_swap_count=number_format($total_dba_credit_swap_count, 2, '.', '');

        $dadhboardData['users_count'] = $query->count();          
        $dadhboardData['business_count'] = $query1->count();      
        
        //to find out the weekely dba
        $days_array=array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun');
        $datetime = \DateTime::createFromFormat('d/m/Y',date('d/m/Y'));
        $day=$datetime->format('D');
        $day_index=array_search($day,$days_array)-1;
        $weekly_array=array();
         foreach($days_array as $key=>$value)
         {
           $previous_date=date('Y-m-d', strtotime("-".$day_index." Day"));
           $weekly_array[$previous_date]=$value;
           $day_index--;
         }

         //to find out the dba sum of daily
         $day_sum_array=array();
         $max_sum_week=0;
         foreach($weekly_array as $key=>$day)
         {

            $to=$key;
            $from=$to;
            $day_sum = DbaTransaction::where(function ($q) use($to,$from){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->whereDate('created_at','>=',$from);
            $q->whereDate('created_at','<=',$to);
            $q->where('trans_for','SWAP');
            })->sum('real_value');
            if($day_sum!='0')
            {
            $day_sum_array[$day]=number_format($day_sum, 2, '.', '');
            if($max_sum_week < $day_sum)
            {
            $max_sum_week=$day_sum;   
            }
            }
            else{
            $day_sum_array[$day]='null'; 
            }
        }

        //to find out the month sum
        $month_sum_array=array();
        $max_sum_month=0;
        for($i=1;$i<=12;$i++)
        {
            $monthNum  = $i;
            $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');
            $month_sum = DbaTransaction::where(function ($q) use($i){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->whereMonth('created_at','=',$i);
            $q->whereYear('created_at','=',date('Y'));
            $q->where('trans_for','SWAP');
            })->sum('real_value');
            if($month_sum!='0')
            {
            $month_sum_array[$monthName]=number_format($month_sum, 2, '.', '');
            if($max_sum_month < $month_sum)
            {
            $max_sum_month=$month_sum;   
            }
            }
            else{
            $month_sum_array[$monthName]='null'; 
            }
        }
        return view('admin.admins.dashboard', ['title'=>$pageTitle, 'actdashboard'=>1, 'dadhboardData'=>$dadhboardData,'total_dba_credit_swap_count'=>$total_dba_credit_swap_count,'total_dba_in_wallet'=>$total_dba_in_wallet,'total_dba_credit_swap_weekly_count'=>$total_dba_credit_swap_weekly_count,'total_dba_credit_swap_monthly_count'=>$total_dba_credit_swap_monthly_count,'day_sum_array'=>$day_sum_array,'month_sum_array'=>$month_sum_array,'max_sum_month'=>$max_sum_month,'max_sum_week'=>$max_sum_week]);

        
    }

    public function dashboardCopy() {
        $pageTitle = 'Admin Dashboard'; 
        $dadhboardData = array();    
        $query = new User();
        $query1 = new User();
        $role = 'Agent';
        $query = $query->orWhere(function ($q) use ($role) {
            $q->where('user_type', 'Personal')
                    ->orWhere(function($q) use ($role){
                $q = $q->where('user_type', $role)->where('first_name', '!=', '');
            });
        });
        $query1 = $query1->orWhere(function ($q) use ($role) {
            $q->where('id','!=', 1)->where('user_type', 'Business')
                    ->orWhere(function($q) use ($role){
                $q = $q->where('user_type', $role)->where('business_name','!=', '');
            });
        });

        $last_7_days = date('Y-m-d', strtotime("-1 week"));
        $last_month = date('Y-m-d', strtotime("-1 month"));
        $to=date('Y-m-d');

        //to find out the total dba swap
        $total_dba_credit_swap_count = DbaTransaction::where(function ($q){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->where('trans_for','SWAP');
            })->sum('real_value');  

    

        //to find out the total dba swap weekly
        DB::enableQueryLog();
        $total_dba_credit_swap_weekly_count = DbaTransaction::where(function ($q) use($to,$last_7_days){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->whereDate('created_at','>=',$last_7_days);
            $q->whereDate('created_at','<=',$to);
            $q->where('trans_for','SWAP');
            })->sum('real_value');
         //   dd(DB::getQueryLog());  

         $total_dba_credit_swap_monthly_count = DbaTransaction::where(function ($q) use($to,$last_month){ 
             $q->whereIn('status',[1,7]);
             $q->where('trans_type', 1);
             $q->whereDate('created_at','>=',$last_month);
             $q->whereDate('created_at','<=',$to);
             $q->where('trans_for','SWAP');
             })->sum('real_value');
          //   dd(DB::getQueryLog());  

        //total available dba
        $total_dba_wallet= User::where(function ($q){ 
            $q->where('id','!=', 1);
            })->sum('dba_wallet_amount');    
         
        //total dba hold    
        $total_dba_hold_wallet= User::where(function ($q){ 
            $q->where('id','!=', 1);
            })->sum('dba_hold_wallet_amount');   
            
            
        $total_dba_in_wallet=number_format($total_dba_wallet+$total_dba_hold_wallet, 2, '.', '');
        $total_dba_credit_swap_count=number_format($total_dba_credit_swap_count, 2, '.', '');

        $dadhboardData['users_count'] = $query->count();          
        $dadhboardData['business_count'] = $query1->count();      
        
        //to find out the weekely dba
        $days_array=array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun');
        $datetime = \DateTime::createFromFormat('d/m/Y',date('d/m/Y'));
        $day=$datetime->format('D');
        $day_index=array_search($day,$days_array)-1;
        $weekly_array=array();
         foreach($days_array as $key=>$value)
         {
           $previous_date=date('Y-m-d', strtotime("-".$day_index." Day"));
           $weekly_array[$previous_date]=$value;
           $day_index--;
         }

         //to find out the dba sum of daily
         $day_sum_array=array();
         $max_sum_week=0;
         foreach($weekly_array as $key=>$day)
         {

            $to=$key;
            $from=$to;
            $day_sum = DbaTransaction::where(function ($q) use($to,$from){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->whereDate('created_at','>=',$from);
            $q->whereDate('created_at','<=',$to);
            $q->where('trans_for','SWAP');
            })->sum('real_value');
            if($day_sum!='0')
            {
            $day_sum_array[$day]=number_format($day_sum, 2, '.', '');
            if($max_sum_week < $day_sum)
            {
            $max_sum_week=$day_sum;   
            }
            }
            else{
            $day_sum_array[$day]='null'; 
            }
        }

        //to find out the month sum
        $month_sum_array=array();
        $max_sum_month=0;
        for($i=1;$i<=12;$i++)
        {
            $monthNum  = $i;
            $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');
            $month_sum = DbaTransaction::where(function ($q) use($i){ 
            $q->whereIn('status',[1,7]);
            $q->where('trans_type', 1);
            $q->whereMonth('created_at','=',$i);
            $q->whereYear('created_at','=',date('Y'));
            $q->where('trans_for','SWAP');
            })->sum('real_value');
            if($month_sum!='0')
            {
            $month_sum_array[$monthName]=number_format($month_sum, 2, '.', '');
            if($max_sum_month < $month_sum)
            {
            $max_sum_month=$month_sum;   
            }
            }
            else{
            $month_sum_array[$monthName]='null'; 
            }
        }
        return view('admin.admins.dashboard_copy', ['title'=>$pageTitle, 'actdashboard'=>1, 'dadhboardData'=>$dadhboardData,'total_dba_credit_swap_count'=>$total_dba_credit_swap_count,'total_dba_in_wallet'=>$total_dba_in_wallet,'total_dba_credit_swap_weekly_count'=>$total_dba_credit_swap_weekly_count,'total_dba_credit_swap_monthly_count'=>$total_dba_credit_swap_monthly_count,'day_sum_array'=>$day_sum_array,'month_sum_array'=>$month_sum_array,'max_sum_month'=>$max_sum_month,'max_sum_week'=>$max_sum_week]);

        
    }

    public function dashboardTab()
    {
        $input = Input::all();
        $tab=$input['act_tab'];
        if($tab=='dashboard')
        {
            $dadhboardData = array();    
            $query = new User();
            $query1 = new User();
            $role = 'Agent';
            $query = $query->orWhere(function ($q) use ($role) {
                $q->where('user_type', 'Personal')
                        ->orWhere(function($q) use ($role){
                    $q = $q->where('user_type', $role)->where('first_name', '!=', '');
                });
            });
            $query1 = $query1->orWhere(function ($q) use ($role) {
                $q->where('id','!=', 1)->where('user_type', 'Business')
                        ->orWhere(function($q) use ($role){
                    $q = $q->where('user_type', $role)->where('business_name','!=', '');
                });
            });
    
            $last_7_days = date('Y-m-d', strtotime("-1 week"));
            $last_month = date('Y-m-d', strtotime("-1 month"));
            $to=date('Y-m-d');
    
            //to find out the total dba swap
            $total_dba_credit_swap_count = DbaTransaction::where(function ($q){ 
                $q->whereIn('status',[1,7]);
                $q->where('trans_type', 1);
                $q->where('trans_for','SWAP');
                })->sum('real_value');  
    
        
    
            //to find out the total dba swap weekly
            DB::enableQueryLog();
            $total_dba_credit_swap_weekly_count = DbaTransaction::where(function ($q) use($to,$last_7_days){ 
                $q->whereIn('status',[1,7]);
                $q->where('trans_type', 1);
                $q->whereDate('created_at','>=',$last_7_days);
                $q->whereDate('created_at','<=',$to);
                $q->where('trans_for','SWAP');
                })->sum('real_value');
             //   dd(DB::getQueryLog());  
    
             $total_dba_credit_swap_monthly_count = DbaTransaction::where(function ($q) use($to,$last_month){ 
                 $q->whereIn('status',[1,7]);
                 $q->where('trans_type', 1);
                 $q->whereDate('created_at','>=',$last_month);
                 $q->whereDate('created_at','<=',$to);
                 $q->where('trans_for','SWAP');
                 })->sum('real_value');
              //   dd(DB::getQueryLog());  
    
            //total available dba
            $total_dba_wallet= User::where(function ($q){ 
                $q->where('id','!=', 1);
                })->sum('dba_wallet_amount');    
             
            //total dba hold    
            $total_dba_hold_wallet= User::where(function ($q){ 
                $q->where('id','!=', 1);
                })->sum('dba_hold_wallet_amount');   
                
                
            $total_dba_in_wallet=number_format($total_dba_wallet+$total_dba_hold_wallet, 2, '.', '');
            $total_dba_credit_swap_count=number_format($total_dba_credit_swap_count, 2, '.', '');
    
            $dadhboardData['users_count'] = $query->count();          
            $dadhboardData['business_count'] = $query1->count();      
            
            //to find out the weekely dba
            $days_array=array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun');
            $datetime = \DateTime::createFromFormat('d/m/Y',date('d/m/Y'));
            $day=$datetime->format('D');
            $day_index=array_search($day,$days_array)-1;
            $weekly_array=array();
             foreach($days_array as $key=>$value)
             {
               $previous_date=date('Y-m-d', strtotime("-".$day_index." Day"));
               $weekly_array[$previous_date]=$value;
               $day_index--;
             }
    
             //to find out the dba sum of daily
             $day_sum_array=array();
             $max_sum_week=0;
             foreach($weekly_array as $key=>$day)
             {
    
                $to=$key;
                $from=$to;
                $day_sum = DbaTransaction::where(function ($q) use($to,$from){ 
                $q->whereIn('status',[1,7]);
                $q->where('trans_type', 1);
                $q->whereDate('created_at','>=',$from);
                $q->whereDate('created_at','<=',$to);
                $q->where('trans_for','SWAP');
                })->sum('real_value');
                if($day_sum!='0')
                {
                $day_sum_array[$day]=number_format($day_sum, 2, '.', '');
                if($max_sum_week < $day_sum)
                {
                $max_sum_week=$day_sum;   
                }
                }
                else{
                $day_sum_array[$day]='null'; 
                }
            }
    
            //to find out the month sum
            $month_sum_array=array();
            $max_sum_month=0;
            for($i=1;$i<=12;$i++)
            {
                $monthNum  = $i;
                $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
                $monthName = $dateObj->format('F');
                $month_sum = DbaTransaction::where(function ($q) use($i){ 
                $q->whereIn('status',[1,7]);
                $q->where('trans_type', 1);
                $q->whereMonth('created_at','=',$i);
                $q->whereYear('created_at','=',date('Y'));
                $q->where('trans_for','SWAP');
                })->sum('real_value');
                if($month_sum!='0')
                {
                $month_sum_array[$monthName]=number_format($month_sum, 2, '.', '');
                if($max_sum_month < $month_sum)
                {
                $max_sum_month=$month_sum;   
                }
                }
                else{
                $month_sum_array[$monthName]='null'; 
                }
            }
            return view('admin.admins.dashboard_tab', ['dadhboardData'=>$dadhboardData,'total_dba_credit_swap_count'=>$total_dba_credit_swap_count,'total_dba_in_wallet'=>$total_dba_in_wallet,'total_dba_credit_swap_weekly_count'=>$total_dba_credit_swap_weekly_count,'total_dba_credit_swap_monthly_count'=>$total_dba_credit_swap_monthly_count,'day_sum_array'=>$day_sum_array,'month_sum_array'=>$month_sum_array,'max_sum_month'=>$max_sum_month,'max_sum_week'=>$max_sum_week]);    
        }
        elseif($tab=='fee')
        {
            $isPermitted = $this->validatePermission(Session::get('admin_role'), 'mi-report');
            if ($isPermitted == false) {
                return 1; 
            }

        return view('admin.admins.fee');
        }
        elseif($tab=='product')
        {
            $isPermitted = $this->validatePermission(Session::get('admin_role'), 'mi-report');
            if ($isPermitted == false) {
                return 1; 
            }

        return view('admin.admins.product');
        }
        elseif($tab=='customer')
        {
            $isPermitted = $this->validatePermission(Session::get('admin_role'), 'mi-report');
            if ($isPermitted == false) {
                return 1; 
            }
            return view('admin.admins.customer_tab');   
        }
        elseif($tab=='country')
        {
            $isPermitted = $this->validatePermission(Session::get('admin_role'), 'mi-report');
            if ($isPermitted == false) {
                return 1; 
            }
            return view('admin.admins.country_tab');   
        }

    }

    public function fetchCustomerReport()
    {
        $input = Input::all();
        $data=array();
        $filter=$input['filter'];
        $query=new User();
        $query1=new User();
        if($filter=="personal")
        {
        $query=$query->where('user_type','Personal')->orwhere('user_type','Agent')->where('first_name','!=',"");   
        $query1=$query1->where('user_type','Personal')->where('is_logged_in',1)->orwhere('user_type','Agent')->where('first_name','!=',"")->where('is_logged_in',1);
        }
        elseif($filter=="business")
        {
        $query=$query->where('user_type','Business')->orwhere('user_type','Agent')->where('business_name','!=',"");   
        $query1=$query1->where('user_type','Business')->where('is_logged_in',1)->orwhere('user_type','Agent')->where('business_name','!=',"")->where('is_logged_in',1);   
        }
        elseif($filter=="all")
        {
        $query1=$query1->where('is_logged_in',1);     
        }

        $total_users=$query->count();
        $total_active_users=$query1->count();
        $total_active_users_ids=$query1->pluck('id');

        $current_date=date('Y-m-d');
        $last_date_before_90_days = date('Y-m-d', strtotime('-90 Days', strtotime(date('Y-m-d'))));
        


        //to calculate the total active users
        $days_array=array('90 Days','60 Days','30 Days');
        $total_transactions_in_90_days =Transaction::select('user_id',DB::raw('count(*) as total'))->whereDate('created_at','>=',$last_date_before_90_days)->whereDate('created_at','<=',$current_date)->whereIn('user_id',$total_active_users_ids)->groupBy('user_id')->pluck('user_id')->count();
       $total_user_not_transacted=count($total_active_users_ids)-$total_transactions_in_90_days;
       
        // DB::enableQueryLog();
        foreach($days_array as $days)
        {
            $trans=new Transaction();
            if($filter=="personal")
            {
            $trans=$trans->whereHas('User', function ($q){
            $q->where('user_type','Personal')->orwhere('user_type','Agent')->where('first_name','!=',"");   
            });
            }
            elseif($filter=="business")
            {
            $trans=$trans->whereHas('User', function ($q){
            $q->where('user_type','Business')->orwhere('user_type','Agent')->where('business_name','!=',""); 
            });
            }
            $last_date= date('Y-m-d', strtotime('-'.$days, strtotime(date('Y-m-d'))));
            $trans_count=$trans->select('user_id',DB::raw('count(*) as total'))->whereDate('created_at','>=',$last_date)->whereDate('created_at','<=',$current_date)->where('user_id','!=','1')->groupBy('user_id')->pluck('user_id')->count();
            $data['total_users']=$total_users;
            $data['total_active_users']=$total_active_users;
            $data[$days]=$trans_count;
        }

        $total_user_not_transacted_in_last_90_days=$data['total_users']-($data['90 Days']+$total_user_not_transacted);
        $data['total_user_not_transacted_in_last_90_days']=$total_user_not_transacted_in_last_90_days;

      
        return view('admin.admins.fetch_customer_report',['data'=>$data]);   
    }

    public function fetchCountryAmountReport()
    {
        // echo "<pre>";
        global $currencyList;   
        $input = Input::all();
        $user_country_list=User::groupBy('country')->pluck('country');
        $country_list=Country::whereIn('name',$user_country_list)->pluck('name','sortname');
        // $country_list=Country::whereIn('name',array('Australia'))->pluck('name','sortname');
         $filter=$input['filter'];
         $last_lable=explode("/",$filter)[0];
         $current_lable=explode("/",$filter)[1];
         $currency=$input['currency'];
         if($filter=='Last Year / Current Year')
         {
             $month_sum_array=array();
             $max_sum_month=0;
             $current_year=date('Y');
             $last_year=date('Y', strtotime('last year'));
             $year_array=array($last_year,$current_year);
             $total_transactions=array();
             foreach($country_list as $key=>$country)
             {
                 $data=array();
                 foreach($year_array as $year)
                 {
                     $data['country_name']=$country;
                     $data['short_code']=$key;
                     $trans_count=Transaction::where('currency',$currency)->where('user_id','!=',1)->whereYear('created_at','=',$year)->whereHas('User', function ($q) use($country) {
                         $q->where('country',$country); 
                         })->sum('amount');   
                     $year==$last_year ? $data[trim($last_lable)]=$trans_count : $data[trim($current_lable)]=$trans_count;
                 }
                 $total_transactions[]=$data[trim($last_lable)];
                 $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                 array_push($month_sum_array,$data);
             }
           }
           if($filter=='Last Week / Current Week')
           {
               $month_sum_array=array();
               $startOfLastWeek = Carbon::now()->subWeek(1)->startOfWeek()->toDateString();
               $endOflastWeek = Carbon::now()->subWeek(1)->endOfWeek()->toDateString();
               $startOfCurrentWeek=Carbon::now()->startOfWeek()->toDateString();
               $endOfCurrentWeek=Carbon::now()->endOfWeek()->toDateString();
               $last_current_week_array=array(array($startOfLastWeek,$endOflastWeek,'Last Week'),array($startOfCurrentWeek,$endOfCurrentWeek,'Current Week'));
               $total_transactions=array();
               foreach($country_list as $key=>$country)
               {
                   $data=array();
                   foreach($last_current_week_array as $week_array)
                   {
                       $week_start_date=$week_array[0];
                       $week_end_date=$week_array[1];
                       $week=$week_array[2];

                       $data['country_name']=$country;
                       $data['short_code']=$key;
                       $trans_count=Transaction::where('currency',$currency)->where('user_id','!=',1)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->whereHas('User', function ($q) use($country) {
                           $q->where('country',$country); 
                           })->sum('amount');   
                       $data[trim($week)]=$trans_count;
                   }
                   $total_transactions[]=$data[trim($last_lable)];
                   $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                   array_push($month_sum_array,$data);
               }
           }
           if($filter=='Last Month / Current Month')
           {
               $month_sum_array=array();
               $max_sum_month=0;
               $current_year=date('Y');
               $current_month=date('m');
               $last_month=date('m', strtotime('last month'));
               $month_array=array($last_month,$current_month);
               $total_transactions=array();
               foreach($country_list as $key=>$country)
               {
                 $data=array();
                 foreach($month_array as $month)
                 {
                     $data['country_name']=$country;
                     $data['short_code']=$key;
                     $trans_count=Transaction::where('currency',$currency)->where('user_id','!=',1)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->whereHas('User', function ($q) use($country) {
                         $q->where('country',$country); 
                         })->sum('amount');
                     $month==$last_month ? $data[trim($last_lable)]=$trans_count : $data[trim($current_lable)]=$trans_count;
                 }
                 $total_transactions[]=$data[trim($last_lable)];
                 $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                 array_push($month_sum_array,$data);
               }
            }

            if($filter=='Day Before Yesterday / Yesterday')
            {
               $month_sum_array=array();
               $current_year=date('Y');
               $before_yesterday = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))));
               $yesterday= date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))));
               $date_array=array($before_yesterday,$yesterday);
               $total_transactions=array();
               foreach($country_list as $key=>$country)
               {
                 $data=array();
                 foreach($date_array as $date)
                 {  
                     $data['country_name']=$country;
                     $data['short_code']=$key;
                     $trans_count=Transaction::where('currency',$currency)->where('user_id','!=',1)->whereDate('created_at','=',$date)->whereHas('User', function ($q) use($country) {
                         $q->where('country',$country); 
                         })->sum('amount');
                     $date==$before_yesterday ? $data[trim($last_lable)]=$trans_count : $data[trim($current_lable)]=$trans_count;
                 }
                    $total_transactions[]=$data[trim($last_lable)];
                    $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                    array_push($month_sum_array,$data);  
                }
               }

               $assing_arr=$month_sum_array;
               $total_transactions=array_sum($total_transactions);
               $sort=trim($last_lable);
               $column = array_column($assing_arr,$sort);
               array_multisort($column, SORT_DESC, $assing_arr);
               $top_80=($total_transactions*80)/100;
               $top_80_country=array();
               $least_array=array();
               $least_20_country=array();
               $total_80=0;
               foreach($assing_arr as $asign)
               {
                     $top_80_array=array();
                     $total_80+=$asign[$sort];
                     if($asign[$sort] > $top_80)
                     {
                        $top_80_array['country_name']=$asign['country_name'];
                        $top_80_array['short_code']=$asign['short_code'];
                        $top_80_array[$sort]=$asign[$sort];
                        $top_80_array['total_transactions']=$asign['total_transactions'];
                        $top_80_array[trim($current_lable)]=$asign[trim($current_lable)]; 
                        $top_80_country[]=$top_80_array;
                     }
                     elseif($total_80 < $top_80)
                     {
                       $top_80_array['country_name']=$asign['country_name'];
                       $top_80_array['short_code']=$asign['short_code'];
                       $top_80_array[$sort]=$asign[$sort];
                       $top_80_array['total_transactions']=$asign['total_transactions'];
                       $top_80_array[trim($current_lable)]=$asign[trim($current_lable)]; 
                       $top_80_country[]=$top_80_array;
                     }
                     else{
                       if($asign[$sort]!=0)
                       {
                       $least_20=array();
                       $least_20['country_name']=$asign['country_name'];
                       $least_20['short_code']=$asign['short_code'];
                       $least_20['total_transactions']=$asign['total_transactions'];
                       $least_20[$sort]=$asign[$sort];
                       $least_20[trim($current_lable)]=$asign[trim($current_lable)]; 
                       $least_20_country[]=$least_20;
                       }
                     }
                    
               }
           
           $columnn = array_column($least_20_country,$sort);
           array_multisort($columnn, SORT_ASC, $least_20_country);
           return view('admin.admins.fetch_country_amount_report',['month_sum_array'=>$assing_arr,'last_lable'=>trim($last_lable),'current_lable'=>trim($current_lable),'top_80_country'=>$top_80_country,'least_20_country'=>$least_20_country,'currency'=>$currency]);  

    }

    public function fetchCountryTransactionReport()
    {
        $input = Input::all();
        $user_country_list=User::groupBy('country')->pluck('country');
        $country_list=Country::whereIn('name',$user_country_list)->pluck('name','sortname');
        // $country_list=Country::whereIn('name',array('India'))->pluck('name','sortname');
        $filter=$input['filter'];
        $last_lable=explode("/",$filter)[0];
        $current_lable=explode("/",$filter)[1];
        if($filter=='Last Year / Current Year')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $current_year=date('Y');
            $last_year=date('Y', strtotime('last year'));
            $year_array=array($last_year,$current_year);
            $total_transactions=array();
            foreach($country_list as $key=>$country)
            {
                $data=array();
                foreach($year_array as $year)
                {
                    $data['country_name']=$country;
                    $data['short_code']=$key;
                    $trans_count=Transaction::whereYear('created_at','=',$year)->whereHas('User', function ($q) use($country) {
                        $q->where('country',$country); 
                        })->count();
                    $year==$last_year ? $data[trim($last_lable)]=$trans_count : $data[trim($current_lable)]=$trans_count;
                }
                $total_transactions[]=$data[trim($last_lable)];
                $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                array_push($month_sum_array,$data);
            }
          }
          if($filter=='Last Month / Current Month')
          {
              $month_sum_array=array();
              $max_sum_month=0;
              $current_year=date('Y');
              $current_month=date('m');
              $last_month=date('m', strtotime('last month'));
              $month_array=array($last_month,$current_month);
              $total_transactions=array();
              foreach($country_list as $key=>$country)
              {
                $data=array();
                foreach($month_array as $month)
                {
                    $data['country_name']=$country;
                    $data['short_code']=$key;
                    $trans_count=Transaction::whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->whereHas('User', function ($q) use($country) {
                        $q->where('country',$country); 
                        })->count();
                    $month==$last_month ? $data[trim($last_lable)]=$trans_count : $data[trim($current_lable)]=$trans_count;
                }
                $total_transactions[]=$data[trim($last_lable)];
                $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                array_push($month_sum_array,$data);
              }
           }
           if($filter=='Last Week / Current Week')
           {
               $month_sum_array=array();
               $startOfLastWeek = Carbon::now()->subWeek(1)->startOfWeek()->toDateString();
               $endOflastWeek = Carbon::now()->subWeek(1)->endOfWeek()->toDateString();
               $startOfCurrentWeek=Carbon::now()->startOfWeek()->toDateString();
               $endOfCurrentWeek=Carbon::now()->endOfWeek()->toDateString();
               $last_current_week_array=array(array($startOfLastWeek,$endOflastWeek,'Last Week'),array($startOfCurrentWeek,$endOfCurrentWeek,'Current Week'));
               $total_user=array();
               foreach($country_list as $key=>$country)
               {
                 $data=array();
                 foreach($last_current_week_array as $week_array)
                 {  
                   $week_start_date=$week_array[0];
                   $week_end_date=$week_array[1];
                   $week=$week_array[2];
                    $data['country_name']=$country;
                    $data['short_code']=$key;
                    $trans_count=Transaction::whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->whereHas('User', function ($q) use($country) {
                        $q->where('country',$country); 
                        })->count();;
                    $data[trim($week)]=$trans_count;
                 }
                    $total_transactions[]=$data[trim($last_lable)];
                    $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                    array_push($month_sum_array,$data);
                }
   
           }
           if($filter=='Day Before Yesterday / Yesterday')
           {
              $month_sum_array=array();
              $current_year=date('Y');
              $before_yesterday = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))));
              $yesterday= date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))));
              $date_array=array($before_yesterday,$yesterday);
              $total_transactions=array();
              foreach($country_list as $key=>$country)
              {
                $data=array();
                foreach($date_array as $date)
                {  
                    $data['country_name']=$country;
                    $data['short_code']=$key;
                    $trans_count=Transaction::whereDate('created_at','=',$date)->whereHas('User', function ($q) use($country) {
                        $q->where('country',$country); 
                        })->count();
                    $date==$before_yesterday ? $data[trim($last_lable)]=$trans_count : $data[trim($current_lable)]=$trans_count;
                }
                   $total_transactions[]=$data[trim($last_lable)];
                   $data['total_transactions']= $data[trim($last_lable)]+$data[trim($current_lable)];
                   array_push($month_sum_array,$data);
               }
              }
     
              $assing_arr=$month_sum_array;
              $total_transactions=array_sum($total_transactions);
              $sort=trim($last_lable);
              $column = array_column($assing_arr,$sort);
              array_multisort($column, SORT_DESC, $assing_arr);
              $top_80=($total_transactions*80)/100;
              $top_80_country=array();
              $least_array=array();
              $least_20_country=array();
              $total_80=0;
              foreach($assing_arr as $asign)
              {
                    $top_80_array=array();
                    $total_80+=$asign[$sort];
                    if($asign[$sort] > $top_80)
                    {
                       $top_80_array['country_name']=$asign['country_name'];
                       $top_80_array['short_code']=$asign['short_code'];
                       $top_80_array[$sort]=$asign[$sort];
                       $top_80_array['total_transactions']=$asign['total_transactions'];
                       $top_80_array[trim($current_lable)]=$asign[trim($current_lable)]; 
                       $top_80_country[]=$top_80_array;
                    }
                    else if($total_80 < $top_80)
                    {
                      $top_80_array['country_name']=$asign['country_name'];
                      $top_80_array['short_code']=$asign['short_code'];
                      $top_80_array[$sort]=$asign[$sort];
                      $top_80_array['total_transactions']=$asign['total_transactions'];
                      $top_80_array[trim($current_lable)]=$asign[trim($current_lable)]; 
                      $top_80_country[]=$top_80_array;
                    }
                    else{
                      if($asign[$sort]!=0)
                      {
                      $least_20=array();
                      $least_20['country_name']=$asign['country_name'];
                      $least_20['short_code']=$asign['short_code'];
                      $least_20['total_transactions']=$asign['total_transactions'];
                      $least_20[$sort]=$asign[$sort];
                      $least_20[trim($current_lable)]=$asign[trim($current_lable)]; 
                      $least_20_country[]=$least_20;
                      }
                    }
                   
              }
          
          $columnn = array_column($least_20_country,$sort);
          array_multisort($columnn, SORT_ASC, $least_20_country);
          return view('admin.admins.fetch_country_transaction_report',['month_sum_array'=>$assing_arr,'last_lable'=>trim($last_lable),'current_lable'=>trim($current_lable),'top_80_country'=>$top_80_country,'least_20_country'=>$least_20_country]);   

    }

    public function fetchCountryReport()
    {
        // echo "<pre>";
        $input = Input::all();
        $user_country_list=User::groupBy('country')->pluck('country');
        $country_list=Country::whereIn('name',$user_country_list)->pluck('name','sortname');
        // $country_list=Country::whereIn('name',array('India'))->pluck('name','sortname');
        $filter=$input['filter'];
        $last_lable=explode("/",$filter)[0];
        $current_lable=explode("/",$filter)[1];
        if($filter=='Last Year / Current Year')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $current_year=date('Y');
            $last_year=date('Y', strtotime('last year'));
            $year_array=array($last_year,$current_year);
            $total_user=array();
            foreach($country_list as $key=>$country)
            {
                $data=array();
                foreach($year_array as $year)
                {
                    $data['country_name']=$country;
                    $data['short_code']=$key;
                    $user_count=User::where('country',$country)->whereYear('created_at','=',$year)->count();
                    $year==$last_year ? $data[trim($last_lable)]=$user_count : $data[trim($current_lable)]=$user_count;
                }
                $total_user[]=$data[trim($last_lable)];
                $data['total_user']= $data[trim($last_lable)]+$data[trim($current_lable)];
                array_push($month_sum_array,$data);
            }
          }
        if($filter=='Last Month / Current Month')
        {
              $month_sum_array=array();
              $max_sum_month=0;
              $current_year=date('Y');
              $current_month=date('m');
              $last_month=date('m', strtotime('last month'));
              $month_array=array($last_month,$current_month);
              $total_user=array();
              foreach($country_list as $key=>$country)
              {
                $data=array();
                foreach($month_array as $month)
                {
                    $data['country_name']=$country;
                    $data['short_code']=$key;
                    $user_count=User::where('country',$country)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count();
                    $month==$last_month ? $data[trim($last_lable)]=$user_count : $data[trim($current_lable)]=$user_count;
        
                }
                $total_user[]=$data[trim($last_lable)];
                $data['total_user']= $data[trim($last_lable)]+$data[trim($current_lable)];
                array_push($month_sum_array,$data);
              }
        }
        if($filter=='Last Week / Current Week')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $startOfLastWeek = Carbon::now()->subWeek(1)->startOfWeek()->toDateString();
            $endOflastWeek = Carbon::now()->subWeek(1)->endOfWeek()->toDateString();
            $startOfCurrentWeek=Carbon::now()->startOfWeek()->toDateString();
            $endOfCurrentWeek=Carbon::now()->endOfWeek()->toDateString();
            $last_current_week_array=array(array($startOfLastWeek,$endOflastWeek,'Last Week'),array($startOfCurrentWeek,$endOfCurrentWeek,'Current Week'));
            $total_user=array();
            foreach($country_list as $key=>$country)
            {
              $data=array();
              foreach($last_current_week_array as $week_array)
              {  
                $week_start_date=$week_array[0];
                $week_end_date=$week_array[1];
                $week=$week_array[2];
                 $data['country_name']=$country;
                 $data['short_code']=$key;
                 $user_count=User::where('country',$country)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count();
                 $data[trim($week)]=$user_count;
              }
                 $total_user[]=$data[trim($last_lable)];
                 $data['total_user']= $data[trim($last_lable)]+$data[trim($current_lable)];
                 array_push($month_sum_array,$data);
             }

        }
        if($filter=='Day Before Yesterday / Yesterday')
        {
           $month_sum_array=array();
           $current_year=date('Y');
           $before_yesterday = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))));
           $yesterday= date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))));
           $date_array=array($before_yesterday,$yesterday);
           $total_user=array();
           foreach($country_list as $key=>$country)
           {
             $data=array();
             foreach($date_array as $date)
             {  
                $data['country_name']=$country;
                $data['short_code']=$key;
                $user_count=User::where('country',$country)->whereDate('created_at','=',$date)->count();
                $date==$before_yesterday ? $data[trim($last_lable)]=$user_count : $data[trim($current_lable)]=$user_count;
             }
                $total_user[]=$data[trim($last_lable)];
                $data['total_user']= $data[trim($last_lable)]+$data[trim($current_lable)];
                array_push($month_sum_array,$data);
            }
           }

            $assing_arr=$month_sum_array;
            $total_user=array_sum($total_user);
            $sort=trim($last_lable);
            $column = array_column($assing_arr,$sort);
            array_multisort($column, SORT_DESC, $assing_arr);
            $top_80=($total_user*80)/100;
            $top_80_country=array();
            $least_array=array();
            $least_20_country=array();
            $total_80=0;
            foreach($assing_arr as $asign)
            {
                  $top_80_array=array();
                  $total_80+=$asign[$sort];
                  if($asign[$sort] > $top_80)
                    {
                    $top_80_array['country_name']=$asign['country_name'];
                    $top_80_array['short_code']=$asign['short_code'];
                    $top_80_array[$sort]=$asign[$sort];
                    $top_80_array['total_user']=$asign['total_user'];
                    $top_80_array[trim($current_lable)]=$asign[trim($current_lable)]; 
                    $top_80_country[]=$top_80_array;
                    }
                  else if($total_80 < $top_80)
                  {
                    $top_80_array['country_name']=$asign['country_name'];
                    $top_80_array['short_code']=$asign['short_code'];
                    $top_80_array[$sort]=$asign[$sort];
                    $top_80_array['total_user']=$asign['total_user'];
                    $top_80_array[trim($current_lable)]=$asign[trim($current_lable)]; 
                    $top_80_country[]=$top_80_array;
                  }
                  else{
                    if($asign[$sort]!=0)
                    {
                    $least_20=array();
                    $least_20['country_name']=$asign['country_name'];
                    $least_20['short_code']=$asign['short_code'];
                    $least_20['total_user']=$asign['total_user'];
                    $least_20[$sort]=$asign[$sort];
                    $least_20[trim($current_lable)]=$asign[trim($current_lable)]; 
                    $least_20_country[]=$least_20;
                    }
                  }
                 
            }
        
        $columnn = array_column($least_20_country,$sort);
        array_multisort($columnn, SORT_ASC, $least_20_country);
        return view('admin.admins.fetch_country_report',['month_sum_array'=>$assing_arr,'last_lable'=>trim($last_lable),'current_lable'=>trim($current_lable),'top_80_country'=>$top_80_country,'least_20_country'=>$least_20_country]);   
    }


    public function fetchProductGraph()
    {
        $input = Input::all();
        $filter=$input['filter'];
        $trans_type=$input['trans_type'];
        $last_lable=explode("/",$filter)[0];
        $current_lable=explode("/",$filter)[1];
        //echo "<pre>";
        if($trans_type=="outgoing")
        {
        global $currencyList;    
        $transaction=array('Manual Withdraw','CryptoWithdraw','Withdraw##Agent','Global Pay','3rd Party Pay','Mobile Top-up','GIFT CARD');
        $dba_transactions=array('WITHDRAW');
        $fetch_column='transactions.real_value';
        $type=2;
        $field_currency="currency"; 
        $dba_status=array(2,1);
        $fetch_dba_column='dba_transactions.real_value';
        }
        elseif($trans_type=="incoming")
        {
        global $currencyList;    
        $transaction=array('ManualDeposit','OZOW_EFT','CryptoDeposit');
        $dba_transactions=array('DEPOSIT');
        $fetch_column='transactions.real_value';
        $status=1;
        $type=1;
        $field_currency="currency"; 
        $dba_status=array(1,7);
        $fetch_dba_column='dba_transactions.real_value';
        }
        elseif($trans_type=="GIFT CARD")
        {
        global $currencyList;   
        $transaction=array($trans_type);   
        $fetch_column='transactions.sender_real_value';
        $type=2;
        }
        elseif($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit")
        {
        global $currencyList;    
        $transaction=array($trans_type);   
        $fetch_column='transactions.real_value';
        $status=1;
        $type=1;
        }
        elseif($trans_type=="DBA Purchased")
        {
        $field_currency="currency";    
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('DEPOSIT');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="DBA WITHDRAW")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('WITHDRAW');    
        $type=2;
        $status=array(1,2);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="SWAP")
        {
        $field_currency="receiver_currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('SWAP');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="eSavings")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('SWAP');    
        $type=2;
        $status=array(7);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="AFFILIATE SWAP")
        {
        $field_currency="receiver_currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('AFFILIATE SWAP');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="Credit_dba")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('DBA WALLET ADJUST (CREDIT)');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="debit_dba")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('DBA WALLET ADJUST (DEBIT)');    
        $type=2;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="Credit_Fiat" || $trans_type=="debit_Fiat")
        {
        global $currencyList;   
        $transaction=array('W2W');   
        $fetch_column='transactions.real_value';
        $type=2;
        }
        elseif($trans_type=="all_transactions")
        {
        global $currencyList;   
        }
        else{
        global $currencyList;   
         $transaction=array($trans_type);   
         $fetch_column='transactions.real_value';
         $type=2;
        }
       
        if($filter=='Last Year / Current Year')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $current_year=date('Y');
            $last_year=date('Y', strtotime('last year'));
            $year_array=array($last_year,$current_year);
            $currency_sum_array=array();
            if($trans_type!="all_transactions")
            {
            foreach($year_array as $year)
            {
              for($i=1;$i<=12;$i++)
                {
                $monthNum  = $i;
                $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
                $monthName = $dateObj->format('M');
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                }
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();
                $dba_trans_count=0;
                if($trans_type=="outgoing" || $trans_type=="incoming")
                {
                //for dba transactions
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();
                }
                $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;

                }
                else{
                $trans_count=0;    
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereIn('status',$status)->whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();  
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
                }
                $month_sum_array[$year][]=array("label" =>$monthName, "y" => $trans_cnt);
                }
            }

             //for calculate amount of currency
             foreach($currencyList as $currency)
             {
                $data=array();
                foreach($year_array as $year)
                {

                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {    
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                }   
                 $trans_currency_sum=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('currency',$currency)->whereYear('created_at','=',$year)->sum('amount');
                 if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                 {
                    $data['currency']=$currency;
                    $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                 }
                }
                else{
                    $query=new DbaTransaction();  
                    $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where($field_currency,$currency)->whereYear('created_at','=',$year)->sum('amount');
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }  
                }
                }
                if(!empty($data))
                {
                array_push($currency_sum_array,$data);
                }
             }
            
             if($trans_type=="outgoing" || $trans_type=="incoming")
             {
             $dbaCurrencyList=array('DBA');
             foreach($dbaCurrencyList as $currency)
             {
                foreach($year_array as $year)
                {
                    $query=new DbaTransaction();  
                    $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where($field_currency,$currency)->whereYear('created_at','=',$year)->sum($fetch_dba_column);
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }  
                }
                if(!empty($data))
                {
                array_push($currency_sum_array,$data);
                }
             }
            }
              }
              else{
                foreach($year_array as $year)
                {
                  for($i=1;$i<=12;$i++)
                    {
                    $monthNum  = $i;
                    $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
                    $monthName = $dateObj->format('M');
                    $trans_count=Transaction::whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$year][]=array("label" =>$monthName, "y" => $trans_cnt);
                    }
                }    
              }
            $max_array=array();
            array_push($max_array,max(array_column($month_sum_array[$last_year], 'y')));
            array_push($max_array,max(array_column($month_sum_array[$current_year], 'y')));
            $previous_graph=json_encode($month_sum_array[$last_year]);
            $current_graph=json_encode($month_sum_array[$current_year]);
        }

        //echo $filter;
        if($filter=='Last Month / Current Month')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $current_year=date('Y');
            $current_month=date('m');
            $last_month=date('m', strtotime('last month'));
            $month_array=array($last_month,$current_month);
            $currency_sum_array=array();
            if($trans_type!="all_transactions")
            {
            foreach($month_array as $month)
            {
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count(); 
                $dba_trans_count=0;
                if($trans_type=="outgoing" || $trans_type=="incoming")
                {
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count(); 
                }
                $dateObj =\DateTime::createFromFormat('!m', $month);
                $monthName = $dateObj->format('F');
                $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
                }
                else{
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count();    
                $dateObj =\DateTime::createFromFormat('!m', $month);
                $monthName = $dateObj->format('F');
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $dba_trans_count;
                }

                $month_sum_array[$monthName][]=array("label" =>$monthName, "y" => $trans_cnt);
            }

              //for calculate amount of currency
              foreach($currencyList as $currency)
              {
                 $data=array();
                 foreach($month_array as $month)
                 {
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                 $query=new Transaction();
                 if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                 {
                 $query=$query->where('status',$status);
                 }
                 elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                  $trans_currency_sum=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('amount');
                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                  }
                 }
                 else{
                    $query=new DbaTransaction(); 
                    $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where($field_currency,$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('amount');
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }
                 }
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }

              if($trans_type=="outgoing" || $trans_type=="incoming")
              {
              $dbaCurrencyList=array('DBA');
              foreach($dbaCurrencyList as $currency)
              {
                 foreach($month_array as $month)
                 {
                     $query=new DbaTransaction();  
                     $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where($field_currency,$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum($fetch_dba_column);
                     if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                     {
                        $data['currency']=$currency;
                        $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                     }  
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }
             }
              }
              else{
                foreach($month_array as $month)
                {
                    $query=new Transaction();
                    $trans_count=$query->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count(); 
                    $dateObj =\DateTime::createFromFormat('!m', $month);
                    $monthName = $dateObj->format('F');
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$monthName][]=array("label" =>$monthName, "y" => $trans_cnt);
                }
              } 
            $max_array=array();
            array_push($max_array,max(array_column($month_sum_array[date('F', strtotime('last month'))], 'y')));
            array_push($max_array,max(array_column($month_sum_array[date('F')], 'y')));
            $previous_graph=json_encode($month_sum_array[date('F', strtotime('last month'))]);
            $current_graph=json_encode($month_sum_array[date('F')]);
        }

        if($filter=='Last Week / Current Week')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $startOfLastWeek = Carbon::now()->subWeek(1)->startOfWeek()->toDateString();
            $endOflastWeek = Carbon::now()->subWeek(1)->endOfWeek()->toDateString();
            $startOfCurrentWeek=Carbon::now()->startOfWeek()->toDateString();
            $endOfCurrentWeek=Carbon::now()->endOfWeek()->toDateString();
            $last_current_week_array=array(array($startOfLastWeek,$endOflastWeek,'Last Week'),array($startOfCurrentWeek,$endOfCurrentWeek,'Current Week'));
            $currency_sum_array=array();
            if($trans_type!="all_transactions")
            {

            foreach($last_current_week_array as $week_array)
            {  

            $week_start_date=$week_array[0];
            $week_end_date=$week_array[1];
            $week=$week_array[2];

            if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
            {  
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 

               $dba_trans_count=0;
               if($trans_type=="outgoing" || $trans_type=="incoming")
               {
               $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 
               }
               $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
              }
              else{
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $dba_trans_count;
              }
               $month_sum_array[$week][]=array("label" =>$week, "y" => $trans_cnt);
           }

           //for calculate amount of currency
              foreach($currencyList as $currency)
              {
                 $data=array();
                 foreach($last_current_week_array as $week_array)
                 {
                    $week_start_date=$week_array[0];
                    $week_end_date=$week_array[1];
                    $week=$week_array[2];
        
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                  $query=new Transaction();
                  if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                  {
                  $query=$query->where('status',$status);
                  }
                  elseif($trans_type=="Credit_Fiat")
                  {
                  $query=$query->where('user_id',1);
                  }
                  elseif($trans_type=="debit_Fiat")
                  {
                  $query=$query->where('receiver_id',1);   
                  }
                  elseif($trans_type=="w2w")
                  {
                   $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                  }    
                  $trans_currency_sum=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('amount');
                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $data[trim($week)]=$trans_currency_sum;
                  }
                  }
                  else{
                   $query=new DbaTransaction(); 
                   $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where($field_currency,$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('amount');
                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $data[trim($week)]=$trans_currency_sum;
                  } 
                  }
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }

              if($trans_type=="outgoing" || $trans_type=="incoming")
              {
              $dbaCurrencyList=array('DBA');
              foreach($dbaCurrencyList as $currency)
              {
                 foreach($last_current_week_array as $week_array)
                 {
                    $week_start_date=$week_array[0];
                    $week_end_date=$week_array[1];
                    $week=$week_array[2];

                     $query=new DbaTransaction();  
                     $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where($field_currency,$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum($fetch_dba_column);
                     if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                     {
                        $data['currency']=$currency;
                        $data[trim($week)]=$trans_currency_sum;
                     }  
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }
             }  
            }
            else{
                foreach($last_current_week_array as $week_array)
                {
                    $week_start_date=$week_array[0];
                    $week_end_date=$week_array[1];
                    $week=$week_array[2];
                    $query=new Transaction();
                    $trans_count=$query->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$week][]=array("label" =>$week, "y" => $trans_cnt);
                }
            }
            $max_array=array();
            array_push($max_array,max(array_column($month_sum_array[trim($last_lable)], 'y')));
            array_push($max_array,max(array_column($month_sum_array[trim($current_lable)], 'y')));
            $previous_graph=json_encode($month_sum_array[trim($last_lable)]);
            $current_graph=json_encode($month_sum_array[trim($current_lable)]);
        }

        if($filter=='Day Before Yesterday / Yesterday')
        {
           $current_year=date('Y');
           $before_yesterday = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))));
           $yesterday= date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))));
           $date_array=array($before_yesterday,$yesterday);
           $currency_sum_array=array();
           if($trans_type!="all_transactions")
           {
           foreach($date_array as $date)
           {  
            if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
            {  
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 

                        
               $dba_trans_count=0;
               if($trans_type=="outgoing" || $trans_type=="incoming")
               {
               $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 
               }
               $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
              }
              else{
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $dba_trans_count;
              }
               $month_sum_array[$date][]=array("label" =>date('d M', strtotime($date)), "y" => $trans_cnt);
           }

           //for calculate amount of currency
              foreach($currencyList as $currency)
              {
                 $data=array();
                 foreach($date_array as $date)
                 {
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                  $query=new Transaction();
                  if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                  {
                  $query=$query->where('status',$status);
                  }
                  elseif($trans_type=="Credit_Fiat")
                  {
                  $query=$query->where('user_id',1);
                  }
                  elseif($trans_type=="debit_Fiat")
                  {
                  $query=$query->where('receiver_id',1);   
                  }
                  elseif($trans_type=="w2w")
                  {
                   $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                  }    
                  $trans_currency_sum=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('currency',$currency)->whereDate('created_at','=',$date)->sum('amount');
                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                  }
                  }
                  else{
                   $query=new DbaTransaction(); 
                   $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where($field_currency,$currency)->whereDate('created_at','=',$date)->sum('amount');
                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                  } 
                  }
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }

              if($trans_type=="outgoing" || $trans_type=="incoming")
              {
              $dbaCurrencyList=array('DBA');
              foreach($dbaCurrencyList as $currency)
              {
                 foreach($date_array as $date)
                 {
                     $query=new DbaTransaction();  
                     $trans_currency_sum=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where($field_currency,$currency)->whereDate('created_at','=',$date)->sum($fetch_dba_column);
                     if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                     {
                        $data['currency']=$currency;
                        $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                     }  
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }
             } 
            }
            else{
                foreach($date_array as $date)
                {  
                    $query=new Transaction();
                    $trans_count=$query::whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$date][]=array("label" =>date('d M', strtotime($date)), "y" => $trans_cnt);
                } 
            }
           $max_array=array();
           array_push($max_array,max(array_column($month_sum_array[date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))))], 'y')));
           array_push($max_array,max(array_column($month_sum_array[date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))))], 'y')));
           $previous_graph=json_encode($month_sum_array[date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))))]);
           $current_graph=json_encode($month_sum_array[date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))))]);
        }  

        $maximum_value=max($max_array)!='null' ? max($max_array) : 0 ;

        if($trans_type!="all_transactions")
        {
        return view('admin.admins.fetch_graph',['previous_graph' => $previous_graph,'current_graph'=>$current_graph,'last_lable'=>$last_lable,'current_lable'=>$current_lable,'currency_sum_array'=>$currency_sum_array,'filter'=>$filter,'maximum_value'=>$maximum_value]);
        }
        else{
            return view('admin.admins.all_transaction_graph',['previous_graph' => $previous_graph,'current_graph'=>$current_graph,'last_lable'=>$last_lable,'current_lable'=>$current_lable,'filter'=>$filter,'maximum_value'=>$maximum_value]);   
        }
    }

    public function fetchFeeGraph()
    {
        $input = Input::all();
        // echo"<pre>";print_r($sender_fees);
        
        $filter=$input['filter'];
        $trans_type=$input['trans_type'];
        $last_lable=explode("/",$filter)[0];
        $current_lable=explode("/",$filter)[1];
        //echo "<pre>";
        if($trans_type=="outgoing")
        {
        global $currencyList;    
        $transaction=array('Manual Withdraw','CryptoWithdraw','Withdraw##Agent','Global Pay','3rd Party Pay','Mobile Top-up','GIFT CARD');
        $dba_transactions=array('WITHDRAW');
        $fetch_column='transactions.real_value';
        $type=2;
        $field_currency="currency"; 
        $dba_status=array(2,1);
        $fetch_dba_column='dba_transactions.real_value';
       
        }
        elseif($trans_type=="incoming")
        {
        global $currencyList;    
        $transaction=array('ManualDeposit','OZOW_EFT','CryptoDeposit');
        $dba_transactions=array('DEPOSIT');
        $fetch_column='transactions.real_value';
      
        $status=1;
        $type=1;
        $field_currency="currency"; 
        $dba_status=array(1,7);
        $fetch_dba_column='dba_transactions.real_value';
        }
        elseif($trans_type=="GIFT CARD")
        {
        global $currencyList;   
        $transaction=array($trans_type);   
        $fetch_column='transactions.sender_real_value';
        $type=2;
        }
        elseif($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit")
        {
        global $currencyList;    
        $transaction=array($trans_type);   
        $fetch_column='transactions.real_value';
        $status=1;
        $type=1;
        }
        elseif($trans_type=="DBA Purchased")
        {
        $field_currency="currency";    
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('DEPOSIT');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="DBA WITHDRAW")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('WITHDRAW');    
        $type=2;
        $status=array(1,2);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="SWAP")
        {
        $field_currency="receiver_currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('SWAP');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="eSavings")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('SWAP');    
        $type=2;
        $status=array(7);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="AFFILIATE SWAP")
        {
        $field_currency="receiver_currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('AFFILIATE SWAP');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="Credit_dba")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('DBA WALLET ADJUST (CREDIT)');    
        $type=1;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="debit_dba")
        {
        $field_currency="currency";      
        $currencyList=array('DBA'=>'DBA');   
        $dba_transactions=array('DBA WALLET ADJUST (DEBIT)');    
        $type=2;
        $status=array(1);
        $fetch_column='dba_transactions.real_value';
        }
        elseif($trans_type=="Credit_Fiat" || $trans_type=="debit_Fiat")
        {
        global $currencyList;   
        $transaction=array('W2W');   
        $fetch_column='transactions.real_value';
        $type=2;
        }
        elseif($trans_type=="all_transactions")
        {
        global $currencyList;   
        }
        else{
        global $currencyList;   
         $transaction=array($trans_type);   
         $fetch_column='transactions.real_value';
         $type=2;
        }
       
        if($filter=='Last Year / Current Year')
        {
            
            $month_sum_array=array();
            $max_sum_month=0;
            $current_year=date('Y');
            $last_year=date('Y', strtotime('last year'));
            $year_array=array($last_year,$current_year);
            $currency_sum_array=array();
            if($trans_type!="all_transactions")
            {
            foreach($year_array as $year)
            {
              for($i=1;$i<=12;$i++)
                {
                $monthNum  = $i;
                $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
                $monthName = $dateObj->format('M');
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                }
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();
                $dba_trans_count=0;
                if($trans_type=="outgoing" || $trans_type=="incoming")
                {
                //for dba transactions
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();
                }
                $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;

                }
                else{
                $trans_count=0;    
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereIn('status',$status)->whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();  
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
                }
                $month_sum_array[$year][]=array("label" =>$monthName, "y" => $trans_cnt);
                }
            }

             //for calculate amount of currency
            //  DB::connection()->enableQueryLog();
             foreach($currencyList as $currency)
             {
                $data=array();
                foreach($year_array as $year)
                {

                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {    
                    
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                }   

                $query1=clone $query;
                $query2=clone $query;

                 $sender_fees=$query1->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('sender_currency',$currency)->whereYear('created_at','=',$year)->sum('sender_fees');
               
                 $receiver_fees=$query2->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('receiver_currency',$currency)->whereYear('created_at','=',$year)->sum('receiver_fees');
                 
                 
                 $trans_currency_sum=$sender_fees+$receiver_fees;

                 if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                 {
                    $data['currency']=$currency;
                    $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                 }
                }
                else{
                    DB::connection()->enableQueryLog();
                    $query=new DbaTransaction();  

                    $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('sender_currency',$currency)->whereYear('created_at','=',$year)->sum('sender_fees');

                    $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereYear('created_at','=',$year)->sum('receiver_fees');
                    $trans_currency_sum=$sender_fees+$receiver_fees;

                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }  
                }
                }
                if(!empty($data))
                {
                array_push($currency_sum_array,$data);
                }
             }
             if($trans_type=="outgoing" || $trans_type=="incoming")
             {
             $dbaCurrencyList=array('DBA');
             foreach($dbaCurrencyList as $currency)
             {
                foreach($year_array as $year)
                {
                    $query=new DbaTransaction();  
                    $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('sender_currency',$currency)->whereYear('created_at','=',$year)->sum('sender_fees');

                    $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereYear('created_at','=',$year)->sum('receiver_fees');
                    
                    $trans_currency_sum=$sender_fees+$receiver_fees;

                    

                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }  
                }
                if(!empty($data))
                {
                array_push($currency_sum_array,$data);
                }
             }
            }
              }
              else{
                foreach($year_array as $year)
                {
                  for($i=1;$i<=12;$i++)
                    {
                    $monthNum  = $i;
                    $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
                    $monthName = $dateObj->format('M');
                    $trans_count=Transaction::whereMonth('created_at','=',$i)->whereYear('created_at','=',$year)->count();
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$year][]=array("label" =>$monthName, "y" => $trans_cnt);
                    }
                } 
                
                foreach($currencyList as $currency)
                {
                   $data=array();
                   foreach($year_array as $year)
                   {
   
                   if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                   {    
                       
                   $query=new Transaction();
                   $query1=clone $query;
                   $query2=clone $query;
   
                    $sender_fees=$query1->where('sender_currency',$currency)->whereYear('created_at','=',$year)->sum('sender_fees');
                  
                    $receiver_fees=$query2->where('receiver_currency',$currency)->whereYear('created_at','=',$year)->sum('receiver_fees');
                    
                    
                    $trans_currency_sum=$sender_fees+$receiver_fees;
   
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }
                   }
                   else{

                       $query=new DbaTransaction();  
   
                       $sender_fees=$query->where('sender_currency',$currency)->whereYear('created_at','=',$year)->sum('sender_fees');
   
                       $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereYear('created_at','=',$year)->sum('receiver_fees');
                       $trans_currency_sum=$sender_fees+$receiver_fees;
   
                       if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                       {
                          $data['currency']=$currency;
                          $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                       }  
                   }
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }

                $dbaCurrencyList=array('DBA');
                foreach($dbaCurrencyList as $currency)
                {
                    $data=array();
                   foreach($year_array as $year)
                   {
                       $query=new DbaTransaction();  
                       $sender_fees=$query->where('sender_currency',$currency)->whereYear('created_at','=',$year)->sum('sender_fees');
   
                       $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereYear('created_at','=',$year)->sum('receiver_fees');
                       
                       $trans_currency_sum=$sender_fees+$receiver_fees;
   
                       if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                       {
                          $data['currency']=$currency;
                          $year==$last_year ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                       }  
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }
               
              }
            $max_array=array();
            array_push($max_array,max(array_column($month_sum_array[$last_year], 'y')));
            array_push($max_array,max(array_column($month_sum_array[$current_year], 'y')));
            $previous_graph=json_encode($month_sum_array[$last_year]);
            $current_graph=json_encode($month_sum_array[$current_year]);
        }

        //echo $filter;
        if($filter=='Last Month / Current Month')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $current_year=date('Y');
            $current_month=date('m');
            $last_month=date('m', strtotime('last month'));
            $month_array=array($last_month,$current_month);
            $currency_sum_array=array();
            if($trans_type!="all_transactions")
            {
            foreach($month_array as $month)
            {
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count(); 
                $dba_trans_count=0;
                if($trans_type=="outgoing" || $trans_type=="incoming")
                {
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count(); 
                }
                $dateObj =\DateTime::createFromFormat('!m', $month);
                $monthName = $dateObj->format('F');
                $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
                }
                else{
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count();    
                $dateObj =\DateTime::createFromFormat('!m', $month);
                $monthName = $dateObj->format('F');
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $dba_trans_count;
                }

                $month_sum_array[$monthName][]=array("label" =>$monthName, "y" => $trans_cnt);
            }

              //for calculate amount of currency
            //   $currencyList=array('USD'=>'USD');
              foreach($currencyList as $currency)
              {
                 $data=array();
                 foreach($month_array as $month)
                 {
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                 $query=new Transaction();
                 if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                 {
                 $query=$query->where('status',$status);
                 }
                 elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 

                $query1=clone $query;
                $query2=clone $query;
                $sender_fees=$query1->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('sender_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('sender_fees');

                $receiver_fees=$query2->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('receiver_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('receiver_fees');
               
                $trans_currency_sum=$sender_fees+$receiver_fees;
                if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                {
                     $data['currency']=$currency;
                     $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                }
                }
                 else{
                    $query=new DbaTransaction(); 

                    $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('sender_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('sender_fees');

                    $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('receiver_fees');

                    $trans_currency_sum=$sender_fees+$receiver_fees;

                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }
                 }
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }

              if($trans_type=="outgoing" || $trans_type=="incoming")
              {
              $dbaCurrencyList=array('DBA');
              foreach($dbaCurrencyList as $currency)
              {
                 foreach($month_array as $month)
                 {
                     $query=new DbaTransaction();

                     $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('sender_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('sender_fees');

                     $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('receiver_fees');
                    
                     $trans_currency_sum=$sender_fees+$receiver_fees;

                     if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                     {
                        $data['currency']=$currency;
                        $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                     }  
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }
             }
              }
              else{
                foreach($month_array as $month)
                {
                    $query=new Transaction();
                    $trans_count=$query->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->count(); 
                    $dateObj =\DateTime::createFromFormat('!m', $month);
                    $monthName = $dateObj->format('F');
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$monthName][]=array("label" =>$monthName, "y" => $trans_cnt);
                }

                foreach($currencyList as $currency)
                {
                   $data=array();
                   foreach($month_array as $month)
                   {
                  if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                  {  
                 
                    $query=new Transaction();
                  $query1=clone $query;
                  $query2=clone $query;
                  $sender_fees=$query1->where('sender_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('sender_fees');
  
                  $receiver_fees=$query2->where('receiver_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('receiver_fees');
                  $trans_currency_sum=$sender_fees+$receiver_fees;
                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                       $data['currency']=$currency;
                       $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                  }
                  }
                   else{
                      $query=new DbaTransaction(); 
  
                      $sender_fees=$query->where('sender_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('sender_fees');
  
                      $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('receiver_fees');
  
                      $trans_currency_sum=$sender_fees+$receiver_fees;
  
                      if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                      {
                         $data['currency']=$currency;
                         $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                      }
                   }
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }
  

                $dbaCurrencyList=array('DBA');
                foreach($dbaCurrencyList as $currency)
                {
                    $data=array();
                   foreach($month_array as $month)
                   {
                       $query=new DbaTransaction();
  
                       $sender_fees=$query->where('sender_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('sender_fees');
  
                       $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereMonth('created_at','=',$month)->whereYear('created_at','=',$current_year)->sum('receiver_fees');
  
                       $trans_currency_sum=$sender_fees+$receiver_fees;
  
                       if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                       {
                          $data['currency']=$currency;
                          $month==$last_month ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                       }  
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }

              } 
            $max_array=array();
            array_push($max_array,max(array_column($month_sum_array[date('F', strtotime('last month'))], 'y')));
            array_push($max_array,max(array_column($month_sum_array[date('F')], 'y')));
            $previous_graph=json_encode($month_sum_array[date('F', strtotime('last month'))]);
            $current_graph=json_encode($month_sum_array[date('F')]);
        }

        if($filter=='Last Week / Current Week')
        {
            $month_sum_array=array();
            $max_sum_month=0;
            $startOfLastWeek = Carbon::now()->subWeek(1)->startOfWeek()->toDateString();
            $endOflastWeek = Carbon::now()->subWeek(1)->endOfWeek()->toDateString();
            $startOfCurrentWeek=Carbon::now()->startOfWeek()->toDateString();
            $endOfCurrentWeek=Carbon::now()->endOfWeek()->toDateString();
            $last_current_week_array=array(array($startOfLastWeek,$endOflastWeek,'Last Week'),array($startOfCurrentWeek,$endOfCurrentWeek,'Current Week'));
            $currency_sum_array=array();
            if($trans_type!="all_transactions")
            {

            foreach($last_current_week_array as $week_array)
            {  

            $week_start_date=$week_array[0];
            $week_end_date=$week_array[1];
            $week=$week_array[2];

            if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
            {  
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 

               $dba_trans_count=0;
               if($trans_type=="outgoing" || $trans_type=="incoming")
               {
               $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 
               }
               $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
              }
              else{
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $dba_trans_count;
              }
               $month_sum_array[$week][]=array("label" =>$week, "y" => $trans_cnt);
           }

           //for calculate amount of currency
              foreach($currencyList as $currency)  
              {
                 $data=array();
                 foreach($last_current_week_array as $week_array)
                 {
                    $week_start_date=$week_array[0];
                    $week_end_date=$week_array[1];
                    $week=$week_array[2];
        
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                  $query=new Transaction();
                  if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                  {
                  $query=$query->where('status',$status);
                  }
                  elseif($trans_type=="Credit_Fiat")
                  {
                  $query=$query->where('user_id',1);
                  }
                  elseif($trans_type=="debit_Fiat")
                  {
                  $query=$query->where('receiver_id',1);   
                  }
                  elseif($trans_type=="w2w")
                  {
                   $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                  }    
                  $query1=clone $query;
                  $query2=clone $query;

                  $sender_fees=$query1->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('sender_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('sender_fees');

                  $receiver_fees=$query2->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('receiver_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('receiver_fees');

                  $trans_currency_sum=$sender_fees+$receiver_fees;

                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $data[trim($week)]=$trans_currency_sum;
                  }
                  }
                  else{
                   $query=new DbaTransaction(); 

                   $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('sender_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('sender_fees');

                   $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('receiver_fees');

                   $trans_currency_sum=$sender_fees+$receiver_fees;

                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $data[trim($week)]=$trans_currency_sum;
                  } 
                  }
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }

              if($trans_type=="outgoing" || $trans_type=="incoming")
              {
              $dbaCurrencyList=array('DBA');
              foreach($dbaCurrencyList as $currency)
              {
                 foreach($last_current_week_array as $week_array)
                 {
                    $week_start_date=$week_array[0];
                    $week_end_date=$week_array[1];
                    $week=$week_array[2];

                     $query=new DbaTransaction();  

                     $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('sender_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('sender_fees');

                     $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('receiver_fees');

                     $trans_currency_sum=$sender_fees+$receiver_fees;

                     if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                     {
                        $data['currency']=$currency;
                        $data[trim($week)]=$trans_currency_sum;
                     }  
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }
             }  
            }
            else{
                foreach($last_current_week_array as $week_array)
                {
                    $week_start_date=$week_array[0];
                    $week_end_date=$week_array[1];
                    $week=$week_array[2];
                    $query=new Transaction();
                    $trans_count=$query->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->count(); 
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$week][]=array("label" =>$week, "y" => $trans_cnt);
                }

                foreach($currencyList as $currency)  
                {
                   $data=array();
                   foreach($last_current_week_array as $week_array)
                   {
                      $week_start_date=$week_array[0];
                      $week_end_date=$week_array[1];
                      $week=$week_array[2];
          
                  if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                  {  
                    $query=new Transaction();
                    $query1=clone $query;
                    $query2=clone $query;
  
                    $sender_fees=$query1->where('sender_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('sender_fees');
  
                    $receiver_fees=$query2->where('receiver_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('receiver_fees');
  
                    $trans_currency_sum=$sender_fees+$receiver_fees;
  
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $data[trim($week)]=$trans_currency_sum;
                    }
                    }
                    else{
                     $query=new DbaTransaction(); 
  
                     $sender_fees=$query->where('sender_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('sender_fees');
  
                     $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('receiver_fees');
  
                     $trans_currency_sum=$sender_fees+$receiver_fees;
  
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $data[trim($week)]=$trans_currency_sum;
                    } 
                    }
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }
  

                $dbaCurrencyList=array('DBA');
                foreach($dbaCurrencyList as $currency)
                {  
                   $data=array();
                   foreach($last_current_week_array as $week_array)
                   {
                      $week_start_date=$week_array[0];
                      $week_end_date=$week_array[1];
                      $week=$week_array[2];
  
                       $query=new DbaTransaction();  
  
                       $sender_fees=$query->where('sender_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('sender_fees');
  
                       $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereDate('created_at','>=',$week_start_date)->whereDate('created_at','<=',$week_end_date)->sum('receiver_fees');
  
                       $trans_currency_sum=$sender_fees+$receiver_fees;
  
                       if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                       {
                          $data['currency']=$currency;
                          $data[trim($week)]=$trans_currency_sum;
                       }  
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }
            
            }
            $max_array=array();
            array_push($max_array,max(array_column($month_sum_array[trim($last_lable)], 'y')));
            array_push($max_array,max(array_column($month_sum_array[trim($current_lable)], 'y')));
            $previous_graph=json_encode($month_sum_array[trim($last_lable)]);
            $current_graph=json_encode($month_sum_array[trim($current_lable)]);
        }

        if($filter=='Day Before Yesterday / Yesterday')
        {
           $current_year=date('Y');
           $before_yesterday = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))));
           $yesterday= date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))));
           $date_array=array($before_yesterday,$yesterday);
           $currency_sum_array=array();
           if($trans_type!="all_transactions")
           {
           foreach($date_array as $date)
           {  
            if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
            {  
                $query=new Transaction();
                if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                {
                $query=$query->where('status',$status);
                }
                elseif($trans_type=="Credit_Fiat")
                {
                $query=$query->where('user_id',1);
                }
                elseif($trans_type=="debit_Fiat")
                {
                $query=$query->where('receiver_id',1);   
                }
                elseif($trans_type=="w2w")
                {
                $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                } 
                $trans_count=$query->whereIn('trans_for',$transaction)->where('trans_type',$type)->whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 

                        
               $dba_trans_count=0;
               if($trans_type=="outgoing" || $trans_type=="incoming")
               {
               $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->where('trans_type',$type)->whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 
               }
               $trans_cnt = $trans_count == '0' ? 'null' : $trans_count+$dba_trans_count;
              }
              else{
                $dba_trans_count=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 
                $trans_cnt = $dba_trans_count == '0' ? 'null' : $dba_trans_count;
              }
               $month_sum_array[$date][]=array("label" =>date('d M', strtotime($date)), "y" => $trans_cnt);
           }

           //for calculate amount of currency
              foreach($currencyList as $currency)
              {
                 $data=array();
                 foreach($date_array as $date)
                 {
                if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                {  
                  $query=new Transaction();
                  if($trans_type=="ManualDeposit" || $trans_type=="OZOW_EFT" || $trans_type=="CryptoDeposit" || $trans_type=="incoming")
                  {
                  $query=$query->where('status',$status);
                  }
                  elseif($trans_type=="Credit_Fiat")
                  {
                  $query=$query->where('user_id',1);
                  }
                  elseif($trans_type=="debit_Fiat")
                  {
                  $query=$query->where('receiver_id',1);   
                  }
                  elseif($trans_type=="w2w")
                  {
                   $query=$query->where('user_id','!=',1)->where('receiver_id','!=',1); 
                  }    
                  $query1=clone $query;
                  $query2=clone $query;

                  $sender_fees=$query1->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('sender_currency',$currency)->whereDate('created_at','=',$date)->sum('sender_fees');

                  $receiver_fees=$query2->whereIn('trans_for',$transaction)->where('trans_type',$type)->where('receiver_currency',$currency)->whereDate('created_at','=',$date)->sum('receiver_fees');

                  $trans_currency_sum=$sender_fees+$receiver_fees;

                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                  }
                  }
                  else{
                   $query=new DbaTransaction(); 
                   $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('sender_currency',$currency)->whereDate('created_at','=',$date)->sum('sender_fees');

                   $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereDate('created_at','=',$date)->sum('receiver_fees');
                 
                   $trans_currency_sum=$sender_fees+$receiver_fees;

                  if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                  {
                     $data['currency']=$currency;
                     $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                  } 
                  }
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }

              if($trans_type=="outgoing" || $trans_type=="incoming")
              {
              $dbaCurrencyList=array('DBA');
              foreach($dbaCurrencyList as $currency)
              {
                 foreach($date_array as $date)
                 {
                     $query=new DbaTransaction();  
                     $sender_fees=$query->whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('sender_currency',$currency)->whereDate('created_at','=',$date)->sum('sender_fees');

                     $receiver_fees=DbaTransaction::whereIn('trans_for',$dba_transactions)->whereIn('status',$dba_status)->where('trans_type',$type)->where('receiver_currency',$currency)->whereDate('created_at','=',$date)->sum('receiver_fees');
                   
                     $trans_currency_sum=$sender_fees+$receiver_fees;

                     if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                     {
                        $data['currency']=$currency;
                        $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                     }  
                 }
                 if(!empty($data))
                 {
                 array_push($currency_sum_array,$data);
                 }
              }
             } 
            }
            else{
                foreach($date_array as $date)
                {  
                    $query=new Transaction();
                    $trans_count=$query::whereDate('created_at','=',$date)->whereYear('created_at','=',$current_year)->count(); 
                    $trans_cnt = $trans_count == '0' ? 'null' : $trans_count;
                    $month_sum_array[$date][]=array("label" =>date('d M', strtotime($date)), "y" => $trans_cnt);
                } 

                foreach($currencyList as $currency)
                {
                   $data=array();
                   foreach($date_array as $date)
                   {
                  if($trans_type!="DBA Purchased" && $trans_type!="DBA WITHDRAW" &&  $trans_type!="SWAP" && $trans_type!="eSavings" && $trans_type!="AFFILIATE SWAP" && $trans_type!="Credit_dba" && $trans_type!="debit_dba")
                  {  
                    $query=new Transaction();
                    $query1=clone $query;
                    $query2=clone $query;
  
                    $sender_fees=$query1->where('sender_currency',$currency)->whereDate('created_at','=',$date)->sum('sender_fees');
  
                    $receiver_fees=$query2->where('receiver_currency',$currency)->whereDate('created_at','=',$date)->sum('receiver_fees');
  
                    $trans_currency_sum=$sender_fees+$receiver_fees;
  
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    }
                    }
                    else{
                     $query=new DbaTransaction(); 
                     $sender_fees=$query->where('sender_currency',$currency)->whereDate('created_at','=',$date)->sum('sender_fees');
  
                     $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereDate('created_at','=',$date)->sum('receiver_fees');
                   
                     $trans_currency_sum=$sender_fees+$receiver_fees;
  
                    if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                    {
                       $data['currency']=$currency;
                       $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                    } 
                    }
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }
  
                $dbaCurrencyList=array('DBA');
                foreach($dbaCurrencyList as $currency)
                {
                   $data=array();
                   foreach($date_array as $date)
                   {
                       $query=new DbaTransaction();  
                       $sender_fees=$query->where('sender_currency',$currency)->whereDate('created_at','=',$date)->sum('sender_fees');
  
                       $receiver_fees=DbaTransaction::where('receiver_currency',$currency)->whereDate('created_at','=',$date)->sum('receiver_fees');
                     
                       $trans_currency_sum=$sender_fees+$receiver_fees;
  
                       if(isset($trans_currency_sum) && $trans_currency_sum!=0)
                       {
                          $data['currency']=$currency;
                          $date==$before_yesterday ? $data[trim($last_lable)]=$trans_currency_sum : $data[trim($current_lable)]=$trans_currency_sum;
                       }  
                   }
                   if(!empty($data))
                   {
                   array_push($currency_sum_array,$data);
                   }
                }
            }
           $max_array=array();
           array_push($max_array,max(array_column($month_sum_array[date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))))], 'y')));
           array_push($max_array,max(array_column($month_sum_array[date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))))], 'y')));
           $previous_graph=json_encode($month_sum_array[date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d H:i:s'))))]);
           $current_graph=json_encode($month_sum_array[date('Y-m-d', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))))]);
        }  

        $maximum_value=max($max_array)!='null' ? max($max_array) : 0 ;

        return view('admin.admins.all_transaction_graph_fee',['previous_graph' => $previous_graph,'current_graph'=>$current_graph,'last_lable'=>$last_lable,'current_lable'=>$current_lable,'currency_sum_array'=>$currency_sum_array,'filter'=>$filter,'maximum_value'=>$maximum_value]);

    }
    
    
    public function changeUsername() {
	  $isPermitted = $this->validatePermission(Session::get('admin_role'),'change-username');
	  if ($isPermitted == false) {
		$pageTitle = 'Not Permitted'; 
        $activetab = 'actchangeusername';  
		return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
	  }
        $pageTitle = 'Change Username'; 
        $activetab = 'actchangeusername'; 
        $adminInfo = DB::table('admins')->select('admins.username','admins.id')->where('id', Session::get('adminid'))->first();
        $input = Input::all();
        if (!empty($input)) {
            $error = '';
            $rules = array(
                'old_username' => 'required|different:new_username',
                'new_username' => 'required|email',
                'confirm_username' => 'required|same:new_username'
            );            
            $customMessages = ['different' => 'You can not change new username same as current username'];
            $validator = Validator::make($input, $rules, $customMessages); 
            if ($validator->fails()) {
                return view('admin.admins.changeUsername', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo])->withErrors($validator);
            } else { 
                DB::table('admins')->where('id', $adminInfo->id)->update(array('username' => $input['new_username']));
                Session::put('admin_username', $input['new_username']);
                Session::flash('success_message', "Admin username updated successfully.");
                return Redirect::to('admin/admins/change-username');
            }            
        }
        return view('admin.admins.changeUsername', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo]);
    }
    
    public function changePassword() {
	  $isPermitted = $this->validatePermission(Session::get('admin_role'),'change-password');
	  if ($isPermitted == false) {
		$pageTitle = 'Not Permitted'; 
        $activetab = 'actchangepassword';  
		return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
	  }	
        $pageTitle = 'Change Password'; 
        $activetab = 'actchangepassword'; 
        $input = Input::all();
        if (!empty($input)) {
            $error = '';
            $rules = array(
                'old_password' => 'required|different:new_password',
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
            );   
            $customMessages = ['different' => 'You can not change new password same as current password.'];
            $validator = Validator::make($input, $rules, $customMessages);             
            if ($validator->fails()) {
                return view('admin.admins.changePassword', ['title'=>$pageTitle, $activetab=>1])->withErrors($validator);
            } else { 
                $adminInfo = DB::table('admins')->select('admins.password','admins.id')->where('id', Session::get('adminid'))->first();
                if(!password_verify($input['old_password'], $adminInfo->password)) {
                    $error = 'Current password is not correct.';
                    return view('admin.admins.changePassword', ['title'=>$pageTitle, $activetab=>1])->withErrors($error);
                }else{
                    $new_password = bcrypt($input['new_password']);
                    DB::table('admins')->where('id', $adminInfo->id)->update(array('password' => $new_password));
                    Session::flash('success_message', "Admin password updated successfully.");
                    return Redirect::to('admin/admins/change-password');
                }
            }            
        }
        return view('admin.admins.changePassword', ['title'=>$pageTitle, $activetab=>1]);
    }
    
    public function changeEmail() {
        $pageTitle = 'Change Email'; 
        $activetab = 'actchangeemail'; 
        $adminInfo = DB::table('admins')->select('admins.email','admins.id')->where('id', Session::get('adminid'))->first();
        $input = Input::all();
        if (!empty($input)) {
            $error = '';
            $rules = array(
                'old_email' => 'required|email|different:new_email',
                'new_email' => 'required|email',
                'confirm_email' => 'required|email|same:new_email'
            );     
            $customMessages = ['different' => 'You can not change new email same as current email'];
            $validator = Validator::make($input, $rules, $customMessages);             
            if ($validator->fails()) {
                return view('admin.admins.changeEmail', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo])->withErrors($validator);
            } else { 
                DB::table('admins')->where('id', $adminInfo->id)->update(array('email' => $input['new_email']));
                Session::flash('success_message', "Admin email updated successfully.");
                return Redirect::to('admin/admins/change-email');
            }            
        }
        return view('admin.admins.changeEmail', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo]);
    }
    
    public function changeCommission() {
        $pageTitle = 'Change Commission'; 
        $activetab = 'actchangecommission'; 
        $adminInfo = DB::table('admins')->select('admins.commission','admins.id')->where('id', Session::get('adminid'))->first();
        $input = Input::all();
        if (!empty($input)) {
            $error = '';
            $rules = array(
                'commission' => 'required'
            );     
            $validator = Validator::make($input, $rules);           
            if ($validator->fails()) {
                return view('admin.admins.changeCommission', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo])->withErrors($validator);
            } else { 
                DB::table('admins')->where('id', $adminInfo->id)->update(array('commission' => $input['commission']));
                Session::flash('success_message', "Commission updated successfully.");
                return Redirect::to('admin/admins/change-commission');
            }            
        }
        return view('admin.admins.changeCommission', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo]);
    }
    
    public function walletBalance() {
        $isPermitted = $this->validatePermission(Session::get('admin_role'), 'wallet balance');
        if ($isPermitted == false) {
            $pageTitle = 'Not Permitted';
            $activetab = 'actchangeusername';
            return view('admin.admins.notPermitted', ['title' => $pageTitle, $activetab => 1]);
        }

        $pageTitle = 'Wallet Balance'; 
        $activetab = 'actwallet'; 
        $adminInfo = DB::table('users')->select('users.wallet_amount','users.id','users.dba_wallet_amount')->where('id', 1)->first();
        
        return view('admin.admins.walletBalance', ['title'=>$pageTitle, $activetab=>1, 'adminInfo'=>$adminInfo]);
    }
	
    public function changeService() {
        $pageTitle = 'Change Service'; 
        $activetab = 'actservices'; 
        $serviceInfo = DB::table('services')->where('id', 1)->first();
        $input = Input::all();
        if (!empty($input)) {
            $error = '';
            $rules = array(
                'ach' => 'required',
                'c21' => 'required',
                'eft' => 'required',
            );     
            $validator = Validator::make($input, $rules);           
            if ($validator->fails()) {
                return view('admin.admins.changeService', ['title'=>$pageTitle, $activetab=>1, 'serviceInfo'=>$serviceInfo])->withErrors($validator);
            } else { 
                DB::table('services')->where('id', 1)->update(array('ach' => $input['ach'],'c21' => $input['c21'],'eft' => $input['eft']));
                Session::flash('success_message', "Service details updated successfully.");
                return Redirect::to('admin/admins/change-service');
            }            
        }
        return view('admin.admins.changeService', ['title'=>$pageTitle, $activetab=>1, 'serviceInfo'=>$serviceInfo]);
    }
	
  public function listRole(Request $request)
  {
	if (Session::get('admin_role') != 1) {
 	 $pageTitle = 'Not Permitted'; 
     $activetab = 'actchangepassword';  
	 return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
    }  
	
    $pageTitle = 'List Role\'s'; 
    $activetab = 'actconfigrole';
	
	$roles = DB::table('roles')->where('id','!=',1)->orderBy('role_name','ASC')->get();
	return view('admin.admins.listRole', ['title'=>$pageTitle, $activetab=>1, 'roles'=>$roles]);
  }

  public function addRole()
  {
	if (Session::get('admin_role') != 1) {
 	 $pageTitle = 'Not Permitted'; 
     $activetab = 'actchangepassword';  
	 return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
    }
	
    $pageTitle = 'Create Role'; 
    $activetab = 'actconfigrole';

	$input = Input::all();
    if (!empty($input)) {
     $error = '';
     $rules = array(
	 'role_name' => 'required',
	 );
     
	 $validator = Validator::make($input, $rules);           
     if ($validator->fails()) {
      return view('admin.admins.add-role', ['title'=>$pageTitle, $activetab=>1])->withErrors($validator);
     } else {
	   $isExists = Role::where('role_name',trim($input['role_name']))->first();
	   if(!empty($isExists)) {
		 Session::flash('error_message', "Role name already exists!");
         return Redirect::to('admin/admins/roles');   
	   }
	   else {
       $role = new Role([
	    'role_name' => trim($input['role_name']),
	    'created_at' => date('Y-m-d H:i:s'),
	    'updated_at' => date('Y-m-d H:i:s'),
	   ]);
	   $role->save();
	   $role_id = $role->id;
	   
	   foreach ($input['permission'] as $permission) {
	    $perm = new Permission([
	     'role_id'=>$role_id,
	     'permission_name'=>$permission,
	     'created_at'=>date('Y-m-d H:i:s'),
	     'updated_at'=>date('Y-m-d H:i:s'),
	     ]);
		 $perm->save();
	   }
	   
        Session::flash('success_message', "Role added Successfully.");
        return Redirect::to('admin/admins/roles');
	 }
    }            
    }
    return view('admin.admins.addRole', ['title'=>$pageTitle, $activetab=>1]);	
  }
  
  public function editRole($slug)
  {
	if (Session::get('admin_role') != 1) {
 	 $pageTitle = 'Not Permitted'; 
     $activetab = 'actchangepassword';  
	 return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
    }
	
	$pageTitle = 'Edit Role'; 
    $activetab = 'actconfigrole';
	
	$role = Role::where('id',$slug)->first();
	$permissions = Permission::where('role_id',$slug)->get();
	$permissionArr = array();
	foreach ($permissions as $permission)
	{
	  $permissionArr[] = $permission->permission_name;	
	}
	
	$input = Input::all();
    if (!empty($input)) {
     $error = '';
     $rules = array(
	 'role_name' => 'required',
	 'permission' => 'required',
	 );
     
	 $validator = Validator::make($input, $rules);           
     if ($validator->fails()) {
      return view('admin.admins.editRole', ['title'=>$pageTitle, $activetab=>1, 'role'=>$role, 'permissions'=>$permissionArr])->withErrors($validator);
     } else { 
	   if(!isset($input['permission']) && Count($input['permission']) <= 0) {
		 Session::flash('error_message', "Role should have atleast one permission.");
         return Redirect::to('admin/admins/roles');
	   }		   
	   Role::where('id',$slug)->update(['role_name' => $input['role_name'], 'updated_at' => date('Y-m-d H:i:s')]);
       Permission::where('role_id',$slug)->delete();
	   
	   foreach ($input['permission'] as $permission) {
	    $perm = new Permission([
	     'role_id'=>$slug,
	     'permission_name'=>$permission,
	     'created_at'=>date('Y-m-d H:i:s'),
	     'updated_at'=>date('Y-m-d H:i:s'),
	     ]);
		 $perm->save();
	   }
	   
      Session::flash('success_message', "Role updated Successfully.");
      return Redirect::to('admin/admins/roles');
    }            
    }
    return view('admin.admins.editRole', ['title'=>$pageTitle, $activetab=>1, 'role'=>$role, 'permissions'=>$permissionArr]);  
  }
  
  public function listSubadmin(Request $request)
  {
	if (Session::get('admin_role') != 1) {
 	 $pageTitle = 'Not Permitted'; 
     $activetab = 'actchangepassword';  
	 return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
    }
	
	$pageTitle = 'List Admin'; 
    $activetab = 'actsubadmin';
	$query = new Admin();
    $query = $query->sortable();
    $query = $query->where('id','!=',1);
	
	if ($request->has('keyword')) {
     $keyword = $request->get('keyword');
     $query = $query->where(function($q) use ($keyword) {
      $q->where('username', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
      });
    }
	
	$admins = $query->orderBy('id', 'ASC')->paginate(20); 
	//DB::table('admins')->orderBy('id','ASC')->get();
	
	if ($request->ajax()) {
        return view('elements.admin.admins.subAdminList', ['admins' => $admins]);
      }
	
	return view('admin.admins.listSubAdmin', ['title'=>$pageTitle, $activetab=>1, 'admins'=>$admins]);  
  }
  
  private function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
  
  public function addAdmin()
  {
	if (Session::get('admin_role') != 1) {
 	 $pageTitle = 'Not Permitted'; 
     $activetab = 'actchangepassword';  
	 return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
    }
	
	$pageTitle = 'Add Admin'; 
    $activetab = 'actsubadmin';

	$roles = Role::where('id','!=',1)->orderBy('id','ASC')->get();
	$roleArr = array();
	foreach ($roles as $role)
	{
	  $roleArr[$role->id] = $role->role_name;	
	}

	$input = Input::all();
    if (!empty($input)) {
     $error = '';
     $rules = array(
	 'name' => 'required',
	 'surname' => 'required',
	 'email' => 'required|email:filter',
	 'pass' => 'required|min:6',
	 'cpass' => 'required|same:pass',
	 );
	 $customMessages = [
                'name.required' => 'Name field can\'t be left blank.',
                'surname.required' => 'Surname field can\'t be left blank.',
                'email.required' => 'Email field can\'t be left blank.',
                'email.email' => 'Invalid Email! Try again.',
                'pass.required' => 'Password field can\'t be left blank.',
                //'pass.regex' => 'Password must be at least 8 character long, contains an upper case letter, a lower case letter, a number and a symbol.',
				'cpass.required' => 'Confirm password field can\'t be left blank',
				'cpass.same' => 'Confirm password did\'t match',
            ];
     
	 $validator = Validator::make($input, $rules, $customMessages);           
     if ($validator->fails()) {
      return view('admin.admins.addAdmin', ['title'=>$pageTitle, $activetab=>1, 'roleList' => $roleArr])->withErrors($validator);
     } else { 
	  $isExists = Admin::where('email',$input['email'])->first();
	  if (!empty($isExists)) {
		Session::flash('error_message', "Username/Email Already Exists.");
       return Redirect::to('admin/admins/list-subadmin');  
	  }
	  else {
       $adm = new Admin([
	    'role_id' => $input['role_id'],
	    'first_name' => $input['name'],
	    'last_name' => $input['surname'],
	    'username' => $input['email'],
	    'password' => bcrypt($input['pass']),
	    'email' => $input['email'],
	    'ip_address' => $this->get_client_ip(),
	    'slug' => $input['name'],
	    'created_at' => date('Y-m-d H:i:s'),
	    'status' => 1,
	    'updated_at' => date('Y-m-d H:i:s'),
	   ]);
	   $adm->save();
       Session::flash('success_message', "Admin added Successfully.");
       return Redirect::to('admin/admins/list-subadmin');
	  }
    }            
    }
    return view('admin.admins.addAdmin', ['title'=>$pageTitle, $activetab=>1, 'roleList'=>$roleArr]);
  }
  
  public function editAdmin($slug)
  {
	if (Session::get('admin_role') != 1) {
 	 $pageTitle = 'Not Permitted'; 
     $activetab = 'actchangepassword';  
	 return view('admin.admins.notPermitted', ['title'=>$pageTitle, $activetab=>1]);  
    }
	
	$pageTitle = 'Edit Admin Account'; 
    $activetab = 'actsubadmin';

	$admin = Admin::where('id',$slug)->first();
	$roles = Role::where('id','!=',1)->orderBy('id','ASC')->get();
	$roleArr = array();
	foreach ($roles as $role)
	{
	  $roleArr[$role->id] = $role->role_name;	
	}

	$input = Input::all();
    if (!empty($input)) {
   //  print_r($input); die;
     $error = '';
     $rules = array(
	 'first_name' => 'required',
	 'last_name' => 'required',
	 'email' => 'required|email:filter',
	 'pass' => 'sometimes|nullable|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[\W_]/',
	// 'cpass' => 'required|same:pass',
	 );
	 $customMessages = [
                'first_name.required' => 'Name field can\'t be left blank.',
                'last_name.required' => 'Surname field can\'t be left blank.',
                'email.required' => 'Email field can\'t be left blank.',
                'email.email' => 'Invalid Email! Try again.',
               // 'pass.required' => 'Password field can\'t be left blank.',
				//'cpass.required' => 'Confirm password field can\'t be left blank',
				//'cpass.same' => 'Confirm password did\'t match',
                'pass.regex' => 'Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.'
            ];
     
	 $validator = Validator::make($input, $rules, $customMessages);           
     if ($validator->fails()) {
      return view('admin.admins.editAdmin', ['title'=>$pageTitle, $activetab=>1, 'roleList' => $roleArr, 'admin' => $admin])->withErrors($validator);
     } else { 
	   //print_r($input); die;
	   $ip = $this->get_client_ip();	
       if($input['pass']!="")
       {
       Admin::where('id',$slug)->update(['role_id'=>$input['role_id'],'first_name'=>$input['first_name'],'last_name'=>$input['last_name'],'username'=>$input['email'],'email'=>$input['email'],'password'=>bcrypt($input['pass']),'ip_address'=>$ip,'updated_at'=>date('Y-m-d H:i:s')]);
       }
       else{
        Admin::where('id',$slug)->update(['role_id'=>$input['role_id'],'first_name'=>$input['first_name'],'last_name'=>$input['last_name'],'username'=>$input['email'],'email'=>$input['email'],'ip_address'=>$ip,'updated_at'=>date('Y-m-d H:i:s')]);   
       }
       Session::flash('success_message', "Admin detail updated Successfully.");
       return Redirect::to('admin/admins/list-subadmin');
	  
    }            
    }
    return view('admin.admins.editAdmin', ['title'=>$pageTitle, $activetab=>1, 'roleList'=>$roleArr, 'admin' => $admin]);
  }
    
}
?>