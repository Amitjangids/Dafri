<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\User;
use App\Beneficiary;
use DB;
use Redirect;
use Stripe;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function __construct()
    {
        //echo $this->get_client_ip();  
        
    /*  if($this->get_client_ip()!='49.36.239.250' && $this->get_client_ip()!='223.177.185.222')
        {     
         echo '
         <!DOCTYPE html>
<html>
<head>

	 <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

<style>
	*{
		padding: 0px;
		margin:0px;
		box-sizing: border-box;
	}

	.dafri_page h1 {
    font-size: 52px;
    font-weight: bold;
    color: #000;
    text-align: center;
    padding: 10px;
    margin-top: 0px;
}

.dafri_page p {
    text-align: center;
    color: black;
    font-size: 18px;
}

.dafri_page {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

}

img.dafri_img {
    width: 280px;
}

</style>
</head>
<body>

<section class="maintenance_page" style="width: 600px;max-width: 100%;padding:10px;margin:0px auto; height: 100vh;display: flex;align-items: center;">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<div class="dafri_page">
				<img src="https://'.$_SERVER['SERVER_NAME'].'/public/img/2639855_maintenance_icon.svg" style="margin-top: 0px;padding: 10px;text-align: center;" class="dafri_img">
				<h1>Maintenance Mode</h1>
				<p>This site is currently under going scheduled maintenance.
                    <br> Please check back soon.</p>
			</div>
			</div>
		</div>
	</div>
</section>

</body>
</html>
         ' ;

         die;
         
        }
             */
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


    public function encpassword($passwordPlain = 0) {
        return password_hash($passwordPlain, PASSWORD_DEFAULT);
    }

    public function getRandString($length) {
        $length = ceil($length / 2);
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    public function serialiseFormData($data = array(), $isEdit = 0) {
        $formData = array();
        unset($data['_token']);
        unset($data['_method']);
        unset($data['confirm_password']);
        // if ($isEdit == 0) {
        //     $data['created_at'] = date('Y-m-d H:i:s');
        // }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function createSlug($slug = null, $tablename = null, $fieldname = 'slug') {
        $slug = filter_var($slug, FILTER_SANITIZE_STRING);
        $slug = str_replace(' ', '-', strtolower($slug));
        $isSlugExist = DB::table($tablename)->where($fieldname, $slug)->first();
        if (!empty($isSlugExist)) {
            $slug = $slug . '-' . bin2hex(openssl_random_pseudo_bytes(6));
            $this->createSlug($slug, $tablename, $fieldname);
        }
        return $slug;
    }

    public function uploadImage($file, $upload_path = null) {
        $orgName = $file->getClientOriginalName();
        $newFileName = bin2hex(openssl_random_pseudo_bytes(4)) . '_' . $orgName;
        $file->move($upload_path, $newFileName);
        return $newFileName;
    }

    public function resizeImage($uploadedFileName, $imgFolder, $thumbfolder, $newWidth = false, $newHeight = false, $quality = 75, $bgcolor = false) {
        $img = $imgFolder . $uploadedFileName;
        $newName = $uploadedFileName;
        $dest = $thumbfolder . $newName;
        list($oldWidth, $oldHeight, $type) = getimagesize($img);
        $ext = $this->image_type_to_extension($type);
        if ($newWidth OR $newHeight) {
            $widthScale = 2;
            $heightScale = 2;

            if ($newWidth)
                $widthScale = $newWidth / $oldWidth;
            if ($newHeight)
                $heightScale = $newHeight / $oldHeight;
            //debug("W: $widthScale  H: $heightScale<br>");
            if ($widthScale < $heightScale) {
                $maxWidth = $newWidth;
                $maxHeight = false;
            } elseif ($widthScale > $heightScale) {
                $maxHeight = $newHeight;
                $maxWidth = false;
            } else {
                $maxHeight = $newHeight;
                $maxWidth = $newWidth;
            }

            if ($maxWidth > $maxHeight) {
                $applyWidth = $maxWidth;
                $applyHeight = ($oldHeight * $applyWidth) / $oldWidth;
            } elseif ($maxHeight > $maxWidth) {
                $applyHeight = $maxHeight;
                $applyWidth = ($applyHeight * $oldWidth) / $oldHeight;
            } else {
                $applyWidth = $maxWidth;
                $applyHeight = $maxHeight;
            }

            $startX = 0;
            $startY = 0;

            switch ($ext) {
                case 'gif' :
                    $oldImage = imagecreatefromgif($img);
                    break;
                case 'png' :
                    $oldImage = imagecreatefrompng($img);
                    break;
                case 'jpg' :
                case 'jpeg' :
                    $oldImage = imagecreatefromjpeg($img);
                    break;
                default :
                    return false;
                    break;
            }
            //create new image
            $newImage = imagecreatetruecolor($applyWidth, $applyHeight);
            imagecopyresampled($newImage, $oldImage, 0, 0, $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
            switch ($ext) {
                case 'gif' :
                    imagegif($newImage, $dest, $quality);
                    break;
                case 'png' :
                    imagepng($newImage, $dest, 8);
                    break;
                case 'jpg' :
                case 'jpeg' :
                    imagejpeg($newImage, $dest, $quality);
                    break;
                default :
                    return false;
                    break;
            }
            imagedestroy($newImage);
            imagedestroy($oldImage);
            if (!$newName) {
                unlink($img);
                rename($dest, $img);
            }
            return true;
        }
    }

    public function image_type_to_extension($imagetype) {
        if (empty($imagetype))
            return false;
        switch ($imagetype) {
            case IMAGETYPE_GIF : return 'gif';
            case IMAGETYPE_JPEG : return 'jpg';
            case IMAGETYPE_PNG : return 'png';
            case IMAGETYPE_SWF : return 'swf';
            case IMAGETYPE_PSD : return 'psd';
            case IMAGETYPE_BMP : return 'bmp';
            case IMAGETYPE_TIFF_II : return 'tiff';
            case IMAGETYPE_TIFF_MM : return 'tiff';
            case IMAGETYPE_JPC : return 'jpc';
            case IMAGETYPE_JP2 : return 'jp2';
            case IMAGETYPE_JPX : return 'jpf';
            case IMAGETYPE_JB2 : return 'jb2';
            case IMAGETYPE_SWC : return 'swc';
            case IMAGETYPE_IFF : return 'aiff';
            case IMAGETYPE_WBMP : return 'wbmp';
            case IMAGETYPE_XBM : return 'xbm';
            default : return false;
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

    public function validatePermission($role_id, $permission) {
        $flag = DB::table('permissions')->select('permissions.id')->where('role_id', $role_id)->where('permission_name', $permission)->first();
        if (!empty($flag))
            return true;
        else
            return false;
    }

    public function generateNumericOTP($n) {
        $generator = "1357902468";
        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }
        return $result;
    }

    public function getCountryCode($name = null) {
        $iso_array = array(
            'ABW' => 'Aruba',
            'AFG' => 'Afghanistan',
            'AGO' => 'Angola',
            'AIA' => 'Anguilla',
            'ALA' => 'Åland Islands',
            'ALB' => 'Albania',
            'AND' => 'Andorra',
            'ARE' => 'United Arab Emirates',
            'ARG' => 'Argentina',
            'ARM' => 'Armenia',
            'ASM' => 'American Samoa',
            'ATA' => 'Antarctica',
            'ATF' => 'French Southern Territories',
            'ATG' => 'Antigua and Barbuda',
            'AUS' => 'Australia',
            'AUT' => 'Austria',
            'AZE' => 'Azerbaijan',
            'BDI' => 'Burundi',
            'BEL' => 'Belgium',
            'BEN' => 'Benin',
            'BES' => 'Bonaire, Sint Eustatius and Saba',
            'BFA' => 'Burkina Faso',
            'BGD' => 'Bangladesh',
            'BGR' => 'Bulgaria',
            'BHR' => 'Bahrain',
            'BHS' => 'Bahamas',
            'BIH' => 'Bosnia and Herzegovina',
            'BLM' => 'Saint Barthélemy',
            'BLR' => 'Belarus',
            'BLZ' => 'Belize',
            'BMU' => 'Bermuda',
            'BOL' => 'Bolivia, Plurinational State of',
            'BRA' => 'Brazil',
            'BRB' => 'Barbados',
            'BRN' => 'Brunei Darussalam',
            'BTN' => 'Bhutan',
            'BVT' => 'Bouvet Island',
            'BWA' => 'Botswana',
            'CAF' => 'Central African Republic',
            'CAN' => 'Canada',
            'CCK' => 'Cocos (Keeling) Islands',
            'CHE' => 'Switzerland',
            'CHL' => 'Chile',
            'CHN' => 'China',
            'CIV' => 'Côte d\'Ivoire',
            'CMR' => 'Cameroon',
            'COD' => 'Congo, the Democratic Republic of the',
            'COG' => 'Congo',
            'COK' => 'Cook Islands',
            'COL' => 'Colombia',
            'COM' => 'Comoros',
            'CPV' => 'Cape Verde',
            'CRI' => 'Costa Rica',
            'CUB' => 'Cuba',
            'CUW' => 'Curaçao',
            'CXR' => 'Christmas Island',
            'CYM' => 'Cayman Islands',
            'CYP' => 'Cyprus',
            'CZE' => 'Czech Republic',
            'DEU' => 'Germany',
            'DJI' => 'Djibouti',
            'DMA' => 'Dominica',
            'DNK' => 'Denmark',
            'DOM' => 'Dominican Republic',
            'DZA' => 'Algeria',
            'ECU' => 'Ecuador',
            'EGY' => 'Egypt',
            'ERI' => 'Eritrea',
            'ESH' => 'Western Sahara',
            'ESP' => 'Spain',
            'EST' => 'Estonia',
            'ETH' => 'Ethiopia',
            'FIN' => 'Finland',
            'FJI' => 'Fiji',
            'FLK' => 'Falkland Islands (Malvinas)',
            'FRA' => 'France',
            'FRO' => 'Faroe Islands',
            'FSM' => 'Micronesia, Federated States of',
            'GAB' => 'Gabon',
            'GBR' => 'United Kingdom',
            'GEO' => 'Georgia',
            'GGY' => 'Guernsey',
            'GHA' => 'Ghana',
            'GIB' => 'Gibraltar',
            'GIN' => 'Guinea',
            'GLP' => 'Guadeloupe',
            'GMB' => 'Gambia',
            'GNB' => 'Guinea-Bissau',
            'GNQ' => 'Equatorial Guinea',
            'GRC' => 'Greece',
            'GRD' => 'Grenada',
            'GRL' => 'Greenland',
            'GTM' => 'Guatemala',
            'GUF' => 'French Guiana',
            'GUM' => 'Guam',
            'GUY' => 'Guyana',
            'HKG' => 'Hong Kong',
            'HMD' => 'Heard Island and McDonald Islands',
            'HND' => 'Honduras',
            'HRV' => 'Croatia',
            'HTI' => 'Haiti',
            'HUN' => 'Hungary',
            'IDN' => 'Indonesia',
            'IMN' => 'Isle of Man',
            'IND' => 'India',
            'IOT' => 'British Indian Ocean Territory',
            'IRL' => 'Ireland',
            'IRN' => 'Iran, Islamic Republic of',
            'IRQ' => 'Iraq',
            'ISL' => 'Iceland',
            'ISR' => 'Israel',
            'ITA' => 'Italy',
            'JAM' => 'Jamaica',
            'JEY' => 'Jersey',
            'JOR' => 'Jordan',
            'JPN' => 'Japan',
            'KAZ' => 'Kazakhstan',
            'KEN' => 'Kenya',
            'KGZ' => 'Kyrgyzstan',
            'KHM' => 'Cambodia',
            'KIR' => 'Kiribati',
            'KNA' => 'Saint Kitts and Nevis',
            'KOR' => 'Korea South',
            'KWT' => 'Kuwait',
            'LAO' => 'Lao People\'s Democratic Republic',
            'LBN' => 'Lebanon',
            'LBR' => 'Liberia',
            'LBY' => 'Libya',
            'LCA' => 'Saint Lucia',
            'LIE' => 'Liechtenstein',
            'LKA' => 'Sri Lanka',
            'LSO' => 'Lesotho',
            'LTU' => 'Lithuania',
            'LUX' => 'Luxembourg',
            'LVA' => 'Latvia',
            'MAC' => 'Macao',
            'MAF' => 'Saint Martin (French part)',
            'MAR' => 'Morocco',
            'MCO' => 'Monaco',
            'MDA' => 'Moldova, Republic of',
            'MDG' => 'Madagascar',
            'MDV' => 'Maldives',
            'MEX' => 'Mexico',
            'MHL' => 'Marshall Islands',
            'MKD' => 'Macedonia, the former Yugoslav Republic of',
            'MLI' => 'Mali',
            'MLT' => 'Malta',
            'MMR' => 'Myanmar',
            'MNE' => 'Montenegro',
            'MNG' => 'Mongolia',
            'MNP' => 'Northern Mariana Islands',
            'MOZ' => 'Mozambique',
            'MRT' => 'Mauritania',
            'MSR' => 'Montserrat',
            'MTQ' => 'Martinique',
            'MUS' => 'Mauritius',
            'MWI' => 'Malawi',
            'MYS' => 'Malaysia',
            'MYT' => 'Mayotte',
            'NAM' => 'Namibia',
            'NCL' => 'New Caledonia',
            'NER' => 'Niger',
            'NFK' => 'Norfolk Island',
            'NGA' => 'Nigeria',
            'NIC' => 'Nicaragua',
            'NIU' => 'Niue',
            'NLD' => 'Netherlands',
            'NOR' => 'Norway',
            'NPL' => 'Nepal',
            'NRU' => 'Nauru',
            'NZL' => 'New Zealand',
            'OMN' => 'Oman',
            'PAK' => 'Pakistan',
            'PAN' => 'Panama',
            'PCN' => 'Pitcairn',
            'PER' => 'Peru',
            'PHL' => 'Philippines',
            'PLW' => 'Palau',
            'PNG' => 'Papua New Guinea',
            'POL' => 'Poland',
            'PRI' => 'Puerto Rico',
            'PRK' => 'Korea North',
            'PRT' => 'Portugal',
            'PRY' => 'Paraguay',
            'PSE' => 'Palestinian Territory, Occupied',
            'PYF' => 'French Polynesia',
            'QAT' => 'Qatar',
            'REU' => 'Réunion',
            'ROU' => 'Romania',
            'RUS' => 'Russian Federation',
            'RUS' => 'Russia',
            'RWA' => 'Rwanda',
            'SAU' => 'Saudi Arabia',
            'SDN' => 'Sudan',
            'SEN' => 'Senegal',
            'SGP' => 'Singapore',
            'SGS' => 'South Georgia and the South Sandwich Islands',
            'SHN' => 'Saint Helena, Ascension and Tristan da Cunha',
            'SJM' => 'Svalbard and Jan Mayen',
            'SLB' => 'Solomon Islands',
            'SLE' => 'Sierra Leone',
            'SLV' => 'El Salvador',
            'SMR' => 'San Marino',
            'SOM' => 'Somalia',
            'SPM' => 'Saint Pierre and Miquelon',
            'SRB' => 'Serbia',
            'SSD' => 'South Sudan',
            'STP' => 'Sao Tome and Principe',
            'SUR' => 'Suriname',
            'SVK' => 'Slovakia',
            'SVN' => 'Slovenia',
            'SWE' => 'Sweden',
            'SWZ' => 'Swaziland',
            'SXM' => 'Sint Maarten (Dutch part)',
            'SYC' => 'Seychelles',
            'SYR' => 'Syrian Arab Republic',
            'TCA' => 'Turks and Caicos Islands',
            'TCD' => 'Chad',
            'TGO' => 'Togo',
            'THA' => 'Thailand',
            'TJK' => 'Tajikistan',
            'TKL' => 'Tokelau',
            'TKM' => 'Turkmenistan',
            'TLS' => 'Timor-Leste',
            'TON' => 'Tonga',
            'TTO' => 'Trinidad and Tobago',
            'TUN' => 'Tunisia',
            'TUR' => 'Turkey',
            'TUV' => 'Tuvalu',
            'TWN' => 'Taiwan, Province of China',
            'TZA' => 'Tanzania, United Republic of',
            'UGA' => 'Uganda',
            'UKR' => 'Ukraine',
            'UMI' => 'United States Minor Outlying Islands',
            'URY' => 'Uruguay',
            'USA' => 'United States',
            'UZB' => 'Uzbekistan',
            'VAT' => 'Holy See (Vatican City State)',
            'VCT' => 'Saint Vincent and the Grenadines',
            'VEN' => 'Venezuela, Bolivarian Republic of',
            'VGB' => 'Virgin Islands, British',
            'VIR' => 'Virgin Islands, U.S.',
            'VNM' => 'Viet Nam',
            'VUT' => 'Vanuatu',
            'WLF' => 'Wallis and Futuna',
            'WSM' => 'Samoa',
            'YEM' => 'Yemen',
            'ZAF' => 'South Africa',
            'ZMB' => 'Zambia',
            'ZWE' => 'Zimbabwe'
        );

        $iso_array_new = array_flip($iso_array);

        return $iso_array_new[$name];
    }

    function addBeneficiary($user_id = null, $receiver_id = null) {
        $userInfo = User::where('id', $user_id)->first();
        $receiverInfo = User::where('id', $receiver_id)->first();

        $beneficiaryExist = DB::table('beneficiaries')->where('user_id', $user_id)->where('receiver_id', $receiver_id)->first();

        if (empty($beneficiaryExist)) {
            $transCount = DB::table('transactions')->where('user_id', $user_id)->where('receiver_id', $receiver_id)->where('trans_for', 'W2W')->count();
            if ($transCount > 4) {
                $benefit = new Beneficiary([
                    'user_id' => $user_id,
                    'receiver_id' => $receiver_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $benefit->save();
            }
        }
    }

}
