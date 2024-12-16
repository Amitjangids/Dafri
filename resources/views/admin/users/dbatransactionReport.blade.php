@extends('layouts.admin')
@section('content')
<?php

function matchSel($first, $second) {
    if ($first == $second)
        return "selected";
}
?>
<?php 

$dated_value=$toDate.'/'.$frmDate; 
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>DBA Transaction Report</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active">DBA Transaction Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            <div class="admin_search">
                {{ Form::open(array('method' => 'post', 'id' => 'adminSearch')) }}
                <div class="form-group align_box dtpickr_inputs">
                    <span class="hints">Search by Sender Name / Receiver Name / Email Address / Account Number / Trans. ID / Ref ID</span>     
                    <span class="hint">{{Form::text('keyword', $keyword, ['class'=>'form-control', 'placeholder'=>'Search by Name', 'autocomplete' => 'off', 'id'=>'keyword'])}}</span>
                    <span class="hint" style="width:20%">{{Form::text('transaction_id', $transaction_id, ['class'=>'form-control', 'placeholder'=>'Search by Trans. ID / Ref ID', 'autocomplete' => 'off', 'id'=>'transaction_id'])}}</span>   
                    <span class="hint">{{Form::text('date', null, ['class'=>'form-control', 'placeholder'=>'Search by Date', 'id'=>'date', 'autocomplete' => 'off', 'id'=>'date','readonly'])}}</span>
                    <!-- <span class="hint">
                        <select name="srch_currency" class="form-control" id="currency">
                            <option value="-1">Select Currency</option>
                            @php global $currencyList; @endphp
                                        @foreach($currencyList as $currencyVal)
                                            <option <?php echo matchSel($currencyVal,$currency);?> value="{{$currencyVal}}">{{$currencyVal}}</option>
                                        @endforeach
                        </select>
                    </span> -->
                    <div class="admin_asearch">
                        <div class="ad_s ajshort">{{Form::submit('Submit', ['class' => 'btn btn-info admin_ajax_searchhhhh'])}}</div>
                        <div class="ad_cancel"><a href="{{URL::to('admin/reports/dba-transaction-report')}}" class="btn btn-default canlcel_le">Clear Search</a></div>
                    </div>
                </div>
                {{ Form::close()}}
              <!--  <div class="add_new_record"><a href="{{URL::to('admin/users/add')}}" class="btn btn-default"><i class="fa fa-plus"></i> Add Personal User</a></div> -->
            </div>            
            <div class="m_content" id="listID">
                @include('elements.admin.users.dbatransReport')
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
    jQuery(function ($) {
        $('#date').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
<?php
$transCalDays = 30;
$chkTransDate = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $transCalDays . ' days'));
$to = $chkTransDate;
$from = date('Y-m-d');
?>
        $('#date').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + '/' + picker.endDate.format('YYYY-MM-DD'));
        });
        $('#date').on('daterangepicker.change', function (ev, picker) {
            alert();
        });

        $('#date').data('daterangepicker').setStartDate('<?php echo $toDate; ?>');
        $('#date').data('daterangepicker').setEndDate('<?php echo $frmDate; ?>');
        $('#date').val('<?php echo $dated_value; ?>');

    });
</script>
<script>
    function exportCSV()
    {
        var keyword = $('#keyword').val();
        var date = $('#date').val();
        if (keyword == "") {
            keyword = 'na';
        }
        location.href = "<?php echo HTTP_PATH; ?>/admin/reports/dbaexportCSV/" + keyword + "/" + date + "/-1";
    }
</script>
@endsection