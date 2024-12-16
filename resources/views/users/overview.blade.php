@extends('layouts.inner')
@section('content')

<script>
    $(document).ready(function () {
    $("#addAcnt").validate();
    });</script>

<style>
.second-vcard h2 {
    font-size: 18px;
    margin: 9px 0px 6px 0px
}

textarea {
  resize: none;
}
</style>

<style>
    .not_txt{
        padding-bottom: 5px;
    }
    .form-group.form-field select {
    height: 32px;
    border-radius: 10px;
    background: #f5f5f5;
    padding: 5px;
    border: none;
    text-indent: 20px;
    font-size: 12px;
    width: 100%;
}
.form-group.form-field textarea {
 
    border-radius: 10px;
    background: #f5f5f5;
    padding: 5px;
    border: none;
    text-indent: 20px;
    font-size: 12px;
    width: 100%;
}
</style>

<script>
    $(document).ready(function () {
    $(".open-drop").click(function() {
        $(".open-drop-list").slideToggle('fast',function() {
            if($(this).is(':visible')==true)
            {
            $('.open-drop').removeClass('total_open');
            $('.open-drop').addClass('total_close');
            }
            else{
            $('.open-drop').addClass('total_open');    
            $('.open-drop').removeClass('total_close');   
            }
             });
    });
    $(".open-drop1").click(function() {
        $(".open-drop-list1").slideToggle('fast',function() {
            if($(this).is(':visible')==true)
            {
            $('.open-drop1').removeClass('usd_open');
            $('.open-drop1').addClass('usd_close');
            }
            else{
            $('.open-drop1').addClass('usd_open');    
            $('.open-drop1').removeClass('usd_close');   
            }
             });
    });
    });
    </script>

<?php //if($recordInfo->dba_wallet_amount!='0') { ?>


<script>

get_balance_in_usd();

setInterval(function () { get_balance_in_usd();  }, 60000);

function get_balance_in_usd()
{
var dba_wallet_balance='<?php echo $recordInfo->dba_wallet_amount+$recordInfo->dba_hold_wallet_amount; ?>';
$.ajax({
      url: '<?php echo HTTP_PATH;?>/auth/get-dba-conversion-usd?dba_wallet=' + dba_wallet_balance,
      success: function (data) {
      var res=JSON.parse(data);
      var total=res.total;
      var daily_rate=res.daily_rate;
      $('#usd_wallet_balance').html('USD '+total);
      $('#current_rate').html('USD '+daily_rate);
      }
    });

}

</script>  



