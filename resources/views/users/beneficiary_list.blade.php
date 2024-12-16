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
    .bene_head{
        display: flex;
    }
    .bene_head h5{
        width: 70%;
    }
    .list_btn{
        margin: 0 !important;
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

                    <div class="row">
                        <div class="col-sm-12">

                            <div class="agent-req">
                                <div class="heading-section wth-head bene_head">
                                    <h5>Beneficiary List</h5>

                                    <div class="add-new-recipients mt-30 list_btn">
                                        <a href="{{URL::to('auth/add-beneficiary')}}">Add new beneficiary {{HTML::image('public/img/front/add_new_recipients.svg', SITE_TITLE)}}</a>
                                    </div>
                                </div>
                                <div class="row">

                                    <?php foreach ($records as $val) { ?>
                                        <div class="col-sm-6">
                                            <div class="requst-box">
                                                <div class="tran-name">
                                                    <?php
                                                    
                                                    if($val->receiver_name != ''){
                                                        $receiver_name = $val->receiver_name;
                                                                $transShortName = ucfirst(substr($val->receiver_name, 0, 1));
                                                    } else{
                                                        if ($val->Receiver->user_type == 'Personal') {
                                                            $receiver_name = $val->Receiver->first_name . ' ' . $val->Receiver->last_name;
                                                            $transShortName = ucfirst(substr($val->Receiver->first_name, 0, 1)) . ucfirst(substr($val->Receiver->last_name, 0, 1));
                                                        } elseif ($val->Receiver->user_type == 'Business') {
                                                            $receiver_name = $val->Receiver->business_name;
                                                            $transShortName = ucfirst(substr($receiver_name, 0, 1));
                                                        } elseif ($val->Receiver->user_type == 'Agent') {
                                                            if ($val->Receiver->first_name != '') {
                                                                $receiver_name = $val->Receiver->first_name . ' ' . $val->Receiver->last_name;
                                                                $transShortName = ucfirst(substr($val->Receiver->first_name, 0, 1)) . ucfirst(substr($val->Receiver->last_name, 0, 1));
                                                            } else {
                                                                $receiver_name = $val->Receiver->business_name;
                                                                $transShortName = ucfirst(substr($receiver_name, 0, 1));
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="tran-name-icon">{{$transShortName}}</div>
                                                    <div class="trans-name-title">
                                                        {{substr(strtoupper($receiver_name),0,40)}}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="trans-money">
                                                        <p style="font-size:11px;">&nbsp;</p>
                                                        <span>{{$val->Receiver->phone}}</span>

                                                    </div>

                                                </div>
                                                <div class="btn-req">
                                                    <!--<a href="{{URL::to('auth/fund-transfer/'.$val->id)}}">Pay</a>-->
                                                    <a href="{{URL::to('auth/fund-transfer-phone/'.$val->Receiver->account_number)}}">Pay</a>
                                                    <a href="{{URL::to('auth/deleteBeneficiary/'.$val->id)}}" title="Remove From Beneficiary" >Remove</a>
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

<script>
    function rejectWithdraw(id) {
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