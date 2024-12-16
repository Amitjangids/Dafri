@extends('layouts.inner')
@section('content')
{{ HTML::script('public/assets/js/jquery.validate.js')}}
<script type="text/javascript">
    $(document).ready(function () {
        $("#chngPin").validate();
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
        }, "Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol.");
    });
</script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
            <div class="row">
                <div class="heading-section col-sm-12 mb-90 mt-60">
                    <h5>Change Password</h5>
                </div>
                <div class="col-sm-4 ad-rec">
                    {{ Form::open(array('method' => 'post', 'name' =>'chngPin', 'id' => 'chngPin', 'class' => 'row border-form change-pin')) }}
                    <div class="form-group col-sm-12">
                        <label>Current password</label>
                        {{Form::password('current_password', ['placeholder'=>'Current password', 'class'=>'required'])}}
                    </div>
                    <div class="form-group col-sm-12">
                        <label>
                            New Password
                        </label>
                        {{Form::password('new_password', ['placeholder'=>'New Password', 'id' => 'newpassword', 'minlength' => 8,'class'=>'required passworreq'])}}
                    </div>
                    <div class="form-group col-sm-12">
                        <label>
                            Confirm Password
                        </label>
                        {{Form::password('confirm_password', ['placeholder'=>'Confirm Password', 'equalTo' => '#newpassword','class'=>'required'])}}
                    </div>
                    <div class="col-sm-12">
                        <button class="sub-btn " type="submit">
                            Submit
                        </button>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
@endsection