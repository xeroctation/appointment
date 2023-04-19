<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

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


        return view('home', compact('events'));
    }

//    public function index(){
//        return view('home');
//    }
}
