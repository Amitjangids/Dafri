@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">

        @include('elements.top_header')
        <?php global $kycStatus;?>
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-12">
                    <div class="heading-section text-center">
                        <h5>KYC Details (
                            @if($recordInfo->is_kyc_done == 1)
                            Approved
                            @elseif($recordInfo->is_kyc_done == 2)
                            Declined
                            @else
                            @if($recordInfo->identity_image != '')
                            Pending
                            @else
                            Missing
                            @endif
                            @endif
                            )</h5>
                        @if($recordInfo->is_kyc_done != 1)
                        
                            @if($recordInfo->is_kyc_done == 2)
                            
                                @if($recordInfo->user_type == 'Business')
                                <span><a href="{{URL::to('business-kyc-update/'.$recordInfo->slug)}}">Re-submit KYC detail</a></span>
                                @elseif($recordInfo->user_type == 'Personal')
                                <span><a href="{{URL::to('personal-kyc-update/'.$recordInfo->slug)}}">Re-submit KYC detail</a></span>
                                @endif
                            
                            @endif
                            
                                @if($recordInfo->identity_image == '' && $recordInfo->is_kyc_done == 0)
                                    @if($recordInfo->user_type == 'Business')
                                    <span><a href="{{URL::to('business-kyc-update/'.$recordInfo->slug)}}">Start KYC Verification</a></span>
                                    @elseif($recordInfo->user_type == 'Personal')
                                    <span><a href="{{URL::to('personal-kyc-update/'.$recordInfo->slug)}}">Start KYC Verification</a></span>
                                    @endif
                                @else
                                
                                @endif
                            
                        @endif
                    </div>
                     <?php /* <div class="profile-dp mt-3">
                        {{ Form::open(array('method' => 'post', 'id' => 'uplaodprofileimg', 'enctype' => "multipart/form-data")) }}
                        <div class="profiledp-box  m-auto">
                      
                                @if(isset($recordInfo->profile_image))
                                {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE, ['id'=> 'pimage'])}}
                                @else
                                {{HTML::image('public/img/front/no-user.png', SITE_TITLE, ['id'=> 'pimage'])}}
                                @endif
                           

                        </div>
                       
                        {{ Form::close()}}
                    </div> */?>
                </div>
                @if($recordInfo->profile_image != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Selfie</h4>
                        <h6><strong>Status</strong> - {{$kycStatus[$recordInfo->selfie_status]}}</h6>
                        @if(isset($recordInfo->profile_image))
                                {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE, ['id'=> 'pimage'])}}
                                @else
                                {{HTML::image('public/img/front/no-user.png', SITE_TITLE, ['id'=> 'pimage'])}}
                                @endif
                    </div> 
                </div>
                @endif
               
                @if(!empty($recordInfo->identity_image) && !empty($recordInfo->address_document))
                
                @if($recordInfo->identity_card_type == 'Passport')
                
                <div class="col-sm-6">
                    <div class="kyc-details">
                        
                        <h4>Identity Card</h4>
                        <h6><strong>Type </strong> - {{$recordInfo->identity_card_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->national_identity_number}}</h6>
                        <h6><strong>Status</strong> - {{$kycStatus[$recordInfo->identity_status]}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->identity_image, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
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
                        <h6><strong>Status</strong> - {{$kycStatus[$recordInfo->identity_status]}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->identity_image, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
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
                        
                        <h4>Identity Card Back Side</h4>
                        <h6><strong>Type </strong> - {{$recordInfo->identity_card_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->national_identity_number}}</h6>
                        <h6><strong>Status</strong> - {{$kycStatus[$recordInfo->back_identity_status]}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->identity_image_back, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image_back == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
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
                        <h6><strong>Status</strong> - {{$kycStatus[$recordInfo->address_status]}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->address_document, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity_image == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
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
                @else
                <div class="col-sm-12">
                    <div class="no_record" style="padding: 50px;">Documents not uploaded yet.</div>                        
                </div>
                @endif
                
                
                 @if($recordInfo->written_notes != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Written Notes</h4>
                        @php
                        $ext = pathinfo($recordInfo->written_notes, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->written_notes == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->written_notes}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->written_notes}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->written_notes}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->written_notes, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>
                @endif


                @if($recordInfo->registration_document != '')
<!--                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Business detail</h4>
                        <h6><strong>Business Type </strong> - {{$recordInfo->business_type}}</h6>
                        <h6><strong>Number</strong> - {{$recordInfo->registration_number}}</h6>
                        @php
                        $ext = pathinfo($recordInfo->registration_document, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->registration_document == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->registration_document}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->registration_document}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->registration_document}}</a>
                            </object></div>
                         {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} 
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->registration_document, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif


                    </div>
                </div>-->
                @endif
                @if($recordInfo->user_type == 'Business' || ($recordInfo->user_type == 'Agent' && $recordInfo->director_name != ""))
                <?php
                $certificate_of_incorporation = $article = $memorandum = $tax_certificate = $address_proof = $identity = $person_identity = 0;
                ?>
                @if($recordInfo->certificate_of_incorporation != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Certificate of Incorporation</h4>
                        @php
                        $ext = pathinfo($recordInfo->certificate_of_incorporation, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->certificate_of_incorporation == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->certificate_of_incorporation}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->certificate_of_incorporation}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->certificate_of_incorporation}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->certificate_of_incorporation, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>
                <?php
                $certificate_of_incorporation = 1;
                ?>
                @endif

                @if($recordInfo->article != '')
