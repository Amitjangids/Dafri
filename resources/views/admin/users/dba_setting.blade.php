@extends('layouts.admin')
@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <h1>Admin Setting</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> Edit Admin Setting</li>
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
                        <label class="col-sm-3 control-label">DBA Affiliate(%) <span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="number" class="form-control amt" required placeholder="DBA Affiliate(%)" autocomplete="off" name="dba_aff_per" onkeypress='return validateFloatKeyPress(this,event);' value="{{$recordInfo->dba_aff_per}}">    
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Interest (%) for Daily<span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control amt1" required placeholder="Interest (%) for daily" autocomplete="off" onkeypress='return validateFloatKeyPress(this,event);' name="dba_int_daily" value="{{$recordInfo->dba_int_daily}}">    
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Interest (%) for 60 Days<span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control amt2" required placeholder="Interest (%) for 60 Days" autocomplete="off" name="dba_int_60" onkeypress='return validateFloatKeyPress(this,event);' value="{{$recordInfo->dba_int_60}}">    
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Interest (%) for 90 Days<span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control amt3" required placeholder="Interest (%) for 90 Days" autocomplete="off" name="dba_int_90" onkeypress='return validateFloatKeyPress(this,event);' value="{{$recordInfo->dba_int_90}}">    
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Interest (%) for 180 Days<span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control amt4" required placeholder="Interest (%) for 180 Days" autocomplete="off" name="dba_int_180" onkeypress='return validateFloatKeyPress(this,event);' value="{{$recordInfo->dba_int_180}}">    
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Interest (%) for 365 Days<span class="require">*</span></label>
                        <div class="col-sm-9">
                        <input type="text" class="form-control amt5" required placeholder="Interest (%) for 365 Days" autocomplete="off" name="dba_int_365" onkeypress='return validateFloatKeyPress(this,event);' value="{{$recordInfo->dba_int_365}}">    
                        </div>
                    </div>


                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/merchants')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
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