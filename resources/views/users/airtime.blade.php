@extends('layouts.inner')
@section('content')
<style>
    .fund-name-box h6 {
        font-size: 12px !important;
    }

    .btn_sub {
        width: auto;
        display: inline-block;
        padding: 12px 20px;
        margin-bottom: 30px;

    }

    .btn_sub:hover {
        text-decoration: none;
        color: var(--main-white-color);
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2 w-100">
            <div class="row" ng-app="">
                <div class="col-sm-12 mt-4">
                    <h4 class="form-head-top">Mobile Top-up </h4>
                </div>
                <div class="col-sm-12">
                    <div class="top-up-form">
                        {{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm', 'id' => 'fundTransfrForm', 'class' => '','[formGroup]'=>'formGroup')) }}
                        <!--       <div class="radio-top-up-form">
                            <div class="radio-top">
                                <input type="radio" id="f-option" name="selector" checked="">
                                <label for="f-option">Prepaid</label>
                                <div class="check"></div>
                            </div>
                            <div class="radio-top">
                                <input type="radio" id="f-option1" name="selector">
                                <label for="f-option1">Postpaid</label>
                                <div class="check"></div>
                            </div>
                        </div> -->
                        <div class="top-form-fields">
                            <div class="form-group">
                                {{Form::text('topup_phone', null, ['class'=>'required digits','placeholder'=>'Enter phone number', 'id'=> 'recipient_phone', 'autocomplete'=>'OFF', 'number'=>true,'onfocusout' => 'changeOperator(this.value)'])}}
                                <!--{{Form::text('recipient_phone', null, ['class'=>'required digits','placeholder'=>'Enter phone number', 'id'=> 'recipient_phone', 'autocomplete'=>'OFF', 'number'=>true,'onkeyup' => 'changeOperator(this.value)','onfocusout' => 'changeOperator(this.value)'])}}-->
                            </div>
                            <div class="form-group" id="operator_set">
                                {{Form::select('operator_type', array(),null, ['id'=>'operator_type','class' => 'required form-control required','placeholder' => 'Choose Service Provider','onChange'=>'getPlan()'])}}
                            </div>
                            <!--                                <div class="form-group">
                                                                <input type="text" name="" placeholder="Circle">
                                                            </div>-->
                            <div class="form-group">
                                {{Form::text('trnsfrAmnt', null, ['class'=>'required','placeholder'=>'Select Plan ', 'id'=> 'trnsfrAmnt', 'autocomplete'=>'OFF', 'number'=>true, 'ng-model' => 'trnsfrAmnt', 'onkeypress'=>'return validateFloatKeyPress(this,event);','readonly' ])}}
                            </div>
                            <div class="btn-group-top">
                                <input type="hidden" name="trnsfrCurrncy" id="trnsfrCurrncy" value="{{$recordInfo -> currency}}">
                                <input type="hidden" name="contryCode" id="contryCode" value="">
                                <input type="hidden" name="amountDeduct" id="amountDeduct" value="">
                                <input type="hidden" name="senderCurrencyCode" id="senderCurrencyCode" value="">
                                <input type="hidden" name="access_token" id="access_token" value="{{$access_token}}">
                                <button class="sub-btn btn_sub" onclick="isExists();" type="button">Proceed</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="col-sm-12 mt-4"  id="update_plan">

                    <div class="">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

<!-- basic modal 
<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog md1">
        <div class="modal-content transfer-pop">
            <div class="transfer-fund-pop">
                <h4 class="text-center mb-3 ft-img"><img src="https://www.dafribank.com/public/img/front/Fundtransfer-thumb.svg"><br>Mobile Top-up</h4>
                <div class="filed-box">
                    <div class="form-control-new">
                        <label>Number:</label>
                        <input style="text-transform: uppercase;" type="text" id="recipNumber" value="" disabled>
                    </div>
                    <div class="form-control-new">
                        <label>Amount:</label>
                        <input type="text" id="recipAmount" placeholder="0" disabled>
                    </div>
                </div>

                <div class="filed-box">
                    <div class="form-control-new w100">
                        <label></label>
                        <textarea type="text" id="recipMsg" rows="7" cols="49" disabled style="resize: none;"></textarea>
                    </div>                    
                </div>


            </div>
            <div class="modal-footer pop-ok">
                <button type="button" class="btn btn-default" onclick="$('#fundTransfrForm').submit();">Confirm</button>
                <button type="button" class="btn btn-default bg-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>-->





<div class="modal" id="basicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="basicModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                        <div class="popup-form">
                            <div class="pop-logo">
                            <img src="{{HTTP_PATH}}/public/img/dafribank-logo-mobile.svg">
                        </div>
                           
                           <div class="popup-info">
                               <div class="popup-info-data">
                                   <span class="label-mini">Tel:</span> <span id="recipNumber"></span>
                               </div>

                               <div class="popup-info-data">
                                   <span class="label-mini">Amount:</span> <span id="recipAmount"></span>
                               </div>

                             

                               <div class="popup-info-data" id="cuncyConvrsnTF">
                               <span id="recipMsg"></span>
                               </div>

                            

                           </div>
                        <div class="form-btns-pop">
                            <button type="button" class="confrm-btn btn btn-default button_disable" onclick="$('#fundTransfrForm').submit();">Confirm</button>
                      
                        </div>
                    </div>
                </div>
            </div>
        </div>

<style>
    .modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1060;
    display: none;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
}
.modal.fade .modal-dialog {
    transition: transform .3s ease-out;
    transform: translate(0,-50px);
}
.pop1 .modal-dialog {
    max-width: 598px;
}
.modal-dialog {
    margin: 10px auto !important;
    padding: 0 10px;
}
.modal-content {
    border-radius: 15px;
}
.popup-form {
    padding: 20px 40px;
}
.pop-logo {
    text-align: center;
    margin-bottom: 30px;
}
.popup-info-data {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 500;
}
.popup-info-data .label-mini {
    color: #1deb8d;
}
.form-btns-pop {
    text-align: center;
}
.confrm-btn {
    font-size: 16px;
    background: #000;
    padding: 8px 20px;
    border-radius: 5px;
    text-transform: capitalize;
    color: #fff;
    border: 1px solid #000;
    cursor: pointer;
}
</style>





<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
{{ HTML::script('public/assets/js/intlTelInput.js')}}
<script>
    
    $(document).ready(function () {
    $("#fundTransfrForm").validate();
    });
    
                    var phone_number = window.intlTelInput(document.querySelector("#recipient_phone"), {
                        separateDialCode: true,
                        preferredCountries: false,
                        onlyCountries: ['ZA', 'NG', 'BW', 'LS', 'NA', 'SZ', 'CM', 'ZW', 'US', 'GB', 'GH', 'ZM', 'KE', 'PH', 'ID', 'BR', 'RW'],
                        hiddenInput: "recipient_phone",
                        utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
                    });

                    function isExists()
                    {
                        
                        var operator_type = document.getElementById('operator_type').value;
                        var to_curr = document.getElementById('trnsfrCurrncy').value;
                        var amount = document.getElementById('trnsfrAmnt').value;
                        
                        if(to_curr == '' || amount == '' || operator_type == ''){
                            $('#failed_message').html('Please verify your information and try again.');
                            $('#failed-alert-Modal').modal('show');
                        } else if($.isNumeric(amount) == false){
                            $('#failed_message').html('Please enter a valid number.');
                            $('#failed-alert-Modal').modal('show');
                        } else {
                        
                        
                        $.ajax({
                            url: "{!! HTTP_PATH !!}/checkTopup",
                            type: "POST",
                            data: {'to_curr': to_curr, 'amount': amount, _token: '{{csrf_token()}}'},
                            beforeSend: function () {
                    $('#loaderID').css("display", "flex");
                },
                            success: function (result) {
                                var json = $.parseJSON(result);
                                if (json.result == 1) {
                                    $('#recipNumber').html($('#contryCode').val()+''+$('#recipient_phone').val());
                                    $('#recipAmount').html(json.to_amt);
                                    
                                    if(json.currency_same == 1){
                                        var msg = 'You are about to Top-Up ' +json.to_amt + ' airtime through our eTop-Up.\n \n Your DafriBank account will be charged.'
                                    } else{
                                        var msg = 'You are about to Top-Up ' +json.to_amt + ' airtime through our eTop-Up. You have different currency ' +json.from_curr+ '. You need to pay '+json.from_amt +' for eTop-Up.\n \nYour DafriBank account will be charged.'
                                    }
                                    $('#recipMsg').html(msg);
                                    $('#basicModal').modal('show');
                                    $('#loaderID').css("display", "none");
                                }
                            }
                        });
                    }
                    }

                    function changeOperator(myStr) {
                        var coCode = $('.iti__selected-dial-code').html();
                        document.getElementById('contryCode').value = coCode;
                        
                        $('#amountDeduct').val('');
                        $('#trnsfrAmnt').val('');

                        var withSpace = myStr.length;

                        if (withSpace > 5) {
                            var phone = myStr;
                            var access_token = $('#access_token').val();
                            var contryCode = coCode;

                            $.ajax({
                                url: "{!! HTTP_PATH !!}/getOperator",
                                type: "POST",
                                beforeSend: function () {
                    $('#loaderID').css("display", "flex");
                },
                                data: {'access_token': access_token, 'phone': phone, 'contryCode': contryCode, _token: '{{csrf_token()}}'},
                                success: function (result) {

                                    if (result != 0) {
                                        $('#operator_set').html(result);
                                        $('#loaderID').css("display", "none");
                                        
                                        getPlan();
                                    }
//                    alert(result);

//                    $('#success_message').html('OTP sent successfully');
//                    $('#success-alert-Modal').modal('show');
                                }
                            });
                        }

                    }

                    function getPlan() {
                        $('#amountDeduct').val('');
                        $('#trnsfrAmnt').val('');
                        $('#update_plan').html('');
                        var access_token = $('#access_token').val();
                        var operator_id = $('#operator_type').val();

                        $.ajax({
                            url: "{!! HTTP_PATH !!}/getPlanData",
                            type: "POST",
                            data: {'access_token': access_token, 'operator_id': operator_id, _token: '{{csrf_token()}}'},
                            beforeSend: function () {
                    $('#loaderID').css("display", "flex");
                },
                            success: function (result) {

                                if (result != 0) {
                                    $('#update_plan').html(result);
                                }
                                $('#loaderID').css("display", "none");
                                
                              
    
//                    alert(result);

//                    $('#success_message').html('OTP sent successfully');
//                    $('#success-alert-Modal').modal('show');
                            }
                        });
                    }

                    function updatePrice(price, currency, id, payPrice,senderCurrencyCode) { 
                        $('#amountDeduct').val(payPrice);
                        $('#trnsfrAmnt').val(price);
                        $('#trnsfrCurrncy').val(currency);
                        $('#senderCurrencyCode').val(senderCurrencyCode);
                        $('.price').removeClass('active');
                        $('#'+id).addClass('active');
                    }

                    function validateFloatKeyPress(el, evt) {
                        var charCode = (evt.which) ? evt.which : event.keyCode;
                        var number = el.value.split('.');
                        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                            return false;
                        }
                        //just one dot
                        if (number.length > 1 && charCode == 46) {
                            return false;
                        }
                        //get the carat position
                        var caratPos = getSelectionStart(el);
                        var dotPos = el.value.indexOf(".");
                        if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1)) {
                            return false;
                        }
                        return true;
                    }

                    function getSelectionStart(o) {
                        if (o.createTextRange) {
                            var r = document.selection.createRange().duplicate()
                            r.moveEnd('character', o.value.length)
                            if (r.text == '')
                                return o.value.length
                            return o.value.lastIndexOf(r.text)
                        } else
                            return o.selectionStart
                    }
</script>
@endsection
