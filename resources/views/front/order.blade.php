@extends('front.layout')
@section('content')
<div id="main-wrapper" class="container" style="min-width: 1164px;">
    <div class="col-lg-12">
        <div class="panel panel-white">
            <div class="panel-body no-padding">

                @if (Session::has('success') )
                <div class="span6 alert alert-success">
                    {{ Session::get('success') }}
                </div>
                @endif
                @if( count($errors) > 0 )
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
                        <li style="width:20%;" class="active"><a id="ntab1" href="#tab1" data-toggle="tab" id="upload_tab"><i class="fa fa-user m-r-xs"></i>Select Video/Picture</a></li>
                        <li style="width:20%;"><a id="ntab2" href="#tab2" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Shipping Address</a></li>
                        <li style="width:20%;"><a id="ntab3" href="#tab3" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Personalize DVD</a></li>
                        <li style="width:20%;"><a id="ntab4" href="#tab4" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Order More DVDs</a></li>
                        <li style="width:20%;"><a id="ntab5" href="#tab5" data-toggle="tab"><i class="fa fa-truck m-r-xs"></i>Preview Order</a></li>
                    </ul>

                    <div class="progress progress-sm m-t-sm" style='height: 5px; margin-bottom: 10px;'>
                        <div class="progress-bar wizard-progress-bar progress-bar-primary" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        </div>
                    </div>

                    <div id="upload-progress-wrapper">
                        <h5></h5>
                        <p></p>
                        <div class="progress progress-sm m-t-sm">
                            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                        </div>
                    </div>

                    <form id="wizardForm">
                        <div class="tab-content no-padding">
                            <div class="tab-pane active fade in" id="tab1">
                                <div class="row m-b-lg">
                                    <div id="txt_cnt_files_selected" class="col-md-12">
                                        <h3>0 Out of {{ $dvd_per_month }} Media Spaces Uploaded</h3>
                                    </div>

                                    <div class="col-md-12 dropzone" id="dropzone">
                                        {{--<div class="dz-default dz-message"><span></span></div>--}}

                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab2">
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
                                                <input type="text" class="form-control" name="self_zipcode" id="self_zipcode" maxlength='5' placeholder="Zip"  value="{{ $user->zipcode }}">
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
                                            <input type="text" class="form-control" name="dvd_title" id="dvd_title" placeholder="DVD Title" onkeyup="return count_letter()" maxlength="30" required>

                                        </div>
                                        <div class="form-group col-md-12">
                                            <span style="color: green" id="lbl_left_letter">30 characters left available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab4">
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
                                                <input type="text" class="form-control" name="friend_zipcode" id="friend_zipcode" maxlength='5' placeholder="Zip"  value="">
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
                                        <div class="form-group col-md-12" onclick="askUserForPatience()">
                                            <button type="button" id="pay-now" class="btn btn-primary pull-right">Pay and Order</button>
                                            <input type="hidden" name="pay_status" id="pay_status" value="">
											<input type="hidden" name="uid" id="uid" value="{{ $user->id }}">
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

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Register Payment Information (Paypal or Credit card)</h4>
            </div>
            {{ Form::open( array('id' => 'braintree_form', 'class' => 'form-horizontal')) }}
            <div class="modal-body">

                <div id="payment-form"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Register</button>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>

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
                    <input type="number" min="1" max="500" class="form-control" name="edit_friend_quantity" id="edit_friend_quantity" placeholder="Quantity" value="1" required>
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
<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<!--<script src="https://js.braintreegateway.com/web/3.7.0/js/client.min.js"></script>-->
<!--<script src="https://sdk.amazonaws.com/js/aws-sdk-2.58.0.min.js"></script>-->
<script type="text/javascript">
<!--
	var uploadRequest = new Array();
	$.ajaxSetup({
	   headers: {
	       'X-CSRF-TOKEN': "{{ csrf_token() }}",
	   }
	});	
