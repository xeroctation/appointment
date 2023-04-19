@extends('layouts.app')

@section('content')

    <div id="calendar"></div>
    <script>
        $(document).ready(function () {
            var SITEURL = "{{ url('/') }}";
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
@endsection
