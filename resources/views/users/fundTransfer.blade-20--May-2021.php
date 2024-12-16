@extends('layouts.inner')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="d-flex" id="wrapper">
@include('elements.side_menu')
<!-- Page Content -->
<div id="page-content-wrapper">
<div class="head-bar">
<div class="wrapper2">
<button class="btn btn-mobile" id="menu-toggle"><span class="navbar-toggler-icon">{{HTML::image('public/img/front/bars.svg', SITE_TITLE)}}</span></button>
<div class="row">
<div class="col-sm-6">
<div class="search-head">
{{HTML::image('public/img/front/search.svg', SITE_TITLE)}}<input type="text" name="search_q" id="search_q" placeholder="Search" onchange="goTo(this.value);">
</div>
</div>
<div class="col-sm-6">
<div class="noti-right-bar">
<div class="profile-top-bar">
<a href="{{URL::to('auth/my-account')}}">
<div class="pro-top-bar-img">
@if(isset($recordInfo->profile_image))
{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE)}}
@else
{{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
@endif
</div>
@if($recordInfo->user_type == 'Personal')
<span>Hi, {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</span>
@elseif($recordInfo->user_type == 'Business')
<span>Hi, {{ucwords($recordInfo->director_name)}}</span>
@elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
<span>Hi, {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</span>
@elseif($recordInfo->user_type == 'Agent' && $recordInfo->director_name != "")
<span>Hi, {{ucwords($recordInfo->director_name)}}</span>
@endif
</a>
</div>
<a href="{{URL::to('auth/notifications')}}">{{HTML::image('public/img/front/bell.svg', SITE_TITLE)}}</a>
</div>
</div>
</div>
</div>
</div>
<div class="wrapper2 w-100">

<div class="row" ng-app="">
<div class="col-sm-5 border-right">
<div class="ersu_message">@include('elements.errorSuccessMessage')</div>
{{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm', 'id' => 'fundTransfrForm', 'class' => '','[formGroup]'=>'formGroup')) }}
<div class="heading-section ">
<h5>Fund transfer</h5>
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
<div class="deposit-amt">
<div class="heading-section">
<h5>Amount</h5>
</div>
<div class="drop-text-field">
{{Form::text('trnsfrAmnt', null, ['placeholder'=>'Enter amount', 'id'=> 'trnsfrAmnt', 'autocomplete'=>'OFF', 'number'=>true, 'required'=>true, 'ng-model' => 'trnsfrAmnt' ])}}
<?php	
$currncyList = array($recordInfo->currency=>$recordInfo->currency);
?>
{{Form::select('trnsfrCurrncy', $currncyList,null, ['class' => 'dropdown-arrow', 'id' => 'trnsfrCurrncy'])}}

<span id="trnsfrAmntError" style="color:#FF0000;font-size:11px; display:none;">The amount is required.</span>
</div>
<div class="drop-text-field"> 
{{Form::text('recipient_accntNumbr', null, ['placeholder'=>'Enter account number', 'id'=> 'recipient_accntNumbr', 'autocomplete'=>'OFF', 'number'=>true, 'required'=>true ])}}
<span id="trnsfrRecipientAccNumbrError" style="color:#FF0000;font-size:11px;; display:none;margin-top:7px;">The account number is required.</span>
</div>
<div class="drop-text-field"> 
{{Form::text('recipient_email', null, ['placeholder'=>'Enter email', 'id'=> 'recipient_email', 'autocomplete'=>'OFF', 'number'=>true, 'required'=>true ])}}
<span id="trnsfrRecipientEmailError" style="color:#FF0000;font-size:11px;; display:none;margin-top:7px;">The account email is required.</span>
</div>


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
<button class="sub-btn" onclick="covertCurrency();" type="button">Pay now</button>
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
}
else if ($res->user_type == 'Business') {
$receipientName = $res->director_name; 
}
else if ($res->user_type == 'Agent' && $res->first_name != "") {
$receipientName = $res->first_name." ".$res->last_name; 
}
else if ($res->user_type == 'Agent' && $res->director_name != "") {
$receipientName = $res->director_name; 
}
@endphp
<div class="recipients-box">
<div class="recipients-img-box">
@if($res->profile_image != "" || $res->profile_image != Null)
{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$res->profile_image, SITE_TITLE)}}
@else
{{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
@endif
</div>
<div class="recipients-name-box">
<h6>{{$receipientName}}</h6>
<span>Account ending {{substr($res->account_number,6,4)}}</span>
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
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1));
}
else if ($res->user_type == 'Agent' && $res->first_name != "")
{
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}
else if ($res->user_type == 'Agent' && $res->director_name != "")
{
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
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
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1));
}
else if ($res->user_type == 'Agent' && $res->first_name != "")
{
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}
else if ($res->user_type == 'Agent' && $res->director_name != "")
{
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}	
}
else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id > 0 && $tran->trans_type == 2) {
$res = getUserByUserId($tran->receiver_id);
if ($res != false && $res->user_type == 'Personal') {
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
}
else if ($res != false && $res->user_type == 'Business') {
$transFnm = $res->director_name;
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
else if ($res != false && $res->user_type == 'Agent' && $res->director_name != "") {
$transFnm = $res->director_name;
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
else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2) {
$res = getUserByUserId($tran->user_id);
if ($res != false && $res->user_type == 'Personal') {
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
}
else if ($res != false && $res->user_type == 'Business') {
$transFnm = $res->director_name;
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
else if ($res != false && $res->user_type == 'Agent' && $res->director_name != "") {
$transFnm = $res->director_name;
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
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1));
}
else if ($res->user_type == 'Agent' && $res->first_name != "")
{
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}
else if ($res->user_type == 'Agent' && $res->director_name != "")
{
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}	
}
else if ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
$res = getUserByUserId($tran->user_id);
if ($res->user_type == 'Personal') {
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1));
}
else if ($res->user_type == 'Business') {
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1));
}
else if ($res->user_type == 'Agent' && $res->first_name != "")
{
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}
else if ($res->user_type == 'Agent' && $res->director_name != "")
{
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
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
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1));
}
else if ($res->user_type == 'Agent' && $res->first_name != "")
{
$transFnm = $res->first_name;
$transLnm = $res->last_name;
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
}
else if ($res->user_type == 'Agent' && $res->director_name != "")
{
$transFnm = $res->director_name;
$transLnm = "";
$transName = $transFnm." ".$transLnm;
$transShortName = ucfirst(substr($transFnm,0,1)).ucfirst(substr($transLnm,0,1)); 
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
@elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2)
<span>Withdraw</span>
@elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1)
<span>Received</span>
@elseif ($tran->user_id != Session::get('user_id') && $tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2)
<span>Received</span>
@elseif ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1)
<span>Received</span>
@elseif ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent")
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
<!-- /#page-content-wrapper -->
</div>
<!-- basic modal -->
<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
<div class="modal-dialog md1">
<div class="modal-content transfer-pop">
<div class="transfer-fund-pop">
<h4 class="text-center mb-3">Fund Transfer</h4>
<div class="filed-box">
<div class="form-control-new">
<label>Recipients Name:</label>
<input type="text" id="recipName" value="" disabled>
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
<button type="button" class="btn btn-default" onclick="$('#fundTransfrForm').submit();">Confirm</button>
<button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
</div>
</div>
</div>
</div>
<script src="/dafri/public/assets/js/front/top_search.js"></script>
<script>
function validateEmail(email) {
var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
return re.test(email.toLowerCase());
}

