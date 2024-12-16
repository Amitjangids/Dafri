<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dafri Premier</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="shortcut icon" type="img/svg" href="{{ PUBLIC_PATH }}/assets/fronts/images/fevicon.svg" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/bootstrap.min.css') }}" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"> --}}

    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/style.css?v=1.1') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/media.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/fronts/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
</head>

<body>
    @yield('content')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('public/assets/fronts/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/fronts/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/fronts/js/jquery.in-viewport-class.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/assets/fronts/js/pagescript.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    {{ HTML::script('public/assets/js/jquery.validate.js') }}
    <script type="text/javascript">
        const passwordInput = document.getElementById('password');
        const passwordCInput = document.getElementById('cpassword');
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordC = document.getElementById('togglePasswordC');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
        });
        togglePasswordC.addEventListener('click', function() {
            const type = passwordCInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordCInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#loginform").validate();
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#loginformF").validate();
            $.validator.addMethod("passworreq", function(input) {
                    var reg = /[0-9]/; //at least one number
                    var reg2 = /[a-z]/; //at least one small character
                    var reg3 = /[A-Z]/; //at least one capital character
                    var reg4 = /[\W_]/; //at least one special character
                    return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
                },
                "Password must be at least 8 characters long, contains an upper case letter, a lower case letter, a number and a symbol."
                );
        });
    </script>
</body>

</html>
