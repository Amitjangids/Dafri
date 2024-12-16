@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                {{ Form::open(array('method' => 'post', 'id' => 'withdrawReqstForm', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit();')) }}
                <div class="col-sm-6">
                    <div class="heading-section wth-head">
                        <div class="ee er_msg" style="width:100%">@include('elements.errorSuccessMessage')</div>
                        <h5>Money Out</h5>
                    </div>
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format(floor($recordInfo->wallet_amount*100)/100,2,'.',',')}}</h2>
                            <h6>{{$recordInfo->gender}} @if($recordInfo->user_type == 'Personal')
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
                            <h5>Amount</h5>
                        </div>
                        <div class="drop-text-field">
                            <input type="text" name="withdrawAmnt" placeholder="Enter amount" min="10" autocomplete="OFF" onkeypress='return validateFloatKeyPress(this, event);'>
                            <div class="withdraw_currency currency_all">{{$recordInfo->currency}}</div>
                        </div>
                        <div class="drop-text-field rn" id="remark_option" style="display: none;">
                            <textarea name="remark" id="remark" placeholder="Payout Instructions:" style="resize: none;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="method-box frnt-withdrwal">
                    <div class="heading-section wth-head">
                        <h5>Payment Type</h5>
                    </div>
                    <div class="inner-mathod-box active-hover">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-6" name="radio" type="radio" value="manual_withdraw" checked onclick="checkRadio('radio-6');">
                                <label for="radio-6" class="radio-label"></label>
                            </div> 
                            @if($recordInfo->country == 'South Africa')
                                <span>Bank Transfer</span>
                            @else
                                <span>Bank Transfer</span>
                            @endif
                            
                        </div>                         
                        <div class="svg-icon ">   
                             {{HTML::image('public/img/front/bank-transfer.svg', SITE_TITLE)}}
                        </div>
<!--                            {{HTML::image('public/img/front/width-draw-manual.svg', SITE_TITLE)}}
</div>-->
                    </div>
<!--                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-1" name="radio" type="radio" value="bank_transfer" checked="" onclick="checkRadio('radio-1');">
                                <label for="radio-1" class="radio-label"></label>
                            </div>

                            <span>Bank Transfer</span>
                        </div>
                        <div class="svg-icon">
                            {{HTML::image('public/img/front/bank-transfer.svg', SITE_TITLE)}}
                        </div>
                    </div>-->
                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-2" name="radio" type="radio" value="debit_credit_card" onclick="checkRadio('radio-2');">
                                <label for="radio-2" class="radio-label"></label>
                            </div>
                            <span>Debit / Credit Cards </span>
                        </div>
                        <!--<div class="svg-icon ">-->
                            <!-- {{HTML::image('public/img/front/visa-logo.png', SITE_TITLE)}} -->
                        <!--</div>-->
                        <span class="Footer_bottomLeftLink__eRrXC"><svg width="28" height="17" fill="none" xmlns="http://www.w3.org/2000/svg" class="Footer_bottomLeftCredit__nFEgv"><circle cx="18.953" cy="8.25" r="8.25" fill="#FE973E"></circle><circle opacity="0.9" cx="8.25" cy="8.25" r="8.25" fill="#E12024"></circle></svg><svg viewBox="0 0 33 11"  fill="none" xmlns="http://www.w3.org/2000/svg" class="Footer_bottomLeftVisa__PvFpr svg-card"><path d="M13.489.62l-1.656 10.305h2.647L16.135.62H13.49zm7.985 4.197c-.925-.456-1.492-.765-1.492-1.232.011-.425.48-.86 1.525-.86.86-.022 1.492.18 1.972.382l.24.107.36-2.157a6.682 6.682 0 00-2.376-.424c-2.614 0-4.455 1.359-4.466 3.303-.022 1.434 1.317 2.23 2.32 2.708 1.024.49 1.373.807 1.373 1.242-.012.669-.829.977-1.59.977-1.057 0-1.624-.159-2.485-.531l-.349-.159-.37 2.241c.622.276 1.766.52 2.953.532 2.778 0 4.586-1.338 4.609-3.41.008-1.137-.698-2.007-2.224-2.719zM30.866.651h-2.049c-.63 0-1.11.182-1.384.83l-3.932 9.444h2.778l.766-2.044h3.108l.397 2.053H33L30.866.65zm-3.05 6.166c.053.005 1.065-3.338 1.065-3.338l.807 3.338h-1.873zM9.62.619L7.027 7.621l-.282-1.38c-.48-1.594-1.983-3.325-3.661-4.185l2.375 8.86h2.8L12.423.62H9.62z"></path><path d="M5.872 1.95C5.67 1.164 5.025.634 4.15.623H.042L0 .813c3.204.79 5.894 3.221 6.766 5.508l-.894-4.37z" fill="#FAA61A"></path></svg></span>
                    </div>

                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-4" name="radio" type="radio" value="agent" onclick="checkRadio('radio-4');">
                                <label for="radio-4" class="radio-label"></label>
                            </div>
                            <span>Bank Agent</span>
                        </div>
                        <div class="svg-icon">
                            {{HTML::image('public/img/front/payment-agent.svg', SITE_TITLE)}}
                        </div>
                    </div>

                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-5" name="radio" type="radio" value="crypto" onclick="checkRadio('radio-5');">
                                <label for="radio-5" class="radio-label"></label>
                            </div>
                            <span>Crypto Currencies</span>
                        </div>
                        <div class="svg-icon">
                            {{HTML::image('public/img/front/CryptoCurrencies.svg', SITE_TITLE)}}
                        </div>
                    </div>
                   <?php /* <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-3" name="radio" type="radio" value="paypal" checked="" onclick="checkRadio('radio-3');">
                                <label for="radio-3" class="radio-label"></label>
                            </div>
                            <span>PayPal</span>
                        </div>                         
                        <div class="svg-icon paypal-logo">   
                            {{HTML::image('public/img/front/paypal-logo.svg', SITE_TITLE)}}</div>
                    </div> */?>
<!--                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-6" name="radio" type="radio" value="manual_withdraw" checked="" onclick="checkRadio('radio-6');">
                                <label for="radio-6" class="radio-label"></label>
                            </div>
                            <span>Manual Bank Withdrawal</span>
                        </div>                         
                        <div class="svg-icon ">   
                            {{HTML::image('public/img/front/width-draw-manual.svg', SITE_TITLE)}}</div>
                    </div>-->
                    <button class="sub-btn button_disable" type="submit">
                        Withdraw
                    </button>
                </div>
                {{ Form::Close() }}
            </div>

        </div>
        <?php
        Session::forget('error_message');
        Session::forget('success_message');
        Session::save();
        ?>
        <script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
        <script type="text/javascript">

$(document).ready(function () {
    $(".inner-mathod-box").click(function () {  
    $('input[name="radio"]').removeAttr("checked");       
    var myid= $(this).find('.radio-card').find('input:radio').attr("id");
    $('#'+myid).attr('checked',true);
    });
   });

            function  checkRadio(id){
                if(id == 'radio-4'){
                    $('#remark_option').show();
                } else{
                    $('#remark').val('');
                    $('#remark_option').hide();
                }
            }
                                        $(document).ready(function () {
                                            $(".active-hover").click(function () {

                                            });

                                            $(".inner-mathod-box").hover(
                                                    function () {
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
                                                if (r.text == '')
                                                    return o.value.length
                                                return o.value.lastIndexOf(r.text)
                                            } else
                                                return o.selectionStart
                                        }

    function disable_submit()
    {
    $('.button_disable').prop('disabled', true);   
    return true;
    }


        </script>
        @endsection