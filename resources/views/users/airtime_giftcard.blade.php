@extends('layouts.inner')
@section('content')
<!-- <script>
$(document).ready(function () {
    $('#failed_message').html("Services are not available at the moment");
    $('#failed-alert-Modal').modal('show');
})</script> -->
<style>
    .fund-name-box h6 {
        font-size: 12px !important;
    }

    .btn_sub {
        width: auto;
        display: inline-block;
        padding: 12px 20px;
        margin-bottom: 30px;

    }

    .btn_sub:hover {
        text-decoration: none;
        color: var(--main-white-color);
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2 w-100">
            <div class="row" ng-app="">

                <div class="col-sm-12 mt-4">
                    <div class="top-header-giftcard">
                        <h4 class="form-head-top">GiftCard</h4>
                        <div class="daily-yd">
                     <a class="drop-total-bal-head" href="{{HTTP_PATH}}/auth/giftcard-purchased">My GiftCards</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="top-up-form">

                  
                        {{ Form::open(array('method' => 'post', 'name' =>'fundTransfrForm', 'id' => 'fundTransfrForm', 'class' => '','[formGroup]'=>'formGroup','onsubmit'=>'return getPlan(1);')) }}
                        <div class="top-form-fields">
                            <div class="form-group" id="operator_set">
                            <select name="countryCode" class="required form-control" id="countryCode">
                            <option value="" selected="true">Select Country</option>
                            <?php foreach($countryData as $country) { ?>
                            <option value="{{$country->sortname}}" <?php if($country->name==$recordInfo->country) { echo "selected"; } ?>>{{$country->name}} </option>
                            <?php } ?>
                            </select>
                            </div>  

                            <div class="form-group">
                                {{Form::text('productName', null, ['class'=>'form-control','placeholder'=>'Search Product', 'id'=> 'trnsfrAmnt', 'autocomplete'=>'OFF' ])}}
                            </div>
                       
                            <div class="btn-group-top">
                                <input type="hidden" name="access_token" id="access_token" value="{{$access_token}}">
                                <button class="sub-btn btn_sub" type="submit">Search</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                      
                    </div>
                </div>   
                <div class="col-sm-12 mt-4"  id="update_plan">
                    <div class="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
{{ HTML::style('public/assets/css/intlTelInput.css?ver=1.3')}}
{{ HTML::script('public/assets/js/intlTelInput.js')}}
<script>
    $(document).ready(function () {
    $("#fundTransfrForm").validate();
    //getPlan(1);
    });
    

                    function getPlan(pageNum) {
                        var access_token = $('#access_token').val();
                        var operator_id = $('#countryCode').val();
                        if(operator_id=="")
                        {
                        return false;
                        }
                        var productName = $('#trnsfrAmnt').val();
                        //alert(operator_id);
                        $.ajax({
                            url: "{!! HTTP_PATH !!}/getGiftCard",
                            type: "POST",
                            data: {'access_token': access_token, 'operator_id': operator_id, _token: '{{csrf_token()}}','pageNum':pageNum,'productName':productName},
                            beforeSend: function () {
                            $('#loaderID').css("display", "flex");
                            },
                            success: function (result) {
                                if (result != 0) {
                                $('#update_plan').html(result);
                                }
                            $('#loaderID').css("display", "none");
                            }
                        });
                        return false;
                    }


                    function productDetail(id)
                    {
                      $('#product_id').val(id);
                      $('#product_id_form').submit();
                    }

</script>

{{ Form::open(array('method' => 'post', 'name' =>'product_id_form', 'id' => 'product_id_form', 'class' => '','[formGroup]'=>'formGroup','url'=>'/product-detail')) }}

<input type="hidden" name="product_id" id="product_id">

{{ Form::close() }}

@endsection
