@extends('layouts.inner')
@section('content')
<!-- <script>
$(document).ready(function () {
    $('#failed_message').html("Services are not available at the moment");
    $('#failed-alert-Modal').modal('show');
})</script> -->
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
    <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            {{ Form::open(array('method' => 'post', 'id' => 'addAcnt', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit_bank();')) }}
            <div class="modal-dialog">
                <div class="modal-content bank-detail-form">
                    <h4>Bank Detail
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h4>

                    <div class="row">
                         <div class="col-sm-12">
                            <div class="form-group form-field">
                                <label>
                                    Select Bank Transfer
                                </label>
                                <div class="field-box">

                                <select class="required type_transfer" id="type_transfer" name="type_transfer">
            <option value="US Bank Transfer">US Bank Transfer</option>
            <option value="UK Bank Transfer">UK Bank Transfer</option>
            <option value="IBAN EU Transfer">IBAN EU Transfer</option>
            <option value="Transfer To Wise">Transfer To Wise </option>
            <option value="Nigeria Bank Transfer">Nigeria Bank Transfer</option>
            <option value="SA Bank Transfer">SA Bank Transfer</option>
            <option value="Botswana Bank Transfer">Botswana Bank Transfer</option>
            <option value="Swaziland Bank Transfer">Swaziland Bank Transfer</option>
            <option value="Lesotho Bank Transfer">Lesotho Bank Transfer</option>
            <option value="Namibia Bank Transfer">Namibia Bank Transfer</option>
        </select>
              
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-sm-6 cotb"  style="display:none">
                            <div class="form-group form-field">
                                <label>
                                Country of The Bank
                                </label>
                                <div class="field-box">
                                    <input name="cotb" id="cotb" class="required" placeholder="Enter Country of The Bank"  type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-field">
                                <label>
                                    Account Holder
                                </label>
                                <div class="field-box">
                                    <input name="accName" id="accName" class="required" placeholder="Enter Account Holder Name" type="text">
                                </div>
                            </div>
                        </div>



                        <div class="col-sm-6 we"  style="display:none">
                            <div class="form-group form-field">
                                <label>
                                    Wisa Email
                                </label>
                                <div class="field-box">
                                    <input name="wisaEmail" id="wisaEmail" class="required" placeholder="Enter Wisa Email"  type="text">
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-sm-6 bic"  style="display:none">
                            <div class="form-group form-field">
                                <label>
                                    BIC
                                </label>
                                <div class="field-box">
                                    <input name="bic" id="bic" class="required" placeholder="Enter BIC"   type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 rn">
                            <div class="form-group form-field">
                                <label>
                                    Routing number
                                </label>
                                <div class="field-box">
                                    <input name="routNumbr" id="routNumbr" class="required" placeholder="Routing number" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 an">
                            <div class="form-group form-field">
                                <label>
                                    Account Number
                                </label>
                                <div class="field-box">
                                    <input name="accNumbr" id="accNumbr" class="required" placeholder="Enter Account Number" type="text" onkeypress="return validateFloatKeyPress(this,event);">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 can">
                            <div class="form-group form-field">
                                <label>
                                    Confirm Account Number
                                </label>
                                <div class="field-box">
                                    <input name="confirm_accNumbr" id="confirm_accNumbr" class="required" placeholder="Confirm Account Number" equalTo = '#accNumbr' type="text" onkeypress="return validateFloatKeyPress(this,event);"   >
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 ibn" style="display:none">
                            <div class="form-group form-field">
                                <label>
                                IBAN Number
                                </label>
                                <div class="field-box">
                                    <input name="iBan" id="iBan" class="required" placeholder="Enter IBAN Number" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 at">
                            <div class="form-group form-field">
                                <label class="accounttypename">
                                    Account Type
                                </label>
                                <div class="field-box">
                                    <input name="acctTyp" id="acctTyp" class="required" placeholder="Enter Account Type" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 swc" style="display:none">
                            <div class="form-group form-field">
                                <label>
                                Swift Code 
                                </label>
                                <div class="field-box">
                                    <input name="swc" id="swc" class="required" placeholder="Enter Swift Code" type="text">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 c">
                            <div class="form-group form-field">
                                <label>
                                    Currency 
                                </label>
                                <div class="field-box">
                                    <input name="currncy" id="currncy" class="required" placeholder="Enter Currency  " type="text">
                                </div>
                            </div>
                        </div>  
                            <div class="col-sm-6 sc" style="display:none">
                            <div class="form-group form-field">
                                <label>
                                    Sort Code 
                                </label>
                                <div class="field-box">
                                    <input name="sorCode" id="sorCode" class="required" placeholder="Enter Sort Code" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 bn">
                            <div class="form-group form-field">
                                <label>
                                    Bank Name
                                </label>
                                <div class="field-box">
                                    <input name="bnkName" id="bnkName" class="required" placeholder="Enter Bank Name " type="text">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 ba">
                            <div class="form-group form-field">
                                <label>
                                    Bank Address
                                </label>
                                <div class="field-box">
                                    <textarea name="bnkAdd" id="bnkAdd" class="required" placeholder="Enter Bank Address" rows="4" cols="50"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 rfp">
                            <div class="form-group form-field">
                                <label>
                                    Reference
                                </label>
                                <div class="field-box">
                                    <textarea name="reasonPay" id="reasonPay" class="required" placeholder="Enter Reference" rows="4" cols="50"> </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 brc" style="display:none">
                            <div class="form-group form-field">
                                <label class="brcname">
                                    Branch Code 
                                </label>
                                <div class="field-box">
                                    <input name="brnchCod" id="brnchCod" class="required" placeholder="Enter Branch Code" type="text">
                                </div>
                            </div>
                        </div>
                       
                        
                        
                        <div class="col-sm-12">
                            <input type="hidden" name="addAccount" value="true">
                            <div class="form-group form-field">
                                <button class="sub-btn button_disable_bank" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{Form::close()}}
      
        </div>
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
                        <div class="drop-text-field">
                            <input type="text" name="withdrawAmnt" placeholder="Enter amount" min="10" autocomplete="OFF" onkeypress='return validateFloatKeyPress(this, event);'>
                            <div class="withdraw_currency currency_all">{{$recordInfo->currency}}</div>
                        </div>
                    </div>
                </div>

                <div class="method-box mb-box">
                    <div class="heading-section wth-head">
                        <h5>Beneficiary list</h5>
                    </div>
                    @if(!empty($bankAccnt))
                    @php 
                    $sno = 1;
                    @endphp
                    @foreach ($bankAccnt as $bnkAcnt)	
                    <div class="account-num">
                        <div class="w-b-name">
                        @if($bnkAcnt->account_number)
                            <h5>{{strtoupper($bnkAcnt->account_name)}}</h5>
                            <span>{{$bnkAcnt->account_number}}</span>
                           
                            @elseif($bnkAcnt->iBan)
                            <h5>{{$bnkAcnt->iBan}}</h5>
                            <span>{{$bnkAcnt->bic}}</span>
                            @elseif($bnkAcnt->wisaEmail)
                            <h5>{{$bnkAcnt->wisaEmail}}</h5>
                            <span>{{$bnkAcnt->bic}}</span>
                            @endif
                        </div>
                        <div class="cart-rt">
                            <div class="radio-card">
                                <input id="radio-{{$sno}}" name="account_id" type="radio" value="{{$bnkAcnt->id}}">
                                <label for="radio-{{$sno}}" class="radio-label"></label>
                            </div>
                            <div class="delete-icon"><a href="{{URL::to('auth/delete-withdraw-account/'.$bnkAcnt->id)}}">{{HTML::image('public/img/front/delete1.svg', SITE_TITLE)}}</a></div>
                        </div>
                    </div>
                    @php
                    $sno++;
                    @endphp
                    @endforeach
                    @endif

                    <div class="add-new">
                        <a href="#" data-toggle="modal" data-target="#basicModal"><span>+</span> Add New Beneficiary</a>
                    </div>
                    <input type="hidden" name="saveWithdrawReq" value="true">
                    <input type="hidden" name="addAccount" value="false">

                    @if($recordInfo->country == 'South Africa')
                    <div class="mb-box">
                        <div class="inst_normal">
                            <div class=" check-new">
                                <input type="radio" id="fastPay" name="payment_type" value="fast" checked="checked">
                                <label for="fastPay">Immediate Payment</label>
                            </div>
                            
                            <div class=" check-new">
                                <input type="radio" id="normalPay" name="payment_type" value="normal">
                                <label for="normalPay">Normal Payment</label>
                            </div>
                            
                            <div class="not_txt">
                                Note: Immediate Payment carry an intermediary fee of R55. T&Cs Apply
                            </div>
                        </div>
                    </div>
                    @endif
                    
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