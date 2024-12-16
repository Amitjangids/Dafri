@extends('layouts.home')
@section('content')

<!-- page-heading -->
    <section class="page-heading">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-6">
                    <h1 >
                        Business <br>
                        account
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
    <section class="heading-two">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="blue-clr">
                        Africa's best business <br>
                        banking is here
                    </h2>
                    <p class="blue-clr">Be a part of the movement to change the way businesses avail banking services. Apply now for a free business account with Africa's best banking service provider.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- account-type-business -->
    <section class="account-type-business">
        <div class="heading-two">
            <div class="container wrapper2">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="blue-clr">
                            Type of business <br>
                            account we offer
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="business-type-main">
            <div class="container wrapper2">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="account-type-thumb gold-bg">
                            {{HTML::image('public/img/front/account-icon.svg', SITE_TITLE)}}
                            <h5>Gold</h5>
                            <p>For businesses with annual turnover from $0.00 up to $1 million, Gold accounts are favourable to startups and small-scale businesses. We provide you with online tools that support your growth.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="account-type-thumb platinum-bg">
                            {{HTML::image('public/img/front/account-icon.svg', SITE_TITLE)}}
                            <h5>Platinum</h5>
                            <p>For businesses with an annual turnover from $1 million to $5 million. Our straightforward banking charges gives you clarity, so you can concentrate more on building your business.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="account-type-thumb enterprise-bg">
                            {{HTML::image('public/img/front/account-icon.svg', SITE_TITLE)}}
                            <h5>Enterprise</h5>
                            <p>For established businesses with an annual turnover of about $5 million, we provide dedicated support. You'll get a personal relationship manager who will assist your business with its banking needs.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- powerfull-banking -->
    <section class="powerfull-banking">
        <div class="heading-two">
            <div class="container wrapper2">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="blue-clr">
                            Powerfully simple <br>
                            Business banking
                        </h2>
                        <p class="blue-clr">DafriBank is here to serve the needs of various types of businesses. Whether you are a merchant looking to accept cashless payments from your customers, or a Broker looking to send funds with low transaction fees, DafriBank supports your needs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="power-bank-business">
            <div class="container wrapper2">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="list-power">
                            <ul>
                                <li><i class="list-humb"></i><span class="blue-clr">Free ATM withdrawals</span></li>
                                <li><i class="list-humb"></i><span class="blue-clr">Unlimited card swipes</span></li>
                                <li><i class="list-humb"></i><span class="blue-clr">Access to a business savings account</span></li>
                                <li><i class="list-humb"></i><span class="blue-clr">Access to internet banking</span></li>
                                <li><i class="list-humb"></i><span class="blue-clr">Overdraft facility available</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="powerfull-banking-img">
                            {{HTML::image('public/img/home-banner.png', SITE_TITLE)}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- features-box -->
    <section class="feature-box">
        <div class="heading-two">
            <div class="container wrapper2">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="blue-clr">
                            Business account <br>
                            features
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="features-main">
            <div class="container wrapper2">
                <div class="row">
                    <div class="main-feat">
                        <div class="feat-box">
                            <h6>
                                01
                            </h6>
                            <h4>Integrate with your accounting tools</h4>
                            <p>You can integrate your DafriBank business account with your existing accounting software to ease out the vendor payments and salary deposits</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                02
                            </h6>
                            <h4>Movement of funds for brokerage </h4>
                            <p>DafriBank provides easy movement of funds, which is built to assist traders in moving funds effortlessly.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                03
                            </h6>
                            <h4>Digital Receipts</h4>
                            <p>Your business needs to keep copies of payments and bills. DafriBank makes maintaining business account receipts easy.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                04
                            </h6>
                            <h4>Overdraft at a
                                low rate</h4>
                            <p>Our service lets you make use of pre-approved overdrafts. All DafriBank overdrafts are at very competitive interest rates.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                05
                            </h6>
                            <h4>Accept payments
                                with DafriBank </h4>
                            <p>DafriBank makes it easy for your customers to pay you for your products/services. We'll enable you to accept payment via Cards and E-wallet.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                06
                            </h6>
                            <h4>Instant transaction
                                notification</h4>
                            <p>Get notified via SMS, email, and your Mobile App the second you send or receive a payment. We make sure that our clients never miss a payment due date with help from DafrBank payment alerts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- quick-black-box -->
    <section class="quick-black-box">
        <div class="container">
            <div class="row">
                <div class="col-sm-10 m-auto">
                    <h2>Open an account in minutes. <br>
                        Quick and easy.</h2>
                    <div class="btn-white">
                        <a href="{{URL::to('business-account-registration?refId=na&pid=MA==')}}">Register now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection