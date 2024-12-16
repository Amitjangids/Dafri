@extends('layouts.admin')
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

        $("#adminForm").validate();
    });
</script>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Role</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/admins/roles')}}"><i class="fa fa-user"></i> <span>Manage Roles</span></a></li>
            <li class="active"> Edit Role</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
			{{Form::model($role, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Role Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('role_name', null, ['class'=>'form-control required', 'placeholder'=>'Role Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    
					<div class="form-group">
                        <label class="col-sm-2 control-label">Permissions <span class="require">*</span></label>
                        <div class="col-sm-10">
                         <label class="checkbox-inline"><input <?php if(in_array("change-username",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="change-username">change-username</label>
						 <label class="checkbox-inline"><input <?php if(in_array("change-password",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="change-password">change-password</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-fees",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-fees">list-fees</label>
						 <label class="checkbox-inline"><input <?php if(in_array("edit-fees",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-fees">edit-fees</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-personal-user",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-personal-user">list-personal-user</label>
						 <label class="checkbox-inline"><input <?php if(in_array("add-personal-user",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="add-personal-user">add-personal-user</label>				         
						 <label class="checkbox-inline"><input <?php if(in_array("edit-personal-user",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-personal-user">edit-personal-user</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-business-user",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-business-user">list-business-user</label>
						 <label class="checkbox-inline"><input <?php if(in_array("add-business-user",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="add-business-user">add-business-user</label>
						 <label class="checkbox-inline"><input <?php if(in_array("edit-business-user",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-business-user">edit-business-user</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-pages",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-pages">list-pages</label>
						 <label class="checkbox-inline"><input <?php if(in_array("edit-pages",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-pages">edit-pages</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-faq",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-faq">list-faq</label>
						 <label class="checkbox-inline"><input <?php if(in_array("add-faq",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="add-faq">add-faq</label>
						 <label class="checkbox-inline"><input <?php if(in_array("edit-faq",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-faq">edit-faq</label>
						 <label class="checkbox-inline"><input <?php if(in_array("delete-faq",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="delete-faq">delete-faq</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-blogs",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-blogs">list-blogs</label>
						 <label class="checkbox-inline"><input <?php if(in_array("add-blog",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="add-blog">add-blog</label>
						 <label class="checkbox-inline"><input <?php if(in_array("edit-blog",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-blog">edit-blog</label>
						 <label class="checkbox-inline"><input <?php if(in_array("delete-blog",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="delete-blog">delete-blog</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-agent-request",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-agent-request">list-agent-request</label>
						 <label class="checkbox-inline"><input <?php if(in_array("edit-agent-request",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-agent-request">edit-agent-request</label>
						 <label class="checkbox-inline"><input <?php if(in_array("view-transaction-report",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="view-transaction-report">view-transaction-report</label>
                        <label class="checkbox-inline"><input <?php if(in_array("edit-user-wallet",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="edit-user-wallet">edit-user-wallet</label>
                        
                        <label class="checkbox-inline"><input <?php if(in_array("list-support",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-support">list-support</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-help",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-help">list-help</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-paypal",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-paypal">list-paypal</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-crypto-deposit",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-crypto-deposit">list-crypto-deposit</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-crypto-withdraw",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-crypto-withdraw">list-crypto-withdraw</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-manual-deposit",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-manual-deposit">list-manual-deposit</label>
						 <label class="checkbox-inline"><input <?php if(in_array("list-manual-withdraw",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="list-manual-withdraw">list-manual-withdraw</label>
                                                 <label class="checkbox-inline"><input <?php if(in_array("trans-limit",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="trans-limit">trans-limit</label>
						 <label class="checkbox-inline"><input <?php if(in_array("agent-limit",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="agent-limit">agent-limit</label>
						 <label class="checkbox-inline"><input <?php if(in_array("individual-agent-limit",$permissions)) echo "checked"; ?> type="checkbox" name="permission[]" value="individual-agent-limit">individual-agent-limit</label>

                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="wallet balance"  
                         <?php if(in_array("wallet balance",$permissions)) echo "checked"; ?>>wallet balance</label>
                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="get in touch" 
                         <?php if(in_array("get in touch",$permissions)) echo "checked"; ?>>get in touch</label>

                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="dba-deposit-request" <?php if(in_array("dba-deposit-request",$permissions)) echo "checked"; ?>  >DBA Deposit Request</label>
                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="dba-deposit-request-card" <?php if(in_array("dba-deposit-request-card",$permissions)) echo "checked"; ?> >DBA Deposit by Card</label>
                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="dba-withdraw-request" <?php if(in_array("dba-withdraw-request",$permissions)) echo "checked"; ?> >DBA Withdraw Request</label>
                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="global-withdraw-request" <?php if(in_array("global-withdraw-request",$permissions)) echo "checked"; ?> > Global/3rd Party Pay</label>
                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="gift-card-request" <?php if(in_array("gift-card-request",$permissions)) echo "checked"; ?> > Gift Card / Top Up Request</label>
                         <label class="checkbox-inline"><input type="checkbox" name="permission[]" value="mi-report" <?php if(in_array("mi-report",$permissions)) echo "checked"; ?>>  MI Report</label>
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
    var phone_number = window.intlTelInput(document.querySelector("#phone"), {
    separateDialCode: true,
    preferredCountries:false,
    //onlyCountries: ['iq'],
    hiddenInput: "phone",
    utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
    });

    $("#adminForm").validate(function () {
    var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);
    $("input[name='phone'").val(full_number);
    alert(full_number)
    });
    </script>
    @endsection