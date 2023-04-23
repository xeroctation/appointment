<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $action;
    public $start_time;
    public $end_time;


    public function __construct(Appointment $appointment, $action, $start_time, $end_time)
    {
        $this->appointment = $appointment;
        $this->action = $action;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    public function build()
    {
        return $this->subject('Appointment ' . $this->action)
            ->view('emails.appointment-notification');
    }
}
