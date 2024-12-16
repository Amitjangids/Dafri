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
use App\User;
use App\Mail\SendMailable;
use GuzzleHttp;
use GuzzleHttp\Psr7\MultipartStream;


class CronsController extends Controller {

    public function updateStatus() {

        $users = User::where('kyc_applicant_id', '61a0c735a5051900018cb207')->get();
//        $users = User::where('is_kyc_done', 0)->where('kyc_applicant_id', '!=', '')->get();
        if ($users) {
//            echo '<pre>';print_r($users);
            foreach ($users as $user) {
                $applicantId = $user->kyc_applicant_id;
                
                $applicantStatusStr = $this->getApplicantStatus($applicantId);
                echo '<pre>';print_r($applicantStatusStr);EXIT;
//                echo '<pre>';print_r($applicantStatusStr);
//echo "Applicant status (json string): " . $applicantStatusStr;

//                $response = $this->sendHttpRequest($request, $url);
//                $result = json_decode($response->getBody());
                
//                return $this->sendHttpRequest($request, $url);
//        return json_decode($response->getBody());
//
//                print_r($result->IDENTITY->imageReviewResults);
//                if ($result) {
//                    $username = $user->first_name;
//                    $emailId = $user->email;
//
//                    if (strtolower($user->user_type) == "personal") {
//                        $lognLnk = HTTP_PATH . "/personal-login";
//                    } else {
//                        $lognLnk = HTTP_PATH . "/business-login";
//                    }
//
//                    $emailSubject = 'KYC information has been reviewed successfully';
//                    //$emailBody = 'Dear '.$username.',<br><br>We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank '.$userInfo->user_type.' account has now been approved. <a href="'.$lognLnk.'" target="_blank">Click here</a> to log in to your account.<br><br>We wish you an awesome banking experience with us.<br><br>Best regards,<br>The DafriBank Team';
//                    $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400;color: #A2A2A2"><span>Dear </span> ' . $username . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">We are happy to inform you that your KYC information has been reviewed successfully, and your DafriBank ' . $user->user_type . ' account has now been approved. <a href="' . $lognLnk . '" target="_blank">Click here</a> to log in to your account.<br><br>We wish you an awesome banking experience with us.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E ">Have questions or help ? Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page.</p></td></tr></table></td></tr></tbody></table></body></html>';
//                    Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));
//                }
                
            }
        }
        echo 'done';exit;
    }
    
    public function getApplicantStatus($applicantId)
        // https://developers.sumsub.com/api-reference/#getting-applicant-status-api
    {
        $url = "/resources/applicants/" . $applicantId . "/requiredIdDocsStatus";
        $request = new GuzzleHttp\Psr7\Request('GET', SUMSUB_TEST_BASE_URL . $url);

        $response =  $this->sendHttpRequest($request, $url);
        return json_decode($response->getBody());
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

}

?>