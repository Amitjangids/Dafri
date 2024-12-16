<div class="col-lg-6">
    <div class="inner-chart-flip-parent">
        <div class="inner-chart-wrapper front">
            <div class="inner-chart-box-parent">
                <div class="inner-chart-content">
                    <div class="transactions-box-parent">
                        <h2>No. of Transactions</h2>
                        <div class="total-value-box">
                            <div class="year-transition"> 
                                <div class="dynamic-value">
                                    <span class="black"></span> 
                                    <h4>
                                        <?php echo $last_lable; ?>: <?php  
                                         $prev=json_decode($previous_graph);
                                         $total_last_trans=0;
                                        foreach($prev as $last) { $last->y!='null' ? $total_last_trans+=$last->y :  $total_last_trans+=0; } echo $total_last_trans; ?>
                                    </h4>
                                </div>
                            </div>
                            <div class="year-transition">
                                <div class="dynamic-value">
                                    <span class="green"></span>
                                    <h4><?php echo $current_lable; ?> :  <?php  
                                    $current=json_decode($current_graph);
                                    $total_current_trans=0;
                                    foreach($current as $curr) { $curr->y!='null' ? $total_current_trans+=$curr->y :  $total_current_trans+=0; } echo $total_current_trans; ?></h4>

                                    <?php 
                                    if($total_current_trans!=0 && $total_last_trans!=0)
                                    {
                                    $avg=($total_current_trans-$total_last_trans)/$total_last_trans*100;
                                    if($avg > 0) { ?>
                                    <div class="uparrow">
                                    <i class="fa-solid fa-arrow-up">
                                    <span>{{number_format($avg, 2, '.', ',')}}%</span>
                                    </i>
                                    </div>
                                   <?php }else{  ?>
                                    <div class="downarrow"><i class="fa-solid fa-arrow-down"></i><span>{{number_format($avg, 2, '.', ',')}}%</span></div> 
                                   <?php } } ?>

                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($total_last_trans!=0 || $total_current_trans!=0) { ?>
                    <a href="javascript:void(0)" class="flip-btn">Tile</a>
                    <?php } ?>
                </div>
                <?php if($total_last_trans!=0 || $total_current_trans!=0) { ?>
                <div id="innerchartContainer" style="height: 300px; width: 100%;"></div>
                <?php }else{ ?><div class="no-record text-center">No Record Found ! </div> <?php } ?>

            </div>
        </div>
        <div class="inner-chart-wrapper back">
            <div class="inner-chart-box-parent">
                <div class="inner-chart-content">
                    <h2>No. of Transactions</h2>
                    <a href="javascript:void(0)" class="flip-btn">Tile</a>
                </div>
                <div class="flip-table">
                    <div class="flip-table-parent">
                        <table>
                               <?php if($filter=="Last Year / Current Year") { ?>
                                <tr>
                                <th>&nbsp;</th>
                                <?php 
                                for($i=1;$i<=12;$i++)
                                { 
                                $monthNum  = $i;
                                $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
                                $monthName = $dateObj->format('M'); 
                                ?>
                                <th>{{$monthName}}</th>
                                <?php } ?>
                            </tr>
                            <?php }else{ ?>
                            <th></th>
                            <th>No. of Transactions</th>
                            <?php } ?>
                            <tr>
                                <td>{{trim($last_lable)}}</td>
                                <?php 
                                $prev=json_decode($previous_graph);
                                foreach($prev as $last) {  ?>
                                <td>{{$last->y!='null' ? $last->y : '-'}}</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td>{{trim($current_lable)}}</td>
                                <?php 
                                $current=json_decode($current_graph);
                                foreach($current as $current) {  ?>
                                <td>{{$current->y!='null' ? $current->y : '-'}}</td>
                                <?php } ?>
                            </tr>
                        </table>
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-6">
        <div class="inner-chart-flip-parent not-scroll">
            <div class="inner-chart-wrapper back space-chart">
                <div class="inner-chart-box-parent">
                    <div class="inner-chart-content">
                        <h2>Total Amount of Transactions</h2>
                    </div>
                    <div class="flip-table">
                        <div class="flip-table-parent fixTableHead">
                            <table>
                                <tr>
                                    <th>Currency</th>
                                    <th>{{trim($last_lable)}}</th>
                                    <th>{{trim($current_lable)}}</th>
                                    <th>% Change</th>
                                </tr>
                                <?php if(isset($currency_sum_array) && !empty($currency_sum_array)){ foreach($currency_sum_array as $currency) { ?>
                                <tr>
                                    <td>{{$currency['currency']}}</td>
                                    <td><?php 
                                       if(isset($currency[trim($last_lable)])) { echo number_format($currency[trim($last_lable)], 2, '.', ','); }else{ echo "-";}
                                    ?></td>
                                    <td><?php 
                                       if(isset($currency[trim($current_lable)])) { echo number_format($currency[trim($current_lable)], 2, '.', ','); }else{ echo "-";}
                                    ?></td>
                                    <td>
                                    <?php 
                                    
                                    if(isset($currency[trim($last_lable)]) && isset($currency[trim($current_lable)])) { 
                                         $current_year=$currency[trim($current_lable)];
                                         $last_year=$currency[trim($last_lable)];
                                         $actual=$current_year-$last_year;
                                         $percentage=$actual/$last_year*100;
                                         if($percentage < 0)
                                         {
                                            echo '<span class="red_growth">'.number_format($percentage, 2, '.', ',').'%</span>';
                                         }
                                         else{
                                            echo '<span class="postive_grouth">'.number_format($percentage, 2, '.', ',').'%</span>'; 
                                         }
                                  
                                    }
                                    
                                    ?></td>
                                </tr>
                                <?php } }else{  ?>
                                    <tr>
                                    <td colspan="4">No Record Found !</td>   
                                    </tr>  
                               <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php if($total_last_trans!=0 || $total_current_trans!=0) { ?>

<?php if($filter=="Last Year / Current Year") { ?>
<script type="text/javascript">
var g_width=1100;
 </script>
 <?php }else{ ?>
<script type="text/javascript">
var g_width=550;
</script>
 <?php }   ?>

<script type="text/javascript">
 var chart2 = new CanvasJS.Chart("innerchartContainer", {
      width:g_width,
      theme: "light2",
      animationEnabled: true,
      backgroundColor: "transparent",
      title:{
        text: ""              
      },
      height:270,
      dataPointWidth: 70,
      dataPointMinWidth: 70,
      dataPointMaxWidth: 70,
      data: [ 
       {  
       type: "column",
       color: "#28232A",
       indexLabelFontSize: 14,
       indexLabel: "{y}",
       showInLegend: false,
       dataPoints:<?php echo $previous_graph; ?>
       },

       {
      type: "column",
      indexLabelFontSize: 14,
      color: "#3AC685",
      indexLabel: "{y}",
      showInLegend: false,               
      dataPoints:<?php echo $current_graph; ?>
    }
    ],
    axisX: {
    labelAutoFit: true, //false by default.
    interval:1,
    labelFontSize: 14
    },
    axisY:{
        lineThickness: 0,
        gridThickness: 0,
        tickLength: 0, 
        maximum:<?php echo $maximum_value+100;  ?>,
        labelFormatter: function(){
        return " ";
        },
    },    
  });
chart2.render();

</script>

 <script>
    $(document).ready(function() {
        $(".flip-btn").click(function(){
         $(this).parent().parent().parent().parent().toggleClass('active');
       });
    });
</script>

<?php } ?>