{{ HTML::script('public/assets/js/facebox.js')}}
{{ HTML::style('public/assets/css/facebox.css')}}

<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if($allrecords->isNotEmpty())
<div class="panel-body marginzero">
    <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Help Request List</div>
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
                        <th class="sorting_paging">S.No</th>
                        <th class="sorting_paging">@sortablelink('name', 'Name')</th>
		            	<th class="sorting_paging">@sortablelink('email','email')</th>
                        <th class="sorting_paging">Subject</th>
                        <th class="sorting_paging">Message</th>
                        <th class="sorting_paging">@sortablelink('status', 'Status')</th>
                        <th class="sorting_paging">@sortablelink('created_at', 'Date')</th>
                        <th class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    @endphp
                    @foreach($allrecords as $key=>$allrecord)
                    <tr>
                        <td>{{$key + $allrecords->firstItem()}}</td>
                        <td data-title="name">{{strtoupper($allrecord->name)}}</td>
                        <td data-title="Email">{{$allrecord->email}}</td>
                        <td data-title="subject">{{$allrecord->subject}}</td>
                        <td data-title="message">{{$allrecord->message}}</td>
                        <td data-title="Date">{{ $allrecord->status =='Y' ? 'Completed' : 'Pending'}}</td>
                        <td data-title="Date">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Action">
                            <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span> 
                                </button>
                                <ul class="dropdown-menu pull-right">
                                <?php if($allrecord->status =='N') { ?>
                                <li><a href="{{URL::to('admin/users/approve_get_help/'.$allrecord->id)}}" title="Mark As Completed"  onclick="javascript:return confirm('Are you sure do you want to complete this request ?')" ><i class="fa fa-check"></i>Mark As Completed</a></li>
                                <?php } ?>

								<li><a href="{{URL::to('admin/users/delete_get_help/'.$allrecord->id)}}" title="Delete"  onclick="javascript:return confirm('Are you sure do you want to delete this request ?')" rel='facebox'><i class="fa fa-trash"></i>Delete</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    @php
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
        </div>
    </section>
    {{ Form::close()}}
</div>         
</div> 
@else 
<div id="listingJS" style="display: none;" class="alert alert-success alert-block fade in"></div>
<div class="admin_no_record">No record found.</div>
@endif

<!-- @if(!$allrecords->isEmpty())
@foreach($allrecords as $allrecord)
<div id="info{!! $allrecord->id !!}" style="display: none;">
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($allrecord->name) !!}</legend>
            <div class="drt">
			
                <div class="admin_pop"><span>Name: </span>  <label>{!! strtoupper($allrecord->name) !!}</label></div>
                <div class="admin_pop"><span>Email: </span>  <label>{!! $allrecord->email !!}</label></div>
                <div class="admin_pop"><span>Subject: </span>  <label>{!! $allrecord->subject !!}</label></div>
		<div class="admin_pop"><span>Message: </span>  <label>{!! $allrecord->message !!}</label></div>
                <div class="admin_pop"><span>Date: </span>  <label>{{$allrecord->created_at}}</label></div>
                                
        </fieldset>
    </div>
</div>
@endforeach
@endif -->