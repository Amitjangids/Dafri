@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <?php /*<div class="col-sm-12 success-page">
                    <div class="heading-section">
                        <h5>Add money to your wallet balance</h5>
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
                    <p>{{$amount}} added to your wallet successfully</p>
                    <h6>Transaction ID: {{$transID}}</h6>
                    <h6>Reference ID: {{$refID}}</h6>
                    <h6>
                        <?php echo date('F, d Y, h:i A'); ?>
                    </h6>
                </div> */?>
                
                <div class="col-sm-12 failed-page text-center suss">
                    <div class="heading-section">
                        <h5>Money Transfer Success!</h5>
                    </div>                   
                    
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h2>
                            
                        </div>
                    </div>
                    {{HTML::image('public/img/front/success-check.svg', SITE_TITLE)}}
                    <div class="suss-details">
                        <h5>{{$amount}} added to your wallet successfully
                        </h5>
                        <div class="suss-details-para">
                            <p>
                                Transaction ID: {{$transID}}</p>
                            <p>
                                Reference ID: {{$refID}}</p>
                            <p><?php echo date('F, d Y, h:i A'); ?></p>
                        </div>
                    </div>
                    
                    <div class="" style="margin-bottom: 10px;">
                        <button type="button" class="btn btn-dark" id="confim_btn"  onclick="location.href='{{HTTP_PATH}}/overview';">OK</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection