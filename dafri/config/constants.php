<?php
define('SITE_TITLE', 'DafriBank Digital - Banking with no Border!');
define('MAIL_SIGNATURE', 'The DafriBank Digital Team');
define('TITLE_FOR_LAYOUT', ' | ' . SITE_TITLE);
define('HTTP_PATH', 'http://localhost/dafri');
define('DBA_WEBSITE', 'http://localhost/dba-interest');
define('USDT_WEBSITE', 'http://localhost/usdt-interest');
define('ARTISAN_PATH', 'http://localhost/dafri');
define('PUBLIC_PATH', 'http://localhost/dafri/public');
define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/dafri");

define('MAIL_FROM', 'admin@dafribank.com');

define('CAPTCHA_KEY', '6LeYbxEaAAAAAJGo49cTRPqYG3hSPDVKJA4RPgrH');

define('IMAGE_DOC_EXT', 'image/gif, image/jpeg, image/png, .pdf');
define('IMAGE_EXT', 'image/gif, image/jpeg, image/png');
define('DOC_EXT', '.pdf,.doc,.docx');
define('MAX_IMAGE_UPLOAD_SIZE_DISPLAY', '2MB');
define('MAX_IMAGE_UPLOAD_SIZE_VAL', 2048);

define('LOGO_IMAGE_DISPLAY_PATH', PUBLIC_PATH . '/img/front/logo.svg');
define('LOGO_IMAGE_DISPLAY_PATH_PNG', PUBLIC_PATH . '/img/front/logo-dafri.png');
define('LOGO_PATH', LOGO_IMAGE_DISPLAY_PATH);
define('LOGO_PATH_PNG', LOGO_IMAGE_DISPLAY_PATH_PNG);
define('WHITE_LOGO_PATH', PUBLIC_PATH . '/img/front/dafribank-logo-white.svg');
define('BLACK_LOGO_PATH', PUBLIC_PATH . '/img/front/dafribank-logo-black.svg');

define('FAVICON_PATH', PUBLIC_PATH . '/img/favicon.ico');

define('CIRCLE_API', 'QVBJX0tFWTpjOTg5MTBjYjdlZDM3NWM5NzU2NzQ5MDllOTMyYzk0Yzo2MDZhMGE2MGQzMDY5ZGU5MDgyODA0MTNmNGNlYmZjMQ==');

define('CURR','IQD');

define('OTP_TIME','15');

/*****Twellio Details*****/
global $sms_from;
$sms_from='00000000';
define('Account_SID', '');
define('Auth_Token', '');

define('TWILIO_ID', 'AC4ebd9907404909a194dc4ffca3f46440');
define('TWILIO_TOKEN', 'e6f35e71ed52b96fedf3d76342a0a61f');
define('TWILIO_NUMBER', '+12022171671');

define("SUMSUB_SECRET_KEY", '8JcjoO6UI7VdZuXIy9DZDtGGRCM836hp');
define("SUMSUB_APP_TOKEN", 'tst:nLypbRzfli178uSXXxZiXqEY.B2vlf3BNI2LK3oOkdltBnDDZfsDk0Fju');
define("SUMSUB_TEST_BASE_URL", "https://test-api.sumsub.com");

/*******Sandbox Details*********/
define("AIRTIME_URL",'https://topups-sandbox.reloadly.com');
define("AIRTIME_CLIENT_ID",'M3Txn56T7d9J9NQ3tZBtAbMn7Udp6eLH');
define("AIRTIME_SECRET_KEY",'QYgOah6S9X-Hm5N1k7E7nPcVnPeV3D-c1J17No35BvMrVUe0nV9neMfghfXgxDb');

/*******Sandbox Details Gift Card*********/
define("AIRTIME_GIFTCARD_URL",'https://giftcards-sandbox.reloadly.com');   

/*******Live Details*********/
//define("AIRTIME_URL",'https://topups.reloadly.com');
//define("AIRTIME_CLIENT_ID",'4HVXlSV8fsKvHYgo6dP3Xy4Imc46BoyO');
//define("AIRTIME_SECRET_KEY",'sBdo0JH9PH-Ro3mNMQuMmArpTTUyfO-JFYrw6UKizotN3eUO2OalM5SBtmOQEgl');

/* ******* profile image path ****** */
define('PROFILE_FULL_UPLOAD_PATH', BASE_PATH . '/public/uploads/profile_images/full/');
define('PROFILE_SMALL_UPLOAD_PATH', BASE_PATH . '/public/uploads/profile_images/small/');
define('PROFILE_FULL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/profile_images/full/');
define('PROFILE_SMALL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/profile_images/small/');
define('PROFILE_MW', 250);
define('PROFILE_MH', 250);


