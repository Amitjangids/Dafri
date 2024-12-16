@extends('layouts.admin')
@section('content')
<script type="text/javascript">
    $(document).ready(function () {
        $("#adminForm").validate();
    });
 </script>
{{ HTML::script('public/assets/js/ckeditor/ckeditor.js')}}
<script type="text/javascript">
    $(document).ready(function() {
        CKEDITOR.replace( 'description', {
            toolbar :
                [
                    ['ajaxsave'],
                    ['Styles','Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-'],
                    ['Cut','Copy','Paste','PasteText'],
                    ['Undo','Redo','-','RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Maximize', 'Image', 'Table','Link', 'Unlink']
            ],
            filebrowserUploadUrl : '<?php echo HTTP_PATH;?>/admin/pages/pageimages',
            language: '',
            height: 300,
            //uiColor: '#884EA1'
        });
    });
</script>
 
 
<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Blog</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/blogs')}}"><i class="fa fa-pages"></i> <span>Manage Blogs</span></a></li>
            <li class="active"> Add Blog</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{ Form::open(array('method' => 'post', 'id' => 'blogForm', 'enctype' => "multipart/form-data")) }}
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Category <span class="require">*</span></label>
                        <div class="col-sm-10"> 
                            {{Form::select('category_id', $category,null, ['class' => 'form-control required','placeholder' => 'Select Category'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Blog Title <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('title', null, ['class'=>'form-control required', 'placeholder'=>'Blog Title', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Blog Description <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::textarea('description', null, ['class'=>'form-control required', 'placeholder'=>'Blog Description', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-2 control-label">Blog Image <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::file('blogImage', null, ['class'=>'form-control required', 'placeholder'=>'Blog Image', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-2 control-label">Blog Read Time <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('blogReadTm', null, ['class'=>'form-control required', 'placeholder'=>'Blog Read Time', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="box-footer">
                        <label class="col-sm-2 control-label" for="inputPassword3">&nbsp;</label>
                        {{Form::submit('Submit', ['class' => 'btn btn-info'])}}
                        <a href="{{ URL::to( 'admin/blogs')}}" title="Cancel" class="btn btn-default canlcel_le">Cancel</a>
                    </div>
                </div>
            </div>
            {{ Form::close()}}
        </div>
    </section>
@endsection