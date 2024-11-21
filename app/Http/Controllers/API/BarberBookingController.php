<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BarberBookingController extends Controller
{
    public function allAppointments()
    {
        $user = Auth::user();
        $service_id = Service::where('user_id', $user->id)->pluck('id');
        $pending_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
            ->whereIn('service_id', $service_id)
            ->where('status', 'pending')->get();

        $accepted_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
            ->whereIn('service_id', $service_id)
            ->where('status', 'accepted')->get();

        $rejected_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
            ->whereIn('service_id', $service_id)
            ->where('status', 'rejected')->get();

        $completed_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
            ->whereIn('service_id', $service_id)
            ->where('status', 'completed')->get();
        return response()->json([
            'pending_appointments' => $pending_appointments,
            'accepted_appointments' => $accepted_appointments,
            'rejected_appointments' => $rejected_appointments,
            'completed_appointments' => $completed_appointments,
            'message' => 'all appointments ',
            'status' => 'success',
        ], 200);
    }
    // X--------------X---------X
    public function acceptOrReject(Request $request)
    {
        $user = Auth::user();
        $status = $request->status;
        $appointment = Appointment::find($request->appointment_id);
        if ($appointment) {
            $appointment->update(['status' => $status]);
            // /conditions
            if ($status == 'accepted') {
                $message = "Your appointment has been accepted.";
            } else {
                $message = "Your appointment has been rejected.";
            }
            // Send the notification
            $notificationData = [
                'user_id' => $user->id,
                'send_to' => $appointment->user_id,
                'message' => $message,
                'title' => 'Appointment ' . $status,
                'type' => "appointment",
                'redirect' => $appointment->id,
            ];
            // Create a new notification for the user
            UserNotification::create($notificationData);
            return response()->json([
                'message' => 'appointment updated successully.',
                'status' => 'success',
            ], 200);
        } else {
            return response()->json([
                'message' => 'appointment not found',
                'status' => 'error',
            ], 404);
        }
    }
    //X-------------X----------X
    public function markAsCompleted(Request $request)
    {
        $user = Auth::user();
        $appointment = Appointment::find($request->appointment_id);
        if ($appointment) {
            $appointment->update(['status' => 'completed']);
            return response()->json([
                'message' => 'appointment completed successully.',
                'status' => 'success',
            ], 200);
        } else {
            return response()->json([
                'message' => 'appointment not accepted so far or not available.',
                'status' => 'error',
            ], 404);
        }
    }
}
