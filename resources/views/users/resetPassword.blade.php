@extends('layouts.login')
@section('content')

<!-- logo --> 
<!-- two-part-main -->
<section class="login-same-section">
    <div class="same-login-wrapper container-fluid">
      <div class="row">
        <div class="col-lg-6">
          <div class="login-left-parent">
            <img src="{{ PUBLIC_PATH }}/assets/fronts/images/login-fixed-image.svg" alt="image">
            <div class="login-logo-box">
              <a href="#"><img src="{{ PUBLIC_PATH }}/assets/fronts/images/logo.svg" alt="image"></a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="login-right-parent">
            <div class="login-some-header">
              <div class="header-back-btn">
                <a href="{{url('/')}}"><img src="{{ PUBLIC_PATH }}/assets/fronts/images/backicon.svg" alt="image"> Back</a>
              </div>
              <div class="header-right-parent">
                <a href="personal-account-registration">Don't have an account?</a>
                <a href="{{url('/personal-login')}}" class="bold-content">Sign In</a>
              </div>
            </div>
            <div class="login-page-inner-content step-form-wrapper">
              <div class="tab custom-step-form">
                <h1>Create New <br>Password</h1>
                <p>Use a passphrase at least 15 characters long OR a password at least 8 characters long with letters and numbers.</p>
                @include('elements.errorSuccessMessage')
                    {{ Form::open(array('method' => 'post', 'id' => 'loginformF', 'class' => 'form form-signin')) }}
                <div class="login-inner-form-fileds">
                  <div class="form-group">
                    <label for="password">Password</label>
                    <div class="datebrith-custom"> 
                      {{Form::password('password', ['class'=>'form-control required passworreq', 'placeholder' => 'Enter your password', 'minlength' => 8, 'id'=>'password', 'autocomplete'=>'OFF'])}}
                      <button type="button" id="togglePassword" class="toggle-password">
                        <i class="fa fa-eye-slash"></i>
                    </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="password">Repeat Password</label>
                    <div class="datebrith-custom"> 

                      {{Form::password('confirm_password', ['class'=>'required form-control', 'placeholder' => 'Enter your password','id'=>'cpassword', 'autocomplete'=>'OFF','equalTo'=>'#password'])}}
                      <button type="button" id="togglePasswordC" class="toggle-password">
                        <i class="fa fa-eye-slash"></i>
                    </button>
                    </div>
                  </div>
                  <div class="form-group"> 
                    <button class="btn btn-primaryx" type="submit">
                        Apply
                    </button>
                  </div>
                </div> 

                {{ Form::close()}}

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
                        <h4>Reset password.</h4>
                        <p>Please reset your password.</p>
                    </div>
					@include('elements.errorSuccessMessage')
                    {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => 'form form-signin')) }}
                    <div class="form-group form-field">
                        <label>
                            Password
                        </label>
                        <div class="field-box">
							{{Form::password('password', ['class'=>'required passworreq', 'placeholder' => 'Enter your password', 'minlength' => 8, 'id'=>'password', 'autocomplete'=>'OFF'])}}
                        </div>
                    </div>
					
					<div class="form-group form-field">
                        <label>
                            Confirm Password
                        </label>
                        <div class="field-box">
							{{Form::password('confirm_password', ['class'=>'required', 'placeholder' => 'Enter your password','id'=>'cpassword', 'autocomplete'=>'OFF','equalTo'=>'#password'])}}
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