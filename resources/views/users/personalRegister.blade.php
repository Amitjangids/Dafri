@extends('layouts.login')
@section('content')
<section class="login-same-section">
    <div class="same-login-wrapper container-fluid">
      <div class="row">
        <div class="col-lg-6">
          <div class="login-left-parent">
            <img src="{{PUBLIC_PATH}}/assets/fonts/images/login-fixed-image.svg" alt="image">
            <div class="login-logo-box">
              <a href="#"><img src="{{PUBLIC_PATH}}/assets/fonts/images/logo.svg" alt="image"></a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="login-right-parent">
            <div class="login-some-header">
              <div class="header-back-btn">
                <a href="{{url('/')}}"><img src="{{PUBLIC_PATH}}/assets/fonts/images/backicon.svg" alt="image"> Back</a>
              </div>
              <div class="header-right-parent">
                <a href="#">Already have an account?</a>
                <a href="{{route('signIn')}}" class="bold-content">Sign In</a>
              </div>
            </div>
            <div class="login-page-inner-content">
              <h1>Sign Up</h1>
              <p>Unlock Exclusive Features with Dafri Premier</p>
            </div>
            <div class="login-page-tabs-parent">
                <form action="" method="POST">
                @csrf
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" name="personal" aria-controls="home" aria-selected="true"><figure><img src="{{PUBLIC_PATH}}/assets/fonts/images/user-tabs.svg" alt="image"></figure> For Personal</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" name="bussiness" role="tab" aria-controls="profile" aria-selected="false"><figure><img src="{{PUBLIC_PATH}}/assets/fonts/images/business-tabs.svg" alt="image"></figure> For Business</button>
                </li>
              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                  <div class="login-inner-form-fileds">
                    <div class="form-group">
                      <label>Email</label>
                      <input type="text" name="" class="form-control" placeholder="" value="sophia.harrismail.work@email.com">
                    </div>
                    <div class="form-group">
                      <label>Referral code (Optional)</label>
                      <input type="text" name="" class="form-control" placeholder="" value="sdvsd-891-9jkas">
                    </div>
                    <div class="form-group">
                      <a href="#" class="btn btn-primaryx">Submit</a>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                  <div class="login-inner-form-fileds">
                    <div class="form-group">
                      <label>Email</label>
                      <input type="text" name="" class="form-control" placeholder="" value="dafripremier@email.com">
                    </div>
                    <div class="form-group">
                      <label>Referral code (Optional)</label>
                      <input type="text" name="" class="form-control" placeholder="" value="sdvsd-891-9jkas">
                    </div>
                    <div class="form-group">
                      <a href="#" class="btn btn-primaryx">Sign Up</a>
                    </div>
                  </div>
                </div>
               </form>
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

@endsection