@extends('layouts.home')
@section('content')
<section class="page-heading">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-6">
                    <h1>
                        Frequently <br>
                        Asked Questions
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
    <!-- cookies -->
    <section class="content-page">
        <div class="container wrapper2">
            <div class="row">
                <div class="accordion-box">
                    <div class="container wrapper2">
                        <div id="accordion" class="accordion">
                            <div class="card mb-0">
								
                                @foreach($faqs as $key=>$faq)
								<div class="card-header collapsed" data-toggle="collapse" href="#collapse_{{$key}}">
                                    <a href="javascript:void(0);" class="card-title">
									{{ $faq->faq_ques }}
                                    </a>
                                </div>
                                <div id="collapse_{{$key}}" class="card-body collapse" data-parent="#accordion">
                                    {!! $faq->faq_ans !!}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection