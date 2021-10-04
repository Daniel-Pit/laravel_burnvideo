$(document).ready(function() {
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
 
    $('#rootwizard').bootstrapWizard({
        'tabClass': 'nav nav-tabs',
        onTabShow: function(tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index+1;
            var $percent = ($current/$total) * 100;
            $('#rootwizard').find('.progress-bar').css({width:$percent+'%'});
        },
        'onPrevious': function(tab, navigation, index) {
            if(index == 3) {
                $('#btn_next').css('display', 'inherit');
            }
        },
        'onNext': function(tab, navigation, index) {
            if(index == 1) {
                var cnt_files_selected = calc_selected_media_count();
                if(cnt_files_selected == 0) {
                    bootbox.alert('None of video/picture files selected! Select at least one video/picture file!', function() {
                    });
                    return false;
                }
            }
            else if(index == 4) {

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
                    '         </tr>';
                $('#preview-order-tbody').append(tr);

                if(friend_quantity != '' && friend_firstname != '' && friend_lastname != '' && friend_address != null && friend_city != '' && friend_state != '' &&
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

                    cnt_total_dvd += parseInt(g_cur_additional_order.count);

                    sn += 1;
                    var tr = '<tr>' +
                        '       <th scope="row">' + sn + '</th>' +
                        '       <td>' + g_cur_additional_order.count + '</td>' +
                        '       <td>' + g_cur_additional_order.firstname + ' ' + g_cur_additional_order.lastname + '</td>' +
                        '           <td>' + g_cur_additional_order.street + '</td>' +
                        '           <td>' + g_cur_additional_order.city + '</td>' +
                        '           <td>' + g_cur_additional_order.state + '</td>' +
                        '           <td>' + g_cur_additional_order.zipcode + '</td>' +
                        '         </tr>';
                    $('#preview-order-tbody').append(tr);
                }

                for(var i=0; i<g_additional_order.length; i++) {
                    cnt_total_dvd += parseInt(g_additional_order[i].count);
                    sn += 1;
                    var tr = '<tr>' +
                        '       <th scope="row">' + sn + '</th>' +
                        '       <td>' + g_additional_order[i].count + '</td>' +
                        '       <td>' + g_additional_order[i].firstname + ' ' + g_additional_order[i].lastname + '</td>' +
                    '           <td>' + g_additional_order[i].street + '</td>' +
                    '           <td>' + g_additional_order[i].city + '</td>' +
                    '           <td>' + g_additional_order[i].state + '</td>' +
                    '           <td>' + g_additional_order[i].zipcode + '</td>' +
                    '         </tr>';
                    $('#preview-order-tbody').append(tr);
                }

                $('#lbl_total_dvd').text(cnt_total_dvd);
                $('#lbl_total_purchase').text(((cnt_total_dvd - g_mon_freedvd) * 5.99).toFixed(2));

                // hide next button
                $('#btn_next').css('display', 'none');
            }
            else if(index == 5) {
            }
            var $valid = $("#wizardForm").valid();
            if(!$valid) {
                $validator.focusInvalid();
                return false;
            }
        },
        'onTabClick': function(tab, navigation, index) {
            if(index != 4) {
                return false;
            }
            $('#btn_next').css('display', 'inherit');
        },
    });
    
    $('.date-picker').datepicker({
        orientation: "top auto",
        autoclose: true
    });
});