@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="after-submit-popup">
                        <button type="button" class="btn btn-default" data-dismiss="modal">X</button>
                        <h4>
                            Success!
                        </h4>
                        <div class="success-msg">
                            {{HTML::image('public/img/front/success-icon.svg', SITE_TITLE)}}
                        </div>
                    </div>
                    <div class="suss-msg">
                        <p>Manual Deposit Request Saved Successfully.
                            Our finance team will check
                            and get back to you soon </p>
                    </div>
                </div>
            </div>
        </div>
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row ">
                {{Form::model($recordInfo, ['method' => 'post', 'id' => 'addFundFrm', 'enctype' => "multipart/form-data", 'class' => 'withdraw-form row-pay']) }}     

                <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                <div class="heading-section wth-head col-sm-12">
                    <h5>Pay with card</h5>
                </div>

                <div class="form-group pay-card col-sm-6">
                    <label>Card Number</label>
                    {{Form::text('card_number', null, ['class'=>'required', 'id'=>'card_number', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>

                <div class="form-group pay-card col-sm-3">
                    <label>Expiry Month</label>
                    {{Form::text('expiry_month', null, ['class'=>'required', 'id'=>'expiry_month', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-3">
                    <label>Expiry Year</label>
                    {{Form::text('expiry_year', null, ['class'=>'required', 'id'=>'expiry_year', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>CVC</label>
                    {{Form::text('cvv', null, ['class'=>'required', 'id'=>'cvv', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>

                <div class="form-group pay-card drop-text-field col-sm-6">
                    <label>Amount</label>
                    {{Form::text('amount', Session::get('creditAmount'), ['class'=>'required', 'id'=>'amount', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                    <div class="withdraw_currency">{{$recordInfo->currency}}</div>
                </div>

                <!--                <div class="form-group pay-card col-sm-12">
                                    <label>Description</label>
                                    <textarea  name="description"></textarea>
                                </div>-->
                <div class="heading-section wth-head col-sm-12">
                    <h6>Billing Details</h6>
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>Cardholder Name</label>
                    @if($recordInfo->user_type == 'Personal')
                    @php $name  = ucwords($recordInfo->first_name.' '.$recordInfo->last_name)@endphp
                    @elseif($recordInfo->user_type == 'Business')
                    @php $name  = ucwords($recordInfo->director_name)@endphp
                    @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                    @php $name  = ucwords($recordInfo->first_name.' '.$recordInfo->last_name)@endphp
                    @elseif($recordInfo->user_type == 'Agent' && $recordInfo->director_name != "")
                    @php $name  = ucwords($recordInfo->director_name)@endphp
                    @endif

                    {{Form::text('name', $name, ['class'=>'required', 'id'=>'name_on_card', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>Address line1</label>
                    {{Form::text('address_line1', $recordInfo->addrs_line1, ['class'=>'required', 'id'=>'address_line1', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>Address line2</label>
                    {{Form::text('address_line2', $recordInfo->addrs_line2, ['class'=>'required', 'id'=>'address_line2', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>Postal / ZIP code</label>
                    {{Form::text('postcode', null, ['class'=>'required', 'id'=>'postcode', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>City</label>
                    {{Form::text('city', null, ['class'=>'required', 'id'=>'city', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="form-group pay-card col-sm-6">
                    <label>State / County / Province / Region</label>
                    {{Form::text('district', null, ['class'=>'required', 'id'=>'district', 'autocomplete'=>'OFF', 'minlength' => 1, 'maxlength' => 50])}}
                </div>
                <div class="select-crypto col-sm-6 pay-card select-pay">
                    <label>Country code</label>
                    {{Form::select('currency', $countrList,$recordInfo->country, ['class' => 'required','placeholder' => 'Choose country','id'=>'cryptoCurr'])}}  

                </div>
                <!--                <div class="form-group pay-card col-sm-6">
                                    <label>Phone</label>
                                    {{Form::text('phone', null, ['id'=>'phone','class'=>'required digits', 'placeholder'=>'Enter your mobile number', 'minlength'=>6, 'autocomplete'=>'OFF'])}}
                                </div>
                                <div class="form-group pay-card col-sm-6">
                                    <label>Email</label>
                                    {{Form::text('email', Cookie::get('user_email_address'), ['class'=>'required email', 'id'=>'email', 'placeholder'=>'Enter your business email', 'autocomplete'=>'OFF'])}}
                                </div>-->
                <!--                    <div class="inner-mathod-box">
                    <div class="math-select">
                        <div class="radio-card">
                            <input id="radio-4" name="payment_method" type="radio" value="paypal">
                            <label for="radio-4" class="radio-label"></label>
                        </div>
                        <span>PayPal</span>
                    </div>
<div class="svg-icon paypal-logo">
                     {{HTML::image('public/img/front/paypal-logo.svg', SITE_TITLE)}}
                 </div>
                </div>-->
                <!-- <div class="inner-mathod-box">
                    <div class="math-select">
                        <div class="radio-card">
                            <input id="radio-7" name="payment_method" type="radio" value="manual_deposit">
                            <label for="radio-7" class="radio-label"></label>
                        </div>
                        <span>Manual deposit</span>
                    </div>
                    <div class="svg-icon">
                        {{HTML::image('public/img/front/ManualDeposit.svg', SITE_TITLE)}}
                    </div>
                </div> -->
                <div class="col-sm-6 m-auto mt-new-3">
                    <button class="sub-btn" type="submit">
                        Add Fund
                    </button>
                </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>
    <style>
        .iti--allow-dropdown .iti__flag-container, .iti--separate-dial-code .iti__flag-container {
            height: 45px !important;
        }

        .select-pay select{
            text-align: left !important;
        }
    </style>
    <script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".active-hover").click(function () {});
            $(".inner-mathod-box").hover(
                    function () {
                        $(".inner-mathod-box").removeClass("active-hover");
                        $(this).addClass("active-hover");
                    }
            );
        });
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

    <!--    {{ HTML::style('public/assets/css/front/card.css')}}-->
    {{ HTML::script('public/assets/js/front/creditCardValidator.js')}}
    <script>

        function cardFormValidate() {
            var cardValid = 0;
            //card number validation
            $('#card_number').validateCreditCard(function (result) {
                var cardType = (result.card_type == null) ? '' : result.card_type.name;
                if (cardType == 'Visa') {
                    var backPosition = result.valid ? '2px -163px, 312px -87px' : '2px -163px, 312px -61px';
                } else if (cardType == 'MasterCard') {
                    var backPosition = result.valid ? '2px -247px, 312px -87px' : '2px -247px, 312px -61px';
                } else if (cardType == 'Maestro') {
                    var backPosition = result.valid ? '2px -289px, 312px -87px' : '2px -289px, 312px -61px';
                } else if (cardType == 'Discover') {
                    var backPosition = result.valid ? '2px -331px, 312px -87px' : '2px -331px, 312px -61px';
                } else if (cardType == 'Amex') {
                    var backPosition = result.valid ? '2px -121px, 312px -87px' : '2px -121px, 312px -61px';
                } else {
                    var backPosition = result.valid ? '2px -121px, 312px -87px' : '2px -121px, 312px -61px';
                }
                $('#card_number').css("background-position", backPosition);
                if (result.valid) {
                    $("#card_type").val(cardType);
                    $("#card_number").removeClass('required');
                    cardValid = 1;
                } else {
                    $("#card_type").val('');
                    $("#card_number").addClass('required');
                    cardValid = 0;
                }
            });
            //card details validation
            var cardName = $("#name_on_card").val();
            var expMonth = $("#expiry_month").val();
            var expYear = $("#expiry_year").val();
            var cvv = $("#cvv").val();
            var regName = /^[a-z ,.'-]+$/i;
            var regMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
            var regYear = /^2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
            var regCVV = /^[0-9]{3,3}$/;
            if (cardValid == 0) {
                $("#card_number").addClass('required');
                $("#card_number").focus();
                return false;
            } else if (!regMonth.test(expMonth)) {
                $("#card_number").removeClass('required');
                $("#expiry_month").addClass('required');
                $("#expiry_month").focus();
                return false;
            } else if (!regYear.test(expYear)) {
                $("#card_number").removeClass('required');
                $("#expiry_month").removeClass('required');
                $("#expiry_year").addClass('required');
                $("#expiry_year").focus();
                return false;
            } else if (!regCVV.test(cvv)) {
                $("#card_number").removeClass('required');
                $("#expiry_month").removeClass('required');
                $("#expiry_year").removeClass('required');
                $("#cvv").addClass('required');
                $("#cvv").focus();
                return false;
            } else {
                $("#card_number").removeClass('required');
                $("#expiry_month").removeClass('required');
                $("#expiry_year").removeClass('required');
                $("#cvv").removeClass('required');
                $('#cardSubmitBtn').prop('disabled', false);
                return true;
            }
        }

        $(document).ready(function () {

            $("#addFundFrm").validate();
            $('#addFundFrm input[type=text]').on('keyup', function () {
                cardFormValidate();
            });
        });
        function getPCIPublicKey() {
            $.ajax({
                url: "<?php echo HTTP_PATH; ?>/getPublicKey",
                type: "GET",
                success: function (result) {
                    alert(result);
                }
            });
        }

    </script>



    <script lang="JavaScript" src="<?php echo PUBLIC_PATH; ?>/assets/js/front/openpgp.min.js"></script>
    <script lang="JavaScript" src="<?php echo PUBLIC_PATH; ?>/assets/js/front/openpgp.js"></script>
    <script lang="JavaScript">

        var dataToEncrypt = jQuery.parseJSON('{ "number": "4242424242424242" },{ "cvv": "523" }');
        var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
            function adopt(value) {
                return value instanceof P ? value : new P(function (resolve) {
                    resolve(value);
                });
            }
            return new (P || (P = Promise))(function (resolve, reject) {
                function fulfilled(value) {
                    try {
                        step(generator.next(value)); } catch (e) {
                        reject(e);
                    }
                }
                function rejected(value) {
                    try {
                        step(generator["throw"](value)); } catch (e) {
                        reject(e); }
                }
                function step(result) {
                    result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                }
                step((generator = generator.apply(thisArg, _arguments || [])).next());
            });
        };
        var __generator = (this && this.__generator) || function (thisArg, body) {
            var _ = {label: 0, sent: function () {
                    if (t[0] & 1)
                        throw t[1];
                    return t[1];
                }, trys: [], ops: []}, f, y, t, g;
            return g = {next: verb(0), "throw": verb(1), "return": verb(2)}, typeof Symbol === "function" && (g[Symbol.iterator] = function () {
                return this;
            }), g;
            function verb(n) {
                return function (v) {
                    return step([n, v]);
                };
            }
            function step(op) {
                if (f)
                    throw new TypeError("Generator is already executing.");
                while (_)
                    try {
                        if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done)
                            return t;
                        if (y = 0, t)
                            op = [op[0] & 2, t.value];
                        switch (op[0]) {
                            case 0:
                            case 1:
                                t = op;
                                break;
                            case 4:
                                _.label++;
                                return {value: op[1], done: false};
                            case 5:
                                _.label++;
                                y = op[1];
                                op = [0];
                                continue;
                            case 7:
                                op = _.ops.pop();
                                _.trys.pop();
                                continue;
                            default:
                                if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) {
                                    _ = 0;
                                    continue;
                                }
                                if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) {
                                    _.label = op[1];
                                    break;
                                }
                                if (op[0] === 6 && _.label < t[1]) {
                                    _.label = t[1];
                                    t = op;
                                    break;
                                }
                                if (t && _.label < t[2]) {
                                    _.label = t[2];
                                    _.ops.push(op);
                                    break;
                                }
                                if (t[2])
                                    _.ops.pop();
                                _.trys.pop();
                                continue;
                        }
                        op = body.call(thisArg, _);
                    } catch (e) {
                        op = [6, e];
                        y = 0;
                    } finally {
                        f = t = 0;
                    }
                if (op[0] & 5)
                    throw op[1];
                return {value: op[0] ? op[1] : void 0, done: true};
            }
        };
// example of dynamically fetching openpgp library when using webpack
        var openpgpModule = Promise.resolve().then(function () {
            return require(
                    /* webpackChunkName: "openpgp,
                     webpackPrefetch: true" */
                    'openpgp');
        });
        var pciEncryptionKey = getPCIPublicKey();
        /**
         * Encrypt card data function
         */
        return function (dataToEncrypt) {
            return __awaiter(this, void 0, Promise, function () {
                var decodedPublicKey, openpgp, options;
                var _a;
                return __generator(this, function (_b) {
                    switch (_b.label) {
                        case 0:
                            decodedPublicKey = atob(pciEncryptionKey.publicKey);
                            return [4 /*yield*/, openpgpModule];
                        case 1:
                            openpgp = _b.sent();
                            _a = {
                                message: openpgp.message.fromText(JSON.stringify(dataToEncrypt))
                            };
                            return [4 /*yield*/, openpgp.key.readArmored(decodedPublicKey)];
                        case 2:
                            options = (_a.publicKeys = (_b.sent()).keys,
                                    _a);
                            return [2 /*return*/, openpgp.encrypt(options).then(function (ciphertext) {
                                    return {
                                        encryptedData: btoa(ciphertext.data),
                                        keyId: pciEncryptionKey.keyId
                                    };
                                })];
                    }
                });
            });
        }
        ;




    </script>
    @endsection