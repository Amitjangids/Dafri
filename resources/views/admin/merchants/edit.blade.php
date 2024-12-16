@extends('layouts.admin')
@section('content')

<?php
function matchSel($first,$second)
{
  if ($first == $second)
   return "selected";	  
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        
        var str = $('#phone').val();
        str = str.replace(' ', '');
        $('#phone').val(str);
        
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
            //var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input);
        }, "Password must be a combination of Numbers, Uppercase & Lowercase Letters.");
        $("#adminForm").validate();
		
		$("#phone").keyup(function () {
			$("#phMinLngthErr").html('');
			if (!isNaN(this.value) && this.value != "") {
            // if (this.value.length < 6) {
            //     //$(this).next('.opt_input').focus();
				
			// 	$("#phMinLngthErr").html('Please enter at least 6 digits.');
            // }
			// else {
			// 	$("#phMinLngthErr").html('');
			// }
			}
			/*else if (isNaN(this.value)) {
			  $("#phMinLngthErr").html('Use digits only');	
			}*/
        });

        $("#radio").click(function () {
            $(".main_section").hide();
            $("#station_sec").show();
        });
        $("#advertising").click(function () {
            $(".main_section").hide();
            $("#agency_sec").show();
        });
        $("#advertiser").click(function () {
            $(".main_section").hide();
            $("#advertiser_sec").show();
        });
    });
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

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Business User</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-users"></i> <span>Manage Business Users</span></a></li>
            <li class="active"> Edit Business User</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($recordInfo, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('business_name', null, ['class'=>'form-control required', 'placeholder'=>'Business Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

            <!--         <div class="form-group">
                        <label class="col-sm-2 control-label">Business Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <div class="row perosnal-edit">
                                <div class="col-sm-2">
                                    <div class="perosnal-select-box">
                                    <?php global $sernameList; ?>    
                                    {{Form::select('gender', $sernameList,null, ['class' => 'required form-control','placeholder'=>'Select Prefix'])}}  
                                    </div>
                                </div>
                                <div class="col-sm-10">
                                     <div class="add-select-option">
                                        {{Form::text('business_name', null, ['class'=>'form-control required', 'placeholder'=>'Business Name', 'autocomplete' => 'off'])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    
                 <!--    <div class="form-group">
                        <label class="col-sm-2 control-label"> <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('director_name', null, ['class'=>'form-control required', 'placeholder'=>'Director name', 'autocomplete' => 'off'])}}
                        </div>
                    </div> -->

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Director name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            <div class="row perosnal-edit">
                                <div class="col-sm-1">
                                    <div class="perosnal-select-box">
                                    <?php global $sernameList; ?>    
                                    {{Form::select('gender', $sernameList,null, ['class' => 'required form-control','placeholder'=>'Select Prefix'])}}  
                                    </div>
                                </div>
                                <div class="col-sm-11">
                                     <div class="add-select-option">
                                        {{Form::text('director_name', null, ['class'=>'form-control required', 'placeholder'=>'Director name', 'autocomplete' => 'off'])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', null, ['class'=>'form-control email required', 'placeholder'=>'Business Email', 'autocomplete' => 'off', 'id'=>'email',])}}
                        </div>
                    </div>  


                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off', 'minlength' => 6, 'maxlength' => 16])}}
                            <span id="phMinLngthErr"></span>
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
                            <?php 
                            global $currencyList;
                            ?>
                            {{Form::select('currency', $currencyList,null, ['class' => 'form-control required','placeholder' => 'Select Currency'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Type <span class="require"></span></label>
                        <div class="col-sm-10">
                            <?php global $businessType; ?>
                            {{Form::select('business_type', $businessType,null, ['class' => 'form-control ','placeholder' => 'Select Business Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business registration number <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('registration_number', $recordInfo->registration_number, ['class'=>'form-control ', 'placeholder'=>'Business registration number', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Registration Document <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('registration_document', ['class'=>'form-control', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        @if($recordInfo->registration_document != '')
                            <div class="showeditimage">{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->registration_document, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!--<div class="help-text"><a href="{{ URL::to('admin/merchants/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div>-->
                            @endif
                        </div>
                    </div>
                    <?php /*<div class="form-group">
                        <label class="col-sm-2 control-label">Identity Card Type <span class="require"></span></label>
                        <div class="col-sm-10">
                            <?php global $identityType; ?>
                            {{Form::select('identity_card_type', $identityType,$recordInfo->identity_card_type, ['class' => 'form-control','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('national_identity_number', $recordInfo->national_identity_number, ['class'=>'form-control', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload identity document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('identity_image', ['class'=>'form-control ', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                         @if($recordInfo->identity_image != '')
                            <div class="showeditimage">{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!--<div class="help-text"><a href="{{ URL::to('admin/merchants/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div>-->
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Proof Of Address Type <span class="require"></span></label>
                        <div class="col-sm-10">
                            <?php global $addressType; ?>
                            {{Form::select('address_proof_type', $addressType,$recordInfo->address_proof_type, ['class' => 'form-control','placeholder' => 'Select Document Type'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Enter Document Number (if applicable) <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('address_proof_number', $recordInfo->address_proof_number, ['class'=>'form-control', 'placeholder'=>'Enter Document Number (if applicable)', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Upload proof of address document<span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('address_document', ['class'=>'form-control ', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png, pdf (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                         @if($recordInfo->address_document != '')
                            <div class="showeditimage">{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_document, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!--<div class="help-text"><a href="{{ URL::to('admin/merchants/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div>-->
                            @endif
                        </div>
                    </div>*/?>

		 <div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 1 <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line1', null, ['class'=>'form-control required', 'placeholder'=>'Enter Address Line 1', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label">Address Line 2 <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::text('addrs_line2', null, ['class'=>'form-control ', 'placeholder'=>'Enter Address Line 2', 'autocomplete' => 'off'])}}
                        </div>
                    </div> 

<!--                    <div class="form-group">
                        <label class="col-sm-2 control-label">Selfie <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::file('profile_image', ['class'=>'form-control', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                            @if($recordInfo->image != '')
                            <div class="showeditimage">{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                             <div class="help-text"><a href="{{ URL::to('admin/users/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div> 
                            @endif
                        </div>
                    </div>       -->

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::password('password', ['class'=>'form-control  ', 'placeholder' => 'Password', 'minlength' => 8, 'id'=>'password'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image <span class="require"></span></label>
                        <div class="col-sm-10">
                            {{Form::file('image', ['class'=>'form-control', 'accept'=>IMAGE_EXT])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                            @if($recordInfo->image != '')
                            <div class="showeditimage">{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</div>
                            <!--<div class="help-text"><a href="{{ URL::to('admin/merchants/deleteimage/'.$recordInfo->slug)}}" title="Delete Image" class="canlcel_le"  onclick="return confirm('Are you sure you want to delete?')">Delete Image</a></div>-->
                            @endif
                        </div>
                    </div>                        
                    
                    
                      

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <input type="hidden" name="country_code" id="contryCode" value="{{$recordInfo->country_code}}">
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/merchants')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
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
    formatOnDisplay: false,
    utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#adminForm").validate(function () {
    var full_number = phone_number.getNumber();
    $("input[name='phone'").val(full_number);
    alert(full_number)
    });
    
    function setCountryCode()
	{
	  var coCode = $('.iti__selected-dial-code').html();
	  document.getElementById('contryCode').value = coCode;
	  //alert(coCode+" :: "+document.getElementById('contryCode').value);	
	}
    </script>
    @endsection