@extends('admin.layout')
@section('content')

<script>

    function onClickEdit(id) {
        $('#edit-modal').modal('show');
        hideError();
        var url = "/admin/api_getPromoCode";
        $.post(url, {'id': id}, function (result) {
            $('#edit_uid').val(result.promocode.id);
            $('#edit_promocode').val(result.promocode.name);
            $('#edit_type').val(result.promocode.type);
            $('#edit_value_percent').val(result.promocode.value);
            $('#edit_expiry_date').val(result.promocode.expiry_date);
            $('#edit_description').val(result.promocode.description);
            $('#edit-modal').modal('show');
        });
    }

    function checkParameter(promocode, type, valuepercent, expirydate, description, isAdd) {
        if (promocode.length == 0) {
            showError("Please input promocode", isAdd);
            return false;
        } else if (type.length == 0) {
            showError("Please select type", isAdd);
            return false;
        } else if (valuepercent.length == 0 || valuepercent <= 0) {
            showError("Please Input Valid Value/Percentage", isAdd);
            return false;
        } else if (type == 'percentage' && valuepercent > 100) {
            showError("Percentage should not above 100", isAdd);
            return false;
        } else if (expirydate.length == 0) {
            showError("Please input expirydate", isAdd);
            return false;
        } else if (description.length == 0) {
            showError("Please input description", isAdd);
            return false;
        } else {
            showError("", isAdd);
            return true;
        }
    }


    function onSubmitAdd() {

        var promocode = $('#add_promocode').val();
        var type = $('#add_type').val();
        var valuepercent = $('#add_value_percent').val();
        var expirydate = $('#add_expiry_date').val();
        var description = $('#add_description').val();
        if (checkParameter(promocode, type, valuepercent, expirydate, description, true) == false) {
            return;
        }

        var url = "/admin/api_addPromoCode";
        
        $.post(url
                , {'name': promocode, 'type': type, 'value': valuepercent
                    , 'expiry_date': expirydate, 'description': description
                }
        , function (result) {
            if (result.status) {
                $('#add-modal').modal('hide');         
                window.location.reload();
            } else {
                $.notify(result.message, {position: "bottom center", className: 'error'});
            }
        });
    }

    function onSubmitEdit() {
        var id = $('#edit_uid').val();
        var promocode = $('#edit_promocode').val();
        var type = $('#edit_type').val();
        var valuepercent = $('#edit_value_percent').val();
        var expirydate = $('#edit_expiry_date').val();
        var description = $('#edit_description').val();
        if (checkParameter(promocode, type, valuepercent, expirydate, description, false) == false) {
            return;
        }
        var url = "/admin/api_editPromoCode";
                $.post(url, {'id': id, 'name': promocode, 'type': type, 'value': valuepercent, 'expiry_date': expirydate, 'description': description}, function (result) {
                
                if (result.status) {
                    $('#edit-modal').modal('hide');
                    window.location.reload();
                } else {
                    $.notify(result.message, {position: "bottom center", className: 'error'});
                }
                }
                );
    }

</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Promo Codes
        <small>Available Promo Codes</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-user"></i> Home</a></li>
        <li class="active">Promcodes</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Promocodes</h3>
                    <div class="box-tools">
                        <div class="input-group pull-right">
                            <button class="btn btn-sm btn-primary" onclick="onClickAdd();">Add Promocode</button> 
                        </div>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table  id="objectTable" data-order='[[ 0, "desc" ]]' class="display table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Promo Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Expiration Date</th>
                                <th>Description</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($promos as $idx=>$item)
                            <tr>
                                <td>{{ $idx + 1  }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['value'] }}</td>
                                <td>{{ $item['expiry_date'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="onClickEdit({{$item['id']}});">Edit</button>
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
                <h4 class="modal-title">Add Promocode</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Promocode:</span>
                        <input id="add_promocode" name="promocode" type="text" class="form-control" placeholder="Promocode">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Type:</span>
                        <select id="add_type" name="type" placeholder="Type"  class="form-control">
                            <option value="">-- Select --</option>
                            <option value="value">Value</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Value/Percentage:</span>
                        <input id="add_value_percent" name="value" type="number" min="1" class="form-control" placeholder="Value/Percentage">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group date form_date" data-date="" data-date-format="mm-dd-yyyy" data-link-field="add_expiry_date" data-link-format="mm-dd-yyyy">
                        <span class="input-group-addon input_header">Expiry Date:</span>
                        <input id="add_expiry_date" name="expirydate" type="text" class="form-control" placeholder="Expiration Date" value="" readonly>                                            
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Description:</span>
                        <textarea id="add_description" name="description" class="form-control" placeholder="Descripon"></textarea>
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
                <h4 class="modal-title">Edit Promocode</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Promocode:</span>
                        <input id="edit_promocode" name="promocode" type="text" class="form-control" placeholder="Promocode">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Type:</span>
                        <select id="edit_type" name="type" placeholder="Type"  class="form-control">
                            <option value="">-- Select --</option>
                            <option value="value">Value</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Value/Percentage:</span>
                        <input id="edit_value_percent" name="value" type="number" min="1" class="form-control" placeholder="Value/Percentage">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group date form_date" data-date="" data-date-format="mm-dd-yyyy" data-link-field="edit_expiry_date" data-link-format="mm-dd-yyyy">
                        <span class="input-group-addon input_header">Expiry Date:</span>
                        <input id="edit_expiry_date" name="expirydate" type="text" class="form-control" placeholder="Expiry Date" value="" readonly>                                            
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input_header">Description:</span>
                        <textarea id="edit_description" name="description" class="form-control" placeholder="Descripon"></textarea>
                    </div>
                </div>                                      
                <div class="error">
                    <span id="edit_error" class="text-red">this is error</span>
                </div>
                <input type="hidden" id="edit_uid">
            </div>
            <div class="modal-footer clearfix">
                <button id="edit_cancel_btn" type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button id="edit_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onSubmitEdit();">OK</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@stop


