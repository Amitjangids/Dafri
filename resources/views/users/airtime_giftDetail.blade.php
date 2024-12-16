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

    .btn-submit
    {
      pointer-events: none;
      opacity: 0.5;
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
      .gift-table-parent ul:after {content: "";position: absolute;top: 40px;left: 0;right: 0;border: 1px dashed rgb(0 0 0 / 38%);border-width: 1px;}
      .gift-table-parent ul:before {content: "";position: absolute;bottom: -10px;left: 0;right: 0;border: 1px dashed rgb(0 0 0 / 38%);border-width: 1px;}
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
      .amount-edited input.amount-input-box {max-width: 55px !important;background: #eee !important;padding: 10px 5px !important; border-radius: 5px;}
      .popup-info-data span#recipEmail {word-break: break-all;}

      @media only screen and (max-width: 1399px) {
       .giftcards-parent .modal-body{padding: 25px;}
       .giftcards-parent .modal-dialog {max-width: 700px !important;}
       .header-top-content h3 {font-size: 26px;margin-bottom: 0;}
       .header-top-content h5 {font-size: 21px;margin-bottom: 0;}
       .header-top-content {margin: 10px 0 30px;}
       .giftcard-bottom-content {margin: 30px 0;}
       .giftcar-submit-btn {margin: 30px 0 0;}
       .giftcard-img span{padding:0 5px;}
      }

      @media only screen and (max-width: 1399px) {
        .header-top-content h3 {font-size: 22px;}
        .header-top-content h5 {font-size: 18px;}
        .header-top-content {margin: 10px 0 20px;}
        .giftcar-submit-btn {margin: 20px 0 0;}
        .giftcar-submit-btn .btn {min-width: 100px;padding: 7px 15px;}

      }

      @media only screen and (max-width: 991px) {
        #page-content-wrapper .row.same-section .col-lg-3 {max-width: 33.33%;flex: 0 0 33.33%;}
        .main-box a{display: inline-block; width: 100%; height: 100%;}
        .main-box-content, .main-box{height: auto !important;}

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
        .quantity-button.quantity-up{border-bottom: none;}
        .quantity-button.quantity-down{bottom: 0;}
      }

      @media only screen and (max-width: 650px) {
        
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
                 if(isset($giftDetail->productId) && $giftDetail->denominationType=="FIXED")
                 {
                 foreach($giftDetail->fixedRecipientToSenderDenominationsMap as $key=>$gift) 
                 {
                 ?>
        <div class="col-lg-3">
          <div class="main-box">
              <a type="button" class="" data-toggle="modal" data-target="#exampleModal{{intval($key)}}">
                  <div class="main-box-img">
                      <img src="{{$giftDetail->logoUrls[0]}}" alt="image">
                  </div>
                  <div class="main-box-content">
                      <h2> {{$giftDetail->productName}} 
                        <!-- {{$giftDetail->country->isoName}}  -->
                      </h2>
                  </div>
                  <div class="main-box-content">
                      <h2> {{$giftDetail->recipientCurrencyCode}} {{$key}} </h2>
                  </div>
              </a>
          </div>
      </div>



<!-- Modal -->
{{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm','id'=>'giftCardForm'.intval($key),'class' => '','[formGroup]'=>'formGroup','url'=>'/giftCardorder')) }}
<div class="modal giftcards-parent fade" id="exampleModal{{intval($key)}}" tabindex="-1" role="dialog" aria-labelledby="exampleModal{{$key}}Label" aria-hidden="true">
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
            <h3>Buy Gift Cards</h3>
            <h5>Pay with your DafriBank balance</h5>
          </div>
          <div class="gift-table-parent">
            <!--<p>Provide your quantity of choice for item:</p>-->
                <ul>
                  <li>
                    <div class="giftcard-heading">
                      <h2>Product</h2>
                    </div>
                    <div class="giftcard-img">
                      <img src="{{$giftDetail->logoUrls[0]}}" alt="image">
                      <span>{{ucwords(strtolower($giftDetail->productName))}} 
                        <!-- {{$giftDetail->country->isoName}} -->
                      </span>
                    </div>
                  </li>

                  <li>
                     <div class="giftcard-heading">
                      <h2>Amount</h2>
                    </div>
                    <div class="giftcard-bottom">
                      <h2> 
                       <input type="text" readonly class="width-dynamic proba dva" value="{{$giftDetail->recipientCurrencyCode}}" /> 
                      <input type="text" readonly class="width-dynamic proba dva" value="{{$key}}"/> </h2>
                    </div>
                  </li>

                  <li>
                     <div class="giftcard-heading">
                      <h2>Quantity</h2>
                    </div>
                    <div class="giftcard-bottom">
                      <div class="quantity">
                        <input name ="quantity" type="number" min="1" step="1" value="1" readonly>
                      </div>
                      <input type="hidden" class="unit_price" name="unit_price" id="unit_price{{intval($key)}}" value="{{$giftDetail->senderFee+$gift}}" data-val="{{intval($key)}}">

                      <input type="hidden" class="unit_price" id="unit_price_user_currency{{intval($key)}}" value="{{number_format(($giftDetail->senderFee+$gift)*$converstion_rate, 2, '.', '')}}">

                      <input type="hidden" name="senderCurrencyCode" id="senderCurrencyCode{{intval($key)}}" value="{{$giftDetail->senderCurrencyCode}}">

                    </div>
                  </li>

                  <li>
                     <div class="giftcard-heading">
                      <h2>Total Price</h2>
                    </div>
                    <div class="giftcard-bottom">
                      <h2>
                  
                      <input type="hidden" readonly class="width-dynamic proba dva" value="{{$giftDetail->senderCurrencyCode}}">
                      <input type="hidden" name="total_price_zar" id="total_price_zar{{intval($key)}}" readonly class="width-dynamic proba dva" value="{{$giftDetail->senderFee+$gift}}">
                    
                      <input type="text" readonly class="width-dynamic proba dva" value="{{$recordInfo->currency}}">

                      <input type="text" id="total_price_user_currency{{intval($key)}}" readonly class="width-dynamic proba dva" value="{{number_format(($giftDetail->senderFee+$gift)*$converstion_rate, 2, '.', '')}}">
                    
                    </h2>
                    </div>
                  </li>
                </ul>
          </div>

           <input type="hidden" name="product_id" value="{{$giftDetail->productId}}">
           <input type="hidden" name="productName" value="{{$giftDetail->productName}}">
           <input type="hidden" name="productCountryCode" value="{{$giftDetail->country->isoName}}">
           <input type="hidden" name="usd_amount" value="{{$key}}"/>
           <input type="hidden" name="product_image_link" value="{{$giftDetail->logoUrls[0]}}"/>
           <input type="hidden" id="privacy_check{{intval($key)}}" value="0"/>

          <div class="giftcard-bottom-content">
            <ul>
              <li>*eGift voucher is non-refundable/exchange and cannot be exchanged for cash in part or full and is valid for a single transaction only.</li>
              <li>*eGift vouchers cannot be replaced if lost, stolen or damaged.</li>
              <li>*eGift vouchers are valid till the claim-by-date.</li>
              <li>*eGift vouchers only to be used in their specific region.</li>
              <li>*eGift will be under terms and conditions of their brands.</li>
            <!--   <li>For any discrepancy or complains, kindly send your request to: support@reloadly.com</li> -->
            </ul>
            <p>
              <label class="checkbox">
                <input type="checkbox" class="terms_checkbox" data-val="{{intval($key)}}">
                <span>By clicking confirm, you agree to our <a href="{{HTTP_PATH}}/terms-condition" target="_blank">Terms and conditions</a>.
                </span>
              </label>
            </p>
          </div>
          <span class="error" id="gift_error{{intval($key)}}" style="color:red;display:none"></span>
          <div class="giftcar-submit-btn">
            <a href="javascript:void(0)" onclick="check_fees('{{intval($key)}}')" class="btn btn-submit" id="btn-submit{{intval($key)}}">Confirm</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<!-- Modal -->
{{ Form::close() }}
      <?php }  }elseif(isset($giftDetail->productId) && $giftDetail->denominationType=="RANGE"){ ?>
        <?php $key=$giftDetail->minRecipientDenomination;?>
        <div class="col-lg-3">
          <div class="main-box">
          <a type="button" class="" data-toggle="modal" data-target="#exampleModal{{intval($key)}}">
                  <div class="main-box-img">
                      <img src="{{$giftDetail->logoUrls[0]}}" alt="image">
                  </div>
                  <div class="main-box-content">
                      <h2> {{$giftDetail->productName}} {{$giftDetail->country->isoName}} </h2>
                  </div>
                  <div class="main-box-content">
                      <h2> {{$giftDetail->recipientCurrencyCode}} {{$giftDetail->minRecipientDenomination}} - {{$giftDetail->recipientCurrencyCode}} {{$giftDetail->maxRecipientDenomination}}</h2>
                  </div>
              </a>
          </div>
      </div>

<!-- Modal -->
{{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm','id'=>'giftCardForm'.intval($key),'class' => '','[formGroup]'=>'formGroup','url'=>'/giftCardorder')) }}
<div class="modal giftcards-parent fade" id="exampleModal{{intval($key)}}" tabindex="-1" role="dialog" aria-labelledby="exampleModal{{$key}}Label" aria-hidden="true">
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
            <h3>Buy Gift Cards</h3>
            <h5>Pay from your Balance</h5>
          </div>
          <div class="gift-table-parent">
            <p>Provide your quantity of choice for item:</p>
                <ul>
                  <li>
                    <div class="giftcard-heading">
                      <h2>Product</h2>
                    </div>
                    <div class="giftcard-img">
                      <img src="{{$giftDetail->logoUrls[0]}}" alt="image">
                      <span>{{$giftDetail->productName}} {{$giftDetail->country->isoName}}</span>
                    </div>
                  </li>

                  <li>
                     <div class="giftcard-heading">
                      <h2>Amount</h2>
                    </div>
                    <div class="giftcard-bottom">
                      <h2 class="amount-edited"> 
                       <input type="text" readonly class="width-dynamic proba dva" value="{{$giftDetail->recipientCurrencyCode}}" /> 
                      <input type="text" class="amount-input-box" name="usd_amount" class="width-dynamic proba dva" value="{{$key}}"  onkeypress="return validateFloatKeyPress(this,event);" onkeyup="return validatePress(this.value,'{{intval($key)}}');" /> </h2>
                    </div>
                  </li>

                  <li>
                     <div class="giftcard-heading">
                      <h2>Quantity</h2>
                    </div>
                    <div class="giftcard-bottom">
                      <div class="quantity">
                        <input name ="quantity" type="number" min="1" step="1" value="1" max="{{$giftDetail->minRecipientDenomination}}" step="1" value="{{$giftDetail->maxRecipientDenomination}}" readonly>
                      </div>

                      <input type="hidden" class="unit_price" name="unit_price" id="unit_price{{intval($key)}}" value="{{$giftDetail->senderFee+($giftDetail->minSenderDenomination/$giftDetail->minRecipientDenomination)*$giftDetail->minRecipientDenomination}}" data-val="{{intval($key)}}">

                      <input type="hidden" class="unit_price" id="unit_price_user_currency{{intval($key)}}" value="{{number_format(($giftDetail->senderFee+($giftDetail->minSenderDenomination/$giftDetail->minRecipientDenomination)*$giftDetail->minRecipientDenomination)*$converstion_rate, 2, '.', '')}}">

                      <input type="hidden" name="senderCurrencyCode" id="senderCurrencyCode{{intval($key)}}" value="{{$giftDetail->senderCurrencyCode}}">

                    </div>
                  </li>

                  <li>
                     <div class="giftcard-heading">
                      <h2>Total Price</h2>
                    </div>
                    <div class="giftcard-bottom">
                      <h2>
                      <input type="hidden" readonly class="width-dynamic proba dva" value="{{$giftDetail->senderCurrencyCode}}">
                      <input type="hidden" name="total_price_zar" id="total_price_zar{{intval($key)}}" readonly class="width-dynamic proba dva" value="{{$giftDetail->senderFee+($giftDetail->minSenderDenomination/$giftDetail->minRecipientDenomination)*$giftDetail->minRecipientDenomination}}">

                      <input type="text" readonly class="width-dynamic proba dva" value="{{$recordInfo->currency}}">

                      <input type="text" id="total_price_user_currency{{intval($key)}}" readonly class="width-dynamic proba dva" value="{{number_format(($giftDetail->senderFee+($giftDetail->minSenderDenomination/$giftDetail->minRecipientDenomination)*$giftDetail->minRecipientDenomination)*$converstion_rate, 2, '.', '')}}">




                    </h2>
                    </div>
                  </li>
                </ul>
          </div>

          <input type="hidden" name="product_id" value="{{$giftDetail->productId}}">
           <input type="hidden" name="productCountryCode" value="{{$giftDetail->country->isoName}}">
           <input type="hidden" name="productName" value="{{$giftDetail->productName}}">
           <input type="hidden" name="product_image_link" value="{{$giftDetail->logoUrls[0]}}"/>
           <input type="hidden" id="privacy_check{{intval($key)}}" value="0"/>
           
          <div class="giftcard-bottom-content">
            <ul>
              <li>*eGift voucher is non-refundable/exchange and cannot be exchanged for cash in part or full and is valid for a single transaction only.</li>
              <li>*eGift vouchers cannot be replaced if lost, stolen or damaged.</li>
              <li>*eGift vouchers are valid till the claim-by-date.</li>
              <li>*eGift vouchers only to be used in their specific region.</li>
              <li>*eGift will be under terms and conditions of their brands.</li>
              <li>*Please add a price range between minimum to maximum amount</li>
            <!--   <li>For any discrepancy or complains, kindly send your request to: support@reloadly.com</li> -->
            </ul>
            <p>
              <label class="checkbox">
                <input type="checkbox" class="terms_checkbox" data-val="{{intval($key)}}">
                <span>By clicking confirm, you agree to our <a href="{{HTTP_PATH}}/terms-condition" target="_blank">Terms and conditions</a>.
                </span>
              </label>
            </p>
            <span class="error" id="gift_error{{intval($key)}}" style="color:red;display:none"></span>
          </div>
          <div class="giftcar-submit-btn">
            <a href="javascript:void(0)" onclick="check_fees('{{intval($key)}}')" class="btn btn-submit" id="btn-submit{{intval($key)}}">Confirm</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<!-- Modal -->

<script>
function validatePress(zar_price,ky)
 {
   var unit_price_zar='{{number_format($giftDetail->minSenderDenomination/$giftDetail->minRecipientDenomination, 2, ".", ",")}}';
   var fees='{{number_format($giftDetail->senderFee, 2,".", "")}}';
   var qty=$('input[type="number"]').val();
   total_price=parseFloat(unit_price_zar*qty*zar_price)+parseFloat(fees*qty);
   unitprice=parseFloat(unit_price_zar*zar_price)+parseFloat(fees);
   $('#unit_price'+ky).val(unitprice);
   $('#total_price_zar'+ky).val(total_price);
   var conversionRate=parseFloat('{{number_format($converstion_rate, 2,".", "")}}')
   $('#unit_price_user_currency'+ky).val(unitprice*conversionRate);
   $('#total_price_user_currency'+ky).val(total_price*conversionRate);

 }
</script>


                <?php }  ?>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>


<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
{{ HTML::script('public/assets/js/intlTelInput.js')}}
  

<script type="text/javascript">
  jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function() {
      var spinner = jQuery(this),
        input = spinner.find('input[type="number"]'),
        btnUp = spinner.find('.quantity-up'),
        btnDown = spinner.find('.quantity-down'),
        min = input.attr('min'),
        max = input.attr('max');

      btnUp.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue >= max) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue + 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");

        var test=spinner.parent('.giftcard-bottom').find('.unit_price');
        total_price_zar=$('#'+test.attr('id')).val();
        $('#total_price_zar'+test.data('val')).val(total_price_zar*newVal);

        user_currency_price=$('#unit_price_user_currency'+test.data('val')).val();
        $('#total_price_user_currency'+test.data('val')).val(user_currency_price*newVal);
      });

      btnDown.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue <= min) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue - 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
        var test=spinner.parent('.giftcard-bottom').find('.unit_price');
        total_price_zar=$('#'+test.attr('id')).val();
        $('#total_price_zar'+test.data('val')).val(total_price_zar*newVal);

        user_currency_price=$('#unit_price_user_currency'+test.data('val')).val();
        $('#total_price_user_currency'+test.data('val')).val(user_currency_price*newVal);
      });
  });
