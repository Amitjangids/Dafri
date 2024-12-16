@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <!--  <div class="col-sm-12 success-page">
                        <div class="heading-section">
                            <h5>Money transfer success!</h5>
                        </div>
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
                        {{HTML::image('public/img/front/success.svg', SITE_TITLE)}}
                        <p>Thank you for banking with us</p>
                        <h6>Transaction ID: {{$transID}}</h6>
                        <h6>Reference ID: {{$refID}}</h6>
                        <h6>
                <?php echo date('F, d Y, H:i A'); ?>
                        </h6>
                    </div>
                -->
                
                <!-- success -->
                <div class="col-sm-12 failed-page text-center suss">
                    <div class="heading-section">
                        <h5>Money Transfer Success!</h5>
                    </div>
                    
                    
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Transaction Amount</span>
                            <h2>{{$recordInfo->currency}} {{number_format($transInfo->amount,2,'.',',')}}</h2>
                            
                        </div>
                    </div>
                    {{HTML::image('public/img/front/success-check.svg', SITE_TITLE)}}
                    <div class="suss-details">
                        <h5>Thank you for banking with us
                        </h5>
                        <div class="suss-details-para">
                            <p>
                                Transaction ID: {{$transID}}</p>
                            <p>
                                Reference ID: {{$refID}}</p>
                            <p><?php echo date('F, d Y, H:i A'); ?></p>
                        </div>
                    </div>
                    <div class="" style="margin-bottom: 10px;">
                        <button type="button" class="btn btn-dark" id="confim_btn"  onclick="location.href='{{HTTP_PATH}}/overview';">OK</button>
                        <button type="button" class="btn btn-dark" id="confim_btnn"  onclick="location.href='{{HTTP_PATH}}/beneficiaryAdd/{{$transInfo->user_id}}/{{$transInfo->receiver_id}}';">Add Beneficiary</button>
                    </div>
                </div>
                
                <div class="col-sm-12 d-none success-page text-center">
                    <div class="heading-section">
                        <h5>Money Transfer Success!</h5>
                    </div>
                    <div class="vcard-wrapper">
                        <div class="vcard gold-vcard">
                            <span>Available balance</span>
                            <h2>USD 15,438.89</h2>
                            <h5>Xolane Ziggy</h5>
                        </div>
                    </div>
                    <div class="wallet-card d-none">
                        <span>Available balance</span>
                        <h1>USD 15,438. <span> 89</span></h1>
                        <div class="card-btm-row">
                            <h6>Xolane Ziggy</h6><img src="images/card-logo.svg">
                        </div>
                    </div>
                    <i class="fas fa-check-circle"></i>
                    <p>Thank you for banking with us</p>
                    <h6>Transaction ID: 5778774994</h6>
                    <h6>Reference ID: 1622147754201082169925</h6>
                    <h6>
                        Sep, 14 2020, 09:03 PM
                    </h6>
                </div>
                <div class="col-sm-12 d-none failed-page text-center">
                    <div class="heading-section">
                        <h5>Money Transfer Failed!</h5>
                    </div>
                    <div class="vcard-wrapper">
                        <div class="vcard gold-vcard">
                            <span>Available balance</span>
                            <h2>USD 15,438.89</h2>
                            <h5>Xolane Ziggy</h5>
                        </div>
                    </div>
                    <div class="wallet-card d-none">
                        <span>Available balance</span>
                        <h1>USD 15,438. <span> 89</span></h1>
                        <div class="card-btm-row">
                            <h6>Xolane Ziggy</h6><img src="images/card-logo.svg">
                        </div>
                    </div>
                    <i class="fas fa-times-circle"></i>
                    <p>Please verify your information and try again.</p>
                </div>
                <div class="col-sm-12  d-none failed-page text-center">
                    <div class="heading-section">
                        <h5>Insufficient funds!</h5>
                    </div>
                    <div class="vcard-wrapper">
                        <div class="vcard gold-vcard">
                            <span>Available balance</span>
                            <h2>USD 15,438.89</h2>
                            <h5>Xolane Ziggy</h5>
                        </div>
                    </div>
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Please verify your information and try again.</p>
                </div>
                <!-- success -->
                <div class="col-sm-12  d-none failed-page text-center suss">
                    <div class="heading-section">
                        <h5>Money Transfer Success!</h5>
                    </div>
                    <div class="vcard-wrapper">
                        <div class="vcard silver-vcard">
                            <span>Available balance</span>
                            <h2>ZAR 42, 975. 19</h2>
                        </div>
                    </div>
                    {{HTML::image('public/img/front/success-check.svg', SITE_TITLE)}}
                    <div class="suss-details">
                        <h5>Thank you for banking with us
                        </h5>
                        <div class="suss-details-para">
                            <p>
                                Transaction ID: 859</p>
                            <p>
                                Reference ID: 162214775420802</p>
                            <p>May 27, 2021, 20:36 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection