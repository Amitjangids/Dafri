@extends('layouts.home')
@section('content')

<!-- page-heading -->
    <section class="page-heading">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-6">
                    <h1>
                        Personal <br>
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
                        An account with <br>
                        no monthly fees
                    </h2>
                    <p class="blue-clr">Sign up for your personal account with DafriBank in minutes, A bank renowned for its first-class functionality and low banking fees. </p>
                </div>
            </div>
        </div>
    </section>
    <section class="financial-ecosystem">
        <div class="heading-two">
            <div class="container wrapper2">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="blue-clr">
                            Building your <br>
                            financial ecosystem
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container wrapper2">
            <div class="row">
                <div class="box-new">
                    <div class="col-sm-6">
                        <div class="account-types">
                            <div class="tilt-box silver">
                                {{HTML::image('public/img/front/piggi.svg', SITE_TITLE)}}
                            </div>
                            <h4 class="blue-clr">DafriBank Silver <br> Account </h4>
                            <p class="blue-clr">Ideal for clients whose annual income is between zero to  $5010</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="account-types">
                            <div class="tilt-box orange">
                                {{HTML::image('public/img/front/piggi.svg', SITE_TITLE)}}
                            </div>
                            <h4 class="blue-clr">DafriBank Gold
                                <br> Cheque Account </h4>
                            <p class="blue-clr">Ideal for clients whose annual income is not less than $5010.90 and not more than $17,896.02.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="account-types">
                            <div class="tilt-box blue">
                                {{HTML::image('public/img/front/piggi.svg', SITE_TITLE)}}
                            </div>
                            <h4 class="blue-clr">DafriBank Premier
                                <br> Account </h4>
                            <p class="blue-clr">Ideal for clients whose annual income is not less than $17,896.08 and not more than $44740.13.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="account-types">
                            <div class="tilt-box black">
                                {{HTML::image('public/img/front/piggi.svg', SITE_TITLE)}}
                            </div>
                            <h4 class="blue-clr">DafriBank Private
                                <br> Wealth Account </h4>
                            <p class="blue-clr">Ideal for clients with an annual income of $50,000 and assets valued at $1 million. </p>
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
                            Personal account <br>
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
                            <h6 >
                                01
                            </h6>
                            <h4>No monthly fees</h4>
                            <p>The personal accounts you will hold with DafriBank will be completely cost-free. There will be no charge for EFT, domestic transfers, and ATM withdrawals.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                02
                            </h6>
                            <h4>Pay on the Go</h4>
                            <p>Enjoy hassle-free payment with hundreds of merchants accepting payment via our online platform. Pay via our QR code integrated wallet.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                03
                            </h6>
                            <h4>Easy movement of
                                funds for traders</h4>
                            <p>We enable you to make various types of transactions from the online portal and mobile app without physically visiting the bank branch.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                04
                            </h6>
                            <h4>Use debit card, EFT, and Crypto</h4>
                            <p>Load your flexible DafriBank App wallet with funds from either your debit card, EFT, or Crypto wallet.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                05
                            </h6>
                            <h4>Exchange currencies</h4>
                            <p>Need to exchange currency for your international transaction? DafriBank allows the exchange of currencies in USD, GBP, EUR, NIG,  ZAR, and many more.</p>
                        </div>
                        <div class="feat-box">
                            <h6>
                                06
                            </h6>
                            <h4>Liquidate your deposits anywhere</h4>
                            <p>Whether you want to Cash your deposits or deposit funds into your account, DafriBank will process it instantly.via our available and secure options. No delays.</p>
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
                        <a href="{{URL::to('personal-account-registration?refId=na&pid=MA==')}}">Register now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
        

@endsection