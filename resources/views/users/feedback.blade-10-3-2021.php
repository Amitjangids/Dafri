@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
            <div class="head-bar">
                <div class="wrapper2">
                    <button class="btn btn-mobile" id="menu-toggle"><span class="navbar-toggler-icon">{{HTML::image('public/img/front/bars.svg', SITE_TITLE)}}</span></button>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="search-head">
                                {{HTML::image('public/img/front/search.svg', SITE_TITLE)}}<input type="text" name="" placeholder="Search">
                            </div>
                        </div>
                        <div class="col-sm-6">
                             <div class="noti-right-bar">
                                <div class="profile-top-bar">
                                    <a href="{{URL::to('auth/my-account')}}">
                                    <div class="pro-top-bar-img">
                                    @if(isset($recordInfo->profile_image))
                        {{HTML::image(PROFILE_FULL_DISPLAY_PATH.$recordInfo->profile_image, SITE_TITLE)}}
						@else
                                        {{HTML::image('public/img/front/pro-img.jpg', SITE_TITLE)}}
                                        @endif
                                    </div>
                                    @if($recordInfo->user_type == 'Personal')
                                    <span>Hi, {{ucwords($recordInfo->first_name.' '.$recordInfo->last_name)}}</span>
                                    @else
                                    <span>Hi, {{ucwords($recordInfo->director_name)}}</span>
                                    @endif
                                    </a>
                                </div>
                                <a href="{{URL::to('auth/notifications')}}">{{HTML::image('public/img/front/bell.svg', SITE_TITLE)}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrapper2">
			<div class="ersu_message">@include('elements.errorSuccessMessage')</div>
                <div class="row" ng-app="">
				{{ Form::open(array('method' => 'post', 'name' =>'fbForm', 'id' => 'fbForm', 'class' => '','[formGroup]'=>'formGroup')) }}
                    <div class="col-sm-12">
                        <div class="heading-section">
                            <h5>Feedback</h5>
                            <p>We would love to hear your thoughts, suggestions, concerns or
                                problems with anything so we can improve!</p>
                        </div>
                        <div class="feedback-form">
                            <h6>Feedback type</h6>
                            <div class="text-field-filter ">
                                <div class="radio-card">
                                    <input id="radio-1" name="fbType" value="Comments" type="radio" checked="">
                                    <label for="radio-1" class="radio-label">Comments</label>
                                </div>
                                <div class="radio-card">
                                    <input id="radio-2" name="fbType" value="Suggestions" type="radio">
                                    <label for="radio-2" class="radio-label">Suggestions</label>
                                </div>
                                <div class="radio-card">
                                    <input id="radio-3" name="fbType" value="Questions" type="radio">
                                    <label for="radio-3" class="radio-label">Questions</label>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-form">
                            <h6>Describe your feedback*</h6>
                            {{Form::textarea('fbDesc', null, ['class'=>'form-control', 'id'=> 'fbDesc'])}}
                        </div>
                        <div class="feedback-form border-form">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>
                                        First name*
                                    </label>
                                    <input type="text" name="fbFname" id="fbFname" placeholder="Enter your first name">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>
                                        Last name*
                                    </label>
                                    <input type="text" name="fbLname" id="fbLname" placeholder="Enter your last name">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>
                                        Email*
                                    </label>
                                    <input type="text" name="fbEmail" id="fbEmail" placeholder="Enter your mail">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                        
                        <div class="col-sm-4">
                            <button class="sub-btn" type="submit">
                                Submit
                            </button>
                        </div>
                    </div>
                    </div>
					{{ Form::close() }}
                </div>
            </div>
        </div>
    <!-- /#page-content-wrapper -->
</div>
@endsection