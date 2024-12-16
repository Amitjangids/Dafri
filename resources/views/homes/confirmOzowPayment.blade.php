<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{$title.TITLE_FOR_LAYOUT}}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <link rel="icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <meta name="robots" content="noindex, nofollow">

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
		<link rel="stylesheet" href="{{HTTP_PATH}}/public/assets/assets/css/style1.css?v=31">

        <script src="{{HTTP_PATH}}/public/assets/js/front/jquery.min.js"></script>
        <script src="{{HTTP_PATH}}/public/assets/js/jquery.validate.js"></script> 
        <script type="text/javascript">
        $(document).ready(function () {
        $("#loginform").validate();
        });

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
        </script>


                         @include('elements.left_login_page')
                         {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => 'log-form')) }}     
						<div class="log-txt-mob">
							<a class="navbar-brand" href="{{HTTP_PATH}}"><img src="{{HTTP_PATH}}/public/assets/assets/images/white-dafribank-logo.svg" alt="dafribank" /></a>
							<h1>Leap into the banking<br/> the world <span>loves</span>.</h1>
						</div>
						<h5>Verification</h5>
						<span class="subheading">Please enter the 6 digit code sent to your registered email address.</span>
							<div class="row  g-3">
								<div class="col-md-12">
									<div class="input-group">
										<input type="text" name="otp_code" class="form-control required" id="code"  placeholder="6 digit code"  aria-label="code" aria-describedby="basic-code" autocomplete="off">
										<span class="input-group-text" id="basic-code"><i class="fa-solid fa-key"></i></span>
									  </div>
								</div>
								
								<div class="col-md-12 mt-4">
									<button type="submit" class="btn btn-dark btn-lg">Submit</button>
								</div>
								<div class="col-md-12">
									<hr/>
								</div>
								<div class="col-md-12 mt-4">
									<p class="text-center">Didn't receive a code? <a href="javascript:void(0);" onclick="resetOTP()">Resend</a></p>
								</div>
							  </div>
							
                              {{ Form::close()}}

                    </div>
			</div>

			</div>
		</div>
	</section>

    <section class="custom-modal-wrapper">
		<div class="container">
		<?php if(Session::has('success_session_message')){ ?>
            <script>
                $(document).ready(function() {
                    $('#success_message').html("<?php echo Session::get('success_session_message');?>");
                    $('#success-alert-Modal').modal('show');
                });
            </script>
        <?php 
        Session::forget('success_session_message');
        } else if(Session::has('error_session_message')){  ?>
            <script>
                $(document).ready(function() {
                    $('#error_message').html("<?php echo Session::get('error_session_message');?>");
                    $('#error-alert-Modal').modal('show');
                });
            </script>
        <?php 
        Session::forget('error_session_message');
        }  ?>


			<!-- Modal -->
			<div class="modal fade" id="error-alert-Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered">
			    <div class="modal-content">
			      <div class="modal-body">
			      	<h2 id="error_message">Invalid email or password. you have three more attempts.</h2>

			      </div>
			      <div class="modal-footer">
			       <a href="#" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</a>
			      </div>
			    </div>
			  </div>
			</div>

            <!------for success--->

            	<!-- Modal -->
			<div class="modal fade" id="success-alert-Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered">
			    <div class="modal-content">
			      <div class="modal-body">
			      	<h2 id="success_message"></h2>

			      </div>
			      <div class="modal-footer">
			       <a href="#" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</a>
			      </div>
			    </div>
			  </div>
			</div>



		</div>

	</section>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

