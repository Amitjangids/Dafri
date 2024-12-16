@extends('layouts.inner')
@section('content')

<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <?php global $currencyList;?>
        <div class="wrapper2">
            <div class="row">
                <div class="heading-section col-sm-12">
                    <h5>Exchange</h5>
                    <span>Note : You will be charged USD {{$fee_value}} or equivalent on each currency exchange request.</span>
                    
                </div>
                <div class="col-sm-8">
                    {{Form::open( ['method' => 'post', 'id' => 'exchangeForm']) }}
                    <div class="exchange-box">
                        <div class="exchange-field-box">
                            <label>You will pay</label>
                            <div class="drop-text-field">
                                <input type="text" name="from_amount" id="from_amount" placeholder="Enter amount" class="required" number='true'>
                                {{Form::select('from_currency', $currencyList,$recordInfo->currency, ['class' => 'dropdown-arrow required','id'=>'from_currency'])}}
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="ex-icon" onclick="changeValues()"> 
                            {{HTML::image('public/img/front/exchange-icon.svg', SITE_TITLE)}}
                        </a>
                        <div class="exchange-field-box">
                            <label>Recipient will receive </label>
                            <div class="drop-text-field">
                                <input type="text" name="to_amount" id="to_amount" placeholder="Enter amount" class="">
                                {{Form::select('to_currency', $currencyList,null, ['class' => 'dropdown-arrow','id'=>'to_currency'])}}
                            </div>
                        </div>
                        <div class="conv-rate" id="rate_section" style="display: none;">
                            <p>1 USD = 380.50 NGN</p>
                            <p>Currency exchange request charges paid - USD 0.50</p>
                            <p>  <a href="{{HTTP_PATH}}/auth/fund-transfer" class="cp-link">Pay Now </a>  </p>
                        </div>

                      
<div class="updateterm">
                        <button class="sub-btn" type="button" onclick="exchangeCurrency()">
                            Exchange
                        </button>

                        </div>
                    </div>
                    {{ Form::close()}}
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#exchangeForm").validate();
        $("#from_amount").on("keyup", function(e) {
            $('#rate_section').html('');
            $('#rate_section').hide();
            $('#to_amount').val('');

            $('.updateterm').html('<button class="sub-btn" type="button" onclick="exchangeCurrency()">Exchange</button>');
});
        
    });
    
    function changeValues(){
        var from_amount = $('#from_amount').val();
        var from_currency = $('#from_currency').val();
        var to_amount = $('#to_amount').val();
        var to_currency = $('#to_currency').val();
        
        $('#from_amount').val(to_amount);
        $('#from_currency').val(to_currency);
        $('#to_amount').val(from_amount);
        $('#to_currency').val(from_currency);
    }
    
    function exchangeCurrency(){ 
        if($('#from_amount').val() == ''){
            $('#error_message').html('Please enter amount.');
            $('#error-alert-Modal').modal('show');
        } else { 
            $.ajax({
                type: 'POST',
                url: "<?php echo HTTP_PATH; ?>/exchange-currency",
                data: $('#exchangeForm').serialize(),
                cache: false,
                beforeSend: function () {
                    $('#ploader').show();
                    $('#loaderID').css("display", "flex");
                },
                success: function (result) {                    
                    var json = $.parseJSON(result);
                    if (json.result == 1) {
                        $('#to_amount').val(json.to_amount);
                        $('#rate_section').html(json.data);
                        $('.updateterm').html('');
                        $('.updateterm').html(json.buttontext);
                        
                        $('#rate_section').show();
                    } else{
                        $('#error_message').html(json.data);
                        $('#error-alert-Modal').modal('show');
                    }  
                    $('#ploader').hide();
                    $('#loaderID').css("display", "none");
                },
                error: function (data) {

                }
            });
        }
    }
</script>
@endsection