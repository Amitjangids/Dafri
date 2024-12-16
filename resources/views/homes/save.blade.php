@extends('layouts.home_new')
@section('content')        
<section class="same-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="same-banner-heading">
                        <h2>Earn up to 80% interest per annum on your DBA </h2>
                        <p>Becoming Africa’s Number 1 Cryptocurrency: The DBA Journey.</p>
                        <ul>
                            <li> Licensed by the CIBA <span> <img src="{{HTTP_PATH}}/public/assets/assets/images/liceance-icon.png" alt="{{HTTP_PATH}}/public/assets/assets/images"> </span> </li>
                            <li> Deposits Insured <span> <img src="{{HTTP_PATH}}/public/assets/assets/images/deposite-icon.png" alt="{{HTTP_PATH}}/public/assets/assets/images"> </span> </li>
                        </ul>
                        <a href="{{URL::to('choose-account')}}" class="btn btn-primaryx">Buy Now</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="epay-image-parent">
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/earn-banner-img.png" alt="image">
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
                        <img src="{{HTTP_PATH}}/public/assets/assets/images/dba-coin-icon.png" alt="image">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dba-coiun-content">
                        <ul>
                            <li> <strong>Step 01</strong> <p>Buy DBA from DafriBank eSwap, LBank, PCS, Hotbit or any other exchange</p> </li>
                            <li> <strong>Step 02</strong> <p>Deposit DBA into DafriBank eSavings Vault and select your plan - how long you want to fix your deposit </p> </li>
                            <li> <strong>Step 03</strong> <p>Lock your DBA and start earning cool  interest.</p> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="same-section investing-section">
        <div class="container">
            <div class="same-heading">
                <h3>What is the benefit of investing in DBA? </h3>
                <p>Good question, Digital Bank of Africa (DBA) is a product by DafriBank, a digital bank licensed and regulated by the central bank of Comoros. DafriBank is one of the fastest growing Fintech brands in Africa and has been tagged by analysts as the next African unicorn with 510 merchants signing up for its ePay API within 3 months of operation. Investment in DBA technically makes you a shareholder in an aspiring unicorn company. The price of 1 DBA is $0.10 at the time of writing, assuming you picked up 100 000 DBA today with $10 000, and the price surge to $10 in the near future the portfolio will automatically be worth $1 000 000.00. </p>
            </div>
            <div class="same-heading">
                <h3>Tell me more? </h3>
                <p>DBA is also a native token for DafriXchange Pro, AfriGo Mall, DafriSocial and DafriBorrow. For a comparison sake, BNB was $16 two years ago and currently trading at $510 at the time of writing. With DBA you will soon be able to bank and trade for free within the DafriBank and DafriXchange ecosystems.</p>
            </div>
        </div>
    </section>

    <section class="same-dba same-section pt-0">
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

  