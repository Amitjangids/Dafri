

<!-- header -->
<header id="header" class="header-relative">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg">
                <div class="logo col-sm-2">
                    <a class="navbar-brand" href="{!! HTTP_PATH !!}">{{HTML::image(LOGO_PATH, SITE_TITLE)}}</a>
                </div>
                <div class="col-sm-7">
                    <div class="page-heading">
                        <h2>{{$page_heading}}</h2>
                    </div>
                </div>
                <?php $userHInfo = DB::table('users')->where('id', session()->get('user_id'))->first(); ?>
                <div class="col-sm-3 ml-auto">
                    <div class="pro-name">
                        <span>{{$userHInfo->name}}</span>
                        <div class="profile-header"  id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <!--<a class="nav-link dropdown-toggle" href="#" id="" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                            @if(isset($userHInfo->profile_image))
                            {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$userHInfo->profile_image, SITE_TITLE, ['id'=> 'pimage'])}}
                            @else
                            {{HTML::image('public/img/front/no-user.png', SITE_TITLE, ['id'=> 'pimage'])}}
                            @endif
                            <!--</a>-->
                        </div>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                            <a class="dropdown-item" href="{{URL::to('users/dashboard')}}">Dashboard </a>
                            <a class="dropdown-item" href="{{URL::to('users/settings')}}">Edit Profile</a>
                            <a class="dropdown-item" href="{{URL::to('logout')}}">Logout</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>
<style>
    .pro-name .dropdown-menu {
        left: auto;
        right: 0;
        z-index: 999;
    }
</style>



