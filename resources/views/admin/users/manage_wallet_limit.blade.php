@extends('layouts.admin')
@section('content')

<script type="text/javascript">
    $(document).ready(function () {
    $("#adminForm").validate();
    });
</script>

{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}

<div class="content-wrapper">
    <section class="content-header">
        <h1>Configure Transaction's Limit</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-user"></i> <span>Manage Personal Users</span></a></li>
            <li class="active">Configure Transaction's Limit</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($user_limit, ['method' => 'post', 'id' => 'adminForm', 'class' => 'form form-signin']) }}
            <div class="form-horizontal">
                <div class="box-body">

                <div class="form-group">
                        <label class="col-sm-2 control-label">Username</label>
                        <div class="col-sm-10" style="padding-top: 7px;margin-bottom: 0;">
                            @if($recordInfo->user_type == 'Personal')
                            @php $name  = strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)@endphp
                            @elseif($recordInfo->user_type == 'Business')
                            @php $name  = strtoupper($recordInfo->director_name)@endphp
                            @elseif($recordInfo->user_type == 'Agent' && $recordInfo->first_name != "")
                            @php $name  = strtoupper($recordInfo->first_name.' '.$recordInfo->last_name)@endphp
                            @elseif($recordInfo->user_type == 'Agent' && $recordInfo->director_name != "")
                            @php $name  = strtoupper($recordInfo->director_name)@endphp
                            @endif
                            {{$name}}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Account Number</label>
                        <div class="col-sm-10" style="padding-top: 7px;margin-bottom: 0;">
                            {{$recordInfo->account_number}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Daily Limit <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('daily_limit',null,['class'=>'form-control required','id'=>'daily_limit', 'placeholder'=>'Daily Limit', 'autocomplete' => 'off','onkeypress'=>"return validateFloatKeyPress(this,event);"])}}
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Weekly Limit <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('week_limit',null,  ['class'=>'form-control required','id'=>'week_limit', 'placeholder'=>'Weekly Limit', 'autocomplete' => 'off','onkeypress'=>"return validateFloatKeyPress(this,event);"])}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Monthly Limit <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('month_limit',null, ['class'=>'form-control required','id'=>'month_limit', 'placeholder'=>'Monthly Limit', 'autocomplete' => 'off','onkeypress'=>"return validateFloatKeyPress(this,event);"])}}
                        </div>
                    </div>
                



                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info button_disable','onclick'=>"setCountryCode();"])}}
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
    
    {{ HTML::script('public/assets/js/intlTelInput.js')}}

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

    $(document).ready(function () {
   
    window.onload = () => {
    const myInput = document.getElementById('daily_limit');
    myInput.onpaste = e => e.preventDefault();

    const myInput1 = document.getElementById('week_limit');
    myInput1.onpaste = e => e.preventDefault();

    const myInput2 = document.getElementById('month_limit');
    myInput2.onpaste = e => e.preventDefault();

    }


    });


</script>    


    @endsection