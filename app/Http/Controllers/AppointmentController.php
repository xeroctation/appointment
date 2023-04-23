<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentNotification;
use App\Models\Appointment;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index()
    {
        $events = [];

        $appointments = Appointment::where('user_id', Auth::id())->get();

        foreach ($appointments as $appointment) {
            $events[] = [
                'id' => $appointment->id,
                'title' => $appointment->name .  ' - ' . ($appointment->service_provider_id ? $appointment->serviceProvider->name : ''),
                'start' => \Carbon\Carbon::parse($appointment->start_time)->format('Y-m-d H:i:s'),
                'end' => \Carbon\Carbon::parse($appointment->end_time)->format('Y-m-d H:i:s'),
            ];
        }

        return view('appointments.calendar', compact('events'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'name' => 'required|string'
        ]);

        $event = Appointment::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'user_id' => $userId,
            'service_provider_id' => $request->service_provider_id,
        ]);

        Mail::to($event->user->email)->send(new AppointmentNotification($event, 'created', $request->start_time, $request->end_time));

        return response()->json($event);
    }

    public function serviceProvider()
    {
        $service_providers = ServiceProvider::all();
        return response()->json($service_providers);
    }



    public function update(Request $request ,$id)
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
        return response()->json('Event updated');
    }

    public function delete(Request $request, $id)
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
