<div class="tab-pane active" id="all1" role="tabpanel" aria-labelledby="all1-tab1">
    <div class="flip-table">
        <div class="flip-table-parent customer_report_table">
            <table>
                <tr>
                    <th>Total Customers</th>
                    <th>Active Customers</th>
                    <th colspan="3">Number of Customer Transacted in Last
                        <div class="cols_class">
                            <div class="col">90 Days</div>
                            <div class="col">60 Days</div>
                            <div class="col">30 Days</div>
                        </div>
                    </th>
                    <th>Neither Acitve Nor Transacted In Last 90 Days</th>
                </tr>
               
                <tr>
                    <td>{{$data['total_users']}}</td>
                    <td>{{$data['total_active_users']}}</td>
                    <td colspan="3">
                        <table>
                            <tr>
                                <td>{{$data['90 Days']}}</td>
                                <td>{{$data['60 Days']}}</td>
                                <td>{{$data['30 Days']}}</td>
                            </tr>
                        </table>
                    </td>
                    <td>{{$data['total_user_not_transacted_in_last_90_days']}}</td>
                </tr>
               
            </table>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="personal-user" role="tabpanel" aria-labelledby="personal-user-tab">
    Personal user
</div>
 <div class="tab-pane fade" id="business-user" role="tabpanel" aria-labelledby="business-user-tab">
   Business user
</div>
                                          