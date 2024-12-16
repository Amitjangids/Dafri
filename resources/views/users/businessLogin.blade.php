@extends('layouts.login')
@section('content')
<script type="text/javascript">
   
    $(document).ready(function () {
        $('#emails').keyup(function() {
           
            $(this).val($(this).val().replace(/ +?/g, ''));
          });
        $('#password').keyup(function() {
           
            $(this).val($(this).val().replace(/ +?/g, ''));
          });

        $("#loginform").validate();        
        $(".enterkey").keyup(function(e) {
            if(e.which == 13) {
                postform();
            }
        });
        $("#user_password").keyup(function(e) {
            if(e.which == 13) {
                postform();
            }
        });
    });
    
    function postform(){
        if($("#loginform").valid()){
            $('#btnloader').show();
            $("#loginform").submit();
        }
    }
    
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
                    <div class="left-main-heading ">
                        <h1>Leap in banking, the world <span>loves<span>.</span></span></h1>
                        <p>Explore an easy and better way to save, make payments, manage your money and your business whenever you want, wherever you are!</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-page">
                        <div class="form-heading">
                            <h4>Log in.</h4>
                            <p>Log in with your data that you entered during your registration.</p>
                        </div>
                        {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => '')) }}  
                        <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                            <div class="form-group form-field">
                                <label>
                                    Your e-mail
                                </label>
                                <div class="field-box">
                                	
                                
                                {{Form::text('email', Cookie::get('user_email_address'), ['class'=>'form-control required email enterkey', 'placeholder'=>'Your e-mail', 'autocomplete'=>'OFF', 'onkeypress'=>"return event.charCode != 32", 'id'=>'emails'])}}
                                </div>
                            </div>
                             <div class="form-group form-field">
                                <label>
                                   Your password
                                </label>
                                <div class="field-box">
                                {{Form::input('password', 'password', Cookie::get('user_password'), array('class' => "required form-contro enterkeyl", 'placeholder' => 'Your password', 'id'=>'password','minlength'=>8, 'onkeypress'=>"return event.charCode != 32"))}}
                                <i onclick="showPass()" id="showEye">{{HTML::image('public/img/front/eye.svg', SITE_TITLE)}}</i>
                                </div>
                            </div>
                            <div class="form-group check-field-box">
<div class=" check-new">
{{Form::checkbox('user_remember', '1', Cookie::get('user_remember'),array('id'=>'remember_sec'))}}
      <label for="remember_sec">Keep me logged in</label>
    </div>
                            	<!-- {{Form::checkbox('user_remember', '1', Cookie::get('user_remember'), array('class' => "", 'id' =>"remember_sec"))}}
                            	<p>Keep me logged in</p> -->
                            </div>
                            <button class="sub-btn" type="submit">
                            	Log in
                            </button>
                            <div class="form-para">
                            	<h5>Don't have an account? <a href="{{URL::to('business-account-registration')}}">Register</a>
</h5>
<a href="{{URL::to('forgot-password')}}">Forgot password?</a>
                            </div>
                        {{ Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection