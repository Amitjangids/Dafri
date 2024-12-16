<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

class updateKycStatus extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateKycStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update KYC Status After 1 Hour';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
//        $users = User::where('is_kyc_done', 0)->where('kyc_applicant_id', '618e6adebc07f70001a2dd46')->get();
        $users = User::where('is_kyc_done', 0)->where('kyc_applicant_id', '!=', '')->get();
        if ($users) {
//            echo '<pre>';print_r($users);exit;
            foreach ($users as $user) {
                $applicantId = $user->kyc_applicant_id;
                $user_id = $user->id;

//                $applicantExist = $this->fetchApplicant($applicantId);
//                echo '<pre>';print_r($applicantExist);exit;

                $applicantStatusData = $this->getDocumentStatus($applicantId);
                $applicantStatusStrMain = $this->getApplicantStatus($applicantId);
                
                
                if (isset($applicantStatusStrMain->reviewStatus) && $applicantStatusStrMain->reviewStatus != 'completed') {
                    
                  continue;
                }
//echo '<pre>123';
//                print_r($applicantStatusData);
//                exit;
                if (!empty($applicantStatusData)) {
                    foreach ($applicantStatusData as $key => $applicantStatusStr) {
                        
                        if($key == 'IDENTITY'){
                            $type = 'Identity Document';
                            $type1 = 'identity';
                        } else if($key == 'PROOF_OF_RESIDENCE'){
                            $type = 'Proof Of Address';
                            $type1 = 'address';
                        } else if($key == 'SELFIE'){
                            $type = 'Selfie';
                            $type1 = 'selfie';
                        }
                        
                        if($type1 == 'identity'){
                            $type2 = 'Identity Document';
                        } else if($type1 == 'address'){
                            $type2 = 'Proof Of Address Document';
                        } else {
                            $type2 = 'Selfie';
                        }
                        
                        if (isset($applicantStatusStr->reviewResult->reviewAnswer)) { 
                            $reviewAnswer = $applicantStatusStr->reviewResult->reviewAnswer;
                            if ($reviewAnswer == 'GREEN') {

                                User::where('id', $user_id)->update(array($type1.'_status' => '1', 'updated_at' => date('Y-m-d H:i:s')));

                                $emailId = $user->email;

                                if ($user->first_name != "") {
                                    $username = strtoupper($user->first_name);
                                    $lognLnk = HTTP_PATH . "/personal-login";
                                } else {
                                    $username = strtoupper($user->business_name);
                                    $lognLnk = HTTP_PATH . "/business-login";
                                }

                                $emailSubject = 'KYC information has been reviewed successfully';                                
                                $emailData['type'] = $type2;
                                $emailData['subject'] = $emailSubject;
                                $emailData['username'] = strtoupper($username);

                                Mail::send('emails.kycDocumentReviewd', $emailData, function ($message)use ($emailData, $emailId) {
                                    $message->to($emailId, $emailId)->subject($emailData['subject']);
                                });
                            } elseif ($reviewAnswer == 'RED') {

                                User::where('id', $user_id)->update(array($type1.'_status' => '2',  'updated_at' => date('Y-m-d H:i:s')));

                                $emailId = $user->email;

                                if ($user->first_name != "") {
                                    $username = strtoupper($user->first_name);
                                    $lognLnk = HTTP_PATH . "/personal-login";
                                } else {
                                    $username = strtoupper($user->business_name);
                                    $lognLnk = HTTP_PATH . "/business-login";
                                }

                                $emailSubject = 'Your KYC information was not approved';

                                $reson = '';
                                foreach($applicantStatusStr->imageReviewResults as $keys => $result){                                    
                                    if(isset($result->moderationComment)){
                                        $reson .= $result->moderationComment;
                                    }
                                    if(isset($result->clientComment)){
                                        $reson .= $result->clientComment;
                                    }
                                }
                                $emailData['reason'] = $reson;
                                $emailData['type'] = $type2;
                                $emailData['subject'] = $emailSubject;
                                $emailData['username'] = strtoupper($username);

                                Mail::send('emails.kycDocumentDeclined', $emailData, function ($message)use ($emailData, $emailId) {
                                    $message->to($emailId, $emailId)->subject($emailData['subject']);
                                });
                            }
                            echo 'Done for user ID : ' . $user_id;
                        }
                    }
                }
                
                
                $applicantStatusStr = $this->getApplicantStatus($applicantId);
                
//                echo '<pre>123';
//                print_r($applicantStatusStr);
//                exit;
                if ((isset($applicantStatusStr->code) && $applicantStatusStr->code == 404)) {
                    
                } else {
                    if (isset($applicantStatusStr->reviewResult->reviewAnswer)) {
                        $reviewAnswer = $applicantStatusStr->reviewResult->reviewAnswer;
                        if ($reviewAnswer == 'GREEN') {

                            User::where('id', $user_id)->update(array('is_kyc_done' => '1', 'is_verify' => '1', 'updated_at' => date('Y-m-d H:i:s')));

                            $emailId = $user->email;

                            if ($user->first_name != "") {
                                $username = strtoupper($user->first_name);
                                $lognLnk = HTTP_PATH . "/personal-login";
                            } else {
                                $username = strtoupper($user->business_name);
                                $lognLnk = HTTP_PATH . "/business-login";
                            }

                            $emailSubject = 'KYC information has been reviewed successfully';

                            
                            $emailData['subject'] = $emailSubject;
                            $emailData['username'] = strtoupper($username);

                            Mail::send('emails.kycReviewd', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subject']);
                            });
                        } elseif ($reviewAnswer == 'RED') {

                            if ($applicantStatusStr->reviewResult->reviewRejectType == 'FINAL') {
                                User::where('id', $user_id)->update(array('kyc_decline_type' => '2','is_kyc_done' => '2', 'is_verify' => '0', 'updated_at' => date('Y-m-d H:i:s')));
                            } else if ($applicantStatusStr->reviewResult->reviewRejectType == 'RETRY') {
                                User::where('id', $user_id)->update(array('kyc_decline_type' => '1','is_kyc_done' => '2', 'is_verify' => '1', 'updated_at' => date('Y-m-d H:i:s')));
                            }


                            $emailId = $user->email;

                            if ($user->first_name != "") {
                                $username = strtoupper($user->first_name);
                                $lognLnk = HTTP_PATH . "/personal-login";
                            } else {
                                $username = strtoupper($user->business_name);
                                $lognLnk = HTTP_PATH . "/business-login";
                            }

                            $emailSubject = 'Your KYC information was not approved';

                            $reson = '';                                   
                                    if(isset($applicantStatusStr->reviewResult->moderationComment)){
                                        $reson .= $applicantStatusStr->reviewResult->moderationComment;
                                    }
                                    if(isset($applicantStatusStr->reviewResult->clientComment)){
                                        $reson .= $applicantStatusStr->reviewResult->clientComment;
                                    }
                                $emailData['reason'] = $reson;
                            $emailData['subject'] = $emailSubject;
                            $emailData['username'] = strtoupper($username);

                            Mail::send('emails.kycDeclined', $emailData, function ($message)use ($emailData, $emailId) {
                                $message->to($emailId, $emailId)
                                        ->subject($emailData['subject']);
                            });
                        }
                        echo 'Done for user ID : ' . $user_id;
                    }
                }
            }
        }
