@extends('layouts.login')
@section('content')
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
                                <a href="{{ url('/') }}"><img
                                        src="{{ PUBLIC_PATH }}/assets/fronts/images/backicon.svg" alt="image"> Back</a>
                            </div>
                            <div class="header-right-parent">
                                <a href="#">Don't have an account?</a>
                                <a href="{{ route('signUp') }}" class="bold-content">Sign Up</a>
                            </div>
                        </div>
                        <div class="login-page-inner-content">
                            <h1>Sign In</h1>
                            <p>Welcome Back to Dafri Premier</p>
                        </div>

                        <div class="login-page-tabs-parent">
                          @include('elements.errorSuccessMessage')
                            <form action="{{ route('login') }}" method="POST" id="loginform">
                                @csrf
                                <input type="hidden" name="tabName" value="personal">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                            data-bs-target="#home" type="button" name="personal" role="tab"
                                            aria-controls="home" aria-selected="true">
                                            <figure><img src="{{ PUBLIC_PATH }}/assets/fronts/images/user-tabs.svg"
                                                    alt="image"></figure> For Personal
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                            data-bs-target="#profile" type="button" name="bussiness" role="tab"
                                            aria-controls="profile" aria-selected="false">
                                            <figure><img src="{{ PUBLIC_PATH }}/assets/fronts/images/business-tabs.svg"
                                                    alt="image"></figure> For Business
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab">
                                        <div class="login-inner-form-fileds">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" name="email" class="form-control required"
                                                    placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <div class="datebrith-custom">
                                                    <input type="password" id="password" name="password"
                                                        class="form-control required">
                                                        <button type="button" id="togglePassword" class="toggle-password">
                                                          <i class="fa fa-eye-slash"></i>
                                                      </button>
                                                </div>
                                            </div>
                                            <div class="signin-user-fields form-group custom-radio-fileds">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="flexCheckDefault">
                                                    <label class="form-check-label" for="flexCheckDefault">Keep me
                                                        signed</label>
                                                </div>
                                                <a href="forgot-password">Forgot Password?</a>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-primaryx" type="submit">Sign In</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                              <div class="login-inner-form-fileds">
                                                <div class="form-group">
                                                  <label>Email</label>
                                                  <input type="text" name="email1" class="form-control" placeholder="" >
                                                </div>
                                                <div class="form-group">
                                                  <label for="password">Password</label>
                                                  <div class="datebrith-custom">
                                                    <input type="password" id="password" name="password1" class="form-control" >
                                                    <span class="toggle-password">View</span>
                                                  </div>
                                                </div>
                                                <div class="signin-user-fields form-group custom-radio-fileds">
                                                  <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1">
                                                    <label class="form-check-label" for="flexCheckDefault1">Keep me signed</label>
                                                  </div>
                                                  <a href="#">Forgot Password?</a>
                                                </div>
                                                <div class="form-group">
                                                <button class="btn btn-primaryx" type="submit" >Sign In</button>
                                                </div>
                                              </div>
                                            </div> -->
                                </div>
                            </form>
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


    <script>
        const tabButtons = document.querySelectorAll('#myTab button');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const name = button.getAttribute('name');
                const hiddenInput = document.querySelector('input[name="tabName"]');
                if (hiddenInput) {
                    hiddenInput.value = name;
                }
            });
        });
    </script>
@endsection
