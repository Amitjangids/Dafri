@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-12">
                    <div class="heading-section">
                        <h5>Affiliate program</h5>
                    </div>
                </div>
                <div class="table-box col-sm-12">
                    <h6>Summary</h6>
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Balance</th>
                                <th scope="col">Turnover</th>
                                <th scope="col">Earnings</th>
                                <th scope="col">Withdrawal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>     
                                <td>
                                    @php
                        $res = getAffiliateBalanceByUserId($recordInfo->id);
                        $used_fund=getUsedAffiliateBalanceByUserId($recordInfo->id);
                    @endphp
                    @if($res)USD {{number_format($res-$used_fund,10,'.',',')}}@else USD 0.00 @endif
                                </td>
                                <td>@php
                        $res = getAffiliateBalanceByUserId($recordInfo->id);
                    @endphp
                    @if($res)USD {{number_format($res,10,'.',',')}}@else USD 0.00 @endif</td>
                                <td>25%</td>
                                <td>Min. USD 0.015</td>
                            </tr>
                        </tbody>
                    </table>

                    <a class="affiliate_btn" href="{{DBA_WEBSITE}}/autologin?enctype={{ $enc_user_id }}&api_token=token&action=auth/affiliate-swap" target="_blank">Swap Fund</a>

                </div>

                <div class="table-box col-sm-12">
                    <div class="ref-link">
                        <div class="ref-link-box">
                            <h6>My referral links</h6>
                            <span>
                                Engage more users by placing referral link on your website.
                            </span>
                        </div>
                        <?php if($refCodes_exist==0) { ?>
                        <a href="{{URL::to('auth/generateReferral')}}">Generate link</a>
                        <?php } ?>
                    </div>
                    <div class="table-responsive">


                        <table class="table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">First name</th>
                                    <th scope="col">Link</th>
                                    <th scope="col">Registration</th>
                                    <th scope="col">USD Profit</th>
                                    <th scope="col">USD Turnover</th>
                                </tr>
                            </thead>
                            @if (!empty($refCodes))
                            <tbody>
                                @foreach ($refCodes as $refcode)
                                <tr>
                                    <td>{{$refcode->referal_name}}</td>
                                    <td>
                                    <?php if($referalCode==$refcode->referal_link)
                                    { ?>
                                    <a id="refLink_{{$refcode->id}}" href="{{URL::to('choose-account?'.$refcode->referal_link)}}">{{$refcode->referal_link}}</a>
                                    <?php } else{  echo $refcode->referal_link; } ?>
                                
                                    </td>
                                    <td>{{$refcode->num_register}}<a href="{{URL::to('auth/referral-detail?'.$refcode->referal_link)}}" target="_blank"  class="cp-link" >View</a> </td>
                                    <td>0.0000000000</td>
                                    <td>0.0000000000 
                                        <!-- <a href="javascript:deleteRefCode('{{$refcode->id}}');">{{HTML::image('public/img/front/delete.svg', 'Delete Link')}}</a> -->
                                        <?php if($referalCode==$refcode->referal_link)
                                        { ?>
                                        <a href="javascript:void(0);" class="cp-link" onclick="copyTextToClipboard('{{URL::to('choose-account?'.$refcode->referal_link)}}');">Copy</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @endif
                        </table>
                    </div>
                    <p class="stat-pata">Our affiliate partners are entitled to a whopping 25% of the total revenue we earn on fees from transactions carried out by each of their customers.</p>
                </div>

                <div class="table-box col-sm-12">
                    <h6>User statistics</h6>
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Source</th>
                                <th scope="col">Actions</th>
                                <th scope="col">USD Profit</th>
                                <th scope="col">USD Turnover</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($refrlHistry as $refHistry)
                            @php    
                            $date = date_create($refHistry->created_at);
                            $refDate = date_format($date,'d M Y');

                            $res = getUserByUserId($refHistry->user_id);
                            if ($res == false) {
                            continue;	 
                            }
                            @endphp	
                            <?php 
                          //  echo "<pre>";
                          //  print_r($res); ?>
                            <tr>
                                <td class="stat">{{ $refDate }}</td>
                                <td class="stat">
                                    @if($res->user_type == 'Personal')
                                    {{ strtoupper(strtolower($res->first_name." ".$res->last_name))}}
                                    @elseif($res->user_type == 'Business')
                                    {{ strtoupper(strtolower($res->business_name)) }}
                                    @elseif($res->user_type == 'Agent' && $res->first_name != "")
                                    {{ strtoupper(strtolower($res->first_name." ".$res->last_name))}}
                                    @elseif($res->user_type == 'Agent' && $res->business_name != "")
                                    {{ strtoupper(strtolower($res->business_name)) }}
                                    @endif
                                </td>
                                <td class="stat">{{ $refHistry->action }}</td>
                                <td class="stat">{{ number_format($refHistry->amount,10,'.',',') }}</td>
                                <td class="stat"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="stat-pata">*All amount have been calculated between 00:00 and 23:59 UTC</p>
                </div>
            
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script>
                                function deleteRefCode(refCode)
                                {
                                    $('#confim_btn').attr('onClick', 'deleteFunc(' + refCode + ')');
                                    $('#basicModal').modal('show');
//                            var a = confirm('Do you really want to delete this referral code? All the benifits of this referral will be flused.');
//                            if (a) {
//                                location.href = '/dafri/auth/deleteRefCode/' + refCode;
//                            }
                                }

                                function deleteFunc(refCode) {
                                    location.href = '<?php echo HTTP_PATH; ?>/auth/deleteRefCode/' + refCode;
                                }

                                function myFunction() {
                                    /* Get the text field */
                                    var copyText = document.getElementById("myInput");

                                    /* Select the text field */
                                    copyText.select();
                                    copyText.setSelectionRange(0, 99999); /* For mobile devices */

                                    /* Copy the text inside the text field */
                                    document.execCommand("copy");

                                    /* Alert the copied text */
//                            alert("Copied the text: " + copyText.value);
                                    $('#blank_message').html("Copied the text: " + copyText.value);
                                    $('#blank-alert-Modal').modal('show');
                                }
                                
                                function copyTextToClipboard(text) {
                                    var textArea = document.createElement("textarea");
                                    textArea.value = text
                                            document.body.appendChild(textArea);
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                    var successful = document.execCommand('copy');
                                    var msg = successful ? 'successful' : 'unsuccessful';
//                                    alert("Your account details copied successfully");
                                    $('#blank_message').html('Your referral link copied successfully');
                                                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.body.removeChild(textArea);
                                    }
</script>

<!-- basic modal -->
<div class="modal x-dialog fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body ">
                <p class="text-center"><strong>Do you really want to delete this referral code? All the benefits of this referral will be flushed.</strong></p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><button type="button" class="btn btn-dark" id="confim_btn" onclick="deleteFunc();">OK</button></li>
                    <li class="list-inline-item"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection