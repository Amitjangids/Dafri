@extends('layouts.home_new')
@section('content')
	<section class="banner-section">
        <div class="container">
          <div class="banner-slider-wrapper">
            <div class="row">
              <div class="col-md-6">
                <div class="banner-content">
                  <h1>Simplify the way you send or receive money online <sup>.TM</sup></h1>
                  <p>DafriPremier<sup>TM</sup> is a financial technology company (FinTech),  not a bank. Banking services are provided by DafriBank Digital LTD.</p>
                  <h6><img src="{{PUBLIC_PATH}}/assets/fronts/images/credit-card.svg" align="card"> Pay & Get Paid Online </h6>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Your email" aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button class="btn btn-primaryx" type="button" id="button-addon2">Get started</button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                  <div class="spaces-gal owl-carousel">
                      <div class="spaces-slide one" data-dot="<button>01</button>">
                        <picture>
                          <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/experiment01.svg">
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-mobileslider-img01.svg" alt="image">
                        </picture> 
                      </div>
                      <div class="spaces-slide two" data-dot="<button>02</button>">
                        <picture>
                          <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/experiment02.svg">
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-mobileslider-img02.svg" alt="image">
                        </picture> 
                      </div>
                      <div class="spaces-slide three" data-dot="<button>03</button>">
                        <picture>
                          <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/experiment03.svg">
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-mobileslider-img03.svg" alt="image">
                        </picture> 
                      </div>
                      <div class="spaces-slide four" data-dot="<button>04</button>">
                        <picture>
                          <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/experiment04.svg">
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-mobileslider-img02.svg" alt="image">
                        </picture>
                      </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="marquee-section">
        <div class="container-fluid">
          <marquee class="marq" direction="left" loop="infinite">
            <ul>
              <li><img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-bottom-logo-icon01.svg" alt="icon"></li>
              <li><img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-bottom-logo-icon02.svg" alt="icon"></li>
              <li><img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-bottom-logo-icon03.svg" alt="icon"></li>
              <li><img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-bottom-logo-icon04.svg" alt="icon"></li>
              <li><img src="{{PUBLIC_PATH}}/assets/fronts/images/banner-bottom-logo-icon05.svg" alt="icon"></li>
            </ul>
          </marquee>
        </div>
      </section>

      <section class="same-section dark-bg-color">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="notifyme-image text-left">
                <picture>
                  <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/notifyme.svg">
                  <img src="{{PUBLIC_PATH}}/assets/fronts/images/notifyme-mobile-img.svg" alt="image">
                </picture>
              </div>
            </div>
            <div class="col-md-6">
              <div class="same-heading">
                <h2>DafriPremier ™ for me.  Everything you wanna  do online</h2>
                <p>Whether you are a freelancer, offering online services or wanting to pay a service provider, a forex, a crypto or a betting institution DafriPremier ™ app allows you to do just about anything online.</p>
                <div class="btnsr section-btn-spacing">
                  <ul>
                    <li><a href="javascript:" class="btn btn-primaryx">Apply now</a></li>
                    <li><a href="javascript:" class="btn btn-defaultx">Learn more</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="same-section terminal-section">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="same-heading">
                <h2>DafriPremier™ For Business. With a free Check Out Terminal ™ </h2>
                <p>DafriCheck Out™ Terminal moves fast so should your business. Accept payment from anywhere in the world. Our Terminal requires no additional integration or coding; simply generate a link and start accepting Visa, MasterCard or crypto payments that settle in fiat.</p>
                <div class="btnsr section-btn-spacing">
                  <ul>
                    <li><a href="javascript:" class="btn btn-primaryx">Apply now</a></li>
                    <li><a href="javascript:" class="btn btn-defaultx">Learn more</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="notifyme-image text-right">
                <picture>
                  <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/notifyme-image.svg" alt="icon">
                  <img src="{{PUBLIC_PATH}}/assets/fronts/images/notifymemobile-image.svg" alt="image">
                </picture>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="same-section brand-logo-section">
        <div class="container">
          <div class="same-heading text-center">
            <h2>Send or receive money from all these fine institutions</h2>
          </div>
          <div class="logo-wrapper gallery_infrastructure">
              <ul>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon01.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon02.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon03.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon04.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon05.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon06.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon07.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon08.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon09.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon10.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon11.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon12.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon13.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon14.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon15.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon16.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon17.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon18.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon19.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon20.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon21.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon22.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon23.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon24.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon25.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon26.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon27.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon28.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon29.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon30.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon31.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon32.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon33.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon34.png" alt="icon"></li>
                <li class="team__person"><img src="{{PUBLIC_PATH}}/assets/fronts/images/money-icon35.png" alt="icon"></li>
              </ul>
          </div>
        </div>
      </section>

      <section class="same-section dark-bg-color mobicash-cta-section">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="same-heading text-center">
                <h2>Send money to any mobile number via MobiCash™</h2>
                <p>Your recipient doesn't have to be a DafriClient™ to receive money from our MobiCash™  eWallet. All you need is their cellphone number to MobiCash them.</p>
                <div class="btnsr section-btn-spacing">
                  <ul>
                    <li><a href="javascript:" class="btn btn-primaryx">Learn more</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="same-section invoice-section">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="same-heading">
                <h2>Create and send invoices with a hyperlinked PayNow button!</h2>
                <p>Whether you are doing social media influencing work or selling things online our invoicing service allows you to get paid fast. Simply create an invoice, insert the client’s email and get paid just like that.</p>
                <div class="btnsr section-btn-spacing">
                  <ul>
                    <li><a href="javascript:" class="btn btn-primaryx">Learn more</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="notifyme-image text-left">
                <picture>
                  <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/invoive-image01.svg" alt="image">
                  <img src="{{PUBLIC_PATH}}/assets/fronts/images/invoivemobile-image01.svg" alt="image">
                </picture>
              </div>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="same-heading">
                <h2>Aiding crypto adoption in Africa and beyond</h2>
                <p>We have enabled fiat on-ramp and off-ramp to over top 70 crypto institutions. Send and receive money from crypto wallets and exchanges including Coinbase, Binance and KuCoin.</p>
                <div class="banner-content">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Your email" aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button class="btn btn-primaryx" type="button" id="button-addon2">Sign up</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="notifyme-image text-right">
                <picture>
                  <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/invoive-image02.svg" alt="image">
                  <img src="{{PUBLIC_PATH}}/assets/fronts/images/invoivemobile-image02.svg" alt="image">
                </picture>
              </div>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="same-heading">
                <h2>EscrowPay™ One out of five people lose money online to unscrupulous actors</h2>
                <p>Don’t be that guy. Use our escrow service to create escrow contracts and close complex deals online.</p>
                <div class="btnsr section-btn-spacing">
                  <ul>
                    <li><a href="javascript:" class="btn btn-primaryx">Tell me more</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="notifyme-image text-left">
                <picture>
                  <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/invoive-image03.svg" alt="image">
                  <img src="{{PUBLIC_PATH}}/assets/fronts/images/invoivemobile-image03.svg" alt="image">
                </picture>
              </div>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="same-heading">
                <h2>Meet money without borders!</h2>
                <p>What else can I do with my DafriPremier ™ Account? The list is endless. Manage and pay your bill or subscription online, buy airtime, data or gift card. Hire a freelancer, influencer or create an influencer profile and get hired by brands around the world. Send and receive cross border payment. Request payment with our payment link. The list goes.</p>
                <div class="banner-content">
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Your email" aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button class="btn btn-primaryx" type="button" id="button-addon2">Take me there</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="notifyme-image text-right">
                <picture>
                  <source media="(min-width:576px)" srcset="{{PUBLIC_PATH}}/assets/fronts/images/invoive-image04.svg" alt="image">
                  <img src="{{PUBLIC_PATH}}/assets/fronts/images/invoivemobile-image04.svg" alt="image">
                </picture>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="same-section dark-bg-color our-story-section">
        <div class="container">
          <div class="row">
            <div class="col-md-6">
              <div class="same-heading stick-fixed">
                <h2>Our story</h2>
                <p>DafriPremier™ was founded on a notion that Africa should have its own payment company built by those who understand its culture and market dynamism. </p>
                <div class="btnsr section-btn-spacing">
                  <ul><li><a href="javascript:" class="btn btn-primaryx">Apply now</a></li></ul>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="story-inner-box">
                <div class="same-heading">
                  <h2>2020</h2>
                  <p>In 2020 a group of aggrieved digital entrepreneurs who have been left stranded following an arbitrary decision taken by one of the payment giants to disable and dislodge cashout mechanism available to Africans on their platform leaving those with funds in their wallet stranded set the idea that today is DafriPremier™ in motion.</p>
                </div>
              </div>
              <div class="story-inner-box">
                <div class="same-heading">
                  <h2>2021</h2>
                  <p>In 2021 a group of aggrieved digital entrepreneurs who have been left stranded following an arbitrary decision taken by one of the payment giants to disable and dislodge cashout mechanism available to Africans on their platform leaving those with funds in their wallet stranded set the idea that today is DafriPremier™ in motion.</p>
                </div>
              </div>
              <div class="story-inner-box">
                <div class="same-heading">
                  <h2>2022</h2>
                  <p>In 2022 the confidence of the founders were buyoend when the  startup received an interest from SoftBank, one of the largest investment banks in the world. A subsequent interest came in light  of a R602 million ($30 million) offer made by a South African holding company for a majority share in the same year.</p>
                </div>
              </div>
              <div class="story-inner-box">
                <div class="same-heading">
                  <h2>2023</h2>
                  <p>In 2023 the confidence of the founders were buyoend when the  startup received an interest from SoftBank, one of the largest investment banks in the world. A subsequent interest came in light  of a R602 million ($30 million) offer made by a South African holding company for a majority share in the same year.</p>
                </div>
              </div>
              <div class="story-inner-box">
                <div class="same-heading">
                  <h2>2024</h2>
                  <p>In 2024 the confidence of the founders were buyoend when the  startup received an interest from SoftBank, one of the largest investment banks in the world. A subsequent interest came in light  of a R602 million ($30 million) offer made by a South African holding company for a majority share in the same year.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="same-section testimonials-section">
        <div class="container">
            <div class="testimonial_sec same-heading">
              <h2>What our clients say</h2>
              <div class="testimonial_inner">
                <div class="card">
                    <div class="card-body">
                      <div class="clients-header">
                        <figure>
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/clients-image01.png" alt="image">
                        </figure>
                        <h4>Cynthia</h4>
                        <p>CEO, Stylish Hairs Inc.</p>
                      </div>
                      <p>Best thing to happen to Africa in recent years. I love the Auto-settlement:)</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                      <div class="clients-header">
                        <figure>
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/clients-image02.png" alt="image">
                        </figure>
                        <h4>Jacinta</h4>
                        <p>MD, Move Fashion</p>
                      </div>
                      <p>DafriPremier is our darling. Very underrated!</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                      <div class="clients-header">
                        <figure>
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/clients-image03.png" alt="image">
                        </figure>
                        <h4>Cynthia</h4>
                        <p>CEO, Stylish Hairs Inc.</p>
                      </div>
                      <p>The claims process was hassle-free, and they even provided helpful guidance on mitigating further damage.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                      <div class="clients-header">
                        <figure>
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/clients-image01.png" alt="image">
                        </figure>
                        <h4>Cynthia</h4>
                        <p>CEO, Stylish Hairs Inc.</p>
                      </div>
                      <p>Best thing to happen to Africa in recent years. I love the Auto-settlement:)</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                      <div class="clients-header">
                        <figure>
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/clients-image02.png" alt="image">
                        </figure>
                        <h4>Jacinta</h4>
                        <p>MD, Move Fashion</p>
                      </div>
                      <p>DafriPremier is our darling. Very underrated!</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                      <div class="clients-header">
                        <figure>
                          <img src="{{PUBLIC_PATH}}/assets/fronts/images/clients-image03.png" alt="image">
                        </figure>
                        <h4>Cynthia</h4>
                        <p>CEO, Stylish Hairs Inc.</p>
                      </div>
                      <p>The claims process was hassle-free, and they even provided helpful guidance on mitigating further damage.</p>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </section>
	 

	
	@endsection

  