//-->
</script>

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
			console.log(value);
            var url = "check-promo";
            $.post(url, 
				{
                    'name': promocode, 
                    'value': value,
                    "_token": "{{ csrf_token() }}"
                },
				function (result) {
					console.log(result);
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
</script>
<script>
    var clientToken = "{{ $clientToken }}";
    braintree.setup(clientToken, "dropin", {
		container: "payment-form",
        paymentMethodNonceReceived: function (event, nonce) {
            $('#myModal').modal('hide');
            //waitingDialog.show('Please wait while registering payment method...');
            $.post('ajax-register-card',
				    { 
                        'payment_method_nonce': nonce,
                        "_token": "{{ csrf_token() }}"
                    }
                ).done(function (data) {
					//waitingDialog.hide();
					if (data.retcode == '200') {
					//ajax_pay();
						ajax_pay(myFiles, myFilesAddtext, file_counter);
					} else if (data.retcode == '201'){
						bootbox.alert(data.msg, function() { });
					}
					else {
						bootbox.alert(data.msg, function() { });
					}
				}).fail(function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                });
        }
    });
</script>
<script>
	var TOTAL_BOX_COUNT = <?php echo $dvd_per_month ?>;
	var SIZE_ONE_VIDEO = 80; /// 100Mbytes
	var g_additional_order = [];
	var g_cur_additional_order;
	var g_self_order;
	var g_mon_freedvd = {!! $user->mon_freedvd !!};
//          var g_mon_freedvd = 0;
	var g_mon_nextday = {!! $mon_nextday !!};
	var g_upload_started = false;
	var g_upload_finished = false;
	var edit_row = - 1;
	var myFiles = Array();
	var myFilesAddtext = Array();
	var file_counter = 0;
	var g_upload_status = '';
	var myXhr = null;
	var uploadTimeOut = null;
	/*function newUploadFunc() {
	 
	 if (g_upload_started == false) {
	 
	 g_upload_started = true;
	 upload_box(0);
	 }
	 
	 }*/
	
	function initMediaBoxText(){

		for (i = 0; i < TOTAL_BOX_COUNT ; i ++)
		{
			myFiles[i] = "";
			myFilesAddtext[i] = "";
		}

	}
	initMediaBoxText();

	function uploadFileCounter(){
		var curFileCount = 0;
		for (i = 0; i < TOTAL_BOX_COUNT ; i ++){
			if(myFiles[i] == "")
				continue;

			curFileCount ++;

		}
		
		return curFileCount;
	}

	function askUserForPatience () {
		
		clearTimeout(uploadTimeOut);

		if (document.getElementById("pay-now").disabled) {
			bootbox.alert("Please be patient while we are uploading your media spaces. Once the upload is complete, we will let you know so you can complete your order. Please do not exit or your files will not upload.", function() { });
		}

	}

	console.log('in-0');
	
	document.getElementById("pay-now").disabled = true;
	
	function get_empty_box() {
		var cnt = 0;
		for (var i = 0; i < TOTAL_BOX_COUNT; i++) {
			var id = '#box' + i;
			if ($(id).length && ($(id).val() == '' || $(id).val() == 'undefined')) {
				cnt += 1;
			}
		}
		return cnt;
	}

	function remove_box(remove_cnt) {
		var removed_cnt = 0;
		for (var i = TOTAL_BOX_COUNT - 1; i >= 0; i--) {
			var id = '#box' + i;
			if ($(id).val() == '') {
				$(id).parent().parent().remove();
				removed_cnt ++;

				if (removed_cnt == remove_cnt) {
					return true;
				}
			}
		}
		return false;
	}

	var rotation = {
	  1: 'rotate(0deg)',
	  3: 'rotate(180deg)',
	  6: 'rotate(90deg)',
	  8: 'rotate(270deg)'
	};

	function _arrayBufferToBase64( buffer ) {
	  var binary = ''
	  var bytes = new Uint8Array( buffer )
	  var len = bytes.byteLength;
	  for (var i = 0; i < len; i++) {
	    binary += String.fromCharCode( bytes[ i ] )
	  }
	  return window.btoa( binary );
	}
	
	var orientation = function(file, callback) {
	  var fileReader = new FileReader();
	  fileReader.onloadend = function() {
	    var base64img = "data:"+file.type+";base64," + _arrayBufferToBase64(fileReader.result);
	    var scanner = new DataView(fileReader.result);
	    var idx = 0;
	    var value = 1; // Non-rotated is the default
	    if(fileReader.result.length < 2 || scanner.getUint16(idx) != 0xFFD8) {
	      // Not a JPEG
	      if(callback) {
	        callback(base64img, value);
	      }
	      return;
	    }
	    idx += 2;
	    var maxBytes = scanner.byteLength;
	    while(idx < maxBytes - 2) {
	      var uint16 = scanner.getUint16(idx);
	      idx += 2;
	      switch(uint16) {
	        case 0xFFE1: // Start of EXIF
	          var exifLength = scanner.getUint16(idx);
	          maxBytes = exifLength - idx;
	          idx += 2;
	          break;
	        case 0x0112: // Orientation tag
	          // Read the value, its 6 bytes further out
	          // See page 102 at the following URL
	          // http://www.kodak.com/global/plugins/acrobat/en/service/digCam/exifStandard2.pdf
	          value = scanner.getUint16(idx + 6, false);
	          maxBytes = 0; // Stop scanning
	          break;
	      }
	    }
	    if(callback) {
	      callback(base64img, value);
	    }
	  }
	  fileReader.readAsArrayBuffer(file);
	};	
	
	function update_preview(input) {

		if (input.files && input.files[0]) {

			var ext = input.value.split('.').pop();
			ext = ext.toLowerCase();

			if (ext == 'jpg' || ext == 'jpeg' || ext == 'gif' || ext == 'png') {
				// var reader = new FileReader();
				// reader.onload = function (e) {

					//$(input).parent().find('img').attr('src', e.target.result);
					// loadImage.parseMetaData(e.target.result, function(data) {
					// 	    //default image orientation
					// 	    var orientation = 2;
					// 	    //if exif data available, update orientation
					// 	    if (data.exif) {
					// 	        orientation = data.exif.get('Orientation');
					// 	    }
					// 	    var loadingImage = loadImage(
					// 	        e.target.result,
					// 	        function(canvas) {
					// 	            //here's the base64 data result
					// 	            var base64data = canvas.toDataURL('image/jpeg');
					// 	            //here's example to show it as on imae preview
					// 	            var img_src = base64data.replace(/^data\:image\/\w+\;base64\,/, '');
					// 	            //$('#result-preview').attr('src', base64data);
					// 	            $(input).parent().find('img').attr('src', base64data);
					// 	        }, {
					// 	            //should be set to canvas : true to activate auto fix orientation
					// 	            canvas: true,
					// 	            orientation: orientation
					// 	        }
					// 	    );
					// });
					//console.log(input);
					

					
				// }

				// reader.readAsDataURL(input.files[0]);
				
				orientation(input.files[0], function(base64img, value) {
        			// $('#placeholder1').attr('src', base64img);
        			// $(input).parent().find('img').attr('src', base64img);
			        console.log(rotation[value]);
			        var rotated = $(input).parent().find('img').attr('src', base64img);
			        if(value) {
			          rotated.css('transform', rotation[value]);
			        }
    			});
				
			}
			else if (ext.toLowerCase() == 'wmv' 
			|| ext.toLowerCase() == 'mpg' 
			|| ext.toLowerCase() == 'avi' 
			|| ext.toLowerCase() == 'mp4' 
			|| ext.toLowerCase() == 'mov' 
			|| ext.toLowerCase() == 'mpeg' 
			|| ext.toLowerCase() == 'm4v' 
			|| ext.toLowerCase() == 'f4v' 
			|| ext.toLowerCase() == '3gp'  
			|| ext.toLowerCase() == 'mts' 
			|| ext.toLowerCase() == '3g2' ) {
				$(input).parent().find('img').attr('src', 'assets/frontend/images/video.jpg');
			}

			$(input).parent().parent().find('.dz-details').find('.dz-add-text').show();
			$(input).parent().parent().find('.dz-details').find('.dz-delete').show();


			var currentBoxnumString = $(input).parent().parent().find('.dz-details').find('.dz-add-text :input').attr("id");
			var currentBoxNum = currentBoxnumString.replace("add-text-", "");
			console.log("current change box number = " + currentBoxNum);
			var currentUploadRequestNumber = parseInt(currentBoxNum);
			
			console.log("upload Object2 = " + uploadRequest[currentUploadRequestNumber]);
			if(typeof uploadRequest[currentUploadRequestNumber] !== 'undefined' && uploadRequest[currentUploadRequestNumber] != null ){
				uploadRequest[currentUploadRequestNumber].abort();
				myFiles[currentUploadRequestNumber] = "";
				myFilesAddtext[currentUploadRequestNumber] = "";
				var addTextObjectIdStr = "#add-text-" + currentUploadRequestNumber;
				$(addTextObjectIdStr).val('');

			}

			upload_box(currentUploadRequestNumber);

		} else {
			$(input).parent().find('img').css('transform', '');
			$(input).parent().find('img').attr('src', 'assets/frontend/images/empty.png');
		}

		orderStatusCheck();
	}

	function calc_box_occupation(size_bytes) {
		var n = Math.ceil(size_bytes / (SIZE_ONE_VIDEO * 1024 * 1024));
		if ( n < 1 ) {
			n = 1;
		}      
		return n;
	}

	// window.URL = window.URL || window.webkitURL;
	// function getMediaboxCount(inputfile) {
	// 	var resolveFlag = false;
	// 	var returnFlag = false;
	// 	var deferred = $.Deferred();
		
	// 	var selectedCount = 0;
	// 	if ( isVideo(inputfile.name) ) {
	// 		var video = document.createElement('video');
	// 		video.preload = 'metadata';
	// 		video.onloadedmetadata = function() {
	// 			window.URL.revokeObjectURL(this.src)
	// 			var duration = video.duration;
	// 			console.log("video duration = " + duration);
	// 			var minutes = Math.floor(duration/60);
	// 			var seconds = duration % 60;
	// 			if ( minutes >= 1 ) {
	// 				selectedCount = minutes;
	// 				if ( seconds > 0 ) {
	// 					selectedCount += 1;
	// 				}
	// 				deferred.resolve(selectedCount);
	// 			} else {
	// 				if ( seconds > 0 ) {
	// 					selectedCount = 1;
	// 				}
					
	// 				deferred.resolve(selectedCount);
	// 			}
	// 		}
	// 		video.src = URL.createObjectURL(inputfile);
	// 		console.log("duration = " + video.duration);
	// 	} else {
	// 		selectedCount = 1;
	// 		deferred.resolve(selectedCount);
	// 	}
		
	// 	// while(returnFlag == false){
	// 	// 	customSleep(10);
	// 	// 	returnFlag = resolveFlag;
	// 	// }
	// 	return deferred.promise();
	// }
	
	// function getMediaboxCountCalc(inputfile) {
	// 	var resultCount = 0;
	// 	$.when( getMediaboxCount(inputfile) ).done(function( returnCount ) {
	// 		console.log("media Box = " + returnCount);
	// 		resultCount = returnCount;
	// 		//return returnCount;
	// 	});
	// }

	// function customSleep(milliseconds) {
	// 	var start = new Date().getTime();
	// 	for (var i = 0; i < 1e7; i++) {
	// 		if ((new Date().getTime() - start) > milliseconds){
	// 			break;
	// 		}
	// 	}
	// }

	function get_display_mediabox_cnt() {
		var cnt = 0;
		for (var i = 0; i < TOTAL_BOX_COUNT; i++) {
			var id = '#box' + i;
			if ($(id).length) {
				if ($(id).val() == '' || $(id).val() == 'undefined') {
					cnt ++;
				} else {
					cnt += calc_box_occupation($(id)[0].files[0].size);
				}
			}
		}
		return cnt;
	}

	function add_mediabox(cnt) {
		console.log("current media box = " + cnt)
		for (var i = 0; i < cnt; i++) {
			var t = 0;
			while (true) {
				var id = '#box' + t;
				if (!$(id).length) {
					break;
				}
				t ++;
			}
			var html = '';
			var new_box = '<div no="' + parseInt(t) + '" class="dz-preview dz-processing dz-error dz-complete dz-image-preview">' +
				'           <div class="dz-image div-media-box">' +
				'               <input type="file" onchange="input_media_box_onchanged(this);" accept="image/*,video/*" id="box' + t + '" name="box' + t + '" class="input-media-box" style="display:none">' +
				'               <img class="preview-media-box">' +
				'           </div>' +
				'           <div class="dz-details">' +
				'               <div class="dz-size">' +
				'                   <span data-dz-size=""></span>' +
				'               </div>' +
				'               <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>' +
				'               <div class="dz-filename">' +
				'                   <span data-dz-name=""></span>' +
				'               </div>' +
				'               <div class="add-media-box">' +
				'                   <button type="button" class="btn_add" onclick="btn_add_onclicked(this);">Add/Change</button>' +
				'               </div>' +
				'               <div class="dz-delete"><a class="btn_delete" onclick="btn_del_onclicked(this);"><img src="assets/frontend/images/delete.png"></a> </div>' +
				'               <div class="dz-add-text" id="dz-add-text-'+ parseInt(t) +'">' +
				'                   <input type="text" onchange="add_mediatext(this);" id="add-text-'+ parseInt(t) +'" name="add-text-'+ parseInt(t) +'" maxlength="15" placeholder="Add Caption Here!"/>' +
				'               </div>' +
				'           </div>' +
				'       </div>';
			html += new_box;
			$('#dropzone').append(html);
		}

		//bind_file_input();
	}

	function bind_file_input() {
		$('.btn_add').click(function() {
			$(this).parent().parent().parent().find('input.input-media-box').click();
		});
		$('.input-media-box').change(function() {

			if (this.files && this.files[0]) {
				var tInput = $(this);
				// update file name info
				$(this).parent().parent().find('.dz-details .dz-filename span').text(this.value.split('\\').pop());
				// update file size info

				var size_mbytes = this.files[0].size / (1024 * 1024);
				var cnt_box_occupation = calc_box_occupation(this.files[0].size);
				var cnt_empty_box = get_empty_box();
				if (cnt_box_occupation > 1) {
					if (cnt_empty_box < cnt_box_occupation - 1) {
						bootbox.alert("You have selected a media file that is too large for one media space. Please select a smaller file or delete a few existing media spaces in order for this one to upload.", function() {
							tInput.parent().parent().find('.div-media-box input').val('');
							tInput.parent().parent().find('.dz-filename span').text('Click Below');
							tInput.parent().parent().find('.dz-size span').text('');
							update_preview(tInput.parent().parent().find('.div-media-box input'));
							update_selected_media_count();
						});
						return false;
					} else {
						remove_box(cnt_box_occupation - 1);
					}
				}

				$(this).parent().parent().find('.dz-details .dz-size span').text(size_mbytes.toFixed(1) + ' MB');
				// update preview image
				update_preview(this);
			} else {
				// update file name info
				$(this).parent().parent().find('.dz-details .dz-filename span').text('Click Below');
				// update file size info
				$(this).parent().parent().find('.dz-details .dz-size span').text('');
				// update preview image
				update_preview(this);
			}

			update_selected_media_count();
		});

		$('.btn_delete').click(function() {

			if ($(this).parent().parent().parent().find('.div-media-box input')[0].files && $(this).parent().parent().parent().find('.div-media-box input')[0].files[0]) {

				var cnt_box_occupation = calc_box_occupation($(this).parent().parent().parent().find('.div-media-box input')[0].files[0].size);
				var i = $(this).parent().parent().parent().attr('no');
				$(this).parent().parent().parent().find('.div-media-box input').val('');
				$(this).parent().parent().find('.dz-filename span').text('Click Below');
				$(this).parent().parent().find('.dz-size span').text('');
				update_preview($(this).parent().parent().parent().find('.div-media-box input'));
				add_mediabox(cnt_box_occupation - 1);
				update_selected_media_count();
			}
		});
	}

	function update_selected_media_count() {

		var cnt_files_selected = calc_selected_media_count();
		var cnt_mediabox_selected = get_filled_mediabox_cnt();

		// $('#txt_cnt_files_selected').html('<h3>' + cnt_mediabox_selected + ' Out of ' + {{ $dvd_per_month }} + ' Media Spaces Uploaded ( ' + cnt_files_selected + ' files selected )</h3>'); 
		$('#txt_cnt_files_selected').html('<h3>' + cnt_mediabox_selected + ' Out of ' + {!! $dvd_per_month !!} + ' Media Spaces Uploaded </h3>');
	}

	function get_filled_mediabox_cnt() {
		var cnt = 0;
		for (var i = 0; i < TOTAL_BOX_COUNT; i++) {
			var id = '#box' + i;
			if ($(id).length) {
				if ($(id).val() == '' || $(id).val() == 'undefined') {
					continue;
				} else {
					cnt += calc_box_occupation($(id)[0].files[0].size);
				}
			}
		}
		return cnt;
	}

	function btn_add_onclicked(elem) {

		$(elem).parent().parent().parent().find('input.input-media-box').click();

	}

	function orderStatusCheck(){
		var countUploaded = calc_selected_media_count();
		console.log("countUploaded = " + countUploaded);
		if( g_upload_status == "completed" ){

			$("#btn_next").removeClass("disabled");
			$("#ntab2").removeClass("disabled");
			$("#ntab3").removeClass("disabled");
			$("#ntab4").removeClass("disabled");
			$("#ntab5").removeClass("disabled");

		} else {

			$("#btn_next").addClass("disabled");
			$("#ntab2").addClass("disabled");
			$("#ntab3").addClass("disabled");
			$("#ntab4").addClass("disabled");
			$("#ntab5").addClass("disabled");

		}
	}

	function btn_del_onclicked(elem) {
		console.log(elem);
		$(elem).parent().parent().find('.dz-add-text').hide();
		$(elem).parent().parent().find('.dz-delete').hide();
		var currentBoxnumString = $(elem).parent().parent().find('.dz-add-text :input').attr("id");
		var currentBoxNum = currentBoxnumString.replace("add-text-", "");
		console.log("current delete box number = " + currentBoxNum);
		var currentUploadRequestNumber = parseInt(currentBoxNum);

		console.log("UploadObject = " + uploadRequest[currentUploadRequestNumber]);
		if(typeof uploadRequest[currentUploadRequestNumber] !== 'undefined' && uploadRequest[currentUploadRequestNumber] !== null ){
			uploadRequest[currentUploadRequestNumber].abort();
			myFiles[currentUploadRequestNumber] = "";
			myFilesAddtext[currentUploadRequestNumber] = "";
			var addTextObjectIdStr = "#add-text-" + currentUploadRequestNumber;
			$(addTextObjectIdStr).val('');
		}

		if ($(elem).parent().parent().parent().find('.div-media-box input')[0].files && $(elem).parent().parent().parent().find('.div-media-box input')[0].files[0]) {


			//if (g_upload_status == "uploading"){
				//myXhr.abort();
			//	uploadRequest[currentUploadRequestNumber].abort();
			//}

			if ($(elem).parent().parent().parent().find('.div-media-box input')[0].hasAttribute('data-uploaded')){

				
				//$.ajax({   
				//	url:'/api/file-delete',
				//	type: 'POST',
				//	data:{ 'fileid': $(elem).parent().parent().parent().find('.div-media-box input')[0].getAttribute('data-uploaded') },
				//	success: function(e){
				//		console.log("File Successfully deleted!!");
				//	}
				//});

				var filekey = $(elem).parent().parent().parent().find('.div-media-box input')[0].getAttribute('data-uploaded');
				var params = {
				  Key: filekey
				};
				BurnVideobucket.deleteObject(params, function(err, data) {
				  if (err) {
					console.log(err, err.stack); // an error occurred
				  } else {
					console.log("File Successfully deleted!!" + JSON.stringify(data));           // successful response
				  }
				});
				
				$(elem).parent().parent().parent().find('.div-media-box input')[0].removeAttribute('data-uploaded');

			}

			var cnt_box_occupation = calc_box_occupation($(elem).parent().parent().parent().find('.div-media-box input')[0].files[0].size);

			var i = $(elem).parent().parent().parent().attr('no');
			$(elem).parent().parent().parent().find('.div-media-box input').val('');
			$(elem).parent().parent().find('.dz-filename span').text('Click Below');
			$(elem).parent().parent().find('.dz-size span').text('');
			update_preview($(elem).parent().parent().parent().find('.div-media-box input'));
			add_mediabox(cnt_box_occupation - 1);
			update_selected_media_count();
		}

		$(elem).parent().parent().find('div.dz-progress span').css('width', '0px');
		renderUploadProgress();
		orderStatusCheck();

	}

	function getExtension(filename) {
		var parts = filename.split('.');
		return parts[parts.length - 1];
	}

	function isVideo(filename) {
		var ext = getExtension(filename);
		switch (ext.toLowerCase()) {
		case 'm4v':
        case 'f4v':    
		case 'mov':
		case 'avi':
		case 'mpg':
		case 'mpeg':
		case 'mp4':
		case 'wmv':
		case '3gp':   
		case 'mts':
		case '3g2':
			// etc
			return true;
		}
		return false;
	}

	function add_mediatext(elem){
		var currentBoxnumString = $(elem).parent().parent().find('.dz-add-text :input').attr("id");
		var currentBoxNum = currentBoxnumString.replace("add-text-", "");
		console.log("current delete box number = " + currentBoxNum);
		var currentUploadRequestNumber = parseInt(currentBoxNum);
		
		var add_text_val = $(elem).val();
		if(add_text_val){
			myFilesAddtext[currentUploadRequestNumber] = add_text_val;
		}

	}

	function input_media_box_onchanged(elem) {

		console.log("file upload " + elem);

		$(elem).parent().parent().find('.dz-add-text').hide();
		$(elem).parent().parent().find('.dz-delete').hide();
		var currentBoxnumString = $(elem).parent().parent().find('.dz-add-text :input').attr("id");
		var currentBoxNum = currentBoxnumString.replace("add-text-", "");
		console.log("current delete box number = " + currentBoxNum);
		var currentUploadRequestNumber = parseInt(currentBoxNum);

		console.log("UploadObject = " + uploadRequest[currentUploadRequestNumber]);
		if(typeof uploadRequest[currentUploadRequestNumber] !== 'undefined' && uploadRequest[currentUploadRequestNumber] !== null ){
			uploadRequest[currentUploadRequestNumber].abort();
			myFiles[currentUploadRequestNumber] = "";
			myFilesAddtext[currentUploadRequestNumber] = "";
			var addTextObjectIdStr = "#add-text-" + currentUploadRequestNumber;
			$(addTextObjectIdStr).val('');

		}

		//if (g_upload_status == "uploading"){
			//myXhr.abort();
		//	uploadRequest[currentUploadRequestNumber].abort();
		//}

		if (elem.hasAttribute('data-uploaded')){

			//$.ajax({
			//	url:'burnvideo/public/file-delete',
			//	type: 'POST',
			//	data:{
			//		'fileid': elem.getAttribute('data-uploaded')
			//	},
			//	success: function(e){
			//		console.log("Old file Successfully deleted!!");
			//	}
			//});

			var filekey = $(elem).parent().parent().parent().find('.div-media-box input')[0].getAttribute('data-uploaded');
			var params = {
			  Key: filekey
			};
			BurnVideobucket.deleteObject(params, function(err, data) {
			  if (err) {
				console.log(err, err.stack); // an error occurred
			  } else {
				console.log("File Successfully deleted!!" + JSON.stringify(data));           // successful response
			  }
			});

			elem.removeAttribute('data-uploaded');
		}
		if (elem.files && elem.files[0]) {
			var tInput = $(elem);
			// update file name info
			$(elem).parent().parent().find('.dz-details .dz-filename span').text(elem.value.split('\\').pop());
			// update file size info

			var size_mbytes = elem.files[0].size / (1024 * 1024);
			console.log("file size = " + elem.files[0].size);

			var cnt_box_occupation = calc_box_occupation(elem.files[0].size);
			var cnt_empty_box = get_empty_box();

			if((elem.files[0].size <= 2048) || (isVideo(elem.files[0].name) && (elem.files[0].size <= 204800))){

				bootbox.alert('You have selected an invalid media file that is too small or no longer on your PC. Please choose another media file to continue with your order.', function () {
					tInput.parent().parent().find('.div-media-box input').val('');
					tInput.parent().parent().find('.dz-filename span').text('Click Below');
					tInput.parent().parent().find('.dz-size span').text('');
					update_preview(tInput.parent().parent().find('.div-media-box input'));
					update_selected_media_count();

				});
				return false;
			}
			if (cnt_box_occupation > 1) {
				if (cnt_empty_box < cnt_box_occupation - 1) {
					bootbox.alert("You have selected a media file that is too large for one media space. Please select a smaller file or delete a few existing media spaces in order for this one to upload.", function() {
						tInput.parent().parent().find('.div-media-box input').val('');
						tInput.parent().parent().find('.dz-filename span').text('Click Below');
						tInput.parent().parent().find('.dz-size span').text('');
						update_preview(tInput.parent().parent().find('.div-media-box input'));
						update_selected_media_count();
					});
					return false;
				} else {
					remove_box(cnt_box_occupation - 1);
				}
			}
			$(elem).parent().parent().find('.dz-details .dz-size span').text(size_mbytes.toFixed(1) + ' MB');
			// update preview image
			update_preview(elem);
		} else {
			// update file name info
			$(elem).parent().parent().find('.dz-details .dz-filename span').text('Click Below');
			// update file size info
			$(elem).parent().parent().find('.dz-details .dz-size span').text('');
			// update preview image
			update_preview(elem);
		}
		$(elem).parent().parent().find('.dz-details div.dz-progress span').css('width', '0px');
		update_selected_media_count();
		renderUploadProgress();
	}

	function generate_box() {

		var html = '';

		for (var i = 0; i < TOTAL_BOX_COUNT; i++) {
			var new_box = '<div no="' + i + '" class="dz-preview dz-processing dz-error dz-complete dz-image-preview">' +
				'           <div class="dz-image div-media-box">' +
				'               <input type="file" onchange="input_media_box_onchanged(this);" accept="image/*,video/*" id="box' + i + '" name="box' + i + '" class="input-media-box" style="display:none">' +
				'               <img class="preview-media-box">' +
				'           </div>' +
				'           <div class="dz-details">' +
				'               <div class="dz-size">' +
				'                   <span data-dz-size=""></span>' +
				'               </div>' +
				'               <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>' +
				'               <div class="dz-filename">' +
				'                   <span data-dz-name=""></span>' +
				'               </div>' +
				'               <div class="add-media-box">' +
				'                   <button type="button" class="btn_add" onclick="btn_add_onclicked(this);">Add/Change</button>' +
				'               </div>' +
				'               <div class="dz-delete"><a class="btn_delete" onclick="btn_del_onclicked(this);"><img src="assets/frontend/images/delete.png"></a> </div>' +
				'               <div class="dz-add-text" id="dz-add-text-'+ i +'">' +
				'                   <input type="text" onchange="add_mediatext(this);" id="add-text-'+ i +'" name="add-text-'+ i +'" maxlength="15" placeholder="Add Caption Here!"/>' +
				'               </div>' +
				'           </div>' +
				'       </div>';
			html += new_box;
		}
		$('#dropzone').empty();
		$('#dropzone').append(html);
		//bind_file_input();
	}

	function ajax_pay(file_id, addTxt_id, file_count) {

		console.log(file_count, file_id, addTxt_id);
		var uid = $("#uid").val();
		if (uid) {

			waitingDialog.show("Please wait while paying...");
			var filled_mediabox_cnt = get_filled_mediabox_cnt();

			$.post('ajax-pay',
				{
					'filled_mediabox_cnt': filled_mediabox_cnt,
					'self_order': g_self_order,
					'additional_order': g_additional_order,
					'promocode' : $("#add_promocode_order").val(),
					'dvd_title': $('#dvd_title').val(),
					'uid': uid,
					'f1': file_id[0],
					'f2': file_id[1],
					'f3': file_id[2],
					'f4': file_id[3],
					'f5': file_id[4],
					'f6': file_id[5],
					'f7': file_id[6],
					'f8': file_id[7],
					'f9': file_id[8],
					'f10': file_id[9],
					'f11': file_id[10],
					'f12': file_id[11],
					'f13': file_id[12],
					'f14': file_id[13],
					'f15': file_id[14],
					'f16': file_id[15],
					'f17': file_id[16],
					'f18': file_id[17],
					'f19': file_id[18],
					'f20': file_id[19],
					'f21': file_id[20],
					'f22': file_id[21],
					'f23': file_id[22],
					'f24': file_id[23],
					'f25': file_id[24],
					'f26': file_id[25],
					'f27': file_id[26],
					'f28': file_id[27],
					'f29': file_id[28],
					'f30': file_id[29],
					'f31': file_id[30],
					'f32': file_id[31],
					'f33': file_id[32],
					'f34': file_id[33],
					'f35': file_id[34],
					'f36': file_id[35],
					'f37': file_id[36],
					'f38': file_id[37],
					'f39': file_id[38],
					'f40': file_id[39],
					'f41': file_id[40],
					'f42': file_id[41],
					'f43': file_id[42],
					'f44': file_id[43],
					'f45': file_id[44],
					'f46': file_id[45],
					'f47': file_id[46],
					'f48': file_id[47],
					'f49': file_id[48],
					'f50': file_id[49],
					't1': addTxt_id[0],
					't2': addTxt_id[1],
					't3': addTxt_id[2],
					't4': addTxt_id[3],
					't5': addTxt_id[4],
					't6': addTxt_id[5],
					't7': addTxt_id[6],
					't8': addTxt_id[7],
					't9': addTxt_id[8],
					't10': addTxt_id[9],
					't11': addTxt_id[10],
					't12': addTxt_id[11],
					't13': addTxt_id[12],
					't14': addTxt_id[13],
					't15': addTxt_id[14],
					't16': addTxt_id[15],
					't17': addTxt_id[16],
					't18': addTxt_id[17],
					't19': addTxt_id[18],
					't20': addTxt_id[19],
					't21': addTxt_id[20],
					't22': addTxt_id[21],
					't23': addTxt_id[22],
					't24': addTxt_id[23],
					't25': addTxt_id[24],
					't26': addTxt_id[25],
					't27': addTxt_id[26],
					't28': addTxt_id[27],
					't29': addTxt_id[28],
					't30': addTxt_id[29],
					't31': addTxt_id[30],
					't32': addTxt_id[31],
					't33': addTxt_id[32],
					't34': addTxt_id[33],
					't35': addTxt_id[34],
					't36': addTxt_id[35],
					't37': addTxt_id[36],
					't38': addTxt_id[37],
					't39': addTxt_id[38],
					't40': addTxt_id[39],
					't41': addTxt_id[40],
					't42': addTxt_id[41],
					't43': addTxt_id[42],
					't44': addTxt_id[43],
					't45': addTxt_id[44],
					't46': addTxt_id[45],
					't47': addTxt_id[46],
					't48': addTxt_id[47],
					't49': addTxt_id[48],
					't50': addTxt_id[49],
					'file_counter' : file_counter,
                    "_token": "{{ csrf_token() }}"
			})
			.done(function (data) {
				console.log(data);
				waitingDialog.hide();
				if (data.retcode == '200') {
					bootbox.alert(data.msg, function() {
						//window.location.assign("http://burnvideo.net/");
						window.location.assign("/");
						//start file upload
						//$('#upload_tab').click();
						//g_upload_started = true;
						//upload_box(0, data.orderid);
					});
				} else if (data.retcode == '201') {
					//bootbox.alert('You didn\'t register payment method. Please register payment method and try again.', function() {
					//	location.href = 'register-card';
					//});
					$('#myModal').modal('show');
				} else if (data.retcode == '202') {
					$('#myModal').modal('show');
				} else {
					if ( data.msg ) {
						bootbox.alert(data.msg, function() {
							//window.location.assign("http://burnvideo.net/");
							window.location.assign("/");
						});
					} else {
						bootbox.alert("Sorry, Unkown error occur", function() {
							//window.location.assign("http://burnvideo.net/");
							window.location.assign("/");
						});
					}
				}
			})
			.fail(function(response) {
				console.log('Error: ' + response.responseText);
			});

		} else {
			bootbox.alert("Unkown User", function() {

				//window.location.assign("http://burnvideo.net/");
				window.location.assign("/");
			});
		}

	}

	function upload_box(i) {

		var userIdentify = $("#uid").val();
		var id = "input[name=box" + i + "]";
		if ($(id)[0] && $(id)[0].files[0] && !$(id)[0].hasAttribute('data-uploaded')) {

			var formData = new FormData();
			//formData.append("box", $(id)[0].files[0]);

				//formData.append("orderid", rnd);
			var file = $(id)[0].files[0];
			$("div#upload-progress-wrapper h5").html("Upload Progress:");
			$("div#upload-progress-wrapper .progress-bar").addClass('active');

			var uploadFileName = file.name;
			uploadFileName = uploadFileName.replace(/[&\/\\#,+()$~%'":*?<>{}]/g, "");
			uploadFileName = uploadFileName.replace(/\s/g,'');
			var filekey = userIdentify + "-frompc-box" + i + "-" + (new Date).getTime() + '-' + uploadFileName;

			var upload_url = "https://www.burnvideo.net/webupload/Containers/burunvideo/upload?uploadKey=" + filekey;
			formData.append("file", file);
			//formData.append("uploadKey", filekey);
			
			$.ajax({
				url: upload_url,
				data:formData,
				crossDomain: true,
				cache:false,
				xhr:function() {
					
					uploadRequest[i] = $.ajaxSettings.xhr();
					
					if (uploadRequest[i].upload) {

						uploadRequest[i].upload.addEventListener('progress', function(evt){
							console.log("Uploaded :: " + parseInt((evt.loaded * 100) / evt.total)+'%');

							actual_size = $(id)[0].files[0].size;

							$(id)[0].setAttribute('data-partial-upload', (evt.loaded > actual_size)? actual_size:evt.loaded);
							progressHandlingFunction(i, evt);
							renderUploadProgress();
						}, false);
						uploadRequest[i].addEventListener('abort', function(evt){
							g_upload_status = "paused";
							$("div#upload-progress-wrapper h5").html("Upload Progress ( Paused ):");
							$(id)[0].removeAttribute('data-partial-upload');
							$(id).parent().parent().find('span.dz-upload').css('width', '0px');
							renderUploadProgress();
							$("div#upload-progress-wrapper .progress-bar").removeClass('active');
						}, false);
						uploadRequest[i].onreadystatechange = function() {
							if (uploadRequest[i].readyState == 4 && uploadRequest[i].status == 200) {
								$(id)[0].removeAttribute('data-partial-upload');
								$("div#upload-progress-wrapper .progress-bar").removeClass('active');
							}
						};
					}
					return uploadRequest[i];
				},
				processData:false,
				contentType:false,
				type:'POST',
				success:function(resp){

					// var resultStatus = resp.retcode;
					
					// if ( resultStatus == 200 ){
						
					// 	var uploadedFileName = resp.fname;
					// 	$(id)[0].removeAttribute('data-partial-upload');
					// 	$("div#upload-progress-wrapper .progress-bar").removeClass('active');
						
					// 	$(id)[0].setAttribute('data-uploaded', uploadedFileName);
					// 	console.log("upload filekey = " + uploadedFileName);
					// 	myFiles[i] = uploadedFileName;
					// 	file_counter = uploadFileCounter();
					// 	renderUploadProgress();

					// } else {
					// 	upload_box(i);
					// }
					
					var result = resp.result.files.file[0];
					$(id)[0].removeAttribute('data-partial-upload');
					$("div#upload-progress-wrapper .progress-bar").removeClass('active');
					$(id)[0].setAttribute('data-uploaded', result.providerResponse.name);
					console.log("upload filekey = " + result.providerResponse.name);
					myFiles[i] = result.providerResponse.name;
					file_counter = uploadFileCounter();
					renderUploadProgress();
				},
		        error: function(data){
		            console.log(data);
		            upload_box(i);
		        }
			});
		}

	}

	function progressHandlingFunction(index, evt) {

		if (evt.lengthComputable) {
			var percentComplete = Math.round(evt.loaded * 100 / evt.total);
			$('#box' + index).parent().parent().find('.dz-details .dz-progress .dz-upload').css('width', percentComplete);
		} else {
			// Unable to compute progress information since the total size is unknown
			console.log(evt);
			console.log('unable to complete');
		}
	}



	$(document).ready(function() {

		count_letter();
		generate_box();
		$('#pay-now').click(function() {
			if (g_mon_freedvd == 0 && g_mon_nextday != 0) {
	//            bootbox.confirm("You are placing an additional order before your next due date. You will be charged $5.99 per DVD for this order. Do you wish to proceed?", function(result) {
	//                            bootbox.confirm("You are placing an additional order before your next due date. You will be charged $5.99 (minus the promo code) per DVD for this order. Do you wish to proceed?", function(result) {
	//                                if (result == true) {
				ajax_pay(myFiles, myFilesAddtext, file_counter);
	//                                }
	//                                else {
						//    location.href = 'order';
	//                                }
	//                            });
			} else {
				ajax_pay(myFiles, myFilesAddtext, file_counter);
			}
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
		if (friend_state == '' || friend_state == null || friend_state == "None") {
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
		var titleString = $('#dvd_title').val();
		var left_available = 30 - $.trim(titleString).length;
		$('#lbl_left_letter').text(left_available + ' characters left available');
		if (left_available < 0) {
			$('#dvd_title').val($('#dvd_title').val().slice(0, 30));
			$('#lbl_left_letter').text('0 characters left available');
			return false;
		}
		return true;
	}


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
		var curDVDCount = parseInt($('#edit_friend_quantity').val());
		var curStatesVal = $('#edit_friend_state').val();
		if ( (curDVDCount > 0) && curStatesVal !== 'None'){
			$("#col1_" + edit_row).text($('#edit_friend_quantity').val());
			$("#col2_" + edit_row).text($('#edit_friend_firstname').val());
			$("#col3_" + edit_row).text($('#edit_friend_lastname').val());
			$("#col4_" + edit_row).text($('#edit_friend_address').val());
			$("#col5_" + edit_row).text($('#edit_friend_city').val());
			$("#col6_" + edit_row).text($('#edit_friend_state').val());
			$("#col7_" + edit_row).text($('#edit_friend_zipcode').val());
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
			
		} else {
			if ( curDVDCount < 1 ) {

                bootbox.alert('Number of DVD should be input a number greater than 1.', function () {
                });
			}
			if ( curStatesVal === 'None' ) {
                bootbox.alert('You have not selected the Shipping State.', function () {
                });
			}
			return false;
		}
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
		if ($("#show_promocode").is(":visible") && $("#add_promocode_order").val().trim().length > 0){
			applyPromocodeFn();
		}
	}

	function renderUploadProgress(){
		var total_upload_size = 0;
		var total_uploaded_size = 0;
		var total_upload_file_count = 0;
		var total_file_uploaded_count = 0;
		$("input.input-media-box").each(function(idx){
			if (this.files[0]){
				total_upload_size += this.files[0].size;
				total_upload_file_count++;

				if (this.hasAttribute('data-uploaded')){
					total_file_uploaded_count++;
					total_uploaded_size += this.files[0].size;
				}

				if (this.hasAttribute('data-partial-upload')){
					total_uploaded_size += parseFloat(this.getAttribute('data-partial-upload'));
				}
			}
		});

		progress_text = "Uploaded " + total_file_uploaded_count + " of " + total_upload_file_count;
		if ((uploading_input = $("input.input-media-box[data-partial-upload]")).length > 0){
			console.log(uploading_input[0].files[0]);
			if( typeof uploading_input[0].files[0] === 'undefined' || uploading_input[0].files[0] == null ){
				progress_text += "  ";				
				console.log(progress_text);
			} else {
				progress_text += " (" + uploading_input[0].files[0].name + ") ";
				console.log(progress_text);
			}

		}

		$("#upload-progress-wrapper p").text(progress_text);

		console.log("total_uploaded_size = " + total_uploaded_size);
		console.log("total_upload_size = " + total_upload_size);

		percentComplete = (total_upload_size > 0)? ((total_uploaded_size / total_upload_size) * 100):0;
		progressbar = $("#upload-progress-wrapper div.progress-bar");
		progressbar.attr('aria-valuenow', parseFloat(percentComplete.toFixed(2)));
		progressbar.css('width', parseFloat(percentComplete) + '%');
		progressbar.text((percentComplete > 0)? (parseFloat(percentComplete.toFixed(2))) + '%':'');

		console.log("g_upload_status = " + g_upload_status)
		if(total_uploaded_size < total_upload_size){
			g_upload_status = "uploading";

		} else {
			g_upload_status = "completed";
		}

	}

</script>
{{ Html::script('assets/frontend/js/pages/form-wizard-order.js') }}
{{ Html::script('assets/frontend/js/pages/load-image.all.min.js') }}
@stop