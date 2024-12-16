@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                {{ Form::open(array('method' => 'post', 'id' => 'depstCrypto', 'class' => 'withdraw-form','onsubmit'=>'return disable_submit();','url'=>'auth/send-crypto-withdraw-otp')) }}
                <div class="col-sm-6 border-right mob-big">
                    <div class="ee er_msg">@include('elements.errorSuccessMessage')</div>
                    <div class="heading-section">
                        <h5>Crypto Withdrawal</h5>
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
                           {{'USD '.base64_decode(Session::get('withdrawAmntCrypUSDto64'))}}
                        </div>
                    </div>
                </div>
                <div class="method-box mb-box">
                    <div class="heading-section wth-head">
                        <h5>Select Crypto options</h5>
                    </div>
                    <div class="select-crypto">
                    <select name="cryptoCurr" id="cryptoCurr">
                    <?php foreach($crypto_currency as $value) { ?>

                    <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                     
                    <?php } ?>
                    </select>
                    </div>
                  
                     <div class="neo-box">
                    	<h5>Payout Address</h5>
                        <input type="text" name="payoutAddr" id="payoutAddr" placeholder="Payout Address" autocomplete="off" required>

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

function setDepositAddrs()
{
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
//    alert("Deposit Address copied successfully");
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

</script>
@endsection