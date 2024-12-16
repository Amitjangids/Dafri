@extends('layouts.login')
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
        $("#registerform").validate();

        $(".opt_input").keyup(function () { 
            if (this.value.length == this.maxLength) { 
                $(this).next('label').remove();
                $(this).next('.opt_input').focus();
            }
        });

    });

    function hideerrorsucc() {
        $('.close.close-sm').click();
    }

    function resetOTP() {
        var phone = $('#phone').val();
		var user_id = $('#user_id').val();
        $.ajax({
            url: "{!! HTTP_PATH !!}/resentOtp",
            type: "POST",
            //data: {'phone': phone, _token: '{{csrf_token()}}'},
            data: {'user_id': user_id,'phone': phone, _token: '{{csrf_token()}}'},
			success: function (result) {
                $('#success_message').html('OTP sent successfully');
                                                                    $('#success-alert-Modal').modal('show');
            }
        });
    }


</script>

<script>
        $(document).ready(function () {
            $('.d0').focus();
    $('input').bind('paste', function (e) {
        var $start = $(this);
        var source

        //check for access to clipboard from window or event
        if (window.clipboardData !== undefined) {
            source = window.clipboardData
        } else {
            source = e.originalEvent.clipboardData;
        }
        var data = source.getData("Text");
        console.log("data.length",data.length);
        if (data.length > 0) {
    var columns = data.split("");
      for (var i = 0; i < columns.length; i++) {
        $('.d'+i).focus();
    $('.d'+i).val(columns[i]);
                    }
        e.preventDefault();
            
        }
    });     });
</script>
<!-- logo -->
<div class="pre-regsiter-logo">
    <div class="wrapper">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo-white">
                    <a href="{!! HTTP_PATH !!}"> {{HTML::image(BLACK_LOGO_PATH, SITE_TITLE)}}</a>
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
                {{ Form::open(array('method' => 'post', 'id' => 'registerform', 'class' => ' border-form','onsubmit'=>'return disable_submit();')) }} 
                <div class="form-page sign-page" id="step_account2" >
                    <h6 class="steps">
                        Step 2/3
                    </h6>
                    <div class="form-heading">
                        <h4><span>Verification  </span>
                        </h4>
                        <p>Please enter the 6 digit code sent to your registered email address and mobile number.</p>
                    </div>
                    <div class="mob-icon">
                        {{HTML::image('public/img/front/mail.svg', SITE_TITLE)}}
                    </div>
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="row">
                        <div class="veri-input col-sm-12">
                            <input class="opt_input required digits d0" type="text" name="otp_code" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d1" type="text" name="otp_code1" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d2" type="text" name="otp_code2" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d3" type="text" name="otp_code3" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d4" type="text" name="otp_code4" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d5" type="text" name="otp_code5" placeholder="0" maxlength="1" autocomplete="off">
                        </div>

                        <div class="col-sm-12">                        
                            <button class="sub-btn button_disable" type="submit" id='step_2'>
                                Continue
                            </button>
                        </div>
                        <div class="form-para col-sm-12 text-left">
                            <h5>Didn't receive a code?  <a href="javascript:void(0);" onclick="resetOTP()">Resend</a>
							<input type="hidden" id="phone" value="<?php echo $userInfo->phone;?>">
							<input type="hidden" id="user_id" value="<?php echo $userInfo->id;?>">
                            </h5>
                        </div>

                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</section>

<script>
function disable_submit()
    {
    var empty_field=0;    
    $(".required").each(function() {
    if($(this).val()=="")
    {
    empty_field=1;  
    }
    });
    if(empty_field==0)
    {
    $('.button_disable').prop('disabled', true);   
    return true;
    }
    return false;
    }
</script>    

@endsection