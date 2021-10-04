@extends('admin.layout')
@section('content')

<script>

    function onClickEdit(id)
    {
        hideError();
        var url = "/admin/api_getUser";
        $.post(url, {'uid':id}, function(result){
            $('#edit_uid').val(result.user.id);
            $('#edit_email').val(result.user.email);
            $('#edit_oldemail').val(result.user.email);
            $("#edit_oldpassword").val(result.user.password);
            $("#edit_password").val(result.user.password);
            $("#edit_password2").val(result.user.password);
            $("#edit_first_name").val(result.user.first_name);
            $("#edit_last_name").val(result.user.last_name);
            $("#edit_street").val(result.user.street);
            $("#edit_city").val(result.user.city);
            $("#edit_state").val(result.user.state);
            $("#edit_zipcode").val(result.user.zipcode);
            $('#edit-modal').modal('show');
        });
    }

    function checkParameter(email, password, password2, firstName, lastName, street, city, state, zipcode, isAdd)
    {
    if (email.length == 0)
    {
    showError("Please input email address", isAdd);
            return false;
    }

    if (password.length == 0)
    {
    showError("Please input password", isAdd);
            return false;
    }

    if (password != password2)
    {
    showError("Error! password and retype password is different.", isAdd);
            return false;
    }

    if (firstName.length == 0)
    {
    showError("Please input first name", isAdd);
            return false;
    }

    if (lastName.length == 0)
    {
    showError("Please input last name", isAdd);
            return false;
    }

    if (street.length == 0)
    {
    showError("Please input street", isAdd);
            return false;
    }

    if (city.length == 0)
    {
    showError("Please input city", isAdd);
            return false;
    }

    if (state.length == 0)
    {
    showError("Please input state", isAdd);
            return false;
    }

    if (zipcode.length == 0)
    {
    showError("Please input zipcode", isAdd);
            return false;
    }

    return true;
    }


    function onSubmitAdd()
    {
    var email = $('#add_email').val();
            var password = $('#add_password').val();
            var password2 = $('#add_password2').val();
            var firstName = $('#add_first_name').val();
            var lastName = $('#add_last_name').val();
            var street = $('#add_street').val();
            var city = $('#add_city').val();
            var state = $('#add_state').val();
            var zipcode = $('#add_zipcode').val();
            if (checkParameter(email, password, password2, firstName, lastName, street, city, state, zipcode, true) == false)
    {
    return;
    }

    var url = "/admin/api_addUser";
            $.post(url
                    , {   'email':email, 'password':password, 'first_name':firstName
                            , 'last_name':lastName, 'street':street, 'city':city
                            , 'state':state, 'zipcode':zipcode
                    }
            , function(result){
            $('#add-modal').modal('hide');
                    if (result.status)
            {
            window.location.reload();
            }
            else
            {
            $.notify("Error added user", { position: "bottom center", className: 'error' });
            }
            });
    }

    function onSubmitEdit()
    {
            var email = $('#edit_email').val();
            var password = $('#edit_password').val();
            var password2 = $('#edit_password2').val();
            var firstName = $('#edit_first_name').val();
            var lastName = $('#edit_last_name').val();
            var street = $('#edit_street').val();
            var city = $('#edit_city').val();
            var state = $('#edit_state').val();
            var zipcode = $('#edit_zipcode').val();
            var uid = $('#edit_uid').val();
            var oldpassword = $('#edit_oldpassword').val();
            var oldemailaddr = $('#edit_oldemail').val();
            if (checkParameter(email, password, password2, firstName, lastName, street, city, state, zipcode, true) == false)
            {
                return;
            }

            var url = "/admin/api_editUser";
            $.post(
                url, 
                {   
                    'email':email, 
                    'password':password, 
                    'first_name':firstName, 
                    'last_name':lastName,
                    'street':street,
                    'city':city,
                    'state':state,
                    'zipcode':zipcode,
                    'uid':uid,
                    'oldpassword':oldpassword,
                    'oldemailaddr':oldemailaddr
                }, 
                function(result){
                    $('#edit-modal').modal('hide');
                    if (result.status == 0)
                    {
                        $.notify("Already exist email address", { position: "bottom center", className: 'error' });
                        
                    } else if (result.status)
                    {
                        window.location.reload();
                    }
                    else
                    {
                        $.notify("Fail edit user", { position: "bottom center", className: 'error' });
                    }
                });
    }

    function onSubmitDelete(uid)
    {

    var url = "/admin/api_deleteUser";
            $.post(url, { 'uid':uid }, function(result){
            if (result.status)
            {
            window.location.reload();
            }
            else
            {
            $.notify("Fail delete user", { position: "bottom center", className: 'error' });
            }
            });
    }

