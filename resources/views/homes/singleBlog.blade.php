@extends('layouts.home_new')
@section('content')
    {{-- <section class="blog-single">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1>{{ $blog->title }}</h1>
                    <div class="blog-date-time">
                        <span>
                            {{ $blogDate }}
                        </span>
                        <span>
                            {{ $blog->read_time }}
                        </span>
                    </div>
                    <div class="social-footer">
                        <a
                            href="https://www.facebook.com/DafriBank/">{{ HTML::image('public/img/front/facebook.svg', SITE_TITLE) }}</a>
                        <a
                            href="https://twitter.com/DafriBank?s=09">{{ HTML::image('public/img/front/twitter.svg', SITE_TITLE) }}</a>
                        <a
                            href="https://www.linkedin.com/mwlite/company/dafribank-limited">{{ HTML::image('public/img/front/linkedin.svg', SITE_TITLE) }}</a>
                        <a
                            href="https://instagram.com/dafribank?igshid=uvltbfz738kg">{{ HTML::image('public/img/front/instagram.svg', SITE_TITLE) }}</a>
                    </div>
                    <div class="single-blog-image-main">
                        {{ HTML::image(BLOG_FULL_DISPLAY_PATH . $blog->image, $blog->title) }}
                    </div>
                    {!! $blog->description !!}
                </div>
            </div>
        </div>
    </section> --}}
    <section class="same-section blog-details-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header-back-btn">
                        <a href="{{HTTP_PATH}}/blogs"><img src="{{PUBLIC_PATH}}/assets/fonts/images/backicon.svg" alt="image"> Back</a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog-details-parent">
                        <h2>{{ $blog->title }}</h2>
                        <ul>
                            <li>
                                <label>Category</label>
                                <p>#{{ $blog->name }}</p>
                            </li>
                            <li>
                                <label>Date</label>
                                <p>{{ $blogDate }}</p>
                            </li>
                            <li>
                                <label>Time to read</label>
                                <p>{{ $blog->read_time }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="contact-social-wrapper">
                      <ul>
                        <li><a href="https://instagram.com/dafribank?igshid=uvltbfz738kg">{{ HTML::image('public/assets/fonts/images/instagram-icon.svg', SITE_TITLE) }}</a></li>
                        <li><a href="#">{{ HTML::image('public/assets/fonts/images/twitter-icon.svg', SITE_TITLE) }}</a></li>
                        <li><a href="https://www.facebook.com/DafriBank/">{{ HTML::image('public/assets/fonts/images/facebook-icon.svg', SITE_TITLE) }}</a></li>
                      </ul>
                    </div>
                  </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="blog-details-top-image">
                        {{ HTML::image(BLOG_FULL_DISPLAY_PATH . $blog->image, $blog->title) }}
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="center-content"> 
                        {!! $blog->description !!}
                    </div>
                </div>
            </div> 
        </div>
    </section>
@endsection
