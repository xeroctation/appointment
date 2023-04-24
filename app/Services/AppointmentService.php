<?php

namespace App\Services;

use App\Mail\AppointmentNotification;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentService
{
    public function index(object $user){
        $events = [];

        $appointments = Appointment::where('user_id', $user->id)->get();

        foreach ($appointments as $appointment) {
            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->name .  ' - ' . ($appointment->service_provider_id ? $appointment->serviceProvider->name : ''),
                'start' => \Carbon\Carbon::parse($appointment->start_time)->format('Y-m-d H:i:s'),
                'end' => \Carbon\Carbon::parse($appointment->end_time)->format('Y-m-d H:i:s'),
            ];
        }

        return $events;
    }

    public function store(object $request, object $user)
    {
        $request->validate([
            'name' => 'required|string',
            'service_provider_id' => 'required'
        ]);

        $event = Appointment::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'user_id' => $user->id,
            'service_provider_id' => $request->service_provider_id,
        ]);

        Mail::to($event->user->email)->send(new AppointmentNotification($event, 'created', $request->start_time, $request->end_time));

        return $event;
    }

    public function update(object $request, int $id)
    {
        $event = Appointment::find($id);
        if(! $event) {
            return response()->json([
                'error' => 'Unable to locate the event'
            ], 404);
        }
        $event->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);
        return 'Event updated';
    }

    public function delete(object $request, int $id)
    {
        $event = Appointment::find($id);
        if(! $event) {
            return response()->json([
                'error' => 'Unable to locate the event'
            ], 404);
        }
        $event->delete();

        Mail::to($event->user->email)->send(new AppointmentNotification($event, 'deleted', $request->start_time, $request->end_time));

        return $id;
    }


}
