<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Route::get('/', ['middleware' => ['adminAuth'], 'uses' => 'LoginController@index'])->name('index');
/*Route::get('users_report', function () {
  $users = DB::table('users')->get();
  return view('users_report',['users'=>$users]);	
});*/

Route::group(['middleware' => ['web']], function () {
  Route::get('autologin', function () {
      Session::forget('user_id');
      $encryption=$_GET['enctype'];
      $decrypted = Crypt::decryptString($encryption);
      list($crypted_token, $enc_iv) = explode("::", $encryption);;
      $cipher_method = 'aes-128-ctr';
      $enc_key = openssl_digest(php_uname(), 'SHA256', TRUE);
      $token = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
      $user = $decrypted;
      $action = $_GET['action'];
      Session::put('user_id', $user);
      return redirect()->intended('/'.$action);
  });
});

Route::get('/', 'HomesController@index');
Route::any('/get_content_blog', 'HomesController@get_content_blog');
Route::any('/webhook', 'UsersController@webhook'); 
Route::get('/personal-account', 'HomesController@personalAccount');
Route::get('/business-account', 'HomesController@businessAccount');
// Route::get('/personal-account', 'HomesController@personalAccount');
// Route::get('/business-account', 'HomesController@businessAccount');
Route::any('/dba-application', 'HomesController@dbaApplication');
Route::get('/about', 'HomesController@about');
Route::get('/learning-center', 'HomesController@learningCenter');
Route::any('/contact', 'HomesController@contact');
Route::get('/press', 'HomesController@press');
Route::get('/career', 'HomesController@career');
Route::get('/blogs', 'HomesController@blogs');
Route::get('/blog/{slug}', 'HomesController@singleBlog');
Route::get('/terms-condition', 'HomesController@termsCondition');
Route::get('/privacy-policy', 'HomesController@privacyPolicy');
Route::get('/private-banking', 'HomesController@privateBanking');
Route::get('/dafrixchange', 'HomesController@dafrixchange');
Route::get('/merchat-api', 'HomesController@merchatApi');
Route::get('/dba-currency', 'HomesController@dbaCurrency');
Route::get('/defi-loan', 'HomesController@defiLoan');
Route::get('/investor-relations', 'HomesController@investorRelations');
Route::get('/affiliate', 'HomesController@affiliate');
Route::get('/aml-policy', 'HomesController@amlPolicy');
Route::get('/press/{slug}', 'HomesController@pressDetail');
Route::get('/buy-cell-crypto', 'UsersController@buyCellCrypto');

Route::get('/epay', 'HomesController@spend');
Route::get('/earn', 'HomesController@save');
Route::get('/ecash', 'HomesController@budget');
Route::get('/gift-card', 'HomesController@borrow');

Route::get('/success/{slug}', 'HomesController@paymentsuccess');

Route::post('/users/uploadprofileimage', 'UsersController@uploadprofileimage');

Route::get('/templateTest', 'UsersController@templateTest');
Route::get('/update_record', 'UsersController@update_record');

Route::get('/deposit-api', 'PagesController@depositAPI');
Route::get('/withdraw-api', 'PagesController@withdrawAPI');

Route::get('/faq', 'HomesController@faqs');
Route::get('/debit-cards', 'HomesController@debitCards');
Route::get('/cookie-policy', 'HomesController@cookieNotice');
Route::get('/what-we-offer', 'HomesController@products');
Route::any('/online-payment', 'HomesController@onlinePayment');
Route::any('/setOnlinePayment/{base64Req}', 'HomesController@setOnlinePayment');
Route::any('/confirm-online-payment', 'HomesController@confirmOnlinePayment');

Route::get('/test', 'UsersController@test');
Route::get('/choose-account', 'UsersController@chooseAccount');
Route::any('/personal-account-registration', 'UsersController@personalRegister')->name('signUp');
Route::any('/personal-account-verification/{slug}', 'UsersController@personalVerify');
Route::any('/personal-kyc-verification/{slug}', 'UsersController@personalKycVerify');
Route::any('/personal-kyc-update/{slug}', 'UsersController@personalKycUpdate');
Route::any('/personal-login', 'UsersController@personalLogin')->name('signIn');

Route::any('/account-verification/{slug}', 'UsersController@otpVerify');

Route::any('/business-account-registration', 'UsersController@businessRegister');
Route::any('/business-account-verification/{slug}', 'UsersController@businessVerify');
Route::any('/business-kyc-verification/{slug}', 'UsersController@businessKycVerify');
Route::any('/business-kyc-update/{slug}', 'UsersController@businessKycUpdate');
Route::any('/business-login', 'UsersController@businessLogin')->name('login');
Route::any('/forgot-password', 'UsersController@forgotPassword');
Route::any('/verify-email', 'UsersController@verifyEmail');

Route::any('/reset-password/{slug}', 'UsersController@resetPassword');

Route::any('/overview', 'UsersController@overview');
Route::any('/resentOtp', 'UsersController@resentOtp');
Route::any('/resentVerifyOtp', 'UsersController@resentVerifyOtp');
Route::any('auth/affiliate-program', 'UsersController@affiliateProgram');
Route::any('auth/generateReferral', 'UsersController@generateReferral');
Route::any('auth/deleteRefCode/{slug}', 'UsersController@deleteRefCode');
Route::any('auth/my-account', 'UsersController@myAccount');
Route::any('auth/account-detail', 'UsersController@accountDetail');
Route::any('auth/add-fund', 'UsersController@addFund');
Route::any('auth/add-credit-fund', 'UsersController@addCreditFund');
Route::any('auth/authencticatePayment', 'UsersController@authencticatePayment');
Route::any('auth/transactions', 'UsersController@transactions');
Route::any('auth/verifyEFTPayment', 'UsersController@verifyEFTPayment');
Route::any('auth/add-recipient', 'UsersController@addRecipient');
Route::any('auth/check-card-payment', 'UsersController@check_card_payment');

Route::any('auth/fund-transfer-phone/{phone}', 'UsersController@fundTransferPhone');
Route::any('auth/fund-transfer', 'UsersController@fundTransfer');
Route::any('auth/fund-transfer/{slug}', 'UsersController@fundTransfer');
Route::any('auth/airtime', 'UsersController@airtime');
Route::any('auth/topup', 'UsersController@topup');
Route::any('getOperator', 'UsersController@getOperator');
Route::any('getPlanData', 'UsersController@getPlanData');
Route::any('checkTopup', 'UsersController@checkTopup');
Route::any('beneficiaryAdd/{user_id}/{receiver_id}', 'UsersController@beneficiaryAdd');
Route::any('auth/add-beneficiary', 'UsersController@add_beneficiary');

Route::any('auth/airtime_giftcard', 'UsersController@airtime_giftcard');
Route::any('getGiftCard', 'UsersController@getGiftCard');
Route::any('product-detail', 'UsersController@getGiftCardDetail');
Route::any('giftCardorder', 'UsersController@giftCardorder');
Route::any('getGiftCardFee', 'UsersController@getGiftCardFee');
Route::any('auth/giftcard-purchased', 'UsersController@giftcardPurchased');
Route::any('getGiftCardRedeem', 'UsersController@getGiftCardRedeem');

//Route::any('auth/fund-transfer/{number}', [
//    'as' => 'auth/fund-transfer', 'uses' => 'UsersController@fundTransfer'
//]);

Route::any('auth/notifications', 'UsersController@notifications');
Route::any('auth/transfer-success/{slug}/{uslug}', 'UsersController@successFundTransfer');
Route::any('auth/success-add-fund/{transID}/{refID}/{amnt}', 'UsersController@successAddFund');
Route::any('auth/my-recipients', 'UsersController@myRecipients');
Route::any('auth/edit-recipient/{slug}', 'UsersController@editRecipient');
Route::any('auth/delete-recipient/{slug}', 'UsersController@deleteRecipient');
Route::any('auth/feedback', 'UsersController@feedback');
Route::any('auth/help', 'UsersController@help');
Route::any('auth/getCurrencyRate', 'UsersController@getCurrencyRate');
Route::any('auth/checkUserExists', 'UsersController@checkUserExists');

