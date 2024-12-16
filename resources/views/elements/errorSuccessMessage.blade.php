{{-- @if (count($errors) > 0 || Session::has('error_message') || isset($error_message))
    <div class="alert alert-block alert-danger">
        <button data-dismiss="alert" class="close close-sm" type="button">
            <i class="fa fa-times"></i>
        </button>
        @if (isset($error_message)) {{$error_message}} @endif
        
        @foreach ($errors->all() as $error)
             {{$error}}<br/>
        @endforeach 
        
        @if (Session::has('error_message')) {{Session::get('error_message')}} @endif
    </div>
@endif

@if (Session::has('success_message')) 
    <div class="alert alert-success">
        <button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>
        {{Session::get('success_message')}} 
        {{Session::forget('success_message')}}
    </div>
@endif --}}


@if (session('error_session_message'))
    <div class="alert alert-danger alert-dismissible fade show auto-hide-alert" role="alert">
        <strong>Error !</strong> {{ session('error_session_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php
        session()->forget('error_session_message');
    @endphp
@endif

@if (session('success_session_message'))
    <div class="alert alert-success alert-dismissible fade show auto-hide-alert" role="alert">
        <strong>Success !</strong> {{ session('success_session_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php
        session()->forget('success_session_message');
    @endphp
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let alertBox = document.querySelector('.auto-hide-alert');
        if (alertBox) {
            setTimeout(function() {
                let alert = new bootstrap.Alert(alertBox);
                alert.close();
            }, 5000);
        }
    });
</script>
