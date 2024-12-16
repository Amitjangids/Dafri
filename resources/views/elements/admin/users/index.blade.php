{{ HTML::script('public/assets/js/facebox.js')}}
{{ HTML::style('public/assets/css/facebox.css')}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script type="text/javascript">
    $(document).ready(function ($) {
        $('.close_image').hide();
        $('a[rel*=facebox]').facebox({
            closeImage: '{!! HTTP_PATH !!}/public/img/close.png'
        });
        
        $(document).bind('reveal.facebox', function() { $('.date_picker_range').daterangepicker({
            maxDate: new Date(),
            locale: {
                format: 'YYYY-MM-DD'
            }
            }); });

      //$('.dropdown-menu a').on('click', function (event) {
        // $(this).parent().parent().parent().parent().toggleClass('open');
     // });
     
    });
</script>
<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if(!$allrecords->isEmpty())

<div class="panel-body marginzero" style="padding-bottom: 100px">
    <!-- <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div> -->
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Personal Users List [ Total Users : {{$users_count}} ]</div>
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$allrecords->appends(Request::except('_token'))->render()}}
                </div>
            </div>                
        </div>
        <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th style="width:5%">#</th>
                        <th class="sorting_paging">@sortablelink('first_name', 'Name')</th>
                        <th class="sorting_paging">Account Number</th>
                        <th class="sorting_paging">@sortablelink('email', 'Email Address')</th>
                        <th class="sorting_paging">@sortablelink('phone', 'Phone')</th>
                        <th class="sorting_paging">@sortablelink('is_verify', 'Status')</th>
                        <th class="sorting_paging">KYC Status</th>
                        <th class="sorting_paging">Edited By</th>
                        <th class="sorting_paging">@sortablelink('created_at', 'Date')</th>
                        <th class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allrecords as $allrecord)
                    @php
                    if ($allrecord->first_name == "")
                    continue;
                    @endphp
                    <tr>
                        <th style="width:5%"><input type="checkbox" onclick="javascript:isAllSelect(this.form);" name="chkRecordId[]" value="{{$allrecord->id}}" /></th>
                        <td data-title="Name"><a href="#info{!! $allrecord->id !!}" title="View User Detail" class="" rel='facebox'>{{strtoupper($allrecord->first_name)}}</a></td>
                        <td data-title="Name">{{$allrecord->account_number}}</td>
                        <td data-title="Email Address">{{$allrecord->email?$allrecord->email:'N/A'}}</td>
                        <td data-title="Contact Number">{{$allrecord->phone}}</td>
                        <td data-title="Status" id="verify_{{$allrecord->slug}}">
                            @if($allrecord->is_verify == 1)
                            Activated
                            @else
                            Deactivated
                            @endif
                        </td>
                        <td data-title="KYC Status">
                            @if($allrecord->is_kyc_done == 1)
                            Approved
                            @elseif($allrecord->is_kyc_done == 2)
                            Declined
                            @else
                            @if($allrecord->identity_image != '')
                            Pending
                            @else
                            Not Submitted
                            @endif
                            @endif
                        </td>
                        <td data-title="Edited By">
                            @php
                            if ($allrecord->edited_by != '-1') {
                            $edited_by = getAdminNameById($allrecord->edited_by);
                            }
                            else {
                            $edited_by = 'N/A'; 
                            }
                            @endphp
                            {{ $edited_by }}
                        </td>
                        <td data-title="Date">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Action">
                            <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>


                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                @php
                                $flag = validatePermission(Session::get('admin_role'),'edit-personal-user');
                                @endphp
                                <ul class="dropdown-menu pull-right">
                                    @if($flag == true)
                                    <li class="right_acdc" id="status{{$allrecord->id}}">
                                        @if($allrecord->is_verify == '1')
                                        <a href="{{ URL::to( 'admin/users/deactivate/'.$allrecord->slug)}}" title="Deactivate User" class="deactivate"><i class="fa fa-check"></i>Deactivate User</a>
                                        @else
                                        <a href="{{ URL::to( 'admin/users/activate/'.$allrecord->slug)}}" title="Activate User" class="activate"><i class="fa fa-ban"></i>Activate User</a>
                                        @endif
                                    </li>
                                    <li><a href="{{ URL::to( 'admin/users/edit/'.$allrecord->slug)}}" title="Edit" class=""><i class="fa fa-pencil"></i>Edit User</a></li>
                                    @endif                                   

                                    <li><a href="#info{!! $allrecord->id !!}" title="View User Detail" class="" rel='facebox'><i class="fa fa-eye"></i>View User Detail</a></li>

                                    @if($flag == true)
                                    <li><a href="{{ URL::to( 'admin/users/kycdetail/'.$allrecord->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                    @php
                                    $isAgent = isAgent($allrecord->id);
                                    @endphp
                                    @if($isAgent == false)
                                    <li><a href="{{ URL::to( 'admin/users/admin-agent-request/'.$allrecord->id)}}" title="Generate Agent Request" class=""><i class="fa fa-file"></i>Generate Agent Request</a></li>
                                    @else
                                    <li><a href="{{ URL::to( 'admin/users/set-agent-trans-limit/'.$allrecord->id)}}" title="Generate Agent Request" class=""><i class="fa fa-file"></i>Set Agent Limit</a></li>
                                    @endif

                                    @endif

                                    <li><a href="{{ URL::to( 'admin/users/adjust-client-wallet/'.$allrecord->id)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Pay Client Fiat</a></li>

                                    <li><a href="{{ URL::to( 'admin/users/adjust-dba-wallet/'.$allrecord->id)}}" title="Adjust DBA Client Balance" class=""><i class="fa fa-file"></i>Pay Client DBA</a></li>

                                    <li><a href="{{ URL::to( 'admin/users/adjust-client-fix-wallet/'.$allrecord->id)}}" title="Pay Client Fiat (Zero Fee)" class=""><i class="fa fa-file"></i>Pay Client Fiat (Zero Fee)</a></li>

                                    <li><a href="{{ URL::to( 'admin/users/adjust-client-dba-fix-wallet/'.$allrecord->id)}}" title="Pay Client DBA (Zero Fee)" class=""><i class="fa fa-file"></i>Pay Client DBA (Zero Fee)</a></li>
                                   <!--- Pending Request Add By Sushil 28 -04-2022-->
                                 <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-file"></i>Pending Request <i class="fa fa-caret-right" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                     <li><a  onclick="event.stopPropagation();" href="{{ URL::to( 'admin/users/manual-deposit-requestlist/'.$allrecord->slug)}}" title="Deposit Request (Fiat)" class="dropdown-item"><i class="fa fa-file"></i>Deposit Request (Fiat) </a></li>
                                        <li><a onclick="event.stopPropagation();" href="{{ URL::to( 'admin/users/manual-withdraw-requestlist/'.$allrecord->slug)}}" title="Withdraw Request (Fiat)" class="dropdown-item"><i class="fa fa-file"></i>Withdraw Request (Fiat) </a></li>
                                        <li><a onclick="event.stopPropagation();" href="{{ URL::to( 'admin/users/crypto-deposit-requestlist/'.$allrecord->slug)}}" title="Deposit Request (Crypto)" class="dropdown-item"><i class="fa fa-file"></i>Deposit Request (Crypto) </a></li>
                                        <li><a onclick="event.stopPropagation();" href="{{ URL::to( 'admin/users/crypto-withdraw-requestlist/'.$allrecord->slug)}}" title="Withdraw Request ( Crypto)" class="dropdown-item"><i class="fa fa-file"></i>Withdraw Request ( Crypto) </a></li>
                                    </ul>
                                  </li>

                            <!--- Pending Request Add By Sushil 28 -04-2022-->

                                    <li><a href="{{ URL::to( 'admin/users/manage-limit/'.$allrecord->slug)}}" title="Manage Transaction Limit" class=""><i class="fa fa-file"></i>Configure Transaction's Limit</a></li>
 
                                    <li><a href="{{ URL::to( 'admin/users/transaction-list/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                    <li><a href="{{ URL::to( 'admin/users/dba-transaction-list/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>

                                    <li><a href="{{ URL::to( 'admin/users/wallet_summary/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA eCash </a></li>

                                    <li><a href="{{ URL::to( 'admin/users/ref-detail/'.$allrecord->id)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Affiliated Accounts</a></li>

                                    <li><a href="#basicModalStatement{!! $allrecord->id !!}" title="DownLoad Statement" class="" rel='facebox'><i class="fa fa-eye"></i>DownLoad Statement</a></li>
                                    

                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="search_frm" style="margin-bottom:40px;">
                <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-info">Select All</button>
                <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-info">Unselect All</button>
                <?php
                $accountStatus = array(
                    'Verify' => "Verify User",
                    'Unverify' => "Unverify User",
                    // 'Delete' => "Delete",
                );
                ;
                ?>
                <div class="list_sel">{{Form::select('action', $accountStatus,null, ['class' => 'small form-control','placeholder' => 'Action for selected record', 'id' => 'action'])}}</div>
                <button type="submit" class="small btn btn-success btn-cons btn-info" onclick="return ajaxActionFunction();" id="submit_action">OK</button>
            </div>    
        </div>

        <div class="topn">
            <!--   <div class="topn_left">Personal Users List</div> -->
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$allrecords->appends(Request::except('_token'))->render()}}
                </div>
            </div> 

        </div>
    </section>
    {{ Form::close()}}
</div>         
</div> 
@else 
<div id="listingJS" style="display: none;" class="alert alert-success alert-block fade in"></div>
<div class="admin_no_record">No record found.</div>
@endif

@if(!$allrecords->isEmpty())
@foreach($allrecords as $allrecord)

<div id="basicModalStatement{{$allrecord->id}}" style="display: none;">
<div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <div class="drt">
            {{ Form::open(array('method' => 'post', 'name' =>'downlodStatemnt', 'id' => 'downlodStatemnt', 'class' => 'row border-form','[formGroup]'=>'formGroup','url'=>'/admin/users/download_statement/'.$allrecord->id)) }}
                <div class="download-statment">
                    <h6>Download statement</h6>
                    <p>For which period do you need a statement</p>
                    <div class="stat-opt">
                        <div class="text-field-filter">
                            <div class="radio-card">
                                <input id="radio-4" name="perdStatmnt" value="last_month" type="radio">
                                <label for="radio-4" class="radio-label">Last month</label>
                            </div>
                            <div class="radio-card">
                                <input id="radio-5" name="perdStatmnt" value="last_3_month" type="radio">
                                <label for="radio-5" class="radio-label">Last  3 months</label>
                            </div>
                            <div class="radio-card">
                                <input id="radio-6" name="perdStatmnt" value="last_6_month" type="radio">
                                <label for="radio-6" class="radio-label">Last 6 months</label>
                            </div>
                            <div class="radio-card">
                                <input id="radio-7" name="perdStatmnt" value="last_1_yr" type="radio">
                                <label for="radio-7" class="radio-label">Last  1 year</label>
                            </div>
                        </div>
                        <p>or select a custom date range </p>

                        <div class="text-field-filter col-sm-7 fs m-auto">
                            <div class="main-calaner-icon">
                                <input type="text" name="statement_date" class="date_picker_range" id="statement_date-{{$allrecord->id}}" placeholder="Date From - To" autocomplete="off">
                            </div>
                            {{HTML::image('public/img/front/calender.svg', SITE_TITLE)}}
                        </div>
                        <div class="btn-pro">
                            <input type="hidden" name="dwnldStatmnt" value="true">
                            <button class="sub-btn" type="submit">Proceed</button>
                        </div>
                        <!-- <p class="text-left">Your statement will be sent to your registered email.</p> -->
                    </div>
                </div>

                {{ Form::close() }}
            </div>
      </fieldset>
    </div>
    </div>

<div id="info{!! $allrecord->id !!}" style="display: none;">
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($allrecord->first_name.' '.$allrecord->last_name) !!}</legend>
            <div class="drt">
                <div class="admin_pop"><span>User Type: </span>  <label>@isset($allrecord->user_type) {{$allrecord->user_type}} @endisset</label></div>
                <div class="admin_pop"><span>Email: </span>  <label>@isset($allrecord->email) {{$allrecord->email}} @endisset</label></div>
                <div class="admin_pop"><span>First Name: </span>  <label>{!! strtoupper($allrecord->first_name) !!}</label></div>
                <div class="admin_pop"><span>Last Name: </span>  <label>{!! strtoupper($allrecord->last_name) !!}</label></div>
                <div class="admin_pop"><span>Phone Number: </span>  <label>{!! $allrecord->phone !!}</label></div>

                <div class="admin_pop"><span>Account Number: </span>  <label>{!! $allrecord->account_number !!}</label></div>
                <div class="admin_pop"><span>Wallet Type: </span>  <label>{!! $allrecord->account_category !!}</label></div>
                <div class="admin_pop"><span>Account Balance: </span>  <label>{!! $allrecord->currency.' '.number_format($allrecord->wallet_amount ,10,'.',',') !!}</label></div>

                <div class="admin_pop"><span>Interest Wallet: </span>  <label>{!! $allrecord->dba_currency.' '.number_format(($allrecord->dba_wallet_amount+$allrecord->dba_hold_wallet_amount ) ,10,'.',',') !!}</label></div>
                <div class="admin_pop"><span>DBA Wallet Available: </span>  <label>{!! $allrecord->dba_currency.' '.number_format($allrecord->dba_wallet_amount ,10,'.',',') !!}</label></div>
                <div class="admin_pop"><span>DBA Wallet Hold: </span>  <label>{!! $allrecord->dba_currency.' '.number_format($allrecord->dba_hold_wallet_amount,10,'.',',') !!}</label></div>


                
                <div class="admin_pop"><span>Affiliate Balance: </span>  <label>
                        @php
                        $res = getAffiliateBalanceByUserId($allrecord->id);
                        @endphp
                        @if($res)USD {{$res}}@else USD 0.00 @endif
                    </label></div>
                <div class="admin_pop"><span>Country: </span>  <label>{{$allrecord->country?$allrecord->country:'N/A'}}</label></div>
                <!--<div class="admin_pop"><span>Currency: </span>  <label>{{$allrecord->currency?$allrecord->currency:'N/A'}}</label></div>-->
                <div class="admin_pop"><span>Address Line1: </span>  <label>{{$allrecord->addrs_line1}}</label></div>
                <div class="admin_pop"><span>Address Line2: </span>  <label>{{$allrecord->addrs_line2}}</label></div>


                <div class="admin_pop"><span>Identity Card Type: </span>  <label>{!! $allrecord->identity_card_type !!}</label></div>

                <div class="admin_pop"><span>National Identity Number: </span>  <label>{{$allrecord->national_identity_number?$allrecord->national_identity_number:'N/A'}}</label></div>

                @if($allrecord->identity_card_type == 'Passport')
                @php
                $ext = pathinfo($allrecord->identity_image, PATHINFO_EXTENSION);
                @endphp
                <div class="admin_pop"><span>Identity document</span> <label>
                        @if($allrecord->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image}}" type="application/pdf" width="420" height="350">
                                <a href="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image}}">{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image}}</a>
                            </object></div>

                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </label>
                </div>
                
                @else
                
                @php
                $ext = pathinfo($allrecord->identity_image, PATHINFO_EXTENSION);
                @endphp
                <div class="admin_pop"><span>Identity document front</span> <label>
                        @if($allrecord->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image}}" type="application/pdf" width="420" height="350">
                                <a href="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image}}">{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image}}</a>
                            </object></div>

                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </label>
                </div>
                
                @php
                $ext = pathinfo($allrecord->identity_image_back, PATHINFO_EXTENSION);
                @endphp
                <div class="admin_pop"><span>Identity document back</span> <label>
                        @if($allrecord->identity_image_back == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image_back}}" type="application/pdf" width="420" height="350">
                                <a href="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image_back}}">{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image_back}}</a>
                            </object></div>

                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity_image_back, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </label>
                </div>
                
                @endif

                <div class="admin_pop"><span>Proof Of Address Type: </span>  <label>{!! $allrecord->address_proof_type !!}</label></div>
                <div class="admin_pop"><span>Proof Of Address Document Number: </span>  <label>{{$allrecord->address_proof_number?$allrecord->address_proof_number:'N/A'}}</label></div>
                @if($allrecord->address_document != '')
                <div class="admin_pop"><span>Proof Of Address Document</span> <label>
                        @php
                        $ext = pathinfo($allrecord->address_document, PATHINFO_EXTENSION);
                        @endphp
                        @if($allrecord->address_document == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->address_document}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->address_document}}">{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->identity_image}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->address_document, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif   </label></div>
                @endif

                @if($allrecord->profile_image != '')
                <div class="admin_pop"><span>Selfie</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->profile_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif

                @if($allrecord->image != '')
                <div class="admin_pop"><span>Profile Image</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif
        </fieldset>
    </div>
