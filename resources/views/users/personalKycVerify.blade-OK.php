@extends('layouts.login')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
        $.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Only letters, numbers and underscore allowed.");
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            //var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input);
        }, "Password must be a combination of Numbers, Uppercase & Lowercase Letters.");
        $("#registerform").validate();

        $(".opt_input").keyup(function () {
            if (this.value.length == this.maxLength) {
                $(this).next('label').remove();
                $(this).next('.opt_input').focus();
            }
        });

    });

    function hideerrorsucc() {
        $('.close.close-sm').click();
    }

</script>
<!-- logo -->
<div class="pre-regsiter-logo">
    <div class="wrapper">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo-white">
                    <a href="{!! HTTP_PATH !!}"> {{HTML::image(BLACK_LOGO_PATH, SITE_TITLE)}}</a>
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
                    <h1>Leap in the 
                        banking the
                        world <span>loves<span>.</span></span></h1>
                    <p>Explore an easy and better way to save, make payments, manage your money and your business whenever you want, wherever you are!</p>
                </div>
            </div>
            <div class="col-sm-6">
                {{ Form::open(array('method' => 'post', 'id' => 'registerform', 'class' => ' border-form', 'enctype' => "multipart/form-data")) }} 

                <div class="form-page sign-page">
                    <h6 class="steps">
                        Step 3/3
                    </h6>
                    <div class="form-heading">
                        <h4><span>KYC verification </span>
                        </h4>
                        <p>Please upload your identity card and proof of address for account security purposes.</p>
                    </div>
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="row">
                        <div class="form-group col-sm-6 ">
                            <label>
                                Identity card
                            </label>
                            <div class="selectdiv">
                                <?php global $identityType; ?>
                                {{Form::select('identity_card_type', $identityType,null, ['class' => 'required','placeholder' => 'Select document type'])}}

                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>
                                Proof of address
                            </label>
                            <div class="selectdiv">
                                <?php global $addressType; ?>
                                {{Form::select('address_proof_type', $addressType,null, ['class' => 'required','placeholder' => 'Select document type'])}}

                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            {{Form::text('national_identity_number', null, ['class'=>'', 'placeholder'=>'Enter document number', 'autocomplete'=>'OFF'])}}
                        </div>
                        <div class="form-group col-sm-6">
                            {{Form::text('address_proof_number', null, ['class'=>'', 'placeholder'=>'Enter document number', 'autocomplete'=>'OFF'])}}
                        </div>
                        <div class="file-container col-sm-6">
                            <div class="file-uploader">
                                <label for="file-input">
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    Upload identity
                                    document</label>
                                {{Form::file('identity_image', ['class'=>'required', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input'])}}
                                <p class="text1">Please drag & drop file (PNG,JPG,PDF)</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="file-uploader">
                                <label for="file-input1">
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    Upload proof of
                                    address document</label>
                                {{Form::file('address_document', ['class'=>'required', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input1'])}}
                                <p class="text1">Please drag & drop file (PNG,JPG,PDF)</p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button class="sub-btn" type="submit" id='step_3'>
                                Continue
                            </button>
                        </div>

                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</section>

@endsection