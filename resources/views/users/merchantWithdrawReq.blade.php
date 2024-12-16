@extends('layouts.inner')
@section('content')
<script>
    $(document).ready(function() {
    $(".wid_remark").click(function() {
    var value= $(this).data('val');
    if (value && value != 'na'){
    $('#blank_message').html(value);
    } else{
    $('#blank_message').html('No remark found');
    }
    $('#blank-alert-Modal').modal('show');
    });
    });


</script>
<style>
    .remark_right{
        width: 100%;
        text-align: right;
        padding: 10px 0;
    }
</style>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row ">
                <div class="w100">

                    @php
                    $card_class = getUserCardType($recordInfo->account_category);
                    @endphp
                    <div class="row">

                        <div class=" col-sm-6">
                            <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                            <div class="heading-section wth-head">
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
                        </div>


                        <div class="trans-hist col-sm-6">
                            <div class="heading-section trans-head">
                                <h5>Transactions</h5> <a href="{{URL::to('auth/transactions')}}">View all</a>
                            </div>
                            <div class="tran-list">
                                @if (Count($trans) > 0)
                                @foreach ($trans as $tran)
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
                                        @if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != 0 && $tran->trans_type == 2)
                                        <span>Sent</span>
                                        @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1)
                                        <span>Topup</span>
                                        @elseif ($tran->user_id != Session::get('user_id') && $tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2)
                                        <span>Received</span>
                                        @elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1)
                                        <span>Received</span>
                                        @elseif ($tran->user_id != Session::get('user_id') && $tran->receiver_id == Session::get('user_id') && $tran->trans_type == 1)
                                        <span>Received</span>
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
                            </div>
                        </div>


                    </div>



                    <div class="row">
                        <div class="col-sm-12">

                            <div class="agent-req">
                                <div class="heading-section wth-head">
                                    <h5>Pending Requests</h5>
                                </div>
                                <div class="row">

                                    @foreach($wthdrwReq as $val)
                                    <div class="col-sm-6">
                                        <div class="requst-box">
                                            <div class="tran-name">
                                                <div class="tran-name-icon">D</div>
                                                <div class="trans-name-title">
                                                    {{substr($val->user_name,0,40)}}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="trans-money">
                                                    @php
                                                    $date = date_create($val->created_at);
                                                    $reqDate = date_format($date,'M, d Y, H:i A');

                                                    $user = getUserByUserId($val->user_id);
                                                    @endphp
                                                    <p style="font-size:11px;">{{$reqDate}}</p>
                                                    <span>{{$user->currency.' '.$val->amount}}</span>
                                                </div>
                                            </div>
                                            <div class="remark_right">
                                                    <span class="wid_remark" data-val="<?php echo $val->remark; ?>">View Payout Instructions</span>
                                                </div>
                                            <div class="btn-req">
                                                <a href="#" data-toggle="modal" data-target="#confirmDialog_{{$val->id}}">Accept</a>
                                                <a href="#" data-toggle="modal" data-target="#editDialog_{{$val->id}}">Edit</a>
                                                <a href="#" data-toggle="modal" data-target="#rejectDialog_{{$val->id}}">Reject</a>
                                                <!--<a href="{{URL::to('auth/agent-decline-withdraw-request/'.base64_encode($val->id).'/')}}">Reject</a>-->
                                            </div>
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
    </div>
</div>

<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>

@foreach($wthdrwReq as $val)
@php
$user = getUserByUserId($val->user_id);

if($user->user_type == 'Personal'){
    $ussName = $user->first_name.' '.$user->last_name;
} else if($user->user_type == 'Business'){
    $ussName = $user->business_name;
} else if($user->user_type == 'Agent' && $user->first_name != ''){
    $ussName = $user->first_name.' '.$user->last_name;
} else if($user->user_type == 'Agent' && $user->business_name != ''){
    $ussName = $user->business_name;
}

@endphp
<!-- basic modal -->

