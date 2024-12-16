@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Pay Client DBA</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/users')}}"><i class="fa fa-cogs"></i> <span>User Management</span></a></li>
            <li class="active"> Pay Client DBA</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{ Form::model($recordInfo, array('method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data")) }}
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Username</label>
                        <div class="col-sm-10" style="padding-top: 7px;margin-bottom: 0;">
                            @if($recordInfo->user_type == 'Personal')
                            @php $name  = strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)@endphp
                            @elseif($recordInfo->user_type == 'Business')
                            @php $name  = strtoupper($recordInfo->director_name)@endphp
                            @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                            @php $name  = strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)@endphp
                            @elseif($recordInfo->user_type == 'Agent' && $recordInfo->director_name != "")
                            @php $name  = strtoupper($recordInfo->director_name)@endphp
                            @endif
                            {{$name}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Account Number</label>
                        <div class="col-sm-10" style="padding-top: 7px;margin-bottom: 0;">
                            {{$recordInfo->account_number}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Account Balance <span class="require">*</span>  {{$recordInfo->dba_currency}}</label>
                        <div class="col-sm-10">
                            {{Form::text('wallet_amount',$recordInfo->dba_wallet_amount, ['id'=>'wallet_amount','class'=>'form-control required', 'placeholder'=>'Wallet Balance', 'autocomplete' => 'off', 'disabled' => 'true'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Account Action <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php $serviceType = array('debit' => 'Debit', 'credit' => 'Credit'); ?>                        
                            {{Form::select('wallet_action', $serviceType,null, ['class' => 'form-control','id' => 'wallet_action','placeholder' => 'Select Action'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Amount <span class="require">*</span>  {{$recordInfo->dba_currency}}</label>
                        <div class="col-sm-10">
                            {{Form::text('amount', null, ['class'=>'form-control required','id'=>'amount', 'placeholder'=>'Amount', 'autocomplete' => 'off','onkeypress'=>"return validateFloatKeyPress(this,event);"])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Reason <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('reason', null, ['class'=>'form-control required','id'=>'reason', 'placeholder'=>'Reason', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <!-- {{Form::submit('Submit', ['class' => 'btn btn-info'])}} -->
                        {{Form::button('Submit', ['class' => 'btn btn-info','id'=>'check_form'])}}
                        {{Form::reset('Reset', ['class' => 'btn btn-default canlcel_le'])}}
                    </div>
                </div>
            </div>



            {{ Form::close()}}
        </div>
        <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog md1">
                <div class="modal-content transfer-pop">
                    <div class="transfer-fund-pop">
                        <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Adjust User Balance</h4>
                        <div class="filed-box">
                            <div class="form-control-new">
                                <label>User Name </label>
                                <input type="text" value="{!! strtoupper($name) !!}" id="recipAccNum" placeholder="" disabled>
                            </div>
                            <div class="form-control-new">
                                <label>Account Number </label>
                                <input type="text" id="recipName" value="{{ $recordInfo->account_number }}" disabled>
                            </div>
                        </div>
                        <div class="filed-box">
                            <div class="form-control-new">
                                <label>Account Balance ({{$recordInfo->dba_currency}}) </label>
                                <input type="text" value="{!! $name !!}" id="recipAccBal" placeholder="" disabled>
                            </div>
                            <div class="form-control-new">
                                <label>Account Action </label>
                                <input type="text" id="account_action" value="{{ $recordInfo->account_number }}" disabled>
                            </div>
                        </div>
                        <div class="filed-box">
                            <div class="form-control-new">
                                <label>Amount ({{$recordInfo->dba_currency}}) </label>
                                <input type="text" value="{!! $name !!}" id="pop_amount" placeholder="" disabled>
                            </div>
                            <div class="form-control-new">
                                <label>Reason  </label>
                                <input type="text" id="pop_reason" value="{{ $recordInfo->account_number }}" disabled>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer pop-ok">

                        <button type="button" class="btn btn-default button_disable" id="sub_button" onclick="disable_submit()">Confirm</button>
                        <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        $(document).ready(function () {

            window.onload = () => {
 const myInput = document.getElementById('amount');
 myInput.onpaste = e => e.preventDefault();
}


            $('#check_form').click(function () {
                var err = true;
                $('.form-control').removeClass('error');
                var wallet_action = $('#wallet_action').val();
                var reason = $('#reason').val();
                var amount = $('#amount').val();
                if (wallet_action == '') {
                    err = false;
                    $('#wallet_action').addClass('error');
                }
                if (reason == '') {
                    err = false;
                    $('#reason').addClass('error');
                }
                if (amount == '') {
                    err = false;
                    $('#amount').addClass('error');
                }
                if (err) {
                    $('#basicModal').modal('show');
                }
            });
            $('#basicModal').on('show.bs.modal', function (event) {
                var wallet_action = $('#wallet_action').val();
                $('#account_action').val(wallet_action);
                var reason = $('#reason').val();
                $('#pop_reason').val(reason);
                var amount = $('#amount').val();
                $('#pop_amount').val(amount);
                var amount = $('#wallet_amount').val();
                $('#recipAccBal').val(amount);

            });
            $('#sub_button').click(function () {
                //alert('sss');
                $("#adminForm").submit();
            });
        })

    function disable_submit()
    {
    $('.button_disable').prop('disabled', true);   
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
    @endsection