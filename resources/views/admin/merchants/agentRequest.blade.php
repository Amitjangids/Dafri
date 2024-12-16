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
</script>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Agent</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/users')}}"><i class="fa fa-user"></i> <span>Manage Personal Users</span></a></li>
            <li class="active"> Add Agent</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
			{{Form::model($user, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">First Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('director_name', null, ['class'=>'form-control required', 'placeholder'=>'First Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Last Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Last Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Country <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::select('country', $countrList,null, ['class' => 'form-control required','placeholder' => 'Select Country'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Commission <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('commission', null, ['id'=>'commission','class'=>'form-control required', 'placeholder'=>'Commission', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Minimum Deposit/Withdrawal <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('min_deposit', null, ['id'=>'min_deposit','class'=>'form-control required digits', 'placeholder'=>'Minimum Deposit/Withdrawal', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Physical Address <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('address', null, ['id'=>'address','class'=>'form-control required', 'placeholder'=>'Physical Address', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off', 'minlength' => 8, 'maxlength' => 16])}}

                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Payment Methods Supported <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('payment_method', null, ['id'=>'payment_method','class'=>'form-control required', 'placeholder'=>'Payment Methods Supported', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control email required', 'placeholder'=>'Email', 'autocomplete' => 'off','readonly'])}}
                        </div>
                    </div>  
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Description <span class="require">*</span></label>
                        <div class="col-sm-10">
                        {{Form::text('desc', null, ['id'=>'desc','class'=>'form-control required', 'placeholder'=>'Description', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('profile_image', ['class'=>'form-control required', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
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
	//var ph = document.querySelector("#phone");
	//ph = ph.trim();
	var phone_number = window.intlTelInput(document.querySelector("#phone"), {
    //var phone_number = window.intlTelInput(ph, {
    separateDialCode: true,
    preferredCountries:false,
    //onlyCountries: ['iq'],
    hiddenInput: "phone",
    utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#adminForm").validate(function () {
    var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
	full_number = full_number.trim();
	//alert(full_number);
    $("input[name='phone'").val(full_number);
    //alert(full_number)
    });
	
	$( document ).ready(function() {
      var phn = document.getElementById('phone').value;
	  phn = phn.trim();
	  setTimeout(function(){ document.getElementById('phone').value = phn; }, 3000);	  
	});
    </script>
    @endsection