{{ HTML::script('public/assets/js/facebox.js')}}
{{ HTML::style('public/assets/css/facebox.css')}}

<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if(!$allrecords->isEmpty())
<div class="panel-body marginzero" style="padding-bottom: 100px">
    <!-- <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div> -->
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Crypto Withdraw Currency List</div>
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$allrecords->appends(Request::except('_token'))->render()}}
                </div>
            </div>                
        </div>
        <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th class="sorting_paging">@sortablelink('name', 'Name')</th>
                        <th class="sorting_paging">@sortablelink('created_at', 'Date')</th>
                        <th class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allrecords as $allrecord)
                     <tr>
                     <td>{{$allrecord->name}}</td>
                     <td>{{$allrecord->created_at->format('M d, Y h:i A')}}</td> 
                     <td>
                     <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                    <div class="btn-group">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-list"></i>
                    <span class="caret"></span>
                    </button>
                         <ul class="dropdown-menu pull-right">
                         <li><a href="{{ URL::to( 'admin/users/crypto_withdraw_currency_delete/'.$allrecord->id)}}" title="Delete" class="" onclick="javascript:return confirm('Are you sure you want to delete this currency ?');" ><i class="fa fa-trash"></i>Delete</a></li>
                          </ul>
                    </div>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        <div class="topn">
            <!--   <div class="topn_left">Personal Users List</div> -->
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                    {{$allrecords->appends(Request::except('_token'))->render()}}
                </div>
            </div> 

        </div>
    </section>
    {{ Form::close()}}
</div>         
</div> 
@else 
<div id="listingJS" style="display: none;" class="alert alert-success alert-block fade in"></div>
<div class="admin_no_record">No record found.</div>
@endif