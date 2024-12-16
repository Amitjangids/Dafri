@extends('layouts.home_new')
@section('content')
    <section class="same-section contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="same-heading">
                        <h2>Contact Us</h2>
                        <p>Our team is here to provide you with the support and answers you need.</p>
                        <div class="contact-social-wrapper">
                            <a href="mailto:hello@dafripremier.com">hello@dafripremier.com</a>
                            <ul>
                                <li><a href="#"><img src="{{PUBLIC_PATH}}/assets/fonts/images/instagram-icon.svg" alt="image"></a></li>
                                <li><a href="#"><img src="{{PUBLIC_PATH}}/assets/fonts/images/twitter-icon.svg" alt="image"></a></li>
                                <li><a href="#"><img src="{{PUBLIC_PATH}}/assets/fonts/images/facebook-icon.svg" alt="image"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
                    <div class="login-inner-form-fileds">
                        {{ Form::open(['method' => 'post', 'name' => 'fbForm', 'id' => 'fbForm', 'class' => 'row', '[formGroup]' => 'formGroup']) }}
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control required" placeholder="Enter your name">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control required" placeholder="Enter your email">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control required" name="message" placeholder="Write your message"></textarea>
                        </div>
                        <div class="form-group">
                            {{-- <a href="#" class="btn btn-primaryx">Send</a> --}}
                            <button type="submit" class="btn btn-primaryx">Send</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <section class="about-us">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-12">
                    <h6 class="heading-short">
                        Contact Us
                    </h6>
                </div>
                <div class="col-sm-9">
                    <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
                    <div class="form-main contact-form">
                        {{ Form::open(['method' => 'post', 'name' => 'fbForm', 'id' => 'fbForm', 'class' => 'row', '[formGroup]' => 'formGroup']) }}

                        <div class="form-group col-sm-6">
                            <label>
                                Full Name
                            </label>
                            <input type="text" class="required" name="name" placeholder="Enter your Full name" />
                        </div>
                        <div class="form-group col-sm-6">
                            <label>
                                Email
                            </label>
                            <input type="email" class="required" name="email" placeholder="Enter your email" />
                        </div>
                        <div class="form-group col-sm-12">
                            <label>
                                Subject
                            </label>
                            <input type="text" class="required" name="subject" id="subject"
                                placeholder="Enter the subject of your mail" />
                        </div>
                        <div class="form-group col-sm-12">
                            <label>
                                Message
                            </label>
                            <textarea name="message" class="required" id="message" placeholder="write your message"></textarea>
                        </div>
                        <div class="form-group col-sm-12">
                            <button type="submit" class="btn btn-dark btn-rounded">Submit</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class=" contact-box">
                        <h4>Contact Information</h4>
                        <ul class="list-unstyled contact-list">
                            <li><a href="#"><i class="fa fa-envelope fa-fw"></i> hello@dafribank.com</a></li>

                            <li><a href="#"><i class="fa fa-phone fa-fw"></i> 011 568 5053</a></li>
                            <li><a href="#"><i class="fa fa-fax fa-fw"></i> 086 560 9785</a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </section> --}}
@endsection
