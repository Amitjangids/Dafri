@extends('layouts.inner')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<style>
    .bene-form {
        padding-bottom: 75px;
    }
</style>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
            <div class="row" ng-app="">
                <div class="heading-section col-sm-12 mb-90 mt-60">
                    <h5>Add beneficiary</h5>
                </div>
                <div class="col-sm-6 ad-rec">
                    {{ Form::open(array('method' => 'post', 'name' =>'addBeneficiaryForm', 'id' => 'addBeneficiaryForm', 'class' => 'row border-form bene-form')) }}
                    <div class="form-group col-sm-6">
                        <label>Name</label>
                        {{Form::text('recipName', null, ['class'=>'required', 'placeholder'=>'Enter name', 'id'=> 'recipName', 'autocomplete'=>'OFF' ])}}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Email</label>
                        {{Form::email('recipEmail', null, ['class'=>'email required', 'placeholder'=>'Enter email', 'id'=> 'recipEmail', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Account number</label>
                        {{Form::text('recipAccntNum', null, ['class'=>'required', 'placeholder'=>'Enter account number', 'id'=> 'recipAccntNum', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Confirm account number</label>
                        {{Form::text('conf_recipAccntNum', null, ['class'=>'required', 'placeholder'=>'Confirm account number', 'id'=> 'conf_recipAccntNum', 'equalTo' => '#recipAccntNum', 'autocomplete'=>'OFF'])}}
                    </div>

                    <button class="sub-btn" type="submit">
                        Submit
                    </button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

{{ HTML::script('public/assets/js/jquery.validate.js')}}
<script type="text/javascript">
    $(document).ready(function () {
        $("#addBeneficiaryForm").validate();
    });
    </script>
@endsection