<?php //} ?>

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
                    @php
                    $card_class = getUserCardType($recordInfo->account_category);
                    @endphp
                    <!-- <div class="wallet-card {{$card_class}}">
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
                        <!-- <a href="{{DBA_WEBSITE}}/autologin?enctype={{ $enc_user_id }}&api_token=token&action=overview" target="_blank">
                        <div class="vcard {{$card_class}} second-vcard" >
                            <span>Available balance</span>
                            <h2>A:{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_wallet_amount)*100)/100,2,'.',',')}}</h2>
                            <h2>H:{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_hold_wallet_amount)*100)/100,2,'.',',')}}</h2>
                            <h2>T:{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_hold_wallet_amount+$recordInfo->dba_wallet_amount)*100)/100,2,'.',',')}}</h2>
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
                         </a>

                         <a href="https://www.nimbleappgenie.live/dba-interest/autologin?enctype={{ $enc_user_id }}&api_token=token&action=overview" target="_blank">
                        <div class="vcard {{$card_class}} second-vcard" >
                            <span>Available balance</span>
                            <h2>A:{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_wallet_amount)*100)/100,2,'.',',')}}</h2>
                            <h2>H:{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_hold_wallet_amount)*100)/100,2,'.',',')}}</h2>
                            <h2>T:{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_hold_wallet_amount+$recordInfo->dba_wallet_amount)*100)/100,2,'.',',')}}</h2>
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
                         </a>     -->



                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>


                <div class="top-wallet-main">
                <div class="wallet-top">
                    <div class="wallet-head-box">
                    <div class="daily-yd">
                        <a class="drop-total-bal-head" href="{{DBA_WEBSITE}}/autologin?enctype={{ $enc_user_id }}&api_token=token&action=overview" target="_blank">eSavings Vault</a>
                        </div>
                        <div class="daily-yd">
                        <a class="drop-total-bal-head" href="{{DBA_WEBSITE}}/autologin?enctype={{ $enc_user_id }}&api_token=token&action=auth/swap" target="_blank">Buy DBA</a>
                        </div>
                    </div>
                    <div class="balance-wallet-box">
                        <div class="balance-wallet-drop open-drop total_open">
                            <h6 class="drop-total-bal-head">Total Balance </h6>
                            <h5 class="drop-total-bal">{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_hold_wallet_amount+$recordInfo->dba_wallet_amount)*100)/100,2,'.',',')}}</h5>
                            <ul class="balance-wallet-drop-list open-drop-list" style="display: none;">
                                <li>
                                    <h6>Available Balance </h6>
                                    <h5>{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_wallet_amount)*100)/100,4,'.',',')}}</h5>
                                </li>
                                <li>
                                    <h6>eSavings Vault </h6>
                                    <h5>{{$recordInfo->dba_currency}} {{number_format(floor(($recordInfo->dba_hold_wallet_amount)*100)/100,4,'.',',')}}</h5>
                                </li>
                            </ul>
                        </div>
                        <div class="balance-wallet-drop open-drop1 usd_open">
                            <h6 class="drop-total-bal-head">Value</h6>
                            <h5 class="drop-total-bal" id="usd_wallet_balance">USD 0.0</h5>
                            <ul class="balance-wallet-drop-list open-drop-list1" style="display: none;">
                                <li>
                                    <h6>1 DBA = </h6>
                                    <h5 id="current_rate">USD 0.0</h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
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
                                     Pay
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/withdraw-request')}}">
                                    {{HTML::image('public/img/front/withdraw-b.svg', SITE_TITLE)}}
                                    <span>
                                    Withdraw
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="https://localhost/dba-interest/overview/" target="_blank">
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
                                <a href="{{URL::to('auth/transactions')}}" target="_blank">
                                    {{HTML::image('public/img/front/pdf.svg', SITE_TITLE)}}
                                    <span>eStatement  </span>
                                </a>
                            </div>
                        </div>




                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                            <a class="drop-total-bal-head" href="{{DBA_WEBSITE}}/autologin?enctype={{ $enc_user_id }}&api_token=token&action=overview" target="_blank">
                                    {{HTML::image('public/img/front/MyDigitalAsset-thumb.svg', SITE_TITLE)}}
                                    <span>My Digital Assets</span>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/exchange')}}">
                                    {{HTML::image('public/img/front/exchange-thumb.svg', SITE_TITLE)}}
                                    <span>Exchange </span>
                                </a>
                            </div>
                        </div>

                        <!-- <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/airtime')}}">
                                    {{HTML::image('public/img/front/by-airtime-thumb.svg', SITE_TITLE)}}
                                    <span>Buy Airtime </span>
                                </a>
                            </div>
                        </div> -->

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

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                                <a href="{{URL::to('auth/dafri-me')}}">
                                    {{HTML::image('public/img/front/money-bill-1-regular.svg', SITE_TITLE)}}
                                    <span>Request Payment</span>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box new-giftcard">
                                <a href="{{URL::to('auth/airtime_giftcard')}}">
                                    {{HTML::image('public/img/gift-solid.svg', SITE_TITLE)}}
                                    <span>GiftCard</span>
                                    <span class="addnewgiftcar">new</span>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-3 qa-box-main">
                            <div class="qa-box">
                               <a href="#" data-toggle="modal" data-target="#basicModal">
                                    {{HTML::image('public/img/front/PrivateBanking-thumb.svg', SITE_TITLE)}}
                                    <span>Add Bank Details</span>
                                </a>
                            </div>
                        </div>   


                        @if($recordInfo->api_key!="" && $recordInfo->api_enable=='Y')
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
                            <h5>Transactions</h5>   @if (Count($trans) > 0) <a href="{{URL::to('auth/transactions')}}">View all</a> @endif
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
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "Agent";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));  
                            } 
                            }
                            }
                            else if ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "Agent";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));  
                            } 
                            }	
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1) {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2) {
                            $res = getUserByUserId($tran->user_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 2) {
                            $res = getUserByUserId($tran->receiver_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));  
                            } 
                            }	
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != 0 && $tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                            $agent = getAgentById($tran->receiver_id);
                            if ($agent != false) {
                            $transFnm = $agent->first_name;
                            $transLnm = $agent->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));  
                            }
                            }
                            else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1) {
                            $res = getUserByUserId($tran->receiver_id);
                            if ($res != false && $res->user_type == 'Personal') {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Business') {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            }
                            else {
                            $res = getUserByUserId($tran->receiver_id);	
                            if ($res != false && $recordInfo->user_type == 'Personal')	{
                            $transFnm = $recordInfo->first_name;
                            $transLnm = $recordInfo->last_name;	
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                            }
                            else if ($res != false && $recordInfo->user_type == 'Business') {
                            $transFnm = $recordInfo->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1));
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                            $transFnm = $res->first_name;
                            $transLnm = $res->last_name;
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                            $transFnm = $res->business_name;
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
                            }
                            else {
                            $transFnm = "N/A";
                            $transLnm = "";
                            $transName = $transFnm." ".$transLnm;
                            $transShortName = strtoupper(substr($transFnm,0,1)); 
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
                                @if($tran->user_id == Session::get('user_id') && $tran->trans_type ==1)
                                <span>Money In</span>
                                @elseif($tran->user_id == Session::get('user_id') && $tran->trans_type ==2)
                                <span>Money Out</span>
                                @elseif($tran->receiver_id == Session::get('user_id') && $tran->trans_type ==2)
                                <span>Money In</span>
                                @endif
                                </div>
                                <div class="trans-money">
                                    {{$tran->currency}} {{number_format($tran->amount,2,'.',',')}}
                                    @php
                                    $date = date_create($tran->created_at);
                                    $transDate = date_format($date,'M, d Y, H:i A');
                                    @endphp
                                    <p style="font-size:11px;">{{$transDate}}</p>
                                    <p style="font-size:11px;"><a style="color: #fff;" href="{{URL::to('auth/transaction-detail/'.$tran->id)}}"><button style="background-color: #000;color: #fff;">View</button></a></p>

                                </div>

                            </div>
                            @endforeach
                            @else
                            No Record Found
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

<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            {{ Form::open(array('method' => 'post', 'id' => 'addAcnt', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit_bank();','url'=>'/auth/manual-withdraw')) }}
            <div class="modal-dialog">
                <div class="modal-content bank-detail-form">
                    <h4>Bank Detail
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h4>

                    <div class="row">
                         <div class="col-sm-12">
                            <div class="form-group form-field">
                                <label>
                                    Select Bank Transfer
                                </label>
                                <div class="field-box">

                                <select class="required type_transfer" id="type_transfer" name="type_transfer">
            <option value="US Bank Transfer">US Bank Transfer</option>
            <option value="UK Bank Transfer">UK Bank Transfer</option>
            <option value="IBAN EU Transfer">IBAN EU Transfer</option>
            <option value="Transfer To Wise">Transfer To Wise </option>
            <option value="Nigeria Bank Transfer">Nigeria Bank Transfer</option>
            <option value="SA Bank Transfer">SA Bank Transfer</option>
            <option value="Bank Wire Transfer (Global)">Bank Wire Transfer (Global)</option>
            <option value="Botswana Bank Transfer">Botswana Bank Transfer</option>
            <option value="Swaziland Bank Transfer">Swaziland Bank Transfer</option>
            <option value="Lesotho Bank Transfer">Lesotho Bank Transfer</option>
            <option value="Namibia Bank Transfer">Namibia Bank Transfer</option>
        </select>
              
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-sm-6 cotb"  style="display:none">
                            <div class="form-group form-field">
                                <label>
                                Country of The Bank
                                </label>
                                <div class="field-box">
                                    <input name="cotb" id="cotb" class="required" placeholder="Enter Country of The Bank"  type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-field">
                                <label>
                                    Account Holder
                                </label>
                                <div class="field-box">
                                    <input name="accName" id="accName" class="required" placeholder="Enter Account Holder Name" value="{{$account_name}}" readonly type="text">
                                </div>
                            </div>
                        </div>



                        <div class="col-sm-6 we"  style="display:none">
                            <div class="form-group form-field">
                                <label>
                                    Wisa Email
                                </label>
                                <div class="field-box">
                                    <input name="wisaEmail" id="wisaEmail" class="required" placeholder="Enter Wisa Email"  type="text">
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-sm-6 bic"  style="display:none">
                            <div class="form-group form-field">
                                <label>
                                    BIC
                                </label>
                                <div class="field-box">
                                    <input name="bic" id="bic" class="required" placeholder="Enter BIC"   type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 rn">
                            <div class="form-group form-field">
                                <label>
                                    Routing number
                                </label>
                                <div class="field-box">
                                    <input name="routNumbr" id="routNumbr" class="required" placeholder="Routing number" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 an">
                            <div class="form-group form-field">
                                <label>
                                    Account Number
                                </label>
                                <div class="field-box">
                                    <input name="accNumbr" id="accNumbr" class="required" placeholder="Enter Account Number" type="text" onkeypress="return validateFloatKeyPress(this,event);">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 can">
                            <div class="form-group form-field">
                                <label>
                                    Confirm Account Number
                                </label>
                                <div class="field-box">
                                    <input name="confirm_accNumbr" id="confirm_accNumbr" class="required" placeholder="Confirm Account Number" equalTo = '#accNumbr' type="text" onkeypress="return validateFloatKeyPress(this,event);"   >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 ibn" style="display:none">
                            <div class="form-group form-field">
                                <label>
                                IBAN Number
                                </label>
                                <div class="field-box">
                                    <input name="iBan" id="iBan" class="required" placeholder="Enter IBAN Number" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 at">
                            <div class="form-group form-field">
                                <label class="accounttypename">
                                    Account Type
                                </label>
                                <div class="field-box">
                                    <input name="acctTyp" id="acctTyp" class="required" placeholder="Enter Account Type" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 swc" style="display:none">
                            <div class="form-group form-field">
                                <label>
                                Swift Code 
                                </label>
                                <div class="field-box">
                                    <input name="swc" id="swc" class="required" placeholder="Enter Swift Code" type="text">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 c">
                            <div class="form-group form-field">
                                <label>
                                    Currency 
                                </label>
                                <div class="field-box">
                                    <input name="currncy" id="currncy" class="required" placeholder="Enter Currency  " type="text">
                                </div>
                            </div>
                        </div>  
                            <div class="col-sm-6 sc" style="display:none">
                            <div class="form-group form-field">
                                <label>
                                    Sort Code 
                                </label>
                                <div class="field-box">
                                    <input name="sorCode" id="sorCode" class="required" placeholder="Enter Sort Code" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 bn">
                            <div class="form-group form-field">
                                <label>
                                    Bank Name
                                </label>
                                <div class="field-box">
                                    <input name="bnkName" id="bnkName" class="required" placeholder="Enter Bank Name " type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 ba">
                            <div class="form-group form-field">
                                <label>
                                    Bank Address
                                </label>
                                <div class="field-box">
                                    <textarea name="bnkAdd" id="bnkAdd" class="required" placeholder="Enter Bank Address" rows="4" cols="50"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 rfp">
                            <div class="form-group form-field">
                                <label>
                                    Reason For Payment
                                </label>
                                <div class="field-box">
                                    <textarea name="reasonPay" id="reasonPay" class="required" placeholder="Enter Reason For Payment" rows="4" cols="50"> </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 brc" style="display:none">
                            <div class="form-group form-field">
                                <label class="brcname">
                                    Branch Code 
                                </label>
                                <div class="field-box">
                                    <input name="brnchCod" id="brnchCod" class="required" placeholder="Enter Branch Code" type="text">
                                </div>
                            </div>
                        </div>
                       
                        
                        
                        <div class="col-sm-12">
                            <input type="hidden" name="addAccount" value="true">
                            <div class="form-group form-field">
                                <button class="sub-btn button_disable_bank" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{Form::close()}}
        </div>


    <script>
    function disable_submit_bank()
    {
    var empty_field = 0;
    $(".required").each(function() {
    if ($(this).val() == "")
    {
    empty_field = 1;
    }
    });
    if (empty_field == 0 && $(".required").hasClass('error') == false)
    {
    $('.button_disable_bank').prop('disabled', true);
    return true;
    }
    return false;
    }
    </script>

<script type="text/javascript">

    function disable_submit()
    {
    $('.button_disable').prop('disabled', true);
    return true;
    }

    function disable_submit_bank()
    {
    var empty_field = 0;
    $(".required").each(function() {
    if ($(this).val() == "")
    {
    empty_field = 1;
    }
    });
    if (empty_field == 0 && $(".required").hasClass('error') == false)
    {
    $('.button_disable_bank').prop('disabled', true);
    return true;
    }
    return false;
    }


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
</script>


<script>
$(document).ready(function(){
    $('.rn').show();
        $('.rn :input').addClass('required');
        $('.an').show();
        $('.an :input').addClass('required');
        $('.can').show();
        $('.can :input').addClass('required');
        $('.at').show();
        $('.at :input').addClass('required');
        $('.c').show();
        $('.c :input').addClass('required');
        $('.bn').show();
        $('.bn :input').addClass('required');
        $('.ba').show();
        $('.ba :input').addClass('required');
        $('.rfp').show();
        $('.rfp :input').addClass('required');
        $('.ibn').hide();
        $('.ibn :input').removeClass('required');
        $('.sc').hide();
        $('.sc :input').removeClass('required');
        $('.brc').hide();
        $('.brc :input').removeClass('required');
        $('.bic').hide();
        $('.bic :input').removeClass('required');
        $('.we').hide();
        $('.we :input').removeClass('required');
        $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");

    $("select.type_transfer").change(function(){
        var selectedCountry = $(this).children("option:selected").val();
      if(selectedCountry=="US Bank Transfer"){
       
        $('.rn').show();
        $('.rn :input').addClass('required');
        $('.an').show();
        $('.an :input').addClass('required');
        $('.can').show();
        $('.can :input').addClass('required');
        $('.at').show();
        $('.at :input').addClass('required');
        $('.c').show();
        $('.c :input').addClass('required');
        $('.bn').show();
        $('.bn :input').addClass('required');
        $('.ba').show();
        $('.ba :input').addClass('required');
        $('.rfp').show();
        $('.rfp :input').addClass('required');
        $('.ibn').hide();
        $('.ibn :input').removeClass('required');
        $('.sc').hide();
        $('.sc :input').removeClass('required');
        $('.brc').hide();
        $('.brc :input').removeClass('required');
        $('.bic').hide();
        $('.bic :input').removeClass('required');
        $('.we').hide();
        $('.we :input').removeClass('required');
        $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");

       }else if(selectedCountry=="UK Bank Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').hide();
$('.at :input').removeClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.ba').show();
$('.ba :input').addClass('required');
$('.rfp').show();
$('.rfp :input').addClass('required');
$('.ibn').show();
$('.ibn :input').addClass('required');
$('.sc').show();
$('.sc :input').addClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
 $('.we').hide();
  $('.we :input').removeClass('required');
  $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
}else if(selectedCountry=="IBAN EU Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').hide();
$('.an :input').removeClass('required');
$('.can').hide();
$('.can :input').removeClass('required');
$('.at').hide();
$('.at :input').removeClass('required');
$('.bn').hide();
$('.bn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').show();
$('.bic :input').addClass('required');
$('.ibn').show();
$('.ibn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.ba').show();
$('.ba :input').addClass('required');
$('.we').hide();
  $('.we :input').removeClass('required');
  $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
}else if(selectedCountry=="Transfer To Wise"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').hide();
$('.an :input').removeClass('required');
$('.can').hide();
$('.can :input').removeClass('required');
$('.at').hide();
$('.at :input').removeClass('required');
$('.bn').hide();
$('.bn :input').removeClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.rfp').show();
$('.rfp :input').addClass('required');
$('.we').show();
 $('.we :input').addClass('required');
 $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
}else if(selectedCountry=="Nigeria Bank Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').hide();
$('.cotb :input').removeClass('required');
$('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Type of Account');
 $('#acctTyp').attr("placeholder", "Enter Type of Account");
}else if(selectedCountry=="SA Bank Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').show();
$('.brc :input').addClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').hide();
 $('.cotb :input').removeClass('required');
 $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Type of Account');
 $('#acctTyp').attr("placeholder", "Enter Type of Account");
}else if(selectedCountry=="Bank Wire Transfer (Global)"){
    
$('.brcname').html('Branch Code/Routing/Sortcode');
$('#brnchCod').attr("placeholder", "Enter Branch Code/Routing/Sortcode'");
$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').show();
$('.brc :input').addClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').show();
$('.ba :input').addClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').show();
$('.rfp :input').addClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').show();
 $('.cotb :input').addClass('required');
 $('.swc').show();
 $('.swc :input').addClass('required');
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
 
}else if(selectedCountry=="Botswana Bank Transfer" || selectedCountry=="Swaziland Bank Transfer" || selectedCountry=="Lesotho Bank Transfer" || selectedCountry=="Namibia Bank Transfer"){
    $('.accounttypename').html('Type of Account');
    $('#acctTyp').attr("placeholder", "Enter Type of Account");
$('.brcname').html('Branch Code');
$('#brnchCod').attr("placeholder", "Enter Branch Code");
$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').show();
$('.brc :input').addClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').hide();
 $('.cotb :input').removeClass('required');
 $('.swc').hide();
 $('.swc :input').removeClass('required');
 
}


    });
});
</script>   



@endsection