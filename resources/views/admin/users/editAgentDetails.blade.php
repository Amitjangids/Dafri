@extends('layouts.admin')
@section('content')
<script type="text/javascript">
$(document).ready(function() {
     $('#email').keyup(function() {
           
            $(this).val($(this).val().replace(/ +?/g, ''));
          });
       
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

    $("#adminForm").validate();
});
</script>
{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Agent</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/users/bank-agent-request')}}"><i class="fa fa-user"></i> <span>Bank Agent Request</span></a></li>
            <li class="active"> Edit Agent</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($agent, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">First Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('first_name', $agent->first_name, ['class'=>'form-control required', 'placeholder'=>'First Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Last Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('last_name', $agent->last_name, ['class'=>'form-control required', 'placeholder'=>'Last Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Country <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::select('country', $countrList,null, ['class' => 'form-control required','placeholder' => 'Select Country'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Commission <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('commission', $agent->commission, ['id'=>'commission','class'=>'form-control required', 'placeholder'=>'Commission', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Minimum Deposit/Withdrawal <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('min_amount', $agent->min_amount, ['id'=>'min_deposit','class'=>'form-control required digits', 'placeholder'=>'Minimum Deposit/Withdrawal', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Physical Address <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('address', $agent->address, ['id'=>'address','class'=>'form-control required', 'placeholder'=>'Physical Address', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
              
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('phone', $agent->phone, ['id'=>'phone','class'=>'form-control required digits', 'placeholder'=>'Mobile Number', 'autocomplete' => 'off', 'minlength' => 8, 'maxlength' => 16])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Payment Methods Supported <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('payment_methods', $agent->payment_methods, ['id'=>'payment_method','class'=>'form-control required', 'placeholder'=>'Payment Methods Supported', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('email', $agent->email, ['class'=>'form-control email required', 'placeholder'=>'Email', 'autocomplete' => 'off','id'=>'email','readonly'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('description', $agent->description, ['id'=>'desc','class'=>'form-control required', 'placeholder'=>'Description', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('profile_image', ['class'=>'form-control', 'accept'=>IMAGE_EXT,'onchange'=>"readURL(this);",'id'=>"file-input",])}}
                            <span class="help-text"> Supported File Types: jpg, jpeg, png (Max. {{ MAX_IMAGE_UPLOAD_SIZE_DISPLAY }}).</span>
                        </div>
                      
                    
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                       @if($recordInfo->profile_image != '')
                           {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$agent->profile_image, SITE_TITLE,['style'=>"max-width: 200px",'id'=>'target','src'=>"#"])}}
                        @else
                                <img id="target" 'style'="max-width: 200px" src="#" alt="your image" style="display:none;" />
                        @endif
                        </div>
                    
                    </div>
                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        {{Form::reset('Reset', ['class' => 'btn btn-default canlcel_le'])}}
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
    {{ HTML::script('public/assets/js/intlTelInput.js')}}
    <script>
    //var ph = document.querySelector("#phone");
    //ph = ph.trim();
    var phone_number = window.intlTelInput(document.querySelector("#phone"), {
        //var phone_number = window.intlTelInput(ph, {
        separateDialCode: true,
        preferredCountries: false,
        //onlyCountries: ['iq'],
        hiddenInput: "phone",
        utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#adminForm").validate(function() {
        var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
        full_number = full_number.trim();
        //alert(full_number);
        $("input[name='phone'").val(full_number);
        //alert(full_number)
    });

    $(document).ready(function() {
        var phn = document.getElementById('phone').value;
        phn = phn.trim();
        setTimeout(function() { document.getElementById('phone').value = phn; }, 3000);
    });
    
    </script>
    <script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#target')
                        .attr('src', e.target.result)
                        .width(200)
                        .height(200);
                $('#target').show();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
    @endsection