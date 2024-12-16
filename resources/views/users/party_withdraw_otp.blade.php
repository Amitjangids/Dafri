@extends('layouts.inner')
@section('content')
<script>
    $(document).ready(function () {
    $("#addAcnt").validate();
    });</script>

<style>
    .not_txt{
        padding-bottom: 5px;
    }
    .form-group.form-field select {
    height: 32px;
    border-radius: 10px;
    background: #f5f5f5;
    padding: 5px;
    border: none;
    text-indent: 20px;
    font-size: 12px;
    width: 100%;
}
.form-group.form-field textarea {
 
    border-radius: 10px;
    background: #f5f5f5;
    padding: 5px;
    border: none;
    text-indent: 20px;
    font-size: 12px;
    width: 100%;
}
textarea {
  resize: none;
}

</style>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                {{ Form::open(array('method' => 'post', 'id' => 'depstCrypto', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit();')) }}
                <div class="col-sm-6 border-right mob-big">
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="heading-section">
                        <h5>3rd Party Pay</h5>
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
                            <h5>Amount</h5>
                        </div>
                        <div class="depo-am">
                            {{$recordInfo->currency.' '.base64_decode(Session::get('party_withdrawAmntMW64'))}}
                        </div>
                    </div>
                </div>

                <div class="method-box mb-box">
                    <div class="heading-section wth-head otp-box">
                        <h5>Enter OTP</h5>
                        <p>Please enter OTP received on your registered email address.</p>
                    </div>
                    <div class="form-group form-field ">

                        <div class="field-box otp-textbox">
                            <input name="otp_verify" id="otp_verify" class="required" placeholder="Enter OTP" type="text">
                        </div>
                        <div class="text-right resend"><a href="javascript:resetOTP();">Resend OTP</a></div>
                    </div>


                    <input type="hidden" name="saveWithdrawReq" value="false">
                    <input type="hidden" name="addAccount" value="false">
                    <input type="hidden" name="validateOTP" value="true">
                    <button class="sub-btn button_disable" type="submit">
                        Submit
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<?php
Session::forget('error_message');
Session::forget('success_message');
Session::save();
?>
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
    function resetOTP() {
    var user_id = {{$recordInfo -> id}};
    $.ajax({
    //url: "{!! HTTP_PATH !!}/resentOtp",
    url: "<?php echo HTTP_PATH; ?>/auth/resentOtpManualWithdraw",
            type: "POST",
            data: {'user_id': user_id, _token: '{{csrf_token()}}'},
            success: function (result) {
            //alert(result);
            $('#success_message').html('OTP sent successfully');
            $('#success-alert-Modal').modal('show');
            }
    });
    }

    function setDepositAddrs() {
    var depositAddr = $('#cryptoCurr').find(':selected').attr('data-deposit-addr');
    $('#dpostAddr').html(depositAddr);
    }

    function copyTextToClipboard() {
    var textArea = document.createElement("textarea");
    textArea.value = $('#dpostAddr').html();
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
//        alert("Deposit Address copied successfully");
    $('#blank_message').html('Deposit Address copied successfully');
    $('#blank-alert-Modal').modal('show');
    //console.log('Copying text command was ' + msg);
    } catch (err) {
    console.log('Oops, unable to copy');
    }

    document.body.removeChild(textArea);
    }

    function disable_submit()
    {
    $('.button_disable').prop('disabled', true);
    return true;
    }

    function disable_submit_bank()
    {
    var empty_field = 0;
    $(".required").each(function() {
    if ($(this).val() == "")
    {
    empty_field = 1;
    }
    });
    if (empty_field == 0 && $(".required").hasClass('error') == false)
    {
    $('.button_disable_bank').prop('disabled', true);
    return true;
    }
    return false;
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


    
</script>
<script>
$(document).ready(function(){
    $('.rn').show();
        $('.rn :input').addClass('required');
        $('.an').show();
        $('.an :input').addClass('required');
        $('.can').show();
        $('.can :input').addClass('required');
        $('.at').show();
        $('.at :input').addClass('required');
        $('.c').show();
        $('.c :input').addClass('required');
        $('.bn').show();
        $('.bn :input').addClass('required');
        $('.ba').show();
        $('.ba :input').addClass('required');
        $('.rfp').show();
        $('.rfp :input').addClass('required');
        $('.ibn').hide();
        $('.ibn :input').removeClass('required');
        $('.sc').hide();
        $('.sc :input').removeClass('required');
        $('.brc').hide();
        $('.brc :input').removeClass('required');
        $('.bic').hide();
        $('.bic :input').removeClass('required');
        $('.we').hide();
        $('.we :input').removeClass('required');
        $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");

    $("select.type_transfer").change(function(){
        var selectedCountry = $(this).children("option:selected").val();
      if(selectedCountry=="US Bank Transfer"){
       
        $('.rn').show();
        $('.rn :input').addClass('required');
        $('.an').show();
        $('.an :input').addClass('required');
        $('.can').show();
        $('.can :input').addClass('required');
        $('.at').show();
        $('.at :input').addClass('required');
        $('.c').show();
        $('.c :input').addClass('required');
        $('.bn').show();
        $('.bn :input').addClass('required');
        $('.ba').show();
        $('.ba :input').addClass('required');
        $('.rfp').show();
        $('.rfp :input').addClass('required');
        $('.ibn').hide();
        $('.ibn :input').removeClass('required');
        $('.sc').hide();
        $('.sc :input').removeClass('required');
        $('.brc').hide();
        $('.brc :input').removeClass('required');
        $('.bic').hide();
        $('.bic :input').removeClass('required');
        $('.we').hide();
        $('.we :input').removeClass('required');
        $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");

       }else if(selectedCountry=="UK Bank Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').hide();
$('.at :input').removeClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.ba').show();
$('.ba :input').addClass('required');
$('.rfp').show();
$('.rfp :input').addClass('required');
$('.ibn').show();
$('.ibn :input').addClass('required');
$('.sc').show();
$('.sc :input').addClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
 $('.we').hide();
  $('.we :input').removeClass('required');
  $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
}else if(selectedCountry=="IBAN EU Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').hide();
$('.an :input').removeClass('required');
$('.can').hide();
$('.can :input').removeClass('required');
$('.at').hide();
$('.at :input').removeClass('required');
$('.bn').hide();
$('.bn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').show();
$('.bic :input').addClass('required');
$('.ibn').show();
$('.ibn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.ba').show();
$('.ba :input').addClass('required');
$('.we').hide();
  $('.we :input').removeClass('required');
  $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
}else if(selectedCountry=="Transfer To Wise"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').hide();
$('.an :input').removeClass('required');
$('.can').hide();
$('.can :input').removeClass('required');
$('.at').hide();
$('.at :input').removeClass('required');
$('.bn').hide();
$('.bn :input').removeClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.rfp').show();
$('.rfp :input').addClass('required');
$('.we').show();
 $('.we :input').addClass('required');
 $('.cotb').hide();
        $('.cotb :input').removeClass('required');
        $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
}else if(selectedCountry=="Nigeria Bank Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').hide();
$('.brc :input').removeClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').hide();
$('.cotb :input').removeClass('required');
$('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Type of Account');
 $('#acctTyp').attr("placeholder", "Enter Type of Account");
}else if(selectedCountry=="SA Bank Transfer"){

$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').show();
$('.brc :input').addClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').hide();
 $('.cotb :input').removeClass('required');
 $('.swc').hide();
 $('.swc :input').removeClass('required');
 $('.brcname').html('Branch Code');
 $('#brnchCod').attr("placeholder", "Enter Branch Code");
 $('.accounttypename').html('Type of Account');
 $('#acctTyp').attr("placeholder", "Enter Type of Account");
}else if(selectedCountry=="Bank Wire Transfer (Global)"){
    
$('.brcname').html('Branch Code/Routing/Sortcode');
$('#brnchCod').attr("placeholder", "Enter Branch Code/Routing/Sortcode'");
$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').show();
$('.brc :input').addClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').show();
$('.ba :input').addClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').show();
$('.rfp :input').addClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').show();
 $('.cotb :input').addClass('required');
 $('.swc').show();
 $('.swc :input').addClass('required');
 $('.accounttypename').html('Account Type');
 $('#acctTyp').attr("placeholder", "Enter Account Type");
 
}else if(selectedCountry=="Botswana Bank Transfer" || selectedCountry=="Swaziland Bank Transfer" || selectedCountry=="Lesotho Bank Transfer" || selectedCountry=="Namibia Bank Transfer"){
    $('.accounttypename').html('Type of Account');
    $('#acctTyp').attr("placeholder", "Enter Type of Account");
$('.brcname').html('Branch Code');
$('#brnchCod').attr("placeholder", "Enter Branch Code");
$('.rn').hide();
$('.rn :input').removeClass('required');
$('.an').show();
$('.an :input').addClass('required');
$('.can').show();
$('.can :input').addClass('required');
$('.at').show();
$('.at :input').addClass('required');
$('.bn').show();
$('.bn :input').addClass('required');
$('.c').show();
$('.c :input').addClass('required');
$('.sc').hide();
$('.sc :input').removeClass('required');
$('.brc').show();
$('.brc :input').addClass('required');
$('.bic').hide();
$('.bic :input').removeClass('required');
$('.ba').hide();
$('.ba :input').removeClass('required');
$('.ibn').hide();
$('.ibn :input').removeClass('required');
$('.rfp').hide();
$('.rfp :input').removeClass('required');
$('.we').hide();
 $('.we :input').removeClass('required');
 $('.cotb').hide();
 $('.cotb :input').removeClass('required');
 $('.swc').hide();
 $('.swc :input').removeClass('required');
 
}


    });
});
</script>   
@endsection