//        echo 'done';
        exit;
    }

    public function getApplicantStatus($applicantId) {
        // https://developers.sumsub.com/api-reference/#getting-applicant-status-api
        $url = "/resources/applicants/" . $applicantId . "/status";
        $request = new GuzzleHttp\Psr7\Request('GET', SUMSUB_TEST_BASE_URL . $url);

        $response = $this->sendHttpRequest($request, $url);

        return json_decode($response->getBody());
    }

    public function getDocumentStatus($applicantId) {
        // https://developers.sumsub.com/api-reference/#getting-applicant-status-api
        $url = "/resources/applicants/" . $applicantId . "/requiredIdDocsStatus";
        $request = new GuzzleHttp\Psr7\Request('GET', SUMSUB_TEST_BASE_URL . $url);

        $response = $this->sendHttpRequest($request, $url);

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

        try {
            $response = $client->send($request);

            if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201) {
                echo "Error: " . $response->getStatusCode();
                exit;
            }
        } catch (GuzzleHttp\Exception\GuzzleException $e) {

            $response = $e->getResponse();
        }
//echo '<pre>';print_r($response);exit;
        return $response;
    }

    private function createSignature($ts, $httpMethod, $url, $httpBody) {
        return hash_hmac('sha256', $ts . strtoupper($httpMethod) . $url . $httpBody, SUMSUB_SECRET_KEY);
    }

    public function fetchApplicant($applicantId) {
        // https://developers.sumsub.com/api-reference/#creating-an-applicant


        $url = '/resources/applicants/' . $applicantId . '/status';
        $request = new GuzzleHttp\Psr7\Request('POST', SUMSUB_TEST_BASE_URL . $url);
        $request = $request->withHeader('Content-Type', 'application/json');

        $responseBody = $this->sendHttpRequest($request, $url)->getBody();
        echo '<pre>';
        print_r($responseBody);
        exit;
        return json_decode($responseBody)->{'id'};
    }

}
