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
        CKEDITOR.replace( 'faq_ans', {
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
        <h1>Edit FAQ</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="{{URL::to('admin/pages/faq-list')}}"><i class="fa fa-pages"></i> <span>Manage FAQ's</span></a></li>
            <li class="active"> Edit FAQ</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
            </div>
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            {{Form::model($recordInfo, ['method' => 'post', 'id' => 'edtFaqForm', 'enctype' => "multipart/form-data"]) }}
            <div class="form-horizontal">
                <div class="box-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Question <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::textarea('faq_ques', null, ['class'=>'form-control required', 'placeholder'=>'Question', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Answer <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::textarea('faq_ans', null, ['class'=>'form-control required', 'placeholder'=>'Answer', 'autocomplete' => 'off'])}}
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-2 control-label">Sort Order <span class="require">*</span></label>
                        <div class="col-sm-10">
                            {{Form::text('sort_order', null, ['class'=>'form-control required', 'placeholder'=>'Sort Order', 'autocomplete' => 'off'])}}
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