<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\SendMailable;
use App\User;
use App\Agent;
use App\InvitedUser;
use App\Transaction;
use DB;

class updateFundTransfer extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateFundTransfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Fund Transfer After 30 days';

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
        $invtdUsrs = InvitedUser::where('status', 0)->orderBy('id', 'DESC')->get();
        if (Count($invtdUsrs) > 0) {
            $currDateTime = date('Y-m-d H:i:s');
            foreach ($invtdUsrs as $invtdUsr) { 
                $reqDateTime = $invtdUsr->created_at;
                $hourdiff = round((strtotime($currDateTime) - strtotime($reqDateTime)) / 3600, 1);
                if ($hourdiff >= 720) {
                    //Update User Wallet (Return Withdraw Request Amount) Start  
                    $user = User::where('id', $invtdUsr->host_id)->first();
                    $userWallet = $user->wallet_amount + $invtdUsr->amount;
                    User::where('id', $invtdUsr->host_id)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    //Update User Wallet (Return Withdraw Request Amount) End

                    if ($user->user_type == "Personal") {
                        $userName = $user->first_name ." ". $user->last_name;
                        $lognURL = "https://www.dafribank.com/personal-login";
                    }else if ($user->user_type == "Business") {
                        $userName = $user->director_name;
                        $lognURL = "https://www.dafribank.com/business-login";
                    } else if ($user->user_type == "Agent" && $user->first_name != "") {
                        $userName = $user->first_name ." ". $user->last_name;
                        $lognURL = "https://www.dafribank.com/personal-login";
                    }else if ($user->user_type == "Agent" && $user->director_name != "") {
                        $userName = $user->director_name;
                        $lognURL = "https://www.dafribank.com/business-login";
                    }
                         
                    $emailSubject = "DafriBank Digital | Refund initiated for amount " . $user->currency . ' ' . $invtdUsr->amount;

                    $trasactionId = $invtdUsr->trans_id;
                    $invite_email = $invtdUsr->Invite_email;
                    $emailId = $user->email;
                    $emailData['subject'] = $emailSubject;
                    $emailData['userName'] = $userName;
                    $emailData['invite_email'] = $invite_email;
                    $emailData['transaction_id'] = $trasactionId;
                    $emailData['amount'] = $user->currency . ' ' . $invtdUsr->amount;
                    $emailData['loginLnk'] = $lognURL;
                    Mail::send('emails.fundRefund', $emailData, function ($message)use ($emailData, $emailId) {
                        $message->to($emailId, $emailId)
                                ->subject($emailData['subject']);
                    });
                    
                    Transaction::where('id', $invtdUsr->trans_id)->update(['status' => 3,'updated_at' => DB::raw('updated_at')]);

                    $tarns_req=Transaction::where('id', $invtdUsr->trans_id)->first();
                    $trans = new Transaction([
                        "user_id" =>$user->id,
                        "receiver_id" => 0,
                        "amount" => $tarns_req->amount,
                        "fees" =>0,
                        "currency" => $tarns_req->currency,
                        "sender_fees" => 0,
                        "sender_currency" => $tarns_req->sender_currency,
                        "receiver_currency" => 'USD',
                        "trans_type" => 1,
                        "trans_to" => 'Dafri_Wallet',
                        "trans_for" => 'Fund Transfer (Refund)',
                        "refrence_id" => $tarns_req->id,
                        "user_close_bal" => $userWallet,
                        "real_value" => $tarns_req->amount,
                        "billing_description" => 'IP : ' . $this->get_client_ip(),
                        "status" => 1,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();

                    InvitedUser::where('id', $invtdUsr->id)->update(['status' => 2]);
                }
            }
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
