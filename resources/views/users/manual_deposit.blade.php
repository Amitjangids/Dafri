@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <style>
        .bank-copy-main .radio-card input[type="radio"]:checked+.radio-label:before {
    box-shadow: inset 0 0 0 4px #000;
    background: #fff!important;
}
</style>
    <!-- Page Content -->
    <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="bank-detail-transfer">
                    <h5 id="contryNm"> SOUTH AFRICA </h5>
                    <div class="inner-b-data">
                        <h6 id="accntNm">DAFRITECH (PTY) LTD </h6>
                        <div id="accntNum" class="bank-data"><strong>ACCOUNT:</strong> 4099929441</div>
                        <div id="brnchCod" class="bank-data"><strong>BRANCH CODE:</strong> 632005</div>
                        <div id="accntTyp`" class="bank-data"><strong>ACCOUNT TYPE:</strong> CHEQUE</div>
                    </div>
                    <div id="bankNm" class="b-name">ABSA BANK</div>
                </div>
                <button type="button" class="btn btn-default text-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                {{ Form::open(array('method' => 'post', 'id' => 'addFundFrm', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit();')) }}
                <div class="col-sm-6 border-right mob-big">
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="heading-section">
                        <h5>Add funds</h5>
                    </div>
                    <div class="vcard-wrapper">
                        @php
                        $card_class = getUserCardType($recordInfo->account_category);
                        @endphp
                        <div class="vcard {{$card_class}}">
                            <span>Available balance</span>
                            <h2>{{$recordInfo->currency}} {{number_format($recordInfo->wallet_amount,2,'.',',')}}</h2>
                            <h6>{{$recordInfo->gender}} @if($recordInfo->user_type == 'Personal')
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Business')
                                @include('elements.business_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                                @include('elements.personal_short_name')
                                @elseif($recordInfo->user_type == 'Agent' && $recordInfo->business_name != "")
                                @include('elements.business_short_name')
                                @endif</h6>
                        </div>
                        {{HTML::image('public/img/front/vcard-shadow.png', SITE_TITLE,['class'=>'shadow-bottom'])}}
                    </div>
                    <hr>
                    <div class="deposit-amt">
                        <div class="heading-section">
                            <h5>Deposit amount</h5>
                        </div>
                        <div class="depo-am">
                            {{Session::get('manualDepstCurrnc').' '.Session::get('manualDepstAmnt')}}
                        </div>
                    </div>
                </div>
                <div class="method-box mb-box">
                    <div class="heading-section wth-head">
                        <h5>Reference Number</h5>
                    </div>
                    <div class="ref-nim">
                        <input style="font-size: 19px;" type="text" name="refNum" id="refNum" placeholder="4567467" value="{{$recordInfo->account_number}}" readonly> <a style="font-size: 19px;" href="javascript:copyTextToClipboard('{{$recordInfo->account_number}}');">Copy</a>

                    </div>
                                        <div class="note">Note: Deposit made without this reference number will not be credited.</div>

                    <div class="bank-trans">
                        <div class="heading-section wth-head">
                            <h5>Select Bank</h5>
                        </div>
                        <div class="trans-bank-thumb">
                            <div class="t-bank-name"> {{HTML::image('public/img/front/absa.png', SITE_TITLE)}} <span>ABSA BANK</span></div>
                            <div class="bank-copy-main">
                                <a href="javascript:setPopup('1');" class="bank-detail">View Bank Details</a> <span></span> <a href="javascript:copyTextToClipboard1('SOUTH AFRICA\n\nDAFRITECH (PTY) LTD\n\nACCOUNT: 4099929441\n\nBRANCH CODE: 632005\n\nACCOUNT TYPE: CHEQUE\n\nABSA BANK');" class="copy-det">Copy</a> <span></span>
                                <div class="radio-card">
                                    <input id="radio-1" name="payment_method" type="radio" value="ABSA BANK">
                                    <label for="radio-1" class="radio-label"></label>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="trans-bank-thumb">
                            <div class="t-bank-name"> {{HTML::image('public/img/front/standard-bank.png', SITE_TITLE)}} <span>STANDARD BANK</span></div>
                            <div class="bank-copy-main">
                                <a href="javascript:setPopup('2');" class="bank-detail">View Bank Details</a> <span></span> <a href="javascript:copyTextToClipboard1('SOUTH AFRICA\n\nDAFRITECH (PTY) LTD\n\nACCOUNT: 10143348661\n\nBRANCH CODE: 51001\n\nACCOUNT TYPE: CHEQUE\n\nSTANDARD BANK');" class="copy-det">Copy</a>
<span></span>
                                 <div class="radio-card">
                                    <input id="radio-2" name="payment_method" type="radio" value="STANDARD BANK">
                                    <label for="radio-2" class="radio-label"></label>
                                </div>
                            </div>
                        </div> -->

                        <div class="trans-bank-thumb">
                            <div class="t-bank-name"> {{HTML::image('public/img/front/fnb.png', SITE_TITLE)}} <span>FNB BW</span></div>
                            <div class="bank-copy-main">
                                <a href="javascript:setPopup('3');" class="bank-detail">View Bank Details</a> <span></span> <a href="javascript:copyTextToClipboard1('BOTSWANA\n\nDAFRITECH (PTY) LTD\n\nACCOUNT: 62881068889\n\nBRANCH CODE: 281467\n\nACCOUNT TYPE: CHEQUE\n\nFNB BW');" class="copy-det">Copy</a>
                                <span></span>
                                 <div class="radio-card">
                                    <input id="radio-3" name="payment_method" type="radio" value="FNB BW">
                                    <label for="radio-3" class="radio-label"></label>
                                </div>
                            </div>
                        </div>
                      <!--  <div class="trans-bank-thumb">
                            <div class="t-bank-name"> {{HTML::image('public/img/front/zenith.png', SITE_TITLE)}} <span>ZENITH BANK</span></div>
                            <div class="bank-copy-main">
                                <a href="javascript:setPopup('4');" class="bank-detail">View Bank Details</a> <span></span> <a href="javascript:copyTextToClipboard1('NIGERIA\n\nDAFRITECHNOLOGIES LTD\n\nACCOUNT: 1017518610\n\nACCOUNT TYPE: CHEQUE\n\nZENITH BANK');" class="copy-det">Copy</a>
                                <span></span>
                                 <div class="radio-card">
                                    <input id="radio-4" name="payment_method" type="radio" value="ZENITH BANK (1017518610)">
                                    <label for="radio-4" class="radio-label"></label>
                                </div>
                            </div>
                        </div>-->

                        <div class="trans-bank-thumb">
                            <div class="t-bank-name"> {{HTML::image('public/img/front/zenith.png', SITE_TITLE)}} <span>ZENITH BANK</span></div>
                            <div class="bank-copy-main">
                                <a href="javascript:setPopup('5');" class="bank-detail">View Bank Details</a> <span></span> <a href="javascript:copyTextToClipboard1('NIGERIA\n\nDAFRIPAY SOLUTION LTD\n\nACCOUNT: 1223093934\n\nACCOUNT TYPE: CHEQUE\n\nZENITH BANK');" class="copy-det">Copy</a>
                                <span></span>
                                 <div class="radio-card">
                                    <input id="radio-5" name="payment_method" type="radio" value="ZENITH BANK">
                                    <label for="radio-5" class="radio-label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="trans-bank-thumb">
                            <div class="t-bank-name"> {{HTML::image('public/img/front/union.png', SITE_TITLE)}} <span>UNION BANK</span></div>
                            <div class="bank-copy-main">
                                <a href="javascript:setPopup('6');" class="bank-detail">View Bank Details</a> <span></span> <a href="javascript:copyTextToClipboard1('NIGERIA\n\nDAFRIPAY SOLUTION LTD\n\nACCOUNT: 0175325479\n\nACCOUNT TYPE: CHEQUE\n\nUNION BANK');" class="copy-det">Copy</a>
                                <span></span>
                                 <div class="radio-card">
                                    <input id="radio-6" name="payment_method" type="radio" value="UNION BANK">
                                    <label for="radio-6" class="radio-label"></label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <button class="sub-btn button_disable" type="submit">
                        Submit
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    $(".active-hover").click(function() {});

    $(".inner-mathod-box").hover(
        function() {
            $(".inner-mathod-box").removeClass("active-hover");
            $(this).addClass("active-hover");
        }
    );

});

function copyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
                                                                    $('#blank_message').html('Deposit reference copied successfully');
                                                                    $('#blank-alert-Modal').modal('show');
//		$('.er_msg').html('<div class="alert alert-success"><strong>Success!</strong> View Bank Details copied successfully.</div>');
        //alert("Your account details copied successfully");
        //console.log('Copying text command was ' + msg);
    } catch (err) {
                                                                    $('#error_message').html('Oops, unable to copy');
                                                                    $('#error-alert-Modal').modal('show');
//        $('.er_msg').html('<div class="alert alert-warning"><strong>Failed!</strong> Oops, unable to copy.</div>');
		//console.log('Oops, unable to copy');
    }

    document.body.removeChild(textArea);
}
function copyTextToClipboard1(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
                                                                    $('#blank_message').html('View Bank Details copied successfully');
                                                                    $('#blank-alert-Modal').modal('show');
//		$('.er_msg').html('<div class="alert alert-success"><strong>Success!</strong> View Bank Details copied successfully.</div>');
        //alert("Your account details copied successfully");
        //console.log('Copying text command was ' + msg);
    } catch (err) {
                                                                    $('#error_message').html('Oops, unable to copy');
                                                                    $('#error-alert-Modal').modal('show');
//        $('.er_msg').html('<div class="alert alert-warning"><strong>Failed!</strong> Oops, unable to copy.</div>');
		//console.log('Oops, unable to copy');
    }

    document.body.removeChild(textArea);
}

function setPopup(flag) {
    if (flag == 1) {
        $('#contryNm').html('SOUTH AFRICA');
        $('#accntNm').html('DAFRITECH (PTY) LTD');
        $('#accntNum').html('<strong>ACCOUNT:</strong> 4099929441');
        $('#brnchCod').html('<strong>BRANCH CODE:</strong> 632005');
        $('#accntTyp').html('<strong>ACCOUNT TYPE:</strong> CHEQUE');
        $('#bankNm').html('ABSA BANK');
        $('#basicModal').modal('show');
    } else if (flag == 2) {
        $('#contryNm').html('SOUTH AFRICA');
        $('#accntNm').html('DAFRITECH (PTY) LTD');
        $('#accntNum').html('<strong>ACCOUNT:</strong> 10143348661');
        $('#brnchCod').html('<strong>BRANCH CODE:</strong> 51001');
        $('#accntTyp').html('<strong>ACCOUNT TYPE:</strong> CHEQUE');
        $('#bankNm').html('STANDARD BANK');
        $('#basicModal').modal('show');
    } else if (flag == 3) {
        $('#contryNm').html('BOTSWANA');
        $('#accntNm').html('DAFRITECH (PTY) LTD');
        $('#accntNum').html('<strong>ACCOUNT:</strong> 62881068889');
        $('#brnchCod').html('<strong>BRANCH CODE:</strong> 281467');
        $('#accntTyp').html('<strong>ACCOUNT TYPE:</strong> CHEQUE');
        $('#bankNm').html('FNB BW');
        $('#basicModal').modal('show');
    } else if (flag == 4) {
        $('#contryNm').html('NIGERIA');
        $('#accntNm').html('DAFRITECHNOLOGIES LTD');
        $('#accntNum').html('<strong>ACCOUNT:</strong> 1017518610');
        $('#brnchCod').html('<strong>BRANCH CODE:</strong> -');
        $('#accntTyp').html('<strong>ACCOUNT TYPE:</strong> BUSINESS CHEQUE');
        $('#bankNm').html('ZENITH BANK');
        $('#basicModal').modal('show');
    }else if (flag == 5) {
        $('#contryNm').html('NIGERIA');
        $('#accntNm').html('DAFRIPAY SOLUTION LTD');
        $('#accntNum').html('<strong>ACCOUNT:</strong> 1223093934');
        $('#brnchCod').html('<strong>BRANCH CODE:</strong> -');
        $('#accntTyp').html('<strong>ACCOUNT TYPE:</strong> BUSINESS CHEQUE');
        $('#bankNm').html('ZENITH BANK');
        $('#basicModal').modal('show');
    }else if (flag == 6) {
        $('#contryNm').html('NIGERIA');
        $('#accntNm').html('DAFRIPAY SOLUTION LTD');
        $('#accntNum').html('<strong>ACCOUNT:</strong> 0175325479');
        $('#brnchCod').html('<strong>BRANCH CODE:</strong> -');
        $('#accntTyp').html('<strong>ACCOUNT TYPE:</strong> BUSINESS CHEQUE');
        $('#bankNm').html('UNION BANK');
        $('#basicModal').modal('show');
    }
}

function disable_submit()
    {
   
    $('.button_disable').prop('disabled', true);   
    return true;

    }

</script>
@endsection