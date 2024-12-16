<ul class="nav nav-tabs items " id="myTab2" role="tablist">
<li class="item item1 active">
    <a class="nav-link" id="country-report1-tab1" data-toggle="tab" href="#country-report1" role="tab" aria-controls="home" aria-selected="true">All</a>
</li>
<li class="item item2">
    <a class="nav-link" id="top-user-tab" data-toggle="tab" href="#top-user" role="tab" aria-controls="home" aria-selected="true">Top 80%</a>
</li>
<li class="item item2">
    <a class="nav-link" id="least-user-tab" data-toggle="tab" href="#least-user" role="tab" aria-controls="home" aria-selected="true">Least 20%</a>
</li>
</ul>
<div class="tab-content" id="myTabContent1">
    <div class="tab-pane active" id="country-report1" role="tabpanel" aria-labelledby="country-report1-tab1">
        <div class="flip-table">
            <div class="flip-table-parent fixTableHead new_table">
                <table>
                    <thead>
                    <tr>
                        <th>Country Name</th>
                        <th>Total Users</th>
                        <th colspan="2">Newly Registered Users
                            <div class="cols_class">
                                <div class="col">{{$last_lable}}</div>
                                <div class="col">{{$current_lable}}</div>
                            </div>
                        </th>
                        <th>% Change</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($month_sum_array as $data) { ?>
                        <tr>
                        <td><img src="<?php echo HTTP_PATH; ?>/public/country_icon/{{strtolower($data['short_code'])}}.imageset/{{strtolower($data['short_code'])}}.png" alt="us image" height="14px" width="14px">{{$data['country_name']}}</td>
                         <td>{{$data['total_user']}}</td>
                         <td colspan="2" class="country-report-custom">
                             <table>
                                 <tr>
                                    <td>{{$data[$last_lable]}}</td>
                                    <td>{{$data[$current_lable]}}</td>
                                 </tr>
                             </table>
                         </td>
                         <td>
                        <?php 
                         if($data[$last_lable]!=0 && $data[$current_lable]!=0) 
                         { 
                          $current_year=$data[$current_lable];
                          $last_year=$data[$last_lable];
                          $actual=$current_year-$last_year;
                          $percentage=$actual/$last_year*100;
                          if($percentage < 0)
                          {
                             echo '<span class="red_growth">'.number_format($percentage, 2, '.', ',').'%</span>';
                          }
                          else
                          {
                            echo '<span class="postive_grouth">'.number_format($percentage, 2, '.', ',').'%</span>'; 
                          }
                          }else{ echo "-"; }
                         ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div class="tab-pane " id="top-user" role="tabpanel" aria-labelledby="top-user-tab">
         <div class="flip-table">
            <div class="flip-table-parent fixTableHead new_table">
                <table>
                    <thead>
                    <tr>
                        <th>Country Name</th>
                        <th>Total Users
                            
                        </th>
                        <th colspan="2">Newly Registered Users
                        <div class="cols_class">
                            <div class="col">{{$last_lable}}</div>
                                <div class="col">{{$current_lable}}</div>
                           
                        </div>
                        </th>
                        <th>% Change</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($top_80_country) && count($top_80_country) > 0){ foreach($top_80_country as $data) { ?>
                        <tr>
                        <td><img src="<?php echo HTTP_PATH; ?>/public/country_icon/{{strtolower($data['short_code'])}}.imageset/{{strtolower($data['short_code'])}}.png" alt="us image" height="14px" width="14px">{{$data['country_name']}}</td>
                         <td>{{$data['total_user']}}</td>
                         <td>{{$data[$last_lable]}}</td>
                         <td>{{$data[$current_lable]}}</td>
                         <td>
                        <?php 
                         if($data[$last_lable]!=0 && $data[$current_lable]!=0) 
                         { 
                          $current_year=$data[$current_lable];
                          $last_year=$data[$last_lable];
                          $actual=$current_year-$last_year;
                          $percentage=$actual/$last_year*100;
                          if($percentage < 0)
                          {
                             echo '<span class="red_growth">'.number_format($percentage, 2, '.', ',').'%</span>';
                          }
                          else
                          {
                            echo '<span class="postive_grouth">'.number_format($percentage, 2, '.', ',').'%</span>'; 
                          }
                          }else{ echo "-"; }
                         ?></td>
                        </tr>
                        <?php } }else{  ?>
                        <tr>
                            <td colspan="5" align="center">No Record Found !</td>
                        </tr>
                       <?php  } ?>
                    </tbody>
                </table>
            </div>
         </div>
    </div>
    <div class="tab-pane " id="least-user" role="tabpanel" aria-labelledby="least-user-tab">
        <div class="flip-table">
            <div class="flip-table-parent fixTableHead new_table">
                <table>
                    <thead>
                    <tr>
                        <th>Country Name</th>
                        <th>Total Users
                            
                        </th>
                        <th colspan="2">Newly Registered Users
                        <div class="cols_class">
                            <div class="col">{{$last_lable}}</div>
                                <div class="col">{{$current_lable}}</div>
                           
                        </div>
                        </th>
                        <th>% Change</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($least_20_country) && count($least_20_country) > 0){ foreach($least_20_country as $data) { ?>
                        <tr>
                        <td><img src="<?php echo HTTP_PATH; ?>/public/country_icon/{{strtolower($data['short_code'])}}.imageset/{{strtolower($data['short_code'])}}.png" alt="us image" height="14px" width="14px">{{$data['country_name']}}</td>
                         <td>{{$data['total_user']}}</td>
                         <td>{{$data[$last_lable]}}</td>
                         <td>{{$data[$current_lable]}}</td>
                         <td>
                        <?php 
                         if($data[$last_lable]!=0 && $data[$current_lable]!=0) 
                         { 
                          $current_year=$data[$current_lable];
                          $last_year=$data[$last_lable];
                          $actual=$current_year-$last_year;
                          $percentage=$actual/$last_year*100;
                          if($percentage < 0)
                          {
                             echo '<span class="red_growth">'.number_format($percentage, 2, '.', ',').'%</span>';
                          }
                          else
                          {
                            echo '<span class="postive_grouth">'.number_format($percentage, 2, '.', ',').'%</span>'; 
                          }
                          }else{ echo "-"; }
                         ?></td>
                        </tr>
                        <?php } }else{  ?>
                        <tr>
                            <td colspan="5" align="center">No Record Found !</td>
                        </tr>
                       <?php  } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

                                      