<!--                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Article</h4>
                        @php
                        $ext = pathinfo($recordInfo->article, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->article == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->article}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->article}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->article}}</a>
                            </object></div>
                         {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} 
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->article, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>-->
                <?php
                $article = 1;
                ?>
                @endif

                @if($recordInfo->memorandum != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Memorandum</h4>
                        @php
                        $ext = pathinfo($recordInfo->memorandum, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->memorandum == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->memorandum}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->memorandum}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->memorandum}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->memorandum, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>
                <?php
                $memorandum = 1;
                ?>
                @endif

                @if($recordInfo->tax_certificate != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Tax Certificate</h4>
                        @php
                        $ext = pathinfo($recordInfo->tax_certificate, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->tax_certificate == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->tax_certificate}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->tax_certificate}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->tax_certificate}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->tax_certificate, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>
                <?php
                $tax_certificate = 1;
                ?>
                @endif
                @if($recordInfo->address_proof != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Proof of Business Address</h4>
                        @php
                        $ext = pathinfo($recordInfo->address_proof, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->address_proof == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_proof}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_proof}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_proof}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->address_proof, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>

                <?php
                $address_proof = 1;
                ?>
                @endif
                @if($recordInfo->identity != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Identity of all Directors</h4>
                        @php
                        $ext = pathinfo($recordInfo->identity, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->identity == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div>
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->identity}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(IDENTITY_FULL_DISPLAY_PATH.$recordInfo->identity, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>
                
                <?php
                $identity = 1;
                ?>
                @endif
                @if($recordInfo->person_identity != '')
                <div class="col-sm-6">
                    <div class="kyc-details">
                        <h4>Identity of person or entity holding more than 25% stake in the company</h4>
                        @php
                        $ext = pathinfo($recordInfo->person_identity, PATHINFO_EXTENSION);
                        @endphp
                        @if($recordInfo->person_identity == "") 
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 80px"])}}
                        @elseif (strtolower($ext) == 'pdf')
                        <div class="pdfupload">
                            <object data="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->person_identity}}" type="application/pdf" width="420" height="350">
                                <a href="{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->person_identity}}">{{DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->person_identity}}</a>
                            </object></div>
                        <!-- {{HTML::image('/public/img/pdf-icon.png','PDF Document',array( 'width' => 70, 'height' => 70 ))}} -->
                        @elseif(strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif')
                        {{HTML::image(DOCUMENT_FULL_DISPLAY_PATH.$recordInfo->person_identity, SITE_TITLE)}} 
                        @else
                        {{HTML::image('/public/img/front/no-image.png', SITE_TITLE,['style'=>"max-width: 70px"])}} 
                        @endif
                    </div> 
                </div>
                <?php
                $person_identity = 1;
                ?>
                @endif



                @if($certificate_of_incorporation == 0 || $article == 0 || $memorandum == 0 || $tax_certificate == 0 || $address_proof == 0 || $identity == 0 || $person_identity == 0)
                <div class="col-sm-12">
                    {{ Form::open(array('method' => 'post', 'id' => 'registerform', 'class' => ' border-form', 'enctype' => "multipart/form-data")) }} 
                    <div class="row">
                        @if($certificate_of_incorporation == 0)
                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Certificate Of Incorporation
                                </label>
                                <div class="file-uploader f-upload complance_img">
                                    <label for="file-input1">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('certificate_of_incorporation', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input1', 'onchange'=>'showFilename1()'])}}
                                    <p id="target_file1" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($article == 0)
<!--                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Article
                                </label>
                                <div class="file-uploader f-upload">
                                    <label for="file-input2">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('article', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input2', 'onchange'=>'showFilename2()'])}}
                                    <p id="target_file2" class="text1"></p>
                                </div>
                            </div>
                        </div>-->
                        @endif
                        @if($memorandum == 0)
                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Memorandum
                                </label>
                                <div class="file-uploader f-upload complance_img">
                                    <label for="file-input3">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('memorandum', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input3', 'onchange'=>'showFilename3()'])}}
                                    <p id="target_file3" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($tax_certificate == 0)
                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Tax Certificate
                                </label>
                                <div class="file-uploader f-upload complance_img">
                                    <label for="file-input4">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('tax_certificate', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input4', 'onchange'=>'showFilename4()'])}}
                                    <p id="target_file4" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($address_proof == 0)
                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Proof of Business Address
                                </label>
                                <div class="file-uploader f-upload complance_img">
                                    <label for="file-input5">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('address_proof', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input5', 'onchange'=>'showFilename5()'])}}
                                    <p id="target_file5" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($identity == 0)
<!--                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Identity of all Directors
                                </label>
                                <div class="file-uploader f-upload">
                                    <label for="file-input6">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('identity', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input6', 'onchange'=>'showFilename6()'])}}
                                    <p id="target_file6" class="text1"></p>
                                </div>
                            </div>
                        </div>-->
                        @endif
                        @if($person_identity == 0)
                        <div class="form-group col-sm-6">
                            <div class="kyc-details">
                                <label>
                                    Identity of person or entity holding more than 25% stake in the company
                                </label>
                                <div class="file-uploader f-upload complance_img">
                                    <label for="file-input7">
                                        Upload document 
                                        {{HTML::image('public/img/front/upload.svg', SITE_TITLE)}}
                                    </label>
                                    {{Form::file('person_identity', ['class'=>'', 'accept'=>IMAGE_DOC_EXT,'id'=>'file-input7', 'onchange'=>'showFilename7()'])}}
                                    <p id="target_file7" class="text1"></p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!--                        <div class="col-sm-12 cls_upp">
                                                    <button class="sub-btn" type="submit" id='step_1' onclick="setCountryCode();">
                                                        Update
                                                    </button>
                                                </div>-->

                    </div>
<!--                    <div class="row">
                        <div class="col-sm-5 cls_upp m-auto">
                            <button class="sub-btn" type="submit" id="step_1" onclick="setCountryCode();">
                                Update
                            </button>
                        </div>
                    </div>-->
                    {{ Form::close()}}
                </div>
                @endif
                @endif
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

<script>
    function showFilename1()
    {
        var name = document.getElementById('file-input1');
        document.getElementById('target_file1').innerHTML = name.files.item(0).name;
    }
    function showFilename2()
    {
        var name = document.getElementById('file-input2');
        document.getElementById('target_file2').innerHTML = name.files.item(0).name;
    }
    function showFilename3()
    {
        var name = document.getElementById('file-input3');
        document.getElementById('target_file3').innerHTML = name.files.item(0).name;
    }
    function showFilename4()
    {
        var name = document.getElementById('file-input4');
        document.getElementById('target_file4').innerHTML = name.files.item(0).name;
    }
    function showFilename5()
    {
        var name = document.getElementById('file-input5');
        document.getElementById('target_file5').innerHTML = name.files.item(0).name;
    }
    function showFilename6()
    {
        var name = document.getElementById('file-input6');
        document.getElementById('target_file6').innerHTML = name.files.item(0).name;
    }
    function showFilename7()
    {
        var name = document.getElementById('file-input7');
        document.getElementById('target_file7').innerHTML = name.files.item(0).name;
    }
</script>
@endsection