@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="">
                <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                {{ Form::open(array('method' => 'post', 'id' => 'withdrawAmntForm', 'class' => '')) }}
                <div class="col-sm-12">
                    <div class="heading-section wth-head">
                        <h5>Withdrawal</h5>
                    </div>
                    @php
                    $card_class = getUserCardType($recordInfo->account_category);
                    @endphp
                    <div class="cards-box">
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
                       <?php /* <div class="wallet-card">
                            <a href="{{URL::to('auth/agent-list')}}">
                                {{HTML::image('public/img/front/agents.svg', SITE_TITLE)}}
                                <h1>DafriBank Agents</h1>
                            </a>
                        </div> */?>
                    </div>
                    <div class="deposit-amt">
                        <div class="heading-section">
                            <h5>Account</h5>
                        </div>
                        <div class="drop-text-field">
                            <select class="form-control" name="account" id="account">
                                @if(Count($accounts) > 0)
                                @foreach ($accounts as $account)
                                <option value="{{$account->id}}">{{$account->account_number." (".$account->bank_name.")"}}</option>
                                @endforeach
                                @else
                                <option value="-1">Add Account</option>
                                @endif
                            </select>
                        </div>
                        <div class="heading-section">
                            <h5>Amount</h5>
                        </div>
                        <div class="drop-text-field">
                            <input type="text" name="amount" id="amount" value="{{base64_decode(Session::get('withdrawAmnt6_4'))}}" placeholder="Enter amount" readonly>
                            <div class="withdraw_currency">{{$recordInfo->currency}}</div>
<!-- <select class="dropdown-arrow">
<option value="{{$recordInfo->currency}}">{{$recordInfo->currency}}</option>
</select> -->
                        </div>
                    </div>
                    <div class="withdrawal-opt">

                        <!--    <div class="radio-card">
                            <input id="radio-1" name="radio" type="radio" checked="">
                            <label for="radio-1" class="radio-label">Withdrawal by submitting withdrawal request through DafriBank account</label>
                        </div>
                             <div class="radio-card">
                            <input id="radio-2" name="radio" type="radio" checked="">
                            <label for="radio-2" class="radio-label">Withdrawal by DafriBank agents</label>
                        </div> -->
                    </div>

                    <div class="withdraw_buttons" style="margin-bottom:20px;">
                        <button class="sub-btn w-btn" type="submit">Withdraw</button>
                        <!--<button class="sub-btn w-btn" onclick="location.href = '<?php echo HTTP_PATH; ?>/auth/add-bank-account';" type="button">Add New Account</button>-->
                    </div>	
                    <div class="withdraw_buttons" style="margin-bottom:60px;">
                        <!--<button class="sub-btn w-btn" type="submit">Withdraw</button>-->
                        <button class="sub-btn w-btn" onclick="location.href = '<?php echo HTTP_PATH; ?>/auth/add-bank-account';" type="button">Add New Account</button>
                    </div>	
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script>
                            function choosePaymentMethod(paymentMethod)
                            {
                                document.getElementById('payment_method').value = paymentMethod;
                                document.getElementById('paymentMethod').value = paymentMethod;
                                document.getElementById('paymentMethod').innerHTML = paymentMethod + ' <img src="<?php echo HTTP_PATH; ?>/public/img/front/arrow-down.svg" alt="DafriBank">';
                                document.getElementById('drpDwnDiv').style.display = 'none';
                            }
</script>
@endsection