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

        $("#radio").click(function () {
            $(".main_section").hide();
            $("#station_sec").show();
        });
        $("#advertising").click(function () {
            $(".main_section").hide();
            $("#agency_sec").show();
        });
        $("#advertiser").click(function () {
            $(".main_section").hide();
            $("#advertiser_sec").show();
        });
    });
</script>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Card</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/cards')}}"><i class="fa fa-gift"></i> <span>Manage Cards</span></a></li>
            <li class="active"> Edit Card</li>
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
                        <label class="col-sm-2 control-label">Code <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('serial_number', null, ['class'=>'form-control required', 'placeholder'=>'Code', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Pin Number <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('pin_number', null, ['class'=>'form-control required', 'placeholder'=>'Pin Number', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Value <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('card_value', null, ['class'=>'form-control required', 'placeholder'=>'Value', 'autocomplete' => 'off'])}}
                        </div>
                    </div>     
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Instruction <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::textarea('instruction', null, ['class'=>'form-control required', 'placeholder'=>'Instruction', 'autocomplete' => 'off'])}}
                        </div>
                    </div>

                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/cards/carddetail/'.$cslug)}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
</div>
@endsection