/* ******* Blog image path ****** */
define('BLOG_FULL_UPLOAD_PATH', BASE_PATH . '/public/uploads/blog_images/full/');
define('BLOG_SMALL_UPLOAD_PATH', BASE_PATH . '/public/uploads/blog_images/small/');
define('BLOG_FULL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/blog_images/full/');
define('BLOG_SMALL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/blog_images/small/');
define('BLOG_MW', 250);
define('BLOG_MH', 250);

/* ******* identity image path ****** */
define('DOCUMENT_FULL_UPLOAD_PATH', BASE_PATH . '/public/uploads/documents/full/');
define('IDENTITY_FULL_DISPLAY_PATH', HTTP_PATH .'/public/uploads/documents/full/');
define('DOCUMENT_SMALL_UPLOAD_PATH', BASE_PATH . '/public/uploads/documents/small/');
define('DOCUMENT_FULL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/documents/full/');
define('DOCUMENT_SMALL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/documents/small/');
define('DOCUMENT_MW', 250);
define('DOCUMENT_MH', 250);

/* ******* Company image path ****** */
define('COMPANY_FULL_UPLOAD_PATH', BASE_PATH . '/public/uploads/company/full/');
define('COMPANY_SMALL_UPLOAD_PATH', BASE_PATH . '/public/uploads/company/small/');
define('COMPANY_FULL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/company/full/');
define('COMPANY_SMALL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/company/small/');
define('COMPANY_MW', 250);
define('COMPANY_MH', 250);

/* ******* Banner image path ****** */
define('BANNER_FULL_UPLOAD_PATH', BASE_PATH . '/public/uploads/banner/full/');
define('BANNER_SMALL_UPLOAD_PATH', BASE_PATH . '/public/uploads/banner/small/');
define('BANNER_FULL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/banner/full/');
define('BANNER_SMALL_DISPLAY_PATH', HTTP_PATH . '/public/uploads/banner/small/');
define('BANNER_MW', 150);
define('BANNER_MH', 145);

define('STRIPE_WEBHOOK_KEY','whsec_2kMVfykB3HTTSPJg7YtFSJgEX4rElbRP');
define('IS_OZOW_TEST', 'true');
define('CURRENCY_CONVERT_API_KEY','87a672a4198bc19f5b5fbdc3');
define('TRANS_LIMIT_BEFORE_KYC',50);

global $cardType;
$cardType  = array(
    1 => 'Internet Recharge Card',
    2 => 'Mobile Recharge Card',
    3 => 'Online Gift Card',
);


global $kycStatus;
$kycStatus  = array(
    0 => 'Pending',
    1 => 'Approved',
    2 => 'Declined',
);

global $tranStatus;
$tranStatus  = array(
    1 => 'Success',
    2 => 'Pending',
    3 => 'Failed',
);
global $tranType;
$tranType  = array(
    1 => 'Credit',
    2 => 'Debit',
    3 => 'Topup',
    4 => 'Request',
);

global $businessType;
$businessType  = array(
    'Cooperative (Co-op)' => 'Cooperative (Co-op)',
    'Corporation' => 'Corporation',
    'Limited Liability Company (LLC)' => 'Limited Liability Company (LLC)',
    'Limited Partnership' => 'Limited Partnership',
    'Nonprofit Organization' => 'Nonprofit Organization',
    '(PTY) LTD' => '(PTY) LTD',
    'Sole Proprietorship' => 'Sole Proprietorship',
    'Public Company' => 'Public Company',
);
global $addressType;
$addressType  = array(
    'Bank Statement' => 'Bank Statement',
    'Utility Bills' => 'Utility Bills',
);
global $identityType;
$identityType  = array(
    'National ID Card' => 'National ID Card',
    'Passport' => 'Passport',
    'Driving licence' => 'Driving licence',
);

global $identityCardType;
$identityCardType  = array(
    'National ID Card' => 'ID_CARD',
    'Passport' => 'PASSPORT',
    'Driving licence' => 'DRIVERS',
);

global $currency;
$currency  = array(
    '$' => '$ USD',
    '€' => '€ EURO',
    '₹' => '₹ INR',
);

global $currencyList;
$currencyList = array('USD'=>'USD','GBP'=>'GBP','ZAR'=>'ZAR','BWP'=>'BWP','NGN'=>'NGN','SZL'=>'SZL','KES'=>'KES','EUR'=>'EUR','PHP'=>'PHP','KWD'=>'KWD','IDR'=>'IDR','NAD'=>'NAD','XAF'=>'XAF','INR'=>'INR','PKR'=>'PKR','RUB'=>'RUB','CAD'=>'CAD','BRL'=>'BRL','JPY'=>'JPY','CNY'=>'CNY','ZMW'=>'ZMW','GHS'=>'GHS','AED'=>'AED');

global $sernameList;
$sernameList = array('Mr.'=>'Mr.','Miss.'=>'Miss.','Mrs.'=>'Mrs.','Dr.'=>'Dr.');

?>