@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
@include('elements.side_menu')        
<!-- Page Content -->
<div id="page-content-wrapper">
@include('elements.top_header')
<div class="wrapper2">
                <div class="row">
                   <div class="coming-soonbox">
                    <h1>Coming Soon</h1>
                    <p>This page is under construction.</p>
                    {{HTML::image('public/img/front/plane.svg', SITE_TITLE,array('class' => 'plane'))}}
                </div>
                </div>
            </div>
</div>
<!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@endsection