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
    <?php if($slug=="giftcard") { ?>
    <h1>GiftCard Request</h1>
    <?php } else{ ?>
    <h1>Top Up Request</h1>
    <?php } ?>
    
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <?php if($slug=="giftcard") { ?>
            <li class="active"> GiftCard Request</li>
            <?php } else{ ?>
            <li class="active"> Top Up Request</li>
            <?php } ?>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            <div class="admin_search">
                {{ Form::open(array('method' => 'post', 'id' => 'adminSearch')) }}
                <div class="form-group align_box dtpickr_inputs"> 
                    <span class="hints">Search by Sender Name / Receiver Name / Email Address / Account Number / Trans. ID / Ref ID </span>
                    <span class="hint">{{Form::text('keyword', $keyword, ['class'=>'form-control', 'placeholder'=>'Search by Name', 'autocomplete' => 'off', 'id'=>'keyword'])}}</span>
                    <span class="hint" style="width:20%">{{Form::text('transaction_id', $transaction_id, ['class'=>'form-control', 'placeholder'=>'Search by Trans. ID / Ref ID', 'autocomplete' => 'off', 'id'=>'transaction_id'])}}</span>
                    <span class="hint">{{Form::text('date', null, ['class'=>'form-control', 'placeholder'=>'Search by Date', 'id'=>'date', 'autocomplete' => 'off', 'id'=>'date','readonly'])}}</span>


                    <span class="hint">
                        <select name="trans_for" class="form-control" id="trans_for">
                            <option value="-1">Select Trans. Type</option>
                            <?php if($slug=="giftcard") { ?>
                            <option  <?php echo matchSel('GIFT CARD',$trans_for);?> value="GIFT CARD" >GIFT CARD</option>
                            <option <?php echo matchSel('GIFT CARD(Refund)', $trans_for);?> value="GIFT CARD(Refund)">GIFT CARD(Reverse)</option>
                            <?php }else{ ?>
                            <option  <?php echo matchSel('Mobile Top-up',$trans_for);?> value="Mobile Top-up" >Mobile Top-up</option>
                            <option <?php echo matchSel('Mobile-TopUp(Refund)', $trans_for);?> value="Mobile-TopUp(Refund)">Mobile-TopUp(Reverse)</option>
                           <?php } ?>
                        </select>
                    </span>
                    <span class="hint">
                        <select name="srch_currency" class="form-control" id="currency">
                            <option value="-1">Select Currency</option>
                            @php global $currencyList; @endphp
                                        @foreach($currencyList as $currencyVal)
                                            <option <?php echo matchSel($currencyVal,$currency);?> value="{{$currencyVal}}">{{$currencyVal}}</option>
                                        @endforeach
                        </select>
                    </span>
                    <div class="admin_asearch">
                        <div class="ad_s ajshort">{{Form::submit('Submit', ['class' => 'btn btn-info admin_ajax_searchhhhh'])}}</div>
                        <div class="ad_cancel">
                            <?php if($slug=="giftcard") { ?>
                            <a href="{{URL::to('admin/users/g_a_request/giftcard')}}" class="btn btn-default canlcel_le">
                            <?php }else{ ?>
                            <a href="{{URL::to('admin/users/g_a_request/topup')}}" class="btn btn-default canlcel_le">
                            <?php } ?>
                            Clear Search</a></div>
                    </div>
                </div>
                {{ Form::close()}}
              <!--  <div class="add_new_record"><a href="{{URL::to('admin/users/add')}}" class="btn btn-default"><i class="fa fa-plus"></i> Add Personal User</a></div> -->
            </div>            
            <div class="m_content" id="listID">
                @include('elements.admin.users.manageGAtransReport')
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
        var currency = $('#currency').val();
        if (keyword == "") {
            keyword = 'na';
        }
        location.href = "/admin/reports/exportCSV/" + keyword + "/" + date + "/" + currency;
    }
</script>
@endsection