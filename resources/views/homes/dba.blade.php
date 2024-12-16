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
        <div class="container">
            <div class="row">
                <form>
                    <div class="form-group col-sm-5">
                        <label>
                            Name
                        </label>
                        <input type="text" placeholder="Enter your name">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Surname
                        </label>
                        <input type="text" placeholder="Enter your surname">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Country
                        </label>
                        <input type="text" placeholder="Enter country">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Date of Birth
                        </label>
                        <input type="text" placeholder="Enter your DOB">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Age
                        </label>
                        <input type="text" placeholder="Enter age">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Physical Address
                        </label>
                        <input type="text" placeholder="Enter your physical address ">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Telephone
                        </label>
                        <input type="text" placeholder="Enter telephone no.">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Mobile
                        </label>
                        <input type="text" placeholder="Enter your mobile no.">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Email
                        </label>
                        <input type="text" placeholder="Enter email">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Telegram Handle
                        </label>
                        <input type="text" placeholder="Enter your telegram handle">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Alternative Number
                        </label>
                        <input type="text" placeholder="Enter alternative number">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Occupation
                        </label>
                        <input type="text" placeholder="Enter your occupation ">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Source of the Fund
                        </label>
                        <input type="text" placeholder="Enter source of the fund">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Total Budget (in USD)
                        </label>
                        <input type="text" placeholder="Enter your total budget ">
                    </div>
                    <h2 class="col-sm-12">
                        Next of Kin
                    </h2>
                    <div class="form-group col-sm-5">
                        <label>
                            Name
                        </label>
                        <input type="text" placeholder="Enter your name">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Surname
                        </label>
                        <input type="text" placeholder="Enter your surname  ">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Contact
                        </label>
                        <input type="text" placeholder="Enter contact">
                    </div>
                    <div class="form-group col-sm-5">
                        <label>
                            Relationship
                        </label>
                        <input type="text" placeholder="Enter your relationship  ">
                    </div>
                    <div class="form-group check-box col-sm-12">
                    	<input type="checkbox" name=""> <span>By submitting you have agreed to DafriBank Limited T&Cs on DBA purchase and release structure!</span>
                    </div>
                    <div class="btn-box col-sm-12">
                    	<button>
                    		Submit
                    	</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
	@endsection