@extends('layouts.home')
@section('content')
<section class="page-heading">
        <div class="container wrapper2">
            <div class="row">
                <div class="col-sm-8">
                    <h1>
                        Digital Bank of Africa <br>
                        (DBA) application
                    </h1>
                </div>
                <div class="col-sm-4">
                    <div class="follow-us">
                        <h6>
                            Follow us on
                        </h6>
                        <div class="social-header">
                            <a href="https://www.facebook.com/DafriBank/">{{HTML::image('public/img/front/facebook.svg', SITE_TITLE)}}</a>
                        <a href="https://twitter.com/DafriBank?s=09">{{HTML::image('public/img/front/twitter.svg', SITE_TITLE)}}</a>
                        <a href="https://www.linkedin.com/mwlite/company/dafribank-limited">{{HTML::image('public/img/front/linkedin.svg', SITE_TITLE)}}</a>
                        <a href="https://instagram.com/dafribank?igshid=uvltbfz738kg">{{HTML::image('public/img/front/instagram.svg', SITE_TITLE)}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- form-main -->
    <section class="form-main">
        <div class="container" ng-app="">
		  <div class="ersu_message">@include('elements.errorSuccessMessage')</div>
            <div class="row">
				{{ Form::open(array('name' => 'dbaForm', 'method' => 'post','[formGroup]'=>'formGroup')) }}
                    <div class="form-group col-sm-5">
                        <label>
                            Name
                        </label>
                        <input type="text" name="fname" ng-model="fname" required placeholder="Enter your name">
						<span ng-show="dbaForm.fname.$touched && dbaForm.fname.$invalid">The name is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Surname
                        </label>
                        <input type="text" name="lname" ng-model="lname" required placeholder="Enter your surname">
						<span ng-show="dbaForm.lname.$touched && dbaForm.lname.$invalid">The Surname is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Country
                        </label>
                        <input type="text" name="contry" ng-model="contry" required placeholder="Enter country">
						<span ng-show="dbaForm.contry.$touched && dbaForm.contry.$invalid">The Country is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Date of Birth
                        </label>
                        <input type="text" name="dob" ng-model="dob" id="dob" required placeholder="Enter your DOB" autocomplete="Off">
						<span ng-show="dbaForm.dob.$touched && dbaForm.dob.$invalid">The Date of Birth is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Age
                        </label>
                        <input type="text" name="age" ng-model="age" required placeholder="Enter age">
						<span ng-show="dbaForm.age.$touched && dbaForm.age.$invalid">The Age is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Physical Address
                        </label>
                        <input type="text" name="addrs" ng-model="addrs" required placeholder="Enter your physical address ">
						<span ng-show="dbaForm.addrs.$touched && dbaForm.addrs.$invalid">The Physical Address is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Telephone
                        </label>
                        <input type="text" name="phone" ng-model="phone" required  placeholder="Enter telephone no.">
						<span ng-show="dbaForm.phone.$touched && dbaForm.phone.$invalid">The Telephone number is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Mobile
                        </label>
                        <input type="text" name="mobile" ng-model="mobile" required placeholder="Enter your mobile no.">
						<span ng-show="dbaForm.mobile.$touched && dbaForm.mobile.$invalid">The Mobile number is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Email
                        </label>
                        <input type="email" name="email" ng-model="email" required placeholder="Enter email">
						<span ng-show="dbaForm.email.$touched && dbaForm.email.$invalid">The Email is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Telegram Handle
                        </label>
                        <input type="text" name="telegram_handle" placeholder="Enter your telegram handle">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Alternative Number
                        </label>
                        <input type="text" name="alter_number" placeholder="Enter alternative number">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Occupation
                        </label>
                        <input type="text" name="occupton" ng-model="occupton" required placeholder="Enter your occupation ">
						<span ng-show="dbaForm.occupton.$touched && dbaForm.occupton.$invalid">The Occupation is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Source of the Fund
                        </label>
                        <input type="text" name="fund_source" placeholder="Enter source of the fund">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Total Budget (in USD)
                        </label>
                        <input type="text" name="ttlBudgt" ng-model="ttlBudgt" required placeholder="Enter your total budget ">
						<span ng-show="dbaForm.ttlBudgt.$touched && dbaForm.ttlBudgt.$invalid">The Total Budget is required.</span>
                    </div>
                    <h2 class="col-sm-12">
                        Next of Kin
                    </h2>
                    <div class="form-group col-sm-5">
                        <label>
                            Name
                        </label>
                        <input type="text" name="kinName" ng-model="kinName" required placeholder="Enter your name">
						<span ng-show="dbaForm.kinName.$touched && dbaForm.kinName.$invalid">The Kin Name is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Surname
                        </label>
                        <input type="text" name="kinLname" ng-model="kinLname" required placeholder="Enter your surname  ">
						<span ng-show="dbaForm.kinLname.$touched && dbaForm.kinLname.$invalid">The Kin Surname is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Contact
                        </label>
                        <input type="text" name="kinCont" ng-model="kinCont" required placeholder="Enter contact">
						<span ng-show="dbaForm.kinCont.$touched && dbaForm.kinCont.$invalid">The Kin Contact number is required.</span>
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Relationship
                        </label>
                        <input type="text" name="kinRelation" ng-model="kinRelation" required placeholder="Enter your relationship  ">
						<span ng-show="dbaForm.kinRelation.$touched && dbaForm.kinRelation.$invalid">The Kin Relationship is required.</span>
                    </div>
                    <div class="form-group check-box col-sm-12">
                    	<input type="checkbox" name="agree" ng-model="agree" required> <span>By submitting you have agreed to DafriBank Limited T&Cs on DBA purchase and release structure!</span>
                    </div>
                    <div class="btn-box col-sm-12">
                    	<button type="submit" [disabled]="formGroup.invalid">
                    		Submit
                    	</button>
                    </div>
                {{ Form::close()}}
            </div>
        </div>
    </section>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
    $( function() {
    $( "#dob" ).datepicker({
     maxDate: 0,
	 changeMonth: true,
     changeYear: true,
	 dateFormat: 'dd-mm-yy',
	 yearRange: "-100:+0",
	});
    } );
  </script>
	@endsection