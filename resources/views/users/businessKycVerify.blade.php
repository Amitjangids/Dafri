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

    function submitForm() {
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
                        <!--   <div class="form-group col-sm-6">
                            <label>
                                Business registration document
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input">
                                    Upload document 
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('registration_document', ['class'=>'required', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input', 'onchange'=>'showFilename()'])}}
                                <p id="target_busness_reg_doc" class="text1"></p>
                            </div>
                        </div> -->
                        <div class="form-group col-sm-6">
                            <label>
                                Certificate Of Incorporation
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input1">
                                    Upload document
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('certificate_of_incorporation', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input1', 'onchange'=>'showFilename1()'])}}
                                <p id="target_file1" class="text1"></p>
                            </div>
                        </div>
                        <!--    <div class="form-group col-sm-6">
                            <label>
                                Article
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input2">
                                    Upload document 
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('article', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input2', 'onchange'=>'showFilename2()'])}}
                                <p id="target_file2" class="text1"></p>
                            </div>
                        </div> -->
                        <div class="form-group col-sm-6">
                            <label>
                                Memorandum
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input3">
                                    Upload document
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('memorandum', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input3', 'onchange'=>'showFilename3()'])}}
                                <p id="target_file3" class="text1"></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Tax Certificate
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input4">
                                    Upload document
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('tax_certificate', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input4', 'onchange'=>'showFilename4()'])}}
                                <p id="target_file4" class="text1"></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Proof of Business Address
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input5">
                                    Upload document
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('address_proof', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input5', 'onchange'=>'showFilename5()'])}}
                                <p id="target_file5" class="text1"></p>
                            </div>
                        </div>
                        <!--       <div class="form-group col-sm-6">
                            <label>
                                Identity of all Directors
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input6">
                                    Upload document 
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('identity', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input6', 'onchange'=>'showFilename6()'])}}
                                <p id="target_file6" class="text1"></p>
                            </div>
                        </div> -->
                        <div class="form-group col-sm-12">
                            <label>
                                Identity of person or entity holding more than 25% stake in the company
                            </label>
                            <div class="file-uploader f-upload">
                                <label for="file-input7">
                                    Upload document
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                </label>
                                {{Form::file('person_identity', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input7', 'onchange'=>'showFilename7()'])}}
                                <p id="target_file7" class="text1"></p>
                            </div>
                        </div>

                        <div class="form-group col-sm-6 ">
                            <label>
                                Director's Identity
                            </label>
                            <div class="doc-block">
                                <div class="selectdiv">
                                    <?php global $identityType; ?>
                                    {{Form::select('identity_card_type', $identityType,null, ['class' => '','placeholder' => 'Select document type','onchange'=>'updateSet(this.value);'])}}
                                </div>
                            </div>
                            <br>
                            <div class="doc-block">
                                {{Form::text('national_identity_number', null, ['class'=>'', 'placeholder'=>'Enter ID Number', 'autocomplete'=>'OFF'])}}
                            </div>
                            <div class="file-container doc-block" id="main_image_dv">
                                <div class="file-uploader">
                                    <label for="file-inputI">
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                        Upload identity
                                        document</label>
                                    {{Form::file('identity_image', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-inputI','onchange'=>'showFilenameI();'])}}
                                    <p id="target_identity_image" class="text1"></p>
                                </div>
                            </div>
                            
                            <div class="file-container doc-block" id="">
                                <div class="file-uploader">
                                    <div id="front_image_dv" style="display:none;">
                                        <label for="file-input-front">{{HTML::image('public/img/front/upload.svg', SITE_TITLE)}} <span>Upload <b class="spl_txt">front</b> of the identity document</span></label>
                                        {{Form::file('identity_image_front', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input-front','onchange'=>'showFilenameFront();'])}}
                                    </div>
                                    <div id="back_image_dv" style="display:none;">
                                        <label for="file-input-back">{{HTML::image('public/img/front/upload.svg', SITE_TITLE)}} <span>Upload <b class="spl_txt">back</b> of the identity document</span></label>
                                        {{Form::file('identity_image_back', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input-back','onchange'=>'showFilenameBack();'])}}
                                    </div>
                                    
                                    <p id="target_identity_image_front" class="text1"></p>
                                    <p id="target_identity_image_back" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label>
                                Director's Proof of Address
                            </label>
                            <div class="doc-block">
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
                                    <label for="file-inputD">
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                        Upload address document</label>
                                    {{Form::file('address_document', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-inputD','onchange'=>'showFilenameD();'])}}
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
                        <div class="col-sm-12 text-center take-sel written_notes">
                            <div class="file-uploader">
                                <label for="file-inputDDD">
                                    {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    Upload Written Notes</label>
                                {{Form::file('written_notes', ['name'=>'written_notes','class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-inputDDD','onchange'=>'showFilenameDDD();'])}}
                                <p id="target_written_document" class="text1"></p>
                            </div>
                        </div>
                        <div class="col-sm-12" id="img_set" style="display: none; ">
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
                                            <label for="file-input9">{{HTML::image('public/img/upload-selfie.svg', SITE_TITLE)}} Upload Picture</label>
                                            {{Form::file('profile_image', ['class'=>'', 'accept'=>IMAGE_EXT,'id'=>'file-input9'])}}
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
                                                <label for="file-input8">{{HTML::image('public/img/selfie.svg', SITE_TITLE)}} Take a Selfie</label>
                                                {{Form::file('profile_image_cam', ['class'=>'', 'id'=>'file-input8' ,'capture'=>'camera'])}}
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
                <div class="modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
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
    function showFilenameI() {
        var name = document.getElementById('file-inputI');
        document.getElementById('target_identity_image').innerHTML = name.files.item(0).name;
    }
    
    function showFilenameFront() { 
    var name = document.getElementById('file-input-front');
    document.getElementById('target_identity_image_front').innerHTML = name.files.item(0).name;
    if(name.files.item(0).name!="")
    {
       var identity_card=$('#identity_card_front').val();
       $('#front_image_dv').hide();
           $('#back_image_dv').show();
       if(identity_card=="" && name.files.item(0).name!="")
       {
           $('#identity_card_front').attr('required',true);
           
           
       }
    }
}

function showFilenameBack() {
    var name = document.getElementById('file-input-back');
    document.getElementById('target_identity_image_back').innerHTML = name.files.item(0).name;
    if(name.files.item(0).name!="")
    {
       var identity_card=$('#identity_card_back').val();
       
       $('#front_image_dv').show();
           $('#back_image_dv').hide();
       if(identity_card=="" && name.files.item(0).name!="")
       {
           $('#identity_card_back').attr('required',true);
       }
    }
}

    function showFilenameD() {
        var name = document.getElementById('file-inputD');
        document.getElementById('target_address_document').innerHTML = name.files.item(0).name;
    }

    function showFilenameDDD() {
        var name = document.getElementById('file-inputDDD');
        document.getElementById('target_written_document').innerHTML = name.files.item(0).name;
    }
    
    function updateSet(type){
    
    if(type == 'Passport'){
        $('#main_image_dv').show();
        $('#front_image_dv').hide();
        $('#file-input-front').val('');
        $('#file-input-back').val('');
    } else{
        $('#front_image_dv').show();
        $('#main_image_dv').hide();
        $('#file-inputI').val('');
    }
}

    function readURL(input) {
        //        var name = document.getElementById('file-input3');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#previewImg').attr('src', e.target.result);
                $('#img_set').show();
                $('#basicModal').modal('hide');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#file-input8").change(function () {
        readURL(this);
    });
    $("#file-input9").change(function () {
        readURL(this);
    });

    $("#img-delete").click(function () {
        $('#file-input8').val('');
        $('#file-input9').val('');
        $('#selfiedata').val('');
        $('#previewImg').attr('src', '');
        $('#img_set').hide();
    });
</script>
<script>
    function showFilename() {
        var name = document.getElementById('file-input');
        document.getElementById('target_busness_reg_doc').innerHTML = name.files.item(0).name;
    }

    function showFilename1() {
        var name = document.getElementById('file-input1');
        document.getElementById('target_file1').innerHTML = name.files.item(0).name;
    }

    function showFilename2() {
        var name = document.getElementById('file-input2');
        document.getElementById('target_file2').innerHTML = name.files.item(0).name;
    }

    function showFilename3() {
        var name = document.getElementById('file-input3');
        document.getElementById('target_file3').innerHTML = name.files.item(0).name;
    }

    function showFilename4() {
        var name = document.getElementById('file-input4');
        document.getElementById('target_file4').innerHTML = name.files.item(0).name;
    }

    function showFilename5() {
        var name = document.getElementById('file-input5');
        document.getElementById('target_file5').innerHTML = name.files.item(0).name;
    }

    function showFilename6() {
        var name = document.getElementById('file-input6');
        document.getElementById('target_file6').innerHTML = name.files.item(0).name;
    }

    function showFilename7() {
        var name = document.getElementById('file-input7');
        document.getElementById('target_file7').innerHTML = name.files.item(0).name;
    }


    function checkvalidate(x)
    {
        var identity_card_type = x.identity_card_type.value;
        var national_identity_number = x.national_identity_number.value;
        var identity_image = x.identity_image.value;

        // for address_proof_type
        var address_proof_type = x.address_proof_type.value;
        var address_proof_number = x.address_proof_number.value;
        var address_document = x.address_document.value;

        if (identity_card_type != "" && national_identity_number == "")
        {
            alert("Please Enter Id Number");
            return false;
        }

        if (identity_card_type != "" && identity_image == "")
        {
            alert("Please upload identity document");
            return false;
        }

        if (identity_card_type == "" && identity_image != "")
        {
            alert("Please Select Identity");
            return false;
        }

        // for address_proof_type

        if (address_proof_type != "" && address_proof_number == "")
        {
            alert("Please Enter Id Number Of Address");
            return false;
        }

        if (address_proof_type != "" && address_document == "")
        {
            alert("Please upload address document");
            return false;
        }

        if (address_proof_type == "" && address_document != "")
        {
            alert("Please Select Proof of Address");
            return false;
        }



        return true;
    }

</script>
@endsection