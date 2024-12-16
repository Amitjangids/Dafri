@extends('layouts.admin')
@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Crypto Currency For WithDraw</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/merchants')}}"><i class="fa fa-user"></i> <span>Manage Crypto Currency For WithDraw</span></a></li>
            <li class="active"> Add Crypto Currency For WithDraw</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::open( ['method' => 'post', 'id' => 'adminForm', 'enctype' => "multipart/form-data",'onsubmit'=>'return disable_submit();']) }}
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Currency Name <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('name', null, ['class'=>'form-control required', 'placeholder'=>'Enter Crypto Currency Name', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        <input type="hidden" name="contryCode" id="contryCode" value="">
                        {{Form::submit('Submit', ['class' => 'btn btn-info button_disable','onclick'=>"setCountryCode();"])}}
                        <!-- {{Form::reset('Reset', ['class' => 'btn btn-default canlcel_le'])}} -->
                    </div>
                </div>
            </div>

            <input type="hidden" name="type" value="2">

            {{ Form::close()}}
        </div>
    </section>
    
    @endsection