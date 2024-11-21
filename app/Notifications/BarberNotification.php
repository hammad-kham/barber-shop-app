<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BarberNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Specify the notification delivery channels.
     */
    public function via($notifiable)
    {
        return ['database']; // Store notification in database
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        // Prepare notification data
        $subServiceNames = $this->appointment->appointmentDetails->pluck('subService.name')->implode(' and ');

        $user = $this->appointment->user;

        return [
            'appointment_id' => $this->appointment->id,
            'user_id' => $this->appointment->user_id,
            'user_name' => $user->name,
            'user_image' => $user->image,
            'sub_services' => $subServiceNames,
            'price' => $this->appointment->amount,
            'time' => $this->appointment->time,
            'date' => $this->appointment->date,
            'title' => 'New Appointment Booked',
            'message' => "A new appointment has been booked for $subServiceNames.",
            'type' => 'appointment',
            'redirect' => $this->appointment->id,
        ];
    }
}
