@extends('layouts.admin')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
        $("#adminForm").validate();
    });
</script>
<style>
    .form-horizontal .value_set {
    padding-top: 7px;
    margin-bottom: 0;
}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Wallet Balance</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="javascript:void(0);"><i class="fa fa-cogs"></i> Configuration</a></li>
            <li class="active">Wallet Balance</li>
        </ol>
    </section>   

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($adminInfo, ['method' => 'post', 'id' => 'adminForm', 'class' => 'form form-signin']) }}
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Wallet Balance (USD) <span class="require"></span></label>
                        <div class="col-sm-10 value_set">
                            {{number_format(floor($adminInfo->wallet_amount*100)/100,10,'.',',')}}
                        </div> 
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Wallet Balance (DBA) <span class="require"></span></label>
                        <div class="col-sm-10 value_set">
                            {{number_format(floor($adminInfo->dba_wallet_amount*100)/100,10,'.',',')}}
                        </div> 
                    </div>

                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
</div>
@endsection