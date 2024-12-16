@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    <!-- Button trigger modal -->
    <!-- Modal -->
    <!--    <div class="modal x-alert fade" id="success-alert-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-alert-lg">
                <div class="modal-content">
                    <div class="modal-body ">
                        <i class="fas fa-check-circle"></i>
                        <h4>Payment Successful</h4>
                        <button type="button" class="btn btn-dark">click here</button>
                    </div>
                    <div class="modal-body ">
                        <i class="fas fa-times-circle"></i>
                        <h4>Transaction failed</h4>
                        <button type="button" class="btn btn-dark">ok</button>
                    </div>
                </div>
            </div>
        </div>-->
    <!-- Modal -->
    <!--    <div class="modal x-dialog fade" id="dialog-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body ">
                        {{HTML::image('public/img/front/dafribank-logo-black.svg', SITE_TITLE)}}
                        <p><strong>Dear Andrew Scotch,</strong></p>
                        <p>You are about to send <strong>ZAR 200</strong> to a recipient without a DafriBank Digital account. </p>
                        <p>The transaction will remain pending until the recipient opens a DafriBank Account with the above email address to accept the funds.</p>
                        <p>The funds will be automatically reversed to your DafriBank Account should the recipient fail to accept it within the next 30 days.</p>
                        <ul class="list-inline btn-list">
                            <li class="list-inline-item"><button type="button" class="btn btn-dark">Confirm</button></li>
                            <li class="list-inline-item"><button type="button" class="btn btn-light">Cancel</button></li>
                        </ul>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"><strong>Deposit Address copied successfully</strong></p>
                        <ul class="list-inline mt-1 btn-list">
                            <li class="list-inline-item"><button type="button" class="btn btn-dark">ok</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>-->
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-6 border-right mob-big">
                    <div class="heading-section">
                        <h5>{{$recordInfo->user_type}} account</h5>
                    </div>
                    @php
                    $card_class = getUserCardType($recordInfo->account_category);
                    @endphp
                    <!-- <div class="wallet-card {{$card_class}}">
                    <span>Available balance</span>
                    <h1>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h1>
                    <div class="card-btm-row">
                    @if($recordInfo->user_type == 'Personal')
                    <h6>{{strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)}}</h6>
                    @elseif($recordInfo->user_type == 'Business')
                    <h6>{{strtoupper($recordInfo->business_name)}}</h6>
                    @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                    <h6>{{strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)}}</h6>
                    @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                    <h6>{{strtoupper($recordInfo->business_name)}}</h6>
                    @endif
                    {{HTML::image('public/img/front/card-logo.svg', SITE_TITLE)}}
                    </div>
                    </div> -->
                    <div class="wallet-card d-none">
                        <span>Available balance</span>
                        <h1>USD 15,438.89</h1>
                        <div class="card-btm-row">
                            <h6>Xolane Ziggy</h6><img src="images/card-logo.svg">
                        </div>
                    </div>

                    <!-- golden -->
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h2>
                            <h6>@if($recordInfo->user_type == 'Personal')
                                {{strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)}}
                                @elseif($recordInfo->user_type == 'Business')
                                {{strtoupper($recordInfo->business_name)}}
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                                {{strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)}}
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                                {{strtoupper($recordInfo->business_name)}}
                                @endif</h6>
                        </div>
                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>

                    <!-- silver -->
                    <!--                    <div class="vcard-wrapper d-none">
                                            <div class="vcard silver-vcard">
                                                <span>Available balance</span>
                                                <h2>USD 15,438.89</h2>
                                                <h6>Xolane Ziggy</h6>
                                            </div>
                                            <img class="shadow-bottom" src="public/img/front/vcard-shadow.png">
                                        </div>-->

                    <!-- silver -->
                    <!-- <div class="vcard-wrapper">
                        <div class="vcard black-vcard">
                            <span>Available balance</span>
                            <h2>USD 15,438.89</h2>
                            <h6>Xolane Ziggy</h6>
                        </div>
                        <img class="shadow-bottom" src="public/img/front/vcard-shadow.png">
                    </div> -->
                    <?php /* @if($recordInfo->user_type == 'Business' or ($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")) */ ?>
                    <div class="graph-bar">
                        <div class="heading-section head-small-sec">
                            <h6>Outward</h6><span><strong>{{$recordInfo->currency}} {{number_format($total_expense,2,'.',',')}}</strong> Money out for last week</span>
                        </div>
                        <div id="chart">
                            <ul id="numbers">
                                <li><span>40k</span></li>
                                <li><span>30k</span></li>
                                <li><span>20k</span></li>
                                <li><span>10k</span></li>
                            </ul>
                            <ul id="bars">
                                <li>
                                    @if(isset($expensArr[0]))
                                    <div data-percentage="{{$expensArr[0]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Mon</span>
                                </li>
                                <li>
                                    @if(isset($expensArr[1]))
                                    <div data-percentage="{{$expensArr[1]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Tue</span>
                                </li>
                                <li>
                                    @if(isset($expensArr[2]))
                                    <div data-percentage="{{$expensArr[2]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Wed</span>
                                </li>
                                <li>
                                    @if(isset($expensArr[3]))
                                    <div data-percentage="{{$expensArr[3]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Thu</span>
                                </li>
                                <li>
                                    @if(isset($expensArr[4]))
                                    <div data-percentage="{{$expensArr[4]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Fri</span>
                                </li>
                                <li>
                                    @if(isset($expensArr[5]))
                                    <div data-percentage="{{$expensArr[5]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Sat</span>
                                </li>
                                <li>
                                    @if(isset($expensArr[6]))
                                    <div data-percentage="{{$expensArr[6]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Sun</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="graph-bar">
                        <div class="heading-section head-small-sec">
                            <h6>Inward</h6><span><strong>{{$recordInfo->currency}} {{number_format($total_income,2,'.',',')}}</strong> Money In for last week</span>
                        </div>
                        <div id="chart">
                            <ul id="numbers">
                                <li><span>40k</span></li>
                                <li><span>30k</span></li>
                                <li><span>20k</span></li>
                                <li><span>10k</span></li>
                            </ul>
                            <ul id="bars">
                                <li>
                                    @if(isset($incomeArr[0]))
                                    <div data-percentage="{{$incomeArr[0]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Mon</span>
                                </li>
                                <li>
                                    @if(isset($incomeArr[1]))
                                    <div data-percentage="{{$incomeArr[1]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Tue</span>
                                </li>
                                <li>
                                    @if(isset($incomeArr[2]))
                                    <div data-percentage="{{$incomeArr[2]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif								 
                                    <span>Wed</span>
                                </li>
                                <li>
                                    @if(isset($incomeArr[3]))
                                    <div data-percentage="{{$incomeArr[3]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Thu</span>
                                </li>
                                <li>
                                    @if(isset($incomeArr[4]))
                                    <div data-percentage="{{$incomeArr[4]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Fri</span>
                                </li>
                                <li>
                                    @if(isset($incomeArr[5]))
                                    <div data-percentage="{{$incomeArr[5]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Sat</span>
                                </li>
                                <li>
                                    @if(isset($incomeArr[6]))
                                    <div data-percentage="{{$incomeArr[6]}}" class="bar"></div>
                                    @else
                                    <div data-percentage="0" class="bar"></div>
                                    @endif
                                    <span>Sun</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php /* @endif */ ?>
                </div>
                <div class="col-sm-6 pad-l-50 mob-big">
                    <div class="heading-section">
                        <h5>Quick access</h5>
                    </div>
                    <div class="row quickaccess">
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/add-fund')}}">
                                    {{HTML::image('public/img/front/Deposit-thumb.svg', SITE_TITLE)}}
                                    <span>
                                        Deposit
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/fund-transfer')}}">
                                    {{HTML::image('public/img/front/Fundtransfer-thumb.svg', SITE_TITLE)}}
                                    <span>
                                        Fund
                                        transfer
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/withdraw-request')}}">
                                    {{HTML::image('public/img/front/withdraw-b.svg', SITE_TITLE)}}
                                    <span>
                                        Withdrawal
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('buy-cell-crypto')}}">
                                    {{HTML::image('public/img/front/crypto-b.svg', SITE_TITLE)}}
                                    <span>Buy/Sell Crypto</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/transactions')}}">
                                    {{HTML::image('public/img/front/transaction-history.svg', SITE_TITLE)}}
                                    <span>Transaction History</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/agent-list')}}">
                                    {{HTML::image('public/img/front/agent.svg', SITE_TITLE)}}
                                    <span>Bank Agent List </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/private-banking')}}">
                                    {{HTML::image('public/img/front/PrivateBanking-thumb.svg', SITE_TITLE)}}
                                    <span>Private Banking  </span>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('https://coinmarketcap.com/currencies/safebank-yes/')}}" target="_blank">
                                    {{HTML::image('public/img/front/SafeBank-thumb.svg', SITE_TITLE)}}
                                    <span>SafeBank  </span>
                                </a>
                            </div>
                        </div>




                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/comming-soon')}}">
                                    {{HTML::image('public/img/front/MyDigitalAsset-thumb.svg', SITE_TITLE)}}
                                    <span>My Digital Asset </span>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('dafrixchange')}}">
                                    {{HTML::image('public/img/front/exchange-thumb.svg', SITE_TITLE)}}
                                    <span>Exchange </span>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/airtime')}}">
                                    {{HTML::image('public/img/front/by-airtime-thumb.svg', SITE_TITLE)}}
                                    <span>Buy Airtime </span>
                                </a>
                            </div>
                        </div>

                        @if($recordInfo->user_type != 'Agent')
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/become-bank-agent')}}">
                                    {{HTML::image('public/img/front/bank-agent-thumb.svg', SITE_TITLE)}}
                                    <span>Become a Bank Agent</span>
                                </a>
                            </div>
                        </div>
                        @endif
                        
                         @if($recordInfo->user_type == 'Business' && $recordInfo->api_key != '')
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/merchant-withdraw-request-list')}}">
                                    {{HTML::image('public/img/front/withdraw-b.svg', SITE_TITLE)}}
                                    <span>Client Withdrawal</span>
                                </a>
                            </div>
                        </div>
                        @endif



                        @if($recordInfo->user_type == 'Agent')
                        <!--<div class="col-sm-3 qa-box-main">
                        <div class="qa-box">
                        <a href="{{URL::to('auth/client-deposit')}}">{{HTML::image('public/img/front/client-deposit.svg', SITE_TITLE)}}
                        <span>Client Deposits</span>
                        </a>
                        </div>
                        </div> -->
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/agent-withdraw-request-list')}}">
                                    {{HTML::image('public/img/front/client-withdraw.svg', SITE_TITLE)}}
                                    <span>Withdraw Request's</span>
                                </a>
                            </div>
                        </div>
                        @endif	
                    </div>
                    <div class="trans-hist">
                        <div class="heading-section trans-head">
                            <h5>Transactions</h5> <a href="{{URL::to('auth/transactions')}}">View all</a>
                        </div>
                        <div class="tran-list">
                            @if (Count($trans) > 0)
                            @foreach ($trans as $tran)
                            
                            @if($tran->trans_for == 'Withdraw##Agent' && $tran->user_id == Session::get('user_id'))
                                    @php
                                    $agent = getAgentById($tran->receiver_id);
                                    if ($agent != false) {
                                        $transFnm = $agent->first_name;
                                        $transLnm = $agent->last_name;
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                    }
                                    else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = ucfirst(substr($transFnm,0,1));  
                                    }
                                    @endphp                             
                                @else
                            @php
                            if ($tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2) {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "Agent";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));  
                            } 
                            }
                            }
                            else if ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "Agent";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));  
                            } 
                            }	
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1) {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2) {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 2) {
                            $res = getUserByUserId($tran->receiver_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));  
                            } 
                            }	
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != 0 && $tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));  
                            }
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1) {
                            $res = getUserByUserId($tran->receiver_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            }
                            else {
                            $res = getUserByUserId($tran->receiver_id);	
                            if ($res != false && $recordInfo->user_type == 'Personal')	{
                            $transFnm = $recordInfo->first_name;
                            $transLnm = $recordInfo->last_name;	
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                            }
                            else if ($res != false && $recordInfo->user_type == 'Business') {
                            $transFnm = $recordInfo->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = ucfirst(substr($transFnm,0,1)); 
                            }
                            }
                            @endphp
                            @endif
                            <div class=" trans-thumb">
                                <div class="tran-name">
                                    <div class="tran-name-icon">{{$transShortName}}
                                    </div>
                                    <div class="trans-name-title">
                                        <h6><a href="{{URL::to('auth/transaction-detail/'.$tran->id)}}">{{substr(strtoupper($transName),0,30)}}</a></h6>
                                        @if ($tran->status == 1)
                                        <span>Success</span>
                                        @elseif($tran->status == 2)
                                        <span>Pending</span>
                                        @elseif($tran->status == 3)
                                        <span>Cancelled</span>
                                        @elseif($tran->status == 4)
                                        <span>Failed</span>
                                        @elseif($tran->status == 5)
                                        <span>Error</span>
                                        @elseif($tran->status == 6)
                                        <span>Abandoned</span>
                                        @elseif($tran->status == 7)
                                        <span>PendingInvestigation</span>
                                        @else
                                        <span>Failed</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="trans-status">
                                   @if ($tran->user_id == Session::get('user_id') && $tran->trans_type == 2 && ($tran->trans_for == "W2W" || $tran->trans_for == "Withdraw##Invite_New_User"))
                            <span>Transfer</span>
                            @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id != 0 && $tran->trans_type == 2)
                            <span>Sent</span>
                            @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1)
                            <span>Topup</span>
                            @elseif ($tran->user_id != Session::get('user_id') && $tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2)
                            <span>Received</span>
                            @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1)
                            <span>Received</span>
                            @elseif ($tran->user_id != Session::get('user_id') && $tran->receiver_id == Session::get('user_id') && $tran->trans_type == 1)
                            <span>Received</span>
                            @elseif ($tran->trans_type == 2 && ($tran->trans_for == "W2W" || $tran->trans_for == "Withdraw##Invite_New_User"))
                            <span>Transfer</span>
                            @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2)
                            <span>Withdraw</span>
                           
                            @elseif ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent")
                            <span>Received</span>
                            @endif
                                </div>
                                <div class="trans-money">
                                    {{$tran->currency}} {{number_format($tran->amount,2,'.',',')}}
                                    @php
                                    $date = date_create($tran->created_at);
                                    $transDate = date_format($date,'M, d Y, H:i A');
                                    @endphp
                                    <p style="font-size:11px;">{{$transDate}}</p>

                                </div>

                            </div>
                            @endforeach
                            @endif
                            <!--                            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#success-alert-Modal">
                                                            Payment Successful
                                                        </button>
                                                        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#dialog-Modal">
                                                            Payment Successful detail
                                                        </button>-->
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