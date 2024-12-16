@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Blogs</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> Manage Blogs</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
			<div class="admin_search">
                <form method="POST" action="<?php echo HTTP_PATH; ?>/admin/users" accept-charset="UTF-8" id="adminSearch"><input name="_token" type="hidden" value="aPMV3IeCrSYckUYYsjeN2MR77YNmp66bNyEdBZTx">
                <div class="form-group align_box dtpickr_inputs">
                    <span class="hints">Search by Username or Email Address</span>
                    <span class="hint"><input class="form-control" placeholder="Search by keyword" autocomplete="off" name="keyword" type="text"></span>
                    <div class="admin_asearch">
                        <div class="ad_s ajshort"><button class="btn btn-info admin_ajax_search" type="button">Submit</button></div>
                        <div class="ad_cancel"><a href="<?php echo HTTP_PATH; ?>/admin/users" class="btn btn-default canlcel_le">Clear Search</a></div>
                    </div>
                </div>
                </form>
                <div class="add_new_record"><a href="<?php echo HTTP_PATH; ?>/admin/blogs/add" class="btn btn-default"><i class="fa fa-plus"></i> Add New Blog</a></div>
            </div>
            <div class="m_content" id="listID">
                @include('elements.admin.pages.blogs')
            </div>
        </div>
    </section>
</div>
@endsection