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
            $('#showEye').html('<img src="<?php echo HTTP_PATH; ?>/public/img/front/eye.svg" alt="Dafri Bank">');
        } else {
            x.type = "password";
            $('#showEye').html('<img src="<?php echo HTTP_PATH; ?>/public/img/front/eye.svg" alt="Dafri Bank">');
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
<!-- two-part-main -->
<div class="widt-req">

    <div class="modal-content transfer-pop w-cionfirm">
        <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
        @if(Session::has('erroe_message'))
        <div class="alert alert-danger">
            <strong>Alert!</strong> {{ Session::get('erroe_message') }}
        </div>
        @endif
        <?php if ($amount > 0) { ?>
        <?php if ($order_id != 'N/A') { ?>
        
        <?php if ($user_id != 'N/A') { ?>
                {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => '','onsubmit'=>'return check_validation()')) }} 

                <div class="transfer-fund-pop confirm-form">
                    <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Withdrawal Request</h4>
                    <div class="d-flex FULL-FLEX">
                        <div class="filed-box col-sm-6">
                            <div class="form-control-new w100">
                                <label>Merchant Name:</label>
                                <input type="text" id="recipName" value="{{strtoupper($merchant_name)}}" disabled="">
                            </div>

                        </div>

                        <div class="filed-box col-sm-6">
                            <div class="form-control-new w100">
                                <label>Amount:</label>
                                <input type="text" value="{{$user->currency.' '.$amount}}" id="recipAccNum" placeholder="" disabled="">
                            </div>

                        </div>
                    </div>
                    <div class="filed-box col-sm-12">
                        <div class="form-control-new w100">
                            <label>User Name:</label>
                            <input type="text" id="recipEmail" value="{{strtoupper($user_name)}}" placeholder="" disabled="">
                        </div>

                    </div>

                    <div class="filed-box col-sm-12" id="cuncyConvrsnTF">
                        <div class="form-control-new w100">
                            <label>Remark:</label>
                            <textarea type="textarea" value="{{$remark}}" id="recipAmountTF" placeholder="" disabled="">{{$remark}}</textarea>    
                        </div>                    
                    </div>

                </div>
                <div class="modal-footer pop-ok text-center col-sm-12">
                    @if(!empty($user))
                    <input type="hidden" name="submit" value="submit">
                    <button class="sub-btn button_disable" type="submit">
                        Submit
                    </button>
                    @endif

                </div>
                {{ Form::close() }}

<script>

function check_validation()
{

 $('.button_disable').prop('disabled',true);   
 return true;
}

</script>    


            <?php } else { ?>
                <div class="no_record">
                    Merchant ID / User ID not valid.
                </div>
            <?php } ?>
        <?php } else { ?> 
            <div class="no_record">
                    Order ID not valid.
                </div>
        <?php } ?>
            
        <?php } else { ?>
            <div class="no_record">
                Amount not valid.
            </div>
        <?php } ?>
    </div>



</div>


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