</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Users
        <small>DVD Burner's users</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-user"></i> Home</a></li>
        <li class="active">Users</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Users</h3>
                    <div class="box-tools">
                        <div class="input-group pull-right">
                            <button class="btn btn-sm btn-primary" onclick="onClickAdd();">Add User</button> 
                        </div>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table  id="objectTable" data-order='[[ 0, "desc" ]]' class="display table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>UserID</th>
                                <th>E-Mail</th>
                                <th>Name</th>
                                <th>Street</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                                <th>Anniversary Date</th>
                                <!--
                                <th>Remain Slot</th>
                                -->
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $idx=>$item)
                            <tr>
                                <td>{{ $idx + 1  }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->first_name . " " . $item->last_name }}</td>
                                <td>{{ $item->street }}</td>
                                <td>{{ $item->city }}</td>
                                <td>{{ $item->state }}</td>
                                <td>{{ $item->zipcode }}</td>
                                <td>{{ (($item->first_ordertime == null || $item->first_ordertime == 0 ) ? '- ' : date('n-j-Y', $item->first_ordertime )) }}</td>
                                <!--
                                <td>{{ $item->mon_weight }}</td>
                                -->
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="onClickEdit({{ $item->id }});">Edit</button>
                                    <button class="btn btn-sm btn-primary" onclick="onClickDelete({{ $item->id }});">Delete</button>
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
                <h4 class="modal-title">Add User</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Email:</span>
                        <input id="add_email" name="add_email" type="email" class="form-control" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Password:</span>
                        <input id="add_password" name="add_password" type="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Retype Password:</span>
                        <input id="add_password2" name="add_password2" type="password" class="form-control" placeholder="Retype password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">First Name:</span>
                        <input id="add_first_name" name="add_first_name" class="form-control" placeholder="First Name">                                            
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Last Name:</span>
                        <input id="add_last_name" name="add_last_name" class="form-control" placeholder="Last Name">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Street:</span>
                        <input id="add_street" name="add_street"  class="form-control" placeholder="Street">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">City:</span>
                        <input id="add_city" name="add_city" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">State:</span>
                        <input id="add_state" name="add_state" class="form-control" placeholder="State">
                    </div>
                </div>   
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Zip Code:</span>
                        <input id="add_zipcode" name="add_zipcode" class="form-control" placeholder="Zip Code">
                    </div>
                </div>   
                <div class="error">
                    <span id="add_error" class="text-red">this is error</span>
                </div>
            </div>
            <div class="modal-footer clearfix">
                <button id="add_cancel_btn" type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button id="add_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onSubmitAdd();">OK</button>
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
                <h4 class="modal-title">Edit User</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Email:</span>
                        <!--<input id="edit_email" name="edit_email" type="email" class="form-control" placeholder="Email" disabled>-->
                        <input id="edit_email" name="edit_email" type="email" class="form-control" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Password:</span>
                        <input id="edit_password" name="edit_password" type="password" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Retype Password:</span>
                        <input id="edit_password2" name="edit_password2" type="password" class="form-control" placeholder="Retype password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">First Name:</span>
                        <input id="edit_first_name" name="edit_first_name" class="form-control" placeholder="First Name">                                            
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Last Name:</span>
                        <input id="edit_last_name" name="edit_last_name" class="form-control" placeholder="Last Name">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Street:</span>
                        <input id="edit_street" name="edit_street"  class="form-control" placeholder="Street">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">City:</span>
                        <input id="edit_city" name="edit_city" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">State:</span>
                        <input id="edit_state" name="edit_state" class="form-control" placeholder="State">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">State:</span>
                        <input id="edit_zipcode" name="edit_zipcode" class="form-control" placeholder="ZipCode">
                    </div>
                </div>
                <div class="error">
                    <span id="edit_error" class="text-red">this is error</span>
                </div>
                <input type="hidden" id="edit_uid"> </input>
                <input type="hidden" id="edit_oldpassword"> </input>
                <input type="hidden" id="edit_oldemail"> </input>
            </div>
            <div class="modal-footer clearfix">
                <button id="edit_cancel_btn" type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button id="edit_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onSubmitEdit();">OK</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@stop


