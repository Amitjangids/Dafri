<!-- footer -->
<footer id="footer">
    <div class="container wrapper">
        <div class="row">
            <div class="col-sm-12 ftr-main">
                <div class="footerbox">
                    <div class="f-logo">
                        <a href="{!! HTTP_PATH !!}"> {{HTML::image('public/img/front/footer-logo.svg', SITE_TITLE)}}</a>
                    </div>
                    <h5>Contact us</h5>
                    <ul>
                        <li><span>Email: hello@dafribank.com</span></li>
                        <li><span>Tel: 011 568 5053</span></li>
                        <li><span>Fax: 086 560 9785</span></li>
                    </ul>
                    <div class="social-footer">
                        <a href="https://www.facebook.com/DafriBank/">{{HTML::image('public/img/front/facebook.svg', SITE_TITLE)}}</a>
                        <a href="https://twitter.com/DafriBank?s=09">{{HTML::image('public/img/front/twitter.svg', SITE_TITLE)}}</a>
                        <a href="https://www.linkedin.com/mwlite/company/dafribank-limited">{{HTML::image('public/img/front/linkedin.svg', SITE_TITLE)}}</a>
                        <a href="https://instagram.com/dafribank?igshid=uvltbfz738kg">{{HTML::image('public/img/front/instagram.svg', SITE_TITLE)}}</a>
                    </div>
                </div>
                <div class="footerbox" style="display: none">
                    <h5>Company</h5>
                    <ul>
                        <li><a href="{{URL::to('about')}}">About</a></li>
                        <li><a href="{{URL::to('press')}}">Press </a></li>
                        <li><a href="{{URL::to('career')}}">Career </a></li>
                        <li><a href="{{URL::to('choose-account')}}">Affiliate</a></li>
                        <li><a href="{{URL::to('blogs')}}">Blogs</a></li>
                        <li><a href="{{URL::to('faq')}}">FAQs</a></li>
                    </ul>
                </div>
                <div class="footerbox">
                    <h5>Our Products</h5>
                    <ul>
                        <li><a href="{{URL::to('personal-account')}}">DafriBank for you</a></li>
                        <li><a href="{{URL::to('business-account')}}">DafriBank for business </a></li>
                        <li><a href="{{URL::to('private-banking')}}">Private Banking </a></li>
                        <li><a href="{{URL::to('debit-cards')}}">Debit Cards </a></li>
                        <li><a href="{{URL::to('contact')}}">Report a lost or stolen card</a></li>
                    </ul>
                </div>
                <div class="footerbox">
                    <h5>Resources</h5>
                    <ul>
                        <li><a href="{{URL::to('dafrixchange')}}">DafriXchange</a></li>
                        <li><a href="{{URL::to('merchat-api')}}">Merchant API</a></li>
                        <li><a href="{{URL::to('dba-currency')}}">DBA Currency</a></li>

                        <li><a href="{{URL::to('defi-loan')}}">DeFi Loan</a></li>
                        <!-- <li><a href="{{URL::to('investor-relations')}}">Investor Relations </a></li> -->
                        <li><a href="https://dafribank-assets.s3.us-east-2.amazonaws.com/DafriBank-Digital-LTD-Annual-Fee-Review-2021.pdf" target="_blank">Annual Price Review</a></li>
                    </ul>
                </div>
                <div class="footerbox">
                    <h5>Legal & Policy</h5>
                    <ul>
                        <li><a href="{{URL::to('terms-condition')}}">Terms & Conditions</a></li>
                        <li><a href="{{URL::to('privacy-policy')}}">Privacy Policy</a></li>
                        <li><a href="{{URL::to('cookie-policy')}}">Cookie Notice</a></li>
                        <li><a href="{{URL::to('public/doc-files/dafrigroup-aml.pdf')}}" target="_blank">AML Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 footer-para">
            <p>DafriBank Digital LTD is a bank duly licensed by the Central Bank of Comoros with banking License B2019005. The branchless financial technology company is one of the most recognised offshore banks with web and mobile payment solutions for merchants and digital entrepreneurs. It only takes a few easy steps to set up a DafriBank business account and open up your business to over a million potential customers in 180+ countries. DafriBank is a division of DafriGroup PLC, a public company duly incorporated in South Africa with CIPC Number: 202054881006, in Nigeria with CAC Number: 1691062. DafriGroup held an unrestricted operating license in South Africa under Public Companies Act 71 of 2008. Visa &amp; MasterCard logos are Trademarks of respective brands. Digital assets are subject to a number of risks, including price volatility. Transacting in digital assets could result in significant losses and may not be suitable for some consumers. Digital asset markets and exchanges are not regulated with the same controls or customer protections available with other forms of financial products and are subject to an evolving regulatory environment. Digital assets do not typically have legal tender status and are not covered by deposit protection insurance. The past performance of a digital asset is not a guide to future performance, nor is it a reliable indicator of future results or performance. Additional disclosures can be found on the <a href="https://www.dafribank.com/terms-condition"><strong>Legal</strong> and <strong>Privacy</strong></a></p>
            </div>
            <div class="col-sm-12 col-md-12 copyright-ftr">
                <p> © 2021 DafriBank Digital. All Rights Reserved. A DafriGroup PLC company.</p>
                <hr />
            </div>
            <div class="col-sm-12 col-md-12">
                <ul class="d-flex justify-content-start ul flex-wrap">
                    <li><a href="{{URL::to('about')}}">About</a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('press')}}">Press </a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('career')}}">Career </a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('affiliate')}}">Affiliate</a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('blogs')}}">Blogs</a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('faq')}}">FAQs</a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('privacy-policy')}}">Policy</a></li>
                    <li>|</li>
                    <li><a href="{{URL::to('cookie-policy')}}">Cookie Notice</a></li>
                </ul>
            </div>
        </div>
</footer>