@extends('layouts.home_new')
@section('content')
<style>
    .hidden {
        display: none;
    }
</style>
<section class="same-section blog-section">
    <div class="container">
        <div class="same-heading">
            <h2>Latest News</h2>
            <!-- <a href="#" class="btn btn-defaultx">All News <img src="{{PUBLIC_PATH}}/assets/fonts/images/rightarrow.svg"
                    alt="image"></a> -->
        </div>
        <div class="row">
            @foreach ($getNews as $index => $news)
                @if ($index == 0)
                    <div class="col-lg-6">
                        <div class="blog-content-box">
                            <h3>{{ $news->title }}</h3>
                            <img src="{{ BLOG_FULL_DISPLAY_PATH . $news->image }}" alt="{{ SITE_TITLE }}">

                            <div class="blog-bottom-menu">
                                <div class="blog-menu-left"><a href="#">#{{ $news->name }}</a></div>
                                <div class="blog-menu-right">
                                    <ul>
                                        <li>{{ $news->created_at->format('M d, Y') }}</li>
                                        <li><span></span></li>
                                        <li>{{ $news->read_time }}</li>
                                    </ul>
                                </div>
                            </div>
                            <p>{!! \Illuminate\Support\Str::limit(strip_tags($news->description), 200) !!}</p>

                        </div>
                    </div>
                @endif
            @endforeach
            <div class="col-lg-6">
                <div class="blog-content-box blog-right-content">
                    @foreach ($getNews as $index => $news2)
                        @if ($index > 0)
                            <div class="blog-inner-content">
                                <h4>{{ $news2->title }}</h4>
                                <div class="blog-bottom-menu">
                                    <div class="blog-menu-left"><a href="#">#{{ $news2->name }}</a></div>
                                    <div class="blog-menu-right">
                                    <ul>
                                        <li>{{ $news->created_at->format('M d, Y') }}</li>
                                        <li><span></span></li>
                                        <li>{{ $news->read_time }}</li>
                                    </ul>
                                    </div>
                                </div>
                                <p>{!! \Illuminate\Support\Str::limit(strip_tags($news2->description), 200) !!}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<section class="same-section blog-section our-blog-main-page">
    <div class="container">
        <div class="same-heading">
            <h2>Our Blog</h2>
            <!-- <a href="#" class="btn btn-defaultx">All Articles <img src="{{PUBLIC_PATH}}/assets/fonts/images/rightarrow.svg" alt="image"></a> -->
        </div>
        <div id="blog-container" class="row">
            @foreach ($blogs as $index => $blog)
                <div class="col-lg-6 blog-item {{ $index >= 4 ? 'hidden' : '' }}">
                    <div class="blog-content-box">
                        <h3>{{ strtoupper(strtolower($blog->title)) }}</h3>
                        <a href="{{ URL::to('blog/' . $blog->slug) }}">
                            <img src="{{ BLOG_FULL_DISPLAY_PATH . $blog->image }}" alt="{{ SITE_TITLE }}">
                        </a>
                        <div class="blog-bottom-menu">
                            <div class="blog-menu-left"><a href="#">{{$blog->name}}</a></div>
                            <div class="blog-menu-right">
                                <ul>
                                    <li>{{ $blog->created_at->format('M d, Y') }}</li>
                                    <li><span></span></li>
                                    <li>{{ $blog->read_time }}</li>
                                </ul>
                            </div>
                        </div>
                        <p>Want to improve your financial health? Discover essential tips to develop strong financial habits
                            that will help you save, invest, and spend wisely this year.</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- <section class="blog-page">
    <div class="container wrapper2">
        <div class="row">
            @foreach ($blogs as $blog)
            @php($blogLink = $blog->slug)
            <div class="col-sm-4">
                <div class="blog-thumb">
                    <h4>
                        <a href="{{ URL::to('blog/' . $blogLink) }}">{{ strtoupper(strtolower($blog->title)) }}</a>
                    </h4>
                    <div class="blog-thumb-img">

                        <a href="{{ URL::to('blog/' . $blogLink) }}">{{ HTML::image(BLOG_FULL_DISPLAY_PATH .
                            $blog->image, SITE_TITLE) }}</a>
                    </div>

                    <div class="blog-date-time">
                        <span>
                            {{ $blog->created_at->format('M d, Y') }}
                        </span>
                        <span>
                            {{ $blog->read_time }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section> --}}
@endsection