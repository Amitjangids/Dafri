@extends('layouts.admin')
@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <h1>Gift Card / Airtime Daily Limit Setting</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> Edit Gift Card / Airtime Daily Limit Setting</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($recordInfo, ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data"]) }}            
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Gift Card Limit for Daily (USD) <span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="number" class="form-control amt" required placeholder="Gift Card Limit" autocomplete="off" name="limits_giftcard" onkeypress='return validateFloatKeyPress(this,event);' value="{{$recordInfo->limits_giftcard}}">    
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">AirTime Limit for Daily (USD)<span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control amt1" required placeholder="AirTime Limit for Daily" autocomplete="off" onkeypress='return validateFloatKeyPress(this,event);' name="limits_airtime" value="{{$recordInfo->limits_airtime}}">    
                        </div>
                    </div>

               
                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
          
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

window.onload = () => {
 const myInput = document.getElementsByClassName('amt')[0];
 myInput.onpaste = e => e.preventDefault();

 const myInput1 = document.getElementsByClassName('amt1')[0];
 myInput1.onpaste = e => e.preventDefault();

 const myInput2 = document.getElementsByClassName('amt2')[0];
 myInput2.onpaste = e => e.preventDefault();

 const myInput3 = document.getElementsByClassName('amt3')[0];
 myInput3.onpaste = e => e.preventDefault();

 const myInput4 = document.getElementsByClassName('amt4')[0];
 myInput4.onpaste = e => e.preventDefault();

 const myInput5 = document.getElementsByClassName('amt5')[0];
 myInput5.onpaste = e => e.preventDefault();


}



   </script>






    @endsection