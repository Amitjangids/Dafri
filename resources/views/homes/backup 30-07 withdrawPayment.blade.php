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
<!-- two-part-main -->
<section class="two-part-main">
    <div class="container-fluid">
        <div class="row">
            <div class="modal1" id="confirmDialog">
            <div class="modal-dialog md1">
                <div class="modal-content transfer-pop">
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    @if(Session::has('erroe_message'))
                        <div class="alert alert-danger">
                             <strong>Alert!</strong> {{ Session::get('erroe_message') }}
                        </div>
                    @endif
                    {{ Form::open(array('method' => 'post', 'id' => 'loginform', 'class' => '')) }} 
                    
                    <div class="transfer-fund-pop">
                        <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>WithDral Request</h4>
                        <div class="filed-box">
                            <div class="form-control-new">
                                <label>Merchant Name:</label>
                                <input type="text" id="recipName"  value="{{$merchant_name}}" disabled>
                            </div>
                            <div class="form-control-new">
                                <label>Amount:</label>
                                <input type="text" value="{{$user->currency.' '.$amount}}" id="recipAccNum" placeholder="" disabled>
                            </div>
                        </div>
                        <div class="filed-box">
                            <div class="form-control-new w100">
                                <label>User Name:</label>
                                <input type="text" id="recipEmail" value="{{$user_name}}" placeholder="" disabled>
                            </div>

                        </div>

                        <div class="filed-box" id="cuncyConvrsnTF">
                            <div class="form-control-new w100">
                                <label>Remark:</label>
                                <input type="text" value="{{$remark}}" id="recipAmountTF" placeholder="">
                            </div>                    
                        </div>

                    </div>
                    <div class="modal-footer pop-ok">
                        @if(!empty($user))
                            <button class="sub-btn" type="submit" name="submit" value="submit">
                                Submit
                            </button>
                        @endif
                        
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

            
        </div>
    </div>
</section>

@endsection