</script>
<script type="text/javascript">
    $.fn.textWidth = function(text, font) {
        
        if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
        
        $.fn.textWidth.fakeEl.text(text || this.val() || this.text() || this.attr('placeholder')).css('font', font || this.css('font'));
        
        return $.fn.textWidth.fakeEl.width();
    };

    $('.width-dynamic').on('input', function() {
        var inputWidth = $(this).textWidth();
        $(this).css({
            width: inputWidth
        })
    }).trigger('input');


    function inputWidth(elem, minW, maxW) {
        elem = $(this);
        console.log(elem)
    }

    var targetElem = $('.width-dynamic');

    inputWidth(targetElem);


  function check_fees(id)
  {
  var zar_total_price = $('#total_price_zar'+id).val();
  var senderCurrencyCode = $('#senderCurrencyCode'+id).val();
  var gift_type='{{$giftDetail->denominationType}}';
  var is_term=$('#privacy_check'+id).val();
  if(is_term==0)
  {
  $('#gift_error'+id).html('Please accept our terms & conditions');
  $('#gift_error'+id).show();
  return false;
  }

  if(gift_type=='RANGE')
  {
     var usd_amount=parseFloat($('input[name=usd_amount]').val());
     var min_amount=parseFloat('{{$giftDetail->minRecipientDenomination}}');
     var max_amount=parseFloat('{{$giftDetail->maxRecipientDenomination}}');
     if(usd_amount < min_amount)
     {
     $('#gift_error'+id).html('Please enter a price range between {{$giftDetail->recipientCurrencyCode}} {{$giftDetail->minRecipientDenomination}} - {{$giftDetail->recipientCurrencyCode}} {{$giftDetail->maxRecipientDenomination}}'); 
     $('#gift_error'+id).show(); 
     return false;
     }

     if(usd_amount > max_amount)
     {
     $('#gift_error'+id).html('Please enter a price range between {{$giftDetail->recipientCurrencyCode}} {{$giftDetail->minRecipientDenomination}} - {{$giftDetail->recipientCurrencyCode}} {{$giftDetail->maxRecipientDenomination}}'); 
     $('#gift_error'+id).show(); 
     return false;
     }

  }
  $.ajax({
  url: "{!! HTTP_PATH !!}/getGiftCardFee",
  type: "POST",
  data: {'zar_total_price': zar_total_price,'senderCurrencyCode':senderCurrencyCode, _token: '{{csrf_token()}}'},
  success: function (result) 
  {
    res = result.split('###');
    $('#recipName').html(res[0]);
    $('#recipAccNum').html(res[4]);
    $('#recipEmail').html(res[3]);
    var msg="You will be charged "+res[1]+" "+res[2]+" inclusive fee. Please click on submit to proceed.";
    $('#recipAmountTF').html(msg);
    $('#selected_form').val(id);
    $('#exampleModal'+id).hide();
    $('.modal-backdrop').hide();
    $('#basicModal').modal('show');
  }
  }); 
  }
