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
                                <a href="forgot-password"><img src="{{ PUBLIC_PATH }}/assets/fonts/images/backicon.svg" alt="image"> Back</a>
                            </div>
                            <div class="header-right-parent">
                                <a href="{{route('signIn')}}">Already have an account?</a>
                                <a href="{{route('signIn')}}" class="bold-content">Sign In</a>
                            </div>
                        </div>
                        
                        <div class="login-page-inner-content verifyemail-parent">
                          @include('elements.errorSuccessMessage')
                            <h1>Verify Your Email<br> Address</h1>
                            <p>Please check your email. Password reset  <br>instructions have been sent to your email.</p>
                            <p><strong>@php echo $_GET['email']; @endphp</strong></p>
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
@endsection
