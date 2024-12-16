@extends('layouts.home_new')
@section('content')        
    <section class="same-section epay-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="same-banner-heading">
                        <a href="#">PAYMENT LINKS</a>
                        <h2>Share a link. Get paid.</h2>
                        <p>It’s easy to create and share DafriBank payment links through social media, email, text, or anywhere you want to do business online.</p>
                        <ul>
                            <li> Licensed by the CIBA <span> <img src="{{HTTP_PATH}}/public/assets/assets/images/liceance-icon.png" alt="{{HTTP_PATH}}/public/assets/assets/images"> </span> </li>
                            <li> Deposits Insured <span> <img src="{{HTTP_PATH}}/public/assets/assets/images/deposite-icon.png" alt="{{HTTP_PATH}}/public/assets/assets/images"> </span> </li>
                        </ul>
                        <a href="{{URL::to('choose-account')}}" class="btn btn-primaryx">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="epay-image-parent">
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/e-pay-banner-image.png" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="same-section bg-color anything-section-parent">
        <div class="container">
            <div class="same-heading">
                <h3>Accept online payments for anything you sell.</h3>
                <p>It’s easy to share DafriBank payment links through social media, email, text, or anywhere you want to do business online.</p>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="payment-anything-parent">
                        <figure>
                            <img src="{{HTTP_PATH}}/public/assets/assets/images/payment-image01.png" alt="image">
                        </figure>
                        <div class="payment-bottom-content">
                            <p>Put a "buy now" button on your website. No special software or plugins needed.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="payment-anything-parent">
                        <figure>
                            <img src="{{HTTP_PATH}}/public/assets/assets/images/payment-image02.png" alt="image">
                        </figure>
                        <div class="payment-bottom-content">
                            <p>Send links via email or text message directly to your customers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="payment-anything-parent">
                        <figure>
                            <img src="{{HTTP_PATH}}/public/assets/assets/images/payment-image03.png" alt="image">
                        </figure>
                        <div class="payment-bottom-content">
                            <p>Post links to Instagram, Facebook, or Twitter and sell to your followers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="same-section get-paid-parent">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="same-heading">
                        <h3>Get paid anywhere you can paste a link.</h3>
                        <p>Plus, DafriBank Payment Links comes with features to manage your business and incoming payment activity.</p>
                    </div>
                    <div class="paid-content-parent">
                        <p><strong>Get notified every time you make a sale.</strong> DafriBank notifications help you stay on top of your sales with real-time updates.</p>
                        <p> <strong>Stay on top of your inventory.</strong> DafriBank can alert you if your inventory ever gets low, so you never run out of stock. </p>
                        <p><strong>They’re customizable.</strong> Specify the name of your product or service, add an email field,shipping address prompt, and more.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="paid-image-parent">
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/paid-image.png" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="same-section bg-color">
        <div class="container">
            <div class="same-heading text-center">
                <h3>Run your business better everywhere you go.</h3>
                <p>Every account includes access to DafriBank Dashboard, a suite of tools that save time, deliver useful insights, and help boost your bottom line.</p>
            </div>
            <div class="bussines-parent">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="run-bussiness-img">
                            <img src="{{HTTP_PATH}}/public/assets/assets/images/bussines-img.png">
                        </div>
                    </div>
                    <div class="col-lg-6">
                       <div class="bussiness-content-parent">
                        <ul>
                            <li> <span><img src="{{HTTP_PATH}}/public/assets/assets/images/bussines-list-icon.png" alt="image"></span> Collect and edit customer contact info in one convenient place to streamline your  marketing and sales efforts. </li>
                            <li> <span><img src="{{HTTP_PATH}}/public/assets/assets/images/bussines-list-icon.png" alt="image"></span> Track purchase history down to the customer level and discover new ways to promote and sell more. </li>
                            <li> <span><img src="{{HTTP_PATH}}/public/assets/assets/images/bussines-list-icon.png" alt="image"></span> Instantly see which products and services are your best sellers and grow your business. </li>
                        </ul>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section cta-section same-section common-parent">
        <div class="container">
            <div class="cta-box d-flex justify-content-between align-items-center">
                <div class="row">
                    <div class="col-md-7">   
                        <h3>Open an account in minutes. <br> Quick and easy.</h3>
                        <p class="lead">It only takes a few minutes to start enjoying free benefits. Download DafriBank on Google Play or the App Store.</p>
                        <ul class="list-inline list-store">
                          <li class="list-inline-item"> <a href="{{URL::to('choose-account')}}"><img src="{{HTTP_PATH}}/public/assets/assets/images/google-play.svg" class="d-block" alt="Play Store" loading="lazy"></a></li>
                          <li class="list-inline-item"><a href="{{URL::to('choose-account')}}"><img src="{{HTTP_PATH}}/public/assets/assets/images/app-store.svg" class="d-block" alt="App Store" loading="lazy"></a></li>
                        </ul>
                        <ul class="list-inline list-info">
                          <li class="list-inline-item">Licensed by the CIBA <i class="fa-solid fa-id-card"></i></li>
                          <li class="list-inline-item">Deposits Insured <i class="fa-solid fa-bank"></i></li>
                        </ul>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                          <a href="{{URL::to('choose-account')}}" class="btn btn-dark btn-lg">Register Now</a>
                        </div>
                    </div>
                    <div class="col-md-5 d-none d-sm-inline">
                        <div class="giftcard-bottom-parent">
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/giftcard-bottom-animate-img.png" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection

  