Route::any('auth/withdraw', 'UsersController@withdraw');
Route::any('auth/add-bank-account', 'UsersController@addBankAccount');
Route::any('auth/become-bank-agent', 'UsersController@becomeAgent');
Route::any('auth/edit-agent-details', 'UsersController@editAgentDetails');
Route::any('auth/client-deposit', 'UsersController@clientDeposit');
Route::any('auth/getCurrencyRateByAccountNumber', 'UsersController@getCurrencyRateByAccountNumber');
Route::any('auth/sendOTP4AgentTransfer', 'UsersController@sendOTP4AgentTransfer');
Route::any('auth/client-withdraw', 'UsersController@clientWithdraw');
Route::any('auth/transaction-detail/{slug}', 'UsersController@transactionDetail');
Route::any('auth/agent-list', 'UsersController@listAgent');
Route::any('auth/change-pin', 'UsersController@changePin');
Route::any('auth/withdraw-request', 'UsersController@withdrawRequest');
Route::any('auth/saveWithdrawRequest/{slug}', 'UsersController@saveWithdrawRequest');
Route::any('auth/withdraw-paypal', 'UsersController@withdrawPaypal');
Route::any('auth/agent-withdraw-request-list', 'UsersController@agentWithdrawReq');
Route::any('auth/agent-decline-withdraw-request/{slug}', 'UsersController@agentDeclineWithdrawRequest');

Route::any('auth/agents/agent-withdraw-request', 'UsersController@agentWithdrawRequest');
Route::any('auth/comming-soon', 'UsersController@comming_soon');
Route::any('auth/crypto-deposit', 'UsersController@crypto_deposit');

Route::any('auth/crypto-withdraw', 'UsersController@crypto_withdraw');
Route::any('auth/send-crypto-withdraw-otp', 'UsersController@send_crypto_withdraw_otp');
Route::any('auth/crypto-withdraw-otp', 'UsersController@crypto_withdraw_otp');
Route::any('auth/resentOtpCrypto', 'UsersController@resentOtpCrypto');

Route::any('auth/bank-transfer', 'UsersController@bank_transfer');
Route::any('auth/manual-deposit', 'UsersController@manualDeposit');
Route::any('auth/manual-withdraw', 'UsersController@manual_withdraw');
Route::any('auth/resentOtpManualWithdraw', 'UsersController@resentOtpManualWithdraw');

Route::any('auth/delete-withdraw-account/{account_id}', 'UsersController@deleteWithdrawAccount');
Route::any('auth/kyc-detail', 'UsersController@kyc_detail');
Route::any('auth/compliance', 'UsersController@compliance');
Route::any('auth/private-banking', 'UsersController@private_banking');

Route::any('updateStatus', 'CronsController@updateStatus');
Route::get('client-withdraw', 'UsersController@clientwithdrawal');

Route::any('auth/exchange', 'UsersController@exchange');
Route::any('exchange-currency', 'UsersController@exchangeCurrency');
Route::any('getPublicKey', 'UsersController@getPublicKey');

Route::post('/charges', 'HomesController@onLinePaymentcharges');
Route::post('/api-transaction-detail', 'HomesController@apiTransactionDetail');
Route::any('/ref', 'HomesController@refToId');   


Route::post('/epayme_form', 'HomesController@onLinePaymentchargesEpay');
Route::any('/epayme', 'HomesController@epayme');
Route::any('/confirm-epayme-payment', 'HomesController@confirmEpaymePayment');
Route::any('/ozow-login', 'HomesController@ozowLogin');
Route::any('/confirm-ozow-payment', 'HomesController@confirmOzowPayment');
Route::any('/epay-payment-successfull', 'HomesController@epaypaymentsuccessfull');

