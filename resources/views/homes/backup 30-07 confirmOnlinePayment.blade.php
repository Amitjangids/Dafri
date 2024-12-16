@extends('layouts.login')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
        $("#loginform").validate();
        $(".enterkey").keyup(function (e) {
            if (e.which == 13) {
                postform();
            }
        });
        $("#user_password").keyup(function (e) {
            if (e.which == 13) {
                postform();
            }
        });
        $(".opt_input").keyup(function () { 
            if (this.value.length == this.maxLength) { 
                $(this).next('label').remove();
                $(this).next('.opt_input').focus();
            }
        });
    });

    function showPass() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
            $('#showEye').html('<img src="<?php echo HTTP_PATH; ?>/public/img/front/eye.svg" alt="Dafri Bank">');
        } else {
            x.type = "password";
            $('#showEye').html('<img src="<?php echo HTTP_PATH; ?>/public/img/front/eye.svg" alt="Dafri Bank">');
        }
    }
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
                                <td>{{$merchant_name}}</td>
                                
                            </tr>
                            <tr>
                              <th>Order ID</th>
                                <td>#{{$order_id}}</td>
                               
                            </tr>
                            <tr>
                              <th>Amount</th>
                    		   @if(!empty($user))
                                <td>{{$currency_code.' '.$order_amount}}</td>
                               @else
                    			<td>{{'USD '.$order_amount}}</td>
                    		   @endif
                            </tr>
                       
                        </tbody>
                    </table>
              </div>
            </div>
            <div class="col-sm-6">
                {{ Form::open(array('method' => 'post', 'id' => 'registerform', 'class' => ' border-form')) }} 
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
                            <input class="opt_input required digits" type="text" name="otp_code" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits" type="text" name="otp_code1" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits" type="text" name="otp_code2" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits" type="text" name="otp_code3" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits" type="text" name="otp_code4" placeholder="0" maxlength="1" autocomplete="off">
                            <input class="opt_input required digits" type="text" name="otp_code5" placeholder="0" maxlength="1" autocomplete="off">
                        </div>
                        <div class="ee er_msg confirm_msgg">@include('elements.errorSuccessMessage')</div>
                        <div class="col-sm-12">                        
                            <button class="sub-btn" type="submit" id='step_2'>
                                Continue
                            </button>
                        </div>
                        <div class="form-para col-sm-12 text-left">
                            <h5>Didn't receive a code?  <a href="javascript:void(0);" onclick="resetOTP()">Resend</a>
                            <input type="hidden" id="phone" value="<?php //echo $userInfo->phone;?>">
                            <input type="hidden" id="user_id" value="<?php //echo $userInfo->id;?>">
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

@endsection