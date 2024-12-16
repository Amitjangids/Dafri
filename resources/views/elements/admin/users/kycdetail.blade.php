{{ HTML::script('public/assets/js/jquery.fancybox.js?v=2.1.5')}}
{{ HTML::style('public/assets/css/jquery.fancybox.css?v=2.1.5')}}
<script type="text/javascript">

    $(document).ready(function () {
        $('.fancybox').fancybox();
    });
</script>

@if($userInfo->identity_image != '' || $userInfo->address_document != '' || $userInfo->profile_image != '')

<div class="panel-body marginzero">
    <!-- <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div> -->
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">KYC Details List</div>
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">

            </div>                
        </div>
        <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th class="sorting_paging">Identity Type</th>
                        <th class="sorting_paging">Identity Number</th>
                        <th class="sorting_paging">Picture national identity</th>
                        <th class="sorting_paging">Status</th>
                        <th class="sorting_paging">Date</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @if($userInfo->identity_card_type == 'Passport')
                    <tr>
                        <td data-title="Identity Type">{{$userInfo->identity_card_type}}</td>
                        <td data-title="National Identity Number">{{$userInfo->national_identity_number}}</td>
                        <td data-title="Picture national identity">    
                            <a href="{{IDENTITY_FULL_DISPLAY_PATH.$userInfo->identity_image}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($userInfo->identity_image, PATHINFO_EXTENSION);
                                @endphp
                                @if($userInfo->identity_image == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$userInfo->identity_image, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                        </td>
                        <td data-title="Status">
                            @if($userInfo->identity_status == 1)
                            Approved
                            @elseif($userInfo->identity_status == 2)
                            Declined
                            @else
                            Pending
                            @endif
                        </td>
                        <td data-title="Date">{{$userInfo->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                    
                    @else
                    
                    <tr>
                        <td data-title="Identity Type">{{$userInfo->identity_card_type}} Front</td>
                        <td data-title="National Identity Number">{{$userInfo->national_identity_number}}</td>
                        <td data-title="Picture national identity">    
                            <a href="{{IDENTITY_FULL_DISPLAY_PATH.$userInfo->identity_image}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($userInfo->identity_image, PATHINFO_EXTENSION);
                                @endphp
                                @if($userInfo->identity_image == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$userInfo->identity_image, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                        </td>
                        <td data-title="Status">
                            @if($userInfo->identity_status == 1)
                            Approved
                            @elseif($userInfo->identity_status == 2)
                            Declined
                            @else
                            Pending
                            @endif
                        </td>
                        <td data-title="Date">{{$userInfo->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                    
                    <tr>
                        <td data-title="Identity Type">{{$userInfo->identity_card_type}} Back</td>
                        <td data-title="National Identity Number">{{$userInfo->national_identity_number}}</td>
                        <td data-title="Picture national identity">    
                            <a href="{{IDENTITY_FULL_DISPLAY_PATH.$userInfo->identity_image_back}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($userInfo->identity_image_back, PATHINFO_EXTENSION);
                                @endphp
                                @if($userInfo->identity_image_back == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$userInfo->identity_image_back, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                        </td>
                        <td data-title="Status">
                            @if($userInfo->back_identity_status == 1)
                            Approved
                            @elseif($userInfo->back_identity_status == 2)
                            Declined
                            @else
                            Pending
                            @endif
                        </td>
                        <td data-title="Date">{{$userInfo->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                    
                    @endif

                    <tr>
                        <td data-title="Identity Type">{{$userInfo->address_proof_type}}</td>
                        <td data-title="National Identity Number">{{$userInfo->address_proof_number}}</td>
                        <td data-title="Picture national identity">    
                            <a href="{{IDENTITY_FULL_DISPLAY_PATH.$userInfo->address_document}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($userInfo->address_document, PATHINFO_EXTENSION);
                                @endphp
                                @if($userInfo->address_document == "")
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$userInfo->address_document, SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                        </td>
                        <td data-title="Status">
                            @if($userInfo->address_status == 1)
                            Approved
                            @elseif($userInfo->address_status == 2)
                            Declined
                            @else
                            Pending
                            @endif
                        </td>
                        <td data-title="Date">{{$userInfo->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                    <tr>
                        <td data-title="Identity Type">Selfie</td>
                        <td data-title="National Identity Number"></td>
                        <td data-title="Picture national identity">    
                            <a href="{{PROFILE_FULL_DISPLAY_PATH.$userInfo->profile_image}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($userInfo->profile_image, PATHINFO_EXTENSION);
                                @endphp
                                @if($userInfo->profile_image == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$userInfo->profile_image, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                        </td>
                        <td data-title="Status">
                            @if($userInfo->selfie_status == 1)
                            Approved
                            @elseif($userInfo->selfie_status == 2)
                            Declined
                            @else
                            Pending
                            @endif
                        </td>
                        <td data-title="Date">{{$userInfo->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                        <tr>
                        <td data-title="Identity Type">Written Notes</td>
                        <td data-title="National Identity Number"></td>
                        <td data-title="Picture national identity">    
                            <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$userInfo->written_notes}}" title="View KYC Document" data-fancybox-group="gallery1" class="fancybox">
                                @php
                                $ext = pathinfo($userInfo->written_notes, PATHINFO_EXTENSION);
                                @endphp
                                @if($userInfo->written_notes == "") 
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                                @elseif (strtolower($ext) == 'pdf')
                                {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}}
                                @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                                {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$userInfo->written_notes, SITE_TITLE,['style'=>"max-width: 80px"])}} 
                                @else
                                {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                                @endif
                            </a>
                        </td>
                        <td data-title="Status">
                            @if($userInfo->is_kyc_done == 1)
                            Approved
                            @elseif($userInfo->is_kyc_done == 2)
                            Declined
                            @else
                            Pending
                            @endif
                        </td>
                        <td data-title="Date">{{$userInfo->created_at->format('M d, Y h:i A')}}</td>
                    </tr>
                </tbody>
            </table>
             <div class="search_frm"> 
                @if($userInfo->is_kyc_done == 0)
                <a href="{{ URL::to( 'admin/users/approvekyc/'.$userInfo->slug)}}" title="Approve KYC" class="btn btn-info">Approve KYC</a>
                <a href="{{ URL::to( 'admin/users/declinekyc/'.$userInfo->slug)}}" title="Decline KYC" class="btn btn-info">Decline KYC</a>
                @elseif($userInfo->is_kyc_done == 1)
                <a href="{{ URL::to( 'admin/users/declinekyc/'.$userInfo->slug)}}" title="Decline KYC" class="btn btn-info">Decline KYC</a>
                @elseif($userInfo->is_kyc_done == 2)
                <a href="{{ URL::to( 'admin/users/approvekyc/'.$userInfo->slug)}}" title="Approve KYC" class="btn btn-info">Approve KYC</a>
                <!-- @elseif($userInfo->is_kyc_done == 2)
                <a href="javascript:void();" title="Declined KYC" class="btn btn-info">Declined</a>
                @else
                <a href="javascript:void();" title="Approved KYC" class="btn btn-info">Approved</a>-->
                @endif 

                <a href="{{ URL::previous()}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
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
