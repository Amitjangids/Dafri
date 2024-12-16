@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
                <div class="col-sm-12 mt-5 text-center">
                    <h3 class="bank-head">Bank Transfer</h3>
                </div>
                <div class="col-sm-6">
                    <div class="bank-detail-transfer">
                        <h5><img src="images/south-africa.svg"> SOUTH AFRICA </h5>
                        <div class="inner-b-data">
                            <h6>DAFRITECH (PTY) LTD </h6>
                            <div class="bank-data"><strong>ACCOUNT:</strong> 4099929441</div>
                            <div class="bank-data"><strong>BRANCH CODE:</strong> 632005</div>
                            <div class="bank-data"><strong>ACCOUNT TYPE:</strong> CHEQUE</div>
                        </div>
                        <div class="b-name">ABSA BANK</div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="bank-detail-transfer">
                        <h5><img src="images/south-africa.svg"> SOUTH AFRICA </h5>
                        <div class="inner-b-data">
                            <h6>DAFRITECH (PTY) LTD </h6>
                            <div class="bank-data"><strong>ACCOUNT:</strong> 10143348661</div>
                            <div class="bank-data"><strong>BRANCH CODE:</strong> 51001</div>
                            <div class="bank-data"><strong>ACCOUNT TYPE:</strong> CHEQUE</div>
                        </div>
                        <div class="b-name">STANDARD BANK</div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="bank-detail-transfer">
                        <h5><img src="images/botswana.svg"> BOTSWANA </h5>
                        <div class="inner-b-data">
                            <h6>DAFRITECH (PTY) LTD </h6>
                            <div class="bank-data"><strong>ACCOUNT:</strong> 62881068889</div>
                            <div class="bank-data"><strong>BRANCH CODE:</strong> 281467</div>
                            <div class="bank-data"><strong>ACCOUNT TYPE:</strong> CHEQUE</div>
                        </div>
                        <div class="b-name">FNB BW</div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="bank-detail-transfer">
                        <h5><img src="images/nigeria.svg">NIGERIA </h5>
                        <div class="inner-b-data">
                            <h6>DAFRITECHNOLOGIES LTD </h6>
                            <div class="bank-data"><strong>ACCOUNT:</strong> 1017518610</div>
                            <div class="bank-data"><strong>BRANCH CODE:</strong> N/A</div>
                            <div class="bank-data"><strong>ACCOUNT TYPE:</strong>  CHEQUE </div>
                        </div>
                        <div class="b-name">ZENITH BANK</div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>
<script src="<?php echo HTTP_PATH; ?>/public/assets/js/front/top_search.js"></script>
@endsection