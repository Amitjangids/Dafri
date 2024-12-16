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
            var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
        }, "Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.");

        $("#adminForm").validate();
    });
</script>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Admin</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/admins/list-subadmin')}}"><i class="fa fa-user"></i> <span>Manage Admin's</span></a></li>
            <li class="active"> Add Admin</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::open( ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}
            <div class="form-horizontal">
                <div class="box-body">
					<div class="form-group">
                        <label class="col-sm-2 control-label">Department <span class="require">*</span></label>
                        <div class="col-sm-10">
                         {{Form::select('role_id', $roleList,null, ['class' => 'form-control required'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('name', null, ['class'=>'form-control required', 'placeholder'=>'Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Surname <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('surname', null, ['class'=>'form-control required', 'placeholder'=>'Surname', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
                   <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Username <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('user_name', null, ['class'=>'form-control required', 'placeholder'=>'Username', 'autocomplete' => 'off'])}}
                        </div>
                    </div> -->
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control required email', 'placeholder'=>'Email', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::password('pass', ['class'=>'form-control passworreq', 'placeholder'=>'Password', 'autocomplete' => 'off','id'=>'password', 'minlength' => 8])}}
                        </div>
                    </div> 

					<div class="form-group">
                        <label class="col-sm-2 control-label">Confirm Password <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::password('cpass', ['class'=>'form-control', 'placeholder'=>'Confirm Password', 'autocomplete' => 'off', 'equalTo' => '#password'])}}
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
    var phone_number = window.intlTelInput(document.querySelector("#phone"), {
    separateDialCode: true,
    preferredCountries:false,
    //onlyCountries: ['iq'],
    hiddenInput: "phone",
    utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#adminForm").validate(function () {
    var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
    $("input[name='phone'").val(full_number);
    alert(full_number)
    });
    </script>
    @endsection