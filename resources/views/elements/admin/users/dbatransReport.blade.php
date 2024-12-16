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

            <div class="pull-right"><input type="button" class="btn btn-info" value="Export CSV" onclick=" exportCSV();"></div>		
        </div>
        <div class="tbl-resp-listing" style="scroll-behavior: auto;overflow: scroll;">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th style="width:5%">@sortablelink('id','Trans ID')</th>
                        <th class="sorting_paging">Sender</th>
                        <th class="sorting_paging">Receiver</th>
                        <!--<th class="sorting_paging">@sortablelink('currency','Currency')</th>-->
                        <th class="sorting_paging">@sortablelink('amount','Amount')</th>
                        <th class="sorting_paging">@sortablelink('trans_for','Trans. Type')</th>
                        <th class="sorting_paging">@sortablelink('receiver_fees','Receiver Fees')</th>
                        <th class="sorting_paging">@sortablelink('sender_fees','Sender Fees')</th>
                        <th class="sorting_paging">Ref ID</th>
                        <th class="sorting_paging">@sortablelink('status','Status')</th>
                        <th class="action_dvv"> @sortablelink('created_at', 'Trans Date')</th>
                        <th width="5%" class="action_dvv"> Action</th>
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

                    <?php 

                    if($allrecord->user_id!=0)
                    {
                    $sender_data=getUserByUserId($allrecord->user_id);
                    $sender_slug=$sender_data->slug;
                    }
                    elseif($allrecord->user_id==0)
                    {
                    $sender_data=getUserByUserId($allrecord->receiver_id);
                    $sender_slug=$sender_data->slug;  
                    }

                    if($allrecord->receiver_id!=0)
                    {
                    $receiver_data=getUserByUserId($allrecord->receiver_id);
                    $receiver_slug=$receiver_data->slug;
                    }
                    elseif($allrecord->receiver_id==0)
                    {
                    $receiver_data=getUserByUserId($allrecord->user_id);
                    $receiver_slug=$receiver_data->slug;  
                    }

                    ?>

                    <tr>
                        <th style="width:5%">
                        
                        <a href="#sssd-{!! $allrecord->id !!}" title="View Transaction Detail" class="" rel='facebox'>{{$allrecord->id}}</a></th>

                        <td data-title="Name">
                            
                        <?php if($allrecord->user_id!="1" || $allrecord->user_id==$allrecord->receiver_id )
                        { ?>
                        <a href="{{ URL::to( 'admin/users/user_detail/'.$sender_slug)}}" title="View Request Detail" class="">
                                @if($userTyp == 'Personal')
                                {{isset($allrecord->User->first_name) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):'N/A'}}
                                @elseif($userTyp == 'Business')
                                {{ strtoupper($allrecord->User->business_name) }}
                                @elseif($userTyp == 'Agent')
                                {{isset($allrecord->User->first_name) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):strtoupper($allrecord->User->business_name)}}
                                @endif
                            </a>
                            <?php }else{ ?>
                            @if($userTyp == 'Personal')
                                {{isset($allrecord->User->first_name) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):'N/A'}}
                                @elseif($userTyp == 'Business')
                                {{ strtoupper($allrecord->User->business_name) }}
                                @elseif($userTyp == 'Agent')
                                {{isset($allrecord->User->first_name) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):strtoupper($allrecord->User->business_name)}}
                                @endif
                        <?php } ?>

                        </td>
                        <td data-title="Name">
                            
                        <?php if($allrecord->receiver_id!="1" || $allrecord->user_id==$allrecord->receiver_id )
                          { ?>
                            <a href="{{ URL::to( 'admin/users/user_detail/'.$receiver_slug)}}" title="View Request Detail" class="">

                                @if($allrecord->trans_for == 'Withdraw##Agent')
                                    @php
                                    $agent = getAgentById($allrecord->receiver_id);
                                    if ($agent != false) {
                                        $transFnm = $agent->first_name;
                                        $transLnm = $agent->last_name;
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                                    }
                                    else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = strtoupper(substr($transFnm,0,1));  
                                    }
                                    @endphp
                                    {{strtoupper(strtolower($transFnm))." ".strtoupper(strtolower($transLnm))}}                                
                                @else
                                @if($res != false && $res->user_type == 'Personal')
                                {{strtoupper(strtolower($res->first_name))." ".strtoupper(strtolower($res->last_name))}}
                                @elseif($res != false && $res->user_type == 'Business')
                                {{ strtoupper(strtolower($res->business_name)) }}
                                @elseif($res != false && $res->user_type == 'Agent' && $res->first_name != "")
                                {{strtoupper(strtolower($res->first_name))." ".strtoupper(strtolower($res->last_name))}}
                                @elseif($res != false && $res->user_type == 'Agent' && $res->director_name != "")
                                {{ strtoupper(strtolower($res->business_name)) }}
                                @else
                                @php
                                $agent = getAgentById($allrecord->receiver_id);
                                if ($agent != false) {
                                $transFnm = $agent->first_name;
                                $transLnm = $agent->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                                }
                                else {
                                $transFnm = "N/A";
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = strtoupper(substr($transFnm,0,1));  
                                }
                                @endphp
                                {{strtoupper(strtolower($transFnm))." ".strtoupper(strtolower($transLnm))}}
                                @endif
                                @endif
                            </a>

                               <?php }else{  ?>

                                @if($allrecord->trans_for == 'Withdraw##Agent')
                                    @php
                                    $agent = getAgentById($allrecord->receiver_id);
                                    if ($agent != false) {
                                        $transFnm = $agent->first_name;
                                        $transLnm = $agent->last_name;
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                                    }
                                    else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = strtoupper(substr($transFnm,0,1));  
                                    }
                                    @endphp
                                    {{strtoupper(strtolower($transFnm))." ".strtoupper(strtolower($transLnm))}}                                
                                @else
                                @if($res != false && $res->user_type == 'Personal')
                                {{strtoupper(strtolower($res->first_name))." ".strtoupper(strtolower($res->last_name))}}
                                @elseif($res != false && $res->user_type == 'Business')
                                {{ strtoupper(strtolower($res->business_name)) }}
                                @elseif($res != false && $res->user_type == 'Agent' && $res->first_name != "")
                                {{strtoupper(strtolower($res->first_name))." ".strtoupper(strtolower($res->last_name))}}
                                @elseif($res != false && $res->user_type == 'Agent' && $res->director_name != "")
                                {{ strtoupper(strtolower($res->business_name)) }}
                                @else
                                @php
                                $agent = getAgentById($allrecord->receiver_id);
                                if ($agent != false) {
                                $transFnm = $agent->first_name;
                                $transLnm = $agent->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                                }
                                else {
                                $transFnm = "N/A";
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = strtoupper(substr($transFnm,0,1));  
                                }
                                @endphp
                                {{strtoupper(strtolower($transFnm))." ".strtoupper(strtolower($transLnm))}}
                                @endif
                                @endif

                         <?php } ?>
                        
                        
                        </td>
                        <!--<td data-title="Email Address">{{$allrecord->currency}}</td>-->
                        <td data-title="Contact Number">{{number_format($allrecord->amount,10,'.',',').' '.$allrecord->currency}}</td>

                        <td data-title="KYC Status">
                         @if($allrecord->trans_for=='SWAP')
                         {{$allrecord->trans_for}}
                         <br>
                         ({{number_format($allrecord->real_value,2,'.',',').' DBA'}})
                         @else
                         {{ str_replace("REFUND", "REVERSE", $allrecord->trans_for)}}
                         @endif
                        </td>

                        <td data-title="Status">
                            
                        @if($allrecord->sender_fees=="0.0000000000" && $allrecord->receiver_fees=="0.0000000000")
                        {{number_format($allrecord->fees,10,'.',',').' '.$allrecord->currency}}
                        @else
                        {{number_format($allrecord->receiver_fees,10,'.',',').' '.$allrecord->receiver_currency}}
                        @endif 

                        </td>

                        <td data-title="Status">
                        @if($allrecord->sender_fees=="0.0000000000" && $allrecord->receiver_fees=="0.0000000000")
                        {{number_format($allrecord->fees,10,'.',',').' '.$allrecord->currency}}
                        @else
                        {{number_format($allrecord->sender_fees,10,'.',',').' '.$allrecord->sender_currency}}
                        @endif
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
                            {{ 'Hold' }}
                            @endif


                        </td>
                        <td data-title="Action">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>


                        <td data-title="Action">
                           
                           @php
                   
                   $dba_deposits_all=getTransactionrecord($allrecord->id,'dba_deposits');
                   $dba_withdraws_all= getTransactionrecord($allrecord->id,'dba_withdraws');
                  

             
                   
                   @endphp
                           @if(!empty($dba_deposits_all))
                       
                        <div id="loderstatus{{$dba_deposits_all->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                        <div class="btn-group">
                           <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                               <i class="fa fa-list"></i>
                               <span class="caret"></span>
                           </button>

                           <ul class="dropdown-menu pull-right">
                           @if($dba_deposits_all->status != 1 && $dba_deposits_all->status != 3)
								<li><a target="_blank"  href="{{URL::to('admin/users/edit-dba-deposit-request/'.$dba_deposits_all->id.'/')}}" title="Edit Request" class=""><i class="fa fa-edit"></i>Edit</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModal{{$dba_deposits_all->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>

								<li><a href="{{URL::to('admin/users/change-dba-deposit-req-status/'.$dba_deposits_all->id.'/2')}}" title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>

								<li><a href="#" data-toggle="modal" data-target="#basicModal_reject{{$dba_deposits_all->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a href="{{ URL::to( 'admin/users/kycdetail/'.$dba_deposits_all->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a href="{{ URL::to( 'admin/users/transaction-list/'.$dba_deposits_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a href="{{ URL::to( 'admin/users/dba-transaction-list/'.$dba_deposits_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>

								@endif
                           </ul></div>
                           @endif




                           @if(!empty($dba_withdraws_all))
                       
                       <div id="loderstatus{{$dba_withdraws_all->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                       <div class="btn-group">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                              <i class="fa fa-list"></i>
                              <span class="caret"></span>
                          </button>

                          <ul class="dropdown-menu pull-right">
                          @if($dba_withdraws_all->status != 1 && $dba_withdraws_all->status != 3)
								<!-- <li><a href="{{URL::to('admin/users/edit-crypto-withdraw-request/'.$dba_withdraws_all->id.'/')}}" title="Edit Request" class=""><i class="fa fa-edit"></i>Edit</a></li> -->
								
								<li><a href="#" data-toggle="modal" data-target="#basicModalw{{$dba_withdraws_all->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								
								<li><a href="{{URL::to('admin/users/change-dba-withdraw-req-status/'.$dba_withdraws_all->id.'/2')}}"  title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModal_rejectw{{$dba_withdraws_all->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a href="{{ URL::to( 'admin/users/kycdetail/'.$dba_withdraws_all->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a href="{{ URL::to( 'admin/users/transaction-list/'.$dba_withdraws_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a href="{{ URL::to( 'admin/users/dba-transaction-list/'.$dba_withdraws_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>


								@endif
                          </ul></div>
                          @endif

 </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- <div class="search_frm">
                <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-info">Select All</button>
                <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-info">Unselect All</button>
            <?php
            $accountStatus = array(
                'Verify' => "Verify User",
                'Unverify' => "Unverify User",
                'Delete' => "Delete",
            );
            ;
            ?>
                <div class="list_sel">{{Form::select('action', $accountStatus,null, ['class' => 'small form-control','placeholder' => 'Action for selected record', 'id' => 'action'])}}</div>
                <button type="submit" class="small btn btn-success btn-cons btn-info" onclick="return ajaxActionFunction();" id="submit_action">OK</button>
            </div> -->    
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
<?php //echo '<pre>';print_r($allrecords);exit;?>
@if(!$allrecords->isEmpty())
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

<div id="sssd-{!! $allrecord->id !!}" style="display: none;">
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">Trans ID : 
                {!! $allrecord->id !!}
            </legend>
            <div class="drt">
                <div class="admin_pop"><span>Sender : </span>  <label>  @if($userTyp == 'Personal')
                                {{isset($allrecord->User->first_name) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):'N/A'}}
                                @elseif($userTyp == 'Business')
                                {{ strtoupper($allrecord->User->business_name) }}
                                @elseif($userTyp == 'Agent')
                                {{isset($allrecord->User->first_name) ? strtoupper(strtolower($allrecord->User->first_name)).' '.strtoupper(strtolower($allrecord->User->last_name)):strtoupper($allrecord->User->business_name)}}
                                @endif</label></div>

                                <div class="admin_pop"><span>Receiver : </span>  <label> 
                                @if($allrecord->trans_for == 'Withdraw##Agent')
                                    @php
                                    $agent = getAgentById($allrecord->receiver_id);
                                    if ($agent != false) {
                                        $transFnm = $agent->first_name;
                                        $transLnm = $agent->last_name;
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                                    }
                                    else {
                                        $transFnm = "N/A";
                                        $transLnm = "";
                                        $transName = $transFnm." ".$transLnm;
                                        $transShortName = strtoupper(substr($transFnm,0,1));  
                                    }
                                    @endphp
                                    {{strtoupper(strtolower($transFnm))." ".strtoupper(strtolower($transLnm))}}                                
                                @else
                                @if($res != false && $res->user_type == 'Personal')
                                {{strtoupper(strtolower($res->first_name))." ".strtoupper(strtolower($res->last_name))}}
                                @elseif($res != false && $res->user_type == 'Business')
                                {{ strtoupper(strtolower($res->business_name)) }}
                                @elseif($res != false && $res->user_type == 'Agent' && $res->first_name != "")
                                {{strtoupper(strtolower($res->first_name))." ".strtoupper(strtolower($res->last_name))}}
                                @elseif($res != false && $res->user_type == 'Agent' && $res->director_name != "")
                                {{ strtoupper(strtolower($res->business_name)) }}
                                @else
                                @php
                                $agent = getAgentById($allrecord->receiver_id);
                                if ($agent != false) {
                                $transFnm = $agent->first_name;
                                $transLnm = $agent->last_name;
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = strtoupper(substr($transFnm,0,1)).strtoupper(substr($transLnm,0,1));
                                }
                                else {
                                $transFnm = "N/A";
                                $transLnm = "";
                                $transName = $transFnm." ".$transLnm;
                                $transShortName = strtoupper(substr($transFnm,0,1));  
                                }
                                @endphp
                                {{strtoupper(strtolower($transFnm))." ".strtoupper(strtolower($transLnm))}}
                                @endif
                                @endif
                                </label></div>


                                <div class="admin_pop"><span>Currency : </span>  <label> 
                                {{$allrecord->currency}}
                                </label></div>
                               
                                <div class="admin_pop"><span>Amount : </span>  <label> 
                                {{number_format($allrecord->amount,10,'.',',')}}
                                </label></div>

                                <div class="admin_pop"><span>Sender Fees : </span>  <label> 
                                @if($allrecord->sender_fees=="0.0000000000" && $allrecord->receiver_fees=="0.0000000000")
                        {{number_format($allrecord->fees,10,'.',',').' '.$allrecord->currency}}
                        @else
                        {{number_format($allrecord->sender_fees,10,'.',',').' '.$allrecord->sender_currency}}
                        @endif
                                </label></div>


                                <div class="admin_pop"><span>Receiver Fees : </span>  <label> 
                                @if($allrecord->sender_fees=="0.0000000000" && $allrecord->receiver_fees=="0.0000000000")
                                {{number_format($allrecord->fees,10,'.',',').' '.$allrecord->currency}}
                                @else
                                {{number_format($allrecord->receiver_fees,10,'.',',').' '.$allrecord->receiver_currency}}
                                @endif 
                                </label></div>

                                <div class="admin_pop"><span>Trans. Type : </span>  <label> 
                               @if($allrecord->trans_for=='SWAP')
                         {{$allrecord->trans_for}}
                         <br>
                         ({{number_format($allrecord->real_value,2,'.',',').' DBA'}})
                         @else
                         {{ str_replace("REFUND", "REVERSE", $allrecord->trans_for)}}
                         @endif
                                </label></div>

                                <div class="admin_pop"><span>Ref ID : </span>  <label> 
                                @if($allrecord->refrence_id == 'na')
                            {{ 'N/A' }}
                            @else
                            {{$allrecord->refrence_id}}
                            @endif
                                </label></div>

                                <div class="admin_pop"><span>Status : </span>  <label> 
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
                            </label></div>

                            <div class="admin_pop"><span>Billing Description : </span>  <label> 
                            <?php 
                            if($allrecord->billing_description=='na')
                            {
                                echo "N/A";
                            }
                            else{
                                $bllngDesc = str_replace("<br>","##",$allrecord->billing_description);
                                $descArr = explode("##",$bllngDesc); 
                                foreach($descArr as $val)
                                {
                                    echo $val.'<br>';
                                }  
                            }
                            
                            
                            ?>
                            </label></div>

                            <div class="admin_pop"><span>Trans Date : </span>  <label> 
                            {{$allrecord->created_at->format('M d, Y h:i A')}}
                            </label></div>

                            <div class="admin_pop"><span>Last Updated Date : </span>  <label> 
                            {{$allrecord->updated_at->format('M d, Y h:i A')}}
                            </label></div>



        </fieldset>
    </div>
</div>


@php   $dba_deposits_all=getTransactionrecord($allrecord->id,'dba_deposits'); @endphp

@if(!empty($dba_deposits_all))
@php $userTyp = $dba_deposits_all->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($dba_deposits_all->first_name)).' '.strtoupper(strtoupper($dba_deposits_all->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($dba_deposits_all->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($dba_deposits_all->first_name) ? strtoupper(strtoupper($dba_deposits_all->first_name)).' '.strtoupper(strtoupper($dba_deposits_all->last_name)):strtoupper($dba_deposits_all->director_name) @endphp
@endif
<div class="modal fade" id="basicModal{{$dba_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
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
                        <input type="text" value="{!! 'USD '.$dba_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$dba_deposits_all->blockchain_url}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$dba_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-dba-deposit-req-status/'.$dba_deposits_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$dba_deposits_all->id}}" onclick="disable_submits_{{$dba_deposits_all->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submits_{{$dba_deposits_all->id}}()
{    
$('.button_disable{{$dba_deposits_all->id}}').attr('disabled', true); 
$('.button_disable{{$dba_deposits_all->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_reject{{$dba_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
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
                        <input type="text" value="{!! 'USD '.$dba_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$dba_deposits_all->blockchain_url}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$dba_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-dba-deposit-req-status/'.$dba_deposits_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$dba_deposits_all->id}}" onclick="disable_submits_{{$dba_deposits_all->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endif




@php         $dba_withdraws_all= getTransactionrecord($allrecord->id,'dba_withdraws'); @endphp

@if(!empty($dba_withdraws_all))
@php $userTyp = $dba_withdraws_all->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($dba_withdraws_all->first_name)).' '.strtoupper(strtoupper($dba_withdraws_all->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($dba_withdraws_all->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($dba_withdraws_all->first_name) ? strtoupper(strtoupper($dba_withdraws_all->first_name)).' '.strtoupper(strtoupper($dba_withdraws_all->last_name)):strtoupper($dba_withdraws_all->director_name) @endphp
@endif
<div class="modal fade" id="basicModalw{{$dba_withdraws_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>DBA Withdrawal Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'DBA Withdraw'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! 'USD '.$dba_withdraws_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$dba_withdraws_all->payout_addrs}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$dba_withdraws_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-dba-withdraw-req-status/'.$dba_withdraws_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$dba_withdraws_all->id}}" onclick="disable_submitwc_{{$dba_withdraws_all->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
function disable_submitwc_{{$dba_withdraws_all->id}}()
{    
$('.button_disable{{$dba_withdraws_all->id}}').attr('disabled', true); 
$('.button_disable{{$dba_withdraws_all->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_rejectw{{$dba_withdraws_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>DBA Withdrawal Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'DBA Withdraw'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! 'USD '.$dba_withdraws_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$dba_withdraws_all->payout_addrs}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$dba_withdraws_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-dba-withdraw-req-status/'.$dba_withdraws_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$dba_withdraws_all->id}}" onclick="disable_submitwc_{{$dba_withdraws_all->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
   

@endif




@endforeach
@endif

