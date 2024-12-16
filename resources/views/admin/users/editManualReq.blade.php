@extends('layouts.admin')
@section('content')
<?php 

function matchSel($first, $second) {
    if ($first == $second)
        return "selected";
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        $.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Only letters, numbers and underscore allowed.");
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            //var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input);
        }, "Password must be a combination of Numbers, Uppercase & Lowercase Letters.");
        $("#adminForm").validate();

    });
    
    function changePrice(val){
        $('#recipAmt').val('<?php echo $req->currency;?> '+val);
    }
</script>

<script>
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

window.onload = () => {
 const myInput = document.getElementById('amt');
 myInput.onpaste = e => e.preventDefault();
}



</script>


<style type="text/css">
    .county-code {
        width: 100px;
    }

    .select select {
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        appearance: none;
        outline: 0;
        box-shadow: none;
        border: 0 !important;
        background: #e6e6e6;
        background-image: none;
        font-size: 12px;
    }

    /* Remove IE arrow */
    .select select::-ms-expand {
        display: none;
    }

    /* Custom Select */
    .select {
        position: relative;
        display: flex;
        width: 100%;
        height: 34px;
        background: #eaeaea;
        overflow: hidden;
        border-radius: 0;
    }

    .select select {
        flex: 1;
        padding: 0 .5em;
        color: #000;
        cursor: pointer;
    }

    /* Arrow */
    .select::after {
        content: '\25BC';
        position: absolute;
        top: 0;
        right: 0;
        padding: 0 8px;
        cursor: pointer;
        pointer-events: none;
        -webkit-transition: .25s all ease;
        -o-transition: .25s all ease;
        transition: .25s all ease;
        font-size: 10px;
        height: 40px;
        display: flex;
        align-items: center;
    }

    /* Transition */
    .select:hover::after {
        color: #000;
    }

    .flex-input {
        display: flex;
    }

    .county-code {
        width: 228px;
    }

    .select::after {
        content: '\25BC';
        position: absolute;
        top: 0;
        right: 0;
        padding: 0 8px;
        cursor: pointer;
        pointer-events: none;
        -webkit-transition: .25s all ease;
        -o-transition: .25s all ease;
        transition: .25s all ease;
        font-size: 10px;
        height: 34px;
        display: flex;
        align-items: center;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Manual Request</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/users/manual-deposit-request')}}"><i class="fa fa-user"></i> <span>Manual Deposit Request</span></a></li>
            <li class="active"> Edit Manual Deposit Request</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($req, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data",'onsubmit'=>'return disable_submit()']) }}            
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Amount (<?php echo $req->currency; ?>) <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('amount', null, ['class'=>'form-control required','id'=>'amt', 'placeholder'=>'Amount', 'autocomplete' => 'off','onKeyUp'=>'changePrice(this.value)','onkeypress'=>"return validateFloatKeyPress(this,event);"])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Reference number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('ref_number', null, ['class'=>'form-control required', 'placeholder'=>'Reference number', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <!--{{Form::submit('Submit', ['class' => 'btn btn-info'])}}-->
                        <a href="#" data-toggle="modal" data-target="#basicModal{{$req->id}}" class="btn btn-info">Submit</a>
                        <a href="{{ URL::to( 'admin/users/manual-deposit-request')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
@php $userTyp = $req->User->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($req->User->first_name)).' '.strtoupper(strtoupper($req->User->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($req->User->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($req->User->first_name) ? strtoupper(strtoupper($req->User->first_name)).' '.strtoupper(strtoupper($req->User->last_name)):strtoupper($req->User->director_name) @endphp
@endif
            <div class="modal fade" id="basicModal{{$req->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                <div class="modal-dialog md1">
                    <div class="modal-content transfer-pop">
                        <div class="transfer-fund-pop">
                            <h4 class="text-center mb-3 ft-img"><img src="{{HTTP_PATH}}/public/img/front/Fundtransfer-thumb.svg"><br>Manual Deposite Approval</h4>
                            <div class="filed-box">
                                <div class="form-control-new">
                                    <label>Support type </label>
                                    <input type="text" id="recipName" value="{{'Manual Deposit'}}" disabled>
                                </div>
                                <div class="form-control-new">
                                    <label>User Name </label>
                                    <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                                </div>
                            </div>
                            <div class="filed-box">
                                <div class="form-control-new w100">
                                    <label>Amount</label>
                                    <input type="text" value="{!! $req->currency.' '.$req->amount !!}" id="recipAmt" placeholder="" disabled>
                                </div>
                                <div class="form-control-new w100">
                                    <label>Bank Name</label>
                                    <input type="text" value="{!! $req->bank_name !!}" id="recipEmail" placeholder="" disabled>
                                </div>


                            </div>

                            <div class="filed-box" id="cuncyConvrsnTF">
                                <div class="form-control-new w100">
                                    <label>Date</label>
                                    <input type="text" value="{{$req->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                            {{Form::submit('Confirm', ['class' => 'btn btn-info button_disable'])}}
                            <!--<a href="{{URL::to('admin/users/change-manual-deposit-req-status/'.$req->id.'/1')}}" type="button" class="btn btn-default" >Confirm</a>-->
                            <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            {{ Form::close()}}
        </div>
    </section>

<script>
function disable_submit()
{  
$('.button_disable').hide();  
return true;
}

</script>    



    @endsection