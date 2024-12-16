@extends('layouts.home')
@section('content')
<section class="page-heading">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-8">
                    <h1>
                        Personal and business <br>
                        debit card
                    </h1>
                </div>
                <div class="col-sm-4">
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
                        Manage your daily <br>
                        expense with DafriCard
                    </h2>
                    <p class="blue-clr">Pay for your goods and services online with DafriCard. DafriCard is available for both business and brokerage traders firms that need to make efficient fee transactions.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- account-type-business -->

    <section class="pay-with-easy">
        <div class="container wrapper2">
            <div class="row align-item-center">
                <div class="col-sm-6">
                    <div class="pay-easy-content">
                        <h2 class="blue-clr">
                            Pay with ease
                        </h2>
                        <p class="blue-clr">Pay online for goods and services. The DafriCard is accepted across the world. You can even pay in various foreign currencies without converting your money. Qualified cardholders will also have access to our contactless payment service. Whatever you pay for using DafriCard will be itemized. Thus, you'll see the transactions categorised when you access the Mobile App, or Web Portal spend analysis. </p>
                    </div>
                </div>
                <div class="col-sm-6">
                	<div class="card-box">
					{{HTML::image('public/img/front/sample-card.png', SITE_TITLE)}}
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
                            Liquidate your money
                        </h2 class="blue-clr">
                        <p class="blue-clr">DafriCard gives you the ability to access your money with ease. DafriCard is entirely free for every DafriBank account. The card comes with futuristic features such as contactless payments.</p>
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
                            <h4>No monthly fees</h4>
                            <p>The DafriCard will be completely free of cost. There will be no charge for domestic contactless payment, swipe payments, and ATM withdrawals.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                02
                            </h6>
                            <h4>Use your card abroad</h4>
                            <p>You can use your DafriCard abroad the same as you use it at your home. However, transactions made abroad will attract minimal processing fees. </p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                03
                            </h6>
                            <h4>Pay on the go</h4>
                            <p>Enjoy hassle-free payments with hundreds of merchants accepting payment via DafriCard.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                04
                            </h6>
                            <h4>Intra-Currency transactions</h4>
                            <p>Need to exchange currency for your international transaction? DafriCard has got you covered. You can easily make payments in various foreign currencies without converting your balance first.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                05
                            </h6>
                            <h4>Controlled security</h4>
                            <p>You have complete control over your DafriCard security. If you lose your card or get stolen, you can instantly block it using the DafriBank mobile App or the online portal.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                06
                            </h6>
                            <h4>Loyalty points</h4>
                            <p>We offer personalized reward points based on your spending with DafriCards. The rewards available differ for personal and business accounts.</p>
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
                        <a href="{{URL::to('choose-account')}}">Register now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection