@extends('layouts.admin')
@section('content')
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

<div class="content-wrapper">
    <section class="content-header">
        <h1>NGN Conversion Rates</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> NGN Conversion Rates</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
           
            <?php $ngn_extra_per = DB::table('users')->where('id', 1)->first()->ngn_extra_per; ?>

            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::open(['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
            <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-2">
                        <b>Add on percentage</b>   
                        </div>
                        <div class="col-sm-3">
                        {{Form::text('ngn_extra_per',$ngn_extra_per, ['class'=>'form-control required','onkeypress'=>'return validateFloatKeyPress(this,event);','autocomplete'=>'off'])}}
                        </div>
                        <div class="col-sm-2">   
                        {{Form::submit('Apply', ['class' => 'btn btn-info'])}}
                        </div>
                    </div>
                <div class="box-body">
                <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <b>Live Exchange Rates</b>
                        </div>
                        <div class="col-sm-5">
                         <b>Effective Exchange Rates</b>
                        </div>
                    </div>
                    <?php global $currencyList; foreach($currencyList as $curency) { 
                     $ngn_conversion=convertCurrency($curency,'NGN',$ngn_extra_per);   
                    ?>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">1 {{$curency}} to NGN <span class="require">*</span></label>
                        <div class="col-sm-5">
                            {{Form::text('usd_value',$ngn_conversion['actual_rate'], ['class'=>'form-control required','readonly'])}}
                        </div>
                        <div class="col-sm-5">
                            {{Form::text('usd_value',$ngn_conversion['rate_after_per'], ['class'=>'form-control required','readonly'])}}
                        </div>
                    </div>

                    <?php } ?>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
    @endsection

    <script>
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

    <?php
    function convertCurrency($toCurrency, $frmCurrency,$ngn_extra_per=0) {
        $apikey = CURRENCY_CONVERT_API_KEY;
        $query = $toCurrency . "_" . $frmCurrency;
        $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
        $json = file_get_contents($curr_req);
        $obj = json_decode($json, true);
        $val = floatval($obj[$query]);
        // $val=432.349831;
        $extra_per=($val*$ngn_extra_per)/100;
        $actaul_price_after_per=$val+$extra_per;
        $actaul_price_after_per=number_format($actaul_price_after_per, 2, '.', ',');
        $arr=array();
        $arr['actual_rate']=number_format($val, 2, '.', ',');
        $arr['rate_after_per']=$actaul_price_after_per;
        return $arr;
        }
    ?>