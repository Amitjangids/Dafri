@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                <div class="heading-section  mb-3">
                    <h5>Become a Bank Agent</h5>
                </div>

                {{ Form::open(array('method' => 'post', 'id' => 'becomAgntForm', 'class' => 'row', 'enctype' => "multipart/form-data")) }}

                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Name</label>
                    @if($recordInfo->user_type == 'Personal')
                    {{Form::text('fname', $recordInfo->first_name, ['class'=>'required', 'placeholder'=>'Enter First Name', 'id'=> 'fname', 'autocomplete'=>'OFF'])}}
                    @else
                    {{Form::text('fname', null, ['class'=>'required', 'placeholder'=>'Enter First Name', 'id'=> 'fname', 'autocomplete'=>'OFF'])}}
                    @endif
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Surname</label>
                    @if($recordInfo->user_type == 'Personal')
                    {{Form::text('lname', $recordInfo->last_name, ['class'=>'required', 'placeholder'=>'Enter Last Name', 'id'=> 'lname', 'autocomplete'=>'OFF'])}}
                    @else
                    {{Form::text('lname', null, ['class'=>'required', 'placeholder'=>'Enter Last Name', 'id'=> 'lname', 'autocomplete'=>'OFF'])}}
                    @endif
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay ">
                    <label>Country</label>
                    {{Form::select('country', $countrList,null, ['class' => 'dropdown-arrow required', 'id' => 'country'])}}
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Commission</label>
                    {{Form::text('commission', null, ['class'=>'required', 'placeholder'=>'Commission Rates %', 'id'=> 'commission', 'autocomplete'=>'OFF','onkeypress'=>'return validateFloatKeyPress(this,event);'])}}
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Minimum Deposit/Withdrawal</label>
                    {{Form::text('min_deposit', null, ['class'=>'required', 'placeholder'=>'Minimum Deposit/Withdrawal', 'id'=> 'min_deposit', 'autocomplete'=>'OFF', 'onkeypress'=>'return validateFloatKeyPress(this,event);'])}}
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Physical Address</label>
                    {{Form::text('address', $recordInfo->addrs_line1.", ".$recordInfo->addrs_line2, ['class'=>'required', 'placeholder'=>'Physical Address', 'id'=> 'address', 'autocomplete'=>'OFF'])}}
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay bp">
                    <label>Telephone</label>
                    {{Form::text('phone', null, ['class'=>'required', 'placeholder'=>'Telephone', 'id'=> 'phone', 'autocomplete'=>'OFF'])}}
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Payment Methods Supported </label>
                    {{Form::text('payment_method', null, ['class'=>'required','placeholder'=>'Payment Methods Supported', 'id'=> 'payment_method', 'autocomplete'=>'OFF'])}}
                </div>
                <div class="drop-text-field col-sm-6 bcem-pay">
                    <label>Email</label>
                    <input placeholder="Email" class="required" id="email" value="<?php echo $recordInfo->email; ?>" name="email" readonly type="text">
                </div>
                <div class="file-container col-sm-6 upload-profile">
                    <div class="file-uploader pic-upload">
                        <span>Upload Picture</span>
                        <label for="file-input">Choose file</label>
                        <input id="file-input" name="profileImg" type="file" onchange="readURL(this);" accept="image/gif, image/jpeg, image/png">
                    </div>
                    <img id="target" src="#" alt="your image" style="display:none;" />


                </div>
                <div class="drop-text-field col-sm-12 bcem-pay">
                    <label>Description</label>
                    {{Form::textarea('desc', null, ['class'=>'required', 'placeholder'=>'Description', 'id'=> 'desc', 'autocomplete'=>'OFF'])}}
                </div>
                <div class="btn-box col-sm-12 ntn-new1">
                    <button class="sub-btn" type="submit">Submit</button>
                </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#target')
                        .attr('src', e.target.result)
                        .width(80)
                        .height(80);
                $('#target').show();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
{{ HTML::script('public/assets/js/intlTelInput.js')}}
<script>
    $("#becomAgntForm").validate();

    var phone_number = window.intlTelInput(document.querySelector("#phone"), {
        separateDialCode: true,
        preferredCountries: false,
//onlyCountries: ['iq'],
        hiddenInput: "phone",
        utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#becomAgntForm").validate(function () {
        var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
        $("input[name='phone'").val(full_number);
        alert(full_number)
    });

    function validateFloatKeyPress(el, evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1)) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '')
                return o.value.length
            return o.value.lastIndexOf(r.text)
        } else
            return o.selectionStart
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#becomAgntForm").validate();
    });
</script>
@endsection