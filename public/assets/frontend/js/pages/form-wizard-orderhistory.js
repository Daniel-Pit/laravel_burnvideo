$(document).ready(function () {
    var $validator = $("#wizardForm").validate({
        rules: {
            self_quantity: {
                required: true,
                number: true
            },
            self_firstname: {
                required: true
            },
            self_lastname: {
                required: true
            },
            self_address: {
                required: true
            },
            self_city: {
                required: true
            },
            self_state: {
                required: true
            },
            self_zipcode: {
                required: true
            },
            dvd_title: {
                required: true
            },
        }
    });
    var currentIndex = 0
    $('#rootwizard').bootstrapWizard({
        'tabClass': 'nav nav-tabs',
        onTabShow: function (tab, navigation, index) {
            $('#btn_next').css('display', 'inherit');
            $("#btn_next").text('Next');
            if (index == 3) {
                console.log($("#btn_next"));
                $("#btn_next").text('Preview Order');
            }
            if (index == 4) {
                $('#btn_next').css('display', 'none');
                var sn = 1;
                var friend_quantity = $('#friend_quantity').val();
                var friend_firstname = $('#friend_firstname').val();
                var friend_lastname = $('#friend_lastname').val();
                var friend_address = $('#friend_address').val();
                var friend_city = $('#friend_city').val();
                var friend_state = $('#friend_state').val();
                var friend_zipcode = $('#friend_zipcode').val();

                var cnt_total_dvd = parseInt($('#self_quantity').val());
                g_self_order = {
                    count: $('#self_quantity').val(),
                    firstname: $('#self_firstname').val(),
                    lastname: $('#self_lastname').val(),
                    street: $('#self_address').val(),
                    city: $('#self_city').val(),
                    state: $('#self_state').val(),
                    zipcode: $('#self_zipcode').val()
                };

                $('#preview-order-tbody').empty();
                var tr = '<tr>' +
                        '       <th scope="row">' + sn + '</th>' +
                        '       <td>' + g_self_order.count + '</td>' +
                        '       <td>' + g_self_order.firstname + ' ' + g_self_order.lastname + '</td>' +
                        '           <td>' + g_self_order.street + '</td>' +
                        '           <td>' + g_self_order.city + '</td>' +
                        '           <td>' + g_self_order.state + '</td>' +
                        '           <td>' + g_self_order.zipcode + '</td>' +
                        '           <td>' + ' -- </td>' +
                        '         </tr>';
                $('#preview-order-tbody').append(tr);

                if (friend_quantity != '' && friend_firstname != '' && friend_lastname != '' && friend_address != null && friend_city != '' && friend_state != '' &&
                        friend_zipcode != '') {
                    g_cur_additional_order = {
                        count: friend_quantity,
                        firstname: friend_firstname,
                        lastname: friend_lastname,
                        street: friend_address,
                        city: friend_city,
                        state: friend_state,
                        zipcode: friend_zipcode
                    };

//                    cnt_total_dvd += parseInt(g_cur_additional_order.count);

//                    sn += 1;
                    var tr = '<tr id="row_0">' +
                            '       <th scope="row">' + sn + '</th>' +
                            '       <td id="col1_0">' + g_cur_additional_order.count + '</td>' +
                            '       <td id="col2_0">' + g_cur_additional_order.firstname + ' ' + g_cur_additional_order.lastname + '</td>' +
                            '           <td id="col3_0">' + g_cur_additional_order.street + '</td>' +
                            '           <td id="col4_0">' + g_cur_additional_order.city + '</td>' +
                            '           <td id="col5_0">' + g_cur_additional_order.state + '</td>' +
                            '           <td id="col6_0">' + g_cur_additional_order.zipcode + '</td>' +
                            '           <td>' + '<a data-toggle="tooltip" title="Edit" onclick="editOrderFn(0)" ><i class="fa fa-2x fa-edit"></i></a>' +
                            '<a  data-toggle="tooltip" title="Delete" onclick="removeOrderFn(0)" ><i class="fa fa-2x fa-trash"></i></a></td>' +
                            '         </tr>';
//                    $('#preview-order-tbody').append(tr);
                    g_additional_order.push(g_cur_additional_order);
                    $('#friend_quantity').val('1');
                    $('#friend_firstname').val('');
                    $('#friend_lastname').val('');
                    $('#friend_address').val('');
                    $('#friend_city').val('');
                    $('#friend_state').val('');
                    $('#friend_zipcode').val('');
                }

                for (var i = 0; i < g_additional_order.length; i++) {
                    if (g_additional_order[i]) {
                        cnt_total_dvd += parseInt(g_additional_order[i].count);
                        sn += 1;
                        var tr = '<tr id="row_' + i + '">' +
                                '       <th scope="row">' + sn + '</th>' +
                                '       <td id="col1_' + i + '">' + g_additional_order[i].count + '</td>' +
                                '       <td id="col2_' + i + '">' + g_additional_order[i].firstname + ' ' + g_additional_order[i].lastname + '</td>' +
                                '           <td id="col3_' + i + '">' + g_additional_order[i].street + '</td>' +
                                '           <td id="col4_' + i + '">' + g_additional_order[i].city + '</td>' +
                                '           <td id="col5_' + i + '">' + g_additional_order[i].state + '</td>' +
                                '           <td id="col6_' + i + '">' + g_additional_order[i].zipcode + '</td>' +
                                '           <td>' + '<a data-toggle="tooltip" title="Edit" onclick="editOrderFn(' + i + ')" ><i class="fa fa-2x fa-edit"></i></a>' +
                                '<a data-toggle="tooltip" title="Delete" onclick="removeOrderFn(' + i + ')" ><i class="fa fa-2x fa-trash"></i></a></td>' +
                                '         </tr>';
                        $('#preview-order-tbody').append(tr);
                    }
                }
                $('[data-toggle="tooltip"]').tooltip()
                $('#lbl_total_dvd').text(cnt_total_dvd);
                $('#lbl_total_purchase').text(((cnt_total_dvd - g_mon_freedvd) * 5.99).toFixed(2));
            }
            var $total = navigation.find('li').length;
            var $current = index + 1;
            var $percent = ($current / $total) * 100;
            $('#rootwizard').find('.progress-bar').css({width: $percent + '%'});
            currentIndex = index
        },
        'onPrevious': function (tab, navigation, index) {
            if (index == 3) {
//                $('#btn_next').css('display', 'inherit');
            }
        },
        'onNext': function (tab, navigation, index) {
            if (index == 1) {
                var cnt_order_selected = calc_selected_order();
                if (cnt_order_selected == 0) {
                    bootbox.alert('None of Order to reburn selected! Select one order history!', function () {
                    });
                    return false;
                }
                else if (cnt_order_selected > 1) {
                    bootbox.alert(cnt_order_selected + ' Order to reburn selected! Select only one order history!', function () {
                    });
                    return false;
                }
            }
            else if (index == 4) {

//                var sn = 1;
//                var friend_quantity = $('#friend_quantity').val();
//                var friend_firstname = $('#friend_firstname').val();
//                var friend_lastname = $('#friend_lastname').val();
//                var friend_address = $('#friend_address').val();
//                var friend_city = $('#friend_city').val();
//                var friend_state = $('#friend_state').val();
//                var friend_zipcode = $('#friend_zipcode').val();
//
//                var cnt_total_dvd = parseInt($('#self_quantity').val());
//                g_self_order = {
//                    count: $('#self_quantity').val(),
//                    firstname: $('#self_firstname').val(),
//                    lastname: $('#self_lastname').val(),
//                    street: $('#self_address').val(),
//                    city: $('#self_city').val(),
//                    state: $('#self_state').val(),
//                    zipcode: $('#self_zipcode').val()
//                };
//
//                $('#preview-order-tbody').empty();
//                var tr = '<tr>' +
//                        '       <th scope="row">' + sn + '</th>' +
//                        '       <td>' + g_self_order.count + '</td>' +
//                        '       <td>' + g_self_order.firstname + ' ' + g_self_order.lastname + '</td>' +
//                        '           <td>' + g_self_order.street + '</td>' +
//                        '           <td>' + g_self_order.city + '</td>' +
//                        '           <td>' + g_self_order.state + '</td>' +
//                        '           <td>' + g_self_order.zipcode + '</td>' +
//                        '         </tr>';
//                $('#preview-order-tbody').append(tr);
//
//                if (friend_quantity != '' && friend_firstname != '' && friend_lastname != '' && friend_address != null && friend_city != '' && friend_state != '' &&
//                        friend_zipcode != '') {
//                    g_cur_additional_order = {
//                        count: friend_quantity,
//                        firstname: friend_firstname,
//                        lastname: friend_lastname,
//                        street: friend_address,
//                        city: friend_city,
//                        state: friend_state,
//                        zipcode: friend_zipcode
//                    };
//
//                    cnt_total_dvd += parseInt(g_cur_additional_order.count);
//
//                    sn += 1;
//                    var tr = '<tr>' +
//                            '       <th scope="row">' + sn + '</th>' +
//                            '       <td>' + g_cur_additional_order.count + '</td>' +
//                            '       <td>' + g_cur_additional_order.firstname + ' ' + g_cur_additional_order.lastname + '</td>' +
//                            '           <td>' + g_cur_additional_order.street + '</td>' +
//                            '           <td>' + g_cur_additional_order.city + '</td>' +
//                            '           <td>' + g_cur_additional_order.state + '</td>' +
//                            '           <td>' + g_cur_additional_order.zipcode + '</td>' +
//                            '         </tr>';
//                    $('#preview-order-tbody').append(tr);
//                }
//
//                for (var i = 0; i < g_additional_order.length; i++) {
//                    cnt_total_dvd += parseInt(g_additional_order[i].count);
//                    sn += 1;
//                    var tr = '<tr>' +
//                            '       <th scope="row">' + sn + '</th>' +
//                            '       <td>' + g_additional_order[i].count + '</td>' +
//                            '       <td>' + g_additional_order[i].firstname + ' ' + g_additional_order[i].lastname + '</td>' +
//                            '           <td>' + g_additional_order[i].street + '</td>' +
//                            '           <td>' + g_additional_order[i].city + '</td>' +
//                            '           <td>' + g_additional_order[i].state + '</td>' +
//                            '           <td>' + g_additional_order[i].zipcode + '</td>' +
//                            '         </tr>';
//                    $('#preview-order-tbody').append(tr);
//                }
//
//                $('#lbl_total_dvd').text(cnt_total_dvd);
//                $('#lbl_total_purchase').text(((cnt_total_dvd - g_mon_freedvd) * 5.99).toFixed(2));

                // hide next button
//                $('#btn_next').css('display', 'none');
            }
            else if (index == 5) {
            }
            var $valid = $("#wizardForm").valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
        },
        'onTabClick': function (tab, navigation, index) {
            if (index == 0) {
                var cnt_order_selected = calc_selected_order();
                if (cnt_order_selected == 0) {
                    bootbox.alert('None of Order to reburn selected! Select one order history!', function () {
                    });
                    return false;
                }
                else if (cnt_order_selected > 1) {
                    bootbox.alert(cnt_order_selected + ' Order to reburn selected! Select only one order history!', function () {
                    });
                    return false;
                }
            }
            var $valid = $("#wizardForm").valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
//            if (index != 4) {
//                return false;
//            }
//            $('#btn_next').css('display', 'inherit');
        },
    });

    $('.date-picker').datepicker({
        orientation: "top auto",
        autoclose: true
    });
});