</script>


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
.confrm-btn {
    font-size: 16px;
    background: #000;
    padding: 8px 20px;
    border-radius: 5px;
    text-transform: capitalize;
    color: #fff;
    border: 1px solid #000;
    cursor: pointer;
}
</style>

<div class="modal" id="basicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="basicModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                        <div class="popup-form">
                            <div class="pop-logo">
                            <img src="{{HTTP_PATH}}/public/img/dafribank-logo-mobile.svg">
                        </div>
                           
                           <div class="popup-info">
                               <div class="popup-info-data">
                                   <span class="label-mini">N:</span> <span id="recipName"></span>
                               </div>

                               <div class="popup-info-data">
                                   <span class="label-mini">A:</span> <span id="recipAccNum"></span>
                               </div>

                               <div class="popup-info-data">
                                   <span class="label-mini">E:</span> <span id="recipEmail"></span>
                               </div>

                               <div class="popup-info-data">
                               <span id="recipAmountTF"></span>
                               </div>

                               <input type="hidden" name="selected_form" id="selected_form">

                           </div>
                        <div class="form-btns-pop">
                            <button type="button" class="confrm-btn btn btn-default button_disable" onclick="btn_disable()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script>
function btn_disable()
{  
$('.button_disable').prop('disabled',true); 
var form_id=$('#selected_form').val();   
$('#giftCardForm'+form_id).submit();
}

function validateFloatKeyPress(el, evt) {
     var charCode = (evt.which) ? evt.which : event.keyCode;
     var number = el.value.split('.');
     if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
         return false;
     }
     //just one dot
     if (number.length > 1 && charCode == 46) {
         return false;
     }
     //get the carat position
     var caratPos = getSelectionStart(el);
     var dotPos = el.value.indexOf(".");
     if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1)) {
         return false;
     }
     return true;
 }

 function getSelectionStart(o) {
     if (o.createTextRange) {
         var r = document.selection.createRange().duplicate()
         r.moveEnd('character', o.value.length)
         if (r.text == '')
             return o.value.length
         return o.value.lastIndexOf(r.text)
     } else
         return o.selectionStart
 }

 
 $('.terms_checkbox').change(function() {
 var id=$(this).data('val');  
 if($(this).is(':checked'))
 {
  $('#btn-submit'+id).css('pointer-events','all');
  $('#btn-submit'+id).css('opacity','1');
  $('#privacy_check'+id).val('1');
 }
 else{
  $('#btn-submit'+id).css('pointer-events','none');
  $('#btn-submit'+id).css('opacity','0.5');
  $('#privacy_check'+id).val('0');
 }

});

</script>


@endsection
