@extends('layouts.login')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
    $("#registerform").validate();

    $(".opt_input").keyup(function () { 
            if (this.value.length == this.maxLength) { 
                $(this).next('label').remove();
                $(this).next('.opt_input').focus();
            }
        });

    });
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
                <div class="payment-details">
                    <h5>Payment Details</h5>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <td>
                                 <?php 
               if ($user->user_type == "Personal") {
                $user_name = $user->first_name . ' ' . $user->last_name;
            } else if ($user->user_type == "Business") {
                $user_name = $user->business_name;
            } else if ($user->user_type == "Agent" && $user->first_name == "") {
                $user_name = $user->business_name;
            } else if ($user->user_type == "Agent" && $user->first_name != "") {
                $user_name = $user->first_name . ' ' . $user->last_name;
            }
             
             ?>     
                               {{$user->gender}} {{strtoupper($user_name)}}</td>

                            </tr>

                            <tr>
                                <th>Amount</th>
                                @if(!empty($user))
                                <td>{{$currency_code.' '.$order_amount}}</td>
                                @endif
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-6">
                {{ Form::open(array('method' => 'post', 'id' => 'registerform', 'class' => ' border-form','onsubmit'=>'return disable_submit();')) }} 
                <div class="form-page sign-page" id="step_account2" >

                    <div class="form-heading">
                        <h4><span>Verification  </span>
                        </h4>
                        <p>Please enter the 6 digit code sent to your registered email address.</p>
                    </div>
                    <div class="mob-icon">
                        {{HTML::image('public/img/front/mail.svg', SITE_TITLE)}}
                    </div>

                    <div class="row">
                        <div class="veri-input col-sm-12">
                            <input class="opt_input required digits d0" type="text" name="otp_code" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d1" type="text" name="otp_code1" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d2" type="text" name="otp_code2" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d3" type="text" name="otp_code3" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d4" type="text" name="otp_code4" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits d5" type="text" name="otp_code5" placeholder="0" maxlength="1" autocomplete="off">
                        </div>
                        <div class="ee er_msg confirm_msgg">
                            <?php if(Session::has('success_payment_message')){ ?>
                            <div class="alert alert-success">
                                <?php echo Session::get('success_payment_message'); ?>
                            </div>   
                            <?php } ?>
                        </div>
                        <div class="col-sm-12">                        
                            <button class="sub-btn button_disable" type="submit" id='step_2'>
                                Continue
                            </button>
                        </div>
                        <div class="form-para col-sm-12 text-left">
                            <h5>Didn't receive a code?  <a href="javascript:void(0);" onclick="resetOTP()">Resend</a>
                                <input type="hidden" id="phone" value="<?php //echo $userInfo->phone; ?>">
                                <input type="hidden" id="user_id" value="<?php //echo $userInfo->id; ?>">
                            </h5>
                        </div>

                    </div>
                </div>
                {{ Form::close()}}
            </div>

            <!-- <div class="col-sm-6">
                        <div class="form-page">                     
            <div class="ee er_msg confirm_msgg">@include('elements.errorSuccessMessage')</div>
                        {{ Form::open(array('method' => 'post', 'id' => 'onlinePaymntFrm', 'class' => '')) }}
                        <input type="hidden" name="flag" value="true">
            <button class="sub-btn" type="submit">Confirm</button>
                        {{Form::close()}}
            </div>
            </div> -->
        </div>
    </div>
</section>
<script type="text/javascript">
    function resetOTP() {
    var user_id = {{$userInfo -> id}};
    $.ajax({
    //url: "{!! HTTP_PATH !!}/resentOtp",
    url: "<?php echo HTTP_PATH; ?>/resentVerifyOtpAPI",
            type: "POST",
            data: {'user_id': user_id, _token: '{{csrf_token()}}'},
            success: function (result) {
            //alert(result);
            $('#success_message').html('OTP sent successfully');
            $('#success-alert-Modal').modal('show');
            }
    });
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