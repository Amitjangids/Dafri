<header class="main-header">
    <a href="{{URL::to( 'admin/admins/dashboard')}}" class="logo">
        <span class="logo-mini"><b>{{HTML::image('public/img/dafribank-logo-white.svg', SITE_TITLE,['style'=>"max-width: 20px"])}}</b></span>
        <span class="logo-lg">{{HTML::image('public/img/dafribank-logo-white.svg', SITE_TITLE)}}</span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="javascript:void(0);" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="">
                    <a href="{{URL::to( 'admin/admins/dashboard')}}">
                        <span class="hidden-xs">{{ucwords(Session::get('admin_username'))}}</span>
                    </a>
                </li>
                <li><a href="#" data-toggle="modal" data-target="#exampleModal"  class=""><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Modal -->
<div class="modal x-dialog  fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="basicModal2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content headmodel">
            <div class="modal-body ">
                <button type="button"  class="close" data-dismiss="modal"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{HTML::image('public/img/front/dafribank-logo-black.svg', SITE_TITLE)}}
                <br><br>
                <p>Are you sure you want to logout?</p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><a type="button" href="{{URL::to( 'admin/admins/logout')}}" id="myButton" class="btn btn-dark" onclick="">Confirm</a></li>
                    <li class="list-inline-item"><button type="button"  class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>


  