<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Models\SubService;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\RatingService;
use App\Models\UserNotification;
use App\Models\AppointmentDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class AppointmentController extends Controller
{
    public function makeAppointment(Request $request)
    {
        $user = Auth::user();
        // $service = Service::find($service_id);
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'time' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }
        // Create the appointment record
        $service_id = $request->service_id;
        $service_data = Service::find($service_id);
        $appointment = Appointment::create([
            'user_id' => $user->id,
            'service_id' => $service_id,
            'amount' => 0,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);
        $total_amount = 0;
        $sub_services = $request->sub_services;
        //as array of ids are cmoing so we will loop each id and get data to calculat price
        foreach ($sub_services as $sub_service_id) {
            $sub_service_data = SubService::find($sub_service_id);
            if ($sub_service_data) {
                $total_amount += $sub_service_data->price;

                // Create AppointmentDetail for each sub_service
                AppointmentDetail::create([
                    'appointment_id' => $appointment->id,
                    'sub_service_id' => $sub_service_id,
                    'status' => 'pending',
                ]);
            }
        }
        $appointment->update(['amount' => $total_amount]);
        if ($appointment && $user) {
            $message = "You have new appointment scheduled.";
            $send_to = $service_data->user_id;

            // $data_array = [
            //     'user' => $user->name,
            //     'title' => 'Appointment Scheduled',
            //     'body' => $message,
            //     'time' => $appointment->created_at->format('H:i'),
            // ];
            // $user->sendNotification($user, $data_array, $message);
            // Store notifications in `user_notifications` table for users
            $notificationData = [
                'user_id' => $user->id,
                'send_to' => $send_to,
                'message' => $message,
                'title' => 'New Appointment',
                'type' => "appointment",
                'redirect' => $appointment->id,
            ];
            UserNotification::create($notificationData);
        }
        $data = Appointment::with(['AppointmentDetails'])->find($appointment->id);
        return response()->json([
            'data' => $data,
            'message' => 'Appointment created successfully.',
            'status' => 'success',
        ], 201);
    }
    //X------------X-----------X
    public function getUserAppointments()
    {
        $user = Auth::user();
        $pending_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
        ->where('user_id', $user->id)
        ->where('status', 'pending')->get();

        $accepted_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
        ->where('user_id', $user->id)
        ->where('status', 'accepted')->get();

        $rejected_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
        ->where('user_id', $user->id)
        ->where('status', 'rejected')->get();

        $completed_appointments = Appointment::with(['service', 'AppointmentDetails.subService'])
        ->where('user_id', $user->id)
        ->where('status', 'completed')->get();

            return response()->json([
                'pending_appointments' =>$pending_appointments,
                'accepted_appointments' =>$accepted_appointments,
                'rejected_appointments' =>$rejected_appointments,
                'completed_appointments' =>$completed_appointments,
                'message' => 'all appointments ',
                'status' => 'success',
            ], 200);
    }
    //X-------------X-----------X
    public function cancelAppointment(Request $request)
    {
        $user = Auth::user();
        $appointment_id = $request->appointment_id;
        $status= $request->status;
        if ($appointment_id) {
            // get the appointment
            $appointment = Appointment::where('id', $appointment_id);
            //update status
            $appointment->update($status);
            return response()->json([
                'status' => 'success',
                'message' => 'Appointment cancelled successfully.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'appointment not found,',
                'status' => 'error',
            ], 404);
        }
    }
    //X-----------X---------------X
    public function rateService(Request $request)
    {
        //when a sevice is completed user will rate barber service.
        $user = Auth::user();
        $input = $request->all();
        $appointment_id = $request->appointment_id;
        $validator = Validator::make($input, [
            'rating' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 'error',
            ]);
        }
        // get the appointment
        $appointment = Appointment::where('id', $appointment_id);
        if ($appointment) {
            $rating_service = RatingService::create([
                'user_id' => $user->id,
                'service_id' => $appointment->service_id,
                'appointment_id' => $appointment_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'appointment might not be completed so far.',
            ], 404);
        }
        $data = RatingService::find($rating_service->id);
        return response()->json([
            'appointment' => $data,
            'status' => 'success',
            'message' => 'service rated successfully.',
        ], 200);
    }
    //x------------x------------x
    //user notifications
    public function getUserNotifications()
    {
        $user = Auth::user();
        $notifications = UserNotification::where('send_to', $user->id)->get();
        if ($notifications) {
            return response()->json([
                'notifications' => $notifications,
                'message' => 'notifications retrieved successfull.',
                'status' => 'success',
            ], 200);
        } else {
            return response()->json([
                'message' => 'No notifications found.',
                'status' => 'error',
            ], 404);
        }
    }
    //X------X----------------X
    public function readUserNotification(Request $request)
    {
        $user = Auth::user();

        $notification_id = $request->notification_id;
        $notification = UserNotification::where('id', $notification_id)
            ->where('send_to', $user->id)
            ->first();
        if ($notification) {
            $notification->update(['status' => 1]);
            return response()->json([
                'data' => $notification,
                'message' => 'you have read the notification.',
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'message' => 'notification not found or already read.',
                'status' => 'error'
            ], 404);
        }
    }
}
