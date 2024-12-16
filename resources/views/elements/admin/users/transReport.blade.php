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
    function openClosePopup(name,id){ 
        $('.close').click();
        $('#' + name + id).modal('show');
    }
</script>
<?php
use App\DbaTransaction;
?>

<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if(!$allrecords->isEmpty())
<div class="panel-body marginzero">
    <!-- <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div> -->
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
                        
                        <?php if($allrecord->trans_for!="EPAY_CARD") { ?>
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
                        <?php }else{  ?>
                        <td data-title="Name">
                         {{$allrecord->stripe_sender_email}}
                        </td>
                       <?php  }  ?>

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
                            @if($allrecord->receiver_id == 1 || $allrecord->user_id == 1)
                            @if($allrecord->trans_for=='Exchange Charge')
                            {{$allrecord->trans_for}}
                            @elseif($allrecord->user_id==1 && $allrecord->trans_for=='DBA eCash')
                            {{'DBA eCash'}}
                            @elseif($allrecord->edited_by==1)
                            {{'Adjust By Admin'}}
                            @elseif($allrecord->user_id==1 && $allrecord->trans_for=="W2W" && strpos($allrecord->billing_description,'AdminBalanceAdjust') !== false)
                            {{'Adjust By Admin'}}
                            @elseif($allrecord->trans_type==2 && $allrecord->trans_for=="W2W" && strpos($allrecord->billing_description,'admin##AdminBalanceAdjust') !== false)
                            {{'Adjust By Admin'}}
                            @else
                            @foreach($admin as $adm)
                            @if($adm->id == $allrecord->edited_by)
                             Adjust By Admin ({{$adm->first_name}}{{' '}}{{$adm->last_name}})
                            @endif
                            @endforeach
                            @endif
                            
                            @elseif($allrecord->receiver_id != 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "W2W" )
                            Transfer
                            @elseif($allrecord->receiver_id == 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "Withdraw##Invite_New_User")
                            Invite Pay 
                            @elseif($allrecord->receiver_id != 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "Withdraw##Invite_New_User")
                            Invite Pay 
                            @elseif($allrecord->receiver_id != 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "W2W")
                            Received 
                            @else
                            @if($allrecord->trans_for=='Withdraw##Agent(Reverse)')
                            Withdraw Agent(Reverse)
                            @elseif($allrecord->trans_for=='Converted Amount')
                            Currency Updated
                            @else
                            @php $paymentTypArr = explode('##',$allrecord->trans_for);@endphp
                            @if($paymentTypArr[0]=='SWAP')
                            {{$paymentTypArr[0]}}
                            <br>
                            <?php 
                            $dba_amount=DbaTransaction::where("refrence_id",$allrecord->id)->first();
                            ?>
                            @if($dba_amount)
                            ({{number_format($dba_amount->real_value,2,'.',',').' DBA'}})
                            @else
                            ({{'0.0 DBA'}})
                            @endif
                            @else
                            {{ str_replace("Refund", "Reverse", $paymentTypArr[0])}}
                            @endif
                            @endif
                            @endif
                            @if($allrecord->trans_for == "Mobile Top-up")
                            Mobile Top-up
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
                            {{ 'PendingInvestigation' }}
                            @endif


                        </td>
                        <td data-title="Action">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Action">
                           
                                @php
						
                        $manual_deposits_all=getTransactionrecord($allrecord->id,'manual_deposits');
                        $manual_withdraws_all= getTransactionrecord($allrecord->id,'manual_withdraws');
                        $crypto_deposits_all= getTransactionrecord($allrecord->id,'crypto_deposits');
                        $crypto_withdraws_all= getTransactionrecord($allrecord->id,'crypto_withdraws');

                  
						
						@endphp
                        @if(!empty($allrecord)  && $allrecord->status == 2 && $allrecord->trans_for == "GIFT CARD")
                        <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                             <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu pull-right">
							    @if(is_numeric($allrecord->status) && $allrecord->status == 2)
								<li><a href="#" data-toggle="modal" data-target="#basicModalgift{{$allrecord->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								<li><a href="#"  data-toggle="modal" data-target="#basicModal_rejectgift{{$allrecord->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>
                        @endif
                                </ul></div>

                        @endif

                        @if(!empty($allrecord)  && $allrecord->status == 2 && $allrecord->trans_for == "Mobile Top-up")
                        <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                             <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu pull-right">
							    @if(is_numeric($allrecord->status) && $allrecord->status == 2)
								<li><a href="#" data-toggle="modal" data-target="#basicModalAirtime{{$allrecord->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								<li><a href="#"  data-toggle="modal" data-target="#basicModal_rejectAirtime{{$allrecord->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>
                        @endif
                                </ul></div>

                        @endif
                                @if(!empty($manual_deposits_all) && is_numeric($manual_deposits_all->status) && $manual_deposits_all->status != 3)
                            
                             <div id="loderstatus{{$manual_deposits_all->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                             <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu pull-right">
							   @if(is_numeric($manual_deposits_all->status) && $manual_deposits_all->status == 1)
								<!-- <li><a href="{{URL::to('admin/users/repeat-manual-request/'.$allrecord->id.'/')}}" title="Edit Request" class=""><i class="fa fa-repeat"></i>Repeat Payment</a></li> -->
                                <li><a href="#" data-toggle="modal" data-target="#basicModal_repeat{{$manual_deposits_all->id}}" title="Repeat Payment" class=""><i class="fa fa-check"></i>Repeat Payment</a></li>
								@else
                                <li><a target="_blank" href="{{URL::to('admin/users/edit-manual-request/'.$manual_deposits_all->id.'/')}}" title="Edit Request" class=""><i class="fa fa-edit"></i>Edit</a></li>
								@endif
								
								@if(is_numeric($manual_deposits_all->status) && $manual_deposits_all->status != 1)
								<li><a href="#" data-toggle="modal" data-target="#basicModal{{$manual_deposits_all->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								
								<li><a href="{{URL::to('admin/users/change-manual-deposit-req-status/'.$manual_deposits_all->id.'/2')}}" title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>
								
								<li><a href="#"  data-toggle="modal" data-target="#basicModal_reject{{$manual_deposits_all->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/kycdetail/'.$manual_deposits_all->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/transaction-list/'.$manual_deposits_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a  href="{{ URL::to( 'admin/users/dba-transaction-list/'.$manual_deposits_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>


                                                                @endif
                                </ul></div>
                                @endif



                                @if(!empty($manual_withdraws_all))
                            
                                <div id="loderstatus{{$manual_withdraws_all->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            
                            
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
								<li><a href="#info{!! $manual_withdraws_all->id !!}" title="View Request Detail" class="" rel='facebox'><i class="fa fa-eye"></i>View Request Detail</a></li>
								
								<!-- <li><a href="{{URL::to('admin/users/edit-manual-request/'.$allrecord->id.'/')}}" title="Edit Request" class=""><i class="fa fa-edit"></i>Edit</a></li> -->
																
								@if($manual_withdraws_all->status != 1 && $manual_withdraws_all->status != 3)
								<li><a href="#" data-toggle="modal" data-target="#basicModalw{{$manual_withdraws_all->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								
								<li><a href="{{URL::to('admin/users/change-manual-withdraw-req-status/'.$manual_withdraws_all->id.'/2')}}" title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModal_rejectw{{$manual_withdraws_all->id}}"  title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/kycdetail/'.$manual_withdraws_all->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/transaction-list/'.$manual_withdraws_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a href="{{ URL::to( 'admin/users/dba-transaction-list/'.$manual_withdraws_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>

								@endif
                                </ul>
                            </div>
                               @endif


                               @if(!empty($crypto_deposits_all))
                               <div id="loderstatus{{$crypto_deposits_all->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            
                            
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
								<!-- <li><a href="#info{!! $allrecord->id !!}" title="View Request Detail" class="" rel='facebox'><i class="fa fa-eye"></i>View Request Detail</a></li> -->
								@if($crypto_deposits_all->status != 1 && $crypto_deposits_all->status != 3)
								<li><a target="_blank" href="{{URL::to('admin/users/edit-crypto-request/'.$crypto_deposits_all->id.'/')}}" title="Edit Request" class=""><i class="fa fa-edit"></i>Edit</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModals{{$crypto_deposits_all->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								
								<li><a href="{{URL::to('admin/users/change-crypto-deposit-req-status/'.$crypto_deposits_all->id.'/2')}}" title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModal_rejects{{$crypto_deposits_all->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/kycdetail/'.$crypto_deposits_all->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/transaction-list/'.$crypto_deposits_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a  href="{{ URL::to( 'admin/users/dba-transaction-list/'.$crypto_deposits_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>

								@endif
                                </ul>
                            </div>

                               @endif


                               @if(!empty($crypto_withdraws_all))

                           <div id="loderstatus{{$crypto_withdraws_all->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                           <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
							
								@if($crypto_withdraws_all->status != 1 && $crypto_withdraws_all->status != 3)
							
								
								<li><a href="#" data-toggle="modal" data-target="#basicModalcw{{$crypto_withdraws_all->id}}" title="Change request status to complete" class=""><i class="fa fa-check"></i>Approve</a></li>
								
								<li><a href="{{URL::to('admin/users/change-crypto-withdraw-req-status/'.$crypto_withdraws_all->id.'/2')}}"  title="Change request status to Hold" class=""><i class="fa fa-clock-o"></i>Hold</a></li>
								
								<li><a href="#" data-toggle="modal" data-target="#basicModal_rejectcw{{$crypto_withdraws_all->id}}" title="Change request status to Cancel" class=""><i class="fa fa-ban"></i>Reject</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/kycdetail/'.$crypto_withdraws_all->slug)}}" title="View KYC Details" class=""><i class="fa fa-file"></i>View KYC Details</a></li>

                                <li><a  href="{{ URL::to( 'admin/users/transaction-list/'.$crypto_withdraws_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>Transaction </a></li>
                                   
                                <li><a  href="{{ URL::to( 'admin/users/dba-transaction-list/'.$crypto_withdraws_all->slug)}}" title="Adjust Client Balance" class=""><i class="fa fa-file"></i>DBA Transaction </a></li>


								@endif
                                </ul>
                            </div>


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
                                @if($allrecord->receiver_id == 1 || $allrecord->user_id == 1)
                            @if($allrecord->edited_by==1)
                            {{'Adjust By Admin'}}
                            @elseif($allrecord->user_id==1 && $allrecord->trans_for=='DBA eCash')
                            {{'DBA eCash'}}
                            @elseif($allrecord->user_id==1 && $allrecord->trans_for=="W2W" && strpos($allrecord->billing_description,'AdminBalanceAdjust') !== false)
                            {{'Adjust By Admin'}}
                            @elseif($allrecord->trans_type==2 && $allrecord->trans_for=="W2W" && strpos($allrecord->billing_description,'admin##AdminBalanceAdjust') !== false)
                            {{'Adjust By Admin'}}
                            @else
                            @foreach($admin as $adm)
                            @if($adm->id == $allrecord->edited_by)
                             Adjust By Admin ({{$adm->first_name}}{{' '}}{{$adm->last_name}})
                            @endif
                            @endforeach
                            @endif
                            
                            @elseif($allrecord->receiver_id != 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "W2W")
                            Transfer
                            @elseif($allrecord->receiver_id == 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "Withdraw##Invite_New_User")
                            Invite Pay 
                            @elseif($allrecord->receiver_id != 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "Withdraw##Invite_New_User")
                            Invite Pay 
                            @elseif($allrecord->receiver_id != 0 && $allrecord->trans_type == 2 && $allrecord->trans_for == "W2W")
                            Received 
                            @else
                            @if($allrecord->trans_for=='Withdraw##Agent(Reverse)')
                            Withdraw Agent(Reverse)
                            @elseif($allrecord->trans_for=='Converted Amount')
                            Currency Updated
                            @else
                            @php $paymentTypArr = explode('##',$allrecord->trans_for);@endphp
                            {{ str_replace("Refund", "Reverse", $paymentTypArr[0])}}
                            @endif
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


@if(!empty($allrecord)  && $allrecord->status == 2 && $allrecord->trans_for == "GIFT CARD")
@php $getgiftcarddetail = getgiftcarddetail($allrecord->id);@endphp

@php $userTyp = getUserType($allrecord->user_id);@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($allrecord->User->first_name)).' '.strtoupper(strtoupper($allrecord->User->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($allrecord->User->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($allrecord->User->first_name) ? strtoupper(strtoupper($allrecord->User->first_name)).' '.strtoupper(strtoupper($allrecord->User->last_name)):strtoupper($allrecord->User->director_name) @endphp
@endif

                            

<div class="modal fade" id="basicModalgift{{$allrecord->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Gift Card Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Gift Card'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">

                <div class="form-control-new w100">
                        <label>Product Name</label>
                        <input type="textarea" value="{!! $getgiftcarddetail->productName !!}" id="productName" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Quantity-Unit Price</label>
                        <input type="textarea" value="{!! $getgiftcarddetail->quantity.'-'.$getgiftcarddetail->unitPrice.' '.$getgiftcarddetail->productCurrencyCode !!}" id="productName" placeholder="" disabled>
                    </div>
                    

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                <div class="form-control-new w100">
                        <label>Total Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
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
                <a href="{{URL::to('admin/users/change-giftcard-req-status/'.$allrecord->id.'/1')}}" type="button" class="btn btn-default button_disable{{$allrecord->id}}" onclick="disable_submitgift_{{$allrecord->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submitgift_{{$allrecord->id}}()
{    
$('.button_disable{{$allrecord->id}}').attr('disabled', true); 
$('.button_disable{{$allrecord->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_rejectgift{{$allrecord->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Gift Card Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Gift Card'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">

                <div class="form-control-new w100">
                        <label>Product Name</label>
                        <input type="textarea" value="{!! $getgiftcarddetail->productName !!}" id="productName" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Quantity-Unit Price</label>
                        <input type="textarea" value="{!! $getgiftcarddetail->quantity.'-'.$getgiftcarddetail->unitPrice.' '.$getgiftcarddetail->productCurrencyCode !!}" id="productName" placeholder="" disabled>
                    </div>
                    

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                <div class="form-control-new w100">
                        <label>Total Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
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
                <a href="{{URL::to('admin/users/change-giftcard-req-status/'.$allrecord->id.'/3')}}" type="button" class="btn btn-default button_disable{{$allrecord->id}}" onclick="disable_submitgift_{{$allrecord->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endif



@if(!empty($allrecord)  && $allrecord->status == 2 && $allrecord->trans_for == "Mobile Top-up")  @endphp
@php $userTyp = getUserType($allrecord->user_id);@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($allrecord->User->first_name)).' '.strtoupper(strtoupper($allrecord->User->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($allrecord->User->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($allrecord->User->first_name) ? strtoupper(strtoupper($allrecord->User->first_name)).' '.strtoupper(strtoupper($allrecord->User->last_name)):strtoupper($allrecord->User->director_name) @endphp
@endif

                            

<div class="modal fade" id="basicModalAirtime{{$allrecord->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Mobile Top-up Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Gift Card'}}" disabled>
                    </div>

                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                <div class="form-control-new w100">
                        <label>Total Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
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
                <a href="{{URL::to('admin/users/change-airtime-req-status/'.$allrecord->id.'/1')}}" type="button" class="btn btn-default button_disable{{$allrecord->id}}" onclick="disable_submitairtime_{{$allrecord->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submitairtime_{{$allrecord->id}}()
{    
$('.button_disable{{$allrecord->id}}').attr('disabled', true); 
$('.button_disable{{$allrecord->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_rejectAirtime{{$allrecord->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Mobile Top-up Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Airtime'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                <div class="form-control-new w100">
                        <label>Total Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
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
                <a href="{{URL::to('admin/users/change-airtime-req-status/'.$allrecord->id.'/3')}}" type="button" class="btn btn-default button_disable{{$allrecord->id}}" onclick="disable_submitairtime_{{$allrecord->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endif





 
@php  $manual_deposits_all=getTransactionrecord($allrecord->id,'manual_deposits');  @endphp

@if(!empty($manual_deposits_all))
@php $userTyp = $manual_deposits_all->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($manual_deposits_all->first_name)).' '.strtoupper(strtoupper($manual_deposits_all->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($manual_deposits_all->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($manual_deposits_all->first_name) ? strtoupper(strtoupper($manual_deposits_all->first_name)).' '.strtoupper(strtoupper($manual_deposits_all->last_name)):strtoupper($manual_deposits_all->director_name) @endphp
@endif

<div class="modal fade" id="basicModal_repeat{{$manual_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
    {{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm','class' => '','url'=>'admin/users/repeat-manual-request/'.$manual_deposits_all->id)) }}
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Manual Deposit Repeat Payment</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Manual Deposit'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $manual_deposits_all->currency.' '.$manual_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                  
                    <div class="form-control-new w100">
                        <label>Edit Amount</label>
                        <input type="text" name="edit_amount" value="{!! $allrecord->amount !!}" onkeypress="return validateFloatKeyPress(this,event);" id="edit_amount" placeholder="" required autocomplete="off">
                    </div>

                    <input type="hidden" name="page" value="report">

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $manual_deposits_all->bank_name !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$manual_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <button type="submit" class="btn btn-default bg-btn">Confirm</button>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        </form>
    </div>
</div>

<div class="modal fade" id="basicModal{{$manual_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Manual Deposite Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Manual Deposit'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $manual_deposits_all->currency.' '.$manual_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $manual_deposits_all->bank_name !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                      

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$manual_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-manual-deposit-req-status/'.$manual_deposits_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$manual_deposits_all->id}}" onclick="disable_submit_{{$manual_deposits_all->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submit_{{$manual_deposits_all->id}}()
{    
$('.button_disable{{$manual_deposits_all->id}}').attr('disabled', true); 
$('.button_disable{{$manual_deposits_all->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   


<div class="modal fade" id="basicModal_reject{{$manual_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Manual Deposite Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Manual Deposite'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $manual_deposits_all->currency.' '.$manual_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $manual_deposits_all->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                       

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$manual_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-manual-deposit-req-status/'.$manual_deposits_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$manual_deposits_all->id}}" onclick="disable_submit_{{$manual_deposits_all->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endif


@php   $manual_withdraws_all= getTransactionrecord($allrecord->id,'manual_withdraws');  @endphp

@if(!empty($manual_withdraws_all))
@php 
$account = getAccountById($manual_withdraws_all->account_id);

$user = getUserByUserId($manual_withdraws_all->user_id);

@endphp
<div id="info{!! $manual_withdraws_all->id !!}" style="display: none;">
    <div class="nzwh-wrapper">
        <fieldset class="nzwh">
            <legend class="head_pop">{!! strtoupper($manual_withdraws_all->user_name) !!}</legend>
            <div class="drt">
			
            @if($account->type_transfer)
                <div class="admin_pop"><span>Bank Transfer Type: </span>  <label> {{$account->type_transfer}} </label></div>
                @endif

                @if($account->bank_name)
                <div class="admin_pop"><span>Bank name: </span>  <label> {{$account->bank_name}} </label></div>
                @endif
                @if($account->bnkAdd)
                <div class="admin_pop"><span>Bank Address: </span>  <label> {{$account->bnkAdd}} </label></div>
                @endif

                @if($account->account_name)
                <div class="admin_pop"><span>Account holder: </span>  <label>{!! $account->account_name !!}</label></div>
                @endif
                @if($account->account_number)
                <div class="admin_pop"><span>Account number: </span>  <label>{!! $account->account_number !!}</label></div>
                @endif
                @if($account->branch_code)
                <div class="admin_pop"><span>Branch code: </span>  <label>{!! $account->branch_code !!}</label></div>
                @endif
                @if($user->country)
                <div class="admin_pop"><span>Country: </span>  <label>{!! $user->country !!}</label></div>
                @endif
                @if($account->account_currency)
				<div class="admin_pop"><span>Account currency: </span>  <label>{!! $account->account_currency !!}</label></div>
                @endif
                @if($account->account_type)
                <div class="admin_pop"><span>Account Type: </span>  <label>{{$account->account_type}}</label></div>
                @endif
                @if($account->routing_number)
                <div class="admin_pop"><span>Routing number: </span>  <label>{{$account->routing_number}}</label></div>
                @endif
                @if($account->reasonPay)
                <div class="admin_pop"><span>Reference: </span>  <label>{{$account->reasonPay}}</label></div>
                @endif
                @if($account->iBan)
                <div class="admin_pop"><span>IBAN Number : </span>  <label>{{$account->iBan}}</label></div>
                @endif
                @if($account->bic)
                <div class="admin_pop"><span>BIC : </span>  <label>{{$account->bic}}</label></div>
                @endif
                @if($account->sorCode)
                <div class="admin_pop"><span>Sort Code : </span>  <label>{{$account->sorCode}}</label></div>
                @endif
                @if($account->wisaEmail)
                <div class="admin_pop"><span>Wisa Email : </span>  <label>{{$account->wisaEmail}}</label></div>
                @endif
                @if($account->cotb)
                <div class="admin_pop"><span>Country of The Bank : </span>  <label>{{$account->cotb}}</label></div>
                @endif
                @if($account->swc)
                <div class="admin_pop"><span>Swift Code  : </span>  <label>{{$account->swc}}</label></div>
                @endif
                
				<!--<div class="admin_pop"><span>User name: </span>  <label>{{$allrecord->user_name}}</label></div> -->
				<div class="admin_pop"><span>User currency: </span>  <label>{{$user->currency}}</label></div>
				<div class="admin_pop"><span>Withdraw amount: </span>  <label>{{$user->currency.' '.$manual_withdraws_all->amount}}</label></div>
				<div class="admin_pop"><span>Available Balance: </span>  <label>{{$user->currency .number_format($user->wallet_amount,10,'.',',')}}</label></div>
                <div class="admin_pop"><span>Date: </span>  <label>{{$manual_withdraws_all->created_at}}</label></div>
@if($manual_withdraws_all->status != 1 && $manual_withdraws_all->status != 3)
<div class="row" style="margin-top:20px;">
    <div class="col-12 text-center withdraw-buttn">
        <button class="btn btn-primary btn-lg" onclick="openClosePopup('basicModalw','{{$manual_withdraws_all->id}}')" type="button">Approve </button>
        <button class="btn btn-primary btn-lg"  onclick="openClosePopup('basicModal_rejectw','{{$manual_withdraws_all->id}}')" type="button">Reject </button>
    </div>
  </div>
@endif  
        </fieldset>
		<!-- Button start -->
		<!-- Button end -->
    </div>
</div>

@php $userTyp = $manual_withdraws_all->user_type; @endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($manual_withdraws_all->first_name)).' '.strtoupper(strtoupper($manual_withdraws_all->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($manual_withdraws_all->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($manual_withdraws_all->first_name) ? strtoupper(strtoupper($manual_withdraws_all->first_name)).' '.strtoupper(strtoupper($manual_withdraws_all->last_name)):strtoupper($manual_withdraws_all->director_name) @endphp
@endif

<div class="modal fade" id="basicModalw{{$manual_withdraws_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
            <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br><?php if($manual_withdraws_all->withdraw_type==0) { ?> Manual Withdraw Approval <?php }elseif($manual_withdraws_all->withdraw_type==1){
                    ?>Global Pay Withdraw Approval<?php }else{ ?>3rd Party Pay Withdraw Approval  <?php } ?></h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Manual Withdraw'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
               
                <div class="form-control-new w100">
                        <label>Bank Transfer Type</label>
                        <input type="text" value="{!! $account->type_transfer  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Account holder</label>
                        <input type="text" value="{!! $account->account_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                @if($account->type_transfer=="US Bank Transfer")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Routing number</label>
                        <input type="text" value="{!! $account->routing_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                @elseif($account->type_transfer=="UK Bank Transfer")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>IBAN Number</label>
                        <input type="text" value="{!! $account->iBan  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Sort Code</label>
                        <input type="text" value="{!! $account->sorCode  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                </div>

                @elseif($account->type_transfer=="IBAN EU Transfer")
                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>BIC</label>
                        <input type="text" value="{!! $account->bic  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>IBAN Number</label>
                        <input type="text" value="{!! $account->iBan  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>

                @elseif($account->type_transfer=="Transfer To Wise")
                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Wisa Email</label>
                        <input type="text" value="{!! $account->wisaEmail  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                </div>


                @elseif($account->type_transfer=="Nigeria Bank Transfer")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>

                @elseif($account->type_transfer=="SA Bank Transfer")
               
                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">  
                <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box"> 
               <div class="form-control-new w100">
                       <label>Branch Code</label>
                       <input type="text" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                   </div>

                   <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
               </div>

               <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>
                </div>

                @elseif($account->type_transfer=="Bank Wire Transfer (Global)")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Swift Code</label>
                        <input type="text" value="{!! $account->swc  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Branch Code/Routing/Sortcode</label>
                        <input type="textarea" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Country of The Bank</label>
                        <input type="text" value="{{$account->cotb}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                </div>

                @elseif($account->type_transfer=="Botswana Bank Transfer" || $account->type_transfer=="Swaziland Bank Transfer" || $account->type_transfer=="Lesotho Bank Transfer"  || $account->type_transfer=="Namibia Bank Transfer")
               
                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

               <div class="filed-box">   
               <div class="form-control-new w100">
                       <label>Account Type</label>
                       <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                   </div>
                   <div class="form-control-new w100">
                       <label>Currency</label>
                       <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                   </div>
               </div>

               <div class="filed-box"> 
              <div class="form-control-new w100">
                      <label>Branch Code</label>
                      <input type="text" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                  </div>

                  <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
              </div>

              <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>

              @else

                    <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Bank Account</label>
                        <input type="text" value="{!! $account->account_number !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Branch code</label>
                        <input type="text" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                </div>

                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Country</label>
                        <input type="text" value="{!! $user->country !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>currency</label>
                        <input type="text" value="{!! $account->account_currency  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    
                    <div class="form-control-new w100">
                        <label>Routing number</label>
                        <input type="text" value="{!! $account->routing_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>


                @endif
                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to approve this request?
                        </label>
                    </div>  
                    </div>  
            </div>
            <div class="modal-footer pop-ok">
                <?php if($manual_withdraws_all->withdraw_type==0) { ?> 
                <a href="{{URL::to('admin/users/change-manual-withdraw-req-status/'.$manual_withdraws_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$manual_withdraws_all->id}}" onclick="disable_submitw_{{$manual_withdraws_all->id}}()" >Confirm</a>
                <?php }else{ ?>
                <a href="{{URL::to('admin/users/change-global-withdraw-req-status/'.$manual_withdraws_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$manual_withdraws_all->id}}" onclick="disable_submitw_{{$manual_withdraws_all->id}}()" >Confirm</a>
                <?php  }  ?>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submitw_{{$manual_withdraws_all->id}}()
{    
$('.button_disable{{$manual_withdraws_all->id}}').attr('disabled', true); 
$('.button_disable{{$manual_withdraws_all->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   


<div class="modal fade" id="basicModal_rejectw{{$manual_withdraws_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
            <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br><?php if($manual_withdraws_all->withdraw_type==0) { ?> Manual Withdraw Reject <?php }elseif($manual_withdraws_all->withdraw_type==1){
                    ?>Global Pay Withdraw Reject<?php }else{ ?>3rd Party Pay Withdraw Reject  <?php } ?></h4>
              
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Manual Withdraw'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
               
                <div class="form-control-new w100">
                        <label>Bank Transfer Type</label>
                        <input type="text" value="{!! $account->type_transfer  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Account holder</label>
                        <input type="text" value="{!! $account->account_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                @if($account->type_transfer=="US Bank Transfer")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Routing number</label>
                        <input type="text" value="{!! $account->routing_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                @elseif($account->type_transfer=="UK Bank Transfer")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>IBAN Number</label>
                        <input type="text" value="{!! $account->iBan  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Sort Code</label>
                        <input type="text" value="{!! $account->sorCode  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                </div>

                @elseif($account->type_transfer=="IBAN EU Transfer")
                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>BIC</label>
                        <input type="text" value="{!! $account->bic  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>IBAN Number</label>
                        <input type="text" value="{!! $account->iBan  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>

                @elseif($account->type_transfer=="Transfer To Wise")
                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Wisa Email</label>
                        <input type="text" value="{!! $account->wisaEmail  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                </div>


                @elseif($account->type_transfer=="Nigeria Bank Transfer")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>

                @elseif($account->type_transfer=="SA Bank Transfer")
               
                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">  
                <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                    <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box"> 
               <div class="form-control-new w100">
                       <label>Branch Code</label>
                       <input type="text" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                   </div>

                   <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
               </div>

               <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>
                </div>

                @elseif($account->type_transfer=="Bank Wire Transfer (Global)")

                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">   
                <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Currency</label>
                        <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Swift Code</label>
                        <input type="text" value="{!! $account->swc  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                
                   <div class="form-control-new w100">
                        <label>Bank Address</label>
                        <input type="textarea" value="{!! $account->bnkAdd  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                   <div class="form-control-new w100">
                        <label>Reference</label>
                        <input type="textarea" value="{!! $account->reasonPay  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Branch Code/Routing/Sortcode</label>
                        <input type="textarea" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Country of The Bank</label>
                        <input type="text" value="{{$account->cotb}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   
                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                </div>

                @elseif($account->type_transfer=="Botswana Bank Transfer" || $account->type_transfer=="Swaziland Bank Transfer" || $account->type_transfer=="Lesotho Bank Transfer"  || $account->type_transfer=="Namibia Bank Transfer")
               
                <div class="filed-box">
                <div class="form-control-new w100">
                        <label>Bank Name</label>
                        <input type="text" value="{!! $account->bank_name  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                   <div class="form-control-new w100">
                        <label>Account number</label>
                        <input type="text" value="{!! $account->account_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

               <div class="filed-box">   
               <div class="form-control-new w100">
                       <label>Account Type</label>
                       <input type="text" value="{!! $account->account_type  !!}" id="recipEmail" placeholder="" disabled>
                   </div>
                   <div class="form-control-new w100">
                       <label>Currency</label>
                       <input type="text" value="{!! $account->currncy  !!}" id="recipEmail" placeholder="" disabled>
                   </div>
               </div>

               <div class="filed-box"> 
              <div class="form-control-new w100">
                      <label>Branch Code</label>
                      <input type="text" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                  </div>

                  <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
              </div>

              <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>

              @else

                    <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Bank Account</label>
                        <input type="text" value="{!! $account->account_number !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Branch code</label>
                        <input type="text" value="{!! $account->branch_code  !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                </div>

                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Country</label>
                        <input type="text" value="{!! $user->country !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>currency</label>
                        <input type="text" value="{!! $account->account_currency  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>

                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Account Type</label>
                        <input type="text" value="{!! $account->account_type !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    
                    <div class="form-control-new w100">
                        <label>Routing number</label>
                        <input type="text" value="{!! $account->routing_number  !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                </div>


                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $allrecord->currency.' '.$allrecord->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>

                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$allrecord->created_at}}" id="recipAmountTF" placeholder="" disabled>
                    </div>  
                   
                </div>


                @endif

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>
                            Do you want to reject this request?
                        </label>
                    </div>  
                    </div>  
            </div>
            <div class="modal-footer pop-ok">
                <?php if($manual_withdraws_all->withdraw_type==0) { ?> 
                <a href="{{URL::to('admin/users/change-manual-withdraw-req-status/'.$manual_withdraws_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$manual_withdraws_all->id}}" onclick="disable_submitw_{{$manual_withdraws_all->id}}()"  >Confirm</a>
                <?php }else{ ?>
                <a href="{{URL::to('admin/users/change-global-withdraw-req-status/'.$manual_withdraws_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$manual_withdraws_all->id}}" onclick="disable_submitw_{{$manual_withdraws_all->id}}()"  >Confirm</a>
                <?php  } ?>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


@endif






@php  $crypto_deposits_all= getTransactionrecord($allrecord->id,'crypto_deposits'); @endphp

@if(!empty($crypto_deposits_all))


@php  $userTyp = $crypto_deposits_all->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($crypto_deposits_all->first_name)).' '.strtoupper(strtoupper($crypto_deposits_all->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($crypto_deposits_all->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($crypto_deposits_all->first_name) ? strtoupper(strtoupper($crypto_deposits_all->first_name)).' '.strtoupper(strtoupper($crypto_deposits_all->last_name)):strtoupper($crypto_deposits_all->director_name) @endphp
@endif

<div class="modal fade" id="basicModals{{$crypto_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Crypto Deposit Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Crypto Deposit'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $crypto_deposits_all->crypto_currency.' '.$crypto_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$crypto_deposits_all->blockchain_url}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$crypto_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-crypto-deposit-req-status/'.$crypto_deposits_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$crypto_deposits_all->id}}" onclick="disable_submits_{{$crypto_deposits_all->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function disable_submits_{{$crypto_deposits_all->id}}()
{    
$('.button_disable{{$crypto_deposits_all->id}}').attr('disabled', true); 
$('.button_disable{{$crypto_deposits_all->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_rejects{{$crypto_deposits_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Crypto Deposit Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Crypto Deposit'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $crypto_deposits_all->crypto_currency.' '.$crypto_deposits_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$crypto_deposits_all->blockchain_url}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$crypto_deposits_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-crypto-deposit-req-status/'.$crypto_deposits_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$crypto_deposits_all->id}}" onclick="disable_submits_{{$crypto_deposits_all->id}}()"  >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


@endif






@php   $crypto_withdraws_all= getTransactionrecord($allrecord->id,'crypto_withdraws'); @endphp

@if(!empty($crypto_withdraws_all))


@php  $userTyp = $crypto_withdraws_all->user_type;@endphp
@if($userTyp == 'Personal')
@php $userName = strtoupper(strtoupper($crypto_withdraws_all->first_name)).' '.strtoupper(strtoupper($crypto_withdraws_all->last_name)) @endphp
@elseif($userTyp == 'Business')
@php $userName = strtoupper($crypto_withdraws_all->business_name) @endphp
@elseif($userTyp == 'Agent')
@php $userName = isset($crypto_withdraws_all->first_name) ? strtoupper(strtoupper($crypto_withdraws_all->first_name)).' '.strtoupper(strtoupper($crypto_withdraws_all->last_name)):strtoupper($crypto_withdraws_all->director_name) @endphp
@endif
<div class="modal fade" id="basicModalcw{{$crypto_withdraws_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Crypto Withdrawal Approval</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Crypto Withdraw'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $crypto_withdraws_all->crypto_currency.' '.$crypto_withdraws_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$crypto_withdraws_all->payout_addrs}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$crypto_withdraws_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-crypto-withdraw-req-status/'.$crypto_withdraws_all->id.'/1')}}" type="button" class="btn btn-default button_disable{{$crypto_withdraws_all->id}}" onclick="disable_submitcw_{{$crypto_withdraws_all->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
function disable_submitcw_{{$crypto_withdraws_all->id}}()
{    
$('.button_disable{{$crypto_withdraws_all->id}}').attr('disabled', true); 
$('.button_disable{{$crypto_withdraws_all->id}}').css({'pointer-events':'none','cursor': 'default'});
}
</script>   



<div class="modal fade" id="basicModal_rejectcw{{$crypto_withdraws_all->id}}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Crypto Withdrawal Reject</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Support type </label>
                        <input type="text" id="recipName" value="{{'Crypto Withdraw'}}" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>User Name </label>
                        <input type="text" value="{!! strtoupper($userName) !!}" id="recipAccNum" placeholder="" disabled>
                    </div>
                </div>
                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label>Amount</label>
                        <input type="text" value="{!! $crypto_withdraws_all->crypto_currency.' '.$crypto_withdraws_all->amount !!}" id="recipEmail" placeholder="" disabled>
                    </div>
                    <div class="form-control-new w100">
                        <label>Blockchain URL</label>
                        <input type="text" value="{{$crypto_withdraws_all->payout_addrs}}" id="recipAmountTF" placeholder="" disabled>
                    </div>   

                </div>

                <div class="filed-box" id="cuncyConvrsnTF">
                    <div class="form-control-new w100">
                        <label>Date</label>
                        <input type="text" value="{{$crypto_withdraws_all->created_at}}" id="recipAmountTF" placeholder="" disabled>
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
                <a href="{{URL::to('admin/users/change-crypto-withdraw-req-status/'.$crypto_withdraws_all->id.'/3')}}" type="button" class="btn btn-default button_disable{{$crypto_withdraws_all->id}}" onclick="disable_submitcw_{{$crypto_withdraws_all->id}}()" >Confirm</a>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endif


@endforeach

@endif



<script>
function validateFloatKeyPress(el, evt) {
     var charCode = (evt.which) ? evt.which : event.keyCode;
     var number = el.value.split('.');
     if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
         return false;
     }
     //just one dot
     if (number.length > 1 && charCode == 46) {
         return false;
     }
     //get the carat position
     var caratPos = getSelectionStart(el);
     var dotPos = el.value.indexOf(".");
     if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1)) {
         return false;
     }
     return true;
 }

 function getSelectionStart(o) {
     if (o.createTextRange) {
         var r = document.selection.createRange().duplicate()
         r.moveEnd('character', o.value.length)
         if (r.text == '')
             return o.value.length
         return o.value.lastIndexOf(r.text)
     } else
         return o.selectionStart
 }

 $( document ).ready(function() {
 window.onload = () => {
 const myInput = document.getElementById('edit_amount');
 myInput.onpaste = e => e.preventDefault();
}
});

</script>    



