<!-- Sidebar -->
<!-- <div class="slide-nav" id="sidebar-wrapper">
    <div class="sidebar-heading">
        <a href="{{URL::to('overview')}}"> {{HTML::image(BLACK_LOGO_PATH, SITE_TITLE)}}</a>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{URL::to('overview')}}" class=""><i>{{HTML::image('public/img/front/overview.svg', SITE_TITLE)}}</i>Overview</a>
        <a href="{{URL::to('auth/account-detail')}}" class=""><i>{{HTML::image('public/img/front/profile.svg', SITE_TITLE)}}</i>Profile</a>
        <a href="{{URL::to('auth/fund-transfer')}}" class=""><i>{{HTML::image('public/img/front/Fundtransfer.svg', SITE_TITLE)}}</i>Fund transfer</a>
      
        <a href="{{URL::to('auth/add-fund')}}" class=""><i>{{HTML::image('public/img/front/Deposit.svg', SITE_TITLE)}}</i>Deposit </a>
        <a href="{{URL::to('auth/withdraw-request')}}" class=""><i>{{HTML::image('public/img/front/withdraw-side.svg', SITE_TITLE)}}</i>Withdrawal </a>
        <a href="{{URL::to('auth/comming-soon')}}" class=""><i>{{HTML::image('public/img/front/withdraw-side.svg', SITE_TITLE)}}</i>Compliance </a>
		
		
       





     
    </div>
    <div class="log-btn">
    
     <a href="{{URL::to('logout')}}" class=""><i>{{HTML::image('public/img/front/logout.svg', SITE_TITLE)}}</i>Log out</a>
    </div>
</div> -->

<?php $userHInfo = $recordInfo = DB::table('users')->where('id', session()->get('user_id'))->first(); ?>
@if($userHInfo->user_type == 'Personal')
@php $name  = strtoupper($userHInfo->first_name.' '.$userHInfo->last_name)@endphp
@elseif($userHInfo->user_type == 'Business')
@php $name  = strtoupper($userHInfo->business_name)@endphp
@elseif($userHInfo->user_type == 'Agent' && $userHInfo->first_name != "")
@php $name  = strtoupper($userHInfo->first_name.' '.$userHInfo->last_name)@endphp
@elseif($userHInfo->user_type == 'Agent' && $userHInfo->business_name != "")
@php $name  = strtoupper($userHInfo->business_name)@endphp
@endif

    <?php
    function encstring($simple_string)
    {
      $token =$simple_string;
      // $cipher_method = 'aes-128-ctr';
      // $enc_key = openssl_digest(php_uname(), 'SHA256', TRUE);
      // $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
      // $crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
      $encrypted = Crypt::encryptString($token);
      return $encrypted;
    }
    
    ?>




