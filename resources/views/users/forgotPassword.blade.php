@extends('layouts.login')
@section('content') 

    <section class="login-same-section">
        <div class="same-login-wrapper container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login-left-parent">
                        <img src="{{ PUBLIC_PATH }}/assets/fonts/images/login-fixed-image.svg" alt="image">
                        <div class="login-logo-box">
                            <a href="#"><img src="{{ PUBLIC_PATH }}/assets/fonts/images/logo.svg" alt="image"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login-right-parent">
                        <div class="login-some-header">
                            <div class="header-back-btn">
                                <a href="personal-login"><img src="{{ PUBLIC_PATH }}/assets/fonts/images/backicon.svg"
                                        alt="image"> Back</a>
                            </div>
                            <div class="header-right-parent">
                                <a href="personal-account-registration">Don't have an account?</a>
                                <a href="personal-login" class="bold-content">Sign In</a>
                            </div>
                        </div>
                        <div class="login-page-inner-content step-form-wrapper">
                            <div class="tab custom-step-form">
                                <h1>Reset Password</h1>
                                <p>Enter your email address to receive a password reset link.</p>

                                @include('elements.errorSuccessMessage')
                                {{ Form::open(['method' => 'post', 'id' => 'loginform', 'class' => 'form form-signin']) }}
                                <div class="login-inner-form-fileds">
                                    <div class="form-group">
                                        <label>Email</label>
                                        {{ Form::email('email', Cookie::get('user_email'), ['class' => 'form-control required', 'placeholder' => 'Your e-mail', 'autocomplete' => 'OFF']) }}
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primaryx" type="submit">
                                            Send Verification Code
                                        </button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <div class="login-bottom-fixed-content">
                            <ul>
                                <li><a href="#">Terms & Conditions</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <section class="two-part-main">
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
                        <h4>Forgot password.</h4>
                        <p>Please enter your registered email. We will 
                            send you an email with the instructions for 
                            setting up a new password for your DafriBank 
                            account.</p>
                    </div>
					@include('elements.errorSuccessMessage')
                    {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => 'form form-signin')) }}
                    <div class="form-group form-field">
                        <label>
                            Your e-mail
                        </label>
                        <div class="field-box">
                            {{Form::text('email', Cookie::get('user_email'), ['class'=>'form-control required', 'placeholder'=>'Your e-mail', 'autocomplete'=>'OFF'])}}
                        </div>
                    </div>


                    <button class="sub-btn" type="submit">
                        Continue
                    </button>

                    {{ Form::close()}}
                </div>
            </div>
        </div>
    </div>
</section> --}}
@endsection
