@extends('layouts.inner')
@section('content')
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
                    <h4 class="form-head-top">Mobile Top-up and Bill Payment </h4>
                </div>
                <div class="col-sm-12">
                    <div class="top-up-form">
                        <form>
                            <!--       <div class="radio-top-up-form">
                                <div class="radio-top">
                                    <input type="radio" id="f-option" name="selector" checked="">
                                    <label for="f-option">Prepaid</label>
                                    <div class="check"></div>
                                </div>
                                <div class="radio-top">
                                    <input type="radio" id="f-option1" name="selector">
                                    <label for="f-option1">Postpaid</label>
                                    <div class="check"></div>
                                </div>
                            </div> -->
                            <div class="top-form-fields">
                                <div class="form-group">
                                    <input type="text" name="" placeholder="Mobile Number">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="" placeholder="Operator ">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="" placeholder="Circle">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="" placeholder="Amount">
                                </div>
                                <div class="btn-group-top">
                                    <button class="sub-btn btn_sub">Proceed</button></div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-12 mt-4">
                    <h4 class="form-head-top">Browse Plan for Telkon </h4>
                </div>
                <div class="col-sm-12">
                    <div class="top-up-form">
                        <div class="tabbable-panel plan-table">
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs ">
                                    <li class="active">
                                        <a href="#tab_default_1" data-toggle="tab">
                                            Popular </a>
                                    </li>
                                    <li>
                                        <a href="#tab_default_2" data-toggle="tab">
                                            Validity</a>
                                    </li>
                                    <li>
                                        <a href="#tab_default_3" data-toggle="tab">
                                            Data Add On</a>
                                    </li>
                                    <li>
                                        <a href="#tab_default_4" data-toggle="tab">
                                            Top Up </a>
                                    </li>
                                    <li>
                                        <a href="#tab_default_5" data-toggle="tab">
                                            Roaming</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_default_1">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Circle</th>
                                                    <th>Plan type</th>
                                                    <th>Data</th>
                                                    <th>Validity</th>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="tab_default_2">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Circle</th>
                                                    <th>Plan type</th>
                                                    <th>Data</th>
                                                    <th>Validity</th>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>3 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="tab_default_3">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Circle</th>
                                                    <th>Plan type</th>
                                                    <th>Data</th>
                                                    <th>Validity</th>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="tab_default_4">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Circle</th>
                                                    <th>Plan type</th>
                                                    <th>Data</th>
                                                    <th>Validity</th>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane" id="tab_default_5">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Circle</th>
                                                    <th>Plan type</th>
                                                    <th>Data</th>
                                                    <th>Validity</th>
                                                    <th>Description</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bethlehem</td>
                                                    <td>Recharge</td>
                                                    <td>2 GB</td>
                                                    <td>28 Days</td>
                                                    <td>Enjoy TRULY unlimited Local STD & Roaming calls on any network 2GB Data and 300 SMS. Pack valid for 28 days.</td>
                                                    <td class="price-td">
                                                        <div class="price">200 ZAR</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
var phone_number = window.intlTelInput(document.querySelector("#recipient_phone"), {
    separateDialCode: true,
    preferredCountries: false,
    //onlyCountries: ['iq'],
    hiddenInput: "recipient_phone",
    utilsScript: "<?php echo HTTP_PATH; ?>/public/assets/js/utils.js"
});

function changeOperator(myStr) {
    var coCode = $('.iti__selected-dial-code').html();
    document.getElementById('contryCode').value = coCode;

    var withSpace = myStr.length;

    if (withSpace > 5) {
        alert(coCode);
        var phone = myStr;
        var access_token = $('#access_token').val();
        var contryCode = coCode;

        $.ajax({
            url: "{!! HTTP_PATH !!}/getOperator",
            type: "POST",
            data: { 'access_token': access_token, 'phone': phone, 'contryCode': contryCode, _token: '{{csrf_token()}}' },
            success: function(result) {

                alert(result);
                //                    $('#success_message').html('OTP sent successfully');
                //                    $('#success-alert-Modal').modal('show');
            }
        });
    }

}
</script>
@endsection