@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">

                <div class="transaction-history history-page">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="transaction-history-details">
                                @php
                                $date = date_create($trans->created_at);
                                $transDate = date_format($date,'H:i a / d M Y');
                                @endphp
                                @if($trans->status == 1)
                                <h4>Transaction Successful <small>{{ $transDate }}</small></h4>
                                @elseif($trans->status == 2)
                                <h4>Transaction Pending <small>{{ $transDate }}</small></h4>
                                @elseif($trans->status == 3)
                                <h4>Transaction Cancelled <small>{{ $transDate }}</small></h4>
                                @elseif($trans->status == 4)
                                <h4>Transaction Failed <small>{{ $transDate }}</small></h4>
                                @elseif($trans->status == 5)
                                <h4>Transaction Error <small>{{ $transDate }}</small></h4>
                                @elseif($trans->status == 6)
                                <h4>Transaction Abandoned <small>{{ $transDate }}</small></h4>
                                @elseif($trans->status == 7)
                                <h4>Transaction Pending For Investigation <small>{{ $transDate }}</small></h4>
                                @endif
                                @if($trans->status == 1) 
                                {{HTML::image('public/img/front/successful.svg', SITE_TITLE)}}
                                @elseif($trans->status == 2)
                                {{HTML::image('public/img/front/pending.png', SITE_TITLE)}}
                                @else
                                {{HTML::image('public/img/front/close.svg', SITE_TITLE)}}
                                @endif
                            </div>
                            <div class="trans-card">
                                <h5>Transaction ID</h5>
                                <small>{{ $trans->id }}</small>
                            </div>
                            <div class="trans-card">
                                <h5>Reference ID</h5>
                                <small>
                                    @if($trans->refrence_id == 'na')
                                    {{ 'N/A' }}
                                    @else
                                    {{ $trans->refrence_id }}
                                    @endif
                                </small>
                            </div>
                            <div class="trans-card">
                                <div>
                                <?php
                            $trans->trans_for = str_replace("Refund", "Reverse",$trans->trans_for); ?>
                                    @if($trans->trans_for == 'Mobile Top-up')
                                        <h5> Mobile Top-up</h5>
                                    @elseif($trans->trans_for == 'Exchange Charge')
                                        <h5> Exchange Charge</h5> 
                                    @elseif($trans->trans_for == 'SWAP')
                                    <h5> SWAP</h5>   
                                    @elseif($trans->trans_for == 'EPAY ME')
                                    <h5> EPAY ME</h5>    
                                    @elseif($trans->trans_for == 'GIFT CARD')
                                    <h5> GIFT CARD</h5>   
                                    @elseif($trans->trans_for == 'EPAY MERCHANT')
                                    <h5> EPAY MERCHANT</h5>   
                                    @elseif($trans->trans_for == 'DBA eCash')
                                        <h5>DBA eCash</h5>    
                                        @php $receiver = getUserByUserId($trans->user_id); @endphp
                                        <small> @if ($receiver->user_type == "Personal")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Business")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->first_name != "")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->business_name != "")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @endif</small>    
                                    @elseif($trans->trans_for == 'Merchant_Withdraw')
                                    
                                        @php
                                    $sender = getUserByUserId($trans->receiver_id);
                                    @endphp
                                       <h5> Merchant Withdraw</h5>
                                        <small>
                                        @if ($sender->user_type == "Personal")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Business")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->business_name != "")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small> 
                                     @endif

                                    @if($trans->trans_for == 'ONLINE_PAYMENT')
                                    @if($trans->user_id != Session::get('user_id'))
                                    @php
                                    $sender = getUserByUserId($trans->user_id);
                                    @endphp
                                    <h5>Online Purchase</h5>
                                    <small>
                                        @if ($sender->user_type == "Personal")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Business")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->business_name != "")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>	
                                    @else
                                    @php
                                    $receiver = getUserByUserId($trans->receiver_id);
                                    @endphp
                                    <h5>Online Purchase</h5>
                                    <small>
                                        @if ($receiver->user_type == "Personal")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Business")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->first_name != "")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->business_name != "")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @endif
                                    @else
                                    @if($trans->user_id != Session::get('user_id') && $trans->receiver_id == Session::get('user_id') && $trans->trans_type == 1 && $trans->trans_for == 'W2W')
                                    @php
                                    $sender = getUserByUserId($trans->user_id);
                                    @endphp
                                    @if ($sender->user_type == 'Agent')
                                    <h5>Agent Withdraw</h5>
                                    <small>
                                        @if ($sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @else
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @endif
                                    @elseif($trans->user_id != Session::get('user_id') && $trans->receiver_id == Session::get('user_id') && $trans->trans_type == 2 && $trans->trans_for == 'W2W')
                                    @php
                                    $sender = getUserByUserId($trans->user_id);
                                    @endphp
                                    @if ($sender->user_type == 'Agent')
                                    <h5>Agent Topup</h5>
                                    <small>
                                        @if ($sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @else
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @elseif($sender->user_type == 'Personal')
                                    @if($trans->user_id==1)
                                    <h5>Admin Wallet Adjust</h5>
                                    @else
                                    <h5>Wallet2Wallet</h5>
                                    @endif
                                    <small>
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @elseif($sender->user_type == 'Business')
                                    @if($trans->user_id==1)
                                    <h5>Admin Wallet Adjust</h5>
                                    @else
                                    <h5>Wallet2Wallet</h5>
                                    @endif
                                    <small>
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                    </small>
                                    <small>{{ $sender->phone }}</small>		
                                    @endif
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->receiver_id != Session::get('user_id') && $trans->trans_type == 2 && $trans->trans_for == 'W2W')
                                    @php
                                    $receiver = getUserByUserId($trans->receiver_id);
                                    @endphp
                                    @if ($receiver->user_type == 'Agent')
                                    <h5>Transfer</h5>
                                    <small>
                                        @if ($receiver->first_name != "")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @else
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @elseif($receiver->user_type == 'Personal')
                                    @if($trans->user_id==1)
                                    <h5>Admin Wallet Adjust</h5>
                                    @else
                                    <h5>Wallet2Wallet</h5>
                                    @endif
                                    <small>
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @elseif($receiver->user_type == 'Business')
                                    <h5>Transfer </h5>
                                    <small>
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @endif
                                    @elseif($trans->user_id != Session::get('user_id') && $trans->receiver_id == Session::get('user_id') && $trans->trans_type == 2 && $trans->trans_for == 'Withdraw##Invite_New_User')
                                    @php
                                    $sender = getUserByUserId($trans->user_id);
                                    @endphp
                                    <h5>Invite Pay</h5>
                                    <small>
                                        @if ($sender->user_type == "Personal")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Business")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name == "")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->receiver_id != Session::get('user_id') && $trans->trans_type == 2 && $trans->trans_for == 'Withdraw##Invite_New_User')
                                    @php
                                    if ($trans->receiver_id > 0)
                                    $receiver = getUserByUserId($trans->receiver_id);
                                    else
                                    $receiver = getUserByUserId(1);
                                    @endphp
                                    <h5>Invite Pay</h5>
                                    <small>
                                        @if ($receiver->user_type == "Personal")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Business")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->first_name != "")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->first_name == "")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->receiver_id != Session::get('user_id') && $trans->trans_type == 1 && $trans->trans_for == 'W2W')
                                    @php
                                    $receiver = getUserByUserId($trans->receiver_id);
                                    @endphp
                                    @if ($receiver->user_type == 'Agent')
                                    <h5>Agent Topup</h5>
                                    <small>
                                        @if ($receiver->first_name != "")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @else
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @elseif($receiver->user_type == 'Personal')
                                    <h5>Wallet Topup</h5>
                                    <small>
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @elseif($receiver->user_type == 'Business')
                                    <h5>Wallet Topup</h5>
                                    <small>
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                    </small>
                                    <small>{{ $receiver->phone }}</small>
                                    @endif
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->receiver_id == 0 && $trans->trans_type == 2)
                                    @php
                                    $sender = getUserByUserId($trans->user_id);

                                    $transForArr = explode("##",$trans->trans_for);
                                    if (Count($transForArr) >= 2) {
                                    $paymentType = $transForArr[0];
                                    }
                                    else {
                                    $paymentType = $transForArr[0];
                                    }

                                    if ($paymentType == 'CryptoWithdraw') {
                                    $paymentType = 'Crypto Withdraw';	
                                    }

                                    @endphp
                                    

                                    <h5>Wallet Withdraw ({{str_replace("_"," ",$paymentType)}})</h5>
                                    <small>
                                        @if ($sender->user_type == "Personal")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Business")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->business_name != "")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->receiver_id == 0 && $trans->trans_type == 1)
                                    @php
                                    $sender = getUserByUserId($trans->user_id);
                                    @endphp
                                    @if($trans->trans_for == 'ManualDeposit')
                                    <h5>Wallet Topup ( {{ 'Manual Deposit' }} )</h5>
                                    @elseif($trans->trans_for == 'CryptoDeposit')
                                    <h5>Wallet Topup ( {{ 'Crypto Deposit' }} )</h5>
                                    @elseif($trans->trans_for == 'Converted Amount')
                                    <h5>Amount (After Currency Change)</h5>
                                    @else
                                    <h5>Wallet Topup ( {{ $trans->trans_for }} )</h5>
                                    @endif
                                    <small>
                                        @if ($sender->user_type == "Personal")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Business")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->business_name != "")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->trans_type == 2 && $trans->trans_for == "Withdraw##Agent")
                                    <h5>Agent Withdraw</h5>
                                    @php
                                    $sender = getAgentById($trans->receiver_id);
                                    @endphp
                                    <small>
                                        @if($sender != false)
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @else
                                        {{'N/A'}}
                                        @endif
                                    </small>
                                    <small>
                                        @if($sender != false)
                                        {{ $sender->phone }}
                                        @else
                                        {{'N/A'}}
                                        @endif
                                    </small>
                                    @elseif($trans->receiver_id == $agent_id && $trans->trans_type == 2 && $trans->trans_for == "Withdraw##Agent")
                                    @php
                                    $sender = getUserByUserId($trans->user_id);
                                    @endphp
                                    <h5>Agent Withdraw</h5>
                                    <small>
                                        @if ($sender->user_type == "Personal")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Business")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->first_name != "")
                                        {{ strtoupper(strtolower($sender->first_name))." ".strtoupper(strtolower($sender->last_name)) }}
                                        @elseif($sender->user_type == "Agent" && $sender->business_name != "")
                                        {{ strtoupper(strtolower($sender->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $sender->phone }}</small>
                                    @elseif($trans->user_id == Session::get('user_id') && $trans->receiver_id > 0 && $trans->trans_type == 2 && $trans->trans_for == 'ONLINE_PAYMENT')
                                    @php
                                    $receiver = getUserByUserId($trans->receiver_id);
                                    @endphp
                                    <h5>Online Purchase</h5>
                                    <small>
                                        @if ($receiver->user_type == "Personal")
                                        {{ strtoupper($receiver->first_name)." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Business")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->first_name != "")
                                        {{ strtoupper(strtolower($receiver->first_name))." ".strtoupper(strtolower($receiver->last_name)) }}
                                        @elseif($receiver->user_type == "Agent" && $receiver->business_name != "")
                                        {{ strtoupper(strtolower($receiver->business_name)) }}
                                        @endif
                                    </small>
                                    <small>{{ $receiver->phone }}</small>

                                    @endif
                                    @endif
                                </div>
                                <div class="rt-trans">
                                    <div class="">{{$trans->currency.' '.number_format($trans->amount,2,'.',',')}}</div>
                                    <!-- <div class="small-btn">
                                    <a href="#">Pay Again</a>
                                    <a href="#">Share</a>
                                    </div> -->
                                </div>
                            </div>

                            <div class="trans-card">
                                <div>
                                    <h5>Fees Detail</h5>
                                    @if($trans->trans_for == 'W2W')
                                    <small>{{ 'DafriBank Wallet' }}</small>
                                    <small>{{'******'.substr($recordInfo->account_number,6,4)}}</small>
                                    @endif
                                </div>
                                <div class="">  
                                    <small>Fees: 
                                        @if($trans->trans_for == 'CryptoDeposit' || $trans->trans_for == 'EPAY_CARD'  )
                                        {{ $trans->currency.' '.number_format($trans->receiver_fees,10,'.',',') }}
                                        @elseif($trans->trans_for == 'Manual Withdraw')
                                        {{ $trans->currency.' '.number_format($trans->sender_fees,10,'.',',') }}
                                        @elseif($trans->trans_for == 'ManualDeposit' || $trans->trans_for == 'OZOW_EFT' || $trans->trans_for == 'CardDeposit')
                                        {{ $trans->currency.' '.number_format($trans->receiver_fees,10,'.',',') }}
                                        @elseif($trans->trans_for == 'Mobile Top-up')
                                        {{ $trans->currency.' '.number_format($trans->sender_fees,10,'.',',') }}
                                        @elseif($trans->trans_for == 'Converted Amount')
                                        {{ $trans->sender_currency.' '.number_format($trans->sender_fees,10,'.',',') }}
                                        @elseif($trans->trans_for == 'ONLINE_PAYMENT' || $trans->trans_for == 'Merchant_Withdraw')
                                            @if($trans->user_id != Session::get('user_id'))
                                            @php
                                                $sender = getUserByUserId($trans->receiver_id);
                                            @endphp
                                            {{ $sender->currency.' '.number_format($trans->receiver_fees,10,'.',',') }}
                                            @else
                                            @php
                                                $receiver = getUserByUserId($trans->user_id);
                                            @endphp
                                            {{ $receiver->currency.' '.number_format($trans->sender_fees,10,'.',',') }}
                                            @endif
                                        @else
                                        
                                        @if($trans->user_id != Session::get('user_id'))
                                            @php
                                                $sender = getUserByUserId($trans->receiver_id);
                                            @endphp
                                            {{ $sender->currency.' '.number_format($trans->receiver_fees,10,'.',',') }}
                                            @else
                                            @php
                                                $receiver = getUserByUserId($trans->user_id);
                                            @endphp
                                            {{ $receiver->currency.' '.number_format($trans->sender_fees,10,'.',',') }}
                                            @endif
                                            
                                        @endif
                                        
                                    </small><br>
                                </div>

                            </div>
                            <div class="trans-card">
                                <div class="trans_detail">
                                    <h5>Description</h5>
                                    @php
                                    $bllngDesc = str_replace("<br>","##",$trans->billing_description);
                                    $descArr = explode("##",$bllngDesc);
                                    for ($g=0;$g<Count($descArr);$g++)
                                                      {
                                                      if ($descArr[$g] == 'na') {
                                                      continue;
                                                      }
                                                      if($login_user_id == $trans->receiver_id && strpos($descArr[$g],'SENDER_FEES') !== false ){
                                                        continue;
                                                      }	
                                                      if($login_user_id == $trans->user_id && strpos($descArr[$g],'RECEIVER_FEES') !== false ){
                                                        continue;
                                                      } 
                                                      if($login_user_id == $trans->receiver_id && strpos($descArr[$g],'Conversion Fee ') !== false ){
                                                        continue;
                                                      } 

                                                      if($login_user_id == $trans->receiver_id && strpos($descArr[$g],'Conversion Fees :') !== false ){
                                                        continue;
                                                      } 

                                                      if($login_user_id == $trans->user_id && strpos($descArr[$g],'Conversion Fees RECEIVER =') !== false ){
                                                        continue;
                                                      } 
                                                      if($login_user_id == $trans->receiver_id && strpos($descArr[$g],'Conversion Fees =') !== false ){
                                                        continue;
                                                      }	
                                                      if($login_user_id == $trans->user_id && strpos($descArr[$g],'Received') !== false ){
                                                        continue;
                                                      }	
                                                      if($login_user_id == $trans->receiver_id && strpos($descArr[$g],'Sent') !== false ){
                                                        continue;
                                                      }		
                                                      
                                                      
                                                      @endphp
                                                      <span><?php echo $descArr[$g]; ?></span>  
                                        @php
                                        }
                                        @endphp
                                        <?php 
//                                        if(!empty($trans->reference_note)){echo 'Reference: '.$trans->reference_note;}
                                        ?>
                                </div>
                                <div class="">{{$trans->currency.' '.number_format($trans->amount,2,'.',',')}}</div>

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