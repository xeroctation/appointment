@extends('layouts.app')

@section('content')

    <div id="calendar"></div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enter appointment details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="text" id="name" class="form-control" placeholder="Appointment name">
                    <select id="service_provider_id" name="service_provider_id" class="form-control">
                        <option></option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancelAppointment" data-dismiss="modal">Cancel</button>
                    <button type="button" id="saveAppointment" class="btn btn-primary" disabled>Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            $('#name, #service_provider_id').on('input', function() {
                if ($('#name').val() && $('#service_provider_id').val()) {
                    $('#saveAppointment').removeAttr('disabled');
                } else {
                    $('#saveAppointment').attr('disabled', 'disabled');
                }
            });

            var start_time, end_time;
            var service_provider_id;
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
                hiddenDays: [ 6, 7 ],
                timeFormat: 'H:mm',
                slotLabelFormat:"HH:mm",
                theme: 'jquery-ui',
                minTime: "09:00:00",
                maxTime: "19:00:00",
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
                    $('#cancelAppointment').click(function() {
                        $('#myModal').modal('hide');
                    }),
                    $.ajax({
                        url: "{{ route('service_provider') }}",
                        type: "GET",
                        success: function (data) {
                            $('#service_provider_id').empty();
                            $.each(data, function(index, service_provider) {
                                $('#service_provider_id').append('<option value="' + service_provider.id + '">' + service_provider.name + '</option>');
                            });
                            $('#service_provider_id').prepend('<option selected=""></option>').select2({
                                placeholder: "Select service provider",
                                allowClear: true,
                                dropdownParent: "#myModal"
                            });
                            $('#service_provider_id').on('change', function() {
                                service_provider_id = $(this).val();
                                console.log(service_provider_id);
                            });
                            service_provider_id = $('#service_provider_id').val();
                            console.log(service_provider_id);
                        }
                    });

                    $('#saveAppointment').click(function() {
                        var name = $('#name').val();
                        if (name) {
                            $.ajax({
                                url: "{{ route('calendar.store') }}",
                                data: {
                                    name: name,
                                    start_time: start_time.format("Y-MM-DD HH:mm:ss"),
                                    end_time: end_time.format("Y-MM-DD HH:mm:ss"),
                                    service_provider_id: service_provider_id,
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
                    var id = event.id;
                    var start_time = moment(event.start).format('YYYY-MM-DD');
                    var end_time = moment(event.end).format('YYYY-MM-DD');

                    $.ajax({
                        url: "{{ route('calendar.update', '') }}" +'/'+ id,
                        type:"PATCH",
                        dataType:'json',
                        data: {
                            start_time,
                            end_time,
                        },
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
                    var eventDelete = confirm("Are you sure to delete?");
                    var id = event.id;

                    if (eventDelete) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('calendar.delete', '') }}" +'/'+ id,
                            dataType:'json',
                            success: function (response) {
                                calendar.fullCalendar('removeEvents', response);
                                console.log(response);
                                displayMessage("Event removed");
                            },
                            error:function(error)
                            {
                                console.log(error)
                            },
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
