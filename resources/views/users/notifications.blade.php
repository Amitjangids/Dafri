@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-12">
                    <div class="heading-section wth-head noti-head">
                        <h5>Notifications</h5>
                    </div>
                </div>
                <div class="notification-main-box col-sm-12">
                    <!-- <h5>Today</h5> -->
                    @foreach ($notifications as $notification)
                    <div class="notification-box">
                        <div class="tran-name-icon">D</div>

                        <h6>{{$notification->notif_subj}}</h6>
                        <div class="date-time-noti ml-auto">
                            <span>
                                @php
                                $date = date_create($notification->created_at);
                                $notifDate = date_format($date,'d.m.Y');
                                $notifTime = date_format($date,'H:ia');
                                @endphp
                                {{$notifDate}} | {{$notifTime}}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@endsection