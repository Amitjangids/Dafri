@extends('layouts.inner')
@section('content')
{{ HTML::script('public/assets/js/jquery.validate.js')}}
<style>
    .txtcpy1{
        font-size: 10px;
    }
    .txtcpy2{
         font-size: 10px;
    }
</style>
<script type="text/javascript">

    $("#srchAgntFrm1").validate();
    
    function payonclick(id){ 
        $('.button_disable').prop('disabled', true);   
        location.href = '<?php echo HTTP_PATH; ?>/auth/saveWithdrawRequest/'+id;  
    }
</script>
<div class="modal fade " id="basicModal1" tabindex="-1" role="dialog" aria-labelledby="basicModal1" aria-hidden="true">
    <div class="modal-dialog model-trans">
        <div class="modal-content">
            {{ Form::open(array('method' => 'post', 'name' =>'srchAgntFrm1', 'id' => 'srchAgntFrm1')) }}
            <div class="trans-dettail-pop">
                <h5>Bank Agent Withdrawal</h5>

                <div class="drop-text-field">
                    <label>Amount</label>
                    <div class="p-relative">
                        <input type="text" class="required" id="withdrawAmt" name="withdrawAmnt" value="{{base64_decode(Session::get('withdrawAmntAgnt64'))}}" placeholder="Enter amount" autocomplete="OFF" onkeypress="return validateFloatKeyPress(this, event);">
                        <div class="withdraw_currency" id="withdraw_currency">{{$recordInfo->currency}}</div>
                    </div>
                </div>
                <div class="drop-text-field">
                    <label>Remark</label>
                    <textarea name="remark" id="remark" placeholder="Enter payout details"></textarea>
                </div>
            </div>
            <div class="modal-footer pad-2040">
                <input type="hidden" name="agent_id" id="agent_id" value="">
                <button type="button" class="btn btn-default back-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-default con-btn">Confirm</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                {{ Form::open(array('method' => 'post', 'name' =>'srchAgntFrm', 'id' => 'srchAgntFrm')) }}
                <div class="col-sm-12">
                    <div class="heading-section wth-head">
                        <h5>Agents</h5>
                    </div>
                </div>
                <div class="filter-trans col-sm-12">
                    <span>
                        Filter
                    </span>
                    <div class="text-field-filter agnt-filter">
                        <input type="text" name="keyword" placeholder="Search by Name/ Country">
                        <button class="search-btn"></button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="row">
                @if(count($agents) > 0)
                @foreach($agents as $agent)
                <div class="col-sm-4">
                    <div class="agent-thumb">
                        <div class="agent-content">
                            <div class="agent-thumb-img">
                                @if($agent->profile_image == "")
                                {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
                                @else {{HTML::image('public/uploads/profile_images/full/'.$agent->profile_image, SITE_TITLE)}}
                                @endif
                            </div>

                            <a href="#" class="ag-name" data-toggle="modal" data-target="#user_{{$agent->user_id}}">{{strtoupper($agent->first_name." ".$agent->last_name)}}<i style="margin-left: 10px" class="fas fa-eye"></i></a>
                            <div class="agent-cmsn"><span>Commission</span>
                                <div class="cmsn-prsnt">{{$agent->commission}}%</div>
                            </div>
                            <div class="agent-info">{{HTML::image('public/img/front/awesome-phone-alt.svg', SITE_TITLE)}}<span>{{$agent->phone}}</span></div>
                            <div class="agent-info">{{HTML::image('public/img/front/material-location-on.svg', SITE_TITLE)}}<span>
                                    @php
                                    if (strlen($agent->address) > 28)
                                    $agntAddr = substr($agent->address,0,28).'...'.','.$agent->country;
                                    else
                                    $agntAddr = $agent->address.','.$agent->country;
                                    @endphp
                                    {{ $agntAddr }}
                                </span></div>
                            <div class="agent-btns">
                                @if($showDepositButn == true)
                                <a href="javascript:void(0);" class="get-direction" data-toggle="modal" data-target="#userD_{{$agent->user_id}}">Deposit</a>
                                @endif
                                <!--<a href="#" class="get-direction">Deposit</a>-->
                                @if($showWithdrawButn == true && base64_decode(Session::get('withdrawAmntAgnt64')) != '')
                                <a href="javascript:void(0);" class="get-direction"  data-toggle="modal" data-target="#basicModal_{{$agent->id}}">Withdrawal</a>
                                @else
                                <a href="{{URL::to('auth/withdraw-request')}}" class="get-direction">Withdrawal</a>
                                @endif
                            </div>
                            <!--  <a href="http://maps.google.com/?q={{$agent->address}}" target="_blank" class="get-direction">Get direction</a> -->
                            <!--                            @if($showWithdrawButn == true)
                                                        <a href="{{URL::to('auth/saveWithdrawRequest/'.$agent->id)}}" class="get-direction1"  onclick="showConfirm();">Get direction </a>
                                                        @endif-->
                        </div>


                    </div>
                </div>
                @endforeach
                @else
                <div class="col-sm-4">
                No Record Found
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@foreach($agents as $agent)
<!-- basic modal -->
<div class="modal fade" id="user_{{$agent->user_id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content pro-main-pop">
            
            <div class="agent-profile-block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <div class="agent-pop-top">
                    <div class="agent-thumb-img">
                        @if($agent->profile_image == "")
                        {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
                        @else {{HTML::image('public/uploads/profile_images/full/'.$agent->profile_image, SITE_TITLE)}}
                        @endif
                    </div>
                    <div class="agent-content">
                        <a>{{strtoupper($agent->first_name." ".$agent->last_name)}}</a>
                        <div class="agent-info" onclick="copyTextToClipboard('{{$agent->user_id}}','{{$agent->email}}');">{{HTML::image('public/img/front/ionic-ios-mail.svg', SITE_TITLE)}}<span>{{$agent->email}}</span> <a href="#" class="copy-icon">{{HTML::image('public/img/front/copy-icon.svg', SITE_TITLE)}}</a><div class="txtcpy2" style="display: none"><label>Copied</label></div></div>
                        <div class="agent-info" onclick="copyTextToNumber('{{$agent->user_id}}','{{$agent->phone}}');">{{HTML::image('public/img/front/bigawesome-phone-alt.svg', SITE_TITLE)}}<span>{{$agent->phone}}</span> <a href="#" class="copy-icon">{{HTML::image('public/img/front/copy-icon.svg', SITE_TITLE)}}</a><div class="txtcpy1" style="display: none"><label>Copied</label></div></div>
                        <div class="agent-info">{{HTML::image('public/img/front/bigmaterial-location-on.svg', SITE_TITLE)}}<span>{{$agent->address.','.$agent->country}}</span></div>
                    </div>
                </div>
                <div class="agent-pop-comsn">
                    <span>Commission</span>
                    <div class="prsnt">{{$agent->commission}}%</div>
                </div>
                <div class="two-pop-box">
                    @php
                    $user = getUserByUserId($agent->user_id);
                    @endphp
                    <div class="blnce-pop">
                        <h6>Minimum Deposit:</h6>
                        <div><span>Amount:</span> <strong>{{$user->currency.' '.number_format($agent->min_amount,2,'.',',')}}</strong></div>
                    </div>
                    <div class="blnce-pop">
                        <h6>Current Balance:</h6>
                        <div><span>Amount:</span> <strong>{{$user->currency.' '.number_format($user->wallet_amount,2,'.',',')}}</strong></div>
                    </div>
                    <div class="blnce-pop blnce-pop2">
                        <h6>Payment Methods Supported:</h6>
                        <div>{{$agent->payment_methods}}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pop-ok">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
@endforeach


@foreach($agents as $agent)
<!-- basic modal -->
<div class="modal fade" id="userD_{{$agent->user_id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content pro-main-pop">
            
            <div class="agent-profile-block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <div class="agent-pop-top">
                    <div class="agent-thumb-img">
                        @if($agent->profile_image == "")
                        {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
                        @else {{HTML::image('public/uploads/profile_images/full/'.$agent->profile_image, SITE_TITLE)}}
                        @endif
                    </div>
                    <div class="agent-content">
                        <a>{{strtoupper($agent->first_name." ".$agent->last_name)}}</a>
                        <div class="agent-info" onclick="copyTextToClipboard1('{{$agent->user_id}}','{{$agent->email}}');">{{HTML::image('public/img/front/ionic-ios-mail.svg', SITE_TITLE)}}<span>{{$agent->email}}</span> <a href="#" class="copy-icon">{{HTML::image('public/img/front/copy-icon.svg', SITE_TITLE)}}</a><div class="txtcpy2" style="display: none"><label>Copied</label></div></div>
                        <div class="agent-info" onclick="copyTextToNumber1('{{$agent->user_id}}','{{$agent->phone}}');">{{HTML::image('public/img/front/bigawesome-phone-alt.svg', SITE_TITLE)}}<span>{{$agent->phone}}</span> <a href="#" class="copy-icon">{{HTML::image('public/img/front/copy-icon.svg', SITE_TITLE)}}</a><div class="txtcpy1" style="display: none"><label>Copied</label></div></div>
                        <div class="agent-info">{{HTML::image('public/img/front/bigmaterial-location-on.svg', SITE_TITLE)}}<span>{{$agent->address.','.$agent->country}}</span></div>
                    </div>
                </div>
                <div class="agent-pop-comsn">
                    <span>Commission</span>
                    <div class="prsnt">{{$agent->commission}}%</div>
                </div>
                <div class="two-pop-box">
                    @php
                    $user = getUserByUserId($agent->user_id);
                    @endphp
                    <div class="blnce-pop">
                        <h6>Minimum Deposit:</h6>
                        <div><span>Amount:</span> <strong>{{$user->currency.' '.number_format($agent->min_amount,2,'.',',')}}</strong></div>
                    </div>
                    <div class="blnce-pop">
                        <h6>Current Balance:</h6>
                        <div><span>Amount:</span> <strong>{{$user->currency.' '.number_format($user->wallet_amount,2,'.',',')}}</strong></div>
                    </div>
                    <div class="blnce-pop blnce-pop2">
                        <h6>Payment Methods Supported:</h6>
                        <div>{{$agent->payment_methods}}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pop-ok">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>-->
                <a href="http://maps.google.com/?q={{$agent->address}}" target="_blank" class="get-direction1">Get direction </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="basicModal_{{$agent->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog model-trans">
        <div class="modal-content">
            
            <div class="trans-dettail-pop">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <div class="icon-pop">
                    {{HTML::image('public/img/front/money_ui_icon.svg', SITE_TITLE)}}
                </div>
                <h5>Please confirm the transaction details in order to complete the transfer.</h5>
                <div class="tran-pop-row">
                    <span><b>Transfer From</b></span>
                    <div>
                        <div class="agent_list_ac"><strong>Ac. No. :</strong><span>{{$recordInfo->account_number}}</span></div>

                    </div>
                </div>
                <div class="tran-pop-row">
                    <span><b>Transfer To</b></span>
                    <div>
                        <div class="agent_list_ac"><strong>Ac. No. :</strong><span>
                               @php
                    $user = getUserByUserId($agent->user_id);
                    @endphp
                    {{$user->account_number}}
                            </span></div>
                        <div class="agent_list_ac"><strong>Name :</strong><span>
                                {{strtoupper($agent->first_name." ".$agent->last_name)}}
                            </span></div>
                    </div>
                </div>
                <div class="tran-pop-row">
                    <span><b>Amount</b></span>
                    <div> 
                        <div class="agent_list_ac"><span class="amt_pay">{{$recordInfo->currency}} {{base64_decode(Session::get('withdrawAmntAgnt64'))}}</span></div>

                    </div>
                </div>
                <div class="tran-pop-row">
                    <span><b>Payout Instructions</b></span>
                    <div class="payout_view">
                        <div><span >{{Session::get('remark')}}</span></div>

                    </div>
                </div>
            </div>
            <div class="modal-footer pad-2040">

                <button type="button" class="btn btn-default back-btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default con-btn button_disable" onclick="payonclick('<?php echo $agent->id;?>')" >Confirm</button>

            </div>
        </div>
    </div>
</div>
<script>
                                    function copyTextToClipboard(id,text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text
                                            document.getElementById('user_'+id).appendChild(textArea);
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
                                    //                                    alert("Your account details copied successfully");
                                     $('.txtcpy2').show();
                                     $('.txtcpy2').delay(1000).hide(1);
//                                    $('#blank_message').html('Email address copied successfully');
//                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.getElementById('user_'+id).removeChild(textArea);
                                    }
                                    
                                     function copyTextToNumber(id, text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text;
                                    document.getElementById('user_'+id).appendChild(textArea);
                                  
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
                                    //                                    alert("Your account details copied successfully");
                                  
                                    $('.txtcpy1').show();
                                    $('.txtcpy1').delay(1000).hide(1);
//                                    $('#blank_message').html('Mobile number copied successfully');
//                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.getElementById('user_'+id).removeChild(textArea);
                                    }
                                    
                                    function copyTextToClipboard1(id,text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text
                                            document.getElementById('userD_'+id).appendChild(textArea);
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
                                    //                                    alert("Your account details copied successfully");
                                     $('.txtcpy2').show();
                                     $('.txtcpy2').delay(1000).hide(1);
//                                    $('#blank_message').html('Email address copied successfully');
//                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.getElementById('userD_'+id).removeChild(textArea);
                                    }
                                    
                                     function copyTextToNumber1(id, text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text;
                                    document.getElementById('userD_'+id).appendChild(textArea);
                                  
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
                                    //                                    alert("Your account details copied successfully");
                                  
                                    $('.txtcpy1').show();
                                    $('.txtcpy1').delay(1000).hide(1);
//                                    $('#blank_message').html('Mobile number copied successfully');
//                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.getElementById('userD_'+id).removeChild(textArea);
                                    }

</script>
@endforeach
@endsection