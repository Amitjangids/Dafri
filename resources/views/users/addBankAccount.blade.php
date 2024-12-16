@extends('layouts.inner')
@section('content')
<style>
    .border-form label.error{
        color: red;
    }
</style>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="heading-section col-sm-12 mb-5 mt-5">
                    <h5>Add Account</h5>
                </div>

                <div class="account-detail-box border-form">
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    @if ($recordInfo->country == 'South Africa' and ($recordInfo->currency != 'EUR' and $recordInfo->currency != 'GBP'))
                    {{ Form::open(array('method' => 'post', 'id' => 'addAccountForm', 'class' => '')) }}
                    <?php if($recordInfo->first_name != ''){
                        $firstName = $recordInfo->first_name;
                    }else {
                        $firstName = $recordInfo->business_name;
                    }
                    
                    if($recordInfo->last_name != ''){
                        $lastName = $recordInfo->last_name;
                    }else {
                        $lastName = '';
                    }
                    ?>
                    <div class="col-sm-6 form-group">
                        <label>Select Bank</label>
                        {{Form::select('bank', $bankArr,null, ['class' => 'form-control required', 'id' => 'bank'])}}
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Account Number</label>
                        {{Form::text('accntNumbr', null, ['class'=>'form-control required', 'placeholder'=>'Enter Account Number', 'id'=> 'accntNumbr', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>First Name</label>
                        {{Form::text('first_name', $firstName, ['class'=>'form-control required', 'placeholder'=>'First Name', 'id'=> 'first_name', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Last Name</label>
                        {{Form::text('last_name', $lastName, ['class'=>'form-control required', 'placeholder'=>'Last Name', 'id'=> 'last_name', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Email</label>
                        {{Form::text('email', null, ['class'=>'form-control email required', 'placeholder'=>'Email', 'id'=> 'email', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Mobile</label>
                        {{Form::text('mobile', null, ['class'=>'form-control required', 'placeholder'=>'Mobile', 'id'=> 'mobile', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-12">
                        <div class="sub-btn-box">
                            <button class="sub-btn" type="submit">Save Account</button>
                        </div>
                    </div> 
                    {{ Form::close() }}
                    @elseif($recordInfo->country == 'Nigeria' and ($recordInfo->currency != 'EUR' and $recordInfo->currency != 'GBP'))
                    {{ Form::open(array('method' => 'post', 'id' => 'addAccountForm', 'class' => '')) }}
                    <div class="col-sm-6">
                        <label>Select Bank</label>
                        {{Form::select('bank', $bankArr,null, ['class' => 'form-control required', 'id' => 'bank'])}}
                    </div>
                    <div class="col-sm-6">
                        <label>Account Number</label>
                        {{Form::text('accntNumbr', null, ['class'=>'form-control required required', 'placeholder'=>'Enter Account Number', 'id'=> 'accntNumbr', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-12">
                        <div class="sub-btn-box">
                            <button class="sub-btn" type="submit">Save Account</button>
                        </div>
                    </div>
                    @elseif($recordInfo->country == 'Uganda' and ($recordInfo->currency != 'EUR' and $recordInfo->currency != 'GBP'))
                    {{ Form::open(array('method' => 'post', 'id' => 'addAccountForm', 'class' => '')) }}
                    <div class="col-sm-6">
                        <label>Select Bank</label>
                        {{Form::select('bank', $bankArr,null, ['class' => 'form-control required', 'id' => 'bank'])}}
                    </div>
                    <div class="col-sm-6">
                        <label>Account Number</label>
                        {{Form::text('accntNumbr', null, ['class'=>'form-control required', 'placeholder'=>'Enter Account Number', 'id'=> 'accntNumbr', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Branch Code</label>
                        {{Form::text('brnchCode', null, ['class'=>'form-control required', 'placeholder'=>'Enter Branch Code', 'id'=> 'brnchCode', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>First Name</label>
                        {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter First Name', 'id'=> 'first_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Last Name</label>
                        {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Last Name', 'id'=> 'last_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-12">
                        <div class="sub-btn-box">
                            <button class="sub-btn" type="submit">Save Account</button>
                        </div>
                    </div>   
                    {{ Form::close() }}
                    @elseif ($recordInfo->country == 'United States' && ($recordInfo->currency != 'EUR' && $recordInfo->currency != 'GBP'))
                    {{ Form::open(array('method' => 'post', 'id' => 'addAccountForm', 'class' => '')) }}
                    <div class="col-sm-6">
                        <label>Bank Name</label>
                        {{Form::text('bank_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Bank Name', 'id'=> 'bank_name', 'autocomplete'=>'OFF'])}}
                    </div>
                    <div class="col-sm-6">
                        <label>Account Number</label>
                        {{Form::text('accntNumbr', null, ['class'=>'form-control required', 'placeholder'=>'Enter Account Number', 'id'=> 'accntNumbr', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Routing Number</label>
                        {{Form::text('routing_number', null, ['class'=>'form-control required', 'placeholder'=>'Enter Routing Number', 'id'=> 'routing_number', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Swift Code</label>
                        {{Form::text('swift_code', null, ['class'=>'form-control required', 'placeholder'=>'Enter Swift Code', 'id'=> 'swift_code', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>First Name</label>
                        {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter First Name', 'id'=> 'first_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Last Name</label>
                        {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Last Name', 'id'=> 'last_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Beneficiary Address</label>
                        {{Form::text('addrs', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Address', 'id'=> 'addrs', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-12">
                        <div class="sub-btn-box">
                            <button class="sub-btn" type="submit">Save Account</button>
                        </div>
                    </div>   
                    {{ Form::close() }}
                    @elseif ($recordInfo->currency == 'EUR' OR $recordInfo->currency == 'GBP')
                    {{ Form::open(array('method' => 'post', 'id' => 'addAccountForm', 'class' => '')) }}
                    <div class="col-sm-6">
                        <label>Account Number</label>
                        {{Form::text('accntNumbr', null, ['class'=>'form-control required', 'placeholder'=>'Enter Account Number', 'id'=> 'accntNumbr', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Routing Number</label>
                        {{Form::text('routing_number', null, ['class'=>'form-control required', 'placeholder'=>'Enter Routing Number', 'id'=> 'routing_number', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Swift Code</label>
                        {{Form::text('swift_code', null, ['class'=>'form-control required', 'placeholder'=>'Enter Swift Code', 'id'=> 'swift_code', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Bank Name</label>
                        {{Form::text('bank_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Bank Name', 'id'=> 'bank_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>First Name</label>
                        {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter First Name', 'id'=> 'first_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Last Name</label>
                        {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Last Name', 'id'=> 'last_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Country</label>
                        {{Form::text('country', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Country', 'id'=> 'country', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Postal Code</label>
                        {{Form::text('zip', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Postal Code', 'id'=> 'zip', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Street Number</label>
                        {{Form::text('street_number', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Street Number', 'id'=> 'street_number', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Street Name</label>
                        {{Form::text('street_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Street Name', 'id'=> 'street_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>City</label>
                        {{Form::text('city', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Street Name', 'id'=> 'city', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-12">
                        <div class="sub-btn-box">
                            <button class="sub-btn" type="submit">Save Account</button>
                        </div>
                    </div>   
                    {{ Form::close() }}
                    @elseif ((strtolower(trim($recordInfo->country)) != "united states" and strtolower(trim($recordInfo->country)) != "south africa" and strtolower(trim($recordInfo->country)) != "nigeria" and strtolower(trim($recordInfo->country)) != "uganda") and ($recordInfo->currency != 'EUR' OR $recordInfo->currency != 'GBP'))
                    {{ Form::open(array('method' => 'post', 'id' => 'addAccountForm', 'class' => '')) }}
                    <div class="col-sm-6">
                        <label>Account Number</label>
                        {{Form::text('accntNumbr', null, ['class'=>'form-control required', 'placeholder'=>'Enter Account Number', 'id'=> 'accntNumbr', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Routing Number</label>
                        {{Form::text('routing_number', null, ['class'=>'form-control required', 'placeholder'=>'Enter Routing Number', 'id'=> 'routing_number', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Swift Code</label>
                        {{Form::text('swift_code', null, ['class'=>'form-control required', 'placeholder'=>'Enter Swift Code', 'id'=> 'swift_code', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Bank Name</label>
                        {{Form::text('bank_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Bank Name', 'id'=> 'bank_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>First Name</label>
                        {{Form::text('first_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter First Name', 'id'=> 'first_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Last Name</label>
                        {{Form::text('last_name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Last Name', 'id'=> 'last_name', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Address</label>
                        {{Form::text('addrs', null, ['class'=>'form-control required', 'placeholder'=>'Enter Address', 'id'=> 'addrs', 'autocomplete'=>'OFF'])}}
                    </div>

                    <div class="col-sm-6">
                        <label>Country</label>
                        {{Form::text('country', null, ['class'=>'form-control required', 'placeholder'=>'Enter Beneficiary Country', 'id'=> 'country', 'autocomplete'=>'OFF'])}}
                    </div>								   

                    <div class="col-sm-12">
                        <div class="sub-btn-box">
                            <button class="sub-btn" type="submit">Save Account</button>
                        </div>
                    </div>   
                    {{ Form::close() }}
                    @endif   
                </div>

            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
function choosePaymentMethod(paymentMethod)
{
    document.getElementById('payment_method').value = paymentMethod;
    document.getElementById('paymentMethod').value = paymentMethod;
    document.getElementById('paymentMethod').innerHTML = paymentMethod + ' <img src="<?php echo HTTP_PATH; ?>/public/img/front/arrow-down.svg" alt="DafriBank">';
    document.getElementById('drpDwnDiv').style.display = 'none';
}
</script>
{{ HTML::script('public/assets/js/jquery.validate.js')}}
<script type="text/javascript">
    $(document).ready(function () {
        $("#addAccountForm").validate();
    });
    </script>
@endsection