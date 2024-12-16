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
@if(!$admins->isEmpty())
<div class="panel-body marginzero">
   <!-- <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div> -->
    {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
    <section id="no-more-tables" class="lstng-section">
        <div class="topn">
            <div class="topn_left">Admin List</div>
            <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                <div class="panel-heading" style="align-items:center;">
                   
                </div>
            </div>                
        </div>
        <div class="tbl-resp-listing">
            <table class="table table-bordered table-striped table-condensed cf">
                <thead class="cf ddpagingshorting">
                    <tr>
                        <th style="width:5%">Sno</th>
                        <th>@sortablelink('role_id','Department')</th>
                        <th class="sorting_paging">@sortablelink('username','Username')</th>
                        <th class="sorting_paging">@sortablelink('email','Email')</th>
                        <th class="sorting_paging">@sortablelink('created_at','Date')</th>
                        <th class="action_dvv"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $key=>$admin)
					@php
					 $role_name = getRoleNameById($admin->role_id);
					@endphp
                    <tr>
                        <td style="width:3%">{{$key+1}}</th>
                        <td style="width:20%">{{$role_name}}</th>
                        <td style="width:25%" data-title="Username">{{$admin->username}}</td>
                        <td style="width:29%" data-title="Email">{{$admin->email}}</td>
                        <td style="width:18%" data-title="Date">
						@php
						 $date = date_create($admin->created_at);
						 $createdAt = date_format($date,'M d, Y h:i A');
						@endphp
						{{$createdAt}}
						</td>
                        <td style="width:5%" data-title="Action">
                            <div id="loderstatus{{$admin->id}}" class="right_action_lo">{{HTML::image("public/img/loading.gif", '')}}</div>
                            
                            
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fa fa-list"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="{{ URL::to( 'admin/admins/edit-admin/'.$admin->id)}}" title="Edit Role" class=""><i class="fa fa-pencil"></i>Edit Admin</a></li>
                                </ul>
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