<?php
namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database notification representation.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        // Fetch the sub-service names related to the appointment
        $subServiceNames = $this->appointment->AppointmentDetails->pluck('subService.name')->toArray();
        $subServiceNamesString = implode(' and ', $subServiceNames);

        // Create content for the notification
        return [
            'appointment_id' => $this->appointment->id,
            'user_id' => $this->appointment->user_id,
            'shop_name' => $this->appointment->shop_name,
            'address' => $this->appointment->address,
            'sub_services' => $subServiceNamesString,
            'price' => $this->appointment->price,
            'time' => $this->appointment->time,
            'date' => $this->appointment->date,
            'title' => 'Appointment Scheduled',
            'message' => "You have an appointment for $subServiceNamesString.",
            'type' => 'appointment',
            'redirect' => $this->appointment->id,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
         return [
            // 'message' => "You have an appointment for $subServiceNamesString.",
        ];
    }
}
