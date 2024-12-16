<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <title>{{$title.TITLE_FOR_LAYOUT}}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="With DafriBank Digital banking services explore an easy and better way to save, invest, make payments, manage your money, and your business whenever you want, wherever you are!" />
        <meta property="og:title" content="DafriBank Digital - Banking with no Border!"/>
        <meta property="og:description" content="With DafriBank Digital banking services explore an easy and better way to save, invest, make online payments, manage your money, and your business whenever you want, wherever you are!" />
        <meta property="og:image" content="https://www.dafribank.com/public/img/DafriBank.png" />
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="630"/>
        <link rel="shortcut icon" href="{{HTTP_PATH}}/public/img/favicon.ico" type="image/x-icon"/>
        <link rel="icon" href="{{HTTP_PATH}}/public/img/favicon.ico" type="image/x-icon"/>
         <link href="https://fonts.googleapis.com/css2?family=Sora&display=swap" rel="stylesheet">
    </head>
    <style type="text/css">
     body {
            font-family: 'Sora', sans-serif;
            padding: 0;
            margin: 0;
        }

        .radio-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: auto;
        }

        .radio-card input[type="radio"]+.radio-label:before {
            content: '';
            background-color: #000;
            box-shadow: inset 0 0 0 4px #fff;
            border-radius: 100%;

            display: inline-block;
            width: 24px;
            height: 24px;
            position: relative;
            top: 0;
            margin-right: 1em;
            vertical-align: top;
            cursor: pointer;
            text-align: center;
            -webkit-transition: all 250ms ease;
            transition: all 250ms ease;
        }

        .radio-card label {
            margin-bottom: 0
        }

        .radio-card input[type="radio"]:checked+.radio-label:before {
            background-color: #000;
            box-shadow: inset 0 0 0 4px #fff;
        }

        .radio-card input[type="radio"]:focus+.radio-label:before {
            outline: none;
            border-color: #3197EE;
        }

        .radio-card input[type="radio"]:disabled+.radio-label:before {
            box-shadow: inset 0 0 0 4px #f4f4f4;
            border-color: #b4b4b4;
            background: #b4b4b4;
        }

        .radio-card input[type="radio"]+.radio-label:empty:before {
            margin-right: 0;
        }

        .box-pay {
            background: #000;
            display: inline-flex;
            padding: 30px;
            align-items: center;
            margin-bottom: 10px;
            width: 100%;
            justify-content: space-between;
            box-sizing: border-box;
        }
        form {
            display: flex;
            align-items: center;
            width: 100%;
            flex-direction: column;
        }
        .sub-btn {
            padding: 8px 20px;
            background: #000;
            border: none;
            color: #fff;
            border-radius: 30px;
            cursor: pointer;
        }

        .logo {
            margin: 0 20px;
        }
        .pay-dis{background: #f1f1f1 !important;}
        .text-center{text-align: center;}

        .modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1060;
    display: none;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}
.modal-dialog {
    margin: 10px auto !important;
    padding: 0 10px;
}
@media (min-width: 576px){
.modal-dialog-centered {
    min-height: calc(100% - 3.5rem);
}
}
@media (min-width: 576px){
    .modal-dialog {
        max-width: 500px;
        margin: 1.75rem auto;
    }
}

.modal-content {
    border-radius: 15px;
}
.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: .3rem;
    outline: 0;
}

.popup-form {
    padding: 20px 40px;
}
.pop-logo {
    text-align: center;
    margin-bottom: 20px;
}
.popup-info-data {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 500;
}
.form-btns-pop {
    text-align: center;
}
.confrm-btn {
    font-size: 16px;
    background: #000;
    padding: 8px 20px;
    border-radius: 5px;
    text-transform: capitalize;
    color: #fff;
    border: 1px solid #000 !important;
    cursor: pointer;
    outline:none;
    box-shadow:none;
}
.modal-content {
    border-radius: 15px !important;
}
.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: .3rem;
    outline: 0;
}


.master-card-img img{max-width: 90px !important;}
    .custom_radio{margin: 0; padding-bottom: 50px;}
    .custom_radio .logo img {max-width: 140px; max-height: 70px;}
    .custom_radio input[type="radio"]{display: none;}
    .custom-wrapper-box {background: #000;display: flex;padding: 30px;align-items: center;color: #fff;width: 100%;justify-content: space-between;box-sizing: border-box; height: 140px;}
    .custom-wrapper-box{margin-bottom: 20px;}
    .custom_radio input[type="radio"] + label {position: relative;display: inline-block;padding-left: 4.5em;margin-right: 0;cursor: pointer;-webkit-transition: all 0.3s ease-in-out;transition: all 0.3s ease-in-out;}
    .custom_radio input[type="radio"] + label:before, 
    .custom_radio input[type="radio"] + label:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 20px;
    height: 20px;
    text-align: center;
    color: white;
    font-family: Times;
    border-radius: 100%;
    -webkit-transition: all .3s ease;
    transition: all .3s ease;
    transform: translateY(-50%);
}
    .custom_radio input[type="radio"] + label:before {
    -webkit-transition: all .3s ease;
    transition: all .3s ease;
    width: 24px;
    height: 24px;
    display: inline-block;
    background: #000;
    border:3px solid #fff;
}
    .custom_radio input[type="radio"] + label:hover:before {
      -webkit-transition: all .3s ease;
      transition: all .3s ease;
      box-shadow: inset 0 0 0 0.3em white, inset 0 0 0 1em #000000;
    }
    .custom_radio input[type="radio"]:checked + label:before {
      -webkit-transition: all .3s ease;
      transition: all .3s ease;
      box-shadow: inset 0 0 0 0.2em white, inset 0 0 0 1em #fff;
    }        
    </style>
       


    <body>
    

