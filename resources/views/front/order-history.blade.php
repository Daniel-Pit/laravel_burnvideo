@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">

                @if (Session::has('success') )
                <div class="span6 alert alert-success">
                    {{ Session::get('success') }}
                </div>
                @endif
                @if(count($errors) > 0)
                <div class="alert alert-error alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Error!</h4>

                    <p>The following errors have occurred:</p>
                    <ul id="form-errors">
                        @foreach ($errors->all('<li>:message</li>') as $error)
                        {!! $error !!}
                        @endforeach
                    </ul>
                </div>
                @endif

                <div id="rootwizard">
                    <ul class="nav nav-tabs" role="tablist">
                        <li style="width:20%;" class="active"><a href="#tab1" data-toggle="tab" id="upload_tab"><i class="fa fa-user m-r-xs"></i>Select Item to Reburn</a></li>
                        <li style="width:20%;"><a href="#tab2" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Shipping Address</a></li>
                        <li style="width:20%;"><a href="#tab3" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Personalize DVD</a></li>
                        <li style="width:20%;"><a href="#tab4" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Order More DVDs</a></li>
                        <li style="width:20%;"><a href="#tab5" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Preview Order</a></li>
                    </ul>


                    <div class="progress progress-sm m-t-sm">
                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        </div>
                    </div>
                    <form id="wizardForm">
                        <div class="tab-content">
                            <div class="tab-pane active fade in" id="tab1">
                                <div class="row m-b-lg">
                                    <div class="panel panel-white">
                                        <div class="panel-heading clearfix">
                                            <h3 style="font-size:16pt;font-weight:400;" class="panel-title">Select An Order Below To Reburn DVD</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table id="example" class="display table" style="width: 100%; cellspacing: 0;">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Ref.ID</th>
                                                            <th>DVD Title</th>
                                                            <th>No.of DVD</th>
                                                            <th>Status</th>
                                                            <th>Order Date</th>
                                                            <th>Shipping Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($orders as $idx=>$item)
                                                        <tr>
                                                            <td>
                                                                <?php if ($item->status == 3) { ?>
                                                                    <input type="checkbox" class="reburn-chk" onclick="reburn_chk_clicked(this)" orderid="{{ $item->id }}">
                                                                <?php } else { ?>
                                                                    <input type="checkbox" disabled >
                                                                <?php } ?>

                                                            </td>
                                                            <td>{{ sprintf( "A%'.08d",  $item->id ) }}</td>
                                                            <td>{{  $item->dvdtitle }}</td>
                                                            <td>{{  $item->dvdcount }}</td>
                                                            <td>
                                                                <?php if ($item->status == 4) { ?>
                                                                    <span class="label label-danger">Canceled</span>
                                                                <?php } else if ($item->status == 3) { ?>
                                                                    <span class="label label-success">Success</span>
                                                                <?php } else if ($item->status == 2) { ?>
                                                                    <span class="label label-info">Processing</span>
                                                                <?php } else if ($item->status == 1) { ?>
                                                                    <span class="label label-info">Uploaded</span>
                                                                <?php } else { ?>
                                                                    <span class="label label-warning">Request</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>{{ date('m-d-Y', $item->inserttime) }}</td>
                                                            <td>
                                                                <?php if ($item->status == 3) { ?>
                                                                    {{ date('m-d-Y', $item->updatetime) }}
                                                                <?php } else { ?>
                                                                    -
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab2">
                                <!--                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group col-md-3">
                                                                            <label for="self_quantity"># of DVD's</label><br />
                                                                            <input type="number" min="1" max="500" style="width: 150px" name="self_quantity" id="self_quantity" placeholder="Quantity" value="1">
                                                                            <span>Additional DVDs</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="self_firstname">First Name</label>
                                                                            <input type="text" class="form-control" name="self_firstname" id="self_firstname" placeholder="First Name" value="{{ $user->first_name }}">
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="self_lastname">Last Name</label>
                                                                            <input type="text" class="form-control" name="self_lastname" id="self_lastname" placeholder="Last Name" value="{{ $user->last_name }}">
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="self_address">Address</label>
                                                                            <input type="text" class="form-control" name="self_address" id="self_address" placeholder="Address" value="{{ $user->street }}">
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="self_city">City</label>
                                                                            <input type="text" class="form-control" name="self_city" id="self_city" placeholder="City" value="{{ $user->city }}">
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="self_state">State</label>
                                                                            <select class="form-control m-b-sm" name="self_state" id="self_state">
                                                                                @foreach($states as $state)
                                                                                <option <?php echo $state == $user->state ? 'selected' : '' ?>>{{ $state }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="self_zipcode">Zip</label>
                                                                            <input type="text" class="form-control" name="self_zipcode" id="self_zipcode" placeholder="Zip"  value="{{ $user->zipcode }}">
                                                                        </div>
                                                                    </div>
                                                                </div>-->

                                <div class="panel-body">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label class="col-sm-2 control-label" for="self_quantity"># of DVD's</label>
                                            <div class="col-sm-4" align="left">
                                                <input style="float:left;" type="number" min="1" max="500" name="self_quantity" id="self_quantity" placeholder="Quantity" value="1">
                                                <div class="col-sm-4"></div>
                                                <div class="col-sm-1"></div>
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="self_firstname" class="col-sm-2 control-label">First Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="self_firstname" id="self_firstname" placeholder="First Name" value="{{ $user->first_name }}">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="self_lastname" class="col-sm-2 control-label">Last Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="self_lastname" id="self_lastname" placeholder="Last Name" value="{{ $user->last_name }}">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="self_address" class="col-sm-2 control-label">Address</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="self_address" id="self_address" placeholder="Address" value="{{ $user->street }}">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="self_city" class="col-sm-2 control-label">City</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="self_city" id="self_city" placeholder="City" value="{{ $user->city }}">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label class="col-sm-2 control-label" for="self_state">State</label>
                                            <div class="col-sm-4" align="left">
                                                <select style="float:left;" class="form-control m-b-sm" name="self_state" id="self_state">
                                                    <option value="None">Please select a state : </option>
                                                    @foreach($states as $state)
                                                    <option <?php echo $state == $user->state ? 'selected' : '' ?>>{{ $state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-1"></div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="self_zipcode" class="col-sm-2 control-label">Zip</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="self_zipcode" id="self_zipcode" placeholder="Zip"  value="{{ $user->zipcode }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12">
                                            <label for="dvd_title">Enter DVD Title</label>
                                            <input type="text" class="form-control" name="dvd_title" id="dvd_title" placeholder="DVD Title" onkeyup="return count_letter()" >

                                        </div>
                                        <div class="form-group col-md-12">
                                            <span style="color: green" id="lbl_left_letter">30 characters left available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab4">
                                <!--                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group col-md-3">
                                                                            <label for="friend_quantity"># of DVD's</label><br />
                                                                            <input type="number" min="1" max="500" style="width: 150px;" name="friend_quantity" id="friend_quantity" placeholder="Quantity" value="1">
                                                                            <span>Additional DVDs</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="friend_firstname">First Name</label>
                                                                            <input type="text" class="form-control" name="friend_firstname" id="friend_firstname" placeholder="First Name" >
                                                                            <span class="error" style="display: none" id="friend_firstname_error">This field is required.</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="friend_lastname">Last Name</label>
                                                                            <input type="text" class="form-control" name="friend_lastname" id="friend_lastname" placeholder="Last Name">
                                                                            <span class="error" style="display: none" id="friend_lastname_error">This field is required.</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="friend_address">Address</label>
                                                                            <input type="text" class="form-control" name="friend_address" id="friend_address" placeholder="Address">
                                                                            <span class="error" style="display: none" id="friend_address_error">This field is required.</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="friend_city">City</label>
                                                                            <input type="text" class="form-control" name="friend_city" id="friend_city" placeholder="City">
                                                                            <span class="error" style="display: none" id="friend_city_error">This field is required.</span>
                                                                        </div>
                                                                        <div class="form-group col-md-3">
                                                                            <label for="friend_state">State</label>
                                                                            <select class="form-control m-b-sm" name="friend_state" id="friend_state">
                                                                                @foreach($states as $state)
                                                                                <option>{{ $state }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <span class="error" style="display: none" id="friend_state_error">This field is required.</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <label for="friend_zipcode">Zip</label>
                                                                            <input type="text" class="form-control" name="friend_zipcode" id="friend_zipcode" placeholder="Zip">
                                                                            <span class="error" style="display: none" id="friend_zipcode_error">This field is required.</span>
                                                                        </div>
                                                                        <div class="form-group col-md-12">
                                                                            <button type="button" id="btn_order_more_dvd" class="btn btn-primary pull-right" onclick="btn_order_more_dvd_clicked()">Order More DVD's</button>
                                                                        </div>
                                                                    </div>
                                                                </div>-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label class="col-sm-2 control-label" for="friend_quantity"># of DVD's</label>
                                            <div class="col-sm-8" align="left">
                                                <input style="float:left;" type="number" min="1" max="500" name="friend_quantity" id="friend_quantity" placeholder="Quantity" value="1">
                                                <div class="col-sm-4"></div>
                                                <div class="col-sm-8">Order Additional DVD's for Family and Friends</div>
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="friend_firstname" class="col-sm-2 control-label">First Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="friend_firstname" id="friend_firstname" placeholder="First Name" value="">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="friend_lastname" class="col-sm-2 control-label">Last Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="friend_lastname" id="friend_lastname" placeholder="Last Name" value="">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="friend_address" class="col-sm-2 control-label">Address</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="friend_address" id="friend_address" placeholder="Address" value="">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="friend_city" class="col-sm-2 control-label">City</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="friend_city" id="friend_city" placeholder="City" value="">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label class="col-sm-2 control-label" for="friend_state">State</label>
                                            <div class="col-sm-4" align="left">
                                                <select style="float:left;" class="form-control m-b-sm" name="friend_state" id="friend_state">
                                                    <option value="None">Please select a state : </option>
                                                    @foreach($states as $state)
                                                    <option>{{ $state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-1"></div>
                                        </div><br><br><br>
                                        <div class="form-group">
                                            <div class="col-sm-1"></div>
                                            <label for="friend_zipcode" class="col-sm-2 control-label">Zip</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="friend_zipcode" id="friend_zipcode" placeholder="Zip"  value="">
                                            </div>
                                        </div><br><br><br>
                                        <div class="form-group col-md-12">
                                            <button type="button" id="btn_order_more_dvd" class="btn btn-primary pull-left" onclick="btn_order_more_dvd_clicked()">Save and Order More</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab5">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel-heading clearfix">
                                            <h4 class="panel-title">Total orders</h4>
                                        </div>
                                        <div class="panel-body">
                                            <table id="order-table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>No.of DVDs</th>
                                                        <th>Name</th>
                                                        <th>Address</th>
                                                        <th>City</th>
                                                        <th>State</th>
                                                        <th>Zip</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="preview-order-tbody">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-9"></div>
                                        <div class="form-group col-md-3">
                                            <h4>Total DVDs: <label style="font-weight: bold" id="lbl_total_dvd">3</label></h4>
                                            <h4>Total Purchase: $<label style="font-weight: bold" id="lbl_total_purchase">17.56</label></h4>
                                            <h4 id="lbl_discount_h4" style="display: none;">Discount: $<label style="font-weight: bold;display: none;" id="lbl_discount"></label></h4>
                                            <h4 id="new_lbl_total_purchase_h4" style="display: none;">Discounted Total: $<label style="font-weight: bold;display: none;" id="new_lbl_total_purchase"></label></h4>
                                            <h5><label id="have_promocode" style="cursor: pointer;color: red;" onclick="showPromocodeFn();">Redeem Promo Code</label></h5>
                                            <div id="show_promocode" style="display: none;"> 
                                                <div class="col-md-9">                                                    
                                                    <input type="text" class="form-control" id="add_promocode_order">                                           
                                                </div>
                                                <div class="col-md-3">                                                    
                                                    <button type="button" class="btn btn-sm btn-info" onclick="applyPromocodeFn();">Apply</button>
                                                </div>
                                                <label id="promocode_success" class="text-success text-sm"></label>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group col-md-12">
                                            <button type="button" id="pay-now" class="btn btn-primary pull-right">Pay and Order</button>
                                            <input type="hidden" name="pay_status" id="pay_status" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="pager wizard">
                                <li class="previous"><a href="#" class="btn btn-default">Previous</a></li>
                                <li class="next"><a href="#" class="btn btn-default" id="btn_next">Next</a></li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div><!-- Main Wrapper -->
<!--Edit Order Modal-->
<!-- Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editOrderModalLabel">Edit Order</h4>
            </div>            
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label" for="edit_friend_quantity"># of DVD's (Order Additional DVD's for Family and Friends)</label>
                    <input type="number" min="1" max="500" class="form-control" name="edit_friend_quantity" id="edit_friend_quantity" placeholder="Quantity" value="1">
                </div>
                <div class="form-group">
                    <label for="edit_friend_firstname" class="control-label">First Name</label>
                    <input type="text" class="form-control" name="edit_friend_firstname" id="edit_friend_firstname" placeholder="First Name" value="">
                </div>
                <div class="form-group">
                    <label for="edit_friend_lastname" class="control-label">Last Name</label>
                    <input type="text" class="form-control" name="edit_friend_lastname" id="edit_friend_lastname" placeholder="Last Name" value="">
                </div>
                <div class="form-group">
                    <label for="edit_friend_address" class="control-label">Address</label>
                    <input type="text" class="form-control" name="edit_friend_address" id="edit_friend_address" placeholder="Address" value="">
                </div>
                <div class="form-group">
                    <label for="edit_friend_city" class="control-label">City</label>
                    <input type="text" class="form-control" name="edit_friend_city" id="edit_friend_city" placeholder="City" value="">
                </div>
                <div class="form-group">
                    <label class="control-label" for="edit_friend_state">State</label>
                    <select style="float:left;" class="form-control m-b-sm" name="edit_friend_state" id="edit_friend_state">
                        <option value="None">Please select a state : </option>
                        @foreach($states as $state)
                        <option>{{ $state }}</option>
                        @endforeach
                    </select>
                    <div class="form-group">
                        <label for="edit_friend_zipcode" class="control-label">Zip</label>
                        <input type="text" class="form-control" name="edit_friend_zipcode" id="edit_friend_zipcode" placeholder="Zip"  value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button"  class="btn btn-primary pull-left" onclick="btn_edit_order_dvd_clicked()">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
</div>
<script>
	var g_additional_order = [];
	var g_cur_additional_order;
	var g_self_order;
	var g_mon_freedvd = {{ $user->mon_freedvd }};
//            var g_mon_freedvd = 0;
	var g_mon_nextday = {{ $mon_nextday }};
	function ajax_pay() {
		waitingDialog.show("Please wait while paying...");
		var orderid = get_selected_orderid();
		$.post('ajax-pay',
			{
				'filled_mediabox_cnt': 0,
				'self_order': g_self_order,
				'additional_order': g_additional_order,
				'dvd_title': $('#dvd_title').val(),
				'promocode' : $("#add_promocode_order").val(),
				'preorderid': orderid
			})
			.done(function (data) {
				console.log(data);
				waitingDialog.hide();
				if (data.retcode == '200') {
					bootbox.alert(data.msg, function() {
						location.href = 'order-history';
					});
				} else if (data.retcode == '201'){
					$('#myModal').modal('show');
				} else if (data.retcode == '202') {
					$('#myModal').modal('show');
				} else {
					bootbox.alert(data.msg, function() {
					});
				}
			})
			.fail(function(response) {
				console.log('Error: ' + response.responseText);
			});;
	}

    $(document).ready(function() {

		count_letter();
        $('#pay-now').click(function() {
			//if (g_mon_freedvd == 0 && g_mon_nextday != 0) {
			//	bootbox.confirm("You are placing an additional order before your next due date. You will be charged $5.99 per DVD for this order. Do you wish to //proceed?", function(result) {
			//		if (result == true) {
			//			ajax_pay();
			//		} else {
			//			location.href = 'order';
			//		}
			//	});
			//} else {
			//	ajax_pay();
			//}

			ajax_pay();
		});
    });
	function btn_order_more_dvd_clicked() {

		$('#friend_quantity_error').css('display', 'none');
		$('#friend_firstname_error').css('display', 'none');
		var friend_quantity = $('#friend_quantity').val();
		var friend_firstname = $('#friend_firstname').val();
		var friend_lastname = $('#friend_lastname').val();
		var friend_address = $('#friend_address').val();
		var friend_city = $('#friend_city').val();
		var friend_state = $('#friend_state').val();
		var friend_zipcode = $('#friend_zipcode').val();
		var invalid = false;
		if (friend_quantity == '') {
			$('#friend_quantity_error').css('display', 'inherit');
			invalid = true;
		}

		if (friend_firstname == '') {
			$('#friend_firstname_error').css('display', 'inherit');
			invalid = true;
		}

		if (friend_lastname == '') {
			$('#friend_lastname_error').css('display', 'inherit');
			invalid = true;
		}
		if (friend_address == '') {
			$('#friend_address_error').css('display', 'inherit');
			invalid = true;
		}
		if (friend_city == '') {
			$('#friend_city_error').css('display', 'inherit');
			invalid = true;
		}
		if (friend_state == '' || friend_state == null) {
			$('#friend_state_error').css('display', 'inherit');
			invalid = true;
		}
		if (friend_zipcode == '') {
			$('#friend_zipcode_error').css('display', 'inherit');
			invalid = true;
		}

		if (invalid) {
			return;
		}

		var order = {
			count: friend_quantity,
			firstname: friend_firstname,
			lastname: friend_lastname,
			street: friend_address,
			city: friend_city,
			state: friend_state,
			zipcode: friend_zipcode
		};

		g_additional_order.push(order);
		$('#friend_quantity').val('1');
		$('#friend_firstname').val('');
		$('#friend_lastname').val('');
		$('#friend_address').val('');
		$('#friend_city').val('');
		$('#friend_state').val('');
		$('#friend_zipcode').val('');
	}

    function count_letter() {
		var left_available = 30 - $('#dvd_title').val().length;
        $('#lbl_left_letter').text(left_available + ' characters left available');
        if (left_available < 0) {
			$('#dvd_title').val($('#dvd_title').val().slice(0, 30));
            $('#lbl_left_letter').text('0 characters left available');
            return false;
		}
		return true;
    }

    function reburn_chk_clicked(elem) {
		$('.reburn-chk').attr('checked', false);
        $('.reburn-chk').parent().removeClass('checked');
        elem.checked = true;
    }

    function calc_selected_order() {
		var cnt = 0;
        $('.reburn-chk').each(function(idx, elem) {
			var status = $(elem).parent().hasClass('checked');
			if (status) {
				cnt ++;
			}
		});

        return cnt;
    }

    function get_selected_orderid() {
		var result = 0;
        $('.reburn-chk').each(function(idx, elem) {
			var status = $(elem).parent().hasClass('checked');
            if (status) {
				result = $('.reburn-chk').eq(idx).attr('orderid');
				return false;
			}
		});

        return result;
    }

</script>

<!-- Promocode script-->
<script>
    function showPromocodeFn(){
    $('#show_promocode').show();
        $('#have_promocode').hide();
    }
    function applyPromocodeFn(){
		$('#promocode_success').text('');
        var promocode = $('#add_promocode_order').val();
        var value = ($('#lbl_total_purchase').text()) * 1;
        if (promocode == ""){
			bootbox.alert("Please Input Promocode", function() {});
            $('#new_lbl_total_purchase_h4').hide();
            $('#new_lbl_total_purchase').hide();
            $('#lbl_discount').hide();
            $('#lbl_discount_h4').hide();
		} else {
			var url = "check-promo";
            $.post(
				url, 
				{'name': promocode, 'value': value}, 
				function (result) {
					if (result.status){
						$('#lbl_discount').text(((result.value) * 1).toFixed(2));
						$('#new_lbl_total_purchase').text((value - ($('#lbl_discount').text() * 1)).toFixed(2));
						$('#new_lbl_total_purchase_h4').show();
						$('#new_lbl_total_purchase').show();
						$('#lbl_discount_h4').show();
						$('#lbl_discount').show();
						$('#promocode_success').text('Your Promocode Applied Successfully');
					} else {
					bootbox.alert(result.message, function() {});
							$('#new_lbl_total_purchase').text('');
							$('#lbl_discount').text('');
							$('#add_promocode_order').val('');
							$('#new_lbl_total_purchase_h4').hide();
							$('#new_lbl_total_purchase').hide();
							$('#lbl_discount').hide();
							$('#lbl_discount_h4').hide();
					}
				}
			);
		}
    }

//    Edit and remove order Script
    function editOrderFn(i){
		edit_row = i;
        console.log(edit_row);
        $('#editOrderModal').modal('show');
		console.log(g_additional_order);
		$('#edit_friend_quantity').val(g_additional_order[i].count);
		$('#edit_friend_firstname').val(g_additional_order[i].firstname);
		$('#edit_friend_lastname').val(g_additional_order[i].lastname);
		$('#edit_friend_address').val(g_additional_order[i].street);
		$('#edit_friend_city').val(g_additional_order[i].city);
		$('#edit_friend_state').val(g_additional_order[i].state);
		$('#edit_friend_zipcode').val(g_additional_order[i].zipcode);
    }

    function btn_edit_order_dvd_clicked(){
		$("#col1_" + edit_row).text($('#edit_friend_quantity').val());
		$("#col2_" + edit_row).text($('#edit_friend_firstname').val());
		$("#col3_" + edit_row).text($('#edit_friend_lastname').val());
		$("#col4_" + edit_row).text($('#edit_friend_address').val());
		$("#col5_" + edit_row).text($('#edit_friend_city').val());
		$("#col6_" + edit_row).text($('#edit_friend_state').val());
		$("#col7_" + edit_row).text($('#edit_friend_zipcode').val());
		console.log($('#edit_friend_quantity').val());
		g_additional_order[edit_row].count = $('#edit_friend_quantity').val();
		g_additional_order[edit_row].city = $('#edit_friend_city').val();
		g_additional_order[edit_row].firstname = $('#edit_friend_firstname').val();
		g_additional_order[edit_row].lastname = $('#edit_friend_lastname').val();
		g_additional_order[edit_row].state = $('#edit_friend_state').val();
		g_additional_order[edit_row].street = $('#edit_friend_address').val();
		g_additional_order[edit_row].zipcode = $('#edit_friend_zipcode').val();
		var cnt_total_dvd = parseInt($('#self_quantity').val());

		for (var j = 0; j < g_additional_order.length; j++) {
			cnt_total_dvd += parseInt(g_additional_order[j].count);
		}
		$('#lbl_total_dvd').text(cnt_total_dvd);
        $('#lbl_total_purchase').text(((cnt_total_dvd - g_mon_freedvd) * 5.99).toFixed(2));
        $('#editOrderModal').modal('hide');
    }

    function removeOrderFn(i){
		$('#order-table').closest('table').find('tbody > tr').each(function(){
            var idr = this.id;

            if (idr == 'row_' + i){
				$(this).remove();
            }
        });

        var cnt_total_dvd = parseInt($('#self_quantity').val());

        for (var j = 0; j < g_additional_order.length; j++) {
			if (i == j){
				delete g_additional_order[j];
			}
			if (i != j){
				if (g_additional_order[j]){
					cnt_total_dvd += parseInt(g_additional_order[j].count);
				}
			}
		}
		console.log(cnt_total_dvd);
		$('#lbl_total_dvd').text(cnt_total_dvd);
		$('#lbl_total_purchase').text(((cnt_total_dvd - g_mon_freedvd) * 5.99).toFixed(2));
    }
</script>
{{ Html::script('assets/frontend/js/pages/form-wizard-orderhistory.js') }}
@stop