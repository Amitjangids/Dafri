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
    <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Business Users List</div>
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
                        <th class="sorting_paging">@sortablelink('business_name', 'Business Name')</th>
                        <th class="sorting_paging">@sortablelink('director_name', 'Director Name')</th>
                        <th class="sorting_paging">Account Number</th>
                        <th class="sorting_paging">User ID</th>
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
					  if ($allrecord->first_name != "")
					   continue;
					  else if ($allrecord->id == 0)
						continue;	
					 @endphp
                    <tr>
                        <th style="width:5%"><input type="checkbox" onclick="javascript:isAllSelect(this.form);" name="chkRecordId[]" value="{{$allrecord->id}}" /></th>
                        <td data-title="Business Name">{{ucwords($allrecord->business_name)}}</td>
                        <td data-title="Director Name">{{ucwords($allrecord->director_name)}}</td>
						<td data-title="Director Name">{{$allrecord->account_number}}</td>
						<td data-title="Director Name">{{$allrecord->id}}</td>
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
                            N/A
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
								$flag = validatePermission(Session::get('admin_role'),'edit-business-user');
								@endphp
                                <ul class="dropdown-menu pull-right">
								@if($flag == true)
                                    <li class="right_acdc" id="status{{$allrecord->id}}">
                                        @if($allrecord->is_verify == '1')
                                        <a href="{{ URL::to( 'admin/merchants/deactivate/'.$allrecord->slug)}}" title="Deactivate User" class="deactivate"><i class="fa fa-check"></i>Deactivate User</a>
                                        @else
                                        <a href="{{ URL::to( 'admin/merchants/activate/'.$allrecord->slug)}}" title="Activate User" class="activate"><i class="fa fa-ban"></i>Activate User</a>
                                        @endif
                                    </li>
                                    <li><a href="{{ URL::to( 'admin/merchants/edit/'.$allrecord->slug)}}" title="Edit" class=""><i class="fa fa-pencil"></i>Edit User</a></li>
                                   @endif
                                    
                                    <li><a href="#info{!! $allrecord->id !!}" title="View User Detail" class="" rel='facebox'><i class="fa fa-eye"></i>View User Detail</a></li>
									
									@if($flag == true)
                                    <li><a href="{{ URL::to( 'admin/merchants/kycdetail/'.$allrecord->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>
									
									@php
									$isAgent = isAgent($allrecord->id);
									@endphp
									@if($isAgent == false)
									<li><a href="{{ URL::to( 'admin/merchants/agentRequest/'.$allrecord->id)}}" title="Generate Agent Request" class=""><i class="fa fa-file"></i>Generate Agent Request</a></li>
									@else
									<li><a href="{{ URL::to( 'admin/users/set-agent-trans-limit/'.$allrecord->id)}}" title="Generate Agent Request" class=""><i class="fa fa-file"></i>Set Agent Limit</a></li>										
									@endif
									@endif
									
									<li><a href="{{ URL::to( 'admin/users/adjust-client-wallet/'.$allrecord->id)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Pay Client</a></li>
                                                                        <li><a href="{{ URL::to( 'admin/users/transaction-list/'.$allrecord->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="search_frm">
                <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-info">Select All</button>
                <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-info">Unselect All</button>
                <?php $accountStatus = array(
    'Verify' => "Verify User",
    'Unverify' => "Unverify User",
    'Delete' => "Delete",
);; ?>
                <div class="list_sel">{{Form::select('action', $accountStatus,null, ['class' => 'small form-control','placeholder' => 'Action for selected record', 'id' => 'action'])}}</div>
                <button type="submit" class="small btn btn-success btn-cons btn-info" onclick="return ajaxActionFunction();" id="submit_action">OK</button>
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
            <legend class="head_pop">{!! $allrecord->business_name !!}</legend>
            <div class="drt">
                <div class="admin_pop"><span>User Type: </span>  <label>@isset($allrecord->user_type) {{$allrecord->user_type}} @endisset</label></div>
                <div class="admin_pop"><span>Business Name: </span>  <label>{!! ucwords($allrecord->business_name) !!}</label></div>
                <div class="admin_pop"><span>Director Name: </span>  <label>{!! ucwords($allrecord->director_name) !!}</label></div>
                <div class="admin_pop"><span>Business Email: </span>  <label>{{$allrecord->email?$allrecord->email:'N/A'}}</label></div>
               <div class="admin_pop"><span>Account Number: </span>  <label>{!! $allrecord->account_number !!}</label></div>
                <div class="admin_pop"><span>Account Balance: </span>  <label>{!! $allrecord->wallet_amount  !!}</label></div>
                <div class="admin_pop"><span>Affiliate Balance: </span>  <label>
                    @php
                        $res = getAffiliateBalanceByUserId($allrecord->id);
                    @endphp
                    @if($res)USD {{$res}}@else USD 0.00 @endif
                    </label>
                </div>

                <div class="admin_pop"><span>Phone Number: </span>  <label>{!! $allrecord->phone !!}</label></div>
                <div class="admin_pop"><span>Country: </span>  <label>{{$allrecord->country?$allrecord->country:'N/A'}}</label></div>
                <div class="admin_pop"><span>Currency: </span>  <label>{{$allrecord->currency?$allrecord->currency:'N/A'}}</label></div>

                <div class="admin_pop"><span>Business Type: </span>  <label>{!! $allrecord->business_type !!}</label></div>
                <div class="admin_pop"><span>Business registration number: </span>  <label>{!! $allrecord->registration_number !!}</label></div>
                @if($allrecord->registration_document != '')
                <div class="admin_pop"><span>Business Registration Document</span> <label>{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$allrecord->registration_document, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif
                <div class="admin_pop"><span>Address Line1: </span>  <label>{{$allrecord->addrs_line1}}</label></div>
                <div class="admin_pop"><span>Address Line2: </span>  <label>{{$allrecord->addrs_line2}}</label></div>
                
                <div class="admin_pop"><span>Identity Card Type: </span>  <label>{!! $allrecord->identity_card_type !!}</label></div>
                <div class="admin_pop"><span>National Identity Number: </span>  <label>{{$allrecord->national_identity_number?$allrecord->national_identity_number:'N/A'}}</label></div>
                @if($allrecord->identity_image != '')
                <div class="admin_pop"><span>Upload identity document</span> <label>{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$allrecord->identity_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
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
                        @endif                    </label></div>
                @endif
                @if($allrecord->image != '')
                <div class="admin_pop"><span>Profile Image</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif

                
                @if($allrecord->certificate_of_incorporation != '')
                <div class="admin_pop"><span>Certificate of Incorporation</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->certificate_of_incorporation}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->certificate_of_incorporation, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->certificate_of_incorporation == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->certificate_of_incorporation, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->article != '')
                <div class="admin_pop"><span>Article</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->article}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->article, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->article == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->article, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->memorandum != '')
                <div class="admin_pop"><span>Memorandum</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->memorandum}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->memorandum, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->memorandum == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->memorandum, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->tax_certificate != '')
                <div class="admin_pop"><span>Tax Certificate</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->tax_certificate}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->tax_certificate, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->tax_certificate == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->tax_certificate, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->address_proof != '')
                <div class="admin_pop"><span>Proof of Business Address</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->address_proof}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->address_proof, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->address_proof == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->address_proof, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->identity != '')
                <div class="admin_pop"><span>Identity of all Directors</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->identity}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->identity, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->identity == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->identity, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->person_identity != '')
                <div class="admin_pop"><span>Identity of person or entity holding more than 25% stake in the company</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->person_identity}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->person_identity, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->person_identity == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->person_identity, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                


        </fieldset>
    </div>
</div>
@endforeach
@endif