@extends('layouts.inner')
@section('content')
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
                            USD {{Session::get('cryptoAmnt')}}
                        </div>
                    </div>
                </div>
                <div class="method-box mb-box">
                    <div class="heading-section wth-head">
                        <h5>Select Crypto options</h5>
                    </div>
                    <div class="select-crypto">
                        <select name="cryptoCurr" id="cryptoCurr" onchange="setDepositAddrs();">
                            <!--<option data-deposit-addr="0x77541C3058398De017dBF07aEBa4fbcBCd60e752" value="DBA">DBA</option>-->
                            <?php foreach($crypto_currency as $currency) { ?>
                            <option data-deposit-addr="{{$currency->address}}" value="{{$currency->name}}">
                            {{$currency->name}}</option>
                            <?php } ?>
                          
                           
    
                        </select>
                    </div>
                    <div class="neo-box">
                        <h5>Deposit Address</h5>
                        <div class="deposit-addr">
                            @php
                            $first_str = substr($crypto_currency[0]->address,0,8);
                            $last_str = substr($crypto_currency[0]->address, -8);
                            @endphp
                            <p id="dpostAddr">{{$first_str.'....'.$last_str}}</p> <a id="copy_id" href="javascript:copyTextToClipboard('{{$crypto_currency[0]->address}}');">Copy</a>
                        </div>
                    </div>
                    <div class="neo-box">
                        <h5>Transaction Hash Txt</h5>
                        <input type="text" name="blckChanURL" id="blckChanURL" placeholder="Enter URL">
                        <div class="text-center mt-2">
                            <a href="#" id="show-hidden-menu">What is this?</a>
                            <div class="slide-down hidden-menu" style="display: none;">
                                Blockchain URL or Transaction Hxt for your crypto payment looks similar to this. <a href="https://bscscan.com/tx/0x256c843ad447e66b5be216652bda18c81eb1ad1b432f57e5414d95966a7f48c7" target="_blank">https://bscscan.com/tx/0x256c843ad447e66b5be216652bda18c81eb1ad1b432f57e5414d95966a7f48c7 </a> Depending on the type of crypto currency you are sending. ERC20 and BEP20 Tokens will be similar to the above link while BTC, TRX, and DOT will be different.
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

function setDepositAddrs() {
    var depositAddr = $('#cryptoCurr').find(':selected').attr('data-deposit-addr');
    var first_str = depositAddr.substring(0, 8);
    var second_str = depositAddr.substring(depositAddr.length - 8, depositAddr.length);
    var final_str = first_str + '....' + second_str;
    $('#dpostAddr').html(final_str);
    $('#copy_id').attr("href", "javascript:copyTextToClipboard('" + depositAddr + "')");
    //$('#dpostAddr').html(depositAddr);    
}

function copyTextToClipboard(str) {
    var textArea = document.createElement("textarea");
    textArea.value = str; ///$('#dpostAddr').html();
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        //    alert("Deposit Address copied successfully");
        $('#blank_message').html('Deposit Address copied successfully');
        $('#blank-alert-Modal').modal('show');
        //console.log('Copying text command was ' + msg);
    } catch (err) {
        console.log('Oops, unable to copy');
    }

    document.body.removeChild(textArea);
}
</script>
<script type="text/javascript">
$(document).ready(function() {
    $('#show-hidden-menu').click(function() {
        $('.hidden-menu').slideToggle("slow");
        // Alternative animation for example
        // slideToggle("fast");
    });
});


function disable_submit()
    {
   
    $('.button_disable').prop('disabled', true);   
    return true;

    }


</script>
@endsection