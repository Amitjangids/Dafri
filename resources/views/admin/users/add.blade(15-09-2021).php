@extends('layouts.admin')
@section('content')

<script type="text/javascript">
    $(document).ready(function () {
           $('#email').keyup(function() {
           
            $(this).val($(this).val().replace(/ +?/g, ''));
          });
        $('#password').keyup(function() {
            $(this).val($(this).val().replace(/ +?/g, ''));
          });
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
		
		$("#phone").keyup(function () {
			$("#phMinLngthErr").html('');
			if (!isNaN(this.value) && this.value != "") {
            if (this.value.length < 6) {
                //$(this).next('.opt_input').focus();
				
				$("#phMinLngthErr").html('Please enter at least 6 digits.');
            }
			else {
				$("#phMinLngthErr").html('');
			}
			}
			/*else if (isNaN(this.value)) {
			  $("#phMinLngthErr").html('Use digits only');	
			}*/
        });
    });
</script>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Personal User</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-user"></i> <span>Manage Personal Users</span></a></li>
            <li class="active"> Add Personal User</li>
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
                        <label class="col-sm-2 control-label">First Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'First Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Last Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Last Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control email required','id'=>'email', 'placeholder'=>'Email', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off'])}}
							<!--<span id="phMinLngthErr"></span>-->
                        </div>
                    </div> 

					<div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 1 <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line1', null, ['id'=>'addrs1','class'=>'form-control required', 'placeholder'=>'Enter Address Line 1', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

					<div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 2 (optional)<span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line2', null, ['id'=>'addrs2','class'=>'form-control', 'placeholder'=>'Enter Address Line 2', 'autocomplete' => 'off'])}}
                        </div>
                    </div>	
                     
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Country <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::select('country', $countrList,null, ['class' => 'form-control required','placeholder' => 'Select Country'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Currency <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php $currencyList = array('USD'=>'USD','GBP'=>'GBP','ZAR'=>'ZAR','BWP'=>'BWP','NGN'=>'NGN','NAD'=>'NAD','SZL'=>'SZL','KES'=>'KES','EUR'=>'EUR');?>
                            {{Form::select('currency', $currencyList,null, ['class' => 'form-control required','placeholder' => 'Select Currency'])}}
                        </div>
                    </div>  
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Identity Card Type <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php global $identityType; ?>
                            {{Form::select('identity_card_type', $identityType,null, ['class' => 'form-control required','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('national_identity_number', null, ['class'=>'form-control ', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload identity document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('identity_image', ['class'=>'form-control required', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Proof Of Address Type <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <?php global $addressType; ?>
                            {{Form::select('address_proof_type', $addressType,null, ['class' => 'form-control required','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('address_proof_number', null, ['class'=>'form-control ', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload proof of address document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('address_document', ['class'=>'form-control required', 'accept'=>IMAGE_DOC_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::password('password', ['class'=>'form-control required passworreq', 'placeholder' => 'Password', 'minlength' => 8, 'id'=>'password'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('image', ['class'=>'form-control required', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                    </div>                  
                    

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <input type="hidden" name="contryCode" id="contryCode" value="">
                        {{Form::submit('Submit', ['class' => 'btn btn-info','onclick'=>"setCountryCode();"])}}
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
//    alert(phone_number);

    $("#adminForm").validate(function () {
    var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
    $("input[name='phone'").val(full_number);
    alert(full_number)
    });
    
    function setCountryCode()
	{
	  var coCode = $('.iti__selected-dial-code').html();
	  document.getElementById('contryCode').value = coCode;
//	  alert(coCode+" :: "+document.getElementById('contryCode').value);	
	}
    </script>
    @endsection