Route::post('/epayme_merchant_form', 'HomesController@onLinePaymentchargesEpayMerchant');
Route::any('/epayme_merchant', 'HomesController@epaymeMerchant');
Route::any('/confirm-epayme-merchant-payment', 'HomesController@confirmEpaymeMerchantPayment');

Route::post('/merchat-withdrawal', 'HomesController@merchatWithdrawal');
Route::any('/withdraw-payment', 'HomesController@withdrawPayment');
Route::any('/auth/merchant-withdraw-request-list', 'UsersController@merchantWithdrawReq');
Route::any('auth/merchant-decline-withdraw-request/{slug}', 'UsersController@merchantDeclineWithdrawRequest');
Route::any('/auth/merchant-edit-withdraw-request', 'UsersController@merchantEditWithdrawRequest');

Route::any('/resentVerifyOtpAPI', 'HomesController@resentVerifyOtpAPI');

Route::any('auth/resentOtpAgentWithdraw', 'UsersController@resentOtpAgentWithdraw');
Route::any('auth/referral-detail', 'UsersController@referralDetail');

Route::any('auth/agent-withdraw', 'UsersController@agent_withdraw');
Route::any('auth/beneficiary-list', 'UsersController@beneficiary_list');
Route::any('auth/deleteBeneficiary/{slug}', 'UsersController@deleteBeneficiary');
Route::any('auth/get-dba-conversion-usd', 'UsersController@getDbaConversionUsd');
Route::any('auth/card-payment', 'UsersController@cardPayment');
Route::post('stripe', 'UsersController@stripePost');
Route::any('auth/card-success-payment', 'UsersController@cardSuccessPayment');
Route::any('auth/card-cancel-payment/{slug}', 'UsersController@cardCancelPayment');
Route::any('get_stripe_response', 'UsersController@get_stripe_response');
Route::any('checkAccountLink', 'UsersController@checkAccountLink');
Route::any('auth/dafri-me', 'UsersController@dafriMe');
Route::any('auth/generate-payment-link', 'UsersController@generatePaymentLink');
Route::any('payment/{slug}', 'UsersController@payment');
Route::any('afetrPaymentComplete', 'UsersController@afetrPaymentComplete');

Route::any('auth/merchants-dafri-me', 'UsersController@merchantsdafriMe');
Route::any('auth/generate-merchant-payment-link', 'UsersController@generatemerchantPaymentLink');
Route::any('merchant-payment/{slug}', 'UsersController@merchantPayment');


//for global pay
Route::any('auth/global-withdraw', 'UsersController@global_withdraw');
Route::any('auth/global-withdraw-otp', 'UsersController@global_withdraw_otp');

//for global pay
Route::any('auth/party-withdraw', 'UsersController@party_withdraw');
Route::any('auth/party-withdraw-otp', 'UsersController@party_withdraw_otp');