function covertCurrency()
{
document.getElementById('trnsfrAmntError').style.display = 'none';
document.getElementById('trnsfrRecipientAccNumbrError').style.display = 'none';
document.getElementById('trnsfrRecipientEmailError').style.display = 'none';
if (document.getElementById('trnsfrAmnt').value == "")
{
document.getElementById('trnsfrAmntError').innerHTML = 'The amount is required';
document.getElementById('trnsfrAmntError').style.display = 'block';
document.getElementById('trnsfrAmnt').focus();
}
else if (isNaN(document.getElementById('trnsfrAmnt').value))
{
document.getElementById('trnsfrAmntError').innerHTML = 'Invalid amount! Use number only.';
document.getElementById('trnsfrAmntError').style.display = 'block';
document.getElementById('trnsfrAmnt').focus();  
}
else if (document.getElementById('recipient_accntNumbr').value == "")
{
document.getElementById('trnsfrRecipientAccNumbrError').innerHTML = 'The account number is required.';
document.getElementById('trnsfrRecipientAccNumbrError').style.display = 'block';
document.getElementById('recipient_accntNumbr').focus();  
}
else if (document.getElementById('recipient_email').value == "")
{
document.getElementById('trnsfrRecipientEmailError').innerHTML = 'The account email is required.';
document.getElementById('trnsfrRecipientEmailError').style.display = 'block';
document.getElementById('recipient_email').focus();  
}
else if (!validateEmail(document.getElementById('recipient_email').value))
{
document.getElementById('trnsfrRecipientEmailError').innerHTML = 'Invalid account email!';
document.getElementById('trnsfrRecipientEmailError').style.display = 'block';
document.getElementById('recipient_email').focus();  
}
else {
var from = '<?php echo $recordInfo->currency;?>';
var accNumber = document.getElementById('recipient_accntNumbr').value;
var accEmail = document.getElementById('recipient_email').value;
var amount = document.getElementById('trnsfrAmnt').value;	
//alert(to);  
$.ajax({
url: '/dafri/auth/getCurrencyRate?from='+from+'&accNumber='+accNumber+'&accEmail='+accEmail+'&amount='+amount,
//dataType: 'jsonp',
success: function(data) {
//alert(data);
if (data == "INVALID_ACCOUNT_EMAIL") {
alert("Invalid Account number/Email!");
}
else if (data != false && data != "INVALID_ACCOUNT_EMAIL") {
var res = data.split("###");

if (res[2] == '0.00') {
document.getElementById('cuncyConvrsnTF').style.display = 'block';
document.getElementById('cuncyConvrsnTA').style.display = 'none';

document.getElementById('recipName').value = res[0];
document.getElementById('recipAccNum').value = accNumber;
document.getElementById('recipEmail').value = accEmail;
document.getElementById('recipAmountTF').value = res[1]+' '+amount;
$('#basicModal').modal('show');  
}
else {
document.getElementById('receiver_curr').value = res[1];		  
document.getElementById('conversn_rate').value = res[2];			
document.getElementById('conversn_amount').value = res[3];

var msg = "Dear <?php echo $recordInfo->first_name;?>\nYou are initiating to send "+from+" "+amount+". "+res[0]+" has different currency i.e. "+res[1]+". Hence, "+res[0]+" will receive "+res[1]+" "+res[3]+" at the rate of "+res[2]+" as per present conversion rate.\n\nPlease confirm";

document.getElementById('cuncyConvrsnTF').style.display = 'none';
document.getElementById('cuncyConvrsnTA').style.display = 'block';

document.getElementById('recipName').value = res[0];
document.getElementById('recipAccNum').value = accNumber;
document.getElementById('recipEmail').value = accEmail;
document.getElementById('recipAmount').value = msg;
$('#basicModal').modal('show');
}
//var a = confirm("Dear <?php echo $recordInfo->first_name;?>\nYou are initiating to send "+from+" "+amount+". "+res[0]+" has different currency i.e. "+res[1]+". Hence, "+res[0]+" will receive "+res[1]+" "+res[3]+" at the rate of "+res[2]+" as per present conversion rate.\n\nPlease confirm");
//if (a) {
// $('#fundTransfrForm').submit(); 
//}
}
else {
$('#fundTransfrForm').submit();	
}
}
});
}
}
</script>
@endsection