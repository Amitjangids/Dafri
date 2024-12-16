@extends('layouts.login')
@section('content')
    <script type="text/javascript">
        $(document).ready(function() {
            $.validator.addMethod("alphanumeric", function(value, element) {
                return this.optional(element) || /^[\w.]+$/i.test(value);
            }, "Only letters, numbers and underscore allowed.");
            $.validator.addMethod("passworreq", function(input) {
                var reg = /[0-9]/; //at least one number
                var reg2 = /[a-z]/; //at least one small character
                var reg3 = /[A-Z]/; //at least one capital character
                //var reg4 = /[\W_]/; //at least one special character
                return reg.test(input) && reg2.test(input) && reg3.test(input);
            }, "Password must be a combination of Numbers, Uppercase & Lowercase Letters.");
            $("#registerform").validate();

            $(".opt_input").keyup(function() {
                if (this.value.length == this.maxLength) {
                    $(this).next('label').remove();
                    $(this).next('.opt_input').focus();
                }
            });

        });

        function hideerrorsucc() {
            $('.close.close-sm').click();
        }

        function resetOTP() {
            var phone = $('#phone').val();
            var user_id = $('#user_id').val();
            $.ajax({
                url: "{!! HTTP_PATH !!}/resentVerifyOtp",
                type: "POST",
                //data: {'phone': phone, _token: '{{ csrf_token() }}'},
                data: {
                    'user_id': user_id,
                    'phone': phone,
                    _token: '{{ csrf_token() }}'
                },
                success: function(result) {
                    $('#success_message').html('OTP sent successfully');
                    $('#success-alert-Modal').modal('show');
                }
            });
        }
    </script>


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
                                <a href="{{ url('/forgot-password') }}"><img
                                        src="{{ PUBLIC_PATH }}/assets/fronts/images/backicon.svg" alt="image"> Back</a>
                            </div>
                            <div class="header-right-parent">
                                <a href="#">Don't have an account?</a>
                                <a href="{{ route('signIn') }}" class="bold-content">Sign In</a>
                            </div>
                        </div>
                        <div class="login-page-inner-content step-form-wrapper">
                            <div class="tab custom-step-form">
                                <h1>Verification code</h1>
                                <p>Please enter the 6 digit code sent to your registered email address.</p>
                                <div class="ee er_msg">
                                    @include('elements.errorSuccessMessage')
                                </div>
                                <div class="login-inner-form-fileds">
                                    {{ Form::open(['method' => 'post', 'id' => 'registerOtp', 'class' => ' border-form']) }}
                                    <div class="form-group">
                                        <label>Enter Code</label>
                                        <div class="row">

                                            <div class="otp-fields-parent">
                                                <input type="text" name="otp_code" class="form-control" maxlength="1"
                                                    placeholder="">
                                                <input type="text" name="otp_code1" class="form-control" maxlength="1"
                                                    placeholder="">
                                                <input type="text" name="otp_code2" class="form-control" maxlength="1"
                                                    placeholder="">
                                                <input type="text" name="otp_code3" class="form-control" maxlength="1"
                                                    placeholder="">
                                                <input type="text" name="otp_code4" class="form-control" maxlength="1"
                                                    placeholder="">
                                                <input type="text" name="otp_code5" class="form-control" maxlength="1"
                                                    placeholder="">
                                            </div>
                                            <div class="form-para col-sm-12 text-left">
                                                <h5>
                                                    <a href="javascript:void(0);" onclick="resetOTP()">Resend</a>

                                                </h5>
                                            </div>

                                            <div class="col-sm-12">
                                                <button class="btn btn-primaryx" type="submit" id='step_2'>
                                                    Send
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>





    <script type="text/javascript">
        $(document).ready(function() {
            // Add a custom validation method to validate OTP fields as a group
            $.validator.addMethod("otpRequired", function(value, element) {
                let allFilled = true;
                $(".otp-fields-parent .form-control").each(function() {
                    if ($(this).val().trim() === "") {
                        allFilled = false; // If any field is empty, validation fails
                        return false; // Exit loop early
                    }
                });
                return allFilled;
            }, "Please fill all OTP fields."); // Single error message for all OTP fields

            // Initialize validation
            $("#registerOtp").validate({
                rules: {
                    otp_code: {
                        otpRequired: true
                    }, // Apply the custom rule to one field
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "otp_code") {
                        error.insertAfter(
                            ".otp-fields-parent"); // Position the error message after all OTP fields
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    form.submit(); // Submit the form if validation passes
                }
            });
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const otpFields = document.querySelectorAll(".otp-fields-parent .form-control");

            otpFields.forEach((field, index) => {
                field.addEventListener("input", function() {
                    if (field.value.length === 1 && index < otpFields.length - 1) {
                        otpFields[index + 1].focus(); // Move to the next field
                    }
                });

                field.addEventListener("keydown", function(event) {
                    if (event.key === "Backspace" && field.value.length === 0 && index > 0) {
                        otpFields[index - 1].focus(); // Move to the previous field
                    }
                });

                field.addEventListener("paste", function(event) {
                    const pasteData = event.clipboardData.getData("text");
                    if (pasteData.length === otpFields.length && /^\d+$/.test(pasteData)) {
                        // If the pasted data is exactly the length of OTP fields and contains only digits
                        event.preventDefault(); // Prevent default paste action
                        pasteData.split("").forEach((char, idx) => {
                            otpFields[idx].value = char;
                        });
                        otpFields[otpFields.length - 1].focus(); // Focus on the last field
                    }
                });
            });

            // Optional: Clear all fields and reset focus to the first field
            window.resetOTP = function() {
                otpFields.forEach(field => field.value = "");
                otpFields[0].focus();
            };
        });
    </script>
@endsection
