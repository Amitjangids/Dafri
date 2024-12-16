{{ HTML::script('public/assets/js/facebox.js')}}
{{ HTML::style('public/assets/css/facebox.css')}}
<script type="text/javascript">
    $(document).ready(function ($) {
        $('.close_image').hide();
        $('a[rel*=facebox]').facebox({
            closeImage: '{!! HTTP_PATH !!}/public/img/close.png'
        });
        
        $('.dropdown-menu a').on('click', function (event) { 
            $(this).parent().parent().parent().toggleClass('open');
        });
    });
</script>
<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", '')}}</div>
@if(!$allrecords->isEmpty())
<div class="panel-body marginzero">
    <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
          <!--  <div class="topn_left">Fees List</div> -->
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
                        <th style="width:5%">#</th>
                        <th class="sorting_paging">@sortablelink('fee_name', 'Name')</th>
                        <th class="sorting_paging">@sortablelink('fee_value', 'Value')</th>
                        <th class="sorting_paging">@sortablelink('edited_by', 'Edited By')</th>
                        <th class="sorting_paging">@sortablelink('created_at', 'Created Date')</th>
                        <th class="sorting_paging">@sortablelink('updated_at', 'Updated Date')</th>
                        <th class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allrecords as $key=>$allrecord)
					@php
					$editedBy = getAdminNameById($allrecord->edited_by);
					@endphp
                    <tr>
                        <th style="width:5%">{{$key + $allrecords->firstItem()}}</th>
                        <td data-title="Name">{{$allrecord->fee_name}}</td>
						<td data-title="Country">
                                                    @if($allrecord->fee_name == 'EXCHANGE_FEE') {{$allrecord->fee_value.' USD'}} @else {{$allrecord->fee_value.'%'}}  @endif
                                                    
                                                </td>
						<td data-title="Edited By">{{$editedBy}}</td>
                        <td data-title="Date">{{$allrecord->created_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Date">{{$allrecord->updated_at->format('M d, Y h:i A')}}</td>
                        <td data-title="Action">
                            <div id="loderstatus{{$allrecord->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            
                            
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
								@php
								$flag = validatePermission(Session::get('admin_role'),'edit-fees');
								@endphp
								@if($flag == true)
                                <ul class="dropdown-menu pull-right">
								<li><a href="{{URL::to('/admin/fees/editFees/'.$allrecord->id)}}" title="View Agent Detail"><i class="fa fa-edit"></i>Edit</a></li>
                                </ul>
								@endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
                
        </div>
    </section>
    {{ Form::close()}}
</div>         
</div> 
@else 
<div id="listingJS" style="display: none;" class="alert alert-success alert-block fade in"></div>
<div class="admin_no_record">No record found.</div>
@endif