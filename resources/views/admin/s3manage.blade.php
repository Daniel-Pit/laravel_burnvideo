@extends('admin.layout')
@section('content')
<style>
    .candycontainer { position: absolute; background-color: rgb(255, 255, 255); 
                     border: solid 1px rgb(170, 170, 170); left: 21px; right: 21px; display: none; padding: 10px; margin-top: 1px; border-radius: 3px;
                     max-height: 120px; overflow: scroll; overflow-x: hidden; }
    .candyitem { background-color: #EFF7E2; margin:3px; padding: 3px; border-radius: 5px; border: solid 1px #B3B3B3; white-space: nowrap; display:inline-block;cursor:pointer }
    .candyitem:hover { background-color: #d2d2d2 }
    
    .allitem { background-color: #EFF7E2; margin:3px; padding: 3px; padding-left:10px; padding-right:10px; border-radius: 5px; color:#0066FF; border: solid 1px #B3B3B3; white-space: nowrap; display:inline-block;cursor:pointer }
    .allitem:hover { background-color: #d2d2d2 }
</style>

<script>


	 // Send a delete request
    function onSubmitAdd()
    {
        var from_value = $('#from_order').val();
        var to_value = $('#to_order').val();

       
        if( to_value.length == 0 )
        {
            showError("Please select an order Number", true);
            return false;
        }
        

        var url = "/admin/api_deletes3";
        $.post(url
             , { 'fromValue':from_value, 'toValue':to_value }
             , function(result){
                    console.log(result);
                    $('#add-modal').modal('hide');  
                    if( result.status )
                    {
                        window.location.reload();
                    }
                    else
                    {                     
                        $.notify("Error delete s3 bucket", { position: "bottom center",  className: 'error' });
                    }
        });
    }


</script>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        AWS-S3 Manage
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-bell-o"></i> Home</a></li>
                        <li class="active">S3-Manage</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">S3-Manage</h3> 
									<div class="box-tools">
									    <div class="input-group pull-right">
                                            <button class="btn btn-sm btn-primary" onclick="onClickAdd();" >Delete</button> 
                                        </div>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table id="objectTable" data-order='[[ 0, "desc" ]]' class="display table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <!--
                                                <th>Subject</th>
                                                -->
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($histories as $idx=>$item)
                                            <tr>
                                                <td>{{ $idx + 1  }}</td>
                                                <td>{{ $item->from }}</td>
                                                <td>{{ $item->to }}</td>
                                                <td>{{ date('m-d-Y H:i', $item->executed_date) }}</td>
                                                 <td>
                                                     <?php if( $item->status == 2 ){ ?>
                                                        <span class="label label-success">Deleted</span>
                                                    <?php } else if( $item->status == 1 ) { ?>
                                                        <span class="label label-danger">Deleting</span>
                                                    <?php } else if( $item->status == 0 ) { ?>
                                                        <span class="label label-warning">Started</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>        
                                    </table>
                                </div><!-- /.box-body -->				
                            </div><!-- /.box -->
                        </div>
                    </div>
                </section><!-- /.content -->
                
                
                <!-- add -->
                <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Delete</h4>
                            </div>
                            <div class="modal-body">
								<table style="width: 100%">
									<tr>
										<td>
											 <label>From:</label>
										</td>
									</tr>
									<tr>
										<td>
											<input type="number" id="from_order" name="from_order" class="form-control" value="1">
										</td>
									</tr>
									
									<tr>
										<td>
											 <label>To:</label>
										</td>
									</tr>
									<tr>
										<td>
											<input type="number" id="to_order" name="to_order" class="form-control" >
										</td>
									</tr>
								</table> 
							</div>
							<div class="error">
								<span id="add_error" class="text-red">this is error</span>
							</div>
							<div class="modal-footer clearfix">
								<button id="add_cancel_btn" type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
								<button id="add_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onSubmitAdd();">Delete</button>
							</div>
                            
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                
                
@stop