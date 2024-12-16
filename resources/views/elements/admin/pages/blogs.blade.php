{{ HTML::script('public/assets/js/facebox.js')}}
{{ HTML::style('public/assets/css/facebox.css')}}
<script type="text/javascript">
    $(document).ready(function ($) {
        $('.close_image').hide();
        $('a[rel*=facebox]').facebox({
            closeImage: '{!! HTTP_PATH !!}/public/img/close.png'
        });
    });
</script>
<div class="admin_loader" id="loaderID">{{HTML::image("public/img/website_load.svg", SITE_TITLE)}}</div>
@if(!$allrecords->isEmpty())
    <div class="panel-body marginzero">
        <div class="ersu_message">@include('elements.admin.errorSuccessMessage')</div>
        {{ Form::open(array('method' => 'post', 'id' => 'actionFrom')) }}
            <section id="no-more-tables" class="lstng-section">
                <div class="topn">
                    <div class="topn_left">Blogs List</div>
                    <div class="topn_rightd ddpagingshorting" id="pagingLinks" align="right">
                        <div class="panel-heading" style="align-items:center;">
                            {{$allrecords->appends(Input::except('_token'))->render()}}
                        </div>
                    </div>                
                </div>
                <div class="tbl-resp-listing">
                <table class="table table-bordered table-striped table-condensed cf">
                    <thead class="cf ddpagingshorting">
                        <tr>
                            <th class="sorting_paging">@sortablelink('title', 'Blog Title')</th>
                            <th class="sorting_paging">Edited By</th>
                            <th class="sorting_paging">@sortablelink('created_at', 'Date')</th>
                            <th class="action_dvv"> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allrecords as $allrecord)
                            <tr>
                                <td data-title="Full Name">{{ ucwords(strtolower($allrecord->title))}}</td>
								<td data-title="Full Name">
								@php
						 if ($allrecord->edited_by != '-1') {
						  $edited_by = getAdminNameById($allrecord->edited_by);
						 }
						 else {
						  $edited_by = 'N/A'; 
						 }
						@endphp
						{{ $edited_by }}
								</td>
                                <td data-title="Date">{{$allrecord->created_at->format('M d, Y')}}</td>
                                <td data-title="Action">
                                    <a href="{{ URL::to('admin/blogs/edit/'.$allrecord->id)}}" title="Edit" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                                    <a href="#info{!! $allrecord->id !!}" title="View" class="btn btn-primary btn-xs" rel='facebox'><i class="fa fa-eye"></i></a>
									
				    <a href="#" data-toggle="modal" data-target="#exampleModal2" title="Delete" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></a>
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
<script>
function deleteBlog(blogID)
{
  var a = confirm('Are you sure, you want to delete this blog');
  if (a) {
	location.href = '/admin/blogs/deleteBlog/'+blogID;  
  }
}
</script>
@else 
    <div id="listingJS" style="display: none;" class="alert alert-success alert-block fade in"></div>
    <div class="admin_no_record">No record found.</div>
@endif


@if(!$allrecords->isEmpty())
    @foreach($allrecords as $allrecord)
        <div id="info{!! $allrecord->id !!}" style="display: none;">
            <div class="nzwh-wrapper">
                <fieldset class="nzwh">
                     <legend class="head_pop">{!! $allrecord->title !!}</legend>
                    <div class="drt">
                        <div class="admin_pop">
						{{HTML::image(BLOG_FULL_DISPLAY_PATH.$allrecord->image, SITE_TITLE)}}
						{!! $allrecord->description !!}</div>  
                    </div>
                </fieldset>
            </div>
        </div>
    
<!-- Modal -->
<div class="modal x-dialog  fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="basicModal2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content headmodel">
            <div class="modal-body ">
                <button type="button"  class="close" data-dismiss="modal"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{HTML::image('public/img/front/dafribank-logo-black.svg', SITE_TITLE)}}
                <br><br>
                <p>Are you sure, you want to delete this blog?</p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><a type="button" href="{{ URL::to( 'admin/blogs/deleteBlog/'.$allrecord->id)}}" id="myButton" class="btn btn-dark" onclick="">Confirm</a></li>
                    <li class="list-inline-item"><button type="button"  class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>
    @endforeach
@endif