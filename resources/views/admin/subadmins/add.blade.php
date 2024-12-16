@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Only letters, numbers and underscore allowed.");
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
        }, "Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.");

        $("#adminForm").validate();
    });
</script>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Sub Admin</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/agents')}}"><i class="fa fa-user-secret"></i> <span>Manage Sub Admins</span></a></li>
            <li class="active"> Add Sub Admin</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{ Form::open(array('method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data")) }}
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Username <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('username', null, ['class'=>'form-control required', 'placeholder'=>'Username', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email Address <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control email', 'placeholder'=>'Email Address', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::password('password', ['class'=>'form-control required passworreq', 'placeholder' => 'Password', 'minlength' => 8, 'id'=>'password'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Confirm Password <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::password('confirm_password', ['class'=>'form-control required', 'placeholder' => 'Confirm Password', 'equalTo' => '#password'])}}
                        </div>
                    </div>

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        {{Form::reset('Reset', ['class' => 'btn btn-default canlcel_le'])}}
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>

    {{ HTML::script('public/assets/js/intlTelInput.js')}}
    <script>

//        var phone_number = window.intlTelInput(document.querySelector("#phone"), {
//            separateDialCode: true,
//             preferredCountries:false,
//  onlyCountries: ['iq'],
//            hiddenInput: "phone",
//            utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
//        });
//
//
//        $("#adminForm").validate(function () {
//            var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
//            $("input[name='phone'").val(full_number);
//            alert(full_number)
//
//        });
    </script>
    @endsection




