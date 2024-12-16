@extends('layouts.home')
@section('content')
<section class="page-heading">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h1>
                    DafriXchange
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
<section class="x-content-section">
    <div class="container">

        <div class="row">
            <div class="col-sm-6 x-content">
                <h3 class="my-3"> <strong> The Pan-African </strong> Digital Asset Solution</h3>
                <p>We are a global company with a local presence. We believe that it is our responsibility to unlock the economic potential of a teeming African population through our unique and dependable financial services. </p>
                <p>Founded in 2020 with a mission to aid crypto adoption in Africa and now has clients in 200+ countries. DafriXchange became the first crypto exchange to build anti-restrictive feature known as "Payment Agent" </p>
                <p>DafriXchange is part of DafriTechnologies LTD, a subsidiary of DafriGroup PLC. The team comprises of a world-class professionals, experienced and devoted entrepreneurs with visionary insights, and a long history of success in the business development, finance, management, and Information Technology industries. </p>


            </div>
            <div class="col-sm-6">
                <div class="xchange-img">
                    {{HTML::image('public/img/exchangedafri.jpg', SITE_TITLE)}}
                </div>
            </div>
        </div>

        <br /><br /><br />
        <div class="row">
            <div class="col-sm-12 x-content">
                <h3 class="text-sm-center"> <strong> Our </strong> Values</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="thumb-four-box x-media">
                    <div class="thumb-four-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="thumb-four-content">
                        <h3>
                            Equality of opportunity
                        </h3>
                        <p>We believe every citizen in the world should have equal access to the global financial markets, especially Africans who have been unfairly excluded by many top financial corporations. Our goal is to right that grave wrong. </p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="thumb-four-box x-media">
                    <div class="thumb-four-icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="thumb-four-content">
                        <h3>
                            Ethical Pricing
                        </h3>
                        <p>Striving to remain profitable is an uphill battle that has led many businesses down the path of disreputable practices especially in today's economically dire times. We are different. At DafriXchange we will never compromise integrity, affordability and efficiency for profitability. You can count on that.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="thumb-four-box x-media">
                    <div class="thumb-four-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="thumb-four-content">
                        <h3>
                            Individual Empowerment
                        </h3>
                        <p>Our world-class range of services will allow everyone, anywhere to achieve financial empowerment.</p>
                    </div>
                </div>

            </div>
        </div><br /><br /><br />
        <div class="row">
            <div class="col-sm-12 text-center">
                <div class="btn-black btn-inline">
                    <a href="https://www.dafrixchange.com/login" target="_blank">Trade Now</a>
                </div>
            </div>
        </div>


    </div>
</section>
@endsection