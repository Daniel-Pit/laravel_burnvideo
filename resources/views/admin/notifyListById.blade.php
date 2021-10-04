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
    String.prototype.trim=function(){
        return this.replace("/(^\s*)|(\s*$)/g", "");
    }
    
    String.prototype.ltrim=function(){
        return this.replace("/(^\s*)/g","");
    }
    
    String.prototype.rtrim=function(){
        return this.replace("/(\s*$)/g","");
    }
	
	
    function onSelectUser( searchText, userName )
    {
        var textUser = $("#add_user").val();
        var texts = textUser.split(",");
        var last = texts[texts.length -1].trim();
        
        var find = false;
        for( i = 0; i < texts.length; i++)
        {
            var itemText = texts[i].replace(/(^\s*)|(\s*$)/g, ""); 
            if( userName == itemText )
            {
                find = true;
                break;
            }
        }
        
        if( find == false )
        {
            if( textUser.trim().length == 0 || searchText == "___all___" )
            {
                 textUser =  userName;
            }
            else
            {   
                if( last == searchText )
                {
                    textUser = textUser.substring(0, textUser.length - last.length ) + userName;
                }
                else
                {
                    textUser =  textUser + ", " + userName;
                }
            }
            $("#add_user").val(textUser);
        }
        /*$('#candycontainer').hide();*/
    }
    
    function onFocusUser()
    {        
        $('#candycontainer').show();
        var textUser = $("#add_user").val();
        if( textUser.length == 0 )
        {
            onUserInputChange();
        }
    }
    
    function onUserInputChange()
    {
        var textUser = $("#add_user").val();
        var texts = textUser.split(",");
        var searchText = texts[texts.length -1];
        var url = "/admin/api_searchUsers";
        $.post(url, {'search':searchText}, function(result){           
            if( result.users != null )    
            {
                var length =result.users.length;

		var htmlText = "";
                
                if( length > 0 )
                {
                    var usernames = '';
                    for (var i = 0; i < length; i++) 
                    {
                        var user = result.users[i];
                        
                        if( i > 0 )
                        {
                            usernames += ",";
                        }
                        usernames += user.uname 
                    }
                    
                    htmlText += '<span class="allitem" onmousedown="onSelectUser('
                            + "'___all___'"
                            + ", '" + usernames + "'" + ');">' 
                            + ' Select All ' + '</span>';
                }
               
                
		for (var i = 0; i < length; i++) {
                    var user = result.users[i];
                    htmlText += '<span class="candyitem" onmousedown="onSelectUser('
                            + "'" + searchText + "'"
                            + ", '" + user.uname + "'" + ');">' 
                            + user.uname + '</span>';
		}
                
                $("#candycontainer").html( htmlText);
                $('#candycontainer').show();
            }
        });
    }
    
    function onBlurUser()
    {
        $('#candycontainer').hide();
    }

	 // Send
    function onSubmitAdd()
    {
		var usersfrom = $('#from_id').val();
        var usersend = $('#end_id').val();

        var subject  = $('#add_message').val();
       
        if (usersfrom.length == 0)
        {
            showError("Please input user's id", true);
            return false;
        }
		if (usersend.length == 0)
		{
			showError("Please input user's id", true);
			return false;
		}
        
        if( subject.length == 0 )
        {
            showError("Please input message", true);
            return false;
        }
        
        var url = "/admin/api_sendNotifyById";
        $.post(url
             , { 'usersfrom':usersfrom, 'usersend':usersend, 'message':subject }
             , function(result){
                    $('#add-modal').modal('hide');  
                    if( result.status )
                    {
                        window.location.reload();
                    }
                    else
                    {                     
                        $.notify("Error push notification", { position: "bottom center",  className: 'error' });
                    }
        });
    }

    function onClickEdit( id )
    {   
        hideError();
        var url = "/admin/api_getNotify";
        $.post(url, {'nid':id}, function(result){           
           $('#edit_nid').val(result.msg.nid);
           $('#edit_title').val(result.msg.n_title);
           $("#edit_message").val(result.msg.n_message);
           $("#edit_time").val(result.msg.time);
           $("#edit_user").val(result.msg.users);
           $('#edit-modal').modal('show');
        });
    }
    
    function onSubmitDelete(id)
    {
        var url = "/admin/api_deleteNotify";
        $.post(url, { 'nid':id }, function(result){
            if(result.status)
            {
                window.location.reload();
            }
            else
            {
                $.notify("Fail delete notify", { position: "bottom center",  className: 'error' });
            }
        });
    }

	 $(document).ready(function(){ 
        $('#add_user').bind('keyup', function(e) {
            onUserInputChange();
        });
        
        $('#add_user').bind('focus', function(e) {
            onFocusUser();
        });
        
        $('#add_message').bind('focus', function(e) {
            onBlurUser();
        });
    }); 

</script>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Push
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-bell-o"></i> Home</a></li>
                        <li class="active">Push</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Push By UserId</h3> 
									<div class="box-tools">                                        
                                        <div class="input-group pull-right">
                                            <button class="btn btn-sm btn-primary" onclick="onClickAdd();" >Push</button> 
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
                                                <th>Contents</th>
                                                <th>Users</th>
                                                <th>Time</th>
                                                <th>Operation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($notifies as $idx=>$item)
                                            <tr>
                                                <td>{{ $idx + 1  }}</td>
                                                <!--
                                                <td>{{ $item->n_title }}</td>
                                                -->
                                                <td>{{ substr( $item->n_message, 0, 60) }}</td>
                                                <td>{!! $item->users !!}</td>
                                                <td>{{ date('m-d-Y H:i', $item->n_time) }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="onClickEdit({{ $item->nid }});">View</button>
                                                    <button class="btn btn-sm btn-primary" onclick="onClickDelete({{ $item->nid }});">Delete</button>
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
                                <h4 class="modal-title">Push</h4>
                            </div>
                            <div class="modal-body">
								<table style="width: 100%">
									<tr>
										<td>
											 <label>Receiver:</label>
										</td>
									</tr>
									<tr>
										<td>
											<table>
												<tr>
													<td>
														Start UserId: <input id="from_id" name="from_id" class="form-control">                                    
													</td>
													<td>
														End UserId: <input id="end_id" name="end_id" class="form-control">                        
													</td>
												</tr>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>
											 <label>Message:</label>
										</td>
									</tr>
									<tr>
										<td>
											<textarea id="add_message" style="width:100%;height:200px;resize:none"></textarea>
										</td>
									</tr>
								</table> 
							</div>
							<div class="error">
								<span id="add_error" class="text-red">this is error</span>
							</div>
							<div class="modal-footer clearfix">
								<button id="add_cancel_btn" type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
								<button id="add_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onSubmitAdd();">Send</button>
							</div>
                            
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->




                
                <!-- edit -->
                <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">View Push</h4>
                            </div>
                            
                                <div class="modal-body">
                                    <table style="width: 100%">
                                        <tr>
                                            <td>
                                                 <label>Subject:</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                  <input id="edit_title" name="edit_title" class="form-control" ></input>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <label>Content:</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <textarea id="edit_message" style="width:100%;height:200px;resize:none">
                                                </textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <label>Users:</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <input id="edit_user" name="edit_user" class="form-control" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <label>Time:</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <input id="edit_time" name="edit_time" class="form-control" >
                                            </td>
                                        </tr>
                                    </table>                   
                                </div>
                                <div class="modal-footer clearfix">
                                    <button id="edit_cancel_btn" type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                </div>
                            
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                
                
@stop