</div>


@endforeach
@endif

<style>
    .dropdown-submenu {
    position:relative;
}
.dropdown-submenu>.dropdown-menu {
    top:0;
    left:-10rem; /* 10rem is the min-width of dropdown-menu */
    margin-top:-6px;
}

/* rotate caret on hover */
.dropdown-menu > li > a:hover:after {
    text-decoration: underline;
    transform: rotate(-90deg);
} 
.btn-group.open .dropdown-toggle{box-shadow: none !important;}
@media only screen and (max-width: 767px) {
    .dropdown-menu-right {left: 0 !important; top: 25px !important;}
}
.dropdown-menu-right {right: inherit !important;left: -107%;top: 0;}
li.nav-item.dropdown a#navbarDropdownMenuLink i.fa.fa-caret-right {right: 0;position: absolute;top: 50%;transform: translateY(-50%);}

</style>

<script>
$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
    if (!$(this).next().hasClass('show')) {
        $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    var $subMenu = $(this).next(".dropdown-menu");
    $subMenu.toggleClass('show');
    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
        $('.dropdown-submenu .show').removeClass("show");
    });
    return false;
});
</script>


<style>
fieldset.nzwh .drt form#downlodStatemnt .download-statment h6 {font-size: 22px;color: #000;font-weight: 600;text-align: center;line-height: 1.4;margin-top: 0;margin-bottom: 0;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment p {font-size: 18px;font-weight: 400;color: #000;line-height: 1.6;text-align: center;margin-top: 0;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .stat-opt .text-field-filter {display: flex;align-items: center;justify-content: space-between;flex-wrap: wrap;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .stat-opt .text-field-filter .radio-card {width: 50%;flex: 0 0 50%; padding: 10px;padding-left: 80px;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .stat-opt .text-field-filter .radio-card label.radio-label {font-size: 18px;font-weight: 500;color: #000;vertical-align: middle;line-height: 1.6;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .stat-opt {margin-top: 40px;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .text-field-filter.col-sm-7.fs.m-auto {width: 100%;text-align: center;border: none;display: block !important;margin-bottom: 60px;border-radius: 10px;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .text-field-filter.col-sm-7.fs.m-auto input{border: none;padding: 10px;font-size: 18px;color: #000;position: relative;font-weight: 500;background: #eee;border-radius: 10px;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .stat-opt .text-field-filter .main-calaner-icon{position: relative;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .stat-opt .text-field-filter img {position: absolute;top: 50%;right: 190px;transform: translateY(-50%);}
div#facebox fieldset.nzwh {overflow: hidden;border-radius: 10px;}
.btn-pro button.sub-btn {min-width: 120px;background: #000;border-radius: 10px;border: 1px solid transparent;color: #fff;padding: 8px 15px;font-size: 18px;transition: 0.4s;-webkit-transition: 0.4s;margin-bottom: 10px;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .btn-pro button.sub-btn:hover{background: transparent; color: #000; border-color: #000;}
fieldset.nzwh .drt form#downlodStatemnt .download-statment .btn-pro {text-align: center;}
.daterangepicker.ltr.show-calendar.opensright {z-index: 9999999;}

 @media only screen and (max-width: 767px) {
    .open>.dropdown-menu {display: block;height: 300px;overflow-y: scroll;}
    li.nav-item.dropdown ul.dropdown-menu.dropdown-menu-right.show{height: auto !important; overflow-y: auto !important;}}
}

</style>










