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
        {{ HTML::style('public/assets/css/AdminLTE.min.css')}}
        {{ HTML::style('public/assets/css/font-awesome.min.css')}}

        {{ HTML::script('public/assets/js/jquery-2.1.0.min.js')}}
        {{ HTML::script('public/assets/js/jquery.validate.js')}}
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            @yield('content')
        </div>
        
    </body>
</html>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
  <script>
  $(document).on("click", "#xx", function(e) {
      alert()
    bootbox.alert("Hello world!", function() {
        console.log("Alert Callback");
    });
});
  </script>