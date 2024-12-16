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
<div class="panel-body marginzero">
    <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
         <!--   <div class="topn_left">Personal Users List</div> -->
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
                        <th style="width:5%">@sortablelink('id','Trans ID')</th>
                        <th class="sorting_paging">Sender</th>
                        <th class="sorting_paging">Receiver</th>
                        <th class="sorting_paging">@sortablelink('currency','Currency')</th>
                        <th class="sorting_paging">@sortablelink('amount','Amount')</th>
                        <th class="sorting_paging">@sortablelink('fees','Fees')</th>
                        <th class="sorting_paging">@sortablelink('trans_for','Trans. Type')</th>
                        <th class="sorting_paging">Ref ID</th>
                        <th class="sorting_paging">@sortablelink('status','Status')</th>
                        <th class="action_dvv"> @sortablelink('created_at', 'Date')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allrecords as $allrecord)
					@if ($allrecord->receiver_id > 0)
					@php
					$res = getUserByUserId($allrecord->receiver_id);
					@endphp
					@else
					@php
					$res = getUserByUserId($allrecord->user_id);
					@endphp
					@endif
					
					@php
					 $userTyp = getUserType($allrecord->user_id);
					 if ($userTyp == false)
					 {
					   continue;	 
					 }
					@endphp
                    <tr>
                        <th style="width:5%">{{$allrecord->id}}</th>
                        <td data-title="Name">
						@if($userTyp == 'Personal')
						{{isset($allrecord->User->first_name) ? ucwords(strtolower($allrecord->User->first_name)).' '.ucwords(strtolower($allrecord->User->last_name)):'N/A'}}
						@elseif($userTyp == 'Business')
						{{ $allrecord->User->director_name }}
						@elseif($userTyp == 'Agent')
						{{isset($allrecord->User->first_name) ? ucwords(strtolower($allrecord->User->first_name)).' '.ucwords(strtolower($allrecord->User->last_name)):$allrecord->User->director_name}}
						@endif
						</td>
						<td data-title="Name">
						@if($res != false && $res->user_type == 'Personal')
						{{ucwords(strtolower($res->first_name))." ".ucwords(strtolower($res->last_name))}}
						@elseif($res != false && $res->user_type == 'Business')
						{{ ucwords(strtolower($res->director_name)) }}
						@elseif($res != false && $res->user_type == 'Agent' && $res->first_name != "")
						{{ucwords(strtolower($res->first_name))." ".ucwords(strtolower($res->last_name))}}
						@elseif($res != false && $res->user_type == 'Agent' && $res->director_name != "")
						{{ ucwords(strtolower($res->director_name)) }}
						@else
						{{ 'N/A' }}
						@endif
						</td>
                        <td data-title="Email Address">{{$allrecord->currency}}</td>
                        <td data-title="Contact Number">{{number_format($allrecord->amount,2,'.',',')}}</td>
                        <td data-title="Status">{{number_format($allrecord->fees,2,'.',',')}}</td>
                        <td data-title="KYC Status">
						@php
						 $paymentTypArr = explode('##',$allrecord->trans_for);
						@endphp
						{{$paymentTypArr[0]}}
						</td>
						<td data-title="KYC Status">
						@if($allrecord->refrence_id == 'na')
						{{ 'N/A' }}
						@else
						{{$allrecord->refrence_id}}
						@endif
						</td>
                        <td data-title="Date">
						@if($allrecord->status == 1)
						{{ 'Success' }}
						@elseif($allrecord->status == 2)
						{{ 'Pending' }}
						@elseif($allrecord->status == 3)
						{{ 'Cancelled' }}
						@elseif($allrecord->status == 4)
						{{ 'Failed' }}
						@elseif($allrecord->status == 5)
						{{ 'Error' }}
						@elseif($allrecord->status == 6)
						{{ 'Abandoned' }}
						@elseif($allrecord->status == 7)
						{{ 'PendingInvestigation' }}
						@endif
						
						
						</td>
                        <td data-title="Action">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- <div class="search_frm">
                <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-info">Select All</button>
                <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-info">Unselect All</button>
                <?php $accountStatus = array(
    'Verify' => "Verify User",
    'Unverify' => "Unverify User",
    'Delete' => "Delete",
);; ?>
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