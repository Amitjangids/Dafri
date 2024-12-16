@extends('layouts.home')
@section('content')
<section class="page-heading">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h1>
                    Affiliate
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
<section class="career-section">
    <div class="container">
        <div class="row account-thumb-main">
            <div class="col-sm-7 content-account">
                <h3><strong>The DafriBank</strong> AFFILIATE Program</h3>
                <p>Imagine a world where everyone lives in harmony? A world where people help each other? A world where companies and institutions enable opportunities for communities to thrive? Imagine a bank that shares revenue with its customers?</p>
                <p>The DafriBank affiliate program rewards our customers for talking about us to friends and family. We pay you for inviting people to use the DafriBank Digital platform.</p>
                <p>A unique referral link is assigned to all of our registered users. All you need do is copy this link from your dashboard and share it with friends, family and acquaintances. Once someone registers through your link, and transact on our platform you will automatically earn commissions from transactions. Easy peasy.</p>

            </div>
            <div class="col-sm-5">
                <div class="account-img">
                    {{HTML::image('public/img/affiliate1.jpg', SITE_TITLE)}}
                </div>
            </div>
            <div class="col-sm-12 content-account">

                <p>Our affiliate partners are entitled to a whopping 25% of the total revenue we earn on fees from transactions carried out by each customer. This is not a one-time thing as you're entitled to this commission for as long as your referrals continue to use DafriBank Digital. Rewards are paid out in DBA Currency and can be withdrawn effortlessly at any time.</p>

                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><i class="list-humb"></i><span>25% revenu share</span></li>
                            <li><i class="list-humb"></i><span>Easy cashout</span></li>
                            <li><i class="list-humb"></i><span>Payout in DBA</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><i class="list-humb"></i><span>Earn for Life</span></li>
                            <li><i class="list-humb"></i><span>Free to Join</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 content-account">
                <h3>What are you still waiting for?</h3>
                <p>Head to your dashboard to copy your affiliate link now to start earning free money today.</p>
                <div class="btn-black btn-inline">
                    <a href="{{url('/choose-account')}}" target="_blank">View Dashboard</a>
                </div>


            </div>
        </div>
    </div>
</section>



@endsection