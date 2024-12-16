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
		         $('#emails').keyup(function() {
		           
		            $(this).val($(this).val().replace(/ +?/g, ''));
		          });
		        $('#password').keyup(function() {
		           
		            $(this).val($(this).val().replace(/ +?/g, ''));
		          });
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

                         @include('elements.left_login_page')
                         {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => 'log-form')) }}     
						<div class="log-txt-mob">
							<a class="navbar-brand" href="{{HTTP_PATH}}"><img src="{{HTTP_PATH}}/public/assets/assets/images/white-dafribank-logo.svg" alt="dafribank" /></a>
							<h1>Leap into the banking<br/> the world <span>loves</span>.</h1>
						</div>
						<h2>Get your global bank account!</h2>
							<div class="row  g-3">
								<div class="col-md-12">
									<label for="email" class="form-label">Enter your email address<span class="text-req">*</span></label>
									<div class="input-group">
                                    {{Form::text('email', Cookie::get('user_email_address'), ['class'=>'form-control required email enterkey', 'placeholder'=>'Your e-mail', 'autocomplete'=>'off', 'onkeypress'=>"return event.charCode != 32", 'id'=>'emails'])}}
										<span class="input-group-text" id="basic-email"><i class="fa-solid fa-envelope"></i></span>
									  </div>
								</div>
								
								<div class="col-md-12">
									<label for="password" class="form-label">Password</label>
									<div class="input-group">
                                       {{Form::input('password', 'password', Cookie::get('user_password'), array('class' => "form-control required enterkeyl", 'placeholder' => 'Your password', 'id'=>'password','minlength'=>8, 'onkeypress'=>"return event.charCode != 32",'autocomplete'=>'off'))}}
										<span class="input-group-text" id="basic-password"><i class="fa-solid fa-lock"></i></span>
									  </div>
								</div>

								<div class="col-md-12">
                                <label class="form-check-label agree-text"><a href="{{URL::to('forgot-password')}}">Forgot password?</a></label>
								</div>
								<div class="col-md-12 mt-4">
									<button type="submit" class="btn btn-dark btn-lg">Log in</button>
								</div>
								<div class="col-md-12">
									<hr/>
								</div>
								<!-- <div class="col-md-12 mt-4">
									<p class="text-center">Need an account? <a href="signup.html">Sign up here</a></p>
								</div> -->
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
		</div>

	</section>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>


