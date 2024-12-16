@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-6 mob-big">
                    <div class="heading-section">
                        <h5>Account</h5>
                    </div>
                   
                    
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h2>
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
                    
                </div>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="account-list">
                                <a href="{{URL::to('auth/notifications')}}">
                                    <h4>Notifications</h4>
                                    {{HTML::image('public/img/front/notifications.svg', SITE_TITLE)}}
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-list">
                                <a href="{{URL::to('auth/transactions')}}">
                                    <h4>Transaction history</h4>
                                    {{HTML::image('public/img/front/transaction-history.svg', SITE_TITLE)}}
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-list">
                                <a href="{{URL::to('auth/change-pin')}}">
                                    <h4>Change Password</h4>
                                    {{HTML::image('public/img/front/change-login-pin.svg', SITE_TITLE)}}
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-list">
                                <a href="{{URL::to('auth/feedback')}}">
                                    <h4>Feedback</h4>
                                    {{HTML::image('public/img/front/feedback.svg', SITE_TITLE)}}
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-list">
                                <a href="{{URL::to('auth/account-detail')}}">
                                    <h4>Account details</h4>
                                    {{HTML::image('public/img/front/account-details.svg', SITE_TITLE)}}
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-list">
                                <a href="{{URL::to('auth/help')}}"><h4>Help</h4>
                                    {{HTML::image('public/img/front/help.svg', SITE_TITLE)}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@endsection