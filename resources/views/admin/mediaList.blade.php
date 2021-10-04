@extends('admin.layout')
@section('content')

<script>
    $(document).ready(function() {
	$('#mediaTable').dataTable( {
            "pagingType": "full_numbers"
	} );
    } );
</script>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Media List
                        <small>the user uploaded files</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-bar-chart-o"></i> Home</a></li>
                        <li class="active">Media List</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Media Files</h3>                                    
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table id="mediaTable" data-order='[[ 0, "desc" ]]'class="display table table-hover">
                                         <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>File</th>
                                                <th>Path</th>
                                                <th>Slot</th>
                                                <!--
                                                <th>Status</th>
                                                -->
                                                <th>Create Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($medias as $idx=>$item)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $item->first_name . " " . $item->last_name }}</td>
                                                <td>{{ $item->filename }}</td>
                                                <td><a href="{{ $item->url }}">Get File</a></td>
                                                <td>{{ $item->fweight }}</td>
                                                <!--
                                                <td><?php 
                                                     if( $item->fstatus == 0 )
                                                     {
                                                         echo '<span class="label label-info">Upload</span>';
                                                     }
                                                     else if( $item->fstatus == 1 )
                                                     {
                                                         echo '<span class="label label-warning">Converting</span>';
                                                     }
                                                     else if( $item->fstatus == 2 )
                                                     {
                                                         echo '<span class="label label-success">Success</span>';
                                                     } 
                                                     else if( $item->fstatus == 3 )
                                                     {
                                                         echo '<span class="label label-danger">Fail</span>';
                                                     } 
                                                    ?>
                                                </td>
                                                -->
                                                <td>{{ $item->finserttime }}</td>
                                            </tr>
                                        @endforeach                                      
                                        </tbody>
                                    </table>                                       
                                </div><!-- /.box-body -->
				
                            </div><!-- /.box -->
                        </div>
                    </div>
                </section><!-- /.content -->    
@stop