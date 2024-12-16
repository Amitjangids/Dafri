<?php
namespace App\Http\Middleware;
use Closure;
use Session;
use App\User;
class IsUserlogin
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * 
     */
    public function handle($request, Closure $next){        
        if (Session::has('user_id')){
            $user_id=Session::get('user_id');
            $session_id = Session::getId();
            $current_logged_user=User::where('user_device_ip','like','%'.$this->get_client_ip().'%')->where('is_logged_in', '1')->orderBy('last_login', 'desc')->first();
            // if(isset($current_logged_user))
            // {
            // if($current_logged_user->current_session_id!=$session_id)
            // {      
            //     return redirect('/logout');
            // }
            // }
            return $next($request);
        }else{
            return redirect('/personal-login');
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

}