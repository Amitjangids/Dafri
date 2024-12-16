@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">

    <!DOCTYPE html>
<html>
<head>
	<title>Laravel 5 - Stripe Payment Gateway Integration Example - ItSolutionStuff.com</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">
        .panel-title {
        display: inline;
        font-weight: bold;
        }
        .display-table {
            display: table;
        }
        .display-tr {
            display: table-row;
        }
        .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 61%;
        }
    </style>
</head>
<body>
  
<div class="container">
  
    <h1>Laravel 5 - Stripe Payment Gateway Integration Example <br/> ItSolutionStuff.com</h1>
  
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default credit-card-box">
                <div class="panel-heading display-table" >
                    <div class="row display-tr" >
                        <h3 class="panel-title display-td" >Payment Details</h3>
                        <div class="display-td" >                            
                            <img class="img-responsive pull-right" src="http://i76.imgup.net/accepted_c22e0.png">
                        </div>
                    </div>                    
                </div>
                <div class="panel-body">
  
                    @if (Session::has('success'))
                        <div class="alert alert-success text-center">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <p>{{ Session::get('success') }}</p>
                        </div>
                    @endif
  
                    {{ Form::open(array('url' => 'stripe', 'method' => 'post', 'id' => 'payment-form', 'class' => 'form-section require-validation','data-cc-on-file'=>"false","data-stripe-publishable-key"=>env('STRIPE_KEY')))}}  
                        @csrf
                        <input type="hidden" name="sendername" class="required sendername" value="{{$recordInfo->name}}" placeholder="Full Name">
                        <input type="hidden" name="senderemail" class="required senderemail" value="{{$recordInfo->email}}" placeholder="Email">
                        <input type="text" name="cardnumber" id="card_number" class="card-number" value="4242424242424242" placeholder="Credit Card Number">
                        <input type="hidden" name="zipcode" value="302012" class="required number" minlength="6" maxlength="6" placeholder="ZIP Code">
                        <div class="d-flex">
                            <input type="text" name="month" id="expiry_month" value="05" class="required number month" minlength="1" maxlength="2" placeholder="MM">
                            <input type="text" name="year" id="expiry_year" value="2023" class="required number year" minlength="4" maxlength="4" placeholder="YYYY">
                        </div>
                        <input type="hidden" name="email" class="required" value="{{$recordInfo->email}}" placeholder="">
                        <input type="text" id="cvv" name="cvv" class="required number cvv" value="123" minlength="3" maxlength="3" placeholder="CVV Code">

                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-lg btn-block" type="submit" onclick="paywithstripe()"  >Pay Now ($100)</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>        
        </div>
    </div>
      
</div>
  
</body>

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
  
<script type="text/javascript">

function paywithstripe() {
                $('#payment-form').serialize();
                $.ajax({
                url: "{!! HTTP_PATH !!}/stripe",
                        type: "POST",
                        data: $('#payment-form').serialize(),
                       
                        success: function (result) {
                            // console.log(result);
                            // if(result == '1'){
                            //     $('#payloader').hide();
                            //     alert('Booking payment completed successfully.');
                            //     window.location = "{!! HTTP_PATH !!}/user-dashboard";
                            // }
                            console.log(result);
                            if(result == '0'){
                               // $('#payloader').hide();
                               window.location = "{!! HTTP_PATH !!}/auth/add-fund";
                            } else{
                               // console.log(result);
                                function disableBack() {
                                       window.location.reload() 
                                    }
                                    window.onload = disableBack();
                                    window.onpageshow = function(e) {
                                        if (e.persisted)
                                            disableBack();
                                    }
                                window.location = result;
                            }
                        
                        }
                });
            }

            $(document).ready(function () {
                $("#payment-form").validate();
                $(function () {
                var $form = $(".require-validation");
                $('form.require-validation').bind('submit', function (e) {
                var $form = $(".require-validation"),
                        inputSelector = ['input[type=email]', 'input[type=password]',
                                'input[type=text]', 'input[type=file]',
                                'textarea'
                        ].join(','),
                        $inputs = $form.find('.required').find(inputSelector),
                        $errorMessage = $form.find('div.error'),
                        valid = true;
                $errorMessage.addClass('hide');
                $('.has-error').removeClass('has-error');
                $inputs.each(function (i, el) {
                var $input = $(el);
                if ($input.val() === '') {
                $input.parent().addClass('has-error');
                $errorMessage.removeClass('hide');
                e.preventDefault();
                }
                });
                if (!$form.data('cc-on-file')) {
                e.preventDefault();
                Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                // Stripe.createToken({
                // number: $('.card-number').val(),
                //         name: $('input[name=sendername]').val(),
                //         address_line1: $('input[name=zipcode]').val(),
                //         cvc: $('input[name=cvv]').val(),
                //         exp_month: $('input[name=month]').val(),
                //         exp_year: $('input[name=year]').val()
                // }, stripeResponseHandler);
                }
                });
                function stripeResponseHandler(status, response) {  
                if (response.error) {
                $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                /* token contains id, last4, and card type */
                var token = response['id'];
                $form.find('input[type=text]');
                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.get(0).submit();
                }
                }
                });
                });
</script>
</html>

      
    </div>
</div>

@endsection