<div class="modal fade" id="confirmDialog_{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            {{ Form::open(array('method' => 'post', 'id' => 'agntWithdrawFrm', 'class' => '','onsubmit'=>'return disable_submit();')) }}
            <input type="hidden" name="req" value="{{base64_encode($val->id)}}">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Merchant Withdrawal Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Recipients Name:</label>
                        <input type="text" id="recipName"  value="{{strtoupper($ussName)}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Account No.:</label>
                        <input type="text" value="{{$user->account_number}}" id="recipAccNum" placeholder="6789  4567  2345  6354" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Email:</label>
                        <input type="text" id="recipEmail" value="{{$user->email}}" placeholder="sophicDumond@gamil.com" disabled>
                    </div>

                </div>

                <!-- <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount:</label>
                        <input type="text" value="{{$user->currency.' '.$val->amount}}" id="recipAmountTF" placeholder="" disabled>
                    </div>                    
                </div> -->

                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Amount:</label>
                        <input type="text" value="{{$user->currency.' '.$val->amount}}" id="recipAmountTF" placeholder="" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Fee Charged </label>
                        <div class="pop_amt amt_pnw">
                        <input type="text" value="{{$recordInfo->currency.' '.$val->fees}}" id="recipAmountTFs" placeholder="" disabled>
                        </div>
                    </div>
                </div>


                <div class="filed-box" id="cuncyConvrsnTA" <?php if (empty($val->billing_description)) { ?> style="display:none;" <?php } ?>>
                    <div class="form-control-new w100">
                        <label></label>
                        <textarea type="text" id="recipAmount" rows="7" cols="53" disabled>{{$val->billing_description}}</textarea>
                    </div>                    
                </div>
                
                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to approve this request?
                        </label>
                    </div>  
                    </div>  


            </div>
            <div class="modal-footer pop-ok">
                <button type="submit" class="btn btn-default button_disable">Confirm</button>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<script>
function disable_submit()
    {
    $('.button_disable').prop('disabled', true);
    return true;
    }

</script>    

<!-- <div class="modal x-alert fade" id="confirmDialog_{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-alert-lg">
        <div class="modal-content">
            <div class="modal-body ">
                <p>Do you want to accept this request <br> for below amount</p>
                <p>{{$user->currency.' '.$val->amount}}</p>
                <ul class="list-inline btn-list">
                    
                    <input type="hidden" name="req" value="{{base64_encode($val->id)}}">
                    <li class="list-inline-item"><button type="submit" class="btn btn-dark">Yes</button></li>
                    <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">No</button></li>
                    {{ Form::close() }}
                </ul>
            </div>
        </div>
    </div>
</div> -->

<!-- <div class="modal x-alert fade" id="rejectDialog_{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-alert-lg">
        <div class="modal-content">
            <div class="modal-body ">
                <p>Do you want to reject this request <br> for below amount</p>
                <p>{{$user->currency.' '.$val->amount}}</p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><button type="submit" class="btn btn-dark" onclick="rejectWithdraw('{{base64_encode($val->id)}}')">Yes</button></li>
                    <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">No</button></li>
                </ul>
            </div>
        </div>
    </div>
</div> -->

<div class="modal fade" id="rejectDialog_{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            {{ Form::open(array('method' => 'post', 'id' => 'agntWithdrawFrm', 'class' => '')) }}
            <input type="hidden" name="req" value="{{base64_encode($val->id)}}">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Merchant Withdrawal Rejection</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Recipients Name:</label>
                        <input type="text" id="recipName"  value="{{strtoupper($ussName)}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Account No.:</label>
                        <input type="text" value="{{$user->account_number}}" id="recipAccNum" placeholder="6789  4567  2345  6354" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Email:</label>
                        <input type="text" id="recipEmail" value="{{$user->email}}" placeholder="sophicDumond@gamil.com" disabled>
                    </div>

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount:</label>
                        <input type="text" value="{{$user->currency.' '.$val->amount}}" id="recipAmountTF" placeholder="" disabled>
                    </div>                    
                </div>

                <div class="filed-box" id="cuncyConvrsnTA" <?php if (empty($val->billing_description)) { ?> style="display:none;" <?php } ?>>
                    <div class="form-control-new w100">
                        <label></label>
                        <textarea type="text" id="recipAmount" rows="7" cols="53" disabled>{{$val->billing_description}}</textarea>
                    </div>                    
                </div>
                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to reject this request?
                        </label>
                    </div>  
                    </div>  

            </div>
            <div class="modal-footer pop-ok">
                <button type="button" class="btn btn-default button_disable" onclick="rejectWithdraw('{{base64_encode($val->id)}}')">Confirm</button>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<!-- <div class="modal x-alert fade" id="editDialog_{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-alert-lg">
        <div class="modal-content">
            <div class="modal-body ">
                <p>Do you want to edit this request <br> for below amount</p>
                <p>User request :  {{$user->currency.' '.$val->amount}}</p>
                <label>Send amount :</label> <input type="text" name="amount" value="" id="edit_amount_{{$val->id}}">
                
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><button type="submit" class="btn btn-dark" onclick="editWithdraw('{{$val->id}}')">Yes</button></li>
                    <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">No</button></li>
                </ul>
            </div>
        </div>
    </div>
