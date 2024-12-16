@extends('layouts.inner')
@section('content')
<script>
    $(document).ready(function () {
    $("#uplaodprofileimg").on('change', function (event) {
            var postData = new FormData(this);
            event.preventDefault();
            $.ajax({
                url: "{!! HTTP_PATH !!}/users/uploadprofileimage",
                type: "POST",
                data: postData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#ploader').show();
                },
                success: function (imagename) {
                    $('#pimage').attr("src", "{!! PROFILE_FULL_DISPLAY_PATH !!}" + imagename);
                    $('#pimage1').attr("src", "{!! PROFILE_FULL_DISPLAY_PATH !!}" + imagename);
                    $('#ploader').hide();
                }
            });
        });
        $("#account_detail_form").validate();   
    });</script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-6 mob-big">
                    <div class="heading-section">
                        <h5>Account details</h5>
                    </div>
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h2>
                            <h6>@if($recordInfo->user_type == 'Personal')
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Business')
                                @include('elements.business_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                                @include('elements.business_short_name')
                                @endif</h6>
                        </div>
                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>
                    <br>
                </div>
                <div class="col-sm-6 mob-big">
                    <div class="heading-section">
                        <h5>Profile Picture</h5>
                    </div>
                    <div class="profile-dp">
                        {{ Form::open(array('method' => 'post', 'id' => 'uplaodprofileimg', 'enctype' => "multipart/form-data")) }}
                        <div class="profiledp-box">
                            <div class="dp-new">
                                @if(isset($recordInfo->image))
                                {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->image, SITE_TITLE, ['id'=> 'pimage'])}}
                                @else
                                {{HTML::image('public/img/front/no-user.png', SITE_TITLE, ['id'=> 'pimage'])}}
                                @endif
                            </div>

                        </div>
                        <div class="edit-img">
                            {{Form::file('image', ['class'=>'form-control', 'accept'=>IMAGE_EXT, 'id'=>'profile_image'])}}
                            <a href="#"> {{HTML::image('public/img/front/pencil.svg', SITE_TITLE)}}</a>
                        </div>
                        {{ Form::close()}}   
                    </div>
                </div>
                <div class="col-sm-12">
                {{ Form::open(array('method' => 'post', 'enctype' => "multipart/form-data",'id'=>'account_detail_form')) }}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Account number</span>
                                <h6>{{$recordInfo->account_number}}</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Account holder name</span>
                                <h6>{{strtoupper($recordInfo->gender)}} {{$accountName}}</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Communication address</span>
                                <h6>{{$recordInfo->addrs_line1.", ".$recordInfo->addrs_line2}}</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Mobile number</span>
                                <h6>{{$recordInfo->phone}}</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Email</span>
                                <h6>{{$recordInfo->email}}</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>KYC details</span>
                                <h6><a href="{{URL::to('auth/kyc-detail')}}" style="color:#000;">View my KYC details</a></h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Share details</span>
                                <h6 style="cursor:pointer;" onclick="copyTextToClipboard('{{'Account number: '.$recordInfo->account_number.'\n\n'.'Account holder name: '.$recordInfo->gender.' '.ucwords($accountName).'\n\n'.'Mobile number: '.$recordInfo->phone.'\n\n'.'Email: '.$recordInfo->email}}');">Share my account details</h6>
                            </div>
                        </div>
                        @if($recordInfo->user_type == 'Agent')
                        <div class="col-sm-6">
                            <div class="account-detail-info">
                                <span>Edit Agent Details</span>
                                <h6><a href="{{URL::to('auth/edit-agent-details')}}" style="color:#000;">Edit Agent Details</a></h6>
                            </div>
                        </div>
                        @else
                        @endif

                        @if(empty($recordInfo->gender))
                        <div class="col-sm-6">
                        <div class="account-detail-info">
                        <span>Select Prefix</span>
                        <div class="gender-select">      
                             <?php global $sernameList; ?>   
                            {{Form::select('gender', $sernameList,null, ['class' => 'required','placeholder' => 'Select Prefix'])}}  
                            </div>      
                        </div>            
                        </div>        

                        <div class="col-sm-12">
                            <div class="account-details-btn text-center">
                                <button class="sub-btn button_disable" type="submit" id='step_1'>
                                    Update   
                                </button>
                            </div>
                        </div>   
                        @endif  

                    </div>
                    {{ Form::close()}}
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
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
                                    //                                    alert("Your account details copied successfully");
                                    $('#blank_message').html('Your account details copied successfully');
                                    $('#blank-alert-Modal').modal('show');
                                    //console.log('Copying text command was ' + msg);
                                    } catch (err) {
                                    console.log('Oops, unable to copy');
                                    }

                                    document.body.removeChild(textArea);
                                    }
</script>
@endsection