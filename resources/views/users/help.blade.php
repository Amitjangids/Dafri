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
                        <h5>Help</h5>
                    </div>
                    <div class="feedback-form">
                        <h6>Describe your requirement*</h6>
                        {{Form::textarea('fbDesc', null, ['class'=>'required', 'id'=> 'fbDesc'])}}
                    </div>
                    <div class="feedback-form border-form">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>
                                    First name*
                                </label>
                                {{Form::text('fbFname', $recordInfo->first_name, ['class'=>'required','placeholder'=>'Enter your first name', 'id'=> 'fbFname', 'autocomplete'=>'OFF'])}}
                            </div>
                            <div class="form-group col-sm-3">
                                <label>
                                    Last name*
                                </label>
                                {{Form::text('fbLname', $recordInfo->last_name, ['class'=>'required','placeholder'=>'Enter your last name', 'id'=> 'fbLname', 'autocomplete'=>'OFF'])}}
                            </div>
                            <div class="form-group col-sm-3">
                                <label>
                                    Email*
                                </label>
                                {{Form::text('fbEmail', $recordInfo->email, ['class'=>'required email','placeholder'=>'Enter your mail ', 'id'=> 'fbEmail', 'autocomplete'=>'OFF'])}}
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