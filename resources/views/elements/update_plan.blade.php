<h4 class="form-head-top" id="plan_title">Browse Plan for {{$name}} </h4>
<?php
// echo '<pre>';
// print_r($operator);
//var_dump( !(array)$operator->fixedAmountsDescriptions);
?>
@if(!(array)$operator->fixedAmountsDescriptions == false && $operator->denominationType=="FIXED")
<div class="" id="plan_sset">
    <div class="top-up-form">
        <div class="tabbable-panel plan-table">
            <div class="tabbable-line">

                <div class="tab-content">
                    <div class="tab-pane active" id="tab_default_1">
                        <table>
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 0; @endphp
                                @foreach($operator->suggestedAmountsMap as $key=>$amount)
                                <tr>
                                    <td>{{$operator->fixedAmountsDescriptions->$key}}</td>
                                    <td class="price-td">
                                        <div class="price" id="id{{$i}}" onclick="updatePrice({{$amount}}, '{{$operator->destinationCurrencyCode}}', this.id, {{$key}},'{{$operator->senderCurrencyCode}}')">{{$amount}} {{$operator->destinationCurrencyCode}}</div>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else 

@if(!(array)$operator->suggestedAmountsMap == false && $operator->denominationType=="FIXED")
<div class="" id="plan_sset">
    <div class="top-up-form">
        <div class="tabbable-panel plan-table">
            <div class="tabbable-line">
                <div class="price-box">
                    @foreach($operator->suggestedAmountsMap as $key=>$amount)
                    <div class="price" id="id{{$key}}" onclick="updatePrice({{$amount}}, '{{$operator->destinationCurrencyCode}}', this.id, {{$key}},'{{$operator->senderCurrencyCode}}')">{{$amount}} {{$operator->destinationCurrencyCode}}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@elseif(!(array)$operator->suggestedAmountsMap == false && $operator->denominationType=="RANGE")
<div class="" id="plan_sset">
    <div class="top-up-form">
        <div class="tabbable-panel plan-table">
            <div class="tabbable-line">
                <div class="price-box">
                    @foreach($operator->suggestedAmountsMap as $key=>$amount)
                    <div class="price" id="id{{$key}}" onclick="updatePrice({{$amount}}, '{{$operator->destinationCurrencyCode}}', this.id, {{$key}},'{{$operator->senderCurrencyCode}}')">{{$amount}} {{$operator->destinationCurrencyCode}}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
 @elseif(!(array)$operator->suggestedAmounts == false  && $operator->denominationType=="RANGE")

<div class="" id="plan_sset">
    <div class="top-up-form">
        <div class="tabbable-panel plan-table">
            <div class="tabbable-line">
                <div class="price-box">
                    @foreach($operator->suggestedAmounts as $key=>$amount)
                    <div class="price" id="id{{$amount}}" onclick="updatePrice({{$amount}}, '{{$operator->destinationCurrencyCode}}', this.id, {{$amount}},'{{$operator->senderCurrencyCode}}')">{{$amount}} {{$operator->destinationCurrencyCode}}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> 
@endif
@endif
<script>
$(document).ready(function () {
    $('#trnsfrCurrncy').val('{{$currency}}');
});
</script>
<!--<div class="">
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
</div>-->