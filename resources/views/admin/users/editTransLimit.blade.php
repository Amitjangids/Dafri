@extends('layouts.admin')
@section('content')
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
const myInput = document.getElementById('daily_limit');
myInput.onpaste = e => e.preventDefault();

const myInput1 = document.getElementById('week_limit');
myInput1.onpaste = e => e.preventDefault();

const myInput2 = document.getElementById('month_limit');
myInput2.onpaste = e => e.preventDefault();

}

</script>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Transaction Limit</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/users/transactions-limit')}}"><i class="fa fa-user"></i> <span>Transaction Limit</span></a></li>
            <li class="active"> Edit Transaction Limit</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($limit, ['method' => 'post', 'id' => 'editTransLimitFrm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
                <div class="box-body">
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Membership Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('account_category', null, ['class'=>'form-control required', 'placeholder'=>'Membership Name', 'disabled' => 'true'])}}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Membership Type <span class="require">*</span></label>
                        <div class="col-sm-10">
                         <?php $membershipList = array('1'=>'Customer','2'=>'Merchant');?>
                         {{Form::select('category_for', $membershipList,null, ['class' => 'form-control required','placeholder' => 'Membership Type', 'disabled' => 'true'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Daily Limit <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('daily_limit', null, ['class'=>'form-control required', 'placeholder'=>'Daily Limit', 'autocomplete' => 'off','maxlength' => 14,'onkeypress'=>'return validateFloatKeyPress(this,event);','id'=>'daily_limit'])}}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Week Limit <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('week_limit', null, ['id'=>'week_limit','class'=>'form-control required', 'placeholder'=>'Week Limit', 'autocomplete' => 'off','maxlength' => 14,'onkeypress'=>'return validateFloatKeyPress(this,event);','id'=>'week_limit'])}}

                        </div>
                    </div>                       
                     
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Month Limit <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('month_limit', null, ['id'=>'month_limit','class'=>'form-control required', 'placeholder'=>'Month Limit', 'autocomplete' => 'off','maxlength' => 14,'onkeypress'=>'return validateFloatKeyPress(this,event);','id'=>'month_limit'])}}
                        </div>
                    </div>

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/users/transactions-limit')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
    @endsection