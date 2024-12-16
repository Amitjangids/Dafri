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
                            {{HTML::image('public/img/front/search.svg', SITE_TITLE)}}<input type="text" name="" placeholder="Search">
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
                                    <span>Hi, {{ucwords($recordInfo->director_name)}}</span>
									@elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
									<span>Hi, {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</span>
									@elseif($recordInfo->user_type == 'Agent' && $recordInfo->director_name != "")
									<span>Hi, {{ucwords($recordInfo->director_name)}}</span>
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
			{{ Form::open(array('method' => 'post', 'id' => 'addFundForm', 'class' => '')) }}
                <div class="row">
                    <div class="col-sm-6 border-right mob-big">
                        <div class="heading-section">
                            <h5>Add funds</h5>
                        </div>
						<div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
						@php
						 $card_class = getUserCardType($recordInfo->account_category);
						@endphp
                        <div class="wallet-card {{$card_class}}">
                            <span>Available balance</span>
                            <h1>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h1>
                            <div class="card-btm-row">
							@if($recordInfo->user_type == 'Personal')
                            <h6>{{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</h6>
							@else
							<h6>{{ucwords($recordInfo->director_name)}}</h6>
							@endif
							{{HTML::image('public/img/front/card-logo.svg', SITE_TITLE)}}
                            </div>
                        </div>
                        <div class="deposit-amt">
                            <div class="heading-section">
                                <h5>Deposit amount</h5>
                            </div>
                            <div class="drop-text-field">
                            {{Form::text('tranAmnt', null, ['class'=>'form-control required number', 'placeholder'=>'Enter amount', 'id'=> 'tranAmnt', 'autocomplete'=>'OFF'])}}
							<?php $currencyList = array($recordInfo->currency=>$recordInfo->currency);?>
							{{Form::select('transCurrnc', $currencyList,null, ['class' => 'dropdown-arrow required', 'id' => 'transCurrnc'])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 pad-l-50 mob-big">
                        <div class="heading-section">
                            <h5>Choose payment method</h5>
                        </div>
                        <div class="save-cards">
                            <h6>Saved cards</h6>
                            <div class="row ">
								@foreach($cards as $key=>$card)
                                <div class="col-sm-6">
                                    <div class="card-ticket">
                                        <div class="card-name">
                                            <h3>{{$card->card_name}}</h3>
                                            <span>{{'xxxx-xxxx-xxxx-'.substr(trim($card->card_number),12,4)}}</span>
                                        </div>
                                        <div class="radio-card">
                                            <input id="radio_{{$key}}" name="card" value="{{$card->id}}" type="radio" checked>
                                            <label for="radio-1" class="radio-label"></label>
                                        </div>
                                    </div>
                                </div>
								@endforeach
                                
                            </div>
                        </div>
                        <span class="or">
                            OR
                        </span>
                        <div class="choose-payment-method wrap">
                            <button id="paymentMethod" name="paymentMethod" value="-1" type="button" class="but">Choose payment method {{HTML::image('public/img/front/arrow-down.svg', SITE_TITLE)}}</button>
                            <div id="drpDwnDiv" class="content">
                                <div onclick="choosePaymentMethod('Debit Card');" class="pay-met-box">Debit Card</div>
                                <div onclick="choosePaymentMethod('Credit Card');" class="pay-met-box">Credit Card</div>
                                <div onclick="choosePaymentMethod('Bank Account');" class="pay-met-box">Bank Account</div>
                                <div onclick="choosePaymentMethod('OZOW EFT');" class="pay-met-box">OZOW EFT</div>
                            </div>
                        </div>
						<input type="hidden" name="payment_method" id="payment_method" value="-1">
                            <button class="sub-btn" type="submit">
                                Proceed
                            </button>
                        
                    </div>
                </div>
				 {{ Form::close() }}
            </div>
        </div>
</div>
<script>
function choosePaymentMethod(paymentMethod)
{
  document.getElementById('payment_method').value = paymentMethod;
  document.getElementById('paymentMethod').value = paymentMethod;
  document.getElementById('paymentMethod').innerHTML = paymentMethod+' <img src="<?php echo HTTP_PATH; ?>/public/img/front/arrow-down.svg" alt="DafriBank">';
  document.getElementById('drpDwnDiv').style.display = 'none';
}
</script>
@endsection