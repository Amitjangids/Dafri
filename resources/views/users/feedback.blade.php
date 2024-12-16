@extends('layouts.inner')
@section('content')
{{ HTML::script('public/assets/js/jquery.validate.js')}}
<script type="text/javascript">
    $(document).ready(function () {
        $("#fbForm").validate();
    });
    </script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
            @include('elements.top_header')
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
                            {{Form::textarea('fbDesc', null, ['class'=>'', 'id'=> 'fbDesc'])}}
                        </div>
                        <div class="feedback-form border-form">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>
                                        First name*
                                    </label>
                                    {{Form::text('fbFname', null, ['class'=>'required','placeholder'=>'Enter your first name', 'id'=> 'fbFname', 'autocomplete'=>'OFF'])}}
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>
                                        Last name*
                                    </label>
                                    {{Form::text('fbLname', null, ['class'=>'required','placeholder'=>'Enter your last name', 'id'=> 'fbLname', 'autocomplete'=>'OFF'])}}
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>
                                        Email*
                                    </label>
                                    {{Form::text('fbEmail', null, ['class'=>'required email','placeholder'=>'Enter your mail ', 'id'=> 'fbEmail', 'autocomplete'=>'OFF'])}}
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
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@endsection