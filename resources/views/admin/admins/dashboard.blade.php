@extends('layouts.admin')
@section('content')
<div class="pay_loader" id="loaderID" style="display: none;"><img src="https://nimbleappgenie.live/dafri/public/img/front/dafri_loader.gif" alt=""></div>
<div class="content-wrapper">
    <section class="dashboard-main-tab-wrapper content-header">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item active">
            <a class="nav-link myTab" id="home-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="home" aria-selected="true">Dashboard</a>
          </li>

          <li class="nav-item">
            <a class="nav-link myTab" id="profile-tab" data-toggle="tab" href="#product" role="tab" aria-controls="profile" aria-selected="false">Product Activity</a>
          </li>

          <li class="nav-item">
            <a class="nav-link myTab" id="fee-tab" data-toggle="tab" href="#fee" role="tab" aria-controls="profile" aria-selected="false">Fee Collection Report</a>
          </li>

          <li class="nav-item">
            <a class="nav-link myTab" id="contact-tab" data-toggle="tab" href="#customer" role="tab" aria-controls="contact" aria-selected="false">Customer Report</a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link myTab" id="contact-tab" data-toggle="tab" href="#country" role="tab" aria-controls="contact" aria-selected="false">Country Report</a>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">     
            <div class="tab-pane fade active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <section class="content">
                        <div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3>{{$dadhboardData['users_count']}}</h3>
                                        <p>Personal Users</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <a href="{{URL::to( 'admin/users')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h3>{{$dadhboardData['business_count']}}</h3>
                                        <p>Business Users</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <a href="{{URL::to( 'admin/merchants')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue dba-box">
                                    <div class="inner">
                                        <h3>{{number_format($total_dba_credit_swap_count, 2, '.', '')}}</h3>
                                        <p> Total  DBA Converted</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <a href="{{URL::to( 'admin/reports/dba-transaction-report')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue dba-box">
                                    <div class="inner">
                                        <h3>{{number_format($total_dba_in_wallet, 2, '.', '')}}</h3>
                                        <p> Total DBA In Interest Wallet</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <a href="{{URL::to( 'admin/reports/dba-transaction-report')}}" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue dba-box">
                                    <div class="inner">
                                        <h3>{{number_format($total_dba_credit_swap_weekly_count, 2, '.', '')}}</h3>
                                        <p> Total DBA Converted (Weekly)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <a href="{{URL::to( 'admin/reports/dba-transaction-report')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue dba-box">
                                    <div class="inner">
                                        <h3>{{number_format($total_dba_credit_swap_monthly_count, 2, '.', '')}}</h3>
                                        <p> Total DBA Converted (Monthly)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <a href="{{URL::to( 'admin/reports/dba-transaction-report')}}" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="chart-section">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="main-wrapper-parent">
                                    <div class="graph-title">
                                        <h2>Monthly DBA Chart</h2>
                                    </div>
                                   <div class="chart-parent">
                                       <div id="chartContainer" style="height: 288px; max-width: 100%;"></div>
                                   </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="main-wrapper-parent">
                                    <div class="graph-title">
                                        <h2>Weekly DBA Chart</h2>
                                    </div>
                                    <div class="chart-parent1">
                                       <div id="mychartContainer" style="height: 288px; max-width: 100%;"></div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
    </section>

    <style>
    section.chart-section {padding: 0 15px 80px;}
    .main-wrapper-parent{position: relative;}
    .chart-section .col-lg-6:first-child .main-wrapper-parent{padding-right: 40px;}
    .chart-section .col-lg-6:first-child .main-wrapper-parent:after{content: ""; position: absolute; top: 0; bottom: -10px; right: 0; width: 2px; background: rgb(0 0 0 / 42%);} 
    .chart-parent div#chartContainer {width: 800px; height: auto; overflow: scroll; margin: 0 auto;}
    .chart-parent1 div#mychartContainer {width: 800px; height: auto; overflow: auto; margin: 0 auto;}
    .graph-title{margin-bottom: 30px; text-align: left;}
    .graph-title h2{font-size: 24px; margin: 0;}
    .chart-parent a.canvasjs-chart-credit,
    .chart-parent1 a.canvasjs-chart-credit {display: none;}
    

    @media screen and (max-width:1199px){
        section.chart-section .row .col-lg-6:first-child .main-wrapper-parent{margin-bottom: 50px;}
        .chart-section .col-lg-6:first-child .main-wrapper-parent:after {top: initial; bottom: -30px; width: 100%; height: 2px;}
        .chart-section .col-lg-6:first-child .main-wrapper-parent{padding-right: 0;}
    }
    @media screen and (max-width:767px){
        .chart-parent canvas,
        .chart-parent1 canvas{pointer-events: none;}
    }  

