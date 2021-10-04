@extends('admin.layout')
@section('content')
<script>
   
    function onAddNotes( id )
    {   
        var title = $('#add_title').val();
        var day = $('#add_day').val();
        
        if( title.length == 0 )
        {
            showError("Please input notes", isAdd);
            return;
        }
        
        if( title.length > 30 )
        {
            showError("Please input notes in 1-30 letters", isAdd);
            return;
        }
        
        var url = "/admin/api_addCalendarEvent";
        $.post(url, {'title':title, 'start':day, 'end':day }, function(result){           
            
            if( result.status == 0 )
            {
                var event = {
                    title : title,
                    start : day,
                    end   : day
                };

                $('#calendar').fullCalendar( 'renderEvent', event, true )
                $('#add-modal').modal('hide');
            }
            else if( result.status == 1 )
            {
                showError("This notes is already added.", true);
                return;
            }
            else
            {
                showError("Fail add note.", true);
                return;
            }
        });
    }
    
    function onSubmitDelete(calEvent)
    {
        bootbox.confirm("Are you sure you want to delete notes?", function(result) {
            if( result)
            {
                var url = "/admin/api_deleteCalendarEvent";
                var startday = $.fullCalendar.formatDate( calEvent.start, "yyyy-MM-dd")
                
                $.post(url, {'title':calEvent.title, 'start':startday, 'end':startday  }, function(result){
                    if(result.status == 0)
                    {
                        $('#calendar').fullCalendar('removeEvents', calEvent._id);
                    }
                    else
                    {
                        $.notify("Fail delete notes", { position: "bottom center",  className: 'error' });
                    }
                });
            }
        });
    }
    
    
    $(function() {

                /* initialize the external events
                 -----------------------------------------------------------------*/
                function ini_events(ele) {
                    ele.each(function() {

                        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                        // it doesn't need to have a start or end
                        var eventObject = {
                            title: $.trim($(this).text()) // use the element's text as the event title
                        };

                        // store the Event Object in the DOM element so we can get to it later
                        $(this).data('eventObject', eventObject);

                        // make the event draggable using jQuery UI
                        $(this).draggable({
                            zIndex: 1070,
                            revert: true, // will cause the event to go back to its
                            revertDuration: 0  //  original position after the drag
                        });

                    });
                }
                ini_events($('#external-events div.external-event'));

                /* initialize the calendar
                 -----------------------------------------------------------------*/
                //Date for the calendar events (dummy data)
                var date = new Date();
                var d = date.getDate(),
                        m = date.getMonth(),
                        y = date.getFullYear();
                $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    buttonText: {//This is to add icons to the visible buttons
                        prev: "<span class='fa fa-caret-left'></span>",
                        next: "<span class='fa fa-caret-right'></span>",
                        today: 'today',
                        month: 'month',
                        week: 'week',
                        day: 'day'
                    },
                    //Random default events
                    events: [
                        @foreach($events as $idx=>$item)
                        {
                            title: '{{ $item->title }}',
                            start: '{{ $item->start }}',
                            end: '{{ $item->end }}',
                            backgroundColor: "#00a65a", //Success (green)
                            borderColor: "#00a65a" //Success (green)
                        },
                        @endforeach
                    ],
                    eventClick: function(calEvent, jsEvent, view) {
                        onSubmitDelete(calEvent);
                    },

                    dayClick: function(seldate, jsEvent, view) {
                        hideError();
                        $('#add_display_day').val($.fullCalendar.formatDate( seldate, "MM-dd-yyyy"));
                        $('#add_day').val($.fullCalendar.formatDate( seldate, "yyyy-MM-dd"));
			$('#add-modal').modal('show');
                        
                    },
                    editable: true,
                    droppable: true, // this allows things to be dropped onto the calendar !!!
                    
                    drop: function(date, allDay) { // this function is called when something is dropped

                        // retrieve the dropped element's stored Event Object
                        var originalEventObject = $(this).data('eventObject');

                        // we need to copy it, so that multiple events don't have a reference to the same object
                        var copiedEventObject = $.extend({}, originalEventObject);

                        // assign it the date that was reported
                        copiedEventObject.start = date;
                        copiedEventObject.allDay = allDay;
                        copiedEventObject.backgroundColor = $(this).css("background-color");
                        copiedEventObject.borderColor = $(this).css("border-color");

                        // render the event on the calendar
                        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                        // is the "remove after drop" checkbox checked?
                        if ($('#drop-remove').is(':checked')) {
                            // if so, remove the element from the "Draggable Events" list
                            $(this).remove();
                        }

                    }
                });

                /* ADDING EVENTS */
                var currColor = "#f56954"; //Red by default
                //Color chooser button
                var colorChooser = $("#color-chooser-btn");
                $("#color-chooser > li > a").click(function(e) {
                    e.preventDefault();
                    //Save color
                    currColor = $(this).css("color");
                    //Add color effect to button
                    colorChooser
                            .css({"background-color": currColor, "border-color": currColor})
                            .html($(this).text()+' <span class="caret"></span>');
                });
                $("#add-new-event").click(function(e) {
                    e.preventDefault();
                    //Get value and make sure it is not null
                    var val = $("#new-event").val();
                    if (val.length == 0) {
                        return;
                    }

                    //Create event
                    var event = $("<div />");
                    event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
                    event.html(val);
                    $('#external-events').prepend(event);

                    //Add draggable funtionality
                    ini_events(event);

                    //Remove event from text input
                    $("#new-event").val("");
                });
            });
        
</script>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Calendar
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-calendar"></i> Home</a></li>
                        <li class="active">Calendar</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Calendar</h3>                                    
                                </div><!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <!-- THE CALENDAR -->
                                    <div id="calendar"></div>
                                </div><!-- /.box-body -->				
                            </div><!-- /.box -->
                        </div>
                    </div>
                </section><!-- /.content -->    
                
                
                
                <!-- edit -->
                <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Add Notes</h4>
                            </div>
                            
                                <div class="modal-body">
                                    <table style="width: 100%">
                                        <tr>
                                            <td>
                                                 <label>Notes:</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                  <input id="add_title" name="add_title" class="form-control" ></input>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <label>Day:</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                 <input id="add_display_day" name="add_display_day" class="form-control" readonly>
                                                 <input id="add_day" name="add_day" class="form-control" type="hidden">
                                            </td>
                                        </tr>
                                    </table>                   
                                </div>
                                <div class="error">
                                    <span id="add_error" class="text-red">this is error</span>
                                </div>
                                <div class="modal-footer clearfix">
                                    <button id="add_cancel_btn" type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                    <button id="add_ok_btn" type="button" class="btn btn-primary pull-left" onclick="onAddNotes();">OK</button>
                                </div>
                            
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                
                
@stop