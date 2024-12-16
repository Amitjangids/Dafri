<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Models\Banner;
use App\Models\Card;
use App\Models\Carddetail;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Scratchcard;
use DB;
use Input;

class AuthController extends Controller {

    private function generateNumericOTP($n) {
        $generator = "1357902468";
        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }
        return $result;
    }

    private function generateQRCode($qrString, $user_id) {
        $output_file = 'uploads/qr-code/' . $user_id . '-qrcode-' . time() . '.png';
        $image = \QrCode::format('png')
                ->size(200)->errorCorrection('H')
                ->generate($qrString, base_path() . '/public/' . $output_file);
        return $output_file;
    }

    public function signup(Request $request) {

        if ($request->user_id == "") {
            if ($request->phone == "" or (strlen($request->phone) < 5 or strlen($request->phone) > 15)) {
                $statusArr = array("status" => "Failed", "reason" => "Invalid Phone number");
                return response()->json($statusArr, 200);
            }

            $matchThese = ["users.phone" => $request->phone];
            $flag = DB::table('users')->where($matchThese)->first();

            $otp_number = $this->generateNumericOTP(6);
            //            $res = $this->sendSMS($otp_number,$request->phone);
            if ($flag) {
                if ($flag->name != '') {
                    $statusArr = array("status" => "Failed", "reason" => "User already registered");
                    return response()->json($statusArr, 200);
                } else {

                    $user_id = $flag->id;

                    $statusArr = array("status" => "Success", "reason" => "OTP send, please enter OTP for verification.", "otp" => $otp_number, "user_id" => $user_id);
                    return response()->json($statusArr, 200);
                }
            } else {
                $slug = $this->createSlug(time(), 'users');
                $user = new User([
                    'user_type' => $request->user_type,
                    'phone' => $request->phone,
                    'verify_code' => $otp_number,
                    'slug' => $slug,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $user->save();
                $user_id = $user->id;

                $statusArr = array("status" => "Success", "reason" => "OTP send, please enter OTP for verification.", "otp" => $otp_number, "user_id" => $user_id);
                return response()->json($statusArr, 200);
            }
        } else {
            $user_id = $request->user_id;
            $userInfo = User::where('id', $user_id)->first();
            if (!empty($userInfo)) {

                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] != '0') {
                    $file = $_FILES['profile_image'];
                    $file = Input::file('profile_image');
                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                    $this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                    $profile_image = $uploadedFileName;
                } else {
                    $profile_image = '';
                }

                if (isset($_FILES['identity_image']) && $_FILES['identity_image']['size'] != '0') {
                    $file = $_FILES['identity_image'];
                    $file = Input::file('identity_image');
                    $uploadedFileName = $this->uploadImage($file, IDENTITY_FULL_UPLOAD_PATH);
                    $this->resizeImage($uploadedFileName, IDENTITY_FULL_UPLOAD_PATH, IDENTITY_SMALL_UPLOAD_PATH, IDENTITY_MW, IDENTITY_MH);
                    $identity_image = $uploadedFileName;
                } else {
                    $identity_image = '';
                }
                $qrString = $user_id . "##" . $request->name;
                $qrCode = $this->generateQRCode($qrString, $user_id);

                $user = array(
                    'user_type' => $request->user_type,
                    'name' => $request->name,
                    'email' => $request->email,
                    'city' => $request->city,
                    'area' => $request->area,
                    'qr_code' => $qrCode,
                    'business_name' => $request->business_name,
                    'registration_number' => $request->registration_number,
                    'identity_image' => $identity_image,
                    'profile_image' => $profile_image,
                    'national_identity_number' => $request->national_identity_number,
                    'dob' => date('Y-m-d', strtotime($request->dob)),
                    'password' => $this->encpassword($request->password),
                    'is_verify' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                );

                User::where('id', $user_id)->update($user);

                $statusArr = array("status" => "Success", "reason" => "Register Success.", "user_id" => $user_id);
                return response()->json($statusArr, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "User not found");
                return response()->json($statusArr, 200);
            }
        }
    }

    public function login(Request $request) {


        $device_token = $request->device_token;
        $device_type = $request->device_type;
        $user_type = $request->user_type;

        $userInfo = User::where('phone', $request->phone)->first();
        if (!empty($userInfo)) {
        if ($user_type == $userInfo->user_type) {
            if ($userInfo->is_verify == 0) {
                $statusArr = array("status" => "Failed", "reason" => "Your account might have been temporarily disabled. Please contact us for more details.");
                return response()->json($statusArr, 200);
            } else {
                $password = $request->password;
                if (password_verify($password, $userInfo->password)) {

                    $user = array(
                        'device_token' => $device_token,
                        'device_type' => $device_type
                    );
                    User::where('phone', $request->phone)->update($user);

                    $userInfo = User::where('phone', $request->phone)->first();

                    $userData = array();
                    $userData['name'] = $userInfo->name;
                    $userData['user_id'] = $userInfo->id;
                    $userData['amount'] = $this->numberFormatPrecision($userInfo->wallet_balance, 2, '.');
                    if ($userInfo->profile_image != '') {
                        $userData['profile_image'] = PROFILE_FULL_DISPLAY_PATH . $userInfo->profile_image;
                    } else {
                        $userData['profile_image'] = HTTP_PATH . '/public/img/' . 'no_user.png';
                    }


                    $userData['user_type'] = $userInfo->user_type;
                    $userData['phone'] = $userInfo->phone;
                    $userData['email'] = $userInfo->email;
                    $userData['city'] = $userInfo->city;
                    $userData['area'] = $userInfo->area ? $userInfo->area : '';
                    $userData['dob'] = date('d/m/Y', strtotime($userInfo->dob));
                    $userData['national_identity_number'] = $userInfo->national_identity_number ? $userInfo->national_identity_number : '';
                    $userData['business_name'] = $userInfo->business_name ? $userInfo->business_name : '';
                    $userData['registration_number'] = $userInfo->registration_number ? $userInfo->registration_number : '';

                    $userData['qrcode'] = HTTP_PATH . "/public/" . $userInfo->qr_code;

                    $credentials = request(['phone', 'password']);
                    if (!Auth::attempt($credentials)) {
                        return response()->json(['message' => 'Unauthorized'], 401);
                    }
                    $user = $request->user();

                    $tokenStr = $userInfo->id . " " . $userInfo->name . " " . time();
                    $tokenResult = $user->createToken($tokenStr);
                    //$tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;
                    $token->save();

                    $statusArr = array("status" => "Success", 'is_kyc_done' => $userInfo->is_kyc_done, "access_token" => $tokenResult->accessToken, "token_type" => "Bearer", "reason" => "Login Successfully.");
                    $data['data'] = $userData;

                    $json = array_merge($statusArr, $data);

                    return response()->json($json, 200);
                } else {
                    $statusArr = array("status" => "Failed", "reason" => "You have entered wrong mobile number or password.");
                    return response()->json($statusArr, 200);
                }
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Number already registered as ".$userInfo->user_type);
            return response()->json($statusArr, 200);
        }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "You have entered wrong mobile number or password.");
            return response()->json($statusArr, 200);
        }
    }

    public function forgotPassword(Request $request) {
        $userInfo = User::where('phone', $request->phone)->first();

        if (!empty($userInfo)) {
            $user_id = $userInfo->id;
            $otp_number = $this->generateNumericOTP(6);
            User::where('id', $userInfo->id)->update(array('forget_password_status' => 1));

            $statusArr = array("status" => "Success", "reason" => "OTP send, please enter OTP for verification.", "otp" => $otp_number, "user_id" => $user_id);
            return response()->json($statusArr, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "You entered wrong phone number.");
            return response()->json($statusArr, 200);
        }
    }

    public function resetPassword(Request $request) {
        $userInfo = User::where('phone', $request->phone)->first();

        if (!empty($userInfo)) {
            $user_id = $userInfo->id;
            $password = $this->encpassword($request->password);
            User::where('phone', $userInfo->phone)->update(array('password' => $password));

            $statusArr = array("status" => "Success", "reason" => "Password updated successfully.", "user_id" => $user_id);
            return response()->json($statusArr, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "You entered wrong phone number.");
            return response()->json($statusArr, 200);
        }
    }

    public function changePassword(Request $request) {
        $userInfo = User::where('id', $request->user_id)->first();
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $is_valid = $request->is_valid;
        if (!empty($userInfo)) {
            if ($is_valid == 1) {
                if (!password_verify($old_password, $userInfo->password)) {
                    $statusArr = array("status" => "Failed", "reason" => "Current password is not correct.");
                    return response()->json($statusArr, 200);
                } else if ($old_password == $new_password) {
                    $statusArr = array("status" => "Failed", "reason" => "You can not change new password same as current password.");
                    return response()->json($statusArr, 200);
                } else {
                    $user_id = $userInfo->id;
                    $password = $this->encpassword($request->new_password);
                    User::where('id', $userInfo->id)->update(array('password' => $password));

                    $statusArr = array("status" => "Success", "reason" => "Password updated successfully.", "user_id" => $user_id);
                    return response()->json($statusArr, 200);
                }
            } else {
                if (!password_verify($old_password, $userInfo->password)) {
                    $statusArr = array("status" => "Failed", "reason" => "Current password is not correct.");
                    return response()->json($statusArr, 200);
                } else if ($old_password == $new_password) {
                    $statusArr = array("status" => "Failed", "reason" => "You can not change new password same as current password.");
                    return response()->json($statusArr, 200);
                } else {
                    $otp_number = $this->generateNumericOTP(6);
                    $user_id = $userInfo->id;

                    $statusArr = array("status" => "Success", "reason" => "OTP send, please enter OTP for verification.", "otp" => $otp_number, "user_id" => $user_id);
                    return response()->json($statusArr, 200);
                }
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "You entered wrong phone number.");
            return response()->json($statusArr, 200);
        }
    }

    public function verifyOTP(Request $request) {
        if ($request->user_id == "") {
            $statusArr = array("status" => "Failed", "reason" => "Invalid user id");
            return response()->json($statusArr, 200);
        }
        if ($request->otp_code == "") {
            $statusArr = array("status" => "Failed", "reason" => "Invalid OTP code");
            return response()->json($statusArr, 200);
        }

        $matchThese = ["users.id" => $request->user_id];
        $flag = DB::table('users')->select('users.*')->where($matchThese)->first();
        //echo '<pre>';print_r($flag->verify_code);exit;
        if (empty($flag)) {
            $statusArr = array("status" => "Failed", "reason" => "User not exists!");
            return response()->json($statusArr, 200);
        } elseif ($flag->verify_code != $request->otp_code) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid OTP code");
            return response()->json($statusArr, 200);
        } else {
            $user_id = $request->user_id;
            User::where('id', $user_id)->update([
                'otp_verify' => 1
            ]);
            $statusArr = array("status" => "Success", "reason" => "OTP verification completed, please complete registration process.", "user_id" => $user_id);
            return response()->json($statusArr, 200);
        }
    }

    public function sendSMS($otp_number, $mobile) {
        try {
            $otp_code = $otp_number;
            $toNumber = '+964' . $mobile;

            $message = 'Dear User, Your One Time Password for SatPay App is ' . $otp_code . '. Use this OTP to confirm your identity. Only valid for 3 min.';
            $account_sid = Account_SID;
            $auth_token = Auth_Token;
            $id = "$account_sid";
            $token = "$auth_token";
            global $sms_from;
            $url = "https://api.twilio.com/2010-04-01/Accounts/" . $account_sid . "/Messages.json";
            $data = array(
                'From' => $sms_from,
                'To' => $toNumber,
                'Body' => $message,
            );
            $post = http_build_query($data);
            $x = curl_init($url);
            curl_setopt($x, CURLOPT_POST, true);
            curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($x, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
            curl_setopt($x, CURLOPT_POSTFIELDS, $post);
            $result = curl_exec($x);
            curl_close($x);

            $sentOtp = 1;
            if (isset($data['status'])) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            $statusArr = array("status" => "Failed", "reason" => "Exception! Invalid Phone number");
            return response()->json($statusArr, 501);
        }
    }

    public function resendOTP(Request $request) {
        $request->validate(['phone' => 'required']);
        $otp_number = $this->generateNumericOTP(6);
//        $this->sendSMS($otp_number, $request->phone);
        $statusArr = array("status" => "Success", "reason" => "OTP sent successfully.", "otp" => $otp_number);
        return response()->json($statusArr, 200);
    }

    public function updateProfile(Request $request) {
        ini_set("precision", 14);
        ini_set("serialize_precision", -1);

        if ($request->user_id == "" or!is_numeric($request->user_id)) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid User id");
            return response()->json($statusArr, 200);
        } else {
            try {
                $userInfo = User::where('id', $request->user_id)->first();
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] != '0') {
                    $file = $_FILES['profile_image'];
                    $file = Input::file('profile_image');
                    $uploadedFileName = $this->uploadImage($file, PROFILE_FULL_UPLOAD_PATH);
                    $this->resizeImage($uploadedFileName, PROFILE_FULL_UPLOAD_PATH, PROFILE_SMALL_UPLOAD_PATH, PROFILE_MW, PROFILE_MH);
                    $data['profile_image'] = $uploadedFileName;
                    @unlink(PROFILE_FULL_UPLOAD_PATH . $userInfo->profile_image);
                }

                if (isset($_FILES['identity_image']) && $_FILES['identity_image']['size'] != '0') {
                    $file = $_FILES['identity_image'];
                    $file = Input::file('identity_image');
                    $uploadedFileName = $this->uploadImage($file, IDENTITY_FULL_UPLOAD_PATH);
                    $this->resizeImage($uploadedFileName, IDENTITY_FULL_UPLOAD_PATH, IDENTITY_SMALL_UPLOAD_PATH, IDENTITY_MW, IDENTITY_MH);
                    $data['identity_image'] = $uploadedFileName;
                    @unlink(IDENTITY_FULL_UPLOAD_PATH . $userInfo->identity_image);
                }

                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['city'] = $request->city;
                $data['area'] = $request->area;
                $data['business_name'] = $request->business_name;
                $data['registration_number'] = $request->registration_number;
                $data['national_identity_number'] = $request->national_identity_number;
                if ($request->national_identity_number != $userInfo->national_identity_number) {
                    $data['is_kyc_done'] = 0;
                }
                $data['dob'] = date('Y-m-d', strtotime($request->dob));

                $serialisedData = $this->serialiseFormData($data, 1); //send 1 for edit
                User::where('id', $request->user_id)->update($serialisedData);

                $statusArr = array("status" => "Success", "reason" => "User profile updated successfully.");
                $userInfo = User::where('id', $request->user_id)->first();

                $data = array();
                $userData = array();
                $userData['name'] = $userInfo->name;
                $userData['user_id'] = $userInfo->id;
                $userData['amount'] = $this->numberFormatPrecision($userInfo->wallet_balance, 2, '.');
                if ($userInfo->profile_image != '') {
                    $userData['profile_image'] = PROFILE_FULL_DISPLAY_PATH . $userInfo->profile_image;
                } else {
                    $userData['profile_image'] = HTTP_PATH . '/public/img/' . 'no_user.png';
                }


                $userData['user_type'] = $userInfo->user_type;
                $userData['phone'] = $userInfo->phone;
                $userData['email'] = $userInfo->email;
                $userData['city'] = $userInfo->city;
                $userData['area'] = $userInfo->area ? $userInfo->area : '';
                $userData['dob'] = date('d/m/Y', strtotime($userInfo->dob));
                $userData['national_identity_number'] = $userInfo->national_identity_number ? $userInfo->national_identity_number : '';
                $userData['business_name'] = $userInfo->business_name ? $userInfo->business_name : '';
                $userData['registration_number'] = $userInfo->registration_number ? $userInfo->registration_number : '';

                $data['data'] = $userData;
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } catch (\Exception $ex) {
                $statusArr = array("status" => "Failed", "reason" => "Unknown Exception");
                return response()->json($statusArr, 501);
            }
        }
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
                    'message' => 'Successfully logged out'
        ]);
    }

    public function user_home(Request $request) {
        $userInfo = User::where('id', $request->user_id)->first();
        $lat = $request->lat;
        $lng = $request->lng;
        if (!empty($userInfo)) {

            if ($lat != '' || $lng != '') {
                User::where('id', $userInfo->id)->update(array('lat' => $lat, 'lng' => $lng));
            }


            $userData = array();
            $amount = $userInfo->wallet_balance;

            $bannerArr = array();
            $banners = Banner::where('status', 1)->get();
            if (!empty($banners)) {
                foreach ($banners as $banner) {
//                    $bannerA['banner_name'] = $banner->banner_name;
                    $bannerA['banner_image'] = BANNER_FULL_DISPLAY_PATH . $banner->banner_image;
//                    $bannerA['banner_link'] = $banner->banner_link;
                    $bannerArr[] = $bannerA;
                }
            }
            $data['data'] = $bannerArr;


            $statusArr = array("status" => "Success", 'is_kyc_done' => $userInfo->is_kyc_done, 'amount' => $this->numberFormatPrecision($amount, 2, '.'), "reason" => "Home Details");
//            $data['data'] = $userData;

            $json = array_merge($statusArr, $data);

            return response()->json($json, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "You entered wrong phone number.");
            return response()->json($statusArr, 200);
        }
    }

    public function depositByCard(Request $request) {

        $user_id = $request->user_id;
        $cardNumber = $request->card_number;

        $userInfo = User::where('id', $user_id)->first();
        $cardInfo = Scratchcard::where('card_number', $cardNumber)->first();
        if (!empty($cardInfo)) {
            if ($cardInfo->used_status == 0) {
                if ($cardInfo->expiry_date >= date('Y-m-d')) {
                    $trans = new Transaction([
                        'user_id' => $user_id,
                        'receiver_id' => $user_id,
                        'amount' => $cardInfo->card_value,
                        'trans_type' => 3,
                        'payment_mode' => 'Scratch card',
                        'status' => 1,
                        'refrence_id' => $cardNumber,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $trans->save();

                    $wallet_balance = $userInfo->wallet_balance + $cardInfo->card_value;
                    User::where('id', $user_id)->update(array('wallet_balance' => $wallet_balance));

                    Scratchcard::where('card_number', $cardNumber)->update(array('used_status' => 1));

                    $statusArr = array("status" => "Success", "reason" => "Deposit Completed Successfully.");
                    return response()->json($statusArr, 200);
                } else {
                    $statusArr = array("status" => "Failed", "reason" => "Card already expired.");
                    return response()->json($statusArr, 200);
                }
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Card not a valid card or already used.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "You entered wrong card number.");
            return response()->json($statusArr, 200);
        }
    }

    public function kycUpdate(Request $request) {
        ini_set("precision", 14);
        ini_set("serialize_precision", -1);

        if ($request->user_id == "" or!is_numeric($request->user_id)) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid User id");
            return response()->json($statusArr, 200);
        } else {
            try {
                $userInfo = User::where('id', $request->user_id)->first();

                if (isset($_FILES['identity_image']) && $_FILES['identity_image']['size'] != '0') {
                    $file = $_FILES['identity_image'];
                    $file = Input::file('identity_image');
                    $uploadedFileName = $this->uploadImage($file, IDENTITY_FULL_UPLOAD_PATH);
                    $this->resizeImage($uploadedFileName, IDENTITY_FULL_UPLOAD_PATH, IDENTITY_SMALL_UPLOAD_PATH, IDENTITY_MW, IDENTITY_MH);
                    $data['identity_image'] = $uploadedFileName;
                    @unlink(IDENTITY_FULL_UPLOAD_PATH . $userInfo->identity_image);
                }

                $data['registration_number'] = $request->registration_number;
                $data['national_identity_number'] = $request->national_identity_number;
                $data['is_kyc_done'] = 0;

                $serialisedData = $this->serialiseFormData($data, 1); //send 1 for edit
                User::where('id', $request->user_id)->update($serialisedData);

                $statusArr = array("status" => "Success", 'is_kyc_done' => 0, "reason" => "Kyc details updated successfully.");
                return response()->json($statusArr, 200);
            } catch (\Exception $ex) {
                $statusArr = array("status" => "Failed", "reason" => "Unknown Exception");
                return response()->json($statusArr, 501);
            }
        }
    }

    public function myTransactions(Request $request) {
        if ($request->user_id == "" or!is_numeric($request->user_id)) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid User Id.");
            return response()->json($statusArr, 200);
        } else {
            global $tranType;
//            try {
            $userInfo = User::where('id', $request->user_id)->first();
            if (!empty($userInfo)) {

                $trans = Transaction::where("user_id", $request->user_id)->orwhere("receiver_id", "=", $request->user_id)->orderBy('id', 'desc')->get();
                //echo '<pre>';print_r($trans);exit;
                if (!empty($trans)) {
                    $transArr = array();
                    $transDataArr = array();

                    foreach ($trans as $key => $val) {
                        $transArr['trans_id'] = $val->id;
                        $transArr['trans_amount'] = $this->numberFormatPrecision($val->amount, 2, '.');

                        $transArr['payment_mode'] = $val->payment_mode;


                        if ($val->receiver_id == 0) {
                            $transArr['trans_from'] = $val->payment_mode;
                            $transArr['sender'] = $this->getUserNameById($val->user_id);
                            $transArr['sender_id'] = $val->user_id;
                            $transArr['sender_phone'] = $this->getPhoneById($val->user_id);
                            $transArr['receiver'] = 'Admin';
                            $transArr['receiver_id'] = $val->receiver_id;
                            $transArr['receiver_phone'] = 0;
                            $transArr['trans_type'] = $tranType[$val->trans_type]; //1=Credit;2=Debit;3=topup
                        }elseif ($val->user_id == $request->user_id) { //User is sender
                            $transArr['trans_from'] = $val->payment_mode;
                            $transArr['sender'] = $this->getUserNameById($val->user_id);
                            $transArr['sender_id'] = $val->user_id;
                            $transArr['sender_phone'] = $this->getPhoneById($val->user_id);
                            $transArr['receiver'] = $this->getUserNameById($val->receiver_id);
                            $transArr['receiver_id'] = $val->receiver_id;
                            $transArr['receiver_phone'] = $this->getPhoneById($val->receiver_id);
                            $transArr['trans_type'] = $tranType[$val->trans_type]; //1=Credit;2=Debit;3=topup
                        } else if ($val->receiver_id == $request->user_id) { //USer is Receiver
                            $transArr['trans_from'] = $val->payment_mode;
                            $transArr['sender'] = $this->getUserNameById($val->user_id);
                            $transArr['sender_id'] = $val->user_id;
                            $transArr['sender_phone'] = $this->getPhoneById($val->user_id);
                            $transArr['receiver'] = $this->getUserNameById($val->receiver_id);
                            $transArr['receiver_id'] = $val->receiver_id;
                            $transArr['receiver_phone'] = $this->getPhoneById($val->receiver_id);
                            $transArr['trans_type'] = $tranType[$val->trans_type]; //1=Credit;2=Debit;3=topup
                        }

                        global $tranStatus;
                        $transArr['trans_status'] = $tranStatus[$val->status];

                        $transArr['refrence_id'] = $val->refrence_id;

                        $trnsDt = date_create($val->created_at);
                        $transDate = date_format($trnsDt, "d M Y, h:i A");

                        $transArr['trans_date'] = $transDate;

                        $transDataArr[] = $transArr;
                    }

                    $statusArr = array("status" => "Success", "reason" => "Transaction List.");
                    $data['data'] = $transDataArr;
                    $json = array_merge($statusArr, $data);
                    return response()->json($json, 200);
                } else {
                    $statusArr = array("status" => "Failed", "reason" => "Sorry no transaction found.");
                    return response()->json($statusArr, 200);
                }
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Invalid User.");
                return response()->json($statusArr, 200);
            }
//            } catch (\Exception $ex) {
//                $statusArr = array("status" => "Failed", "reason" => "Unknown Exception");
//                return response()->json($statusArr, 200);
//            }
        }
    }

    private function numberFormatPrecision($number, $precision = 2, $separator = '.') {
        $numberParts = explode($separator, $number);
        $response = $numberParts[0];
        if (count($numberParts) > 1 && $precision > 0) {
            $response .= $separator;
            $response .= substr($numberParts[1], 0, $precision);
        }
        return $response;
    }

    private function getUserNameById($user_id) {
        $matchThese = ["users.id" => $user_id];
        $user = DB::table('users')->select('users.name')->where($matchThese)->first();
        return $user->name;
    }

    private function getPhoneById($user_id) {
        $matchThese = ["users.id" => $user_id];
        $user = DB::table('users')->select('users.phone')->where($matchThese)->first();
        return $user->phone;
    }

    public function depositByAgent(Request $request) {

        $user_id = $request->user_id;
        $phone = $request->phone;
        $amount = $request->amount;

        $userInfo = User::where('phone', $phone)->where('user_type', 'Agent')->first();
        if (!empty($userInfo)) {
            $trans = new Transaction([
                'user_id' => $userInfo->id,
                'receiver_id' => $user_id,
                'amount' => $amount,
                'trans_type' => 4,
                'payment_mode' => 'Agent Deposit',
                'status' => 2,
                'refrence_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $trans->save();

            $statusArr = array("status" => "Success", "reason" => "Deposit Request Send Successfully To Agent.");
            return response()->json($statusArr, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Agent not exist for entered phone number");
            return response()->json($statusArr, 200);
        }
    }

    public function withdrawByAgent(Request $request) {

        $user_id = $request->user_id;
        $phone = $request->phone;
        $amount = $request->amount;

        $loginUser = User::where('id', $user_id)->first();
        $userInfo = User::where('phone', $phone)->where('user_type', 'Agent')->first();
        if (!empty($userInfo)) {
            if ($loginUser->wallet_balance >= $amount) {
                $trans = new Transaction([
                    'user_id' => $userInfo->id,
                    'receiver_id' => $user_id,
                    'amount' => $amount,
                    'trans_type' => 4,
                    'payment_mode' => 'Withdraw',
                    'status' => 2,
                    'refrence_id' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $trans->save();

                $statusArr = array("status" => "Success", "reason" => "Withdraw Request Send Successfully To Agent.");
                return response()->json($statusArr, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Insufficient balance available in the wallet.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Agent not exist for entered phone number");
            return response()->json($statusArr, 200);
        }
    }

    public function fundTransfer(Request $request) {
        if ($request->phone == "" or!is_numeric($request->phone)) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid Phone Number.");
            return response()->json($statusArr, 200);
        } else if ($request->user_id == "" or!is_numeric($request->user_id)) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid Sender Id.");
            return response()->json($statusArr, 200);
        } else if ($request->amount == "" or!is_numeric($request->amount)) {
            $statusArr = array("status" => "Failed", "reason" => "Invalid Amount.");
            return response()->json($statusArr, 200);
        } else {
//            try {         

            $matchThese = ["users.phone" => $request->phone, "users.is_verify" => 1, "users.is_kyc_done" => 1];
            $recieverUser = DB::table('users')->where($matchThese)->first();
//                 echo '<pre>';print_r($matchThese);
//        echo '<pre>';print_r($recieverUser);exit;
            if (!empty($recieverUser)) {

                if ($recieverUser->id == $request->user_id) {
                    $statusArr = array("status" => "Failed", "reason" => "You can not send fund for own account.");
                    return response()->json($statusArr, 200);
                }

                $matchThese = ["users.id" => $request->user_id];
                $senderUser = DB::table('users')->where($matchThese)->first();

                $userActiveAmount = $senderUser->wallet_balance;

                if ($userActiveAmount > $request->amount) {
                    if (!empty($senderUser)) {
                        $refrence_id = time() . rand() . $request->user_id;
                        $trans = new Transaction([
                            'user_id' => $request->user_id,
                            'receiver_id' => $recieverUser->id,
                            'amount' => $request->amount,
                            'trans_type' => 2,
                            'trans_to' => 'Wallet',
                            'payment_mode' => 'wallet2wallet',
                            'refrence_id' => $refrence_id,
                            'status' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $trans->save();
                        $TransId = $trans->id;

                        $sender_wallet_amount = $senderUser->wallet_balance - $request->amount;
                        User::where('id', $request->user_id)->update(['wallet_balance' => $sender_wallet_amount]);

                        $reciever_wallet_amount = $recieverUser->wallet_balance + $request->amount;
                        User::where('id', $recieverUser->id)->update(['wallet_balance' => $reciever_wallet_amount]);
                        $data['data']['wallet_amount'] = $this->numberFormatPrecision($sender_wallet_amount, 2, '.');
                        $data['data']['trans_amount'] = $request->amount;
                        $data['data']['receiver_name'] = $recieverUser->name;
                        $data['data']['receiver_phone'] = $recieverUser->phone;
                        $data['data']['trans_id'] = $TransId;
                        $data['data']['trans_date'] = date('d, M Y, h:i A');

                        $statusArr = array("status" => "Success", "payment_status" => "Success", "reason" => 'Sent Successfully');
                        $json = array_merge($statusArr, $data);
                        return response()->json($json, 200);
                    } else {
                        $statusArr = array("status" => "Failed", "reason" => "Receiver not found or not verified.");
                        return response()->json($statusArr, 200);
                    }
                } else {
                    $statusArr = array("status" => "Failed", "reason" => "Insufficient Balance.");
                    return response()->json($statusArr, 200);
                }
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Receiver not found or not verified.");
                return response()->json($statusArr, 200);
            }
//            } catch (\Exception $ex) {
//                $statusArr = array("status" => "Failed", "reason" => "Unknown Exception");
//                return response()->json($statusArr, 200);
//            }
        }
    }

    public function getUserByQR(Request $request) {
        if ($request->qr_code == "") {
            $statusArr = array("status" => "Failed", "reason" => "Invalid QR Code.");
            return response()->json($statusArr, 200);
        } else {
            $qrCodeArr = explode("##", $request->qr_code);
            $qrId = $qrCodeArr[0];
            if (empty($qrId)) {
                $statusArr = array("status" => "Failed", "reason" => "Invalid QR Code.");
                return response()->json($statusArr, 200);
            }
            if (isset($qrCodeArr[1]) && !empty($qrCodeArr[1])) {
                $qrNm = $qrCodeArr[1];
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Invalid QR Code.");
                return response()->json($statusArr, 200);
            }

            $matchThese = ["users.id" => $qrId];
            $user = DB::table('users')->where($matchThese)->first();
            if ($user) {
                $statusArr = array("status" => "Success", "reason" => "User detail.");
                $userData['id'] = $user->id;
                $userData['name'] = $user->name;
                $userData['phone'] = $user->phone;
                $data['data'] = $userData;
                $json = array_merge($statusArr, $data);
                return response()->json($json, 201);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Invalid QR Code.");
                return response()->json($statusArr, 200);
            }
        }
    }

    public function selectMobileCard(Request $request) {
//        $user_id = $request->user_id;

        $data = array();
        $cards = Card::where('card_type', 2)->where('status', 1)->get();
        //echo '<pre>';print_r($cards);exit;
        if (!empty($cards)) {
            foreach ($cards as $card) {
                $carddetails = Carddetail::where('card_id', $card->id)->where('status', 1)->get();
                if (count($carddetails) > 0) {
                    $cardData['card_id'] = $card->id;
                    $cardData['card_image'] = COMPANY_FULL_DISPLAY_PATH . $card->company_image;
                    $data['data'][] = $cardData;
                }
            }

            if (isset($data['data'])) {
                $statusArr = array("status" => "Success", "reason" => "Card List");
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Mobile recharge card not available.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Mobile recharge card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function mobileCardList(Request $request) {
        $card_id = $request->card_id;

        $data = array();
        $carddetails = Carddetail::where('card_id', $card_id)->where('status', 1)->get();

        if (!empty($carddetails)) {
            foreach ($carddetails as $card) {
                $cardData['card_id'] = $card->id;
                $cardData['card_value'] = $card->card_value;
                $cardData['card_description'] = $card->description?$card->description:'';
                $data['data'][] = $cardData;
            }

            $statusArr = array("status" => "Success", "reason" => "Card List");
            $json = array_merge($statusArr, $data);
            return response()->json($json, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Mobile recharge card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function buyMobileCard(Request $request) {
        $card_id = $request->card_id;
        $user_id = $request->user_id;

        $data = array();
        $carddetail = Carddetail::where('id', $card_id)->first();
        $userInfo = User::where('id', $user_id)->first();

        if (!empty($carddetail)) {
            if ($userInfo->wallet_balance >= $carddetail->card_value) {
                $refrence_id = time() . rand() . '-' . $card_id;
                $trans = new Transaction([
                    'user_id' => $user_id,
                    'receiver_id' => 0,
                    'amount' => $carddetail->card_value,
                    'trans_type' => 2,
                    'trans_to' => 'Wallet',
                    'trans_for' => 'Mobile Recharge Card',
                    'payment_mode' => 'Mobile Recharge Card',
                    'refrence_id' => $refrence_id,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $trans->save();
                $TransId = $trans->id;

                $sender_wallet_amount = $userInfo->wallet_balance - $carddetail->card_value;
                User::where('id', $user_id)->update(['wallet_balance' => $sender_wallet_amount]);

                $result['serial_number'] = $carddetail->serial_number;
                $result['pin_number'] = $carddetail->pin_number;
                $result['instruction'] = $carddetail->instruction;

                $data['data'] = $result;
                $statusArr = array("status" => "Success", "reason" => "Transaction Completed");
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "You have insufficient balance to purchase card.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Mobile recharge card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function selectOnlineCard(Request $request) {
//        $user_id = $request->user_id;

        $data = array();
        $cards = Card::where('card_type', 3)->where('status', 1)->get();
        //echo '<pre>';print_r($cards);exit;
        if (!empty($cards)) {
            foreach ($cards as $card) {
                $carddetails = Carddetail::where('card_id', $card->id)->where('status', 1)->get();
                if (count($carddetails) > 0) {
                    $cardData['card_id'] = $card->id;
                    $cardData['card_image'] = COMPANY_FULL_DISPLAY_PATH . $card->company_image;
                    $data['data'][] = $cardData;
                }
            }

            if (isset($data['data'])) {
                $statusArr = array("status" => "Success", "reason" => "Card List");
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function onlineCardList(Request $request) {
        $card_id = $request->card_id;

        $data = array();
        $carddetails = Carddetail::where('card_id', $card_id)->where('status', 1)->get();

        if (!empty($carddetails)) {
            foreach ($carddetails as $card) {
                $cardData['card_id'] = $card->id;
                $cardData['card_value'] = $card->card_value;
                $cardData['card_description'] = $card->description?$card->description:'';
                $data['data'][] = $cardData;
            }

            $statusArr = array("status" => "Success", "reason" => "Card List");
            $json = array_merge($statusArr, $data);
            return response()->json($json, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function buyOnlineCard(Request $request) {
        $card_id = $request->card_id;
        $user_id = $request->user_id;

        $data = array();
        $carddetail = Carddetail::where('id', $card_id)->first();
        $userInfo = User::where('id', $user_id)->first();

        if (!empty($carddetail)) {
            if ($userInfo->wallet_balance >= $carddetail->card_value) {
                $refrence_id = time() . rand() . '-' . $card_id;
                $trans = new Transaction([
                    'user_id' => $user_id,
                    'receiver_id' => 0,
                    'amount' => $carddetail->card_value,
                    'trans_type' => 2,
                    'trans_to' => 'Wallet',
                    'trans_for' => 'Online Card',
                    'payment_mode' => 'Online Card',
                    'refrence_id' => $refrence_id,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $trans->save();
                $TransId = $trans->id;

                $sender_wallet_amount = $userInfo->wallet_balance - $carddetail->card_value;
                User::where('id', $user_id)->update(['wallet_balance' => $sender_wallet_amount]);

                $result['serial_number'] = $carddetail->serial_number;
                $result['pin_number'] = $carddetail->pin_number;
                $result['instruction'] = $carddetail->instruction;

                $data['data'] = $result;
                $statusArr = array("status" => "Success", "reason" => "Transaction Completed");
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "You have insufficient balance to purchase card.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
            return response()->json($statusArr, 200);
        }
    }
    
    public function selectInternetCard(Request $request) {
//        $user_id = $request->user_id;

        $data = array();
        $cards = Card::where('card_type', 2)->where('status', 1)->get();
        //echo '<pre>';print_r($cards);exit;
        if (!empty($cards)) {
            foreach ($cards as $card) {
                $carddetails = Carddetail::where('card_id', $card->id)->where('status', 1)->get();
                if (count($carddetails) > 0) {
                    $cardData['card_id'] = $card->id;
                    $cardData['card_image'] = COMPANY_FULL_DISPLAY_PATH . $card->company_image;
                    $data['data'][] = $cardData;
                }
            }

            if (isset($data['data'])) {
                $statusArr = array("status" => "Success", "reason" => "Card List");
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function internetCardList(Request $request) {
        $card_id = $request->card_id;

        $data = array();
        $carddetails = Carddetail::where('card_id', $card_id)->where('status', 1)->get();

        if (!empty($carddetails)) {
            foreach ($carddetails as $card) {
                $cardData['card_id'] = $card->id;
                $cardData['card_value'] = $card->card_value;
                $cardData['card_description'] = $card->description?$card->description:'';
                $data['data'][] = $cardData;
            }

            $statusArr = array("status" => "Success", "reason" => "Card List");
            $json = array_merge($statusArr, $data);
            return response()->json($json, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function buyInternetCard(Request $request) {
        $card_id = $request->card_id;
        $user_id = $request->user_id;
        $bundle_number = $request->bundle_number;

        $data = array();
        $carddetail = Carddetail::where('id', $card_id)->first();
        $userInfo = User::where('id', $user_id)->first();

        if (!empty($carddetail)) {
            if ($userInfo->wallet_balance >= $carddetail->card_value) {
                $refrence_id = time() . rand() . '-' . $card_id;
                $trans = new Transaction([
                    'user_id' => $user_id,
                    'receiver_id' => 0,
                    'amount' => $carddetail->card_value,
                    'trans_type' => 2,
                    'trans_to' => 'Wallet',
                    'trans_for' => $bundle_number,
                    'payment_mode' => 'Internet Card',
                    'refrence_id' => $refrence_id,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $trans->save();
                $TransId = $trans->id;

                $sender_wallet_amount = $userInfo->wallet_balance - $carddetail->card_value;
                User::where('id', $user_id)->update(['wallet_balance' => $sender_wallet_amount]);

                $result['serial_number'] = $carddetail->serial_number;
                $result['pin_number'] = $carddetail->pin_number;
                $result['instruction'] = $carddetail->instruction;

                $data['data'] = $result;
                $statusArr = array("status" => "Success", "reason" => "Transaction Completed");
                $json = array_merge($statusArr, $data);
                return response()->json($json, 200);
            } else {
                $statusArr = array("status" => "Failed", "reason" => "You have insufficient balance to purchase card.");
                return response()->json($statusArr, 200);
            }
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Online card not available.");
            return response()->json($statusArr, 200);
        }
    }

    public function nearByUser(Request $request) {
        $user_id = $request->user_id;
        $user_type = $request->user_type;
        
        $userDetail = User::where('id', $user_id)->first();
        $lat = $userDetail->lat;
        $lng = $userDetail->lng;

        $stateQry = "SELECT *,(3959 *acos(cos(radians(" . $lat . ")) *cos(radians(lat)) *cos(radians(lng) -radians(" . $lng . ")) +sin(radians(" . $lat . ")) *sin(radians(lat )))) AS distance FROM users Where user_type='".$user_type."' HAVING distance < 50 ORDER BY distance LIMIT 0, 20";
        $users = DB::select($stateQry);

        $records = array();
        if ($users) {
            foreach($users as $userInfo){
                $userData = array();
                $userData['user_id'] = $userInfo->id;
                $userData['user_type'] = $userInfo->user_type;
                $userData['name'] = $userInfo->name;
                $userData['phone'] = $userInfo->phone;             
                $userData['lat'] = $userInfo->lat;               
                $userData['lng'] = $userInfo->lng;               
                $userData['distance'] = number_format($userInfo->distance*1.609344,1); /*KM*/ 
                if ($userInfo->profile_image != '') {
                    $userData['profile_image'] = PROFILE_FULL_DISPLAY_PATH . $userInfo->profile_image;
                } else {
                    $userData['profile_image'] = HTTP_PATH . '/public/img/' . 'no_user.png';
                }                
                $records[] = $userData;
            }            

            $statusArr = array("status" => "Success", "reason" => "Users List");
            $data['data'] = $records;
            $json = array_merge($statusArr, $data);
            return response()->json($json, 200);
        } else {
            $statusArr = array("status" => "Failed", "reason" => "Users not available.");
            return response()->json($statusArr, 200);
        }
    }

}
