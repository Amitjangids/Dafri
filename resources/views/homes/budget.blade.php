@extends('layouts.home_new')
@section('content')        
<section class="same-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="same-banner-heading">
                        <h2>Share your invite link </h2>
                        <p>Earn up to $2500 weekly from eCash incentive program.</p>
                        <a href="{{URL::to('choose-account')}}" class="btn btn-primaryx">Buy Now</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="epay-image-parent">
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/ecash-banner-img.jpg" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="same-section dba-earn-section bg-color">
        <div class="container">
            <div class="same-heading text-center">
                <h3>How it work</h3>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="paid-image-parent">
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/ecash-img.png" alt="image">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dba-coiun-content">
                        <ul>
                            <li> <strong>Step 01</strong> <p>Share your referral link with friends</p> </li>
                            <li> <strong>Step 02</strong> <p>Your friend sign up, deposit and buy DBA with the money using DafriBank eSwap</p> </li>
                            <li> <strong>Step 03</strong> <p>You will receive 30% of any amount spent by your friend every time he/she buy DBA on DafriBank</p> </li>
                            <li> <strong>Step 04</strong> <p>In addition to a whooping 30% cash back, you will also get a 25% fee split for as long as your friend continue to use DafriBank Digital products</p> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="same-section investing-section">
        <div class="container">
            <div class="same-heading">
                <h3>What is the benefit of investing in DBA?</h3>
                <p>Good question, Digital Bank of Africa (DBA) is a product by DafriBank, a digital bank licensed and regulated by the central bank of Comoros. DafriBank is one of the fastest growing Fintech brands in Africa and has been tagged by analysts as the next African unicorn with 510 merchants signing up for its ePay API within 3 months of operation. Investment in DBA technically makes you a shareholder in an aspiring unicorn company. The price of 1 DBA is $0.10 at the time of writing, assuming you picked up 100 000 DBA today with $10 000, and the price surge to $10 in the near future the portfolio will automatically be worth $1 000 000.00.</p>
            </div>
            <div class="same-heading">
                <h3>Tell me more?</h3>
                <p>DBA is also a native token for DafriXchange Pro, AfriGo Mall, DafriSocial and DafriBorrow. For a comparison sake, BNB was $16 two years ago and currently trading at $510 at the time of writing. With DBA you will soon be able to bank and trade for free within the DafriBank and DafriXchange ecosystems.</p>
            </div>
        </div>
    </section>

    <section class="same-dba same-section p-0">
        <div class="container">
            <div class="dba-box">
                <div class="dba-contant"><h3>Don’t have DBA?</h3></div>
                <div class="dba-button">
                    <a href="{{URL::to('choose-account')}}" class="btn btn-primaryx">Buy Now</a>
                </div>
            </div>
        </div>
    </section>

@endsection

  