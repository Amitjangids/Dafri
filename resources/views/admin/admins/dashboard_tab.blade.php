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

            
<script>
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
            { y:<?php echo $day_sum_array["Mon"]; ?>,  label: "Monday" },
            { y:<?php echo $day_sum_array["Tue"]; ?>,  label: "Tuesday" },
            { y:<?php echo $day_sum_array["Wed"]; ?>,  label: "Wednesday" },
            { y:<?php echo $day_sum_array["Thu"]; ?>,  label: "Thursday" },
            { y:<?php echo $day_sum_array["Fri"]; ?>, label: "Friday" },
            { y:<?php echo $day_sum_array["Sat"]; ?>,  label: "Saturday" },
            { y:<?php echo $day_sum_array["Sun"]; ?>, label: "Sunday" },

        ]
    }]
});
chartt.render();
chart.render();
</script>