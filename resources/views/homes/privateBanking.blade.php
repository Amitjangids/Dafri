@extends('layouts.home')
@section('content')
<section class="page-heading">
    <div class="container wrapper2">
        <div class="row">
            <div class="col-sm-6">
                <h1>
                    Private <br>
                    Banking
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="follow-us">
                    <h6>
                        Follow us on
                    </h6>
                    <div class="social-header">
                        <a href="https://www.facebook.com/DafriBank/">{{HTML::image('public/img/front/facebook.svg', SITE_TITLE)}}</a>
                        <a href="https://twitter.com/DafriBank?s=09">{{HTML::image('public/img/front/twitter.svg', SITE_TITLE)}}</a>
                        <a href="https://www.linkedin.com/mwlite/company/dafribank-limited">{{HTML::image('public/img/front/linkedin.svg', SITE_TITLE)}}</a>
                        <a href="https://instagram.com/dafribank?igshid=uvltbfz738kg">{{HTML::image('public/img/front/instagram.svg', SITE_TITLE)}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- terms-and-conditions -->
<section class="content-page">
    <div class="container wrapper3">
        <div class="row">
            <div class="col-sm-12">
                <div class="big-heading-section text-center">
                    <h3>Experience the benefits of DafriBank Digital Private Banking</h3>
                    <p>Your dedicated private banker is your direct point of entry for your banking and financial needs. You can be assured of confidentiality, discretion and professionalism in all our dealings with you.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="card feature-card">
                    <div class="icon"><i class="fas fa-hands-helping"></i></div>
                    <h3>Relationship-driven</h3>
                    <p>Your dedicated private banker is your direct point of entry for your banking and financial needs.</p>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card feature-card">
                    <div class="icon"><i class="fas fa-puzzle-piece"></i></div>
                    <h3>Customised solutions</h3>
                    <p>Comprehensive range of financial solutions designed to meet your needs.</p>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card feature-card">
                    <div class="icon"><i class="fas fa-parachute-box"></i></div>
                    <h3>Specialised advisory services</h3>
                    <p>Network of experienced specialists.</p>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card feature-card">
                    <div class="icon"><i class="fas fa-lightbulb"></i></div>
                    <h3>Priority services</h3>
                    <p>Convenient service channels.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="info-box text-center">
                    {{HTML::image('public/img/debt-finance1.jpg', SITE_TITLE, ['class' =>'img-fluid'])}}
                    <h4>A one-on-one relationship with a private banker</h4>
                    <p>Need a new transactional account? No problem. What about financing? Got you covered. Or what about expert advice on how to diversify your wealth? Whatever it is that you need, we’re a phone call away.</p>

                </div>
            </div>
        </div>
        <div class="load-more">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="info-box text-center">
                        {{HTML::image('public/img/bonusdeposit.jpg.rendition.1280.1280.jpg', SITE_TITLE, ['class' =>'img-fluid'])}}
                        <h4>Grow and protect your wealth</h4>
                        <p>Through our network of experienced specialists, we’re able to advise you on a range of solutions that will help you protect and preserve your wealth.</p>
                        <a href="mailto:hello@dafribank.com" class="btn btn-outline-dark">Contact Us</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="info-box text-center">
                        {{HTML::image('public/img/bank-draft.jpg.rendition.1280.1280.jpg', SITE_TITLE, ['class' =>'img-fluid'])}}
                        <h4>Be informed of the best investment opportunities</h4>
                        <p>Our qualified team of specialists will consult with you on the best investment opportunities. Our approach involves having an understanding of your needs, advising you on tailored financial solutions, implementing your financial plan and regularly reviewing your situation to ensure you reach your goals and objectives.</p>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="big-heading-section text-center">
                        <h3>Lifestyle Benefits</h3>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="card feature-card">
                        <div class="icon"><i class="fas fa-couch"></i></div>
                        <h3>Extensive airport lounge access</h3>
                        <p>Relax before embarking on your next flight.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card feature-card">
                        <div class="icon"><i class="fas fa-concierge-bell"></i></div>
                        <h3>Special offers</h3>
                        <p>Look out for offers and experiences specially for you.</p>
                    </div>
                </div>
                <div class="col-sm-12 text-center">
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="<?php echo HTTP_PATH; ?>/public/private-bank.pdf" class="btn btn-dark" download>Download brochure</a></li>
                        <li class="list-inline-item"><a href="{{URL::to('contact')}}" class="btn btn-outline-dark">Please contact me</a></li>
                    </ul>
                </div>
            </div>


        </div>
    </div>
</section>
@endsection