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
@if(!$agents->isEmpty())
<div class="panel-body marginzero">
    <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Bank Agent's List</div>
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$agents->appends(Request::except('_token'))->render()}}
                </div>
            </div>                
        </div>
        <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th style="width:5%">#</th>
                        <th class="sorting_paging">@sortablelink('first_name', 'Name')</th>
                        <th class="sorting_paging">@sortablelink('country', 'Country')</th>
                        <th class="sorting_paging">@sortablelink('email', 'Email Address')</th>
                        <th class="sorting_paging">@sortablelink('phone', 'Phone')</th>
						<th class="sorting_paging">@sortablelink('commission','Commission')</th>
                        <th class="sorting_paging">@sortablelink('is_approved', 'Status')</th>
                        <th class="sorting_paging">@sortablelink('edited_by', 'Edited By')</th>
                        <th class="sorting_paging">@sortablelink('created_at', 'Date')</th>
                        <th class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $allrecord)
                    <tr>
                        <th style="width:5%"><input type="checkbox" onclick="javascript:isAllSelect(this.form);" name="chkRecordId[]" value="{{$allrecord->id}}" /></th>
                        <td data-title="Name"><a href="#ss-{!! $allrecord->id !!}" title="View Request Detail" class="" rel='facebox'>{{strtoupper($allrecord->first_name)}}</a></td>
						<td data-title="Country">{{$allrecord->country}}</td>
                        <td data-title="Email Address">{{$allrecord->email?$allrecord->email:'N/A'}}</td>
                        <td data-title="Contact Number">{{$allrecord->phone}}</td>
						<td data-title="Commission">{{$allrecord->commission.'%'}}</td>
                        <td data-title="Status" id="verify_{{$allrecord->id}}">
                            @if($allrecord->is_approved == 1)
                                Approve
                            @elseif($allrecord->is_approved == 2)
                                Reject
							@else
							  Pending
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
                        <td data-title="Date">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Action">
                            <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            
                            
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li class="right_acdcasdsd" id="status{{$allrecord->id}}">


                                        @if($allrecord->is_approved == '1')
                                        <a href="{{ URL::to( 'admin/users/disapproveAgent/'.$allrecord->id)}}" title="Deactivate Agent" class="deactivate"><i class="fa fa-ban"></i>Disapprove Agent</a>
                                        @else
                                        <a href="{{ URL::to( 'admin/users/approveAgent/'.$allrecord->id)}}" title="Activate Agent" class="activate"><i class="fa fa-check"></i>Activate Agent</a>
										<a href="{{ URL::to( 'admin/users/disapproveAgent/'.$allrecord->id)}}" title="Deactivate User" class="deactivate" ><i class="fa fa-ban"></i>Disapprove Agent</a>
                                        @endif
										
										@if($allrecord->is_approved == '1')
										<li><a href="{{ URL::to( 'admin/users/set-agent-trans-limit/'.$allrecord->id)}}" title="Generate Agent Request" class=""><i class="fa fa-file"></i>Set Agent Limit</a></li>	
										@endif
                                    </li>
                                    <li><a href="{{ URL::to( 'admin/users/editagent/'.$allrecord->id)}}" title="Activate Agent" class="activate"><i class="fa fa-edit"></i>Edit Agent Details</a></li>
									<li><a href="#info{!! $allrecord->id !!}" title="View Agent Detail" class="" rel='facebox'><i class="fa fa-eye"></i>View Agent Detail</a></li>
                                </ul>
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
				'Verify' => "Approve Request"
				); ?>
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

@if(!$agents->isEmpty())
@foreach($agents as $allrecord)
<div id="info{!! $allrecord->id !!}" style="display: none;">
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($allrecord->first_name.' '.$allrecord->last_name) !!}</legend>
            <div class="drt">
			<div class="admin_pop"><span>Profile image: </span>  <label> {{HTML::image('/public/uploads/profile_images/full/'.$allrecord->profile_image, 'Profile image')}}</label></div>
                <div class="admin_pop"><span>User ID: </span>  <label> {{$allrecord->user_id}} </label></div>
                <div class="admin_pop"><span>First Name: </span>  <label>{!! strtoupper($allrecord->first_name) !!}</label></div>
                <div class="admin_pop"><span>Last Name: </span>  <label>{!! strtoupper($allrecord->last_name) !!}</label></div>
                <div class="admin_pop"><span>Phone Number: </span>  <label>{!! $allrecord->phone !!}</label></div>
				<div class="admin_pop"><span>Email: </span>  <label>{!! $allrecord->email !!}</label></div>
                <div class="admin_pop"><span>Country: </span>  <label>{{$allrecord->country?$allrecord->country:'N/A'}}</label></div>
                <div class="admin_pop"><span>Commission: </span>  <label>{{$allrecord->commission?$allrecord->commission.'%':'N/A'}}</label></div>
                
                
                <div class="admin_pop"><span>Minimum Deposit/Withdrawal: </span>  <label>{!! $allrecord->min_amount !!}</label></div>
                <div class="admin_pop"><span>Address: </span>  <label>{{$allrecord->address?$allrecord->address:'N/A'}}</label></div>
                                   
                <div class="admin_pop"><span>Payment Method: </span>  <label>{!! $allrecord->payment_methods !!}</label></div>
                <div class="admin_pop"><span>Description: </span>  <label>{{$allrecord->description?$allrecord->description:'N/A'}}</label></div>
                
        </fieldset>
    </div>
</div>
@endforeach
@endif
@if(!$agents->isEmpty())
@foreach($agents as $allrecord)
<div id="ss-{!! $allrecord->id !!}" style="display: none;">
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($allrecord->User->first_name).' '.strtoupper($allrecord->User->last_name) !!}</legend>
            <div class="drt">
                <div class="admin_pop"><span>User Type: </span>  <label>@isset($allrecord->User->user_type) {{$allrecord->User->user_type}} @endisset</label></div>
                @if($allrecord->User->user_type == 'Personal')
		   <div class="admin_pop"><span>Name: </span><label>{{strtoupper(isset($allrecord->User->first_name)) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):$allrecord->User->business_name}}</label></div>
                @elseif($allrecord->User->user_type == 'Business')
                        <div class="admin_pop"><span>Director Name: </span><label>{{ strtoupper($allrecord->User->business_name) }}</label></div>
		@elseif($allrecord->User->user_type == 'Agent')
                        <div class="admin_pop"><span>Name: </span><label>{{strtoupper(isset($allrecord->User->first_name)) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):$allrecord->User->business_name}}</label></div>
		@endif
                 <div class="admin_pop"><span>Phone Number: </span>  <label>{!! $allrecord->User->phone !!}</label></div>
                <div class="admin_pop"><span>Country: </span>  <label>{{$allrecord->User->country?$allrecord->User->country:'N/A'}}</label></div>
                <!--<div class="admin_pop"><span>Currency: </span>  <label>{{$allrecord->User->currency?$allrecord->User->currency:'N/A'}}</label></div>-->
                <div class="admin_pop"><span>Account Balance: </span>  <label>{!! $allrecord->User->currency.' '.$allrecord->User->wallet_amount !!}</label></div>
                <div class="admin_pop"><span>Account Number: </span>  <label>{!! $allrecord->User->account_number !!}</label></div>
                
                
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
                @if($allrecord->User->image != '')
                    <div class="admin_pop"><span>Profile Image</span> <label>{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$allrecord->User->image, SITE_TITLE,['style'=>"max-width: 200px"])}}</label></div>
                @endif
                
                
                
        </fieldset>
    </div>
</div>
@endforeach
@endif