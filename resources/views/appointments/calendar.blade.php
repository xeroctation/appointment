@extends('layouts.app')

@section('content')

    <div id="calendar"></div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enter appointment name</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="text" id="name" class="form-control" placeholder="Appointment name">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="saveAppointment" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var SITEURL = "{{ url('/') }}";
            var start_time, end_time;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var calendar = $('#calendar').fullCalendar({
                editable: true,
                header:{
                    left:'prev,next today',
                    center:'title',
                    right:'month,agendaWeek,agendaDay'
                },
                events: {!! json_encode($events) !!},
                displayEventTime: true,
                eventRender: function (event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }
                },
                selectable: true,
                selectHelper: true,

                select: function (start, end, allDay) {
                    $('#myModal').modal('show');
                    start_time = start;
                    end_time = end;
                    $('#saveAppointment').click(function() {
                        var name = $('#name').val();
                        if (name) {
                            $.ajax({
                                url: SITEURL + "/store",
                                data: {
                                    name: name,
                                    start_time: start_time.format("Y-MM-DD HH:mm:ss"),
                                    end_time: end_time.format("Y-MM-DD HH:mm:ss"),
                                    type: 'create',
                                },
                                type: "POST",
                                success: function (data) {
                                    displayMessage("Event created.");
                                    calendar.fullCalendar('renderEvent', {
                                        id: data.id,
                                        title: name,
                                        start: start_time,
                                        end: end_time,
                                        allDay: allDay
                                    }, true);
                                    calendar.fullCalendar("unselect");
                                }
                            });
                        }
                        $("#myModal").find("input").val("");
                        $('#myModal').modal('hide');
                    });
                },

                eventDrop: function (event, delta) {
                    var start_time = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    var end_time = $.fullCalendar.formatDate(event.end, "Y-MM-DD");
                    $.ajax({
                        url: SITEURL + '/store',
                        data: {
                            title: event.name,
                            start: start_time,
                            end: end_time,
                            id: event.id,
                            type: 'edit'
                        },
                        type: "POST",
                        success: function (response) {
                            console.log(response);
                            console.log(delta)
                            displayMessage("Event updated");
                        },
                        error:function(error)
                        {
                            console.log(error)
                        },
                    });
                },
                eventClick: function (event) {
                    var eventDelete = confirm("Are you sure?");
                    if (eventDelete) {
                        $.ajax({
                            type: "POST",
                            url: SITEURL + '/store',
                            data: {
                                id: event.id,
                                type: 'delete'
                            },
                            success: function (response) {
                                calendar.fullCalendar('removeEvents', event.id);
                                displayMessage("Event removed");

                            }
                        });
                    }
                }
            });
        });
        function displayMessage(message) {
            toastr.success(message, 'Event');
        }
    </script>
@endsection
