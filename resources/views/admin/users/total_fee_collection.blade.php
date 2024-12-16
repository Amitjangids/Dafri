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
        <h1>Total Fee Collection Report</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> Total Fee Collection Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            <div class="admin_search">
                {{ Form::open(array('method' => 'post', 'id' => 'adminSearch')) }}
                <div class="form-group align_box dtpickr_inputs"> 

                    <span class="hint">{{Form::text('date', null, ['class'=>'form-control', 'placeholder'=>'Search by Date', 'id'=>'date', 'autocomplete' => 'off', 'id'=>'date','readonly'])}}</span>

                    <div class="admin_asearch">
                        <div class="ad_s ajshort">{{Form::submit('Submit', ['class' => 'btn btn-info admin_ajax_searchhhhh'])}}</div>
                        <div class="ad_cancel"><a href="{{URL::to('admin/users/total-fee-collection')}}" class="btn btn-default canlcel_le">Clear Search</a></div>
                    </div>
                </div>
                {{ Form::close()}}
            </div>            
            <div class="m_content" id="listID">
            <div class="panel-body marginzero">
            <section id="no-more-tables" class="lstng-section">
            <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th class="sorting_paging"><b>Transations Currency</b></th>
                        <th class="sorting_paging"><b>Total Fee</b></th>
                    </tr>
                </thead>
                <tbody>
                   <?php foreach($allrecords as $key=>$value) {  ?>
                   <tr>
                   <td data-title="Name"><b><?php echo $key; ?></b></td>
                   <td data-title="Name"><?php echo $key; ?> <?php echo $value; ?></td>
                   </tr>
                  <?php }  ?>
                </tbody>
            </table>
        </div>
    </section>
</div>         
</div> 
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
@endsection