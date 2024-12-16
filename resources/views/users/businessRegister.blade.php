@extends('layouts.login')
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

//        $("#registerform").validate();

        $("#phone").keyup(function () {
            $("#phMinLngthErr").html('');
            if (!isNaN(this.value) && this.value != "") {
                if (this.value.length < 6) {
                    //$(this).next('.opt_input').focus();

                    $("#phMinLngthErr").html('Please enter at least 6 digits.');
                } else {
                    $("#phMinLngthErr").html('');
                }
            } else if (isNaN(this.value)) {
                $("#phMinLngthErr").html('Use digits only');
            }
        });

        /*$("#business_name").keyup(function () {
         if (this.value.length == this.maxLength) {
         $('#director_name').focus();
         }
         });
         
         $("#director_name").keyup(function () {
         if (this.value.length == this.maxLength) {
         $('#email').focus();
         }
         });*/

    });
</script>
<!-- logo -->
<div class="pre-regsiter-logo">
    <div class="wrapper">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo-white">
                    <a href="{!! HTTP_PATH !!}">{{HTML::image(BLACK_LOGO_PATH, SITE_TITLE)}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- two-part-main -->
<section class="two-part-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 gray-bg">
                <div class="left-main-heading ">
                    <h1>Leap in banking, the world <span>loves<span>.</span></span></h1>
                    <p>Explore an easy and better way to save, make payments, manage your money and your business whenever you want, wherever you are!</p>
                </div>
            </div>
            <div class="col-sm-6">
                {{ Form::open(array('method' => 'post', 'id' => 'registerform', 'class' => ' border-form', 'enctype' => "multipart/form-data",'onsubmit'=>'return disable_submit();')) }} 
                <div class="form-page sign-page">
                    <h6 class="steps">
                        Step 1/3
                    </h6>
                    <div class="form-heading">
                        <h4><span>Register </span>
                            (Business Account)</h4>
                        <p>Already have an account? <a href="{{URL::to('business-login')}}"> Log in </a></p>
                    </div>
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>
                                Business name
                            </label>
                            {{Form::text('business_name', null, ['class'=>'required', 'placeholder'=>'Enter your business name', 'autocomplete'=>'OFF', 'id'=>'business_name', 'minlength' => 1, 'maxlength' => 50])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Director name
                            </label>
                             <div class="gender-field">
                            {{Form::text('director_name', null, ['class'=>'required', 'id'=>'director_name', 'placeholder'=>'Enter your director name', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                            <div class="gender-select">
                             <?php global $sernameList; ?>   
                            {{Form::select('gender', $sernameList,null, ['class' => 'required'])}}  
                            </div>  
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Business email
                            </label>
                            {{Form::text('email', Cookie::get('user_email_address'), ['class'=>'required email', 'id'=>'email', 'placeholder'=>'Enter your business email', 'autocomplete'=>'OFF', 'onkeypress'=>"return event.charCode != 32"])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Mobile number
                            </label>
                            {{Form::text('phone', null, ['id'=>'phone','class'=>'required digits', 'placeholder'=>'Enter your mobile number', 'minlength'=>6, 'autocomplete'=>'OFF'])}}
                            <!--<span id="phMinLngthErr"></span>-->
                            <!--<label for="phone" id="pherror" generated="true" class="error">Please enter at least 6 characters.</label> -->
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>Country</label>
                            <div class="selectdiv">
                                {{Form::select('country_id', $countrList,null, ['class' => 'required','placeholder' => 'Choose country'])}}  
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>
                                Currency
                            </label>
                            <div class="selectdiv">
                                <?php 
                            global $currencyList;
                            ?>
                                {{Form::select('currency', $currencyList,null, ['class' => 'form-control required','placeholder' => 'Choose currency'])}}
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>
                                Business type
                            </label>
                            <div class="selectdiv">
                                <?php global $businessType; ?>
                                {{Form::select('business_type', $businessType,null, ['class' => 'required','placeholder' => 'Select business type'])}}

                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>
                                Business registration number
                            </label>
                            <!--<div class="selectdiv"> -->
                            {{Form::text('registration_number', null, ['class'=>'form-control required', 'placeholder'=>'Enter business registration number', 'autocomplete' => 'off'])}}

                            <!-- </div> -->
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Address Line 1</label>
                            {{Form::text('addrs_line1', null, ['id'=>'addrs1','class'=>'required', 'placeholder'=>'Enter Address Line 1'])}}
                        </div>

                        <div class="form-group col-sm-6">
                            <label>Address Line 2 (Optional)</label>
                            {{Form::text('addrs_line2', null, ['id'=>'addrs2','class'=>'', 'placeholder'=>'Enter Address Line 2'])}}
                        </div>							

                        <div class="form-group col-sm-6">
                            <label>
                                Password
                            </label>
                            {{Form::password('password', ['class'=>'required passworreq', 'placeholder' => 'Enter your password', 'minlength' => 8, 'id'=>'password', 'onkeypress'=>"return event.charCode != 32"])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Confirm Password
                            </label>
                            {{Form::password('confirm_password', ['class'=>'required', 'placeholder' => 'Enter your password again', 'equalTo' => '#password', 'onkeypress'=>"return event.charCode != 32"])}}
                        </div>

                        <div class="form-group col-sm-12">
                            <label>
                            Referral Link (Optional)
                            </label>
                            {{Form::text('referral',$refId, ['class'=>'', 'placeholder'=>'Enter Referral Link', 'autocomplete'=>'OFF',$readonly,'oninput'=>'myFunction(this)'])}}
                        </div>

                        <div class="form-group col-sm-12 check-field-box">
                            <div class="">
                                <!--{{Form::checkbox('terms', '1', '', array('class' => "required", 'id' =>"terms"))}}-->
                                <!--<input type="checkbox" class="required" id="terms" name="terms">-->
{{--<!--                                {{Form::checkbox('terms', '1', '', array('class' => "required", 'id' =>"terms"))}}
                                <label for="terms">I agree to the <a target="_blank" href="{{URL::to('terms-condition')}}">Terms and Conditions</a> and <a target="_blank" href="{{URL::to('privacy-policy')}}">Privacy Policy</a>.</label>
                                --> --}}
                                <label >By clicking continue button you agree to <a target="_blank" href="{{URL::to('terms-condition')}}">Terms and Conditions</a> and <a target="_blank" href="{{URL::to('privacy-policy')}}">Privacy Policy</a>.</label>

                            </div>

                            <!--  {{Form::checkbox('user_remember', '1', Cookie::get('user_remember'), array('class' => "", 'id' =>"remember_sec"))}} -->

                        </div>

                        <div class="col-sm-12">
                            <!-- <input type="hidden" name="referral" value="{{$refId}}"> -->
                            <input type="hidden" name="parent_id" value="{{$pid}}">
                            <input type="hidden" name="contryCode" id="contryCode" value="">
                            <button class="sub-btn button_disable" type="submit" id='step_1' onclick="setCountryCode();">
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</section>
{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
{{ HTML::script('public/assets/js/intlTelInput.js')}}
<script>
    var phone_number = window.intlTelInput(document.querySelector("#phone"), {
        separateDialCode: true,
        preferredCountries: false,
        //onlyCountries: ['iq'],
        hiddenInput: "phone",
        utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

//    $("#registerform").validate(function () {
//    var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
//    $("input[name='phone'").val(full_number);
//    //alert(full_number)
//    });


function myFunction(x)
 {
     var ref_link=x.value;
     var check_exist=ref_link.indexOf("{{URL::to('/choose-account?refid=')}}");
     if(check_exist >=0)
     {
     var split = ref_link.split("{{URL::to('/choose-account?refid=')}}");
     var ref_actual_link=split[1];
     x.value=ref_actual_link;
     return true;
     }

     var check_exist=ref_link.indexOf("{{URL::to('/business-account-registration?refId=')}}");
     if(check_exist >=0)
     {
     var split = ref_link.split("{{URL::to('/business-account-registration?refId=')}}");
     var ref_actual_link=split[1];
     x.value=ref_actual_link;
     return true;
     }


 }


    $(document).ready(function () {
        
        $("#registerform").validate();
//        $("#registerform").validate({
//            submitHandler: function (form) {
//                if ($('#terms').is(':checked')) {
//                    $("#registerform").submit();
//                } else{
//                    $('#error_message').html('Please accept terms and conditions and privacy policy');
//                    $('#error-alert-Modal').modal('show');
//                }
//            }
//        });
//        $("#registerform").validate();
//        $("#registerform").validate({
//            submitHandler: function (form) {
//
////                var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
////        $("input[name='phone'").val(full_number);
//
//                var form = $('#registerform');
//                var error = false;
//
//                if ($('#terms').is(':checked')) {
////                var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
////                $("input[name='phone'").val(full_number);
//                    form.submit();
//                    return true;
//                } else {
//                    $('#error_message').html('Please accept terms and conditions and privacy policy');
//                    $('#error-alert-Modal').modal('show');
////                alert('Please accept terms and conditions and privacy policy');
//                    return false;
//                }
//            }
//        });
    });

    function setCountryCode()
    {
        var coCode = $('.iti__selected-dial-code').html();
        document.getElementById('contryCode').value = coCode;
        //alert(coCode+" :: "+document.getElementById('contryCode').value);	
    }
</script>

<script>
    function showFilename()
    {
        var name = document.getElementById('file-input');
        document.getElementById('target_busness_reg_doc').innerHTML = name.files.item(0).name;
    }
    function showFilename1()
    {
        var name = document.getElementById('file-input1');
        document.getElementById('target_file1').innerHTML = name.files.item(0).name;
    }
    function showFilename2()
    {
        var name = document.getElementById('file-input2');
        document.getElementById('target_file2').innerHTML = name.files.item(0).name;
    }
    function showFilename3()
    {
        var name = document.getElementById('file-input3');
        document.getElementById('target_file3').innerHTML = name.files.item(0).name;
    }
    function showFilename4()
    {
        var name = document.getElementById('file-input4');
        document.getElementById('target_file4').innerHTML = name.files.item(0).name;
    }
    function showFilename5()
    {
        var name = document.getElementById('file-input5');
        document.getElementById('target_file5').innerHTML = name.files.item(0).name;
    }
    function showFilename6()
    {
        var name = document.getElementById('file-input6');
        document.getElementById('target_file6').innerHTML = name.files.item(0).name;
    }
    function showFilename7()
    {
        var name = document.getElementById('file-input7');
        document.getElementById('target_file7').innerHTML = name.files.item(0).name;
    }


    function disable_submit()
    {
    var empty_field=0;    
    $(".required").each(function() {
    if($(this).val()=="")
    {
    empty_field=1;  
    }
    });
    if(empty_field==0 && $(".required").hasClass('error')==false)
    {
    $('.button_disable').prop('disabled', true);   
    return true;
    }
    return false;
    }


</script>
@endsection