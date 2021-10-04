@extends('admin.layout')
@section('content')
<script>
   
    function onClickCancel(id)
    {
        bootbox.confirm("Are you sure?", function(result) {
            if( result)
            {
                var url = "/admin/api_cancelOrder";
                $.post(url, { 'oid':id }, function(result){
                    if(result.status)
                    {
                        $.notify("Success cancel order", { position: "bottom center",  className: 'success' });
                        window.location.reload();
                    }
                    else
                    {
                        $.notify("Fail cancel order", { position: "bottom center",  className: 'error' });
                    }
                });
            }
        });        
    }
    
    function onClickConvert(id)
    {
        bootbox.confirm("Are you sure?", function(result) {
            if( result)
            {
                var url = "/admin/api_convertOrder";
                $.post(url, { 'oid':id }, function(result){
                    if(result.status)
                    {
                        $.notify("Success convert order", { position: "bottom center",  className: 'success' });
                        window.location.reload();
                    }
                    else
                    {
                        $.notify("Fail convert order", { position: "bottom center",  className: 'error' });
                    }
                });
            }
        });        
    }
    
    function onClickSend(id)
    {
        bootbox.confirm("Are you sure?", function(result) {
            if( result)
            {
                var url = "/admin/api_sendOrder";
                $.post(url, { 'oid':id }, function(result){
                    if(result.status)
                    {
                        $.notify("Success send order", { position: "bottom center",  className: 'success' });
                        window.location.reload();
                    }
                    else
                    {
                        $.notify("Fail send order", { position: "bottom center",  className: 'error' });
                    }
                });
            }
        });        
    }
    
    function onSubmitDelete(id)
    {
        var url = "/admin/api_deleteOrder";
        $.post(url, { 'oid':id }, function(result){
            if(result.status)
            {
                $.notify("Success delete order", { position: "bottom center",  className: 'success' });
                window.location.reload();
            }
            else
            {
                $.notify("Fail delete order", { position: "bottom center",  className: 'error' });
            }
        });
    }
    
    function jsUcfirst(string) 
    {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }    
    
    function onClickEdit( id )
    {   
        var url = "/admin/api_getShipArray";
        $.post(url, {'oid':id}, function(result){           
            var order = result.order;
            var ouser = result.user;
            var shippings = result.shippings;
            var resultCount = shippings.length;

            $("#edit_orderid").val( order.id );
            $("#edit_ordertag").val( order.orderid );
            $("#edit_user").val( jsUcfirst(ouser.first_name.toLowerCase()) + " " + jsUcfirst(ouser.last_name.toLowerCase()) );
			var platform="";
			switch(order.devicetype){
				case 0:
					platform="PC";
					break;
				case 1:
					platform="iOS";
					break;
				case 2:
					platform="Android";
					break;

			}
            $("#edit_platform").val( platform );          
			$("#edit_title").val( order.orderid + ": " + order.dvdtitle );
            $("#edit_title2").val( order.dvdtitle );
            $("#edit_dvdcount").val( order.dvdcount );
            $("#edit_weight").val( order.weight );        
            $("#edit_download").attr( "href", order.zipurl );
            $("#edit_createtime").val( order.inserttime );
            $("#edit_updatetime").val( order.updatetime );
            
            if( order.zipurl == null || order.zipurl.length == 0 )
            {
                $("#edit_download").text("-");
            }
            else
            {
                $("#edit_download").text("TS ZIP" );
            }

            var htmlText = "";            
            for (var i = 0; i < resultCount; i++) {	
                var shipping = shippings[i];
		htmlText += "<tr>";
                htmlText += "<td>" + jsUcfirst(shipping.firstname.toLowerCase()) + " " + jsUcfirst(shipping.lastname.toLowerCase()) ;
                htmlText += "-" + shipping.dvdcount; 
                htmlText += "-" + order.orderid; 
                htmlText += "<br/>" + shipping.street ;
                htmlText += "<br/>" + jsUcfirst(shipping.city.toLowerCase()) ;
                htmlText += " " + shipping.state;
                htmlText += "<br/>" + shipping.zipcode ;
                
                htmlText += "</td>";
                htmlText += "</tr>";
            }
            $("#shippings").html( htmlText);
            hideError();    
            $("#input_edit_title").hide();
            $('#edit-modal').modal('show');
        });
    }
    
    function onSetDvdTitle()
    {
        var oid = $("#edit_orderid").val( );
        var title = $("#edit_title2").val( );
        
        if( title.length > 36 )
        {
            $.notify("Fail Set DVD Title. please input less than 30 letter.", { position: "top center",  className: 'error' });
            return;
        }
        
        var url = "/admin/api_setOrderDvdTitle";
        
        $.post(url, {'oid':oid, 'title':title}, function(result){              
             if( result.status == 0 )
             {
                   $.notify("Success set dvd titile", { position: "bottom center",  className: 'success' });
             }
         });
    }
    
    function showEditTitle()
    {
        var title = $("#edit_title").val();
        var orderid = $("#edit_ordertag").val();
        
        title = title.substring(orderid.length + 2);
        $("#edit_title2").val(title);
        
        $("#input_edit_title").width( $("#edit_title").width() + 24);
        $("#input_edit_title").show();
    }
    
    function commitTitle()
    {
        var title = $("#edit_title2").val();
        var orderid = $("#edit_ordertag").val();
        
        title = orderid + ": " + title;
        $("#edit_title").val(title);
        $("#input_edit_title").hide();
    }

