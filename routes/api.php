<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */
Route::post('getStateList', 'AuthController@getStateList');
Route::post('getCityList', 'AuthController@getCityList');
Route::post('resendOTP', 'AuthController@resendOTP');
Route::post('getIdType', 'AuthController@getIdType');
Route::post('kycUpdate', 'AuthController@kycUpdate');
Route::post('updateKycTwo', 'AuthController@updateKycTwo');

Route::group([
    'prefix' => 'auth'
        ], function () {
    Route::post('isMobileExists', 'AuthController@isMobileExists');
    Route::post('getCountryList', 'AuthController@getCountryList');

    Route::post('signup', 'AuthController@signup');
    Route::post('verifyOTP', 'AuthController@verifyOTP');
    Route::post('login', 'AuthController@login');
    Route::post('forgotPassword', 'AuthController@forgotPassword');
    Route::post('resetPassword', 'AuthController@resetPassword');
    Route::post('changePassword', 'AuthController@changePassword');
    Route::post('updateProfile', 'AuthController@updateProfile');
    
    

    Route::group([
        'middleware' => 'auth:api'
            ], function() {
        Route::post('addCard', 'AuthController@addCard');
        Route::post('getCard', 'AuthController@getCard');
        Route::post('removeCard', 'AuthController@removeCard');
        Route::post('addAccount', 'AuthController@addAccount');
        Route::post('getAccount', 'AuthController@getAccount');
        Route::post('removeAccount', 'AuthController@removeAccount');
        Route::post('getUserByMobile', 'AuthController@getUserByMobile');
        Route::post('depositByCard', 'AuthController@depositByCard');
        Route::post('depositByAgent', 'AuthController@depositByAgent');
        Route::post('withdrawByAgent', 'AuthController@withdrawByAgent');
        Route::post('selectMobileCard', 'AuthController@selectMobileCard');
        Route::post('buyMobileCard', 'AuthController@buyMobileCard');
        Route::post('mobileCardList', 'AuthController@mobileCardList');
        Route::post('selectOnlineCard', 'AuthController@selectOnlineCard');
        Route::post('buyOnlineCard', 'AuthController@buyOnlineCard');
        Route::post('onlineCardList', 'AuthController@onlineCardList');
        Route::post('selectInternetCard', 'AuthController@selectInternetCard');
        Route::post('buyInternetCard', 'AuthController@buyInternetCard');
        Route::post('internetCardList', 'AuthController@internetCardList');
        Route::post('nearByUser', 'AuthController@nearByUser');
//        Route::post('updateProfile', 'AuthController@updateProfile');
        Route::post('userHome', 'AuthController@user_home');
        Route::post('fundTransfer', 'AuthController@fundTransfer');
        Route::post('myTransactions', 'AuthController@myTransactions');
        Route::post('getEFTPayment', 'AuthController@getEFTPayment');
        Route::post('getWalletBalance', 'AuthController@getWalletBalance');
        Route::post('getUserByQR', 'AuthController@getUserByQR');
        Route::post('getDebitTrans', 'AuthController@getDebitTrans');
        Route::post('addPay', 'AuthController@addPay');


        /* Route::get('logout', 'AuthController@logout');
          Route::get('user', 'AuthController@user'); */
    });
});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