<div class="slide-nav customScrollbar" id="sidebar-wrapper">
    <div class="scroll-wrapper customScrollbar-content">
            <div class="sidebar-heading"><a href="{{URL::to('/overview')}}">{{HTML::image('public/img/front/dafribank-logo-white.svg', SITE_TITLE)}}</a></div>
            <a class="nav-profile" href="{{URL::to('auth/account-detail')}}">
                <span class="letter">{{substr($name,0,1)}}</span>
                <span class="nav-text">
                    <label>Profile</label>
                    <strong>{{$recordInfo->gender}} @if($recordInfo->user_type == 'Personal')
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Business')
                                @include('elements.business_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                                @include('elements.business_short_name')
                                @endif</strong>
                </span>
            </a>
            <div class="list-group list-group-flush">
                <a href="{{URL::to('overview')}}"><span class="icon"><i class="fas fa-home"></i></span> <span class="nav-text">Home</span></a>
                <a href="{{URL::to('overview')}}"><span class="icon"><i class="fas fa-flag"></i></span> <span class="nav-text">Overview</span></a>

                <a href="{{URL::to('auth/add-fund')}}"><span class="icon"><i class="fas fa-wallet"></i></span> <span class="nav-text">eDeposit</span></a>

                <a href="{{URL::to('auth/dafri-me')}}"><span class="icon">{{HTML::image('public/img/front/money-bill-1-regular.svg', SITE_TITLE)}}</span> <span class="nav-text">ePay Me</span></a>

                <a href="{{URL::to('auth/fund-transfer')}}"><span class="icon"><i class="fas fa-hand-holding-usd"></i></span> <span class="nav-text">Pay Someone</span></a>

                <a href="{{URL::to('auth/withdraw-request')}}"><span class="icon"><i class="fas fa-handshake"></i></span> <span class="nav-text">Bank Transfer</span></a>

                <a href="{{URL::to('auth/global-withdraw')}}"><span class="icon">{{HTML::image('public/img/front/money-check-dollar-solid.svg', SITE_TITLE)}}</span> <span class="nav-text">Global Pay</span></a>

                <a href="{{URL::to('auth/party-withdraw')}}"><span class="icon"><i class="fas fa-money-bill-alt"></i></span> <span class="nav-text">3rd Party Pay </span></a>

                <a href="{{URL::to('auth/merchants-dafri-me')}}"><span class="icon">{{HTML::image('public/img/front/money-bill-wave-solid.svg', SITE_TITLE)}}</span> <span class="nav-text">ePay4 Business</span></a>
                
                <a href="{{URL::to('auth/transactions')}}"><span class="icon">{{HTML::image('public/img/front/list-check-solid.svg', SITE_TITLE)}}</span> <span class="nav-text">Payment History</span></a>
                
                <a href="{{URL::to('auth/airtime')}}"><span class="icon"><i class="fas fa-mobile-alt"></i></span> <span class="nav-text"> Airtime TopUp</span></a>

                <a href="{{URL::to('auth/airtime_giftcard')}}" class="new-giftcard"><span class="icon"><i class="fa fa-gift"></i></span> <span class="nav-text"> GiftCard</span><span class="addnewgiftcar">new</span> </a>

                <a href="{{DBA_WEBSITE}}/autologin?enctype={{ encstring($userHInfo->id)  }}&api_token=token&action=overview" target="_blank"><span class="icon dafri-dba-logo"><img src="{{HTTP_PATH}}/public/img/front/dba-white.svg" alt="DafriBank - Digital Bank of Africa"></span> <span class="nav-text"> eSavings</span></a>

                <a href="{{URL::to('auth/agent-list')}}"><span class="icon"><i class="far fa-user"></i></span> <span class="nav-text"> Bank Agents</span></a>
                
                <a href="{{URL::to('auth/private-banking')}}"><span class="icon"><i class="fas fa-university"></i></span> <span class="nav-text">Private Banking</span></a>
                <a href="{{URL::to('auth/exchange')}}"><span class="icon"><i class="fas fa-exchange-alt"></i></span> <span class="nav-text">Exchange</span></a>
                <a href="{{URL::to('auth/compliance')}}"><span class="icon"><i class="fas fa-clipboard-list"></i></span> <span class="nav-text">Compliance</span></a>

                <a href="{{URL::to('auth/affiliate-program')}}"><span class="icon"><i class="fas fa-bullhorn"></i></span> <span class="nav-text">Affiliate</span></a>

              {{--  @if($recordInfo->user_type != 'Agent')
                <a href="{{URL::to('auth/become-bank-agent')}}"><span class="icon"><i class="fas fa-bullhorn"></i></span> <span class="nav-text">Add Agent</span></a>
                @else
                <a href="{{URL::to('auth/agent-withdraw-request-list')}}"><span class="icon"><i class="fas fa-bullhorn"></i></span> <span class="nav-text">Withdraw Request's</span></a>
                @endif --}}
                <a href="javascript:void(0);" onclick="openLogout();"><span class="icon"><i class="fas fa-power-off red-text"></i></span> <span class="nav-text">Log Out</span></a>
            </div>
            </div>
        </div>
<script>
function openLogout(){
$('#logout-Modal').modal('show');
}
</script>

{{ HTML::script('public/assets/js/slimscroll.js')}}
 <script>
         function customScollbar() {
  const isTouch = ('ontouchstart' in document.documentElement);
  const scrollers = document.querySelectorAll('.customScrollbar');
  scrollers.forEach(scroller => {
    const content = scroller.querySelector('.customScrollbar-content');
    const scrollbar = scroller.querySelector('.customScrollbar-scrollbar');
    let ratio;
    let posY = 1;
    let offset = 1;
    let scrollbar_top = parseInt(getComputedStyle(scrollbar).top);

    if (isTouch) {
      scrollbar.style.pointerEvents = 'none';
      scroller.ontouchstart = () => scroller.classList.add('onscroll');
      scroller.ontouchend = () => scroller.classList.remove('onscroll');
    }
    const resize = () => {
      ratio = content.clientHeight / content.scrollHeight;
      scrollbar.style.height = `${content.clientHeight * ratio - 2 * scrollbar_top}px`;
    };
    const mouseMove = e => {
      posY = e.clientY - offset;
      content.scrollTo(0, posY / ratio);
    };
    const mouseUp = () => {
      scroller.classList.remove('onscroll');
      window.removeEventListener('mousemove', mouseMove);
      window.removeEventListener('mouseup', mouseUp);
    };
    const mouseDown = val => {
      scroller.classList.add('onscroll');
      offset = val - posY;
      window.addEventListener('mousemove', mouseMove);
      window.addEventListener('mouseup', mouseUp);
      return false;
    };
    scrollbar.onmousedown = e => mouseDown(e.clientY);
    content.onscroll = () => {
      posY = content.scrollTop * ratio;
      scrollbar.style.top = `${posY + scrollbar_top}px`;
    };

    window.addEventListener('resize', resize, { passive: true });
    resize();
  });
}

customScollbar();
        </script>
<!-- /#sidebar-wrapper -->