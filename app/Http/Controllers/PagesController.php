<?php

namespace App\Http\Controllers;

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
use App\Mail\SendMailable;
use App\WithdrawRequest;
use App\InactiveAmount;

class PagesController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function depositAPI() {

        $pageTitle = 'Deposit API';

        return view('pages.deposit_api', ['title' => $pageTitle]);
    }
    
    public function withdrawAPI() {

        $pageTitle = 'Withdraw API';

        return view('pages.withdraw_api', ['title' => $pageTitle]);
    }

    

}

?>