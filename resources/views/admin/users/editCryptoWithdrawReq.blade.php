@extends('layouts.admin')
@section('content')
<?php
 function matchSel($first,$second)
 {
   if ($first == $second)
	return "selected";	
 }
?>
<script type="text/javascript">
    $(document).ready(function () {
        $.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || /^[\w.]+$/i.test(value);
        }, "Only letters, numbers and underscore allowed.");
        $.validator.addMethod("passworreq", function (input) {
            var reg = /[0-9]/; //at least one number
            var reg2 = /[a-z]/; //at least one small character
            var reg3 = /[A-Z]/; //at least one capital character
            //var reg4 = /[\W_]/; //at least one special character
            return reg.test(input) && reg2.test(input) && reg3.test(input);
        }, "Password must be a combination of Numbers, Uppercase & Lowercase Letters.");
        $("#adminForm").validate();

    });
</script>

<style type="text/css">
.county-code {
    width: 100px;
}

.select select {
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    appearance: none;
    outline: 0;
    box-shadow: none;
    border: 0 !important;
    background: #e6e6e6;
    background-image: none;
    font-size: 12px;
}

/* Remove IE arrow */
.select select::-ms-expand {
    display: none;
}

/* Custom Select */
.select {
    position: relative;
    display: flex;
    width: 100%;
    height: 34px;
    background: #eaeaea;
    overflow: hidden;
    border-radius: 0;
}

.select select {
    flex: 1;
    padding: 0 .5em;
    color: #000;
    cursor: pointer;
}

/* Arrow */
.select::after {
    content: '\25BC';
    position: absolute;
    top: 0;
    right: 0;
    padding: 0 8px;
    cursor: pointer;
    pointer-events: none;
    -webkit-transition: .25s all ease;
    -o-transition: .25s all ease;
    transition: .25s all ease;
    font-size: 10px;
    height: 40px;
    display: flex;
    align-items: center;
}

/* Transition */
.select:hover::after {
    color: #000;
}

.flex-input {
    display: flex;
}

.county-code {
    width: 228px;
}

.select::after {
    content: '\25BC';
    position: absolute;
    top: 0;
    right: 0;
    padding: 0 8px;
    cursor: pointer;
    pointer-events: none;
    -webkit-transition: .25s all ease;
    -o-transition: .25s all ease;
    transition: .25s all ease;
    font-size: 10px;
    height: 34px;
    display: flex;
    align-items: center;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Crypto Withdraw Request</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/users/crypto-withdraw-request')}}"><i class="fa fa-user"></i> <span>Crypto Withdraw Request</span></a></li>
            <li class="active"> Edit Crypto Withdraw Request</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($req, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
                <div class="box-body">
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Amount <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('amount', null, ['class'=>'form-control required', 'placeholder'=>'Amount', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Currency <span class="require">*</span></label>
                        <div class="col-sm-10">
                                 
                          <?php $currencyList = array('USDT ERC20'=>'USDT ERC20','SDT BEP20'=>'SDT BEP20','USDT TRC20'=>'USDT TRC20','USDC'=>'USDC','SAFEBANK'=>'SAFEBANK','BTC'=>'BTC','ETH'=>'ETH','BNB (BEP20)'=>'BNB (BEP20)','CAKE'=>'CAKE','DOT'=>'DOT','LINK'=>'LINK','TRX'=>'TRX','BUSD'=>'BUSD');?>
                            {{Form::select('crypto_currency', $currencyList,null, ['class' => 'form-control required','placeholder' => 'Select Currency'])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Payout Address <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('payout_addrs', null, ['class'=>'form-control required', 'placeholder'=>'Payout Address', 'autocomplete' => 'off'])}}
                        </div>
                    </div>  

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/users/crypto-deposit-request')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
    @endsection