@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
 @include('elements.side_menu')        
 <!-- Page Content -->
<div id="page-content-wrapper">
            <div class="head-bar">
                <div class="wrapper2">
                    <button class="btn btn-mobile" id="menu-toggle"><span class="navbar-toggler-icon">{{HTML::image('public/img/front/bars.svg', SITE_TITLE)}}</span></button>
                    <div class="row">
                    <div class="col-sm-6">
                    <div class="search-head">
                    {{HTML::image('public/img/front/search.svg', SITE_TITLE)}}<input type="text" name="search_q" id="search_q" placeholder="Search" onchange="goTo(this.value);">
                    </div>
                    </div>
                        <div class="col-sm-6">
                            <div class="noti-right-bar">
                                <div class="profile-top-bar">
                                    <a href="{{URL::to('auth/my-account')}}">
                                        <div class="pro-top-bar-img">
                                        @if(isset($recordInfo->profile_image))
                        {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE)}}
						@else
                                        {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
                                        @endif
                                        </div>
                                       @if($recordInfo->user_type == 'Personal')
                                    <span>Hi, {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</span>
                                    @elseif($recordInfo->user_type == 'Business')
                                    <span>Hi, {{ucwords($recordInfo->business_name)}}</span>
									@elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
									<span>Hi, {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</span>
									@elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
									<span>Hi, {{ucwords($recordInfo->business_name)}}</span>
                                    @endif
                                    </a>
                                </div>
                                <a href="{{URL::to('auth/notifications')}}">{{HTML::image('public/img/front/bell.svg', SITE_TITLE)}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrapper2">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="heading-section wth-head">
                            <h5>Withdraw with Paypal</h5>
                        </div>
                        <div class="cards-box">
						@php
					 $card_class = getUserCardType($recordInfo->account_category);
					@endphp
                            <div class="wallet-card {{$card_class}}">
                                <span>Available balance</span>
                                <h1>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h1>
                                <div class="card-btm-row">
                                 @if($recordInfo->user_type == 'Personal')
                            <h6>{{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</h6>
                            @elseif($recordInfo->user_type == 'Business')
                            <h6>{{ucwords($recordInfo->business_name)}}</h6>
							@elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
							<h6>{{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</h6>
							@elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
							<h6>{{ucwords($recordInfo->business_name)}}</h6>
                            @endif
                            {{HTML::image('public/img/front/card-logo.svg', SITE_TITLE)}}
                                </div>
                            </div>
                        </div>
                        <div class="deposit-amt">
						{{ Form::open(array('method' => 'post', 'id' => 'withdrawPaypalForm', 'class' => '')) }}
                            <div class="heading-section">
                                <h5>Amount</h5>
                            </div>
                            <div class="drop-text-field">
                                <input type="text" name="paypal_amount" id="paypal_amount" placeholder="Enter amount" value="{{base64_decode($pp_withdraw_amount)}}">
                                <div class="withdraw_currency">ZAR</div>
                            </div>
                              <div class="drop-text-field">
                                <input type="text" name="paypal_email" id="paypal_email" placeholder="Enter email" value="{{$recordInfo->email}}">
                                
                            </div>
                            <div class="drop-text-field">
                            <button class="sub-btn" type="submit">
                            Proceed
                        </button>
                    </div>
					{{ Form::close() }}
                        </div>
                    </div>
                    <div class="method-box">
                        <div class="heading-section wth-head">
                            <h5>Withdrawal Method</h5>
                        </div>
                        <div class="inner-mathod-box">
                            <div class="math-select">
                                <div class="radio-card">
                                    <input id="radio-1" name="radio" type="radio" value="bank_transfer" disabled>
                                    <label for="radio-1" class="radio-label"></label>
                                </div>
                                <span>Bank Transfer</span>
                            </div>
                            {{HTML::image('public/img/front/bank-transfer.png', SITE_TITLE)}}
                        </div>
                        <div class="inner-mathod-box active-hover">
                            <div class="math-select">
                                <div class="radio-card">
                                    <input id="radio-2" name="radio" type="radio" value="debit_credit_card" disabled>
                                    <label for="radio-2" class="radio-label"></label>
                                </div>
                                <span>Debit / Credit Cards </span>
                            </div>
                            {{HTML::image('public/img/front/visa.png', SITE_TITLE)}}
                        </div>
                        <div class="inner-mathod-box">
                            <div class="math-select">
                                <div class="radio-card">
                                <input id="radio-3" name="radio" type="radio" value="paypal" checked="true">
                                    <label for="radio-3" class="radio-label"></label>
                                </div>
                                <span>Paypal</span>
                            </div>                            {{HTML::image('public/img/front/iconfinder__Paypal-39_1156727.png', SITE_TITLE)}}
                        </div>
                        <div class="inner-mathod-box">
                            <div class="math-select">
                                <div class="radio-card">
                                    <input id="radio-4" name="radio" type="radio" value="agent" disabled>
                                    <label for="radio-4" class="radio-label"></label>
                                </div>
                                <span>Payment Agent</span>
                            </div>
                            {{HTML::image('public/img/front/Icon ionic-ios-contacts.png', SITE_TITLE)}}
                        </div>
                       <!-- <button class="sub-btn" type="submit">
                            Withdraw
                        </button> -->
                    </div>
                </div>
				</div>
				</div>
</div>
<script src="/dafri/public/assets/js/front/top_search.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$(".active-hover").click(function() {
});

$(".inner-mathod-box").hover(
function() {
$(".inner-mathod-box").removeClass("active-hover");
$(this).addClass("active-hover");
}
);
});
</script>
@endsection