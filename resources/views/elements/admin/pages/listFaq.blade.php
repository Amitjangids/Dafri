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
                  <!--  <div class="topn_left">Pages List</div> -->
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
                            <th class="sorting_paging">@sortablelink('faq_ques', 'Ques')</th>
                            <th class="sorting_paging">@sortablelink('faq_ans', 'Ans')</th>
                            <th class="sorting_paging">@sortablelink('sort_order', 'Order')</th>
                            <th class="sorting_paging">Edited By</th>
                            <th class="sorting_paging">@sortablelink('created_at', 'Date')</th>
                            <th class="action_dvv"> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allrecords as $allrecord)
                            <tr>
                                <td data-title="Question">{{$allrecord->faq_ques}}</td>
                                <td data-title="Answer">{{substr(strip_tags($allrecord->faq_ans),0,60)}}</td>
                                <td data-title="Sort Order">{{$allrecord->sort_order}}</td>
								<td data-title="Edited By">
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
                                    <a href="{{ URL::to('admin/pages/editFaq/'.$allrecord->id)}}" title="Edit" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                                    <a href="#info{!! $allrecord->id !!}" title="View" class="btn btn-primary btn-xs" rel='facebox'><i class="fa fa-eye"></i></a>
									
									<a href="#" data-toggle="modal" data-target="#exampleModal3" title="Delete" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></a>
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


@if(!$allrecords->isEmpty())
    @foreach($allrecords as $allrecord)
        <div id="info{!! $allrecord->id !!}" style="display: none;">
            <div class="nzwh-wrapper">
                <fieldset class="nzwh">
                     <legend class="head_pop">{!! $allrecord->faq_ques !!}</legend>
                    <div class="drt">
                        <div class="admin_pop">{!! $allrecord->faq_ans !!}</div>  
                    </div>
                </fieldset>
            </div>
        </div>
    <div class="modal x-dialog  fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="basicModal2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content headmodel">
            <div class="modal-body ">
                <button type="button"  class="close" data-dismiss="modal"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{HTML::image('public/img/front/dafribank-logo-black.svg', SITE_TITLE)}}
                <br><br>
                <p>Are you sure, you want to delete this Question?</p>
                <ul class="list-inline btn-list">
                    <li class="list-inline-item"><a type="button" href="{{ URL::to('admin/pages/deleteFaq/'.$allrecord->id)}}" id="myButton" class="btn btn-dark" onclick="">Confirm</a></li>
                    <li class="list-inline-item"><button type="button"  class="btn btn-light" data-dismiss="modal">Cancel</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>
    @endforeach
@endif