</style>
<style>
@import url("https://fonts.googleapis.com/css2?family=Fontdiner+Swanky&family=Roboto:wght@500&display=swap");
* {
box-sizing: 0;
margin: 0;
padding: 0;
}

.not_permitted_main {
display: flex;
align-items: center;
height: 100vh;
max-width: 1000px;
width: calc(100% - 4rem);
margin: 0 auto;
}
.not_permitted_main > * {
display: flex;
flex-flow: column;
align-items: center;
justify-content: center;
height: 100vh;
max-width: 500px;
width: 100%;
padding: 2.5rem;
}

.not_permitted_main aside {
background-image: url("/public/img/right-edges.png");
background-position: top right;
background-repeat: no-repeat;
background-size: 25px 100%;
}
.not_permitted_main aside img {
display: block;
height: auto;
width: 100%;
}

.not_permitted_main main {
text-align: center;
background: #383838;
}
.not_permitted_main main h1 {
font-family: "Fontdiner Swanky", cursive;
font-size: 4rem;
color: #c5dc50;
margin-bottom: 1rem;
}
.not_permitted_main main p {
margin-bottom: 2.5rem;
color:#FFF;
}
.not_permitted_main main p em {
font-style: italic;
color: #c5dc50;
}
.not_permitted_main main button {
font-family: "Fontdiner Swanky", cursive;
font-size: 1rem;
color: #383838;
border: none;
background-color: #f36a6f;
padding: 1rem 2.5rem;
transform: skew(-5deg);
transition: all 0.1s ease;
cursor: url("/public/img/cursors-eye.png"), auto;
}
.not_permitted_main main button:hover {
background-color: #c5dc50;
transform: scale(1.15);
}

@media (max-width: 700px) {

.not_permitted_main {
flex-flow: column;
}
.not_permitted_main > * {
max-width: 700px;
height: 100%;
}

.not_permitted_main aside {
background-image: none;
background-color: white;
}
.not_permitted_main aside img {
max-width: 300px;
}
}
</style>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>
<script>
$(document).ready(function() {
    $(".myTab").click(function(){
    var act_tab= $(this).attr('href').replace('#', '');
    $.ajax({
           beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/dashboard-tab",
            method: 'post',
            data: {"act_tab":act_tab,"_token": "{{ csrf_token() }}"},
            success: function(result){
             if(result==1)
             {
            var url="/admin/admins/dashboard";    
            $('#myTabContent').html('<div class="box box-info"><div class="not_permitted_main"><aside><img src="{{HTTP_PATH}}/public/img/Mirror.png" alt="You are not permitted to view this page." /></aside><main><h1>Sorry!</h1><p>You dont have permission to perform action on this page <em>. . . like your social life.</em></p><button onclick="redirectToAdmin();">You can go now!</button></main></div></div>')   
            $('#loaderID').css("display", "none");
             } 
             else{    
             $('#myTabContent').html(result);
             if(act_tab!='product' && act_tab!='customer' && act_tab!='country'  && act_tab!='fee')
             {
             $('#loaderID').css("display", "none");
             }
            }
            }
		});

   });
});

