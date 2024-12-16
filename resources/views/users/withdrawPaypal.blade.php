@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                <div class="col-sm-6">
                    <div class="heading-section wth-head">
                        <h5>Withdraw with Paypal</h5>
                    </div>
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h2>
                            <h6>@if($recordInfo->user_type == 'Personal')
                                {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}
                                @elseif($recordInfo->user_type == 'Business')
                                {{ucwords($recordInfo->business_name)}}
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                                {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                                {{ucwords($recordInfo->business_name)}}
                                @endif</h6>
                        </div>
                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>
                    <div class="deposit-amt">
                        {{ Form::open(array('method' => 'post', 'id' => 'withdrawPaypalForm','onsubmit'=>'return disable_submit();')) }}
                        <div class="heading-section">
                            <h5>Amount</h5>
                        </div>
                        <div class="drop-text-field">
                            <input type="text" name="paypal_amount" id="paypal_amount" placeholder="Enter amount" value="{{base64_decode($pp_withdraw_amount)}}" readonly>
                            <div class="withdraw_currency">ZAR</div>
                        </div>
                        <div class="drop-text-field">
                            <input type="text" name="paypal_email" id="paypal_email" placeholder="Enter email" value="{{$recordInfo->email}}">

                        </div>
                        <div class="drop-text-field">
                            <button class="sub-btn button_disable" type="submit">
                                Proceed
                            </button>
                        </div>

                    </div>
                </div>
                <div class="method-box">
                    <div class="heading-section wth-head">
                        <h5>Withdrawal Method</h5>
                    </div>
                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-1" name="radio" type="radio" value="bank_transfer" disabled="disabled">
                                <label for="radio-1" class="radio-label"></label>
                            </div>

                            <span>Bank Transfer</span>
                        </div>
                        <div class="svg-icon">
                            {{HTML::image('public/img/front/bank-transfer.svg', SITE_TITLE)}}
                        </div>
                    </div>
                    <div class="inner-mathod-box active-hover">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-2" name="radio" type="radio" value="debit_credit_card" disabled="disabled">
                                <label for="radio-2" class="radio-label"></label>
                            </div>
                            <span>Debit / Credit Cards </span>
                        </div>
                        {{HTML::image('public/img/front/visa.png', SITE_TITLE)}}
                    </div>

                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-4" name="radio" type="radio" value="agent" disabled="disabled">
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
                                <input id="radio-5" name="radio" type="radio" value="agent"  disabled="disabled">
                                <label for="radio-5" class="radio-label"></label>
                            </div>
                            <span>Crypto Currencies</span>
                        </div>
                        <div class="svg-icon">
                            {{HTML::image('public/img/front/CryptoCurrencies.svg', SITE_TITLE)}}
                        </div>
                    </div>
                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-3" name="radio" type="radio" value="paypal" checked="">
                                <label for="radio-3" class="radio-label"></label>
                            </div>
                            <span>PayPal</span>
                        </div>                            {{HTML::image('public/img/front/iconfinder__Paypal-39_1156727.png', SITE_TITLE)}}
                    </div>
<!--                    <div class="inner-mathod-box">
                        <div class="math-select">
                            <div class="radio-card">
                                <input id="radio-6" name="radio" type="radio" value="manual_withdraw"  disabled="disabled">
                                <label for="radio-6" class="radio-label"></label>
                            </div>
                            <span>Manual Bank Withdrawal</span>
                        </div>                         
                        <div class="svg-icon ">   
                            {{HTML::image('public/img/front/width-draw-manual.svg', SITE_TITLE)}}</div>
                    </div>-->
<!--                    <button class="sub-btn" type="submit">
                        Withdraw
                    </button>-->
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script type="text/javascript">
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


                                function disable_submit()
                                {
                                $('.button_disable').prop('disabled', true);   
                                return true;
                                }


</script>
@endsection