@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="after-submit-popup">
                        <button type="button" class="btn btn-default" data-dismiss="modal">X</button>
                        <h4>
                            Success!
                        </h4>
                        <div class="success-msg">
                            {{HTML::image('public/img/front/success-icon.svg', SITE_TITLE)}}
                        </div>
                    </div>
                    <div class="suss-msg">
                        <p>Manual Deposit Request Saved Successfully.
                            Our finance team will check
                            and get back to you soon </p>
                    </div>
                </div>
            </div>
        </div>
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                {{ Form::open(array('method' => 'post', 'id' => 'addFundFrm', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit(this);')) }}
                <div class="col-sm-6 border-right mob-big">
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format(floor($recordInfo->wallet_amount*100)/100,2,'.',',')}}</h2>
                            <h6>@if($recordInfo->user_type == 'Personal')
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Business')
                                @include('elements.business_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                                @include('elements.business_short_name')
                                @endif</h6>
                        </div>
                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>
                    <div class="deposit-amt">
                        <div class="heading-section">
                            <h5>Deposit amount</h5>
                        </div>
                        <div class="drop-text-field">
                            <input type="text" name="tranAmnt" id="tranAmnt" placeholder="Enter amount" autocomplete="OFF" onkeypress='return validateFloatKeyPress(this,event);'>
                            <div class="withdraw_currency currency_all">{{$recordInfo->currency}}</div>
                        </div>
                    </div>
                </div>
                <div class="method-box">
                    <div class="heading-section wth-head">
                        <h5>Payment Method</h5>
                    </div>
                 
                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-7" name="payment_method" type="radio" value="dafri_me" checked>
                                <label for="radio-7" class="radio-label"></label>
                            </div>
                            <span>ePay Me</span>
                        </div>
                        <div class="">
                        <img src="{{HTTP_PATH}}/public/img/epayblack-icon-img.svg" class="dafrme-icon">
                        </div>
                    </div>
<!--                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-4" name="payment_method" type="radio" value="paypal">
                                <label for="radio-4" class="radio-label"></label>
                            </div>
                            <span>PayPal</span>
                        </div>
<div class="svg-icon paypal-logo">
                         {{HTML::image('public/img/front/paypal-logo.svg', SITE_TITLE)}}
                     </div>
                    </div>-->
                    <!-- <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-7" name="payment_method" type="radio" value="manual_deposit">
                                <label for="radio-7" class="radio-label"></label>
                            </div>
                            <span>Manual deposit</span>
                        </div>
                        <div class="svg-icon">
                            {{HTML::image('public/img/front/ManualDeposit.svg', SITE_TITLE)}}
                        </div>
                    </div> -->
                    <button class="sub-btn button_disable" type="submit">
                        Add Fund
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(".active-hover").click(function() {});

    $(".inner-mathod-box").hover(
        function() {
            $(".inner-mathod-box").removeClass("active-hover");
            $(this).addClass("active-hover");
        }
    );

});

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
        if (r.text == '') return o.value.length
        return o.value.lastIndexOf(r.text)
    } else return o.selectionStart
}

    $(document).ready(function () {
    $(".inner-mathod-box").click(function () {  
    $('input[name="payment_method"]').removeAttr("checked");       
    var myid= $(this).find('.radio-card').find('input:radio').attr("id");
    $('#'+myid).attr('checked',true);
    });
   });

</script>



