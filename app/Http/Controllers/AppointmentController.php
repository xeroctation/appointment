<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $events = [];

        $appointments = Appointment::where('user_id', Auth::id())->get();

        foreach ($appointments as $appointment) {
            $events[] = [
                'title' => $appointment->name,
                'start' => \Carbon\Carbon::parse($appointment->start_time)->format('Y-m-d H:i:s'),
                'end' => \Carbon\Carbon::parse($appointment->end_time)->format('Y-m-d H:i:s'),
            ];
        }

        return view('appointments.calendar', compact('events'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        switch ($request->type) {
            case 'create':
                $event = Appointment::create([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'user_id' => $userId,
                    'service_provider_id' => $request->service_provider_id,
                ]);

                return response()->json($event);
                break;

            case 'edit':
                $event = Appointment::where('id', $request->id)->update([
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'user_id' => $userId,
                    'service_provider_id' => $request->service_provider_id,
                ]);

                return response()->json($event);
                break;

            case 'delete':
                $event = Appointment::find($request->id)->delete();

                return response()->json($event);
                break;

            default:
                # ...
                break;
        }
    }
}
