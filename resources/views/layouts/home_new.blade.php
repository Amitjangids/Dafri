<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ SITE_TITLE }}</title>
    <meta name="description"
        content="With DafriBank Digital banking services explore an easy and better way to save, invest, make payments, manage your money, and your business whenever you want, wherever you are!" />
    <meta property="og:title" content="DafriBank Digital - Banking with no Border!" />
    <meta property="og:description"
        content="With DafriBank Digital banking services explore an easy and better way to save, invest, make online payments, manage your money, and your business whenever you want, wherever you are!" />
    <meta property="og:image" content="https://www.dafribank.com/public/img/DafriBank.png" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="{!! FAVICON_PATH !!}" type="image/x-icon" />
    <link rel="icon" href="{!! FAVICON_PATH !!}" type="image/x-icon" />
    <meta name="robots" content="index,follow">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    {{--     {{ HTML::style('public/assets/assets/css/style.css?v=21') }}
    {{ HTML::style('public/assets/assets/css/media.css?v=21') }} --}}

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/style.css?v=1.1') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/media.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
</head>

<body> 
    @include('elements.gift_card_pop_up')
    <div class="site-wrap">
        @include('elements.new_header')
        @yield('content')
        @include('elements.new_footer')



        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="{{ asset('public/assets/fronts/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('public/assets/fronts/js/owl.carousel.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('public/assets/fronts/js/jquery.in-viewport-class.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('public/assets/fronts/js/pagescript.js') }}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
        {{ HTML::script('public/assets/js/jquery.validate.js') }} 
        <script type="text/javascript">
            $(".spaces-gal.owl-carousel").owlCarousel({
                autoplay: true,
                slideSpeed: 9000,
                items: 1,
                autoHeight: true,
                loop: true,
                dots: true,
                dotsData: true,
                nav: false,
                center: true,
                responsiveClass: true,
                smartSpeed: 400,
                margin: 30,
            });
        </script>

        <script type="text/javascript">
            


            $(document).ready(function() {
                $(function() {
                    setRandomClass();
                    setInterval(function() {
                        setRandomClass();
                    }, 1500);

                    function setRandomClass() {
                        var teamList = $('.gallery_infrastructure');
                        var teamItem = teamList.find('.team__person');
                        var number = teamItem.length;
                        var random = Math.floor((Math.random() * number));
                        if (teamItem.eq(random).hasClass('team__person_active')) {
                            var random = random + 1
                        }
                        $('.team__person_active').addClass('team__person_old')
                            .siblings().removeClass('team__person_old');
                        teamItem.eq(random).addClass('team__person_active')
                            .siblings().removeClass('team__person_active');
                    }
                });
            })
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/sticky-kit/1.1.3/sticky-kit.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".stick-fixed").stick_in_parent({
                    offset_top: 110
                });
            });
        </script>

        <script type="text/javascript">
            $('.testimonial_inner').slick({
                dots: false,
                infinite: true,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                prevArrow: false,
                nextArrow: false,
                responsive: [{
                        breakpoint: 1300,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1,
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        }
                    },
                    {
                        breakpoint: 545,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                        }
                    }
                ]
            });
        </script>
        <script type="text/javascript">
            $(document).ready((function() {
                $(".footer-slide").click((function() {
                    if (768 > $(window).width()) {
                        var e = $(this).next(".footer-toogle");
                        $(".footer-slide").not(this).removeClass("openarrow"), $(".footer-toogle").not(
                            e).slideUp(), e.slideToggle(), $(this).toggleClass("openarrow")
                    }
                }))
            }))
        </script>
        <script>
            $(document).ready(function() {
                let blogsToShow = 4; // Number of blogs to show per scroll
                let totalBlogs = $('.blog-item').length; // Total number of blog posts
                let visibleBlogs = 4; // Initially visible blog posts (first 4)

                // Initially hide blog items after the first 4
                $('.blog-item').slice(4).addClass('hidden');

                // When the user scrolls
                $(window).on('scroll', function() {
                    let scrollTop = $(window).scrollTop(); // Current scroll position
                    let windowHeight = $(window).height(); // Height of the viewport
                    let docHeight = $(document).height(); // Total document height

                    // Check if we are close to the bottom of the page (100px from the bottom)
                    if (scrollTop + windowHeight >= docHeight - 1000) {
                        let hiddenBlogs = $('.blog-item.hidden');

                        // If there are hidden blogs, reveal the next set of blogs
                        if (hiddenBlogs.length > 0) {
                            // Reveal the next 'blogsToShow' blogs
                            hiddenBlogs.slice(0, blogsToShow).removeClass('hidden');
                            visibleBlogs += blogsToShow;

                            // If all blogs are visible, disable further scrolling
                            if (visibleBlogs >= totalBlogs) {
                                $(window).off('scroll'); // Disable further scroll event
                            }
                        }
                    }
                });
            });
        </script>
</body>

</html>
