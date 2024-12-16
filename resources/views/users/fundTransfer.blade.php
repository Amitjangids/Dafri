@extends('layouts.inner')
@section('content')
<style>
    .fund-name-box h6{
        font-size: 12px !important;
    }
    .btn_sub{
	width: auto;
	display: inline-block;
	padding: 12px 20px;
        margin-bottom: 30px;
        
}

.btn_sub:hover{
    text-decoration: none;
    color: var(--main-white-color);
}
    
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2 w-100">

            <div class="row" ng-app="">
                <div class="col-sm-5 border-right">
                    <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
                    {{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm', 'id' => 'fundTransfrForm', 'class' => '','[formGroup]'=>'formGroup')) }}
                    <div class="heading-section ">
                        <h5>Fund transfer</h5>
                    </div>
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
                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>
                    <div class="deposit-amt">
                        <div class="heading-section">
                            <h5>Payment Details</h5>
                        </div>
                        <div class="drop-text-field"> 
                            {{Form::text('recipient_email_accnum', $user_data, ['placeholder'=>'Enter account number/email address', 'id'=> 'recipient_email_accnum', 'autocomplete'=>'OFF', 'number'=>true, 'required'=>true ])}}
                            <span id="trnsfrRecipientEmailError" style="color:#FF0000;font-size:11px;; display:none;margin-top:7px;">The account email is required.</span>
                        </div>
                        <div class="drop-text-field">
                            {{Form::text('trnsfrAmnt', $slug, ['placeholder'=>'Enter amount', 'id'=> 'trnsfrAmnt', 'autocomplete'=>'OFF', 'number'=>true,  'onkeypress'=>'return validateFloatKeyPress(this,event);' ])}}

                            <div class="withdraw_currency currency_all">{{$recordInfo -> currency}}</div>
                            <input type="hidden" name="trnsfrCurrncy" value="{{$recordInfo -> currency}}">

                            <span id="trnsfrAmntError" style="color:#FF0000;font-size:11px; display:none;">The amount is required.</span>
                        </div>

                        
                          <div class="drop-text-field rn"> 
                          
                           <!-- <textarea placeholder="Reference Note" name="reference_note"></textarea> -->
                           {{Form::textarea('reference_note', null, ['placeholder'=>'Reference Note', 'id'=> 'reference_note','maxlength'=>'50','style'=>'resize: none;'])}}
                        </div>
                        <!-- <div class="drop-text-field"> 
                        {{Form::text('recipient_accntNumbr', null, ['placeholder'=>'Enter account number', 'id'=> 'recipient_accntNumbr', 'autocomplete'=>'OFF', 'number'=>true, 'required'=>true ])}}
                        <span id="trnsfrRecipientAccNumbrError" style="color:#FF0000;font-size:11px;; display:none;margin-top:7px;">The account number is required.</span>
                        </div>
                         <div class="drop-text-field"> 
                        {{Form::text('recipient_email', null, ['placeholder'=>'Enter email', 'id'=> 'recipient_email', 'autocomplete'=>'OFF', 'number'=>true, 'required'=>true ])}}
                        <span id="trnsfrRecipientEmailError" style="color:#FF0000;font-size:11px;; display:none;margin-top:7px;">The account email is required.</span>
                        </div> -->


                        <div class="form-group check-field-box mt-3">
                            <div class=" check-new">
                                <input type="checkbox" id="sendProof" name="sendProof">
                                <label for="sendProof">send copy of the proof</label>
                            </div>
                            <!-- 	{{Form::checkbox('sendProof', null,['id'=>'sendProof','value'=>'yes'])}}
                            send copy of the proof -->
                        </div>
                    </div>
                    <div class="btn-box">
                        <input type="hidden" name="receiver_curr" id="receiver_curr" value="-1">
                        <input type="hidden" name="conversn_rate" id="conversn_rate" value="-1">
                        <input type="hidden" name="conversn_amount" id="conversn_amount" value="-1">
                        <button class="sub-btn" onclick="isExists();" type="button">Pay now</button>
                        <a class="sub-btn btn_sub" href="{{URL::to('auth/beneficiary-list')}}">Beneficiary List</a>
                    </div>
                    {{ Form::close() }}
                </div>
                <div class="col-sm-7 pd-0">
                    <div class="recipients-box-main">
                        <div class="heading-section mt-0 pt-33">
                            <h5>Recent recipients</h5>
                        </div>
                        @foreach ($recentRecipient as $recip)
                        @php
                        $res = getUserByUserId($recip->receiver_id);
                        if ($res->user_type == 'Personal') {
                        $receipientName = $res->first_name." ".$res->last_name; 
                        $transShortName = strtoupper(substr($res->first_name,0,1)).strtoupper(substr($res->last_name,0,1));
                        }
                        else if ($res->user_type == 'Business') {
                        $receipientName = $res->business_name;
                        $transShortName = strtoupper(substr($res->business_name,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->first_name != "") {
                        $receipientName = $res->first_name." ".$res->last_name;
                        $transShortName = strtoupper(substr($res->first_name,0,1)).strtoupper(substr($res->last_name,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->business_name != "") {
                        $receipientName = $res->business_name;
                        $transShortName = strtoupper(substr($res->business_name,0,1));
                        }
                        @endphp
                        <div class="recipients-box">
                            <div class="recipients-img-box">
                                <div class="tran-name-icon">{{$transShortName}}</div>
                            </div>
                            <div class="recipients-name-box fund-name-box">
                                <h6>{{strtoupper($receipientName)}}</h6>
                                <span>Account ending {{substr($res->account_number, 6, 4)}}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- <div class="view-rs">
                    <div class="add-new-recipients ">
                    <a href="{{URL::to('auth/add-recipient')}}">Add new recipients {{HTML::image('public/img/front/add_new_recipients.svg', 'Add new recipients')}}</a>
                    </div>
                    <div class="add-new-recipients ">
                    <a href="{{URL::to('auth/my-recipients')}}">View all recipients {{HTML::image('public/img/front/view-res.svg', 'View all recipients')}}</a>
                    </div>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="wrapper2">
            <div class="row">
                <div class="trans-hist col-sm-12">
                    <div class="heading-section trans-head">
                        <h5>Transaction history</h5> <a href="{{URL::to('auth/transactions')}}">View all</a>
                    </div>
                    <div class="tran-list">
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
                        if ($res->user_type == 'Personal') {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                        }
                        else if ($res->user_type == 'Business') {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->first_name != "")
                        {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        else if ($res->user_type == 'Agent' && $res->business_name != "")
                        {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        }
                        else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1) {
                        $res = getUserByUserId($tran->user_id);
                        if ($res->user_type == 'Personal') {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                        }
                        else if ($res->user_type == 'Business') {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->first_name != "")
                        {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        else if ($res->user_type == 'Agent' && $res->business_name != "")
                        {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }	
                        }
                        else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id > 0 && $tran->trans_type == 2) {
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
                        else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1) {
                        $res = getUserByUserId($tran->receiver_id);
                        if ($res == false) {
                        continue;
                        }
                        else if ($res->user_type == 'Personal') {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                        }
                        else if ($res->user_type == 'Business') {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->first_name != "")
                        {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        else if ($res->user_type == 'Agent' && $res->business_name != "")
                        {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }	
                        }
                        else if ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                        $res = getUserByUserId($tran->user_id);
                        if ($res->user_type == 'Personal') {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                        }
                        else if ($res->user_type == 'Business') {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->first_name != "")
                        {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        else if ($res->user_type == 'Agent' && $res->business_name != "")
                        {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        }
                        else {
                        $res = getUserByUserId($tran->receiver_id);	
                        if ($recordInfo->user_type == 'Personal')	{
                        $transFnm = $recordInfo->first_name;
                        $transLnm = $recordInfo->last_name;	
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                        }
                        else if ($res->user_type == 'Business') {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1));
                        }
                        else if ($res->user_type == 'Agent' && $res->first_name != "")
                        {
                        $transFnm = $res->first_name;
                        $transLnm = $res->last_name;
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        else if ($res->user_type == 'Agent' && $res->business_name != "")
                        {
                        $transFnm = $res->business_name;
                        $transLnm = "";
                        $transName = $transFnm." ".$transLnm;
                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1)); 
                        }
                        }
                        @endphp
                        @endif
                        <div class=" trans-thumb">
                            <div class="tran-name">
                                <div class="tran-name-icon">{{$transShortName}}</div>
                                <div class="trans-name-title">
                                    <h6><a href="{{URL::to('auth/transaction-detail/'.$tran->id)}}">{{strtoupper($transFnm." ".$transLnm)}}</a></h6>
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
                                {{$tran -> currency}} {{$tran -> amount}}
                                @php
                                $date = date_create($tran->created_at);
                                $transDate = date_format($date,'M, d Y, H:i A');
                                @endphp
                                <p style="font-size:11px;">{{$transDate}}</p>
                                <p style="font-size:11px;"><a style="color: #fff;" href="{{URL::to('auth/transaction-detail/'.$tran->id)}}"><button style="background-color: #000;color: #fff;">View</button></a></p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<!-- basic modal -->
<!-- <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Fund Transfer</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Recipients Name:</label>
                        <input style="text-transform: uppercase;" type="text" id="recipName" value="" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Account No.:</label>
                        <input type="text" id="recipAccNum" placeholder="6789  4567  2345  6354" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Email:</label>
                        <input type="text" id="recipEmail" placeholder="sophicDumond@gamil.com" disabled>
                    </div>

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount:</label>
                        <input type="text" id="recipAmountTF" placeholder="">
                    </div>                    
                </div>

                <div class="filed-box" id="cuncyConvrsnTA" style="display:none;">
                    <div class="form-control-new w100">
                        <label></label>
                        <textarea type="text" id="recipAmount" rows="7" cols="53" disabled></textarea>
                    </div>                    
                </div>


            </div>
            <div class="modal-footer pop-ok">
                <button type="button" class="btn btn-default button_disable" onclick="btn_disable()">Confirm</button>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> -->

<div class="modal" id="basicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="basicModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                        <div class="popup-form">
                            <div class="pop-logo">
                            <img src="{{HTTP_PATH}}/public/img/dafribank-logo-mobile.svg">
                        </div>
                           
                           <div class="popup-info">
                               <div class="popup-info-data">
                                   <span class="label-mini">N:</span> <span id="recipName"></span>
                               </div>

                               <div class="popup-info-data">
                                   <span class="label-mini">A:</span> <span id="recipAccNum"></span>
                               </div>

                               <div class="popup-info-data">
                                   <span class="label-mini">E:</span> <span id="recipEmail"></span>
                               </div>

                               <div class="popup-info-data" id="cuncyConvrsnTF">
                               <span id="recipAmountTF"></span>
                               </div>

                               <div class="popup-info-data mb-4 dis-data mt-2" id="cuncyConvrsnTA">
                               <span id="recipAmount"></span>
                               </div>

                           </div>
                        <div class="form-btns-pop">
                            <button type="button" class="confrm-btn btn btn-default button_disable" onclick="btn_disable()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<style>
    .modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1060;
    display: none;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}
.modal.fade .modal-dialog {
    transition: transform .3s ease-out;
    transform: translate(0,-50px);
}
.pop1 .modal-dialog {
    max-width: 598px;
}
.modal-dialog {
    margin: 10px auto !important;
    padding: 0 10px;
}
.modal-content {
    border-radius: 15px;
}
.popup-form {
    padding: 20px 40px;
}
.pop-logo {
    text-align: center;
    margin-bottom: 30px;
}
.popup-info-data {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 500;
}
.popup-info-data .label-mini {
    color: #1deb8d;
}
.form-btns-pop {
    text-align: center;
}
.confrm-btn {
    font-size: 16px;
    background: #000;
    padding: 8px 20px;
    border-radius: 5px;
    text-transform: capitalize;
    color: #fff;
    border: 1px solid #000;
    cursor: pointer;
}
</style>


<script>
function btn_disable()
{   
$('.button_disable').prop('disabled',true);    
$('#fundTransfrForm').submit();
}

function btn_disable1()
{   
$('.button_disable').prop('disabled',true);    
covertCurrency();
}

/*
$('#reference_note').on('keypress', function (event) {
    var regex = new RegExp("^[a-zA-Z0-9]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
       event.preventDefault();
       return false;
    }
}); */
</script>    



<!-- General popup (Receiver Exists) End -->

<!-- popup (Receiver NOT Exists) Start -->

<!-- basic modal -->
<div class="modal x-dialog fade" id="basicModal2" tabindex="-1" role="dialog" aria-labelledby="basicModal2" aria-hidden="true">
    <!--    <div class="modal-dialog md1">
            <div class="modal-content transfer-pop">
                <div class="transfer-fund-pop">
                    <h4 class="text-center mb-3">Fund Transfer</h4>
                    <div class="filed-box">
                        <div class="form-control-new w100">
                            <label>Email:</label>
                            <input type="hidden" id="recevrNotExtEmail" placeholder="sophicDumond@gamil.com" disabled>
                        </div>
    
                    </div>
    
                    <div class="filed-box" id="cuncyConvrsnTF">
                        <div class="form-control-new w100">
                            <label>Amount:</label>
                            <input type="hidden" id="recevrNotExtAmnt" placeholder="">
                        </div>                    
                    </div>
    
                    <div class="filed-box" id="cuncyConvrsnTA">
                        <div class="form-control-new w100">
                            <label></label>
                            <textarea type="text" id="recevrNotExtMsg" rows="11" cols="53" disabled></textarea>
                        </div>                    
                    </div>
    
                </div>
                <div class="modal-footer pop-ok">
                    <button type="button" class="btn btn-default" onclick="covertCurrency();">Confirm</button>
                    <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>-->

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body ">
                {{HTML::image('public/img/front/dafribank-logo-black.svg', SITE_TITLE)}}
                <br><br>
                <p><strong id="pop_hedname">Dear Andrew Scotch,</strong></p>
                <p>You are about to send <strong id="amount_sett">ZAR 200</strong> to a recipient without a DafriBank Digital account. </p>
                <p>The transaction will remain pending until the recipient opens a DafriBank Account with the above email address to accept the funds.</p>
                <p>The funds will be automatically reversed to your DafriBank Account should the recipient fail to accept it within the next 30 days.</p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><button type="button" id="myButton" class="btn btn-dark button_disable" onclick="btn_disable1();"><i class="check_payment_status"></i> Confirm</button></li>
                    <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- popup (Receiver NOT Exists) End -->



<script src="<?php echo HTTP_PATH;?>/public/assets/js/front/top_search.js"></script>
<script>
                                                      window.onload = () => {
 const myInput = document.getElementById('trnsfrAmnt');
 myInput.onpaste = e => e.preventDefault();
}       
                                                 
                                                    function validateEmail(email) {
                                                        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                                        return re.test(email.toLowerCase());
                                                    }

                                                    function isExists()
                                                    {
                                                        var from = '<?php echo $recordInfo->currency; ?>';
                                                        var amount = document.getElementById('trnsfrAmnt').value;
                                                        var accNumberEmail = document.getElementById('recipient_email_accnum').value;
                                                        $.ajax({
                                                            url: '<?php echo HTTP_PATH;?>/auth/checkUserExists?accNumberEmail=' + accNumberEmail,
//dataType: 'jsonp',
                                                            success: function (data) {
                                                                if (data == "INVALID_ACCOUNT_EMAIL") {
                                                                    $('#basicModal2').modal('hide');
                                                                    $('#failed_message').html('Please verify your information and try again.');
                                                                    $('#failed-alert-Modal').modal('show');
//                                                                    alert("Invalid Account number/Email address");
                                                                } else if (data == false) {
<?php
if ($recordInfo->user_type == "Personal") {
    $user_name = $recordInfo->first_name . ' ' . $recordInfo->last_name;
} else if ($recordInfo->user_type == "Business") {
    $user_name = $recordInfo->business_name;
} else if ($recordInfo->user_type == "Agent" && $recordInfo->first_name == "") {
    $user_name = $recordInfo->business_name;
} else if ($recordInfo->user_type == "Agent" && $recordInfo->first_name != "") {
    $user_name = $recordInfo->first_name . ' ' . $recordInfo->last_name;
}
?>
                    
//                    document.getElementById('recevrNotExtEmail').value = document.getElementById('recipient_email_accnum').value;
//                    document.getElementById('recevrNotExtAmnt').value = document.getElementById('trnsfrAmnt').value;
$('#basicModal2').modal('show'); 
                                                                    document.getElementById('pop_hedname').innerHTML = 'Dear <?php echo strtoupper($user_name); ?>';
                                                                    document.getElementById('amount_sett').innerHTML = from+' '+document.getElementById('trnsfrAmnt').value;
//                    document.getElementById('recevrNotExtMsg').value = "Dear <?php //echo $user_name; ?>,\n\nYou are about to send " + from + " " + amount + ". to a recipient without a DafriBank Digital account.\n\nThe transaction will remain pending until the recipient opens a DafriBank Account with the above email address to accept the funds.\n\nThe funds will be automatically reversed to your DafriBank Account should the recipient fail to accept it within the next 30 days.\n\nT&Cs Apply\n\nConfirm Payment";

                                                                    
                                                                    //var a = confirm("Dear <?php //echo $user_name; ?>,\nYou are initiating transaction to send "+from+" "+amount+". This transaction will remain pending until the receiver open the DafriBank Digital account. This amount will be refunded to you if receiver do not open DafriBank Digital account within the next 30 days.");
                                                                    /*if (a){
                                                                     covertCurrency(); //continueFlag = true;	
                                                                     }*/
                                                                    //document.getElementById('recipEmail').value = accNumberEmail;
                                                                    //document.getElementById('recipAmount').value = 'Dear Andrew,\nYou are initiating transaction to send '+from+' '+amount+'. This transaction will remain pending until the receiver oprn the DafriBank Digital account. This amount will be refunded to you if receiver do not open DafriBank Digital account within the next 30 days.';
                                                                    //$('#basicModal2').modal('show'); 
                                                                } else {
                                                                    covertCurrency(); //continueFlag = true;
                                                                }
                                                            }
                                                        });
                                                    }
                                                       

                                                    function covertCurrency()
                                                    {
                                                        //document.getElementById("myButton").disabled = true;
                                                        document.getElementById('trnsfrAmntError').style.display = 'none';
                                                        document.getElementById('trnsfrRecipientEmailError').style.display = 'none';
//document.getElementById('trnsfrRecipientAccNumbrError').style.display = 'none';
//document.getElementById('trnsfrRecipientEmailError').style.display = 'none';
                                                        if (document.getElementById('trnsfrAmnt').value == "")
                                                        {
                                                            document.getElementById('trnsfrAmntError').innerHTML = 'The amount is required';
                                                            document.getElementById('trnsfrAmntError').style.display = 'block';
                                                            document.getElementById('trnsfrAmnt').focus();
                                                        } else if (isNaN(document.getElementById('trnsfrAmnt').value))
                                                        {
                                                            document.getElementById('trnsfrAmntError').innerHTML = 'Invalid amount! Use number only.';
                                                            document.getElementById('trnsfrAmntError').style.display = 'block';
                                                            document.getElementById('trnsfrAmnt').focus();
                                                        } else if (document.getElementById('recipient_email_accnum').value == "")
                                                        {
                                                            document.getElementById('trnsfrRecipientEmailError').innerHTML = 'Invalid account number/email value.';
                                                            document.getElementById('trnsfrRecipientEmailError').style.display = 'block';
                                                            document.getElementById('recipient_email_accnum').focus();
                                                        }
                                                        /*else if (document.getElementById('recipient_email').value == "" || !validateEmail(document.getElementById('recipient_email').value))
                                                         {
                                                         document.getElementById('trnsfrRecipientEmailError').innerHTML = 'Invalid account email!';
                                                         document.getElementById('trnsfrRecipientEmailError').style.display = 'block';
                                                         document.getElementById('recipient_email').focus();  
                                                         }*/
                                                        else {
                                                            var from = '<?php echo $recordInfo->currency; ?>';
//var accNumber = document.getElementById('recipient_accntNumbr').value;
//var accEmail = document.getElementById('recipient_email').value;
                                                            var accNumberEmail = document.getElementById('recipient_email_accnum').value;
                                                            var amount = document.getElementById('trnsfrAmnt').value;
                                                            if (document.getElementById('sendProof').checked == true) {
                                                                var sendFlag = 'send';
                                                            } else {
                                                                var sendFlag = 'not_send';
                                                            }
                                                            var refrence = document.getElementById('reference_note').value;
                                                            //alert("Send Flag: "+sendFlag);
                                                            $('.check_payment_status').addClass('fa fa-refresh fa-spin',true);
                                                            $.ajax({
                                                       
                                                                url: '<?php echo HTTP_PATH;?>/auth/getCurrencyRate?from=' + from + '&accNumberEmail=' + accNumberEmail + '&amount=' + amount + '&sendFlag=' + sendFlag + '&reference_note=' + refrence,
//dataType: 'jsonp',
                                                                success: function (data) {
//alert(data);
//document.getElementById("myButton").disabled = false;
$('.check_payment_status').removeClass('fa fa-refresh fa-spin');
$('.button_disable').removeAttr('disabled');

                                                                    $dataArr = data.split('###');
                                                                    if ($dataArr[0] == "Invitaion_Done") {
                                                                        var b64TransID = $dataArr[1];
                                                                        var b64RefID = $dataArr[2];

                                                                        location.href = '<?php echo HTTP_PATH;?>/auth/transfer-success/' + b64TransID + '/' + b64RefID;
                                                                    } 
                                                                    else if($dataArr[0] == "Insufficient_Balance_daly")
                                                                    {
                                                                         var msg = $dataArr[1];
                                                                         $('#basicModal2').modal('hide');
                                                                         $('#error_message').html(msg);
                                                                         $('#error-alert-Modal').modal('show');
                                                                         $('.button_disable').prop('disabled',false); 
                                                                    }
                                                                    else if (data == "Insufficient_Balance") {
                                                                        $('#basicModal2').modal('hide');
                                                                        $('#error_message').html('Insufficient Balance');
                                                                    $('#error-alert-Modal').modal('show');
//                                                                        alert("Insufficient Balance");
                                                                        document.getElementById('trnsfrAmnt').focus();
                                                                    } else if (data == "INVALID_ACCOUNT_EMAIL") {
                                                                        $('#basicModal2').modal('hide');
                                                                        $('#error_message').html('Please verify your information and try again.');
                                                                    $('#error-alert-Modal').modal('show');
//                                                                        alert("Invalid Account number/Email!");
                                                                    } else if (data == "Invitaion_Done") {
//                                                                        alert("Invitation Done!!");
                                                                        $('#basicModal2').modal('hide');
                                                                        $('#success_message').html('Invitation Done!!');
                                                                    $('#success-alert-Modal').modal('show');
                                                                    } else if (data != false && data != "INVALID_ACCOUNT_EMAIL") {
                                                                        var res = data.split("###");
                                                                        console.log(res);
                                                                        if (res[2] == '0.00') {
                                                                            document.getElementById('cuncyConvrsnTF').style.display = 'block';
                                                                            document.getElementById('cuncyConvrsnTA').style.display = 'none';

                                                                            $('#recipName').html(res[0]);
                                                                            $('#recipAccNum').html(res[5]);
                                                                            $('#recipEmail').html(res[4]);
                                                                          
                                                                            $('#receiver_curr').val('-1');
                                                                            $('#conversn_rate').val('-1');
                                                                            $('#conversn_amount').val('-1');

                                                                            var msg="You are about to pay "+ from+" "+ amount +" to "+ res[0].toUpperCase()+". Our charge "+from+" "+res[6] +" of the total amount. Please click confirm to proceed.";
                                                                            $('#recipAmountTF').html(msg);
                                                                            $('#basicModal').modal('show');
                                                                        } else {
                                                                            document.getElementById('receiver_curr').value = res[1];
                                                                            document.getElementById('conversn_rate').value = res[2];
                                                                            document.getElementById('conversn_amount').value = res[3];

                                                                            var msg = "You are about to transfer " + from + " " + amount + " to " + res[0].toUpperCase() + " who has a different currency from yours. " + res[0].toUpperCase() + " will receive " + res[1] + " " + res[3] + " at a conversion rate of " + res[2] + ". You will be charged "+from+" "+res[6]+" inclusive conversion fee.\nPlease click confirm to proceed.";

                                                                            document.getElementById('cuncyConvrsnTF').style.display = 'none';
                                                                            document.getElementById('cuncyConvrsnTA').style.display = 'block';

                                                                            $('#recipName').html(res[0]);
                                                                            $('#recipAccNum').html(res[5]);
                                                                            $('#recipEmail').html(res[4]);
                                                                            $('#recipAmount').html(msg);
                                                                            $('#basicModal').modal('show');
                                                                        }
//var a = confirm("Dear <?php echo $recordInfo->first_name; ?>\nYou are initiating to send "+from+" "+amount+". "+res[0]+" has different currency i.e. "+res[1]+". Hence, "+res[0]+" will receive "+res[1]+" "+res[3]+" at the rate of "+res[2]+" as per present conversion rate.\n\nPlease confirm");
//if (a) {
// $('#fundTransfrForm').submit(); 
//}
                                                                    } else {
                                                                        $('.button_disable').prop('disabled',true); 
                                                                        $('#fundTransfrForm').submit();
                                                                    }
                                                                }
                                                            });

                                                        }
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

                                                    function getSelectionStart(o) {
                                                        if (o.createTextRange) {
                                                            var r = document.selection.createRange().duplicate()
                                                            r.moveEnd('character', o.value.length)
                                                            if (r.text == '')
                                                                return o.value.length
                                                            return o.value.lastIndexOf(r.text)
                                                        } else
                                                            return o.selectionStart
                                                    }
</script>

@endsection