function redirectToAdmin(){

    location.href='{{HTTP_PATH}}/admin/admins/dashboard';
}


</script>

<script>
window.onload = function () {
var chartt = new CanvasJS.Chart("chartContainer", {
    width:1200,
    animationEnabled: true,
    theme: "light2", // "light1", "light2", "dark1", "dark2"
    backgroundColor: "transparent",
    title:{
        text: "",
    },
    axisY: {
      //  title: "DBA",
        gridThickness: 0,
        labelFontSize: 14,
        maximum:<?php echo $max_sum_month+30000;  ?>,
        tickLength: 0,
        lineThickness: 0,
        labelFormatter: function(){
        return " ";
        },
    },
    axisX: {
        labelFontSize: 14,
    },
    dataPointWidth: 70,
    dataPointMinWidth:70,
    dataPointMaxWidth:70,
    height:270,
    data: [{        
        type: "column",  
        color: "#000", 
        showInLegend: false, 
        legendMarkerColor: "#000",
        indexLabelFontSize: 14,
        legendText: "",
        indexLabel: "{y}",
        dataPoints: [      
            { y:<?php echo $month_sum_array["January"]; ?>,  label: "January" },
            { y:<?php echo $month_sum_array["February"]; ?>,  label: "February" },
            { y:<?php echo $month_sum_array["March"]; ?>,  label: "March" },
            { y:<?php echo $month_sum_array["April"]; ?>,  label: "April" },
            { y:<?php echo $month_sum_array["May"]; ?>,  label: "May" },
            { y:<?php echo $month_sum_array["June"]; ?>, label: "June" },
            { y:<?php echo $month_sum_array["July"]; ?>,  label: "July" },
            { y:<?php echo $month_sum_array["August"]; ?>, label: "August" },
            { y:<?php echo $month_sum_array["September"]; ?>,  label: "September" },
            { y:<?php echo $month_sum_array["October"]; ?>,  label: "October" },
            { y:<?php echo $month_sum_array["November"]; ?>,  label: "November" },
            { y:<?php echo $month_sum_array["December"]; ?>,  label: "December" },
        ]
    }]
});

var chart = new CanvasJS.Chart("mychartContainer", {
    width:800,
    animationEnabled: true,
    theme: "light2", // "light1", "light2", "dark1", "dark2"
    backgroundColor: "transparent",
    title:{
        text: "",
    },
    
    axisY: {
       // title: "DBA",
        gridThickness: 0,
        labelFontSize: 14,
        tickLength: 0,
        maximum: <?php echo $max_sum_week+4000;  ?>,
        lineThickness: 0,
        labelFormatter: function(){
        return " ";
        },
    },
    axisX: {
        labelFontSize: 14,
    },
    dataPointWidth: 70,
    dataPointMinWidth:70,
    dataPointMaxWidth:70,
    height:270,
    data: [{        
        type: "column",  
        color: "#000", 
        showInLegend: false, 
        indexLabelFontSize: 12,
        legendMarkerColor: "#000",
        legendText: "",
        indexLabel: "{y}",
        dataPoints: [      
            { y:<?php echo $day_sum_array["Mon"] ?? 0; ?>,  label: "Monday" },
            { y:<?php echo $day_sum_array["Tue"] ?? 0; ?>,  label: "Tuesday" },
            { y:<?php echo $day_sum_array["Wed"] ?? 0; ?>,  label: "Wednesday" },
            { y:<?php echo $day_sum_array["Thu"] ?? 0; ?>,  label: "Thursday" },
            { y:<?php echo $day_sum_array["Fri"] ?? 0; ?>, label: "Friday" },
            { y:<?php echo $day_sum_array["Sat"] ?? 0; ?>,  label: "Saturday" },
            { y:<?php echo $day_sum_array["Sun"] ?? 0; ?>, label: "Sunday" },

        ]
    }]
});
chartt.render();
chart.render();
}
</script>

@endsection