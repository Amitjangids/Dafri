<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Mail;
use App\Mail\SendMailable;
use App\User;
use App\Agent;
use App\ReferralCommission;
use App\WithdrawRequest;
use App\InactiveAmount;
use App\Referalcode;
use App\Transaction;
use App\Notification;
use App\Fee;
use DB;

class updateOzowStatus extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateOzowStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status for ozow payment status';

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
    public function handle(Request $request) {

        $transactions = Transaction::where('trans_for', 'OZOW_EFT')->where('status', '2')->where('created_at', '>=', '2022-02-17')->get();

        
//        print_r($transactions);
//        if (!$transactions->isEmpty()) {
//            foreach ($transactions as $transaction) {
//               echo '<pre>';print_r($transaction->id);
//            }
//        }
//        exit;
        if (!$transactions->isEmpty()) {
            foreach ($transactions as $transaction) {
                $refrenceID = $transaction->id;
                $userId = $transaction->user_id;
//                $refrenceID = 560;

                $url = "https://api.ozow.com/GetTransactionByReference?siteCode=DAF-DAF-002&transactionReference=" . $refrenceID;

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//for debug only!
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($curl, CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type:application/json',
                            'ApiKey: vZPLdHyqixINAxEEQsOa6OrhoMYyfMsP'
                        )
                );

                $response = curl_exec($curl);
                curl_close($curl);

//                echo '<pre>';
//                print_r($response);
                $response = json_decode($response);
//                echo '<pre>';
//                print_r($response);
//                exit;

                if ($response) {
                    $paymntFlag = $response['0']->status;
                    if ($paymntFlag == 'Complete') {
                        $transID = $response['0']->transactionReference;
                        $refID = $response['0']->transactionId;
                        $billingDesc = $response['0']->statusMessage;
                        $transAmnt = $response['0']->amount;
                        $transCurrency = $response['0']->currencyCode;

                        $user = User::where('id', $userId)->first();
//                    echo '<pre>';
//                print_r($user);
//                exit;
                        if ($user->user_type == 'Personal') {

                            if ($user->account_category == "Silver") {
                                $fee_name = 'EFT_COMPLETE';
                            } else if ($user->account_category == "Gold") {
                                $fee_name = 'EFT_COMPLETE_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'EFT_COMPLETE_PLATINUM';
                            } else if ($user->account_category == "Private Wealth") {
                                $fee_name = 'EFT_COMPLETE_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'EFT_COMPLETE';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->first_name;
                            $loginURL = HTTP_PATH . "/personal-login";
                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        } else {
                            if ($user->account_category == "Gold") {
                                $fee_name = 'MERCHANT_EFT_COMPLETE_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_EFT_COMPLETE_PLATINUM';
                            } else if ($user->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_EFT_COMPLETE_Enterprises';
                            } else {
                                $fee_name = 'MERCHANT_EFT_COMPLETE_GOLD';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->business_name;
                            $loginURL = HTTP_PATH . "/business-login";
                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        }

                        $userWallet = ($user->wallet_amount + ($transAmnt - $fee_amount));
                        Transaction::where("id", $transID)->update(['real_value'=>($transAmnt - $fee_amount),'user_close_bal' => $userWallet, 'fees' => $fee_amount, 'receiver_fees' => $fee_amount, 'receiver_currency' => $user->currency,'cron_update' => 1, 'refrence_id' => $refID, 'billing_description' => $billingDesc, 'status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

                        
                        $userWalletUSD = $this->fetchCurrencyRate($user->currency, $userWallet);
                        
                        $this->updateCard($userWalletUSD, $userWallet, $user);
                        
//                        if ($user->user_type == 'Personal') {
//                            if ($userWalletUSD >= 21000 and $userWalletUSD <= 50000) {
//                                User::where('id', $userId)->update(['account_category' => 'Gold', 'wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                                $this->sendLevelChangeMail($userId, 'Gold');
//                            } else if ($userWalletUSD >= 50000 and $userWalletUSD <= 100000) {
//                                User::where('id', $userId)->update(['account_category' => 'Platinum', 'wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                                $this->sendLevelChangeMail($userId, 'Platinum');
//                            } else if ($userWalletUSD >= 100000) {
//                                User::where('id', $userId)->update(['account_category' => 'Private Wealth', 'wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                                $this->sendLevelChangeMail($userId, 'Private Wealth');
//                            } else {
//                                User::where('id', $userId)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                            }
//                        } else if ($user->user_type == 'Business') {
//                            if ($userWalletUSD <= 1000000) {
//                                User::where('id', $userId)->update(['account_category' => 'Gold', 'wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                                $this->sendLevelChangeMail($userId, 'Gold');
//                            } else if ($userWalletUSD > 1000000 and $userWalletUSD <= 5000000) {
//                                User::where('id', $userId)->update(['account_category' => 'Platinum', 'wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                                $this->sendLevelChangeMail($userId, 'Platinum');
//                            } else if ($userWalletUSD > 5000000) {
//                                User::where('id', $userId)->update(['account_category' => 'Enterprises', 'wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                                $this->sendLevelChangeMail($userId, 'Enterprises');
//                            } else {
//                                User::where('id', $userId)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                            }
//                        } else {
//                            User::where('id', $userId)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);
//                        }

                        $emailId = $user->email;

                        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hey</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your account has been credited with ' . $transCurrency . ' ' . $transAmnt . ' and fees for this transaction is ' . $fee_amount . '. The transaction ID is ' . $transID . ' and reference ID is ' . $refID . '.<br><br>If this is not you, please contact DafriBank Admin.<br><br>Please contact DafriBank Admin for any assistance.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginURL . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><a href="' . $loginURL . '" style="color: #1381D0; text-decoration: none; font-size: 18px">Head to your dashboard </a> to see more information on this payment<p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody> </table></body></html>';
                        $emailSubject = "DafriBank Digital | Account has been credited with " . $transCurrency . " " . $transAmnt;
//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                        $emailData['subject'] = $emailSubject;
                        $emailData['userName'] = $userName;
                        $emailData['amount'] = $transCurrency . ' ' . $transAmnt;
                        $emailData['fee_amount'] = $fee_amount;
                        $emailData['transID'] = $transID;
                        $emailData['refID'] = $refID;
                        $emailData['loginLnk'] = $loginURL;

                        Mail::send('emails.accountCredit', $emailData, function ($message)use ($emailData, $emailId) {
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

                        echo "Your Transaction has been Successfull.";
                        //Session::flash('success_message', "Your Wallet has been credited with ".$transCurrency." ".$transAmnt);
                        //return Redirect::to('auth/add-fund');	
//                    $fundAmnt = base64_encode($transCurrency . " " . $transAmnt);
//                    return Redirect::to('auth/success-add-fund/' . base64_encode($transID) . '/' . base64_encode($refID) . '/' . $fundAmnt);
                    } else if ($paymntFlag == 'Cancelled') {
                        $transID = $response['0']->transactionReference;
                        $refID = $response['0']->transactionId;
                        $billingDesc = $response['0']->statusMessage;
                        $transAmnt = $response['0']->amount;
                        $transCurrency = $response['0']->currencyCode;

                        $user = User::where('id', $userId)->first();
                        if ($user->user_type == 'Personal') {

                            if ($user->account_category == "Silver") {
                                $fee_name = 'EFT_CANCELLED';
                            } else if ($user->account_category == "Gold") {
                                $fee_name = 'EFT_CANCELLED_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'EFT_CANCELLED_PLATINUM';
                            } else if ($user->account_category == "Private Wealth") {
                                $fee_name = 'EFT_CANCELLED_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'EFT_CANCELLED';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->first_name;

                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        } else {
                            if ($user->account_category == "Gold") {
                                $fee_name = 'MERCHANT_EFT_CANCELLED_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_EFT_CANCELLED_PLATINUM';
                            } else if ($user->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_EFT_CANCELLED_Enterprises';
                            } else {
                                $fee_name = 'MERCHANT_EFT_CANCELLED_GOLD';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->business_name;

                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        }

                        Transaction::where("id", $transID)->update(['fees' => $fee_amount, 'refrence_id' => $refID, 'billing_description' => $billingDesc, 'status' => 3, 'updated_at' => date('Y-m-d H:i:s')]);

                        $userWallet = ($user->wallet_amount - $fee_amount);
                        User::where('id', $userId)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);

                        $emailId = $user->email;

                        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hey</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your transaction for amount  ' . $transCurrency . ' ' . $transAmnt . ' has been cancelled by issuing bank. Fees for this transaction is ' . $transCurrency . ' ' . $fee_amount . '.<br><br>If this is not you, please contact DafriBank Admin.<br><br>Please contact DafriBank Admin for any assistance.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><a href="' . HTTP_PATH . '/business-login" style="color: #1381D0; text-decoration: none; font-size: 18px">Head to your dashboard </a> to see more information on this payment<p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody> </table></body></html>';
                        $emailSubject = "DafriBank Digital | Transaction Cancelled";
                        $emailData['subject'] = $emailSubject;
                        $emailData['userName'] = $userName;
                        $emailData['amount'] = $transCurrency . ' ' . $transAmnt;
                        $emailData['fee_amount'] = $fee_amount;
                        $emailData['transCurrency'] = $transCurrency;
                        $emailData['transID'] = $transID;
                        $emailData['refID'] = $refID;

                        Mail::send('emails.transactionCancelled', $emailData, function ($message)use ($emailData, $emailId) {
                            $message->to($emailId, $emailId)
                                    ->subject($emailData['subject']);
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

                        echo "Your Transaction has been Unsuccessfull.";
//                    Session::put('error_session_message', "Your Transaction has been Unsuccessfull.");
//            Session::flash('error_message', "Your Transaction has been Unsuccessfull.");
//                    return Redirect::to('auth/add-fund');
                    } else if ($paymntFlag == 'Error') {
                        $transID = $response['0']->transactionReference;
                        $refID = $response['0']->transactionId;
                        $billingDesc = $response['0']->statusMessage;
                        $transAmnt = $response['0']->amount;
                        $transCurrency = $response['0']->currencyCode;

                        $user = User::where('id', $userId)->first();
                        if ($user->user_type == 'Personal') {
                            if ($user->account_category == "Silver") {
                                $fee_name = 'EFT_ERROR';
                            } else if ($user->account_category == "Gold") {
                                $fee_name = 'EFT_ERROR_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'EFT_ERROR_PLATINUM';
                            } else if ($user->account_category == "Private Wealth") {
                                $fee_name = 'EFT_ERROR_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'EFT_ERROR';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->first_name;

                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        } else {
                            if ($user->account_category == "Gold") {
                                $fee_name = 'MERCHANT_EFT_ERROR_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_EFT_ERROR_PLATINUM';
                            } else if ($user->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_EFT_ERROR_Enterprises';
                            } else {
                                $fee_name = 'MERCHANT_EFT_ERROR_GOLD';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->business_name;

                            if ($user->referral != 'na') {
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => $userId,
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $refrlComm,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        }

                        $userWallet = ($user->wallet_amount - $fee_amount);
                        User::where('id', $userId)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);

                        $notif = new Notification([
                            'user_id' => $user->id,
                            'notif_subj' => $emailSubject,
                            'notif_body' => $emailBody,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $notif->save();

                        Transaction::where("id", $transID)->update(['fees' => $fee_amount, 'refrence_id' => $refID, 'billing_description' => $billingDesc, 'status' => 5, 'updated_at' => date('Y-m-d H:i:s')]);

                        $emailId = $user->email;

                        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hey</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your transaction for amount   ' . $transCurrency . ' ' . $transAmnt . ' has been failed. Fees for this transaction is ' . $transCurrency . ' ' . $fee_amount . '.<br><br>If this is not you, please contact DafriBank Admin.<br><br>Please contact DafriBank Admin for any assistance.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><a href="' . HTTP_PATH . '/business-login" style="color: #1381D0; text-decoration: none; font-size: 18px">Head to your dashboard </a> to see more information on this payment<p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody> </table></body></html>';
                        $emailSubject = "DafriBank Digital | Transaction Failed";
                        $emailData['subject'] = $emailSubject;
                        $emailData['userName'] = $userName;
                        $emailData['amount'] = $transCurrency . ' ' . $transAmnt;
                        $emailData['fee_amount'] = $fee_amount;
                        $emailData['transCurrency'] = $transCurrency;
                        $emailData['transID'] = $transID;
                        $emailData['refID'] = $refID;

                        Mail::send('emails.verifyEFTPaymentFailed', $emailData, function ($message)use ($emailData, $emailId) {
                            $message->to($emailId, $emailId)
                                    ->subject($emailData['subject']);
                        });

//            Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                        echo "Your Transaction has been Unsuccessfull.";
//                    Session::put('error_session_message', "Your Transaction has been Unsuccessfull.");
//            Session::flash('error_message', "Your Transaction has been Unsuccessfull.");
//                    return Redirect::to('auth/add-fund');
                    } else if ($paymntFlag == 'Abandoned') {
                        $transID = $response['0']->transactionReference;
                        $refID = $response['0']->transactionId;
                        $billingDesc = $response['0']->statusMessage;
                        $transAmnt = $response['0']->amount;
                        $transCurrency = $response['0']->currencyCode;

                        $user = User::where('id', $userId)->first();
                        if ($user->user_type == 'Personal') {
                            if ($user->account_category == "Silver") {
                                $fee_name = 'EFT_ABANDONED';
                            } else if ($user->account_category == "Gold") {
                                $fee_name = 'EFT_ABANDONED_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'EFT_ABANDONED_PLATINUM';
                            } else if ($user->account_category == "Private Wealth") {
                                $fee_name = 'EFT_ABANDONED_PRIVATE_WEALTH';
                            } else {
                                $fee_name = 'EFT_ABANDONED';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->first_name;

                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        } else {
                            if ($user->account_category == "Gold") {
                                $fee_name = 'MERCHANT_EFT_ABANDONED_GOLD';
                            } else if ($user->account_category == "Platinum") {
                                $fee_name = 'MERCHANT_EFT_ABANDONED_PLATINUM';
                            } else if ($user->account_category == "Enterprises") {
                                $fee_name = 'MERCHANT_EFT_ABANDONED_Enterprises';
                            } else {
                                $fee_name = 'MERCHANT_EFT_ABANDONED_GOLD';
                            }

                            $fees = Fee::where('fee_name', $fee_name)->first();
                            $fee_amount = ($transAmnt * $fees->fee_value) / 100;
                            $userName = $user->business_name;

                            if ($user->referral != 'na') {
                                $currency = $user->currency;
                                $refrlComm = ($fee_amount * 25) / 100;
                                $refrlComm = number_format($refrlComm, 2, '.', ',');

                                if ($currency != 'USD') {

                                    $amountt = $refrlComm;
                                    $convr_fee_name = 'CONVERSION_FEE';
                                    $fees_convr = Fee::where('fee_name', $convr_fee_name)->first();
                                    $conversion_feet = $fees_convr->fee_value;

                                    $user_invited_amount1 = $amountt - ($amountt * $conversion_feet) / 100;

                                    $host_currency = trim($currency);
                                    $user_currency = 'USD';

                                    $convertedCurrArr = $this->convertCurrency($host_currency, $user_currency, $user_invited_amount1);
                                    $convertedCurrArr = explode('##', $convertedCurrArr);
                                    $user_invited_amountt = $convertedCurrArr[0];
                                } else {
                                    $user_invited_amountt = $refrlComm;
                                }

//                                echo '<pre>';print_r($convertedCurrArr);exit;

                                $referlCode = 'refid=' . $user->referral;
                                $referrer = Referalcode::where('referal_link', $referlCode)->first();
                                if (!empty($referrer)) {
                                    $refComm = new ReferralCommission([
                                        'user_id' => Session::get('user_id'),
                                        'referrer_id' => $referrer->user_id,
                                        'amount' => $user_invited_amountt,
                                        'action' => 'WALLET_TOPUP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                                    $refComm->save();
                                }
                            }
                        }

                        $userWallet = ($user->wallet_amount - $fee_amount);
                        User::where('id', $userId)->update(['wallet_amount' => $userWallet, 'updated_at' => date('Y-m-d H:i:s')]);

                        Transaction::where("id", $transID)->update(['fees' => $fee_amount, 'refrence_id' => $refID, 'billing_description' => $billingDesc, 'status' => 6, 'updated_at' => date('Y-m-d H:i:s')]);

                        $emailId = $user->email;

                        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hey</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your transaction for amount   ' . $transCurrency . ' ' . $transAmnt . ' has been Abandoned. Fees for this transaction is ' . $transCurrency . ' ' . $fee_amount . '.<br><br>If this is not you, please contact DafriBank Admin.<br><br>Please contact DafriBank Admin for any assistance.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><a href="' . HTTP_PATH . '/business-login" style="color: #1381D0; text-decoration: none; font-size: 18px">Head to your dashboard </a> to see more information on this payment<p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody> </table></body></html>';
                        $emailSubject = "DafriBank Digital | Transaction Abandoned";

                        $emailData['subject'] = $emailSubject;
                        $emailData['userName'] = $userName;
                        $emailData['amount'] = $transCurrency . ' ' . $transAmnt;
                        $emailData['fee_amount'] = $fee_amount;
                        $emailData['transCurrency'] = $transCurrency;
                        $emailData['transID'] = $transID;
                        $emailData['refID'] = $refID;

                        Mail::send('emails.verifyEFTPaymentAbandoned', $emailData, function ($message)use ($emailData, $emailId) {
                            $message->to($emailId, $emailId)
                                    ->subject($emailData['subject']);
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

//            Session::flash('error_message', "Your Transaction has been Unsuccessfull.");
                        echo "Your Transaction has been Unsuccessfull.";
//                    Session::put('error_session_message', "Your Transaction has been Unsuccessfull.");
//                    return Redirect::to('auth/add-fund');
                    } else if ($paymntFlag == 'PendingInvestigation') {
                        $transID = $response['0']->transactionReference;
                        $refID = $response['0']->transactionId;
                        $billingDesc = $response['0']->statusMessage;
                        $transAmnt = $response['0']->amount;
                        $transCurrency = $response['0']->currencyCode;

                        Transaction::where("id", $transID)->update(['refrence_id' => $refID, 'billing_description' => $billingDesc, 'status' => 7, 'updated_at' => date('Y-m-d H:i:s')]);

                        $user = User::where('id', $userId)->first();
                        $emailId = $user->email;
                        if ($user->user_type == 'Personal') {
                            $userName = $user->first_name;
                        } else {
                            $userName = $user->business_name;
                        }

                        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hey</span> ' . $userName . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Your transaction for amount   ' . $transCurrency . ' ' . $transAmnt . ' has been Pending for Investigation.<br><br>If this is not you, please contact DafriBank Admin.<br><br>Please contact DafriBank Admin for any assistance.<br><br>If this is not you, please contact DafriBank Admin.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . HTTP_PATH . '/business-login"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><a href="' . HTTP_PATH . '/business-login" style="color: #1381D0; text-decoration: none; font-size: 18px">Head to your dashboard </a> to see more information on this payment<p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody> </table></body></html>';
                        $emailSubject = "DafriBank Digital | Transaction Pending for Investigation";
                        Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

                        $notif = new Notification([
                            'user_id' => $user->id,
                            'notif_subj' => $emailSubject,
                            'notif_body' => $emailBody,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $notif->save();

//            Session::flash('error_message', "Your Transaction is Pending for Investigation.");
//                    Session::put('error_session_message', "Your Transaction is Pending for Investigation.");
                        echo 'Your Transaction is Pending for Investigation.';
//                    return Redirect::to('auth/add-fund');
                    } else {
                        echo "Invalid Status Code!";
//                    exit;
                    }
                }
            }
        }
    }
    
    public function sendLevelChangeMail($user_id, $newLevel) {
        $user = User::where('id', $user_id)->first();
        if ($user->user_type == 'Personal') {
            $user_name = $user->first_name;
            $loginURL = HTTP_PATH . "/personal-login";
        } else if ($user->user_type == 'Business') {
            $user_name = $user->business_name;
            $loginURL = HTTP_PATH . "/business-login";
        } else if ($user->user_type == 'Agent' and $user->first_name != "") {
            $user_name = $user->first_name;
            $loginURL = HTTP_PATH . "/personal-login";
        } else if ($user->user_type == 'Agent' and $user->business_name != "") {
            $user_name = $user->business_name;
            $loginURL = HTTP_PATH . "/business-login";
        }

        if ($user->user_type == 'Business' && strtolower(trim($newLevel)) == 'gold') {
            return true;
        }

        $emailId = $user->email;
        $emailBody = '<!DOCTYPE html><html><head><title>DafriBank Receipt Welcome</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><style type="text/css">body {padding: 0;margin: 0}table {border-spacing: 0px !important;}</style></head><body><table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: \'Sora\', sans-serif !important; "><tbody><tr><td align="center"><table class="col-600" width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-spacing:0px !important;"><tr align="center"><td style="padding: 50px 0; width: 100%"><a href="#"><img src="' . HTTP_PATH . '/public/img/dafribank-logo-black.png" width="180"></a></td></tr><tr><td><table style="width: 750px; margin: 0 auto; background: #fff;border-radius: 40px; border: 1px solid #C7C7C7; padding: 30px 50px;"><tr><td><h4 style="font-size: 25px; font-weight: 400"><span style="color: #A2A2A2">Hi</span> ' . $user_name . ',</h4><p style="color: #A2A2A2; font-size: 16px; line-height: 27px; font-weight: 400; ">Congratulations! You have earned a ' . $newLevel . ' membership in DafriBank.<br><br>From now, you are eligible to get the benefits of ' . $newLevel . ' Membership.<br><br>Keep Going.<br><br>If you need any assistance, please e-mail us at <a href="mailto:hello@dafribank.com" style="color: #1381D0; text-decoration: none;"> hello@dafribank.com </a><br>or call us on 0115 685 053.<br><br>Regards,<br>DafriBank Team</p></td></tr><tr><td align="center" style="display: block; width: 100%; padding-bottom: 36px; margin-top: 30px;"><a  style="display: inline-block; font-size: 18px;border-radius: 20px;padding: 15px 39px; font-weight: 700; color: #fff; text-decoration: none; background: #000;box-shadow: 8px 8px 0 rgba(0,0,0,0.1);" href="' . $loginURL . '"> View your dashboard</a></td></tr></table></td></tr><tr><td style="padding: 40px 0" align="center"><p style="font-size: 16px; color:#8E8E8E; line-height: 24px;">DafriBank Digital is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062 and in Botswana with CIPA Number: 2854468. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Call 011 568 5053 or visit our <a href="' . HTTP_PATH . '/faq" style="color: #1381D0; text-decoration: none;"> FAQ </a> page<br><br>© ' . date("Y") . ' DafriBank Digital. All Rights Reserved. </p></td></tr></table></td></tr></tbody></table></body></html>';
        $emailSubject = "DafriBank Digital | You have earned a " . $newLevel . " membership";
//        Mail::to($emailId)->send(new SendMailable($emailBody, $emailSubject, Null));

        $emailData['subject'] = $emailSubject;
        $emailData['name'] = $user_name;
        $emailData['newLevel'] = $newLevel;
        $emailData['loginURL'] = $loginURL;

        Mail::send('emails.upgradeLevel', $emailData, function ($message)use ($emailData, $emailId) {
            $message->to($emailId, $emailId)
                    ->subject($emailData['subject']);
        });
    }
    
    private function fetchCurrencyRate($currency, $amount) {
        $apikey = '1c5849e1679846c9ac66887bbdd4d76f';
        if ($currency != 'USD') {
            if ($currency == 'EURO') {
                $query = "EUR_USD";
            } else {
                $query = $currency . "_USD";
            }
            $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
            //"https://api.currconv.com/api/v7/convert?q=".$query."&compact=ultra&apiKey=".$apikey	
            $json = file_get_contents($curr_req);
            $obj = json_decode($json, true);
            //print_R($obj);
            $val = floatval($obj[$query]);
            $total = $val * $amount;
            return $total;
        } else {
            return $amount;
        }
    }
    
    public function updateCard($benificry_wallet_usd = null, $benificry_wallet = null, $user) {

        if ($user->user_type == 'Personal') {
            if ($benificry_wallet_usd >= 21000 and $benificry_wallet_usd <= 50000 and $user->account_category == 'Silver') {
                User::where('id', $user->id)->update(['account_category' => 'Gold', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                $this->sendLevelChangeMail($user->id, 'Gold');
            } else if ($benificry_wallet_usd >= 50000 and $benificry_wallet_usd <= 100000 and ($user->account_category == 'Silver' || $user->account_category == 'Gold')) {
                User::where('id', $user->id)->update(['account_category' => 'Platinum', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                $this->sendLevelChangeMail($user->id, 'Platinum');
            } else if ($benificry_wallet_usd >= 100000 and ($user->account_category == 'Silver' || $user->account_category == 'Gold' || $user->account_category == 'Platinum')) {
                User::where('id', $user->id)->update(['account_category' => 'Private Wealth', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                $this->sendLevelChangeMail($user->id, 'Private Wealth');
            } else {
                User::where('id', $user->id)->update(['wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            }
        } else if ($user->user_type == 'Business') {
            if ($benificry_wallet_usd > 1000000 and $benificry_wallet_usd <= 5000000 and $user->account_category == 'Gold') {
                User::where('id', $user->id)->update(['account_category' => 'Platinum', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                $this->sendLevelChangeMail($user->id, 'Platinum');
            } else if ($benificry_wallet_usd > 5000000 and ($user->account_category == 'Gold' || $user->account_category == 'Platinum')) {
                User::where('id', $user->id)->update(['account_category' => 'Enterprises', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                $this->sendLevelChangeMail($user->id, 'Enterprises');
            } else {
                User::where('id', $user->id)->update(['wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
            }
        } else {
            if ($user->user_type == 'Agent' && $user->first_name != '') {
                if ($benificry_wallet_usd >= 21000 and $benificry_wallet_usd <= 50000 and $user->account_category == 'Silver') {
                    User::where('id', $user->id)->update(['account_category' => 'Gold', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $this->sendLevelChangeMail($user->id, 'Gold');
                } else if ($benificry_wallet_usd >= 50000 and $benificry_wallet_usd <= 100000 and ($user->account_category == 'Silver' || $user->account_category == 'Gold')) {
                    User::where('id', $user->id)->update(['account_category' => 'Platinum', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $this->sendLevelChangeMail($user->id, 'Platinum');
                } else if ($benificry_wallet_usd >= 100000 and ($user->account_category == 'Silver' || $user->account_category == 'Gold' || $user->account_category == 'Platinum')) {
                    User::where('id', $user->id)->update(['account_category' => 'Private Wealth', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $this->sendLevelChangeMail($user->id, 'Private Wealth');
                } else {
                    User::where('id', $user->id)->update(['wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                }
            } else if ($user->user_type == 'Agent' && $user->business_name != '') {
                if ($benificry_wallet_usd > 1000000 and $benificry_wallet_usd <= 5000000 and $user->account_category == 'Gold') {
                    User::where('id', $user->id)->update(['account_category' => 'Platinum', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $this->sendLevelChangeMail($user->id, 'Platinum');
                } else if ($benificry_wallet_usd > 5000000 and ($user->account_category == 'Gold' || $user->account_category == 'Platinum')) {
                    User::where('id', $user->id)->update(['account_category' => 'Enterprises', 'wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                    $this->sendLevelChangeMail($user->id, 'Enterprises');
                } else {
                    User::where('id', $user->id)->update(['wallet_amount' => $benificry_wallet, 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
        }
    }

}