<style>
.headmodel {
    padding: 30px;
    border-radius: 25px;
    text-align: center;
}
        </style>   


        <div class="modal x-dialog fade" id="debit-credit-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content headmodel">
            <div class="modal-body ">
            <i class="fas fa-exclamation-triangle fa-4x"></i>
                <br><br>
                <p><b>Note :</b> Please remember to include your DafriBank email or account number on the Remark !</p>
                <ul class="frnt-logout btn-list">
                    <li class="">
                    <button type="button"  id="myButton" class="btn btn-dark" onclick="open_payment_tab()">Continue</button></li>
                    <li class=""><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
            </div>
        </div>


        <div class="modal x-dialog fade" id="dba-credit-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content headmodel">
            <div class="modal-body ">
            <i class="fas fa-exclamation-triangle fa-4x"></i>
                <br><br>
                <p><b>Note :</b> Please remember to include your DBA payout address on Remark !</p>
                <ul class="frnt-logout btn-list">
                    <li class="">
                    <button type="button"  id="myButton" class="btn btn-dark" onclick="open_payment_tab()">Continue</button></li>
                    <li class=""><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
            </div>
        </div>

        {{ Form::open(array('url' => 'stripe', 'method' => 'post', 'id' => 'payment-form', 'class' => 'form-section require-validation','data-cc-on-file'=>"false","data-stripe-publishable-key"=>env('STRIPE_KEY')))}}  
                        @csrf
                        <input type="hidden" name="sendername" class="required sendername" value="{{$recordInfo->name}}" placeholder="Full Name">
                        <input type="hidden" name="amount" id="card_amount">
                        <input type="hidden" name="currency" value="{{$recordInfo->currency}}">
                        <input type="hidden" name="senderemail" class="required senderemail" value="{{$recordInfo->email}}" placeholder="Email">
                        <input type="hidden" name="cardnumber" id="card_number" class="card-number" value="4242424242424242" placeholder="Credit Card Number">
                        <input type="hidden" name="zipcode" value="302012" class="required number" minlength="6" maxlength="6" placeholder="ZIP Code">
                        <div class="d-flex">
                            <input type="hidden" name="month" id="expiry_month" value="05" class="required number month" minlength="1" maxlength="2" placeholder="MM">
                            <input type="hidden" name="year" id="expiry_year" value="2023" class="required number year" minlength="4" maxlength="4" placeholder="YYYY">
                        </div>
                        <input type="hidden" name="email" class="required" value="{{$recordInfo->email}}" placeholder="">
                        <input type="hidden" id="cvv" name="cvv" class="required number cvv" value="123" minlength="3" maxlength="3" placeholder="CVV Code">
                    </form>


<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

function paywithstripe() {
                $('#payment-form').serialize();
                $('#loaderID').show();
                $.ajax({
                url: "{!! HTTP_PATH !!}/stripe",
                        type: "POST",
                        data: $('#payment-form').serialize(),
                       
                        success: function (result) {
                             console.log(result);
                            // if(result == '1'){
                            //     $('#payloader').hide();
                            //     alert('Booking payment completed successfully.');
                            //     window.location = "{!! HTTP_PATH !!}/user-dashboard";
                            // }
                            // if(result == '0'){
                            //    // $('#payloader').hide();
                            //    window.location = "{!! HTTP_PATH !!}/auth/add-fund";
                            // } else{
                            //     $('#loaderID').hide();
                            //    // console.log(result);
                            //     function disableBack() {
                            //            window.location.reload() 
                            //         }
                            //         window.onload = disableBack();
                            //         window.onpageshow = function(e) {
                            //             if (e.persisted)
                            //                 disableBack();
                            //         }
                            //     window.location = result;
                            // }
                        
                        }
                });
            }

    function disable_submit(x)
    {
    var amt=x.tranAmnt.value;
    var payment_method=x.payment_method.value
    if(amt!="" && payment_method=='card_transfer')
    {  
       
        $.ajax({
        type: "POST",
        url: "<?php echo HTTP_PATH;?>/auth/check-card-payment",
        data:{tranAmnt:amt, "_token": "{{ csrf_token() }}"},
        //dataType: "text",
        success: function(resultData){
        if(resultData!=1)
        {
        $('#error_message').html(resultData);
        $('#error-alert-Modal').modal('show');  
        }
        else{
        $('#card_amount').val(amt);
        paywithstripe(); 
        }
        }
        });
        return false;
    }
    $('.button_disable').prop('disabled', true);   
    return true;
    }





</script>


@endsection
