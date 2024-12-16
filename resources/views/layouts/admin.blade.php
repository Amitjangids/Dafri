<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{$title.TITLE_FOR_LAYOUT}}</title>
        <meta name="robots" content="noindex, nofollow">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <link rel="icon" href="{!! FAVICON_PATH !!}" type="image/x-icon"/>
        <meta name="robots" content="noindex, nofollow">
        
        {{ HTML::style('public/assets/css/bootstrap.min.css')}}
        {{ HTML::style('public/assets/css/AdminLTE.min.css?ver=1.4')}}
        {{ HTML::style('public/assets/css/all-skins.min.css?ver=1.4')}}
        {{ HTML::style('public/assets/css/admin.css?ver=1.1')}}
        {{ HTML::style('public/assets/css/font-awesome.min.css')}}

        {{ HTML::script('public/assets/js/jquery-2.1.0.min.js')}}
        {{ HTML::script('public/assets/js/jquery.validate.js')}}
        {{ HTML::script('public/assets/js/app.min.js')}}
        {{ HTML::script('public/assets/js/ajaxsoringpagging.js')}}
        {{ HTML::script('public/assets/js/listing.js')}}
        {{ HTML::script('public/assets/js/bootstrap.min.js')}}
        
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            @include('elements.admin.header')
            @include('elements.admin.left_menu')
            @yield('content')
        </div>
        
</html>