<style>
    body{display: block !importants;}
    .same-section{padding: 50px 0;}
    .header-section{padding: 15px 0;}
    .payment-header-logo img {max-width: 200px;}
    .payment-section form{display: block;}
    .main-inner-parent .custom_radio{display: flex; align-items: center; justify-content: center; flex-wrap: wrap;}
    .payment-content-parent{margin-bottom: 50px;}
   .payment-content-parent h3{margin-bottom: 20px; font-size: 40px; line-height: 1.4; color: #000; font-weight: 500; font-family: 'Sora', sans-serif !important; letter-spacing: -2px;}
    .payment-content-parent h3 span{font-weight: 700;}
    .payment-content-parent h3 span span{color: #8e8e8e !important;}
    .payment-content-parent h4{font-size: 20px; color: #8e8e8e !important; line-height: 1.4; font-family: 'Sora', sans-serif !important;}
    .custom-wrapper-box{width: 24%; margin: 5px;}
    .main-inner-parent h2{font-size: 25px; font-weight: 500; color: #212529; line-height: 1.4; margin-bottom: 20px; font-family: 'Sora', sans-serif !important;}
    .main-inner-parent{margin: 10px 0 120px;}
    .payment-bottom-content p{font-size: 14px; color: #979797;line-height: 1.6; margin-bottom: 10px; font-weight: 400; text-align: justify;}
    .main-inner-parent input.sub-btn {min-width: 150px; border: 1px solid transparent; padding: 12px 15px;font-size: 18px;font-weight: 500;color: #fff; transition: 0.4s; -webkit-transition: 0.4s;}
    .main-inner-parent input.sub-btn:hover{background: transparent; color: #000; border-color: #000;}
    .small-dafri-img{margin-bottom: 20px; text-align: left;}
    .small-dafri-img img{max-width: 50px;}
    .logo {margin-left: 0 !important;}

    @media (max-width: 1199px){
        .custom-wrapper-box{width: 48%;}
        .payment-content-parent {margin-bottom: 20px;}
    }

    @media (max-width: 767px){
        .custom-wrapper-box {width: 100%;}
         .payment-content-parent h4{font-size: 15px;}
        .payment-content-parent h3 {margin-bottom: 5px;font-size: 22px;}
        .main-inner-parent h2 {margin-bottom: 10px;}
        .main-inner-parent input.sub-btn {min-width: 130px;padding: 6px 10px;font-size: 16px;}
        .custom_radio {padding-bottom: 20px;}
        .main-inner-parent {margin-bottom: 20px;}
        .payment-header-logo img {max-width: 150px;}
        .same-section {padding: 40px 0;}
        .header-section {padding: 13px 0;}
        .custom-wrapper-box{margin: 5px 0;}
        .main-inner-parent {margin: 10px 0 290px !important;}
        .custom_radio input[type="radio"] + label:before, .custom_radio input[type="radio"] + label:after {left: 47.5%;width: 20px;height: 20px;bottom: -25px;top: inherit;transform: translateY(0);}
        .custom_radio input[type="radio"] + label {padding-left: 0;margin-right: 0;text-align: center;width: 100%;}
        .custom_radio .logo img{max-height: 50px;}
        .logo{margin: 0 !important;}
        .main-inner-parent input.sub-btn{margin-top: 50px;}

    }
    @media (max-width: 480px){
        .main-inner-parent input.sub-btn {padding: 8px 10px;border-radius: 10px;}
    }


</style>

<section class="header-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="payment-header-logo">
                    <a href="{{HTTP_PATH}}">
                        <img src="{{HTTP_PATH}}/public/img/dafribank-logo-white01.svg">
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="same-section payment-section">
    <div class="container">
            <div class="payment-content-parent">
                <h3>Leap in banking, the world <span>loves<span>.</span></span></h3>
                <h4>Explore an easy and better way to save, make payments, manage your money and your business whenever you want, wherever you are!</h4>
            </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="main-inner-parent">
                    <h2>Pay With</h2>
                    <form action="{{HTTP_PATH}}/epayme_merchant_form" method="post" onsubmit="return submit_form(this);">
                        <div class="custom_radio">
                            <div class="custom-wrapper-box">
                              <input type="radio" id="featured-1" name="payment_method" value="card_transfer" checked>
                              <label for="featured-1">
                                <div class="logo">
                                    <img src="{{HTTP_PATH}}/public/img/front/dafribank-logo-white.svg">
                                </div>
                                <input type="hidden" name="currency_code" value="{{$recordInfo->currency}}">
                                <input type="hidden" name="merchant_key" value="{{base64_encode($recordInfo->id)}}">
                                <input type="hidden" name="slug" value="{{$payment_link->slug}}">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                              </label>
                            </div>

                            <div class="custom-wrapper-box">
                              <input type="radio" id="featured-6" name="payment_method" value="card_transfer">
                              <label for="featured-6">
                                <div class="logo colorfull-img">
                                    <img src="{{HTTP_PATH}}/public/img/epay-logo-small-icon01.svg">
                                </div>
                                <input type="hidden" name="currency_code" value="{{$recordInfo->currency}}">
                                <input type="hidden" name="merchant_key" value="{{base64_encode($recordInfo->id)}}">
                                <input type="hidden" name="slug" value="{{$payment_link->slug}}">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                              </label>
                            </div>

                            <div class="custom-wrapper-box">
                              <input type="radio" id="featured-5" name="payment_method" value="ozo">
                              <label for="featured-5">
                                <div class="logo appel-color">
                                    <img src="{{HTTP_PATH}}/public/img/ozow01.svg">
                                </div>
                              </label>
                            </div>

                            <div class="custom-wrapper-box">
                              <input type="radio" id="featured-7" name="payment_method" value="usdt">
                              <label for="featured-7">
                                <div class="logo appel-color">
                                    <img src="{{HTTP_PATH}}/public/img/t-frame-icon01.svg">
                                </div>
                              </label>
                            </div>
                        </div>
                         <div class="text-center">
                            <input type="submit" name="submit" value="Submit" class="sub-btn">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="payment-bottom-content">
            <div class="small-dafri-img">
                 <img src="{{HTTP_PATH}}/public/img/dafri-short-logo.png">
            </div>
            <p>DafriBank Digital LTD is authorised and licensed by the Comoros International Banking Authority, a body of the Central Bank of Comoros. DafriBank is a division of DafriGroup PLC, a public company incorporated in South Africa with CIPC Number: 2020/548810/06, in Nigeria with CAC Number: 1691062 and in UK with Registration Number: 13544984. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Digital asset markets and exchanges are not regulated with the same controls or customer protections available with other forms of financial products and are subject to an evolving regulatory environment. Digital assets do not typically have legal tender status and are not covered by deposit protection insurance. The past performance of a digital asset is not a guide to future performance, nor is it a reliable indicator of future results or performance. Additional disclosures can be found on the Legal and Privacy page</p>
            <p>©2022 DafriBank Digital LTD. All Rights Reserved. A DafriGroup PLC Company</p>
        </div>  
    </div>
</section>






<script>

function submit_form(x) 
{
    var payment_method=x.payment_method.value;
    if(payment_method=='card_transfer')
    {
    return true;  
    }
    else if(payment_method=='ozo')
    {
    $('#staticBackdrop').modal('show');  
    return false;   
    }
    else if(payment_method=='usdt')
    {
    $('#staticBackdropusdt').modal('show');  
    return false;   
    }
    else{
    window.location='<?php echo $payment_link->payment_link; ?>';    
    }
    return false;
}

</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="{{HTTP_PATH}}/public/assets/js/front/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<script>
$(document).click(function (e) {
    if ($(e.target).is('.modal')) {
        $('.modal').modal('hide');
    }
    });  

  function btn_disable_form()
   {
 
         window.location='<?php echo HTTP_PATH ?>/ozow-login';
   }



</script>

    <div class="modal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                        <div class="popup-form">
                            <div class="pop-logo">
                            <img src="{{HTTP_PATH}}/public/img/dafribank-logo-mobile.svg">
                        </div>
                           
                           <div class="popup-info">
                               <div class="popup-info-data">
                                 <span id="recipName">Add funds to your DafriBank account via OZOW to pay this merchant.</span>
                               </div>
                           </div>
                        <div class="form-btns-pop">
                            <button class="confrm-btn button_disable" onclick="btn_disable_form()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal" id="staticBackdropusdt" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropusdtLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                        <div class="popup-form">
                            <div class="pop-logo">
                            <img src="{{HTTP_PATH}}/public/img/dafribank-logo-mobile.svg">
                        </div>
                           
                           <div class="popup-info">
                               <div class="popup-info-data">
                                 <span id="recipName">Please login to add funds to your DafriBank account via USDT to pay this user!</span>
                               </div>
                           </div>
                        <div class="form-btns-pop">
                            <button class="confrm-btn button_disable" onclick="btn_disable_form()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>




</body>
</html>