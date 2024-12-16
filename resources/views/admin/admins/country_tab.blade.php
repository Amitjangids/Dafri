<div class="conutry_section">
    <ul class="nav nav-tabs third_tab" id="myTab" role="tablist">
          <li class="nav-item active">
            <a class="nav-link myTab" id="home-tab" data-toggle="tab" href="#users-report" role="tab" aria-controls="home" aria-selected="true" aria-expanded="false">Users Report</a>
          </li>
          <li class="nav-item">
            <a class="nav-link myTab" id="profile-tab" data-toggle="tab" href="#no-of-transactions" role="tab" aria-controls="profile" aria-selected="false" aria-expanded="false">No of Transactions </a>
          </li>
          <li class="nav-item">
            <a class="nav-link myTab" id="contact-tab" data-toggle="tab" href="#amount of-transactions" role="tab" aria-controls="contact" aria-selected="false">Total Amount of Transactions </a>
          </li>
    </ul>
</div>

<div class="row country-report-wrapper">
    <div class="col-lg-12">
        <div class="inner-chart-flip-parent">
              <div class="inner-chart-wrapper front">
                <div class="inner-chart-box-parent table_auto">
                    <div class="custom-select-field">
                        <select class="change-filter">
                        <option option="Last Year/Current Year">Last Year / Current Year</option>
                        <option option="Last Month/Current Month">Last Month / Current Month</option>
                        <option option="Last Week/Current Week">Last Week / Current Week</option>
                        <option option="Yesterday/Current Day">Day Before Yesterday / Yesterday</option>
                        </select>
                        <span class="select-arrow"><i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                    </div>

                    <div class="custom-select-field currency_div" style="display:none">
                        <select class="change-currency">
                            <?php global $currencyList; 
                            foreach($currencyList as $currency) { ?>
                            ?>
                            <option option="{{$currency}}" <?php if($currency=='ZAR'){ echo "selected"; } ?> >{{$currency}}</option>
                            <?php } ?>
                        </select>
                        <span class="select-arrow"><i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                    </div>
                    <div class="customer_tabs table1"></div>
                    </div>
                </div>
        </div>
    </div>
    <!--  <div class="col-lg-12">
        <div class="inner-chart-flip-parent">
            <div class="inner-chart-wrapper back">
                <div class="inner-chart-box-parent">
                    <div class="inner-chart-content">
                        <h2>Country Report</h2>
                        <a href="#" class="flip-btn">Tile</a>
                    </div>
                    <div id="innerchartContainerchart4" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
            <div class="inner-chart-wrapper front space-chart">
                <div class="inner-chart-box-parent  table_size_1">
                    <div class="inner-chart-content">
                        <h2>Country report</h2>
                    </div>
                    <div class="custom-select-field">
                        <select>
                            <option>Currency</option>
                            <option>Currency</option>
                            <option>Currency</option>
                            <option>Currency</option>
                            <option>Currency</option>
                        </select>
                        <span class="select-arrow"><i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                    </div>
                    <div class="customer_tabs">
                        <ul class="nav nav-tabs items " id="myTab2" role="tablist">
                            <li class="item item1 active">
                                <a class="nav-link" id="country-report2-tab1" data-toggle="tab" href="#country-report2" role="tab" aria-controls="home" aria-selected="true">All</a>
                            </li>
                            <li class="item item2">
                                <a class="nav-link" id="top-user-tab1" data-toggle="tab" href="#top-user1" role="tab" aria-controls="home" aria-selected="true">Top 80%</a>
                            </li>
                            <li class="item item2">
                                <a class="nav-link" id="least-user-tab1" data-toggle="tab" href="#least-user1" role="tab" aria-controls="home" aria-selected="true">Least 20%</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent1">
                            <div class="tab-pane active" id="country-report2" role="tabpanel" aria-labelledby="country-report1-tab2">
                                <div class="flip-table">
                                    <div class="flip-table-parent  new_table ">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Country Name</th>
                                                    <th colspan="2">Amount of Transactions
                                                        <div class="cols_class">
                                                            <div class="col">Last Year</div>
                                                            <div class="col">Current year</div>
                                                           
                                                        </div>
                                                    </th>
                                                    <th>% Change</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><img src="https://www.nimbleappgenie.live/dafri/public/img/america_us_usa_icon.png" alt="us image">US</td>
                                                    <td>1234</td>
                                                    <td>1234</td>
                                                    <td>-5%</td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td><img src="https://www.nimbleappgenie.live/dafri/public/img/africa_south_icon.png" alt="AFRICA image"> AFRICA</td>
                                                    <td>3455</td>
                                                    <td>3455</td>
                                                    <td>-5%</td>
                                                </tr>
                                                 <tr>
                                                    <td><img src="https://www.nimbleappgenie.live/dafri/public/img/nigeria_icon-2.png" alt="NIGERIA image">NIGERIA</td>
                                                    <td>1234</td>
                                                    <td>1234</td>
                                                    <td>-5%</td>
                                                </tr>
                                                 <tr>
                                                    <td><img src="https://www.nimbleappgenie.live/dafri/public/img/india_icon.png" alt="INDIA image">INDIA</td>
                                                    <td>5678</td>
                                                    <td>5678</td>
                                                    <td>-5%</td>
                                                </tr>
                                                 <tr>
                                                    <td><img src="https://www.nimbleappgenie.live/dafri/public/img/inghilterra_icon.png" alt="UK image">UK</td>
                                                    <td>5678</td>
                                                    <td>5678</td>
                                                    <td>-5%</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                             <div class="tab-pane " id="top-user1" role="tabpanel" aria-labelledby="top-user-tab1">
                                Top 80%
                             </div>
                             <div class="tab-pane " id="least-user1" role="tabpanel" aria-labelledby="least-user-tab1">
                                Least 20%
                             </div>
                        </div>
                </div>
                </div>
            </div>
        </div>
    </div> -->
