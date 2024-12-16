<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\SendMailable;
use App\User;
use App\Agent;
use App\WithdrawRequest;
use App\InactiveAmount;
use App\Transaction;
use DB;

class updateWithdrawRequests extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateWithdrawRequests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel Agent Withdraw Request After 24 Hour';

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
        $withdrawReq = WithdrawRequest::where('req_type', 'Agent')->get();
//        $withdrawReq = WithdrawRequest::where('req_type', 'Agent')->where('id',286)->get();
        
    // echo '<pre>';print_r($withdrawReq);exit;
        if (Count($withdrawReq) > 0) {
            $currDateTime = date('Y-m-d H:i:s');
            foreach ($withdrawReq as $req) {
                $reqDateTime = $req->created_at;
                $hourdiff = round((strtotime($currDateTime) - strtotime($reqDateTime)) / 3600, 1);
                if ($hourdiff >= 24) {
                    //Update User Wallet (Return Withdraw Request Amount) Start  
                    $user = User::where('id', $req->user_id)->first();

                    //Update User Wallet (Return Withdraw Request Amount) End
                    //Email Start

                    if ($user->user_type == "Personal") {
                        $userName = $user->first_name . " " . $user->last_name;
                        $lognURL = "https://www.dafribank.com/personal-login";
                    } else if ($user->user_type == "Business") {
                        $userName = $user->director_name;
                        $lognURL = "https://www.dafribank.com/business-login";
                    } else if ($user->user_type == "Agent" && $user->first_name != "") {
                        $userName = $user->first_name . " " . $user->last_name;
                        $lognURL = "https://www.dafribank.com/personal-login";
                    } else if ($user->user_type == "Agent" && $user->director_name != "") {
                        $userName = $user->director_name;
                        $lognURL = "https://www.dafribank.com/business-login";
                    }
                    $agent = Agent::where('id', $req->agent_id)->first();
                    if (!empty($agent)) {
                        $agentName = $agent->first_name . ' ' . $agent->last_name;
                        $emailId = $user->email;
                        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td><table class="col-600" width="900" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="https://www.dafribank.com/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400; color: #A2A2A2;"><span>Dear </span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your withdraw request of amount ' . $user->currency . ' ' . $req->amount . ' has been cancelled as agent (' . $agentName . ') did not take any action on this request within 24 hours. Your amount has been refunded to your account. <br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $lognURL . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E ">Have questions or help ? Call 011 568 5053 or visit our <a href="https://www.dafribank.com/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page.</p></td></tr></table></td></tr></tbody></table></body></html>';
                        $emailSubject = "DafriBank Digital | Agent Withdraw Request Cancelled";
                        $emailData['subject'] = $emailSubject;
                        $emailData['userName'] = $userName;
                        $emailData['agentName'] = $agentName;
                        $emailData['amount'] = $req->amount;
                        $emailData['currency'] = $user->currency;
                        $emailData['loginLnk'] = $lognURL;

                        Mail::send('emails.updateWithdrawRqst', $emailData, function ($message)use ($emailData, $emailId) {
                            $message->to($emailId, $emailId)
                                    ->subject($emailData['subject']);
                        });
//			 Mail::to($emailId)->send(new SendMailable($emailBody,$emailSubject,Null));

                        $userWallet = $user->wallet_amount + $req->amount;
                        User::where('id', $req->user_id)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
                        
                        $inactivAmnt = InactiveAmount::where('withdraw_req_id', $req->id)->first();
                        Transaction::where('id', $inactivAmnt->trans_id)->update(['status' => 3, 'updated_at' => DB::raw('updated_at')]);

                        $withdrawAmount = $req->amount;
                        $tarns_req = Transaction::where('id', $inactivAmnt->trans_id)->first();
                        $trans = new Transaction([
                            "user_id" => $tarns_req->user_id,
                            "receiver_id" => 0,
                            "amount" => $withdrawAmount,
                            "fees" => 0,
                            "currency" => $tarns_req->currency,
                            "sender_fees" => 0,
                            "sender_currency" => $tarns_req->sender_currency,
                            "receiver_currency" => 'USD',
                            "trans_type" => 1,
                            "trans_to" => 'Dafri_Wallet',
                            "trans_for" => 'Withdraw##Agent(Refund)',
                            "refrence_id" => $tarns_req->id,
                            "user_close_bal" => $userWallet,
                            "real_value" => $withdrawAmount,
                            "status" => 1,
                            "created_at" => date('Y-m-d H:i:s'),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);
                        $trans->save();

                        
                        //Email End
                        InactiveAmount::where('withdraw_req_id', $req->id)->delete();
                        WithdrawRequest::where('id', $req->id)->delete();
                    }
                    
                }
            }
        }
    }

}
