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
</script>
<style>
    .class_vido{
        width: 320px;
        margin: 0 auto;
        overflow: hidden;
        /*height: 240px;*/
    }
</style>
{{ HTML::script('public/assets/js/front/webcam.js')}}
<!-- Configure a few settings and attach camera -->
<script language="JavaScript">
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.on('error', function () {
        $('#error_message').html('Webcam not found.');
        $('#error-alert-Modal').modal('show');
        $('#basicModal').modal('hide');
    });
    
    Webcam.on('load', function () {
        $('#basicModal').modal('hide');
        $('#cameraModal').modal('show');
    });
</script>
<!-- Code to handle taking the snapshot and displaying it locally -->
<script language="JavaScript">
    function setup() {
        Webcam.reset();

        
        Webcam.attach('#my_camera');


    }

    function take_snapshot() {
        // take snapshot and get image data
        Webcam.snap(function (data_uri) {
            // display results in page

            var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');

            $('#selfiedata').val(raw_image_data);
            $('#previewImg').attr('src', data_uri);
            $('#img_set').show();
            $('#cameraModal').modal('hide');
            Webcam.reset();
        });
    }
    
    function submitForm(){
        $('#registerform').trigger("reset");
        $('#registerform').submit();
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
                    <h1>Leap in banking, the world <span>loves<span>.</span></span></h1>
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
                            <div class="doc-block ">
                                <div class="selectdiv">
                                    <?php global $identityType; ?>
                                    {{Form::select('identity_card_type', $identityType,null, ['class' => '','placeholder' => 'Select document type'])}}
                                </div>
                            </div>
                            <br>
                            <div class="form-group doc-block">
                                {{Form::text('national_identity_number', null, ['class'=>'', 'placeholder'=>'Enter ID Number', 'autocomplete'=>'OFF'])}}
                            </div>
                            <div class="file-container doc-block">
                                <div class="file-uploader">
                                    <label for="file-input">{{HTML::image('public/img/front/upload.svg', SITE_TITLE)}} Upload identity document</label>
                                    {{Form::file('identity_image', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input','onchange'=>'showFilename();'])}}
                                    <p id="target_identity_image" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>
                                Proof of address
                            </label>
                            <div class="doc-block ">
                                <div class="selectdiv">
                                    <?php global $addressType; ?>
                                    {{Form::select('address_proof_type', $addressType,null, ['class' => '','placeholder' => 'Select document type'])}}
                                </div>
                            </div>
                            <br>
                             <div class="form-group doc-block">
                            {{Form::text('address_proof_number', null, ['class'=>'', 'placeholder'=>'Enter ID Number', 'autocomplete'=>'OFF'])}}
                        </div>
                         <div class="doc-block">
                            <div class="file-uploader">
                                <label for="file-input2">{{HTML::image('public/img/front/upload.svg', SITE_TITLE)}} Upload proof of address document</label>
                                {{Form::file('address_document', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input2','onchange'=>'showFilename2();'])}}
                                <p id="target_address_document" class="text1"></p>
                            </div>
                        </div>
                        </div>
                       
                       
                        <div class="col-sm-12 text-center take-sel">
                            <label style="font-style: italic;">
                                We want to ensure that the document belongs to you. Please take a selfie with your identity and a hand note written DafriBank.
                            </label>
                            <a href="#" data-toggle="modal" data-target="#basicModal">{{HTML::image('public/img/camera.svg', SITE_TITLE)}}<span>Take a Selfie</span></a>
                        </div>
                        <div class="col-sm-12 text-center take-sel">
                        <div class="file-uploader">
                         <label for="file-inputDDD">
                                   {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                   Upload Written Notes</label>
                                {{Form::file('written_notes', ['name'=>'written_notes','class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-inputDDD','onchange'=>'showFilenameDDD();'])}}
                            <p id="target_written_document" class="text1"></p>
                        </div>
                    </div>
                        <div class="col-sm-12" id="img_set" style="display: none;">
                            <div class="upload-img-box">
                                <a href="javascript:void(0);" id="img-delete">X</a>
                                <img id="previewImg" src="<?php echo HTTP_PATH; ?>/public/img/front/personal-account.jpg">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button class="sub-btn" type="submit" id='step_3'>
                                Continue
                            </button>
                        </div>
                        <div class="col-sm-12 skip-now" style="text-align: center;">
                            <a href="javascript:void(0);" onclick="submitForm();" style=" color: #000;">Skip for Now</a>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="upload-selfie-main-box">
                                <div class="choose-picture">
                                    <div class="file-container">
                                        <div class="file-uploader">
                                            <label for="file-input3">{{HTML::image('public/img/upload-selfie.svg', SITE_TITLE)}} Upload Picture</label>
                                            {{Form::file('profile_image', ['class'=>'', 'accept'=>IMAGE_EXT,'id'=>'file-input3'])}}
                                            <input id="selfiedata" type="hidden" name="selfiedata" value="" />
                                        </div>
                                    </div>
                                    <!--<a href="#">{{HTML::image('public/img/upload-selfie.svg', SITE_TITLE)}}<span>Upload Picture</span></a>-->
                                </div>
                                <div class="choose-picture">
                                    <?php
                                    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
//                                    $isMob = is_numeric(strpos($ua, "mobile"));
                                    if (is_numeric(strpos($ua, "mobile"))) {
                                        ?>
                                    <div class="file-container">
                                        <div class="file-uploader">
                                            <label for="file-input3">{{HTML::image('public/img/selfie.svg', SITE_TITLE)}} Take a Selfie</label>
                                            {{Form::file('profile_image_cam', ['class'=>'', 'id'=>'file-input4' ,'capture'=>'camera'])}}
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                    <a href="javascript:void(0);" onClick="setup();">{{HTML::image('public/img/selfie.svg', SITE_TITLE)}}<span>Take a Selfie</span></a>
                                    <?php }
                                    ?>
                                    <!--<input type="file" accept="image/*" capture="camera" id='file-input4' />-->
                                    <!--<a href="javascript:void(0);" onClick="setup();">{{HTML::image('public/img/selfie.svg', SITE_TITLE)}}<span>Take a Selfie</span></a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content class_vido">
                            <div id="my_camera"></div>
                            <input type="button" value="Take Snapshot" onClick="take_snapshot()">
                        </div>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</section>
<script>
    
function showFilenameDDD() {
    var name = document.getElementById('file-inputDDD');
    document.getElementById('target_written_document').innerHTML = name.files.item(0).name;
}
function showFilename() {
    var name = document.getElementById('file-input');
    document.getElementById('target_identity_image').innerHTML = name.files.item(0).name;
}

function showFilename2() {
    var name = document.getElementById('file-input2');
    document.getElementById('target_address_document').innerHTML = name.files.item(0).name;
}

function readURL(input) {
    //        var name = document.getElementById('file-input3');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImg').attr('src', e.target.result);
            $('#img_set').show();
            $('#basicModal').modal('hide');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#file-input3").change(function() {
    readURL(this);
});
$("#file-input4").change(function() {
    readURL(this);
});

$("#img-delete").click(function() {
    $('#file-input3').val('');
    $('#file-input4').val('');
    $('#selfiedata').val('');
    $('#previewImg').attr('src', '');
    $('#img_set').hide();
});
</script>
@endsection