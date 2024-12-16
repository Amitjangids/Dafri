@extends('layouts.inner')
@section('content')

<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{ Form::open(array('method' => 'post', 'name' =>'downlodStatemnt', 'id' => 'downlodStatemnt', 'class' => 'row border-form','[formGroup]'=>'formGroup')) }}
                <div class="download-statment">
                    <h6>Download statement</h6>
                    <p>For which period do you need a statement</p>
                    <div class="stat-opt">
                        <div class="text-field-filter">
                            <div class="radio-card">
                                <input id="radio-4" name="perdStatmnt" value="last_month" type="radio">
                                <label for="radio-4" class="radio-label">Last month</label>
                            </div>
                            <div class="radio-card">
                                <input id="radio-5" name="perdStatmnt" value="last_3_month" type="radio">
                                <label for="radio-5" class="radio-label">Last  3 months</label>
                            </div>
                            <div class="radio-card">
                                <input id="radio-6" name="perdStatmnt" value="last_6_month" type="radio">
                                <label for="radio-6" class="radio-label">Last 6 months</label>
                            </div>
                            <div class="radio-card">
                                <input id="radio-7" name="perdStatmnt" value="last_1_yr" type="radio">
                                <label for="radio-7" class="radio-label">Last  1 year</label>
                            </div>
                        </div>
                        <p>or select a custom date range </p>

                        <div class="text-field-filter col-sm-7 fs m-auto">
                            <input type="text" name="statement_date" id="statement_date" class="date_picker" placeholder="Date From - To">
                            {{HTML::image('public/img/front/calender.svg', SITE_TITLE)}}
                        </div>
                        <div class="btn-pro">
                            <input type="hidden" name="dwnldStatmnt" value="true">
                            <button class="sub-btn" type="submit">Proceed</button>
                        </div>
                        <p class="text-left">Your statement will be sent to your registered email.</p>
                    </div>
                </div>

                <div class="modal-footer mf">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
            <div class="row">
                <div class="heading-section trans-head col-sm-12">
                    <h5>Transaction history</h5> <a href="#" data-toggle="modal" data-target="#basicModal">{{HTML::image('public/img/front/pdf.svg', SITE_TITLE)}} Download statement</a>
                </div>
                <div class="filter-trans col-sm-12">
                    <span>
                        Filter
                    </span>
                    <div class="text-field-filter">
                        {{ Form::open(array('method' => 'post', 'id' => 'srchKeyform', 'class' => '')) }}
                        <input type="text" name="keyword" placeholder="Search" value="<?php echo $keyword; ?>">
                        <button class="search-btn"></button>
                        <!--{{ Form:: close() }}-->
                    </div>
                    <div class="text-field-filter">
                        <!--{{ Form::open(array('method' => 'post', 'id' => 'srchDateform', 'class' => '')) }}-->
                        <input type="text" name="srchDate" id="srchDate" placeholder="Date From - To" class="date_picker" value="{{$toDate.' - '.$fromDate}}">
                        {{HTML::image('public/img/front/calender.svg', SITE_TITLE)}}
                        <!--{{ Form:: close() }}-->	
                    </div>
                    <div class="text-field-filter ml-auto">
                        <!--{{ Form::open(array('method' => 'post', 'id' => 'srchTransTypform', 'class' => '')) }}-->
                        <div class="radio-card">
                            <input id="radio-1" name="radio" type="radio" value="all" onclick="$('#srchKeyform').submit();" <?php if ($radio == "all") {
    echo "checked";
} ?>>
                            <label for="radio-1" class="radio-label">All</label>
                        </div>
                        <div class="radio-card">
                            <input id="radio-2" name="radio" type="radio" value="sent" onclick="$('#srchKeyform').submit();" <?php if ($radio == "sent") {
    echo "checked";
} ?>>
                            <label for="radio-2" class="radio-label">Money Out</label>
                        </div>
                        <div class="radio-card">
                            <input id="radio-3" name="radio" type="radio" value="received" onclick="$('#srchKeyform').submit();" <?php if ($radio == "received") {
    echo "checked";
} ?>>
                            <label for="radio-3" class="radio-label">Money In</label>
                        </div>
                        <!-- <div class="radio-card">
                            <input id="radio-8" name="radio" type="radio" value="topup" onclick="$('#srchKeyform').submit();" <?php if ($radio == "topup") {
    echo "checked";
} ?>>
                            <label for="radio-8" class="radio-label">Topup</label>
                        </div> -->
                        {{ Form::close()}}
                    </div>

                </div>
                <div class="tran-list col-sm-12"> <?php //echo '<pre>';print_r($trans);exit;?>
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
                    if ($res == false) {
                    continue;	 
                    }
                    else if ($res->user_type == 'Personal') {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Business') {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }
                    else if ($res->user_type == 'Agent' && $res->first_name != "") {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Agent' && $res->business_name != "") {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
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
                    if ($res == false) {
                    $transFnm = 'N/A';
                    $transLnm = '';
                    $transName = $transFnm." ".$transLnm;	 
                    }
                    else if ($res->user_type == 'Personal') {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Business') {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }
                    else if ($res->user_type == 'Agent' && $res->first_name != "") {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Agent' && $res->business_name != "") {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }	
                    }
                    else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2) {
                    $res = getUserByUserId($tran->user_id);
                    if ($res == false) {
                    $transFnm = 'N/A';
                    $transLnm = '';
                    $transName = $transFnm." ".$transLnm;	 
                    }
                    else if ($res->user_type == 'Personal') {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Business') {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }
                    else if ($res->user_type == 'Agent' && $res->first_name != "") {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Agent' && $res->business_name != "") {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
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
                    else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1) {
                    $res = getUserByUserId($tran->receiver_id);
                    if ($res == false) {
                    $transFnm = 'N/A';
                    $transLnm = '';
                    $transName = $transFnm." ".$transLnm;	 
                    }
                    else if ($res->user_type == 'Personal') {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Business') {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }
                    else if ($res->user_type == 'Agent' && $res->first_name != "") {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Agent' && $res->business_name != "") {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }	
                    }
                    else {
                    $res = getUserByUserId($tran->receiver_id);
                    if ($res == false) {
                    $transFnm = 'N/A';
                    $transLnm = '';
                    $transName = $transFnm." ".$transLnm;	 
                    }
                    else if ($recordInfo->user_type == 'Personal')	{
                    $transFnm = $recordInfo->first_name;
                    $transLnm = $recordInfo->last_name;	
                    $transName = $transFnm." ".$transLnm;	
                    }
                    else if ($res->user_type == 'Business') {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }
                    else if ($res->user_type == 'Agent' && $res->first_name != "") {
                    $transFnm = $res->first_name;
                    $transLnm = $res->last_name;
                    $transName = $transFnm." ".$transLnm;
                    }
                    else if ($res->user_type == 'Agent' && $res->business_name != "") {
                    $transFnm = $res->business_name;
                    $transLnm = "";
                    $transName = $transFnm." ".$transLnm; 
                    }
                    }
                    @endphp
                    @endif
                    <div class=" trans-thumb">
                        <div class="tran-name">
                            <div class="tran-name-icon">{{strtoupper(substr($transFnm,0,1))}} {{strtoupper(substr($transLnm,0,1))}}</div>
                            <div class="trans-name-title">
                                <h6><a href="{{URL::to('auth/transaction-detail/'.$tran->id)}}">{{strtoupper(strtolower($transName))}}</a></h6>
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
                        <div class="trans-money">{{$tran->currency}} {{$tran->amount}}
                            @php
                            $date = date_create($tran->created_at);
                            $transDate = date_format($date,'M, d Y, H:i A');
                            @endphp
                            <p style="font-size:11px;">{{$transDate}}</p>
                            <p style="font-size:11px;"><a style="color: #fff;" href="{{URL::to('auth/transaction-detail/'.$tran->id)}}"><button style="background-color: #000;color: #fff;">View</button></a></p>
                        </div>
                    </div>
                    @endforeach
                    <div class="panel-heading" style="float:right;">
                        {{$trans->appends(Request::except('_token'))->render()}}
                    </div>
                    @else
                    No transaction found for selected period. 
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script>
    $(function () {
        $('#statement_date').daterangepicker({
            maxDate: new Date(),
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#srchDate').daterangepicker({
            maxDate: new Date(),
            startDate: moment().subtract('days', 365),
            locale: {format: 'YYYY-MM-DD'}}, onSelect);

        $('#statement_date').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

    });

    function onSelect(startDate, endDate) {
        var stDT = startDate.format('YYYY-MM-DD');
        var edDT = endDate.format('YYYY-MM-DD');
        document.getElementById('srchDate').value = stDT + ' - ' + edDT;
//alert(document.getElementById('srchDate').value);
        $('#srchKeyform').submit();
    }
</script>

<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( function() {
$( "#srchDate" ).datepicker({ dateFormat: 'yy-mm-dd',maxDate: '0' });
} );
</script> -->
@endsection