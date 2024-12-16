
                <div class="customer_report_box">
                    <div class="custom-select-field">
                        <select class="change-filter">
                            <option value="all">All</option>
                            <option value="personal">Personal User</option>
                            <option value="business">Business User</option>
                        </select>
                        <span class="select-arrow"><i class="fa fa-sort-desc" aria-hidden="true"></i></span>
                    </div>
                    <div class="customer_chart-parent">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="chart-parent">
                                    <div class="inner-chart-wrapper">
                                        <div class="inner-chart-box-parent1">
                                            <div class="inner-chart-content">
                                                <h2>Customer’s Report</h2>
                                            </div>
                                            <div class="customer_tabs">
                                                <div class="tab-content" id="myTabContent1">
                                              
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<script>
var filter='all';
$.ajax({
        url: "{{HTTP_PATH}}/admin/admins/fetch-customer-report",
        method: 'post',
        data: {"filter":filter,"_token": "{{ csrf_token() }}"},
        success: function(result){
        $('#myTabContent1').html(result);
        $('#loaderID').css("display", "none");
        }
	});

    $(".change-filter").change(function(){
        var filter=$(this).val();
        $.ajax({
            beforeSend: function () {
            $('#loaderID').css("display", "flex");
            },
            url: "{{HTTP_PATH}}/admin/admins/fetch-customer-report",
            method: 'post',
            data: {"filter":filter,"_token": "{{ csrf_token() }}"},
            success: function(result){
             $('#myTabContent1').html(result);
             $('#loaderID').css("display", "none");
            }
		});
    });
</script>
       