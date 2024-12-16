<?php
namespace App\Http\Middleware;
use Closure;
use Session;
class web
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * 
     */
    public function autologin($id){    
    echo "heelo"; die;
    }
}