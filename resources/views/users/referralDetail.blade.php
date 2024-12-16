@extends('layouts.inner')
@section('content')
<div class="d-flex" id="wrapper">
    @include('elements.side_menu')        
    <!-- Page Content -->
    <div id="page-content-wrapper">
        @include('elements.top_header')
        <div class="wrapper2">
            <div class="row">
    
                <div class="table-box col-sm-12">
                    <h6>Affiliated Accounts</h6>
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                            <th scope="col">S.No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $i=1;
                            @endphp
                            @foreach($allrecords as $res)
                            @php    
                            $date = date_create($res->created_at);
                            $refDate = date_format($date,'d M Y');
                            @endphp	
                            <?php 
                          //  echo "<pre>";
                          //  print_r($res); ?>
                            <tr>
                            <td>{{ $i }}</td>
                                <td>{{ $refDate }}</td>
                                <td>  
                                    @if($res->user_type == 'Personal')
                                    {{ strtoupper(strtolower($res->first_name." ".$res->last_name))}}
                                    @elseif($res->user_type == 'Business')
                                    {{ strtoupper(strtolower($res->business_name)) }}
                                    @elseif($res->user_type == 'Agent' && $res->first_name != "")
                                    {{ strtoupper(strtolower($res->first_name." ".$res->last_name))}}
                                    @elseif($res->user_type == 'Agent' && $res->business_name != "")
                                    {{ strtoupper(strtolower($res->business_name)) }}
                                    @endif</td>
                                <td>{{ $res->email }}</td>
                            </tr>
                            @php 
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

@endsection