</div>
          
<script>
var filter='Last Year / Current Year';
$.ajax({
        url: "{{HTTP_PATH}}/admin/admins/fetch-country-report",
        method: 'post',
        data: {"filter":filter,"_token": "{{ csrf_token() }}"},
        success: function(result){
        $('.table1').html(result);
        $('#loaderID').css("display", "none");
        }
	});

    $(".change-filter").change(function(){
        var filter=$(this).val();
        var trans_type=$('.third_tab').find('li.active a').attr('href').replace('#', '');
        if(trans_type=="users-report")
        {
            $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-report",
            method: 'post',
            data: {"filter":filter,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		});
        }
        else if(trans_type=="no-of-transactions")
        {
            $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-transaction-report",
            method: 'post',
            data: {"filter":filter,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		    });
        }
        else if(trans_type=="amount of-transactions")
        {
            var currency=$('.change-currency').find(":selected").text();
            $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-amount-report",
            method: 'post',
            data: {"filter":filter,"currency":currency,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		    });
        }
    });

    $(".third_tab li a").click(function(){
        var trans_type=$(this).attr('href').replace('#', '');
        var filter=$('.change-filter').find(":selected").text();
        if(trans_type=="users-report")
        {
            $('.currency_div').hide();
            $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-report",
            method: 'post',
            data: {"filter":filter,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		});
        }
        else if(trans_type=="no-of-transactions")
        {
            $('.currency_div').hide();
            $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-transaction-report",
            method: 'post',
            data: {"filter":filter,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		    });
        }
        else if(trans_type=="amount of-transactions")
        {
            $('.currency_div').show();
            var currency=$('.change-currency').find(":selected").text();
            $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-amount-report",
            method: 'post',
            data: {"filter":filter,"currency":currency,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		    });
        }
    });

    $(".change-currency").change(function(){
        var currency=$(this).val();
        var filter=$('.change-filter').find(":selected").text();
        $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-country-amount-report",
            method: 'post',
            data: {"filter":filter,"currency":currency,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('.table1').html(result);
             $('#loaderID').css("display", "none");
            }
		});
    });



</script>               