</script>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Order List
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-check-square-o"></i> Home</a></li>
                        <li class="active">Orders</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Orders</h3>                                    
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table id="objectTable" data-order='[[ 0, "asc" ]]' class="display table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Order ID</th>
                                                <th>User</th>
                                              
                                              	<th>Platform</th>
                                                <!--
                                                <th>File List</th>
                                                -->
                                                <th>MS Used</th>
                                                <th>File Count</th>
                                                <th>Status</th>
                                                <th>Download</th>
                                                <th>PreOrder</th>
                                                <th>Create Time</th>
                                                <th>Update Time</th>
                                                <th>Operation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $idx=>$item)
                                            <tr>
                                                <td class="td_middle">{{ $idx + 1  }}</td>
                                                <td class="td_middle">{{ empty($item->id)? "-" : sprintf( "A%'.08d",  $item->id )  }}
                                                <?php
                                                    if( $item->firstOrderFlag > 0 ){
                                                        echo "<br><font style='font-size:10px;color:#FF1493;'>NEW</font>";
                                                    }
                                                ?>
                                              	</td>
                                                <td class="td_middle">{{ ucfirst(strtolower($item->first_name)) . ' ' . ucfirst(strtolower($item->last_name)) }}</td>
												<td class="td_middle">
												<?php
													switch($item->devicetype){
														case "0":
															echo "PC";
															break;
														case "1":
															echo "iOS";
															break;
														case "2":
															echo "Android";
															break;
													}

												?>
												</td>                                              
                                                
                                                <td class="td_middle">{{ $item->weight }}</td>
                                                <td class="td_middle">{{ $item->filecount }}</td>
                                                <td class="td_middle">
                                                     <?php if( $item->status == 4 ){ ?>      
                                                        <span class="label label-danger">Canceled</span>
                                                    <?php } else if( $item->status == 3 ) { ?>
                                                        <span class="label label-success">Success</span>
                                                    <?php } else if( $item->status == 2 ) { ?>
                                                        <span class="label label-info">
                                                            <?php
                                                                if ( $item->downloaded == 0 )
                                                                    echo "Converted";
                                                                if ( $item->downloaded == 1 )
                                                                    echo "Downloading";
                                                                if ( $item->downloaded == 2 )
                                                                    echo "Downloaded";
                                                            ?>
                                                        </span>
                                                    <?php } else if( $item->status == 1 ) { ?>
                                                    <?php if( $item->burn_lock == 0 ) { ?>
                                                        <span class="label label-info">Uploaded</span>
													<?php } else if( $item->burn_lock == 2 ) { ?>
                                                        <span class="label label-danger">Bad</span>
													<?php } else { ?>
                                                        <span class="label label-danger"><?php 
                                                  			if($item->burn_app == 0){
                                                              echo "PHP". $item->burn_app_num . " ";
                                                            } else {
                                                              echo "Lambda ";
                                                            } ?>Converting</span>
													<?php } ?>
                                                    <?php } else { ?>
                                                        <span class="label label-warning">Request</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="td_middle">                                                     
                                                    <?php if( $item->status == 3 ) { ?>
                                                        <a href="{{ $item->zipurl }}">TS ZIP</a>
                                                    <?php } else if( $item->status == 2 ) { ?>
                                                        <a href="{{ $item->zipurl }}">TS ZIP</a>
                                                    <?php } else { ?>
                                                        -
                                                    <?php } ?>
                                                </td>
                                                <td class="td_middle"><?php if( $item->preorder > 0 ) echo $item->preorder; else echo "-";?></td>
                                                <td class="td_middle">{{ $item->inserttime }}</td>
                                                <td class="td_middle">{{ $item->updatetime }}</td>
                                                <td class="td_middle">
                                                    <?php if( $item->status == 4 ){ ?>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickEdit({{ $item->id }});'>View</button>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickDelete({{ $item->id }});'>Delete</button>
                                                    <?php } else if( $item->status == 3 ) { ?>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickEdit({{ $item->id }});'>View</button>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickDelete({{ $item->id }});'>Delete</button>
                                                    <?php } else if( $item->status == 2 ) { ?>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickEdit({{ $item->id }});'>View</button>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickSend({{ $item->id }});'>Ship</button>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickCancel({{ $item->id }});'>Cancel</button>
                                                    <?php } else if( $item->status == 1 ) { ?>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickEdit({{ $item->id }});'>View</button>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickCancel({{ $item->id }});'>Cancel</button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickEdit({{ $item->id }});'>View</button>
                                                        <button class="btn btn-sm btn-primary" onclick='onClickCancel({{ $item->id }});'>Cancel</button>
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
                <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Order Information</h4>
                            </div>
                            
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">Order ID:</span>
                                            <input id="edit_ordertag" type="text" class="form-control" onfocus="commitTitle();"  readonly >
                                            <input id="edit_orderid" type="hidden">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">User:</span>
                                            <input id="edit_user" type="text" class="form-control" onfocus="commitTitle();" readonly >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">Platform:</span>
                                            <input id="edit_platform" type="text" class="form-control" readonly >
                                        </div>
                                    </div>                                  
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">DVD Title:</span>
                                            <input id="edit_title" type="text" class="form-control" style="cursor:pointer;" onclick="showEditTitle();" readonly></input>
                                            <div style="display: none;position: absolute;z-index: 500;width:363px;" id="input_edit_title">
                                                <input id="edit_title2" type="text" class="form-control" onblur="commitTitle();"></input>
                                            </div>
                                            <div class="input-group-btn">
                                                <button id="edit_title_btn" type="button" class="btn btn-primary" onclick="onSetDvdTitle();">Set</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">DVD Count:</span>
                                            <input id="edit_dvdcount" type="text" class="form-control" onfocus="commitTitle();" readonly >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">MS Used:</span>
                                            <input id="edit_weight" type="text" class="form-control"  onfocus="commitTitle();" readonly >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">TS ZIP:</span>
                                            <div class="form-control"  onclick="commitTitle();" >
                                                <a id="edit_download"> TS ZIP</a>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">Create Time:</span>
                                            <input id="edit_createtime" type="text" class="form-control"  onfocus="commitTitle();"  readonly >
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon input_header">Update Time:</span>
                                            <input id="edit_updatetime" type="text"  class="form-control"  onfocus="commitTitle();" readonly >
                                        </div>
                                    </div>
                                    <div class="form-group"  onclick="commitTitle();">
                                        <h4>Shipping Information</h4>
                                        <table class="table">  
                                            <tbody id="shippings">                                                
                                            </tbody>
                                        </table>
                                    </div>                                    
                                </div>
                                <div class="modal-footer clearfix">                
                                    <button id="add_ok_btn" type="button" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
                                </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                
@stop
