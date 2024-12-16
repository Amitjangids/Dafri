@extends('layouts.home')
@section('content')
<section class="page-heading">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-6">
                    <h1>
                        What we <br>
                        offer
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
                    <h2>
                        DafriBank <br>
                        products
                    </h2>
                    <p>DafriBank ensures that you have everything you expect from private banking - exclusivity, status, confidentiality, accountability, understanding, and deep expertise. Our relationship-based service provides solutions for personal wealth as well as corporate assets and business interests.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="alter-box-main">
        <div class="container">
            <div class="row">
                <div class="alternet-box-main">
                    <div class="row account-thumb-main">
                        <div class="col-sm-6">
                            <div class="account-img">
							{{HTML::image('public/img/front/personal-account.jpg', SITE_TITLE)}}
                            </div>
                        </div>
                        <div class="col-sm-6 content-account">
                            <h3><strong> Personal </strong> Account</h3>
                            <p>Banking the way it should be. DafriBank For You Account is a bank account that is more than a bank account. It helps you manage your day to day banking needs and helps you save towards financial security and peace-of-mind. Plus you always have access to your funds with easy withdrawals. Applying takes less than 2 minutes! No credit check. No ChexSystems.</p>
                            <ul>
                                <li><i class="list-humb"></i><span>A bank account in 2 minutes</span></li>
                                <li><i class="list-humb"></i><span>Say goodbye to monthly fees</span></li>
                                <li><i class="list-humb"></i><span>Online purchase, bill payments & more</span></li>
                                <li><i class="list-humb"></i><span>No monthly maintenance charges</span></li>
                                <li><i class="list-humb"></i><span>No foreign transaction fees</span></li>
                                <li><i class="list-humb"></i><span>No minimum balance requirements</span></li>

                            </ul>
                            <div class="btn-black">
                                <a href="{{URL::to('personal-account')}}">Learn more</a>
                            </div>
                        </div>
                    </div>
                    <div class="row account-thumb-main">
                        <div class="col-sm-6 content-account">
                            <h3><strong>Premier Business</strong> Banking</h3>
                            <p>We ease the pain of business banking by offering customers a seamless business account and a better overall banking experience. Get paid faster with DafriBusiness. Effortlessly pay international invoices, vendors, and employees with a real exchange rate, in 180+ countries.</p>
                            <ul>
                                <li><i class="list-humb"></i><span>Free business account</span></li>
                                <li><i class="list-humb"></i><span>Free merchant API</span></li>
                                <li><i class="list-humb"></i><span>No monthly subscription fee</span></li>
                                <li><i class="list-humb"></i><span>Pay expenses online or in-store without foreign transaction fees</span></li>
                                <li><i class="list-humb"></i><span>Everything you need to bank abroad</span></li>
                                
                            </ul>
                            <div class="btn-black">
                                <a href="{{URL::to('business-account')}}">Learn more</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-img">
							{{HTML::image('public/img/front/business-account.jpg', SITE_TITLE)}}
                            </div>
                        </div>
                    </div>
                    <div class="row account-thumb-main">
                        <div class="col-sm-6">
                            <div class="account-img">
							{{HTML::image('public/img/front/dafribank-card.jpg', SITE_TITLE)}}
                            </div>
                        </div>
                        <div class="col-sm-6 content-account">
                            <h3><strong>DafriBank</strong> Card</h3>
                            <p>Pay for your goods and services online with DafriCard. DafriCard is available for both business and brokerage traders firms that need to make efficient fee transactions.</p>
                            <ul>
                                <li><i class="list-humb"></i><span>No monthly fees</span></li>
                                <li><i class="list-humb"></i><span>Intra-currency transactions</span></li>
                                <li><i class="list-humb"></i><span>Controlled security</span></li>
                            </ul>
                            <div class="btn-black">
                                <a href="{{URL::to('debit-cards')}}">Learn more</a>
                            </div>
                        </div>
                    </div>
                    <div class="row account-thumb-main">
                        <div class="col-sm-6 content-account">
                            <h3> <strong>DBA </strong> Token</h3>
                            <p>The DBA Token is the native token of DafriBank integrated within our platform to facilitate payments and cross-border transactions and be utilized across all subsidiaries of the DafriGroup.</p>
                            <ul>
                                <li><i class="list-humb"></i><span>ERC 20 tokens</span></li>
                                <li><i class="list-humb"></i><span>Backed by several use-cases</span></li>
                                <li><i class="list-humb"></i><span>Projected to grow in value </span></li>
                            </ul>
                            <div class="btn-black">
                                <a href="{{URL::to('dba-currency')}}">Learn more</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-img">
							{{HTML::image('public/img/front/dba-coin.jpg', SITE_TITLE)}}
                            </div>
                        </div>
                    </div>
                    <div class="row account-thumb-main">
                        <div class="col-sm-6">
                            <div class="account-img">
							{{HTML::image('public/img/front/dafri-exchange.jpg', SITE_TITLE)}}
                            </div>
                        </div>
                        <div class="col-sm-6 content-account">
                            <h3>DafriXchange</h3>
                            <p>DafriExchange is Africa's premier cryptocurrency exchange. Built on customer-centric values, we endeavor to provide a professional, smart, intuitive, and innovative trading experience to promptly serve our customers. The Exchange platform provides the most comprehensive and compliant solutions for traders.</p>
                            <ul>
                                <li><i class="list-humb"></i><span>High-speed performance</span></li>
                                <li><i class="list-humb"></i><span>Low trading fees</span></li>
                                <li><i class="list-humb"></i><span>Top-Notch security features</span></li>
                                <li><i class="list-humb"></i><span>Super user-friendly interface</span></li>
                                
                            </ul>
                            <div class="btn-black">
                                <a href="{{URL::to('dafrixchange')}}">Learn more</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection