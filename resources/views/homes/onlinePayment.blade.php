@extends('layouts.login')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
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
            $('#showEye').html('<img src="https://nimbleappgenie.live/dafri/public/img/front/eye.svg" alt="Dafri Bank">');
        } else {
            x.type = "password";
            $('#showEye').html('<img src="https://nimbleappgenie.live/dafri/public/img/front/eye.svg" alt="Dafri Bank">');
        }
    }
</script>
<!-- logo -->
<div class="pre-regsiter-logo">
    <div class="wrapper">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo-white">
                    <a href="{!! HTTP_PATH !!}">{{HTML::image(BLACK_LOGO_PATH, SITE_TITLE)}}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($order_amount > 0) { ?>
<!-- two-part-main -->
<section class="two-part-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 gray-bg">
              <div class="payment-details">
                <h5>Payment Details</h5>
<table class="table">
    <tbody>
        <tr>
          <th>Name</th>
            <td>{{strtoupper($merchant_name)}}</td>
            
        </tr>
        <tr>
          <th>Order ID</th>
            <td>#{{$order_id}}</td>
           
        </tr>
        <tr>
          <th>Amount</th>
		  
            <td>{{$currency_code.' '.$order_amount}}</td>
          
        </tr>
   
    </tbody>
</table>
              </div>
            </div>
            <div class="col-sm-6">
			    <div class="form-page">
				<!-- @if(empty($user))
			<div class="alert alert-danger">
			<strong>Alert!</strong> Invalid Merchant ID.
		    </div>
			@endif -->
                    
					<div class="form-heading pay-page">
                        <h4>Login to Pay</h4>
                        <p>Log in with your data that you entered during your registration.</p>
                    </div>
                    {{ Form::open(array('method' => 'post', 'id' => 'loginform','onsubmit'=>'return disable_submit();')) }}     
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="form-group form-field">
                        <label>
                            Your e-mail
                        </label>
                        <div class="field-box">                                	
                            {{Form::text('email', Cookie::get('user_email'), ['class'=>'required email enterkey', 'placeholder'=>'Your e-mail', 'autocomplete'=>'OFF'])}}
                        </div>
                    </div>
                    <div class="form-group form-field">
                        <label>
                            Your password
                        </label>
                        <div class="field-box">
                            {{Form::input('password', 'password', Cookie::get('user_password'), array('class' => "required enterkeyl", 'placeholder' => 'Your password', 'id'=>'password','minlength'=>8))}}
                            <i onclick="showPass()" id="showEye">{{HTML::image('public/img/front/eye.svg', SITE_TITLE)}}</i>
                        </div>
                    </div>
                    <div class="form-group check-field-box">


                       <!--  {{Form::checkbox('user_remember', '1', Cookie::get('user_remember'), array('class' => "", 'id' =>"remember_sec"))}} -->
                       
                    </div>
					@if(!empty($user))
                    <button class="sub-btn button_disable" type="submit">
                        Log in
                    </button>
                    @endif 
                    {{ Form::close()}}
					
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    function disable_submit()
    {
    var empty_field=0;    
    $(".required").each(function() {
    if($(this).val()=="")
    {
    empty_field=1;  
    }
    });
    if(empty_field==0 && $(".required").hasClass('error')==false)
    {
    $('.button_disable').prop('disabled', true);   
    return true;
    }
    return false;
    }
</script>    

<?php } else { ?>
<div class="widt-req">

    <div class="modal-content transfer-pop w-cionfirm">
        
        
            <div class="no_record">
                Amount not valid.
            </div>
        <?php } ?>


<style type="text/css">
    .widt-req {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-family: 'Sora', sans-serif;
    }
    .widt-req form {
        display: flex;
        flex-direction: column;
        width: 30%;
        background: #fff;
        padding: 60px;
        box-shadow: 0 0 16px rgb(0 0 0 / 10%);
        border-radius: 25px;
    }
    .widt-req h4{font-size: 28px;}
    label{font-size: 16px; font-weight: 600; margin-bottom: 10px;}
    input{font-size: 16px; border: 1px solid #000;height: 40px;padding: 5px 10px; border-radius: 10px; margin-bottom: 15px; }
    textarea{font-size: 16px; border: 1px solid #000;height: 80px;padding: 5px 10px; border-radius: 10px; margin-bottom: 15px; }
    .submit{display: inline-block; background: #000; width: auto; color: #fff; width: 150px; margin: 0 auto; cursor: pointer;}
    .modal-content.transfer-pop.w-cionfirm {
        display: flex;
        flex-direction: column;
        width: 30%;
        background: #fff;
        padding: 60px;
        box-shadow: 0 0 16px rgb(0 0 0 / 10%);
        border-radius: 25px;
    }
    .modal-content.transfer-pop.w-cionfirm form {
        width: 100%;
        padding: 0;
        box-shadow: none;
    }
    .modal-content.transfer-pop.w-cionfirm form img{width: 60px;}
    .transfer-fund-pop.confirm-form {
        display: flex;
        flex-direction: column;
    }
    .transfer-fund-pop.confirm-form .ft-img {
        text-align: center;
    }
    .transfer-fund-pop.confirm-form .form-control-new {
        display: flex;
        flex-direction: column;
    }
    .transfer-fund-pop.confirm-form .form-control-new input{background: #000; color: #fff;}
    .submit-btn{display: inline-block; background: #000; width: auto; color: #fff; width: 150px; margin: 0 auto; cursor: pointer;font-size: 16px;
                border: 1px solid #000;
                height: 40px;
                padding: 5px 10px;
                border-radius: 10px;}
    .modal-footer.pop-ok.text-center {
        text-align: center;
    }

</style>

@endsection