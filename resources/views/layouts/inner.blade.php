<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{$title.TITLE_FOR_LAYOUT}}</title>
        <meta name="description" content="With DafriBank Digital banking services explore an easy and better way to send & receive money, invest, make payments, manage your money, and your business whenever you want, wherever you are!">
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <link rel="icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <meta name="robots" content="index,follow">



        {{ HTML::style('public/assets/css/front/bootstrap.min.css')}}
        {{ HTML::style('public/assets/css/front/main.css?ver=0.2')}}
        {{ HTML::style('public/assets/css/front/simple-sidebar.css')}}
        {{ HTML::style('public/assets/css/front/responsive_portal.css')}}


        {{ HTML::script('public/assets/js/front/jquery.min.js')}}
        {{ HTML::script('public/assets/js/jquery.validate.js')}} 
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <?php
        ?>
        <?php if (Session::has('success_session_message')) { ?>
            <script>
                $(document).ready(function () {
                    $('#success_message').html("<?php echo Session::get('success_session_message'); ?>");
                    $('#success-alert-Modal').modal('show');
                });
            </script>
            <?php
            Session::forget('success_session_message');
        } else if (Session::has('error_session_message')) {
            ?>
            <script>
                $(document).ready(function () {
                    $('#error_message').html("<?php echo Session::get('error_session_message'); ?>");
                    $('#error-alert-Modal').modal('show');
                });
            </script>
            <?php
            Session::forget('error_session_message');
        } else if (Session::has('failed_session_message')) {
            ?>
            <script>
                $(document).ready(function () {
                    $('#failed_message').html("<?php echo Session::get('failed_session_message'); ?>");
                    $('#failed-alert-Modal').modal('show');
                });
            </script>
        <?php 
        Session::forget('failed_session_message');
        }else if(Session::has('alert_addFund_model')){ ?>
            <script>
            $(document).ready(function() {
            $('#failed_message').html("<?php echo Session::get('alert_addFund_model');?>");
            $('#failed-alert-Modal').modal('show');     
            });
            </script>
            
            <?php 
            Session::forget('alert_addFund_model');
            } ?>

@include('elements.gift_card_pop_up')

    </head>
    <body>
 
        @yield('content') 
        {{ HTML::script('public/assets/js/front/bootstrap.min.js')}}
        {{ HTML::script('public/assets/js/front/custom.js')}}
        <div class="pay_loader" id="loaderID" style="display: none;">{{HTML::image("public/img/front/dafri_loader.gif", '')}}</div>
        <div class="modal x-alert fade" id="success-alert-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-alert-lg">
                <div class="modal-content">
                    <div class="modal-body ">
                        <i class="fas fa-check-circle"></i>
                        <h4 id="success_message">Payment Successful</h4>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">ok</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal x-alert fade" id="error-alert-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-alert-lg">
                <div class="modal-content">
                    <div class="modal-body ">
                        <i class="fas fa-times-circle"></i>
                        <h4 id="error_message">Transaction failed</h4>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">ok</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal x-alert fade" id="failed-alert-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-alert-lg">
                <div class="modal-content">
                    <div class="modal-body ">

                        <i class="fas fa-exclamation-triangle"></i>
                        <h4 id="failed_message">Transaction failed</h4>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">ok</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal x-alert fade" id="blank-alert-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-alert-lg">
                <div class="modal-content">
                    <div class="modal-body ">
                        <h4 id="blank_message">Transaction failed</h4>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">ok</button>
                    </div>
                </div>
            </div>
        </div>


        <style>
.headmodel {
    padding: 30px;
    border-radius: 25px;
    text-align: center;
}
        </style>   

        <div class="modal x-dialog fade" id="logout-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content headmodel">
            <div class="modal-body ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <img src="https://www.nimbleappgenie.live/dafri/public/img/front/dafribank-logo-black.svg" alt="DafriBank - Digital Bank of Africa">
                <br><br>
                <p>Are you sure you want to logout?</p>
                <ul class="frnt-logout btn-list">
                    <li class="">
                    <button type="button"  id="myButton" class="btn btn-dark" onclick="logout();">Confirm</button></li>
                    <li class=""><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
            </div>
        </div>
        
        <script>
            function logout(){
                window.location.href = '<?php echo HTTP_PATH;?>/logout';
            }
        </script>
    </body>
</html>