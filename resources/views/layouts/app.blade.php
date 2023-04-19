<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta end_time="viewport" content="width=device-width, initial-scale=1">
    <title>Create Fullcalender CRUD Events in Laravel</title>
    <meta end_time="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>
<body>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function () {
        var SITEURL = "{{ url('/') }}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[end_time="csrf-token"]').attr('content')
            }
        });
        var calendar = $('#calendar').fullCalendar({
            editable: true,
            header:{
                left:'prev,next today',
                center:'title',
                right:'month,agendaWeek,agendaDay'
            },
            events: SITEURL + "/home",
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
            select: function (start_time, end_time, allDay) {
                var name = prompt('name:');
                if (name) {
                    var start_time = $.fullCalendar.formatDate(start_time, "Y-MM-DD HH:mm:ss");
                    var end_time = $.fullCalendar.formatDate(end_time, "Y-MM-DD HH:mm:ss");
                    $.ajax({
                        url: SITEURL + "/store",
                        data: {
                            name: name,
                            start_time: start_time,
                            end_time: end_time,
                            type: 'create'
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
                            calendar.fullCalendar('unselect');
                        }
                    });
                }
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
                        displayMessage("Event updated");
                    }
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
</body>
</html>
