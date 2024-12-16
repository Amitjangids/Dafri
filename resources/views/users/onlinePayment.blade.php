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
            <td>Robert Jons</td>
            
        </tr>
        <tr>
          <th>Order ID</th>
            <td>#1235465</td>
           
        </tr>
        <tr>
          <th>Amount</th>
            <td>$500</td>
            
        </tr>
   
    </tbody>
</table>
              </div>
            </div>
            <div class="col-sm-6">
                <div class="form-page">
                    <div class="form-heading pay-page">
                        <h4>Login to Pay</h4>
                        <p>Log in with your data that you entered during your registration.</p>
                    </div>
                    {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => '')) }}     
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="form-group form-field">
                        <label>
                            Your e-mail
                        </label>
                        <div class="field-box">                                	
                            {{Form::text('email', Cookie::get('user_email'), ['class'=>'required email enterkey', 'placeholder'=>'Your e-mail', 'autocomplete'=>'OFF'])}}
                        </div>
                    </div>
                    <div class="form-group form-field">
                        <label>
                            Your password
                        </label>
                        <div class="field-box">
                            {{Form::input('password', 'password', Cookie::get('user_password'), array('class' => "required enterkeyl", 'placeholder' => 'Your password', 'id'=>'password','minlength'=>8))}}
                            <i onclick="showPass()" id="showEye">{{HTML::image('public/img/front/eye.svg', SITE_TITLE)}}</i>
                        </div>
                    </div>
                    <div class="form-group check-field-box">


                       <!--  {{Form::checkbox('user_remember', '1', Cookie::get('user_remember'), array('class' => "", 'id' =>"remember_sec"))}} -->
                       
                    </div>
                    <button class="sub-btn" type="submit">
                        Log in
                    </button>
                  
                    {{ Form::close()}}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection