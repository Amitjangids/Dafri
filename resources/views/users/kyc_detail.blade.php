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
                    <div class="heading-section text-center">
                        <h5>KYC Details</h5>
                    </div>
                </div>
                
                @if($recordInfo->identity_card_type == 'Passport')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Identity Card</h4>
                        <h6><strong>Type </strong> - {{$recordInfo->identity_card_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->national_identity_number}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->identity_image, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$recordInfo->identity_image, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div>
                </div>
                
                @else
                
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Identity Card Front Side</h4>
                        <h6><strong>Type </strong> - {{$recordInfo->identity_card_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->national_identity_number}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->identity_image, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$recordInfo->identity_image, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Identity Card Back End</h4>
                        <h6><strong>Type </strong> - {{$recordInfo->identity_card_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->national_identity_number}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->identity_image_back, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image_back == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image_back}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image_back}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image_back}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$recordInfo->identity_image_back, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div>
                </div>
                
                @endif
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Proof of address</h4>
                        <h6><strong>Type </strong> - {{$recordInfo->address_proof_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->address_proof_number}}</h6>
                        
                        
                        @php
                        $ext = pathinfo($recordInfo->address_document, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_document}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_document}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity_image}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$recordInfo->address_document, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script type="text/javascript">
                                $(document).ready(function () {

                                    $(".active-hover").click(function () {});

                                    $(".inner-mathod-box").hover(
                                            function () {
                                                $(".inner-mathod-box").removeClass("active-hover");
                                                $(this).addClass("active-hover");
                                            }
                                    );

                                });

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
</script>
@endsection