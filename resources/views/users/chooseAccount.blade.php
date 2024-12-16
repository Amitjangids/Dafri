@extends('layouts.login')
@section('content')
<div class="black-bg">
        <!-- logo -->
        <div class="pre-regsiter-logo">
            <div class="wrapper">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="logo-white">
                            <a href="{!! HTTP_PATH !!}">{{HTML::image(WHITE_LOGO_PATH, SITE_TITLE)}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- choose-account-main -->
        <section class="choose-account-main">
            <div class="wrapper">
                <div class="row  ca">
                    <div class="col-sm-6">
                        <div class="left-main-heading color-white">
                            <h1>Leap in Banking, the
                                world <span>loves<span>.</span></span></h1>
                            <p>Explore an easy and better way to save, make payments, manage your money and your business whenever you want, wherever you are!</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="choose-head">Choose
                            <span>
                                account </span></h4>
                        <div class="row  ca">
                            <div class="col-sm-6">
                                <div class="choose-account-name">
                                    {{HTML::image('public/img/front/personal-account-dp.jpg', SITE_TITLE)}}
                                    <?php 
                                    echo $pid;
                                    echo $refID;
                                    if($pid!="" && $refID!="") {  ?>
                                    <a href="{{URL::to('personal-account-registration?refId='.$refID.'&pid='.$pid)}}" class="overlay">
                                        <?php }elseif($refID!=""){  ?>
                                            <a href="{{URL::to('personal-account-registration?refId='.$refID)}}" class="overlay">
                                        <?php }else{ ?>
                                        <a href="{{URL::to('personal-account-registration')}}" class="overlay">
                                       <?php } ?>
                                        <h4>
                                            Personal
                                            <br>
                                            <span>
                                                account </span>
                                        </h4>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="choose-account-name">
                                    {{HTML::image('public/img/front/business-account-dp.jpg', SITE_TITLE)}}
                                    <?php if($pid!="" && $refID!="") {  ?>
                                    <a href="{{URL::to('business-account-registration?refId='.$refID.'&pid='.$pid)}}" class="overlay">
                                    <?php }elseif($refID!=""){  ?>
                                    <a href="{{URL::to('business-account-registration?refId='.$refID)}}" class="overlay">
                                    <?php }else{ ?>
                                        <a href="{{URL::to('business-account-registration')}}" class="overlay">
                                        <?php } ?>
                                        <h4>
                                            Business
                                            <br>
                                            <span>
                                                account </span>
                                        </h4>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection