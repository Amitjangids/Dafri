@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Card Details</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/admins/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="active"> Manage Card Details</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
            <div class="admin_search">
                {{ Form::open(array('method' => 'post', 'id' => 'adminSearch')) }}
                <div class="form-group align_box dtpickr_inputs">
                    <span class="hints">Search by Serial Number</span>
                    <span class="hint">{{Form::text('keyword', null, ['class'=>'form-control', 'placeholder'=>'Search by Serial Number', 'autocomplete' => 'off'])}}</span>
                    <div class="admin_asearch">
                        <div class="ad_s ajshort">{{Form::button('Submit', ['class' => 'btn btn-info admin_ajax_search'])}}</div>
                        <div class="ad_cancel"><a href="{{URL::to('admin/cards')}}" class="btn btn-default canlcel_le">Clear Search</a></div>
                    </div>
                </div>
                {{ Form::close()}}
                <div class="add_new_record"><a href="{{URL::to('admin/cards/addcarddetail/'.$cslug)}}" class="btn btn-default"><i class="fa fa-plus"></i> Add Card Detail</a></div>
            </div>            
            <div class="m_content" id="listID">
                @include('elements.admin.cards.carddetail')
            </div>
        </div>
    </section>
</div>
@endsection