</div> -->


<div class="modal fade" id="editDialog_{{$val->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            {{ Form::open(array('method' => 'post','url'=>'auth/merchant-edit-withdraw-request', 'id' => 'agntWithdrawFrm1', 'class' => '','onsubmit'=>'return disable_submit();')) }}
            <input type="hidden" name="req" value="{{base64_encode($val->id)}}">
            <input type="hidden" name="id" value="{{$val->id}}">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="{{HTTP_PATH}}/public/img/front/Fundtransfer-thumb.svg"><br>Merchant Withdrawal Edit</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Recipients Name:</label>
                        <input type="text" id="recipName"  value="{{strtoupper($ussName)}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Account No.:</label>
                        <input type="text" value="{{$user->account_number}}" id="recipAccNum" placeholder="6789  4567  2345  6354" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Email:</label>
                        <input type="text" id="recipEmail" value="{{$user->email}}" placeholder="sophicDumond@gamil.com" disabled>
                    </div>

                </div>

                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Request Amount:</label>
                        <input type="text" value="{{$user->currency.' '.$val->amount}}" id="recipAmountTF" placeholder="" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Sending Amount:</label>

                        <div class="pop_amt amt_pnw">
                            <input required="true" type="text" value="" name="edit_amount" id="edit_amount_{{$val->id}}" onkeypress= "return validateFloatKeyPress(this,event);" >
                            <div class="withdraw_currency">{{$user->currency}}</div>
                        </div>
                    </div>
                </div>




                <div class="filed-box" id="cuncyConvrsnTA" <?php if (empty($val->billing_description)) { ?> style="display:none;" <?php } ?>>
                    <div class="form-control-new w100">
                        <label></label>
                        <textarea type="text" id="recipAmount" rows="7" cols="53" disabled>{{$val->billing_description}}</textarea>
                    </div>                    
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to approve this request with updated amount?
                        </label>
                    </div>  
                    </div>  
            </div>
            <div class="modal-footer pop-ok">
                <button type="submit" class="btn btn-default button_disable">Confirm</button>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<style>
    .pop_amt .withdraw_currency{
        top: 10px;
        right: 10px;
    }
    
    .pop_amt{
        position: relative;
    }
</style>
<script>
 window.onload = () => {
 const myInput = document.getElementById('edit_amount_<?php echo $val->id; ?>');
 myInput.onpaste = e => e.preventDefault();
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


    function rejectWithdraw(id){
    $('.button_disable').prop('disabled', true);    
    window.location.href = "<?php echo HTTP_PATH; ?>/auth/merchant-decline-withdraw-request/" + id + "/";
    }
    function editWithdraw(id){
    var edit_amount = jQuery('#edit_amount_' + id).val();
    jQuery.ajax({
    headers: {
    'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
            method: "POST",
            url: "<?php echo HTTP_PATH; ?>/auth/merchant-edit-withdraw-request",
            data: { id: id, edit_amount: edit_amount }
    }).done(function(msg) {
    // window.location.href = "<?php echo HTTP_PATH; ?>/auth/merchant-decline-withdraw-request/"+id+"/";
    alert("Data Saved: " + msg);
    });
    }
</script>
@endforeach
@endsection