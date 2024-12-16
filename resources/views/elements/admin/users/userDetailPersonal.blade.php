{{ HTML::script('public/assets/js/facebox.js')}}
{{ HTML::style('public/assets/css/facebox.css')}}
<script type="text/javascript">
    $(document).ready(function ($) {
        $('.close_image').hide();
        $('a[rel*=facebox]').facebox({
            closeImage: '{!! HTTP_PATH !!}/public/img/close.png'
        });

        $('.dropdown-menu a').on('click', function (event) {
            $(this).parent().parent().parent().toggleClass('open');
        });
    });
</script>
<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if(!$allrecords->isEmpty())

<div class="panel-body marginzero" style="padding-bottom: 100px">
    <!-- <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div> -->
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Personal Users List</div>
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

                                    <li><a href="{{ URL::to( 'admin/users/transaction-list/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                    <li><a href="{{ URL::to( 'admin/users/dba-transaction-list/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>

                                    <li><a href="{{ URL::to( 'admin/users/wallet_summary/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA eCash </a></li>

                                    <li><a href="{{ URL::to( 'admin/users/ref-detail/'.$allrecord->id)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Affiliated Accounts</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        
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