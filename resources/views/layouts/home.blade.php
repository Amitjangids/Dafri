<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{SITE_TITLE}}</title>
        <meta name="description" content="With DafriBank Digital banking services explore an easy and better way to save, invest, make payments, manage your money, and your business whenever you want, wherever you are!" />
        <meta property="og:title" content="DafriBank Digital - Banking with no Border!"/>
        <meta property="og:description" content="With DafriBank Digital banking services explore an easy and better way to save, invest, make online payments, manage your money, and your business whenever you want, wherever you are!" />
        <meta property="og:image" content="https://www.dafribank.com/public/img/DafriBank.png" />
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="630"/>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <link rel="icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <meta name="robots" content="index,follow">



        {{ HTML::style('public/assets/css/front/style.css?ver=0.2')}}
        {{ HTML::style('public/assets/css/front/responsive.css')}}
        {{ HTML::style('public/assets/css/front/bootstrap.min.css')}}

        {{ HTML::script('public/assets/js/front/jquery.min.js')}}
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>

        <?php if(Session::has('success_session_message')){ ?>
            <script>
                $(document).ready(function() {
                    $('#success_message').html("<?php echo Session::get('success_session_message');?>");
                    $('#success-alert-Modal').modal('show');
                });
            </script>
        <?php 
        Session::forget('success_session_message');
        } else if(Session::has('error_session_message')){  ?>
            <script>
                $(document).ready(function() {
                    $('#error_message').html("<?php echo Session::get('error_session_message');?>");
                    $('#error-alert-Modal').modal('show');
                });
            </script>
        <?php 
        Session::forget('error_session_message');
        } else if(Session::has('failed_session_message')){  ?>
            <script>
                $(document).ready(function() {
                    $('#failed_message').html("<?php echo Session::get('failed_session_message');?>");
                    $('#failed-alert-Modal').modal('show');
                });
            </script>
        <?php 
        Session::forget('failed_session_message');
        }  ?>
        
    </head>
    <body>
        @include('elements.header')
        @yield('content') 
        @include('elements.footer')
        <!-- script -->
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>-->
        {{ HTML::script('public/assets/js/front/bootstrap.min.js')}}
        <script type="text/javascript">
$(document).ready(function () {
    $(function () {
        $(".hamburger-menu").click(function () {
            $(this).toggleClass("active");
            $('.menu').toggleClass("active");
            $('body').toggleClass("active");
        });
    });
    if (window.outerWidth < 1024) {
        //comp_container | comp_submenu 
        $("#comp_container").click(function () {
            $("#comp_submenu").toggle();
            $("#comp_submenu1").hide();
            $("#comp_submenu2").hide();
            $("#comp_submenu3").hide();
            $("#comp_submenu4").hide();
        });
        $("#comp_container1").click(function () {
            $("#comp_submenu1").toggle();
            $("#comp_submenu").hide();
            $("#comp_submenu2").hide();
            $("#comp_submenu3").hide();
            $("#comp_submenu4").hide();
        });
        $("#comp_container2").click(function () {
            $("#comp_submenu2").toggle();
            $("#comp_submenu1").hide();
            $("#comp_submenu").hide();
            $("#comp_submenu3").hide();
            $("#comp_submenu4").hide();
        });
        $("#comp_container3").click(function () {
            $("#comp_submenu3").toggle();
            $("#comp_submenu").hide();
            $("#comp_submenu1").hide();
            $("#comp_submenu2").hide();
            $("#comp_submenu4").hide();
        });
        $("#comp_container4").click(function () {
            $("#comp_submenu4").toggle();
            $("#comp_submenu").hide();
            $("#comp_submenu1").hide();
            $("#comp_submenu2").hide();
            $("#comp_submenu3").hide();
        });
    }

});
        </script>
<!--        <script>
                $(document).ready(function () {
                    $('#blank_message').html("Scheduled System Deployment: <br><br> Time 1:00 pm - 4:00 pm CAT");
                    $('#blank-alert-Modal').modal('show');
                });
            </script>
        <div class="modal x-alert fade" id="blank-alert-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-alert-lg">
                <div class="modal-content">
                    <div class="modal-body ">
                        <h4 id="blank_message">Transaction failed</h4>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">ok</button>
                    </div>
                </div>
            </div>
        </div>-->

         {{ HTML::script('public/assets/js/front/bootstrap.min.js')}}
         
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

        @include('elements.gift_card_pop_up')

    </body>
</html>