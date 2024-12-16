@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-6 border-right">
                    <div class="heading-section">
                        <h5>Buy/Sell Crypto</h5>
                    </div>
                    <div class="layout--tabs crypto-tab">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" id="tabs-title-region-nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" role="tab" href="#block-simple-text-1" aria-selected="false" aria-controls="block-simple-text-1" id="block-simple-text-1-tab">Buy</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" href="#block-simple-text-2" aria-selected="false" aria-controls="block-simple-text-2" id="block-simple-text-2-tab">Sell</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="tab-content">
                                    <div id="block-simple-text-1" class="tab-pane active block block-layout-builder block-inline-blockqfcc-blocktype-simple-text" role="tabpanel" aria-labelledby="block-simple-text-1-tab">
                                        <div class="currency-box">
                                            <h6>Currency</h6>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="currency-thumb">
                                                        <div class="icon">
                                                            {{HTML::image('public/img/front/bitcoin.svg', SITE_TITLE)}}
                                                            <p>Bitcoin <span>BTC</span></p>
                                                        </div>
                                                        <h3>$0.00</h3>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="currency-thumb">
                                                        <div class="icon">
                                                            {{HTML::image('public/img/front/JhHD1j.tif.svg', SITE_TITLE)}}
                                                            <p>Ethereum <span>ETH</span></p>
                                                        </div>
                                                        <h3>$0.00</h3>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="currency-thumb">
                                                        <div class="icon">
                                                            {{HTML::image('public/img/front/Path101.svg', SITE_TITLE)}}
                                                            <p>DBA</p>
                                                        </div>
                                                        <h3>$0.00</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form method="get" action="#">
                                            <div class="exchange-box cur-exc">
                                                <h6>Amount</h6>
                                                <div class="exchange-field-box">
                                                    <div class="drop-text-field">
                                                        <input type="text" name="" placeholder="8,785.28">
                                                        <select class="dropdown-arrow">
                                                            <option value="" selected="">USD</option>
                                                            <option value="1">BTC</option>
                                                            <option value="1">DBA</option>
                                                            <option value="1">ETH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <a href="#" class="ex-icon"> 
                                                    {{HTML::image('public/img/front/exchange-icon.svg', SITE_TITLE)}}
                                                </a>
                                                <div class="exchange-field-box">
                                                    <div class="drop-text-field">
                                                        <input type="text" name="" placeholder="1">
                                                        <select class="dropdown-arrow">
                                                            <option value="" selected="">BTC</option>

                                                            <option value="1">DBA</option>
                                                            <option value="1">ETH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button class="sub-btn" type="submit" onclick="window.location.href = '#';">
                                                    Coming Soon
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="block-simple-text-2" class="tab-pane  block block-layout-builder block-inline-blockqfcc-blocktype-simple-text" role="tabpane2" aria-labelledby="block-simple-text-2-tab">
                                        <div class="currency-box">
                                            <h6>Currency</h6>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="currency-thumb">
                                                        <div class="icon">
                                                            {{HTML::image('public/img/front/bitcoin.svg', SITE_TITLE)}}
                                                            <p>Bitcoin <span>BTC</span></p>
                                                        </div>
                                                        <h3>$0.00</h3>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="currency-thumb">
                                                        <div class="icon">
                                                            {{HTML::image('public/img/front/JhHD1j.tif.svg', SITE_TITLE)}}
                                                            <p>Ethereum <span>ETH</span></p>
                                                        </div>
                                                        <h3>$0.00</h3>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="currency-thumb">
                                                        <div class="icon">
                                                            {{HTML::image('public/img/front/Path101.svg', SITE_TITLE)}}
                                                            <p>DBA</p>
                                                        </div>
                                                        <h3>$0.00</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form method="get" action="#">
                                            <div class="exchange-box cur-exc">
                                                <h6>Amount</h6>
                                                <div class="exchange-field-box">
                                                    <div class="drop-text-field">
                                                        <input type="text" name="" placeholder="0.00">
                                                        <select class="dropdown-arrow">
                                                            <option value="" selected="">USD</option>
                                                            <option value="1">BTC</option>
                                                            <option value="1">DBA</option>
                                                            <option value="1">ETH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <a href="#" class="ex-icon"> 
                                                    {{HTML::image('public/img/front/exchange-icon.svg', SITE_TITLE)}}
                                                </a>
                                                <div class="exchange-field-box">
                                                    <div class="drop-text-field">
                                                        <input type="text" name="" placeholder="1">
                                                        <select class="dropdown-arrow">
                                                            <option value="" selected="">BTC</option>

                                                            <option value="1">DBA</option>
                                                            <option value="1">ETH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button class="sub-btn" type="submit" onclick="window.location.href = '#';">
                                                    Coming Soon
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 pad-l-50">
                    <div class="heading-section">
                        <h5>Trending Bitcoins</h5>
                    </div>
                    <div class="heading-section head-small-sec">
                        <div class="grahp-left">
                            {{HTML::image('public/img/front/JhHD1j.tif.svg', SITE_TITLE)}}
                            <h5>
                                Ethereum
                                <span>ETH</span>
                            </h5>
                        </div>
                        <div class="grahp-right">
                            <h5>
                                USD 53,245.92
                            </h5>
                            <span>
                                +0.34
                            </span>
                        </div>
                    </div>
                    <div class="graph-bar gb">
                        <div id="chart" class="chart-bit-coin">
                            <ul id="numbers">
                                <li><span>45k</span></li>
                                <li><span>30k</span></li>
                                <li><span>15k</span></li>
                            </ul>
                            <ul id="bars">
                                <li>
                                    <div data-percentage="20" class="bar pos1"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos2"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos3"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos3"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos2"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos1"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos2"></div>
                                </li>
                                <li>
                                    <div data-percentage="20" class="bar pos4"></div>
                                </li>
                            </ul>
                        </div>
                        <ul id="mints">
                            <li><span>15 MIN</span></li>
                            <li><span>30 MIN</span></li>
                            <li><span>45 MIN</span></li>
                            <li><span>60 MIN</span></li>
                        </ul>
                        <ul class="days-box-chart">
                            <li>1 hour</li>
                            <li>3 days</li>
                            <li>1 week</li>
                            <li>1 month</li>
                            <li>More</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@endsection