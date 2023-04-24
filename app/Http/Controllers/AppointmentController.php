<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentNotification;
use App\Models\Appointment;
use App\Models\ServiceProvider;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        /** @var object $user*/
        $user = auth()->user();
        $events = $this->service->index($user);

        return view('appointments.calendar', compact('events'));
    }

    public function store(Request $request)
    {
        /** @var object $user*/
        $user = auth()->user();
        $event = $this->service->store($request, $user);

        return response()->json($event);
    }

    public function update(Request $request ,$id)
    {
        $event = $this->service->update($request, $id);
        return response()->json($event);
    }

    public function delete(Request $request, $id)
    {
        return $this->service->delete($request, $id);
    }

    public function serviceProvider()
    {
        $service_providers = ServiceProvider::all();
        return response()->json($service_providers);
    }
}
