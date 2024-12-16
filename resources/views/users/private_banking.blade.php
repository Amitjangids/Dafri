@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-12">
                    <div class="big-heading-section text-center">
                        <h3>Experience the benefits of DafriBank Digital Private Banking</h3>
                        <p>Your dedicated private banker is your direct point of entry for your banking and financial needs. You can be assured of confidentiality, discretion and professionalism in all our dealings with you.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="card feature-card">
                        <div class="icon"><i class="fas fa-hands-helping"></i></div>
                        <h3>Relationship-driven</h3>
                        <p>Your dedicated private banker is your direct point of entry for your banking and financial needs.</p>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card feature-card">
                        <div class="icon"><i class="fas fa-puzzle-piece"></i></div>
                        <h3>Customised solutions</h3>
                        <p>Comprehensive range of financial solutions designed to meet your needs.</p>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card feature-card">
                        <div class="icon"><i class="fas fa-parachute-box"></i></div>
                        <h3>Specialised advisory services</h3>
                        <p>Network of experienced specialists.</p>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card feature-card">
                        <div class="icon"><i class="fas fa-lightbulb"></i></div>
                        <h3>Priority services</h3>
                        <p>Convenient service channels.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="info-box text-center">
                     {{HTML::image('public/img/front/debt-finance1.jpg', SITE_TITLE, ['class'=>"img-fluid"])}}   <!-- <img src="public/img/front/debt-finance1.jpg" class="img-fluid" alt="..."> -->
                        <h4>A one-on-one relationship with a private banker</h4>
                        <p>Need a new transactional account? No problem. What about financing? Got you covered. Or what about expert advice on how to diversify your wealth? Whatever it is that you need, we’re a phone call away.</p>
                        <!--<button type="button" class="btn btn-outline-dark">Tell me More</button>-->
                    </div>
                </div>
            </div>
            <div class="load-more">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="info-box text-center">
                           {{HTML::image('public/img/front/bonusdeposit.jpg.rendition.1280.1280.jpg', SITE_TITLE, ['class'=>"img-fluid"])}}   <!--  <img src="public/img/front/bonusdeposit.jpg.rendition.1280.1280.jpg" class="img-fluid" alt="..."> -->
                            <h4>Grow and protect your wealth</h4>
                            <p>Through our network of experienced specialists, we’re able to advise you on a range of solutions that will help you protect and preserve your wealth.</p>
                            <!--<a href="mailto:hello@dafribank.com" class="btn btn-outline-dark">Contact Us</a>-->
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="info-box text-center">
                            {{HTML::image('public/img/front/bank-draft.jpg.rendition.1280.1280.jpg', SITE_TITLE, ['class'=>"img-fluid"])}}  
                            <!-- <img src="public/img/front/bank-draft.jpg.rendition.1280.1280.jpg" class="img-fluid" alt="..."> -->
                            <h4>Be informed of the best investment opportunities</h4>
                            <p>Our qualified team of specialists will consult with you on the best investment opportunities. Our approach involves having an understanding of your needs, advising you on tailored financial solutions, implementing your financial plan and regularly reviewing your situation to ensure you reach your goals and objectives.</p>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="big-heading-section text-center">
                            <h3>Lifestyle Benefits</h3>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card feature-card">
                            <div class="icon"><i class="fas fa-couch"></i></div>
                            <h3>Extensive airport lounge access</h3>
                            <p>Relax before embarking on your next flight.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card feature-card">
                            <div class="icon"><i class="fas fa-concierge-bell"></i></div>
                            <h3>Special offers</h3>
                            <p>Look out for offers and experiences specially for you.</p>
                        </div>
                    </div>
                    <div class="col-sm-12 text-center">
                        <ul class="list-inline">
                            <li class="list-inline-item"><a href="<?php echo HTTP_PATH; ?>/public/private-bank.pdf" class="btn btn-dark" download>Download brochure</a></li>
                            <!--<li class="list-inline-item"><button type="button" class="btn btn-outline-dark">Please contact me</button></li>-->
                        </ul>
                    </div>
                </div>


            </div>


            <br/><br/>
        </div>
        <?php
        Session::forget('error_message');
        Session::forget('success_message');
        Session::save();
        ?>
        <script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
        <script type="text/javascript">
                                $(document).ready(function () {
                                    $(".active-hover").click(function () {

                                    });

                                    $(".inner-mathod-box").hover(
                                            function () {
                                                $(".inner-mathod-box").removeClass("active-hover");
                                                $(this).addClass("active-hover");

                                            }

                                    );
                                });

                                function validateFloatKeyPress(el, evt) {
                                    var charCode = (evt.which) ? evt.which : event.keyCode;
                                    var number = el.value.split('.');
                                    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                                        return false;
                                    }
                                    //just one dot
                                    if (number.length > 1 && charCode == 46) {
                                        return false;
                                    }
                                    //get the carat position
                                    var caratPos = getSelectionStart(el);
                                    var dotPos = el.value.indexOf(".");
                                    if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1)) {
                                        return false;
                                    }
                                    return true;
                                }

                                function getSelectionStart(o) {
                                    if (o.createTextRange) {
                                        var r = document.selection.createRange().duplicate()
                                        r.moveEnd('character', o.value.length)
                                        if (r.text == '')
                                            return o.value.length
                                        return o.value.lastIndexOf(r.text)
                                    } else
                                        return o.selectionStart
                                }

                                function downloadFile() { 
                                    
                                    $.ajax({
        url: '<?php echo PUBLIC_PATH;?>/private-bank.pdf',
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = 'private-bank.pdf';
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    });
                                   
                                }
        </script>
        @endsection