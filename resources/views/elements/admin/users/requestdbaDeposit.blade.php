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
<style>
    a.copy-icon {
    margin-left: 10px;
}
    .copy-icon img {
    width: 15px;
}
</style>
<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if(!$requests->isEmpty())
<div class="panel-body marginzero">
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">DBA Deposit Request's List</div>  
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$requests->appends(Request::except('_token'))->render()}}
                </div>
            </div>                
        </div>
        <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th width="10%" class="sorting_paging">@sortablelink('user_name', 'Name')</th>
                        <th width="10%" class="sorting_paging">@sortablelink('amount', 'Amount(USD)')</th>
                        <th width="10%" class="sorting_paging">@sortablelink('dba_amount', 'DBA Amount')</th>
                        <th width="10%" class="sorting_paging">@sortablelink('crypto_currency', 'Currency')</th>
                        <th width="20%" class="sorting_paging">@sortablelink('blockchain_url', 'Blockchain URL')</th>
						<th width="10%" class="sorting_paging">@sortablelink('status','Status')</th>
                                                <th class="sorting_paging">@sortablelink('edited_by', 'Edited By')</th>
                        <th width="15%" class="sorting_paging">@sortablelink('created_at', 'Date Created')</th>
						<th width="15%" class="sorting_paging">@sortablelink('updated_at', 'Date Update')</th>
                        <th width="10%" class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i=0;
                    @endphp
                    @foreach($requests as $allrecord)
                    <tr>
                        <td data-title="Ticket ID"><a href="#ss-{{$i}}" title="View Request Detail" class="" rel='facebox'>{{strtoupper($allrecord->user_name)}}</a></td>
                        <td data-title="Support Type">{{$allrecord->amount}} {{$allrecord->req_type_currency}}</td>
						<td data-title="Support Type">{{$allrecord->dba_amount}} DBA</td>
                        <td data-title="Request Text">{{$allrecord->crypto_currency}}</td>
                        <td data-title="First name" style="word-break: break-all;"  ><a href="{{$allrecord->blockchain_url}}" target="_blank">{{$allrecord->blockchain_url}}</a><a onclick="copyTextToClipboard('{{$allrecord->blockchain_url}}');" href="#" class="copy-icon">{{HTML::image('public/img/front/copy-icon.svg', SITE_TITLE)}}</a></td>
						<td data-title="Last name">
						@if($allrecord->status == 0)
						{{'Pending'}}
					    @elseif($allrecord->status == 1)
						{{'Complete'}}
						@elseif($allrecord->status == 2)
						{{'Hold'}}
						@elseif($allrecord->status == 3)
						{{'Reject'}}
						@endif
					    </td>
                                            <td>
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
                        <td data-title="Request Creation Date">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Request Last Update Date">{{$allrecord->updated_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Action">
                            <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            
                            
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
								<!-- <li><a href="#info{!! $allrecord->id !!}" title="View Request Detail" class="" rel='facebox'><i class="fa fa-eye"></i>View Request Detail</a></li> -->
								@if($allrecord->status != 1 && $allrecord->status != 3)
								<li><a href="{{URL::to('admin/users/edit-dba-deposit-request/'.$allrecord->id.'/')}}" title="Edit Request" class=""><i class="fa fa-edit"></i>Edit</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModal{{$allrecord->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>

								<li><a href="{{URL::to('admin/users/change-dba-deposit-req-status/'.$allrecord->id.'/2')}}" title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>

								<li><a href="#" data-toggle="modal" data-target="#basicModal_reject{{$allrecord->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a href="{{ URL::to( 'admin/users/kycdetail/'.$allrecord->User->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a href="{{ URL::to( 'admin/users/transaction-list/'.$allrecord->User->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a href="{{ URL::to( 'admin/users/dba-transaction-list/'.$allrecord->User->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>

								@endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                      @php
                    $i++
                    @endphp
                    @endforeach
                </tbody>
            </table>
           <!-- <div class="search_frm">
                <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-info">Select All</button>
                <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-info">Unselect All</button>
                <?php $accountStatus = array(
				'Verify' => "Approve Request"
				); ?>
                <div class="list_sel">{{Form::select('action', $accountStatus,null, ['class' => 'small form-control','placeholder' => 'Action for selected record', 'id' => 'action'])}}</div>
                <button type="submit" class="small btn btn-success btn-cons btn-info" onclick="return ajaxActionFunction();" id="submit_action">OK</button>
            </div> -->    

            <div class="topn">
            <!--   <div class="topn_left">Personal Users List</div> -->
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$requests->appends(Request::except('_token'))->render()}}
                </div>
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
@if(!$requests->isEmpty())
@php
                    $j=0
                    @endphp
@foreach($requests as $allrecord)

<div id="ss-{{$j}}" style="display: none;">
@if($allrecord->User != '')
@if(($allrecord->User->user_type=="Personal" || $allrecord->User->user_type=="Agent") && $allrecord->User->first_name!="")
<div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($allrecord->User->first_name.' '.$allrecord->User->last_name) !!}</legend>
            <div class="drt">
                <div class="admin_pop"><span>User Type: </span>  <label>@isset($allrecord->User->user_type) {{$allrecord->User->user_type}} @endisset</label></div>
                <div class="admin_pop"><span>Email: </span>  <label>@isset($allrecord->User->email) {{$allrecord->User->email}} @endisset</label></div>
                <div class="admin_pop"><span>First Name: </span>  <label>{!! strtoupper($allrecord->User->first_name) !!}</label></div>
                <div class="admin_pop"><span>Last Name: </span>  <label>{!! strtoupper($allrecord->User->last_name) !!}</label></div>
                <div class="admin_pop"><span>Phone Number: </span>  <label>{!! $allrecord->User->phone !!}</label></div>

                <div class="admin_pop"><span>Account Number: </span>  <label>{!! $allrecord->User->account_number !!}</label></div>
                <div class="admin_pop"><span>Wallet Type: </span>  <label>{!! $allrecord->User->account_category !!}</label></div>
                <div class="admin_pop"><span>Account Balance: </span>  <label>{!! $allrecord->User->currency.' '.number_format($allrecord->User->wallet_amount ,10,'.',',') !!}</label></div>

                <div class="admin_pop"><span>Interest Wallet: </span>  <label>{!! $allrecord->User->dba_currency.' '.number_format(($allrecord->User->dba_wallet_amount+$allrecord->User->dba_hold_wallet_amount ) ,10,'.',',') !!}</label></div>
                <div class="admin_pop"><span>DBA Wallet Available: </span>  <label>{!! $allrecord->User->dba_currency.' '.number_format($allrecord->User->dba_wallet_amount ,10,'.',',') !!}</label></div>
                <div class="admin_pop"><span>DBA Wallet Hold: </span>  <label>{!! $allrecord->User->dba_currency.' '.number_format($allrecord->User->dba_hold_wallet_amount,10,'.',',') !!}</label></div>

                <div class="admin_pop"><span>Affiliate Balance: </span>  <label>
                        @php
                        $res = getAffiliateBalanceByUserId($allrecord->User->id);
                        @endphp
                        @if($res)USD {{$res}}@else USD 0.00 @endif
                    </label></div>
                <div class="admin_pop"><span>Country: </span>  <label>{{$allrecord->User->country?$allrecord->User->country:'N/A'}}</label></div>
                <!--<div class="admin_pop"><span>Currency: </span>  <label>{{$allrecord->User->currency?$allrecord->User->currency:'N/A'}}</label></div>-->
                <div class="admin_pop"><span>Address Line1: </span>  <label>{{$allrecord->User->addrs_line1}}</label></div>
                <div class="admin_pop"><span>Address Line2: </span>  <label>{{$allrecord->User->addrs_line2}}</label></div>


                <div class="admin_pop"><span>Identity Card Type: </span>  <label>{!! $allrecord->User->identity_card_type !!}</label></div>

                <div class="admin_pop"><span>National Identity Number: </span>  <label>{{$allrecord->User->national_identity_number?$allrecord->User->national_identity_number:'N/A'}}</label></div>

                @php
                $ext = pathinfo($allrecord->User->identity_image, PATHINFO_EXTENSION);
                @endphp
                <div class="admin_pop"><span>Upload identity document</span> <label>
                        @if($allrecord->User->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->identity_image}}" type="application/pdf" width="420" height="350">
                                <a href="{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->identity_image}}">{{IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->identity_image}}</a>
                            </object></div>

                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->identity_image, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </label></div>

                <div class="admin_pop"><span>Proof Of Address Type: </span>  <label>{!! $allrecord->User->address_proof_type !!}</label></div>
                <div class="admin_pop"><span>Proof Of Address Document Number: </span>  <label>{{$allrecord->User->address_proof_number?$allrecord->User->address_proof_number:'N/A'}}</label></div>
                @if($allrecord->User->address_document != '')
                <div class="admin_pop"><span>Proof Of Address Document</span> <label>
                        @php
                        $ext = pathinfo($allrecord->User->address_document, PATHINFO_EXTENSION);
                        @endphp
                        @if($allrecord->User->address_document == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->address_document}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->address_document}}">{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->identity_image}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->address_document, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif   </label></div>
                @endif

                @if($allrecord->User->profile_image != '')
                <div class="admin_pop"><span>Selfie</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->User->profile_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif

                @if($allrecord->User->image != '')
                <div class="admin_pop"><span>Profile Image</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->User->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif
        </fieldset>
    </div>

    @else
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($allrecord->User->business_name) !!}</legend>
            <div class="drt">
                <div class="admin_pop"><span>User Type: </span>  <label>@isset($allrecord->User->user_type) {{$allrecord->User->user_type}} @endisset</label></div>
                <div class="admin_pop"><span>Business Name: </span>  <label>{!! strtoupper($allrecord->User->business_name) !!}</label></div>
                <div class="admin_pop"><span>Director Name: </span>  <label>{!! strtoupper($allrecord->User->director_name) !!}</label></div>
                <div class="admin_pop"><span>Business Email: </span>  <label>{{$allrecord->User->email?$allrecord->User->email:'N/A'}}</label></div>
                <div class="admin_pop"><span>API Key: </span>  <label>{{$allrecord->User->api_key?$allrecord->User->api_key:'N/A'}}</label></div>
               <div class="admin_pop"><span>Account Number: </span>  <label>{!! $allrecord->User->account_number !!}</label></div>
               <div class="admin_pop"><span>Wallet Type: </span>  <label>{!! $allrecord->User->account_category !!}</label></div>
               <div class="admin_pop"><span>Account Balance: </span>  <label>{!! $allrecord->User->currency.' '.number_format($allrecord->User->wallet_amount ,10,'.',',') !!}</label></div>

               <div class="admin_pop"><span>Interest Wallet: </span>  <label>{!! $allrecord->User->dba_currency.' '.number_format(($allrecord->User->dba_wallet_amount+$allrecord->User->dba_hold_wallet_amount ) ,10,'.',',') !!}</label></div>
                <div class="admin_pop"><span>DBA Wallet Available: </span>  <label>{!! $allrecord->User->dba_currency.' '.number_format($allrecord->User->dba_wallet_amount ,10,'.',',') !!}</label></div>
                <div class="admin_pop"><span>DBA Wallet Hold: </span>  <label>{!! $allrecord->User->dba_currency.' '.number_format($allrecord->User->dba_hold_wallet_amount,10,'.',',') !!}</label></div>

                <div class="admin_pop"><span>Affiliate Balance: </span>  <label>
                    @php
                        $res = getAffiliateBalanceByUserId($allrecord->User->id);
                    @endphp
                    @if($res)USD {{$res}}@else USD 0.00 @endif
                    </label></div>

                <div class="admin_pop"><span>Phone Number: </span>  <label>{!! $allrecord->User->phone !!}</label></div>
                <div class="admin_pop"><span>Country: </span>  <label>{{$allrecord->User->country?$allrecord->User->country:'N/A'}}</label></div>
                <!--<div class="admin_pop"><span>Currency: </span>  <label>{{$allrecord->User->currency?$allrecord->User->currency:'N/A'}}</label></div>-->

                <div class="admin_pop"><span>Business Type: </span>  <label>{!! $allrecord->User->business_type !!}</label></div>
                <div class="admin_pop"><span>Business registration number: </span>  <label>{!! $allrecord->User->registration_number !!}</label></div>
                @if($allrecord->User->registration_document != '')
                <div class="admin_pop"><span>Business Registration Document</span> <label>{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->registration_document, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif
                <div class="admin_pop"><span>Address Line1: </span>  <label>{{$allrecord->User->addrs_line1}}</label></div>
                <div class="admin_pop"><span>Address Line2: </span>  <label>{{$allrecord->User->addrs_line2}}</label></div>
                
                <div class="admin_pop"><span>Identity Card Type: </span>  <label>{!! $allrecord->User->identity_card_type !!}</label></div>
                <div class="admin_pop"><span>National Identity Number: </span>  <label>{{$allrecord->User->national_identity_number?$allrecord->User->national_identity_number:'N/A'}}</label></div>
                @if($allrecord->User->identity_image != '')
                <div class="admin_pop"><span>Upload identity document</span> <label>{{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->identity_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif

                <div class="admin_pop"><span>Proof Of Address Type: </span>  <label>{!! $allrecord->User->address_proof_type !!}</label></div>
                <div class="admin_pop"><span>Proof Of Address Document Number: </span>  <label>{{$allrecord->User->address_proof_number?$allrecord->User->address_proof_number:'N/A'}}</label></div>
                @if($allrecord->User->address_document != '')
                <div class="admin_pop"><span>Proof Of Address Document</span> <label>
@php
                        $ext = pathinfo($allrecord->User->address_document, PATHINFO_EXTENSION);
                        @endphp
                        @if($allrecord->User->address_document == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->address_document}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->address_document}}">{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->identity_image}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->address_document, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif                    </label></div>
                @endif

                @if($allrecord->User->profile_image != '')
                <div class="admin_pop"><span>Selfie</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->User->profile_image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif

                @if($allrecord->User->image != '')
                <div class="admin_pop"><span>Profile Image</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->User->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif

                
                @if($allrecord->User->certificate_of_incorporation != '')
                <div class="admin_pop"><span>Certificate of Incorporation</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->certificate_of_incorporation}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->certificate_of_incorporation, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->certificate_of_incorporation == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->certificate_of_incorporation, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->User->article != '')
                <div class="admin_pop"><span>Article</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->article}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->article, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->article == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->article, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->User->memorandum != '')
                <div class="admin_pop"><span>Memorandum</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->memorandum}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->memorandum, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->memorandum == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->memorandum, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->User->tax_certificate != '')
                <div class="admin_pop"><span>Tax Certificate</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->tax_certificate}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->tax_certificate, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->tax_certificate == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->tax_certificate, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->User->address_proof != '')
                <div class="admin_pop"><span>Proof of Business Address</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->address_proof}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->address_proof, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->address_proof == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->address_proof, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif
                @if($allrecord->User->identity != '')
                <div class="admin_pop"><span>Identity of all Directors</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->identity}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->identity, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->identity == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->identity, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>
                @endif

                @if($allrecord->User->person_identity != '')
                <div class="admin_pop"><span>Identity of person or entity holding more than 25% stake in the company</span> 
                    <label>
                        <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$allrecord->User->person_identity}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($allrecord->User->person_identity, PATHINFO_EXTENSION);
                                @endphp
                                @if($allrecord->User->person_identity == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$allrecord->User->person_identity, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                    </label>
                </div>          
      @endif
</fieldset>
</div>

    @endif
</div>
@endif
         


@php $userTyp = $allrecord->User->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($allrecord->User->first_name)).' '.strtoupper(strtoupper($allrecord->User->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($allrecord->User->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($allrecord->User->first_name) ? strtoupper(strtoupper($allrecord->User->first_name)).' '.strtoupper(strtoupper($allrecord->User->last_name)):strtoupper($allrecord->User->director_name) @endphp
@endif
<div class="modal fade" id="basicModal{{$allrecord->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>DBA Deposit Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'DBA Deposit'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! 'USD '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$allrecord->blockchain_url}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>
                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to approve this request?
                        </label>
                    </div>  
                    </div>  
            </div>
            <div class="modal-footer pop-ok">
                <a href="{{URL::to('admin/users/change-dba-deposit-req-status/'.$allrecord->id.'/1')}}" type="button" class="btn btn-default button_disable{{$allrecord->id}}" onclick="disable_submit_{{$allrecord->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submit_{{$allrecord->id}}()
{    
$('.button_disable{{$allrecord->id}}').attr('disabled', true); 
$('.button_disable{{$allrecord->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_reject{{$allrecord->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>DBA Deposit Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'DBA Deposit'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! 'USD '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$allrecord->blockchain_url}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>
                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to reject this request?
                        </label>
                    </div>  
                    </div>  
            </div>
            <div class="modal-footer pop-ok">
                <a href="{{URL::to('admin/users/change-dba-deposit-req-status/'.$allrecord->id.'/3')}}" type="button" class="btn btn-default button_disable{{$allrecord->id}}" onclick="disable_submit_{{$allrecord->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
       <?php $j++; ?>
@endforeach
@endif

<script>
                                    function copyTextToClipboard(text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text
                                            document.body.appendChild(textArea);
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
                                                                        alert("Link copied successfully.");
                                    
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.body.removeChild(textArea);
                                    }
                                    </script>