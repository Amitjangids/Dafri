@extends('layouts.inner')
@section('content')
<style>
    .fund-name-box h6 {
        font-size: 12px !important;
    }

    .btn_sub {
        width: auto;
        display: inline-block;
        padding: 12px 20px;
        margin-bottom: 30px;

    }

    .btn_sub:hover {
        text-decoration: none;
        color: var(--main-white-color);
    }
</style>

  
<style>
      .main-box-content {background: #eee;text-align: center;padding: 15px 5px;}
      .main-box-content h2 {display: inline-block;font-size: 18px;color: #000;margin:0;line-height:1.4;font-weight: 500;}
      .main-box:hover{transform:translateY(10px)}
      .main-box {border: 1px solid rgb(0 0 0 / 8%);overflow: hidden;transition:0.4s; -webkit-transition:0.4s;border-radius: 10px;}
      .main-box-img img {width: 100%;}
      .same-section{padding:40px 0;}
      .same-section .row .col-lg-3{margin-bottom:30px;}


      .quantity {position: relative;}
      .quantity input[type=number]::-webkit-inner-spin-button,
      .quantity input[type=number]::-webkit-outer-spin-button{-webkit-appearance: none;margin: 0;}
      .quantity input[type=number]{-moz-appearance: textfield;}
      .quantity input {width: 45px;height: 42px; background: transparent; border: none; line-height: 1.65;float: left;display: block;padding: 0;margin: 0;padding-left: 20px; border: none; color: #000 !important; font-weight: 500 !important;}
      .quantity input:focus {outline: 0;}
      .quantity-nav {float: left;position: relative;height: 42px;}

      .quantity-button {position: relative;cursor: pointer;border-left: 1px solid #eee;width: 20px;text-align: center;color: #333;font-size: 13px;font-family: "Trebuchet MS", Helvetica, sans-serif !important;line-height: 1.7; -webkit-transform: translateX(-100%);transform: translateX(-100%);-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;}
      .quantity-button.quantity-up {position: absolute;height: 50%;top: 0;border-bottom: 1px solid #eee;}
      .quantity-button.quantity-down {position: absolute;bottom: -1px;height: 50%;}
      .giftcards-parent .modal-dialog {max-width: 750px !important;}

      .dafri-logo-img img {filter: brightness(0);}
      .header-top-content {margin: 20px 0 40px;text-align: center;}
      .header-top-content h3 {font-size: 30px;font-weight: 500;color: #000;margin-bottom: 5px;line-height: 1.4;}
      .header-top-content h5 {font-size: 25px;font-weight: 500;color: #000;line-height: 1.4;margin-bottom: 10px;}
      .giftcards-parent .modal-header {border: none;padding: 0 !important;}
      .gift-table-parent p {color: #000;font-size: 16px;font-weight: 500;line-height: 1.6;}
      .gift-table-parent ul {display: flex;align-items: flex-start;justify-content: space-between;padding: 0;margin: 0; position: relative;}
      .giftcard-heading h2 {font-weight: 500;color: #000;font-size: 16px;line-height: 1.4;margin-bottom: 30px;}
      .giftcard-img img {max-width: 120px;}
      .giftcard-img {display: flex;align-items: center;justify-content: space-between;}
      .giftcard-img span {display: inline-block;font-size: 14px;font-weight: 400;color: #000;padding-left: 20px;}
      .giftcard-bottom h2 {font-size: 14px;font-weight: 500;color: #000;line-height: 1.4;}
      .modal-header .close {padding: 1rem 1rem;margin: -1rem -1rem -1rem auto;position: absolute;top: 0px;right: 0px;font-size: 20px;width: 20px;height: 20px;border-radius: 100%;background: #fff;color: #000;display: flex;align-items: center;justify-content: center;z-index: 1;text-shadow: none;opacity: 1;}
      .gift-table-parent ul li{list-style: none;}
      .gift-table-parent ul li:first-child {max-width: 220px;}
      .giftcard-bottom {vertical-align: middle;display: flex;align-items: center;justify-content: center;height: 80px;}
      .quantity-button {position: relative;cursor: pointer;border-left: 1px solid #eee;width: 30px !important;text-align: center;height: 30px !important;background: #000;margin: 2px;font-size: 23px !important;font-family: "Trebuchet MS", Helvetica, sans-serif !important;line-height: 1.7;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;display: flex;align-items: center;justify-content: center;border-radius: 3px;color: #fff; transform: translateX(0) !important;}
      .quantity.quantity {position: relative;display: flex;align-items: center;justify-content: center;}
      .quantity-button.quantity-up,
      .quantity-button.quantity-down{position: inherit;}
      .quantity-nav {height: 100% !important;}
      .quantity input {padding: 0; padding-left: 0; text-align: center;}
      /* .gift-table-parent ul:after {content: "";position: absolute;top: 40px;left: 0;right: 0;border: 1px dashed rgb(0 0 0 / 38%);border-width: 1px;}
      .gift-table-parent ul:before {content: "";position: absolute;bottom: -10px;left: 0;right: 0;border: 1px dashed rgb(0 0 0 / 38%);border-width: 1px;} */
      .giftcard-bottom-content{margin: 50px 0;}
      .giftcard-bottom-content ul{padding: 0; margin: 0;}
      .giftcard-bottom-content ul li{list-style: none; font-size: 14px; font-weight: 400; color: #000; line-height: 1.6;}
      .giftcard-bottom-content ul li + li{padding-top: 5px;}
      .giftcard-bottom-content p{position: relative; padding-left: 20px; margin: 10px 0; cursor: pointer; user-select: none;}
      .giftcard-bottom-content p label{margin: 0;}
      .giftcard-bottom-content span{color: #000; font-size: 14px; font-weight: 500; line-height: 1.6; user-select: none; cursor: pointer;}
      .giftcard-bottom-content p input[type="checkbox"] {position: absolute;left: 0;top: 7px;transform: translateY(0); cursor: pointer;}
      .giftcar-submit-btn{margin: 30px 0; text-align: center;}
      .giftcar-submit-btn .btn {min-width: 120px;text-align: center;padding: 7px 15px;font-size: 14px;background: #000;color: #fff;text-transform: uppercase;font-weight: 500;transition: 0.4s;-webkit-transition: 0.4s;border: 1px solid transparent;border-radius: 7px;}
      .giftcar-submit-btn .btn:hover{background: transparent; color: #000; border-color: #000;}
      .giftcard-bottom h2 input {padding: 0px; margin: 0 2px; align-self: center; min-width: auto; max-width: 200px; text-align:center;background: transparent;border: none;display: inline-block;color: #000;font-weight: 500;font-size: 14px;}
      .giftcards-parent .modal-dialog{height: auto !important; margin: 40px auto !important;}

      @media only screen and (max-width: 1399px) {
       .giftcards-parent .modal-body{padding: 25px;}
       .giftcards-parent .modal-dialog {max-width: 640px !important;}
       .header-top-content h3 {font-size: 26px;margin-bottom: 0;}
       .header-top-content h5 {font-size: 21px;margin-bottom: 0;}
       .header-top-content {margin: 10px 0 30px;}
       .giftcard-bottom-content {margin: 30px 0;}
       .giftcar-submit-btn {margin: 30px 0 0;}
      }

      @media only screen and (max-width: 1399px) {
        .header-top-content h3 {font-size: 22px;}
        .header-top-content h5 {font-size: 18px;}
        .header-top-content {margin: 10px 0 20px;}
        .giftcar-submit-btn {margin: 20px 0 0;}
        .giftcar-submit-btn .btn {min-width: 100px;padding: 7px 15px;}

      }

      @media only screen and (max-width: 991px) {
        .giftcards-parent .modal-dialog {max-width: 600px !important;}
        #page-content-wrapper .row.same-section .col-lg-3 {max-width: 33.33%;flex: 0 0 33.33%;}
        .main-box a{display: inline-block; width: 100%; height: 100%;}
        .gift-table-parent ul li {word-break: break-all;}
      }
      @media only screen and (max-width: 767px) {
        .modal.show .modal-dialog{display: flex !important;}
        .header-top-content h5 {font-size: 16px;}
        .header-top-content h3 {font-size: 20px;}
        .gift-table-parent p {font-size: 14px;}
        .giftcard-heading h2 {font-size: 14px;margin-bottom: 10px;}
        .gift-table-parent ul:after {top: 25px;}
        .giftcard-bottom-content ul li{font-size: 13px;}
        .giftcard-bottom-content span{font-size: 13px;}
        .giftcards-parent .modal-body {padding: 15px 25px;}
        #page-content-wrapper .row.same-section .col-lg-3 {max-width: 50%;flex: 0 0 50%;}
      }

      @media only screen and (max-width: 650px) {
        .gift-table-parent ul{display: inline-block; width: 100%;}
        .giftcard-bottom {display: block;height: auto; max-width: 150px;}
        .giftcard-img{display: block;}
        .gift-table-parent ul li {max-width: 100% !important;text-align: left; padding: 10px 0;}
        .gift-table-parent ul li + li{border-top:1px solid rgb(0 0 0 / 25%);}
        .gift-table-parent ul:before,
        .gift-table-parent ul:after{display: none;}
        .quantity.quantity{display: block;}
        .quantity-button {display: inline-block;line-height: 30px;}
        .quantity-nav{float: inherit;}
        .giftcards-parent .modal-dialog{height: auto !important;}
        .giftcards-parent{top: 50%; transform: translateY(-50%);}
        .giftcards-parent .modal-header .close{margin: 0 !important; top: 10px;right: 10px;background: #000;color: #fff;}
        .giftcards-parent .modal-dialog {max-width: 500px !important;}
        .header-top-content {margin: 20px 0 30px;}
        .header-top-content h3 {margin-bottom: 5px;}
        .modal-content {border: none;overflow: hidden;height: auto;overflow-y: scroll;}
        .modal.show .modal-dialog {display: flex !important;position: absolute;top: 50%;transform: translateY(-50%);margin: 0 auto !important;left: 0;right: 0;}
      }

      @media only screen and (max-width: 576px) {
        .modal.show .modal-dialog{left: 20px; right: 20px;}
        .giftcards-parent .modal-dialog {max-width: 100% !important;}
        #page-content-wrapper .row.same-section .col-lg-3{max-width: 100%; flex: 0 0 100%; margin-top: 20px;}
        #page-content-wrapper .row.same-section .col-lg-3:first-child{margin-top: 0px;}
      }

  </style>




<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2 w-100">
            <div class="row same-section">
              
     <?php 
    if(isset($giftcard[0]->id))
    {
    foreach($giftcard as $key=>$gift) { 
      $date = date_create($gift->updated_at);
      $transDate = date_format($date,'d F Y');
      
      ?>
    <div class="col-lg-3">
          <div class="main-box">
                 <a type="button" class="" data-toggle="modal" data-target="#exampleModal{{$key}}">
                  <div class="main-box-img">
                      <img src="{{$gift->product_image_link}}" alt="image">
                  </div>
                  <div class="main-box-content">
                      <h2> {{ucwords($gift->productName)}} 
                        <!-- {{$gift->countryCode}}  -->
                      </h2>
                  </div>
              </a>
          </div>
      </div>

 <div class="modal giftcards-parent fade" id="exampleModal{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModal{{$key}}Label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="gift-card-box">
          <div class="dafri-logo-img">
            <a href="#">
              <img src="{{HTTP_PATH}}/public/img/front/dafribank-logo-white.svg" alt="image">
            </a>
          </div>
          <div class="header-top-content">
            <h5>Transaction Details</h5>
          </div>
          <div class="gift-table-parent">
                 <ul>
                  <li>Transaction</li>
                  <li>#{{$gift->d_trans_id}}</li>
                </ul>
               
                <ul>
                  <li>Date and time:</li>
                  <li>{{$transDate}}</li>
                </ul>

                <ul>
                  <li>Gift Card Brand:</li>
                  <li>{{$gift->brandName}}</li>
                </ul>

                <ul>
                  <li>Gift Card Product:</li>
                  <li>{{$gift->productName}}</li>
                </ul>

                <ul>
                  <li>Country Code:</li>
                  <li>{{$gift->countryCode}}</li>
                </ul>

                <ul>
                  <li>Amount:</li>
                  <li>{{$gift->user_currency}} {{$gift->amount_user_currency}}</li>
                </ul>

                <ul>
                  <li>Recipient Email:</li>
                  <li>{{$gift->recipientEmail}}</li>
                </ul>

                <ul>
                  <li>Recipient Phone:</li>
                  <li>{{$gift->recipientPhone}}</li>
                </ul>

                <?php if($gift->cardNumber!="" && $gift->pinCode!="") { ?>
                 
                  <?php 
                  $cardnumber=json_decode($gift->cardNumber);
                  $pin=json_decode($gift->pinCode);
                  foreach($cardnumber as $key=>$card)
                  {
                  ?>
                  <ul>
                  <li>CardNumber:</li>
                  <li>{{$card}}</li>
                </ul>

                <ul>
                  <li>PinCode:</li>
                  <li>{{$pin[$key]}}</li>
                </ul>


                  <?php } } ?>

                <?php if($gift->cardNumber=="" && $gift->pinCode=="") { ?>
                <div class="giftcar-submit-btn">
                <?php if($gift->r_trans_id!=0) { ?> 
                <a href="javascript:void(0)" onclick="redeem('{{base64_encode(base64_encode($gift->r_trans_id))}}','{{$key}}')" class="btn btn-submit">Redeem Now</a>
                <?php }else{  ?>
                <h6>Your Gift Card PIN/NUMBER will be generate after admin approval.</h6>
                <?php } ?>
                </div>
                <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
      <?php } ?>
      <?php }else{ 
      echo "Purchased Gift Card Not Found !";
       } ?>
      </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>


<style>
.pop1 .modal-dialog {
    max-width: 598px;
}
.modal-dialog {
    margin: 10px auto !important;
    padding: 0 10px;
}
.modal-content {
    border-radius: 15px;
}
.popup-form {
    padding: 20px 40px;
}
.pop-logo {
    text-align: center;
    margin-bottom: 30px;
}
.popup-info-data {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 500;
}
.popup-info-data .label-mini {
    color: #1deb8d;
}
.form-btns-pop {
    text-align: center;
}
</style>


<div class="modal" id="basicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="basicModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                        <div class="popup-form">
                            <div class="pop-logo">
                            <img src="{{HTTP_PATH}}/public/img/dafribank-logo-mobile.svg">
                        </div>
                           
                           <div class="popup-info" id="rmcode">
                              
                           </div>
                    </div>
                </div>
            </div>
        </div>


<script>

function redeem(code,ky)
{
  $.ajax({
  url: "{!! HTTP_PATH !!}/getGiftCardRedeem",
  type: "POST",
  data: {'code': code, _token: '{{csrf_token()}}'},
  beforeSend: function () {
  $('.modal-backdrop').hide();  
  $('#exampleModal'+ky).hide();  
  $('#loaderID').css("display", "flex");
  },
  success: function (result) {
  $('#loaderID').css("display", "none");
  if(result!=0)
  {
  $('#rmcode').html(result);
  $('#basicModal').modal('show');
  }
  else{
    $('#error_message').html(' Invalid Transaction Id');
    $('#error-alert-Modal').modal('show');  
  }
  }
});

}

</script>


<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
{{ HTML::script('public/assets/js/intlTelInput.js')}}
@endsection
