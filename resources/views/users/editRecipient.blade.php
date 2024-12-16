@extends('layouts.inner')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
            @include('elements.top_header')
            <div class="wrapper2">
			<div class="ersu_message">@include('elements.errorSuccessMessage')</div>
                <div class="row" ng-app="">
                    <div class="heading-section col-sm-12 mb-90 mt-60">
                        <h5>Edit recipient</h5>
                    </div>
                    <div class="col-sm-6 ad-rec">
					{{Form::model($recipient, ['method' => 'post', 'name' => 'editRecipientForm', 'id' => 'editRecipientForm', 'class' => 'row border-form','[formGroup]'=>'formGroup']) }}
                            <div class="form-group col-sm-6">
                            <label>Name</label>
                            {{Form::text('recipient_name', null, ['class'=>'form-control', 'id'=> 'recipient_name', 'autocomplete'=>'OFF'])}}
                            </div>
                            <div class="form-group col-sm-6">
                            <label>Email</label>
                            {{Form::email('recipient_email', null, ['class'=>'form-control', 'id'=> 'recipient_email', 'autocomplete'=>'OFF'])}}
                            </div>
                            <div class="form-group col-sm-6">
                            <label>Account number</label>
                            {{Form::text('recipient_acc_num', null, ['class'=>'form-control', 'placeholder'=>'Enter account number', 'id'=> 'recipient_acc_num', 'autocomplete'=>'OFF'])}}
							<span style="color:#FF0000;font-size:11px;" ng-show="addRecipientForm.recipAccntNum.$touched && addRecipientForm.recipAccntNum.$invalid">The account number is required.</span>
                            </div>
                            <div class="form-group col-sm-6">
                            <label>Confirm account number</label>
                            {{Form::text('conf_recipAccntNum', null, ['class'=>'form-control', 'placeholder'=>'Confirm account number', 'id'=> 'conf_recipAccntNum', 'autocomplete'=>'OFF', 'required'=>true, 'ng-model' => 'conf_recipAccntNum'])}}
							<span style="color:#FF0000;font-size:11px;" ng-show="addRecipientForm.conf_recipAccntNum.$touched && addRecipientForm.conf_recipAccntNum.$invalid">The confirm account number is required.</span>
                            </div>
                            <div class="form-group col-sm-6 ">
                                <label>Select bank</label>
                                <div class="selectdiv">
								<?php $bankList = array('Dafri_Bank_Wallet'=>'Dafri Bank Wallet');?>
							   {{Form::select('recipient_bank', $bankList,null, ['class' => '', 'id' => 'recipient_bank'])}}
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                            <label>Mobile number</label>
                            {{Form::text('recipient_mobile', null, ['class'=>'form-control', 'placeholder'=>'Enter Recipient mobile number', 'id'=> 'recipient_mobile', 'autocomplete'=>'OFF'])}}
							<span style="color:#FF0000;font-size:11px;" ng-show="addRecipientForm.conf_recipAccntNum.$touched && addRecipientForm.conf_recipAccntNum.$invalid">The mobile number is required.</span>
                            </div>
                             <button class="sub-btn" type="submit" [disabled]="formGroup.invalid">
                            Submit
                            </button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    <!-- /#page-content-wrapper -->
</div>
@endsection