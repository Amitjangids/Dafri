<!-- header -->
<header id="header">
    <div class="container">
        <div class="row">
            <div class="col-sm-2">
                <div class="logo">
                    <a href="{!! HTTP_PATH !!}"> {{HTML::image(LOGO_PATH, SITE_TITLE)}}</a>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="menu-open">
                    <ul>
                        <li><a href="{{URL::to('/')}}">Home</a></li>
                        <li><a href="{{URL::to('about')}}">About</a></li>
                        <li><a href="{{URL::to('what-we-offer')}}">Products</a></li>
                        <li><a href="{{URL::to('blogs')}}">Blog</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-4 head-right">
                <div class="sign-head">
                    <a href="{{URL::to('choose-account')}}">Sign up for free</a>
                </div>
                <div class="hamburger">
                    <a class="hamburger-menu flex">
                        <span></span>
                        <span class="hide-bar"></span>
                        <span></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="menu flex">
        <div class="container wrapper">
            <div class="menu-header">
                <a href="{{URL::to('/')}}"> {{HTML::image('public/img/front/menu-logo.svg', SITE_TITLE)}}</a>
                <div class="sign-head sign-btn-black">
                    <a href="{{URL::to('choose-account')}}">Sign up for free</a>
                </div>
            </div>
            <div class="flex">
                  <div class="header-menu mob-m">
                    <h5><a href="{{URL::to('/')}}">Home</a></h5>
                    
                </div>
                <div class="header-menu">
                    <h5 id="comp_container">Company</h5>
                    <ul id="comp_submenu">
                        <li><a href="{{URL::to('about')}}">About</a></li>
                        <li><a href="{{URL::to('press')}}">Press </a></li>
                        <li><a href="{{URL::to('career')}}">Career </a></li>
                        <li><a href="{{URL::to('choose-account')}}">Affiliate</a></li>
                        <li><a href="{{URL::to('blogs')}}">Blogs</a></li>
                        <li><a href="{{URL::to('faq')}}">FAQs</a></li>
                    </ul>
                </div>
                <div class="header-menu">
                    <h5 id="comp_container1">Our Products</h5>
                    <ul id="comp_submenu1">
                        <li><a href="{{URL::to('personal-account')}}">Dafribank for you</a></li>
                        <li><a href="{{URL::to('business-account')}}">Dafribank for business </a></li>
                        <li><a href="{{URL::to('debit-cards')}}">Debit Cards </a></li>
                        <li><a href="#">Find ATM</a></li>
                        <li><a href="{{URL::to('contact')}}">Report a lost or stolen card</a></li>
                    </ul>
                </div>
                <div class="header-menu">
                    <h5 id="comp_container2">Resources</h5>
                    <ul id="comp_submenu2">
                        <li><a href="{{URL::to('dafrixchange')}}">Exchange</a></li>
                        <li><a href="{{URL::to('dba-currency')}}">DBA Currency
                            </a></li>
                        <li><a href="{{URL::to('contact')}}">Get Support
                            </a></li>
                        <li><a href="{{URL::to('defi-loan')}}">DeFi Loan
                            </a></li>
                        <li><a href="{{URL::to('investor-relations')}}">Investors Relations </a></li>
                    </ul>
                </div>
				
				<div class="header-menu">
                <h5 id="comp_container4">Login To Internet Banking</h5>
				<ul id="comp_submenu4">
                <li><a href="{{URL::to('personal-login')}}">Personal</a></li>
                <li><a href="{{URL::to('business-login')}}">Corporate</a></li>
                </ul>
                </div>
				
                <div class="header-menu">
                    <h5 id="comp_container3">Legal &amp; Policy</h5>
                    <ul id="comp_submenu3">
                        <li><a href="{{URL::to('terms-condition')}}">Terms &amp; Conditions
                            </a></li>
                        <li><a href="{{URL::to('privacy-policy')}}">Privacy Policy
                            </a></li>
                        <li><a href="{{URL::to('cookie-policy')}}">Cookie Notice
                            </a></li>
                        <li><a href="{{URL::to('public/doc-files/dafrigroup-aml.pdf')}}">AML Policy
                            </a></li>
                    </ul>
                </div>
				
				
				
            </div>
        </div>
    </div>
</header>