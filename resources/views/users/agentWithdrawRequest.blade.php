@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-5 border-right">

                    <div class="heading-section ">
                        <h5>Withdrawal</h5>
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

                    <div class="col-sm-6 pad-l-50 mob-big">


                        <div class="trans-hist">
                            <div class="heading-section trans-head">
                                <h5>Transactions</h5> <a href="transaction-history.html">View all</a>
                            </div>
                            <div class="tran-list">
                                @foreach ($trans as $tran)
                                @php
                                if ($tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2) {
                                $res = getUserByUserId($tran->user_id);
                                if ($res->user_type == 'Personal') {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Business') {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));
                                }
                                else if ($res->user_type == 'Agent' && $res->first_name != "") {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Agent' && $res->business_name != "") {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));
                                }
                                }
                                else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1) {
                                $res = getUserByUserId($tran->user_id);
                                if ($res->user_type == 'Personal') {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Business') {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));
                                }
                                else if ($res->user_type == 'Agent' && $res->first_name != "") {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Agent' && $res->business_name != "") {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));
                                } 	
                                }
                                else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 2) {
                                $res = getUserByUserId($tran->receiver_id);
                                if ($res == false) {
                                continue;
                                }
                                else if ($res->user_type == 'Personal') {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Business') {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));
                                }
                                else if ($res->user_type == 'Agent' && $res->first_name != "" )
                                {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
                                }
                                else if ($res->user_type == 'Agent' && $res->business_name != "" )
                                {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)); 
                                }
                                }
                                else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1) {
                                $res = getUserByUserId($tran->receiver_id);
                                if ($res == false) {
                                continue;
                                }
                                else if ($res->user_type == 'Personal') {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Business') {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));
                                }
                                else if ($res->user_type == 'Agent' && $res->first_name != "" )
                                {
                                $transFnm = $res->first_name;
                                $transLnm = $res->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
                                }
                                else if ($res->user_type == 'Agent' && $res->business_name != "" )
                                {
                                $transFnm = $res->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)); 
                                }
                                }
                                else {
                                $res = getUserByUserId($tran->receiver_id);	
                                if ($recordInfo->user_type == 'Personal')	{
                                $transFnm = $recordInfo->first_name;
                                $transLnm = $recordInfo->last_name;	
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
                                }
                                else if ($res->user_type == 'Business') {
                                $transFnm = $recordInfo->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));	
                                }
                                else if ($res->user_type == 'Agent' && $res->first_name != "") {
                                $transFnm = $recordInfo->first_name;
                                $transLnm = $recordInfo->last_name;	
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));	
                                }
                                else if ($res->user_type == 'Agent' && $res->business_name != "") {
                                $transFnm = $recordInfo->business_name;
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = ucfirst(substr($transFnm,0,1));	
                                }
                                }
                                @endphp
                                <div class=" trans-thumb">
                                    <div class="tran-name">
                                        <div class="tran-name-icon">{{$transShortName}}</div>
                                        <div class="trans-name-title">
                                            <h6><a href="{{URL::to('auth/transaction-detail/'.$tran->id)}}">{{$transFnm." ".$transLnm}}</a></h6>
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
                                        @if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != 0 && $tran->trans_type == 2)
                                        <span>Sent</span>
                                        @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1)
                                        <span>Received</span>
                                        @elseif ($tran->user_id != Session::get('user_id') && $tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2)
                                        <span>Received</span>
                                        @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1)
                                        <span>Received</span>
                                        @endif
                                    </div>
                                    <div class="trans-money">
                                        {{$tran->currency}} {{$tran->amount}}
                                        
                                        @php
                                        $date = date_create($tran->created_at);
                                        $transDate = date_format($date,'M, d Y, H:i A');
                                        @endphp
                                        <p style="font-size:11px;">{{$transDate}}</p>
                                    </div>
                                </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function covertCurrency()
        {
            if (document.getElementById("agntWithdrawButn").type != 'submit') {
                var from = '<?php echo $recordInfo->currency; ?>';
                var clientAccntNumber = document.getElementById('accNumbr').value;
                var clientEmail = document.getElementById('accEml').value;
                var amount = document.getElementById('amount').value;

                $.ajax({
                    url: '<?php echo HTTP_PATH; ?>/auth/getCurrencyRateByAccountNumber?from=' + from + '&acctNumbr=' + clientAccntNumber + '&amount=' + amount + "&accEml=" + clientEmail,
                    //dataType: 'jsonp',
                    success: function (data) {
                        //alert(data);
                        if (data == "INVALID_ACCOUNT_EMAIL") {
                            alert("Sorry, no user found with given Account number & Email");
                        } else if (data != false) {
                            var res = data.split("###");

                            document.getElementById('receiver_curr').value = res[1];
                            document.getElementById('conversn_rate').value = res[2];
                            document.getElementById('conversn_amount').value = res[3];

                            var a = confirm("Dear <?php echo $recordInfo->first_name; ?>\nYou are initiating to send " + from + " " + amount + ". " + res[0] + " has different currency i.e. " + res[1] + ". Hence, " + res[0] + " will have to pay " + res[1] + " " + res[3] + " at the rate of " + res[2] + " as per present conversation rate.\n\nPlease confirm");
                            if (a) {
                                sendOTP4AgentTransfer(clientAccntNumber, clientEmail, res[3]);
                                //$('#fundTransfrForm').submit(); 
                            }
                        } else {
                            sendOTP4AgentTransfer(clientAccntNumber, clientEmail, amount);
                        }
                    }
                });
            }
        }

        function sendOTP4AgentTransfer(clientAccntNumber, clientEmail, amount)
        {
            $.ajax({
                url: '<?php echo HTTP_PATH; ?>/auth/sendOTP4AgentTransfer?acctNum=' + clientAccntNumber + '&accEm=' + clientEmail + "&amount=" + amount,
                //dataType: 'jsonp',
                success: function (data) {
                    //alert(data);
                    alert("Please enter OTP we sent to client registered mobile number.");
                    document.getElementById("agntWithdrawButn").type = 'submit';
                }
            });
        }
    </script>
    @endsection