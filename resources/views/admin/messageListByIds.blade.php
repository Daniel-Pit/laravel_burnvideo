@extends('admin.layout')
@section('content')
<style>
    .candycontainer { position: absolute; background-color: rgb(255, 255, 255); 
                      border: solid 1px rgb(170, 170, 170); left: 21px; right: 21px; display: none; padding: 10px; margin-top: 1px; border-radius: 3px;
                      max-height: 120px; overflow: scroll; overflow-x: hidden; z-index:10000; }
    .candyitem { background-color: #EFF7E2; margin:3px; padding: 3px; border-radius: 5px; border: solid 1px #B3B3B3; white-space: nowrap; display:inline-block;cursor:pointer }
    .candyitem:hover { background-color: #d2d2d2 }

    .allitem { background-color: #EFF7E2; margin:3px; padding: 3px; padding-left:10px; padding-right:10px; border-radius: 5px; color:#0066FF; border: solid 1px #B3B3B3; white-space: nowrap; display:inline-block;cursor:pointer }
    .allitem:hover { background-color: #d2d2d2 }
</style>
<script>
    function a(){
    }
</script>
<script>
    String.prototype.trim = function(){
    return this.replace("/(^\s*)|(\s*$)/g", "");
    }

    String.prototype.ltrim = function(){
    return this.replace("/(^\s*)/g", "");
    }

    String.prototype.rtrim = function(){
    return this.replace("/(\s*$)/g", "");
    }

    function onClickEdit(id)
    {
    hideError();
            var url = "/admin/api_getMessage";
            $.post(url, {'mid':id}, function(result){
            $('#edit_mid').val(result.msg.mid);
                    $('#edit_title').val(result.msg.m_title);
                    //$("#edit_message").val(result.msg.m_message);
              		$("#edit_message").summernote('code', result.msg.m_message);
                    $("#edit_time").val(result.msg.time);
                    $("#edit_sender").val(result.msg.m_sender);
                    $("#edit_user").val(result.msg.users);
                    $('#edit-modal').modal('show');
            });
    }

    // Send
    function onSubmitAdd()
    {
		var title = $('#add_title').val();
        var usersIds = $('#userIds').val();

		//var content = $('#add_message').summernote('code');
		var subject = $('#add_message').summernote('code');
        //var subject = $('#add_message').val();
        var sender = $('#add_sender option:selected').text();
        if (title.length == 0)
		{
			showError("Please input title", true);
			return false;
		}

        if (usersIds.length == 0)
        {
            showError("Please input user's ids", true);
            return false;
        }

	    if (subject.length == 0)
	    {
			showError("Please input message", true);
            return false;
		}

		var url = "/admin/api_sendMailByIds";
		$.post( url, { 'title':title, 'ids':usersIds, 'message':subject, 'sender': sender })
			.done(function(result){
				$('#add-modal').modal('hide');
				console.log(result.status);
				if (result.status)
				{
					window.location.reload();
				}
				else
				{
					$.notify("Error send mail", { position: "bottom center", className: 'error' });
				}
			})
			.fail(function(response) {
          		$('#add-modal').modal('hide');
				console.log('Error: ' + response.responseText);
				$.notify("Error send mail", { position: "bottom center", className: 'error' });
			});
    }

    function onSubmitDelete(id)
    {
        var url = "/admin/api_deleteMessage";
        $.post(url, { 'mid':id }, function(result){
            if (result.status)
            {
                window.location.reload();
            }
            else
            {
                $.notify("Fail delete message", { position: "bottom center", className: 'error' });
            }
        });
    }

  
    $(document).ready(function(){
      
		$('#add_message').summernote({
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['table', ['table']],
				['insert', ['link', 'picture', 'video', 'hr']],
				['view', ['fullscreen', 'codeview']],
				['help', ['help']]
			],
			height:200,
			fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36', '48' , '64', '72'],
			callbacks: {
				onImageUpload: function(files, editor, $editable) {
					sendFile(files[0],editor,$editable);
				}
			}
		});   
		$('#edit_message').summernote({
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']], // Still buggy
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['table', ['table']],
				['insert', ['link', 'picture', 'video', 'hr']],
				['view', ['fullscreen', 'codeview']],
				['help', ['help']]
			],
			fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36', '48' , '64', '72']
        }); 
 
		function sendFile(file,editor,welEditable) {
            data = new FormData();
            data.append("file", file);

			var url = "/admin/api_uploadMailImage";

            jQuery.ajax({
                url: url,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(result){
					if (result.retCode == 0 && result.url.length > 0)
					{
						jQuery('#add_message').summernote("insertImage", result.url);
						return true;
					}

					$.notify("Fail upload image", { position: "bottom center", className: 'error' });
					return false;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus+" "+errorThrown);
                }
            });
        }      
      
    });</script>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Message
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-envelope"></i> Home</a></li>
        <li class="active">Message</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Messages By IDs</h3>  
                    <div class="box-tools">                                        
                        <div class="input-group pull-right">
                            <button class="btn btn-sm btn-primary" onclick="onClickAdd();" >Send Mail</button> 
                        </div>

                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table id="objectTable" data-order='[[ 0, "desc" ]]' class="display table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <!-- <th>Contents</th> -->
                                <th>Sender</th>
                                <th>Users</th>
                                <th>Time</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $idx=>$item)
                            <tr>
                                <td>{{ $idx + 1  }}</td>
                                <td>{{ $item->m_title }}</td>
                                <!-- <td>{{ substr( $item->m_message, 0, 40) }}</td> -->
                                <td>{{ $item->m_sender }}</td>
                                <td>{!! $item->users !!}</td>
                                <td>{{ date('m-d-Y H:i', $item->m_time) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="onClickEdit({{ $item->mid }});">View</button>
                                    <button class="btn btn-sm btn-primary" onclick="onClickDelete({{ $item->mid }});">Delete</button>
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
                <h4 class="modal-title">Send Mail</h4>
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
                            <input id="add_title" name="add_title" class="form-control" ></input>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Sender:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select id="add_sender"  class="form-control">
                                <option default>questions@burnvideo.net</option>
                                <option>info@burnvideo.net</option>
                                <option>reminder@burnvideo.net</option>
                                <option>promotions@burnvideo.net</option>
                                <option>billing@burnvideo.net</option>
                              	<option>sharla@burnvideo.net</option>
                            </select>
                        </td>
                    </tr>
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
                                        UserIds: <textarea id="userIds" name="userIds" class="form-control" rows=5 cols=110></textarea>
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
                    <tr id="contentarea">
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
                <h4 class="modal-title">View Message</h4>
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
                            <input id="edit_title" name="edit_title" class="form-control" readonly></input>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Message:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <textarea id="edit_message" style="width:100%;height:200px;resize:none" readonly>
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Sender:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input id="edit_sender" name="edit_sender" class="form-control" readonly >
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label>Receiver:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input id="edit_user" name="edit_user" class="form-control" readonly >
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Time:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input id="edit_time" name="edit_time" class="form-control"  readonly >
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