Route::any('/logout', 'UsersController@logout');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
  Route::any('/', 'AdminsController@login');

  Route::any('/users/crypto_debit', 'UsersController@cryptoDebit');
  Route::any('/users/total-fee-collection', 'UsersController@total_fee_collection');
  Route::any('/users/crypto_debit_add', 'UsersController@cryptoDebitAdd');
  Route::any('/users/crypto_currency_delete/{slug}', 'UsersController@crypto_currency_delete');

  Route::any('/users/crypto_withdraw_currency', 'UsersController@cryptoWithdrawCurrency');
  Route::any('/users/crypto_withdraw_add', 'UsersController@crypto_withdraw_add');
  Route::any('/users/crypto_withdraw_currency_delete/{slug}', 'UsersController@crypto_withdraw_currency_delete');

  Route::any('login', 'AdminsController@login');
  Route::any('admins/login', 'AdminsController@login');
  Route::get('admins/logout', 'AdminsController@logout');
  Route::get('admins/dashboard', 'AdminsController@dashboard');
  Route::get('admins/dashboard-copy', 'AdminsController@dashboardCopy');
  Route::any('admins/change-username', 'AdminsController@changeUsername');
  Route::any('admins/change-password', 'AdminsController@changePassword');
  Route::any('admins/change-email', 'AdminsController@changeEmail');
  Route::any('admins/change-commission', 'AdminsController@changeCommission');
  Route::any('admins/forgot-password', 'AdminsController@forgotPassword');
  Route::any('admins/change-service', 'AdminsController@changeService');
  Route::any('admins/wallet-balance', 'AdminsController@walletBalance');
  Route::any('admins/roles', 'AdminsController@listRole');
  Route::any('admins/add-role', 'AdminsController@addRole');
  Route::any('admins/edit-role/{slug}', 'AdminsController@editRole');
  Route::any('admins/list-subadmin', 'AdminsController@listSubadmin');
  Route::any('admins/add-admin', 'AdminsController@addAdmin');
  Route::any('admins/edit-admin/{slug}', 'AdminsController@editAdmin');
  Route::any('/reports/exportCSV/{keyword}/{date}/{todate}/{currency}', 'UsersController@exportCSV');


  /*****Sub Admin Routing*****/
  Route::any('/subadmins', 'SubadminsController@index');
  Route::any('/subadmins/add', 'SubadminsController@add');
  Route::any('/subadmins/edit/{slug}', 'SubadminsController@edit');
  Route::get('/subadmins/activate/{slug}', 'SubadminsController@activate');
  Route::get('/subadmins/deactivate/{slug}', 'SubadminsController@deactivate');
  Route::get('/subadmins/delete/{slug}', 'SubadminsController@delete');

  /*****Individuals Routing*****/
  Route::any('/users', 'UsersController@index');
  Route::any('/users/add', 'UsersController@add');
  Route::any('/users/edit/{slug}', 'UsersController@edit');
  Route::get('/users/activate/{slug}', 'UsersController@activate');
  Route::get('/users/deactivate/{slug}', 'UsersController@deactivate');
  Route::get('/users/delete/{slug}', 'UsersController@delete');
  Route::get('/users/deleteimage/{slug}', 'UsersController@deleteimage');
  Route::get('/users/deleteidentity/{slug}', 'UsersController@deleteidentity');
  Route::get('/users/kycdetail/{slug}', 'UsersController@kycdetail');
  Route::get('/users/approvekyc/{slug}', 'UsersController@approvekyc');
  Route::get('/users/declinekyc/{slug}', 'UsersController@declinekyc');
  Route::any('/users/bank-agent-request', 'UsersController@agentRequest');
  Route::any('/users/disapproveAgent/{slug}', 'UsersController@disapproveAgent');
  Route::any('/users/approveAgent/{slug}', 'UsersController@approveAgent');
  Route::any('/reports/transaction-report', 'UsersController@transactionReport');
  Route::any('/reports/dba-transaction-report', 'UsersController@dbatransactionReport');
  Route::any('/reports/usdt-transaction-report', 'UsersController@usdttransactionReport');
  Route::any('/reports/exportCSV/{keyword}/{from_date}/{to_date}/{currency}', 'UsersController@exportCSV');
  Route::any('/reports/dbaexportCSV/{keyword}/{from_date}/{to_date}/{currency}', 'UsersController@dbaexportCSV');
  Route::any('users/get-in-touch-request', 'UsersController@getintouch');
  Route::any('users/delete_get_help/{slug}', 'UsersController@getintouchdelete');
  Route::any('users/approve_get_help/{slug}', 'UsersController@approvegethelp');
  Route::any('users/dba_setting', 'UsersController@dba_setting');

  Route::any('users/manage-limit/{slug}', 'UsersController@manageLimit');


  Route::any('/fees/list-fees', 'UsersController@listFees');
  Route::any('/fees/list-merchant-fees', 'UsersController@listMerchantFees');
  Route::any('/fees/editFees/{slug}', 'UsersController@editFees');
  Route::any('/users/admin-agent-request/{slug}', 'UsersController@adminAgentRequest');
  Route::any('/merchants/agentRequest/{slug}', 'MerchantsController@agentRequest');
  Route::any('/users/support-request', 'UsersController@supportRequest');
  Route::any('/users/paypal-request', 'UsersController@paypalRequest');
  Route::any('/users/change-pp-req-status/{id}/{status}', 'UsersController@changePaypalReqStatus');
  Route::any('/users/transactions-limit', 'UsersController@transLimit');
  Route::any('/users/edit-transaction-limit/{slug}', 'UsersController@editTransLimit');
  Route::any('/users/agent-limit', 'UsersController@agentLimit');
  Route::any('/users/set-agent-trans-limit/{user_id}', 'UsersController@setAgentTransLimit');
  Route::any('/users/editagent/{user_id}', 'UsersController@editAgentDetails');
  Route::any('/users/crypto-deposit-request', 'UsersController@cryptoDepositRequest');
  Route::any('/users/dba-deposit-request', 'UsersController@dbaDepositRequest');
  Route::any('/users/dba-deposit-by-card', 'UsersController@dbaDepositCardRequest');
  Route::any('/users/change-crypto-deposit-req-status/{id}/{status}', 'UsersController@updateCryptoDepositReqStatus');
  Route::any('/users/change-dba-deposit-req-status/{id}/{status}', 'UsersController@updateDbaDepositReqStatus');
  Route::any('/users/change-dba-deposit-card-req-status/{id}/{status}', 'UsersController@updateDbaDepositCardReqStatus');
  Route::any('/users/edit-crypto-request/{id}', 'UsersController@editCryptoReq');
  Route::any('/users/edit-dba-deposit-request/{id}', 'UsersController@editDbaDepositReq');
  Route::any('/users/edit-dba-deposit-card-request/{id}', 'UsersController@editDbaDepositCardReq');
  Route::any('/users/change-manual-deposit-req-status/{id}/{status}', 'UsersController@updateManualDepositReqStatus');
  Route::any('/users/edit-manual-request/{id}', 'UsersController@editManualReq');
  
  Route::any('/users/conversion', 'UsersController@ngnconversion');

  Route::any('/users/manual-deposit-request', 'UsersController@manualDepositRequest');
  Route::any('/users/repeat-manual-request/{id}', 'UsersController@repeatManualReq');
  Route::any('/users/adjust-client-wallet/{id}', 'UsersController@adjustBalance');
  Route::any('/users/adjust-client-fix-wallet/{id}', 'UsersController@adjustBalanceFix');
  Route::any('/users/adjust-dba-wallet/{id}', 'UsersController@adjustDbaBalance');
  Route::any('/users/adjust-usdt-wallet/{id}', 'UsersController@adjustUsdtBalance');
  Route::any('/users/adjust-client-dba-fix-wallet/{id}', 'UsersController@adjustDbaBalanceFix');
  Route::any('/users/adjust-client-usdt-fix-wallet/{id}', 'UsersController@adjustUsdtBalanceFix');
  Route::any('/users/transaction-list/{slug}', 'UsersController@transactionLists');
  Route::any('/users/dba-transaction-list/{slug}', 'UsersController@dbatransactionLists');
  Route::any('/users/usdt-transaction-list/{slug}', 'UsersController@usdttransactionLists');

  Route::any('/users/crypto-withdraw-request', 'UsersController@cryptoWithdrawRequest');
  Route::any('/users/dba-withdraw-request', 'UsersController@dbaWithdrawRequest');
  Route::any('/users/usdt-withdraw-request', 'UsersController@usdtWithdrawRequest');
  Route::any('/users/edit-crypto-withdraw-request/{id}', 'UsersController@editCryptoWithdrawReq');
  Route::any('/users/change-crypto-withdraw-req-status/{id}/{status}', 'UsersController@updateCryptoWithdrawReqStatus');
  Route::any('/users/change-dba-withdraw-req-status/{id}/{status}', 'UsersController@updateDbaWithdrawReqStatus');
  Route::any('/users/change-usdt-withdraw-req-status/{id}/{status}', 'UsersController@updateUsdtWithdrawReqStatus');
  Route::any('/users/help-request', 'UsersController@helpRequest');

  Route::any('/users/manual-withdraw-request', 'UsersController@manualWithdrawRequest');
  Route::any('/users/change-manual-withdraw-req-status/{id}/{status}', 'UsersController@changeManualReqStatus');

  Route::any('/users/global-withdraw-request', 'UsersController@globalWithdrawRequest');
  Route::any('/users/change-global-withdraw-req-status/{id}/{status}', 'UsersController@changeGlobalReqStatus');

  /*****Agents Routing*****/
  Route::any('/agents', 'AgentsController@index');
  Route::any('/agents/add', 'AgentsController@add');
  Route::any('/agents/edit/{slug}', 'AgentsController@edit');
  Route::get('/agents/activate/{slug}', 'AgentsController@activate');
  Route::get('/agents/deactivate/{slug}', 'AgentsController@deactivate');
  Route::get('/agents/delete/{slug}', 'AgentsController@delete');
  Route::get('/agents/deleteimage/{slug}', 'AgentsController@deleteimage');
  Route::get('/agents/deleteidentity/{slug}', 'AgentsController@deleteidentity');
  Route::get('/agents/kycdetail/{slug}', 'AgentsController@kycdetail');
  Route::get('/agents/approvekyc/{slug}', 'AgentsController@approvekyc');
  Route::get('/agents/declinekyc/{slug}', 'AgentsController@declinekyc');

  /*****Merchant Routing*****/
  Route::any('/merchants', 'MerchantsController@index');
  Route::any('/merchants/add', 'MerchantsController@add');
  Route::any('/merchants/edit/{slug}', 'MerchantsController@edit');
  Route::get('/merchants/activate/{slug}', 'MerchantsController@activate');
  Route::get('/merchants/deactivate/{slug}', 'MerchantsController@deactivate');
  Route::get('/merchants/delete/{slug}', 'MerchantsController@delete');
  Route::get('/merchants/deleteimage/{slug}', 'MerchantsController@deleteimage');
  Route::get('/merchants/deleteidentity/{slug}', 'MerchantsController@deleteidentity');
  Route::get('/merchants/kycdetail/{slug}', 'MerchantsController@kycdetail');
  Route::get('/merchants/approvekyc/{slug}', 'MerchantsController@approvekyc');
  Route::get('/merchants/declinekyc/{slug}', 'MerchantsController@declinekyc');
  Route::get('/merchants/api-activate/{slug}', 'MerchantsController@apiActivate');
  Route::get('/merchants/api-deactivate/{slug}', 'MerchantsController@apiDeactivate');

  /*****Scratch Card Routing*****/
  Route::any('/scratchcards', 'ScratchcardsController@index');
  Route::any('/scratchcards/add', 'ScratchcardsController@add');
  Route::any('/scratchcards/edit/{slug}', 'ScratchcardsController@edit');
  Route::get('/scratchcards/activate/{slug}', 'ScratchcardsController@activate');
  Route::get('/scratchcards/deactivate/{slug}', 'ScratchcardsController@deactivate');
  Route::get('/scratchcards/delete/{slug}', 'ScratchcardsController@delete');

  /*****Card Routing*****/
  Route::any('/cards', 'CardsController@index');
  Route::any('/cards/add', 'CardsController@add');
  Route::any('/cards/edit/{slug}', 'CardsController@edit');
  Route::get('/cards/activate/{slug}', 'CardsController@activate');
  Route::get('/cards/deactivate/{slug}', 'CardsController@deactivate');
  Route::get('/cards/delete/{slug}', 'CardsController@delete');

  Route::any('/cards/carddetail/{cslug}', 'CardsController@carddetail');
  Route::any('/cards/addcarddetail/{cslug}', 'CardsController@addcarddetail');
  Route::any('/cards/editcarddetail/{cslug}/{slug}', 'CardsController@editcarddetail');
  Route::get('/cards/activatecarddetail/{cslug}/{slug}', 'CardsController@activatecarddetail');
  Route::get('/cards/deactivatecarddetail/{cslug}/{slug}', 'CardsController@deactivatecarddetail');
  Route::get('/cards/deletecarddetail/{cslug}/{slug}', 'CardsController@deletecarddetail');

  /*****Card Routing*****/
  Route::any('/banners', 'BannersController@index');
  Route::any('/banners/add', 'BannersController@add');
  Route::any('/banners/edit/{slug}', 'BannersController@edit');
  Route::get('/banners/activate/{slug}', 'BannersController@activate');
  Route::get('/banners/deactivate/{slug}', 'BannersController@deactivate');
  Route::get('/banners/delete/{slug}', 'BannersController@delete');

  Route::any('/transactions', 'TransactionsController@index');
  Route::get('/transactions/delete/{slug}', 'TransactionsController@delete');

  Route::any('/pages', 'PagesController@index');
  Route::any('/pages/edit/{slug}', 'PagesController@edit');
  Route::any('/pages/pageimages', 'PagesController@pageimages');

  Route::any('/blogs', 'PagesController@blogs');
  Route::any('/blogs/add', 'PagesController@addBlog');
  Route::any('/blogs/edit/{slug}', 'PagesController@editBlog');
  Route::any('/blogs/deleteBlog/{slug}', 'PagesController@deleteBlog');

  Route::any('/pages/faq-list', 'PagesController@faqList');
  Route::any('/pages/addFaq', 'PagesController@addFaq');
  Route::any('/pages/editFaq/{slug}', 'PagesController@editFaq');
  Route::any('/pages/deleteFaq/{slug}', 'PagesController@deleteFaq');
  Route::any('/users/wallet_summary/{slug}', 'UsersController@wallet_summary');
  Route::any('/users/ref-detail/{slug}', 'UsersController@referralDetail');
  Route::any('/users/user_detail/{slug}', 'UsersController@userDetail');


  // By sushil 28 April 2022
  Route::any('/users/manual-deposit-requestlist/{slug}', 'UsersController@manualDepositRequestlist');
  Route::any('/users/manual-withdraw-requestlist/{slug}', 'UsersController@manualWithdrawRequestlist');
  Route::any('/users/crypto-deposit-requestlist/{slug}', 'UsersController@cryptoDepositRequestlist');
  Route::any('/users/crypto-withdraw-requestlist/{slug}', 'UsersController@cryptoWithdrawRequestlist');

  Route::any('/users/change-giftcard-req-status/{id}/{status}', 'UsersController@updategiftcardReqStatus');
  Route::any('users/giftairtime_setting', 'UsersController@giftairtime_setting');

  Route::any('users/change-airtime-req-status/{id}/{status}', 'UsersController@airtimeReqUpdate');

  Route::any('users/download_statement/{id}', 'UsersController@downloadStatement');
  Route::any('users/g_a_request/{slug}', 'UsersController@manageGARequest');

  Route::post('admins/dashboard-tab', 'AdminsController@dashboardTab');
  Route::post('admins/fetch-graph', 'AdminsController@fetchProductGraph');
  Route::post('admins/fetch-customer-report', 'AdminsController@fetchCustomerReport');
  Route::any('admins/fetch-country-report', 'AdminsController@fetchCountryReport');
  Route::any('admins/fetch-country-transaction-report', 'AdminsController@fetchCountryTransactionReport');
  Route::any('admins/fetch-country-amount-report', 'AdminsController@fetchCountryAmountReport');
  Route::post('admins/fetch-fee-graph', 'AdminsController@fetchFeeGraph');

});

//Route::get('/', function () {
//    return view('welcome');
//});
