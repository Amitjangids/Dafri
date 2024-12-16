@extends('layouts.inner')
@section('content')

<style>
    .remark_right{
        width: 100%;
        text-align: right;
        padding: 10px 0;
    }
    
    .copy-icon {
	font-size: 22px;
	font-weight: 600;
	margin-bottom: 8px;
}
</style>
<div class="d-flex" id="wrapper">    
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class=" ">
                <div class="w100">

                    <?php
                    $card_class = getUserCardType($recordInfo->account_category);
                    ?>
                    <div class="row">

                        <div class=" col-sm-6">
                            <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                            <div class="heading-section wth-head">
                                <h5>Withdrawal</h5>
                            </div>
                            <div class="vcard-wrapper">
                                <?php
                                $card_class = getUserCardType($recordInfo->account_category);
                                ?>
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
                                <?php
                                if ($tran->receiver_id == Session::get('user_id') && $tran->trans_type == 2) {
                                    $res = getUserByUserId($tran->user_id);
                                    if ($res != false && $res->user_type == 'Personal') {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Business') {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else {
                                        $agent = getAgentById($tran->receiver_id);
                                        if ($agent != false) {
                                            $transFnm = $agent->first_name;
                                            $transLnm = $agent->last_name;
                                            $transName = $transFnm . " " . $transLnm;
                                            $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                        } else {
                                            $transFnm = "Agent";
                                            $transLnm = "";
                                            $transName = $transFnm . " " . $transLnm;
                                            $transShortName = ucfirst(substr($transFnm, 0, 1));
                                        }
                                    }
                                } else if ($tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                                    $res = getUserByUserId($tran->user_id);
                                    if ($res != false && $res->user_type == 'Personal') {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Business') {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else {
                                        $agent = getAgentById($tran->receiver_id);
                                        if ($agent != false) {
                                            $transFnm = $agent->first_name;
                                            $transLnm = $agent->last_name;
                                            $transName = $transFnm . " " . $transLnm;
                                            $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                        } else {
                                            $transFnm = "Agent";
                                            $transLnm = "";
                                            $transName = $transFnm . " " . $transLnm;
                                            $transShortName = ucfirst(substr($transFnm, 0, 1));
                                        }
                                    }
                                } else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 1) {
                                    $res = getUserByUserId($tran->user_id);
                                    if ($res != false && $res->user_type == 'Personal') {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Business') {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    }
                                } else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id == 0 && $tran->trans_type == 2) {
                                    $res = getUserByUserId($tran->user_id);
                                    if ($res != false && $res->user_type == 'Personal') {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Business') {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    }
                                } else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 2) {
                                    $res = getUserByUserId($tran->receiver_id);
                                    if ($res != false && $res->user_type == 'Personal') {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Business') {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else {
                                        $agent = getAgentById($tran->receiver_id);
                                        if ($agent != false) {
                                            $transFnm = $agent->first_name;
                                            $transLnm = $agent->last_name;
                                            $transName = $transFnm . " " . $transLnm;
                                            $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                        } else {
                                            $transFnm = "N/A";
                                            $transLnm = "";
                                            $transName = $transFnm . " " . $transLnm;
                                            $transShortName = ucfirst(substr($transFnm, 0, 1));
                                        }
                                    }
                                } else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != 0 && $tran->trans_type == 2 && $tran->trans_for == "Withdraw##Agent") {
                                    $agent = getAgentById($tran->receiver_id);
                                    if ($agent != false) {
                                        $transFnm = $agent->first_name;
                                        $transLnm = $agent->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    }
                                } else if ($tran->user_id == Session::get('user_id') && $tran->receiver_id != Session::get('user_id') && $tran->trans_type == 1) {
                                    $res = getUserByUserId($tran->receiver_id);
                                    if ($res != false && $res->user_type == 'Personal') {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Business') {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    }
                                } else {
                                    $res = getUserByUserId($tran->receiver_id);
                                    if ($res != false && $recordInfo->user_type == 'Personal') {
                                        $transFnm = $recordInfo->first_name;
                                        $transLnm = $recordInfo->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1)) . ucfirst(substr($transLnm, 0, 1));
                                    } else if ($res != false && $recordInfo->user_type == 'Business') {
                                        $transFnm = $recordInfo->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->first_name != "") {
                                        $transFnm = $res->first_name;
                                        $transLnm = $res->last_name;
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else if ($res != false && $res->user_type == 'Agent' && $res->business_name != "") {
                                        $transFnm = $res->business_name;
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    } else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm . " " . $transLnm;
                                        $transShortName = ucfirst(substr($transFnm, 0, 1));
                                    }
                                }
                                ?>
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
                                        <?php
                                        $date = date_create($tran->created_at);
                                        $transDate = date_format($date, 'M, d Y, H:i A');
                                        ?>
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

                                    <?php foreach ($wthdrwReq as $val) { ?>
                                        <div class="col-sm-6">
                                            <div class="requst-box">
                                                <div class="tran-name">
                                                    <?php 
                                                    $transShortName = ucfirst(substr($val->user_name, 0, 1));
                                                    ?>
                                                    <div class="tran-name-icon">
                                                        {{$transShortName}}
                                                    </div>
                                                    <div class="trans-name-title">
                                                        {{substr(strtoupper($val->user_name),0,40)}}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="trans-money">
                                                        <?php
                                                        $date = date_create($val->created_at);
                                                        $reqDate = date_format($date, 'M, d Y, H:i A');

                                                        $user = getUserByUserId($val->user_id);
                                                        ?>
                                                        <p style="font-size:11px;">{{$reqDate}}</p>
                                                        <span>{{$user->currency.' '.$val->amount}}</span>

                                                    </div>

                                                </div>
                                                <div class="remark_right">
                                                    <?php $strr = preg_replace( "/\r|\n/", "", $val->remark);?>
                                                    <span onclick="showPoop('<?php echo $strr; ?>');">View Payout Instructions</span>
                                                </div>
                                                <div class="btn-req">
                                                    <a href="#" data-toggle="modal" data-target="#confirmDialog_<?php echo $val->id; ?>">Accept</a>
                                                    <a href="#" data-toggle="modal" data-target="#rejectDialog_<?php echo $val->id; ?>">Reject</a>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

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

<?php foreach ($wthdrwReq as $val) { ?>
    <?php
    $user = getUserByUserId($val->user_id);
    ?>
    <!-- basic modal -->
    <div class="modal x-alert fade" id="confirmDialog_<?php echo $val->id; ?>" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-alert-lg">
            <div class="modal-content">
                <div class="modal-body ">
                    <p>Do you want to accept this request <br> for below amount</p>
                    <p>{{$user->currency.' '.$val->amount}}</p>
                    <ul class="list-inline btn-list">
                        {{ Form::open(array('method' => 'post', 'id' => 'agntWithdrawFrm', 'class' => '','onsubmit'=>'return disable_submit'.$val->id.'();')) }}
                        <input type="hidden" name="req" value="{{base64_encode($val->id)}}">
                        <li class="list-inline-item"><button type="submit" class="btn btn-dark button_disable{{$val->id}}">Yes</button></li>
                        <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">No</button></li>
                        {{ Form::close() }}
                    </ul>
                </div>
            </div>
        </div>
    </div>


 <script>
 function disable_submit{{$val->id}}()
 {
 $('.button_disable{{$val->id}}').prop('disabled', true);
 return true;
 }
</script> 

    <div class="modal x-alert fade" id="rejectDialog_<?php echo $val->id; ?>" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-alert-lg">
            <div class="modal-content">
                <div class="modal-body ">
                    <p>Do you want to reject this request <br> for below amount</p>
                    <p>{{$user->currency.' '.$val->amount}}</p>
                    <ul class="list-inline btn-list">
                        <li class="list-inline-item"><button type="submit" class="btn btn-dark button_disable_cancel{{$val->id}}" onclick="rejectWithdraw('<?php echo base64_encode($val->id); ?>')">Yes</button></li>
                        <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">No</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script>
    function rejectWithdraw(id) {
        $('.button_disable_cancel'+atob(id)).prop('disabled', true);
        window.location.href = "<?php echo HTTP_PATH; ?>/auth/agent-decline-withdraw-request/" + id + "/";
    }
</script>

<script type="text/javascript">
    function showPoop(value) {
        if (value && value != 'na') {

            var data = ' <a href="javascript:void(0);" class="copy-icon" id="copy_txt">{{HTML::image("public/img/front/copy-icon.svg", SITE_TITLE)}}</a>'
            $('#blank_message').html(value + data);
            $('#copy_txt').attr("onclick", "copyTextToClipboard1('" + value + "')");
        } else {
            $('#blank_message').html('Payout instructions not found');
        }

        $('#blank-alert-Modal').modal('show');
    }

    function copyTextToClipboard1(str) {

        var textArea = document.createElement("textarea");
        textArea.value = str; ///$('#dpostAddr').html();
        document.getElementById('blank_message').appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
//            alert(msg);
            //    alert("Deposit Address copied successfully");
            $('#blank_message').html('Payout instructions copied successfully');
            //        $('#blank-alert-Modal').modal('show');
            //console.log('Copying text command was ' + msg);
        } catch (err) {
            console.log('Oops, unable to copy');
        }

//        document.getElementById('blank_message').removeChild(textArea);
    }
</script>

@endsection