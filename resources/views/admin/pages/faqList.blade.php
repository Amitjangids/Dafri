@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Frequently Asked Questions</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> Manage FAQ's</li>
        </ol>
    </section>
	
	<section class="content" style="min-height: 110px !important;">
    <div class="box box-info">
	<div class="admin_search">
	  <div class="add_new_record"><a href="{{URL::to('admin/pages/addFaq')}}" class="btn btn-default"><i class="fa fa-plus"></i> Add Question</a></div>
	</div>	
	</div>
	</section>	

    <section class="content">
        <div class="box box-info">
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            <div class="m_content" id="listID">
                @include('elements.admin.pages.listFaq')
            </div>
        </div>
    </section>
</div>
@endsection