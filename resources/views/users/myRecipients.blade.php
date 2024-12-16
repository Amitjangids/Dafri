@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
	<div id="page-content-wrapper">
            @include('elements.top_header')
            <div class="wrapper2">
			<div class="ersu_message">@include('elements.errorSuccessMessage')</div>
                <div class="row">
                    <div class="heading-section col-sm-12 mr-b-30">
                        <h5>Recipients</h5>
                    </div>
                    <div class="col-sm-12">
					@foreach ($recipients as $val)
					@php
					$res = getUserByAccNum($val->recipient_acc_num);
					@endphp
                    <div class="recipients-box recipients-page-box">
                    <div class="recipients-img-box">
					@if(isset($res->profile_image))
					{{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE)}}
					@else
                    {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
                    @endif
                    </div>
                    <div class="recipients-name-box">
                    <h6>{{$val->recipient_name}}</h6>
                    <span>Account ending {{substr($val->recipient_acc_num,6,4)}}</span>
                    </div>
                    <div class="icon-box ml-auto">
                    <a href="{{URL::to('auth/edit-recipient/'.$val->id)}}">{{HTML::image('public/img/front/edit.svg', SITE_TITLE)}}</a>
                    <a href="{{URL::to('auth/delete-recipient/'.$val->id)}}">{{HTML::image('public/img/front/delete.svg', SITE_TITLE)}}</a>
                    </div>
                    </div>
					@endforeach
                        
                         <div class="add-new-recipients mt-30">
                            <a href="{{URL::to('auth/add-recipient')}}">Add new recipients {{HTML::image('public/img/front/add_new_recipients.svg', SITE_TITLE)}}</a>
                        </div>
						
                    </div>
				
                </div>
            </div>
        </div>
</div>
@endsection