<!-- header -->
<header id="header" class="header-relative">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg">
                <div class="logo col-sm-2">
                    <a class="navbar-brand" href="{!! HTTP_PATH !!}">{{HTML::image(LOGO_PATH, SITE_TITLE)}}</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">{{HTML::image('public/img/front/bar.jpg', '')}}</span>
                </button>
                <div class="col-sm-3 ml-auto">
                    <div class="log-rt">


                        @if(session()->has('user_id'))
                        <a href="{{URL::to('logout')}}">Logout</a>
                        @else 
                        <a href="{{URL::to('login')}}">
                            {{HTML::image('public/img/front/login.png', 'login-icon')}}
                            Login /
                        </a>
                        <a href="{{URL::to('register')}}">Register</a>
                        @endif
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>


