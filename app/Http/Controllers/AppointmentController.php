<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $events = [];

        $appointments = Appointment::all();

        foreach ($appointments as $appointment) {
            $events[] = [
                'title' => $appointment->name,
                'start' => \Carbon\Carbon::parse($appointment->start_time)->format('Y-m-d H:i:s'),
                'end' => \Carbon\Carbon::parse($appointment->end_time)->format('Y-m-d H:i:s'),
            ];
        }


        return view('appointments.home', compact('events'));
    }

    public function store(Request $request)
    {
        switch ($request->type) {
            case 'create':
                $event = Appointment::create([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);

                return response()->json($event);
                break;

            case 'edit':
                $event = Appointment::find($request->id)->update([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
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
