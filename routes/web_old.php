<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', 'HomesController@index');
Route::get('/personal-account', 'HomesController@personalAccount');
Route::get('/business-account', 'HomesController@businessAccount');
Route::any('/dba-application', 'HomesController@dbaApplication');
Route::get('/about', 'HomesController@about');
Route::get('/contact', 'HomesController@contact');
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

Route::get('/success/{slug}', 'HomesController@paymentsuccess');

Route::post('/users/uploadprofileimage', 'UsersController@uploadprofileimage');

Route::get('/templateTest', 'UsersController@templateTest');

Route::get('/faq', 'HomesController@faqs');
Route::get('/debit-cards', 'HomesController@debitCards');
Route::get('/cookie-policy', 'HomesController@cookieNotice');
Route::get('/what-we-offer', 'HomesController@products');
Route::any('/online-payment', 'HomesController@onlinePayment');
Route::any('/setOnlinePayment/{base64Req}', 'HomesController@setOnlinePayment');
Route::any('/confirm-online-payment', 'HomesController@confirmOnlinePayment');

Route::get('/test', 'UsersController@test');
Route::get('/choose-account', 'UsersController@chooseAccount');
Route::any('/personal-account-registration', 'UsersController@personalRegister');
Route::any('/personal-account-verification/{slug}', 'UsersController@personalVerify');
Route::any('/personal-kyc-verification/{slug}', 'UsersController@personalKycVerify');
Route::any('/personal-kyc-update/{slug}', 'UsersController@personalKycUpdate');
Route::any('/personal-login', 'UsersController@personalLogin');

Route::any('/account-verification/{slug}', 'UsersController@otpVerify');

Route::any('/business-account-registration', 'UsersController@businessRegister');
Route::any('/business-account-verification/{slug}', 'UsersController@businessVerify');
Route::any('/business-kyc-verification/{slug}', 'UsersController@businessKycVerify');
Route::any('/business-kyc-update/{slug}', 'UsersController@businessKycUpdate');
Route::any('/business-login', 'UsersController@businessLogin');
Route::any('/forgot-password', 'UsersController@forgotPassword');
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
Route::any('auth/fund-transfer', 'UsersController@fundTransfer');
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

Route::post('/charges', 'HomesController@onLinePaymentcharges');
Route::post('/api-transaction-detail', 'HomesController@apiTransactionDetail');
Route::post('/merchat-withdrawal', 'HomesController@merchatWithdrawal');
Route::any('/withdraw-payment', 'HomesController@withdrawPayment');
Route::any('/auth/merchant-withdraw-request-list', 'UsersController@merchantWithdrawReq');
Route::any('auth/merchant-decline-withdraw-request/{slug}', 'UsersController@merchantDeclineWithdrawRequest');
Route::any('/auth/merchant-edit-withdraw-request', 'UsersController@merchantEditWithdrawRequest');

Route::any('/resentVerifyOtpAPI', 'HomesController@resentVerifyOtpAPI');

Route::get('/deposit-api', 'PagesController@depositAPI');
Route::get('/withdraw-api', 'PagesController@withdrawAPI');


Route::any('/logout', 'UsersController@logout');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
  Route::any('/', 'AdminsController@login');
  Route::any('login', 'AdminsController@login');
  Route::any('admins/login', 'AdminsController@login');

  Route::get('admins/logout', 'AdminsController@logout');
  Route::get('admins/dashboard', 'AdminsController@dashboard');
  Route::any('admins/change-username', 'AdminsController@changeUsername');
  Route::any('admins/change-password', 'AdminsController@changePassword');
  Route::any('admins/change-email', 'AdminsController@changeEmail');
  Route::any('admins/change-commission', 'AdminsController@changeCommission');
  Route::any('admins/forgot-password', 'AdminsController@forgotPassword');
  Route::any('admins/change-service', 'AdminsController@changeService');
  Route::any('admins/roles', 'AdminsController@listRole');
  Route::any('admins/add-role', 'AdminsController@addRole');
  Route::any('admins/edit-role/{slug}', 'AdminsController@editRole');
  Route::any('admins/list-subadmin', 'AdminsController@listSubadmin');
  Route::any('admins/add-admin', 'AdminsController@addAdmin');
  Route::any('admins/edit-admin/{slug}', 'AdminsController@editAdmin');


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
  Route::any('/reports/exportCSV/{keyword}/{date}/{currency}', 'UsersController@exportCSV');

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
  Route::any('/users/change-crypto-deposit-req-status/{id}/{status}', 'UsersController@updateCryptoDepositReqStatus');
  Route::any('/users/edit-crypto-request/{id}', 'UsersController@editCryptoReq');
  Route::any('/users/change-manual-deposit-req-status/{id}/{status}', 'UsersController@updateManualDepositReqStatus');
  Route::any('/users/edit-manual-request/{id}', 'UsersController@editManualReq');

  Route::any('/users/manual-deposit-request', 'UsersController@manualDepositRequest');
  Route::any('/users/repeat-manual-request/{id}', 'UsersController@repeatManualReq');
  Route::any('/users/adjust-client-wallet/{id}', 'UsersController@adjustBalance');
  Route::any('/users/transaction-list/{slug}', 'UsersController@transactionLists');

  Route::any('/users/crypto-withdraw-request', 'UsersController@cryptoWithdrawRequest');
  Route::any('/users/edit-crypto-withdraw-request/{id}', 'UsersController@editCryptoWithdrawReq');
  Route::any('/users/change-crypto-withdraw-req-status/{id}/{status}', 'UsersController@updateCryptoWithdrawReqStatus');
  Route::any('/users/help-request', 'UsersController@helpRequest');

  Route::any('/users/manual-withdraw-request', 'UsersController@manualWithdrawRequest');
  Route::any('/users/change-manual-withdraw-req-status/{id}/{status}', 'UsersController@changeManualReqStatus');
  
  Route::any('/users/conversion', 'UsersController@ngnconversion');

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
});

//Route::get('/', function () {
//    return view('welcome');
//});
