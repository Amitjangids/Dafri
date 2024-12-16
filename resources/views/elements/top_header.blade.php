<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active banner-dafri">
            {{HTML::image('public/img/front/banner-dafribank.png', SITE_TITLE)}}
        </div>
        <div class="carousel-item banner-dafri">
        {{HTML::image('public/img/front/banner-dafribank1.jpg', SITE_TITLE)}}
        </div>
        <!-- <div class="carousel-item banner-dafri">
            <a href="https://t.me/DafriExchange/256" target="_blank">{{HTML::image('public/img/front/banner-dafribank2.jpg', SITE_TITLE)}}</a>
        </div> -->
    </div>
    <!--   <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a> -->
</div>
<?php $userHInfo = $recordInfo = DB::table('users')->where('id', session()->get('user_id'))->first(); ?>
<div class="head-bar">
    <div class="wrapper2">
        <button class="btn btn-mobile" id="menu-toggle"><span class="navbar-toggler-icon">
                {{HTML::image('public/img/front/bars.svg', SITE_TITLE)}}
            </span></button>
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="search-head">
                    {{HTML::image('public/img/front/search.svg', SITE_TITLE)}}
                    <input type="text" name="search_q" id="search_q" placeholder="Search" onchange="goTo(this.value, '<?php echo HTTP_PATH; ?>');">
                </div>
            </div>
            <div class="col-sm-6 search-bar">
                <div class="header-kyc-parent">
                <div class="noti-right-bar">
                    <div class="profile-top-bar">
                        <a href="{{URL::to('auth/my-account')}}">
                            <div class="pro-top-bar-img"> 
                                @if(isset($userHInfo->image))
                                {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$userHInfo->image, SITE_TITLE, ['id'=> 'pimage1'])}}
                                @else
                                {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE, ['id'=> 'pimage1'])}}
                                @endif
                            </div>
                            @if($userHInfo->user_type == 'Personal')
                            <span>Hi, {{$recordInfo->gender}}  @include('elements.personal_short_name')</span>
                            @elseif($userHInfo->user_type == 'Business')
                            <span>Hi, {{$recordInfo->gender}}  @include('elements.business_short_name')</span>
                            @elseif($userHInfo->user_type == 'Agent' && $userHInfo->first_name != "")
                            <span>Hi, {{$recordInfo->gender}}  @include('elements.personal_short_name')</span>
                            @elseif($userHInfo->user_type == 'Agent' && $userHInfo->business_name != "")
                            <span>Hi, {{$recordInfo->gender}}  @include('elements.business_short_name')</span>
                            @endif
                        </a>
                        <?php if($userHInfo->is_kyc_done=='1') { ?>
                         <div class="kyc-icon-box">
                            {{HTML::image('public/img/kyc-icon.svg', SITE_TITLE)}}
                        </div>
                        <?php } ?>
                    </div>

                    <a href="{{URL::to('auth/notifications')}}">{{HTML::image('public/img/front/bell.svg', SITE_TITLE)}}</a>
                
                </div>

                <?php if($userHInfo->is_kyc_done=='0') { ?>
                <div class="kyc-button">
                    <a href="{{URL::to('auth/compliance')}}" class="btn sub-btn mb-0">Upload KYC</a>
                </div>
                <?php }elseif($userHInfo->is_kyc_done=='2') { ?>
                <div class="kyc-button">
                <a href="{{URL::to('auth/compliance')}}" class="btn sub-btn mb-0">Re-submit KYC</a>
                </div>
                <?php } ?>
                

                </div>
            </div>
        </div>
        <div class="row custom-kyc-field">
            <div class="col-lg-12">
                <div class="kyc-inner-parent">
                    
                </div>
            </div>
        </div>

    </div>
</div>