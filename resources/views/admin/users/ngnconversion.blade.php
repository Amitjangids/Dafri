@extends('layouts.admin')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
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
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($exchange, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
                <div class="box-body">
                   <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-5 control-label"></label>    
                            <div class="col-sm-5">
                            <label class="control-label">Variation In Percentage (+,-)</label> 
                            <input type="number" class="form-control required" min="-100" max="100" name="other_currency_to_ngn" onkeypress="return validateFloatKeyPress(this,event);" value="{{$exchange['other_currency_to_ngn']}}">
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-5"></label>   
                            <label class="col-sm-3">Live Rates</label>   
                            <label class="col-sm-4">Effective Rates</label> 
                        </div>
                        <?php global $currencyList; foreach($currencyList as $curency) { if($curency!='NGN') { ?>
                             <div class="form-group">
                                <div class="col-sm-2"></div>
                                <label class="col-sm-3 control-label">1 {{$curency}} to NGN <span class="require">*</span></label>
                                <div class="col-sm-3">
                                    {{Form::text('', $actual_value[strtolower($curency).'_value'], ['class'=>'form-control required','onkeypress'=>'return validateFloatKeyPress(this,event);','readonly'])}}
                                </div>

                                <div class="col-sm-3">
                                    {{Form::text(strtolower($curency).'_value', null, ['class'=>'form-control required','onkeypress'=>'return validateFloatKeyPress(this,event);','readonly'])}}
                                </div>

                             </div>
                        <?php } } ?>
                   </div>
                   <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-5 control-label"></label>   
                            <div class="col-sm-5">
                            <label class="control-label">Variation In Percentage (+,-)</label> 
                            <input type="number" class="form-control required" min="-100" max="100" name="ngn_to_other_currency" onkeypress="return validateFloatKeyPress(this,event);" value="{{$exchange['ngn_to_other_currency']}}">
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-5"></label>  
                            <label class="col-sm-3">Live Rates</label>   
                            <label class="col-sm-4">Effective Rates</label>  
                        </div>
                        <?php global $currencyList; foreach($currencyList as $curency) { if($curency!='NGN') { ?>
                    <div class="form-group">
                        <div class="col-sm-2"></div>

                        <label class="col-sm-3 control-label">1 NGN to {{$curency}} <span class="require">*</span></label>
                      
                        <div class="col-sm-3">
                            <?php 
                            $act_rate=$actual_value[strtolower($curency).'_value'];
                            $actual_rate=  number_format((1/$act_rate), 6, '.', '');
                        
                            ?>
                            {{Form::text('', $actual_rate, ['class'=>'form-control required','onkeypress'=>'return validateFloatKeyPress(this,event);','readonly'])}}
                        </div>

                        <div class="col-sm-3">
                            {{Form::text(strtolower($curency).'_val', $exchange_second[strtolower($curency).'_value'], ['class'=>'form-control required','onkeypress'=>'return validateFloatKeyPress(this,event);','readonly'])}}
                        </div>
                        
                    </div>
                    <?php } } ?>
                   </div>
               <!--  <div class="form-group">
                <label class="col-sm-3 control-label"></label>    
                <div class="col-sm-3" style="margin-left:-50px;">
                <label class="control-label">Variation In Percentage (+,-)</label> 
                <input type="number" class="form-control required" min="-100" max="100" name="other_currency_to_ngn" onkeypress="return validateFloatKeyPress(this,event);" value="{{$exchange['other_currency_to_ngn']}}">
                </div>

                <label class="col-sm-3 control-label"></label>   
                <div class="col-sm-3">
                <label class="control-label">Variation In Percentage (+,-)</label> 
                <input type="number" class="form-control required" min="-100" max="100" name="ngn_to_other_currency" onkeypress="return validateFloatKeyPress(this,event);" value="{{$exchange['ngn_to_other_currency']}}">
                </div>
                </div> -->

                <!-- <div class="form-group">
                <label class="col-sm-2"></label>   
                <label class="col-sm-2">Live Rates</label>   
                <label class="col-sm-2">Effective Rates</label>   
                <label class="col-sm-2"></label>  
                <label class="col-sm-2">Live Rates</label>   
                <label class="col-sm-2">Effective Rates</label>   
                </div> -->
               
                
                  
                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/admins/dashboard')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>

    <script>
      function validateFloatKeyPress(el, evt) {
          var charCode = (evt.which) ? evt.which : event.keyCode;
          var number = el.value.split('.');
          var minus = el.value.split('-');
          if (charCode != 46 && charCode > 31 && charCode!=45 && (charCode < 48 || charCode > 57)) {
              return false;
          }
          //just one dot
          if (number.length > 1 && charCode == 46) {
              return false;
          }
          if (minus.length > 1 && charCode == 45) {
              return false;
          }
          
          //get the carat position
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