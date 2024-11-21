<?php

use App\Http\Controllers\API\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BarberController;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\BarberBookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function () {
    Route::post('register', [AuthenticationController::class, 'register']);
    Route::post('login', [AuthenticationController::class, 'login']);

    //forget password routes
    Route::post('forget-password', [AuthenticationController::class, 'ForgetPassword']);
    Route::post('confirm-pin', [AuthenticationController::class, 'ConfirmPIN']);
    Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);

    Route::middleware('auth:api')->group(function () {

        //barber routes
        //set up profile routes
        Route::post('add-details', [BarberController::class, 'addServiceWithDetails']);
        Route::post('home-screen', [BarberController::class, 'homeScreen']);
        //booking appointments api routes
        Route::get('get-barber-appointments', [BarberBookingController::class, 'allAppointments']);
        Route::post('get-user-appointment-detail', [BarberBookingController::class, 'getAppointmentDetails']);
        Route::post('accept-or-reject-appointment', [BarberBookingController::class, 'acceptOrReject']);
        Route::post('mark-as-completed', [BarberBookingController::class, 'markAsCompleted']);

        //barber profile api routes
        Route::post('edit-barber', [BarberController::class, 'editBarberProfile']);
        Route::post('/change-barber-password', [BarberController::class, 'changeBarberPassword']);
        Route::post('/edit-sub-service', [BarberController::class, 'editSubService']);

        Route::post('/add-service-images', [BarberController::class, 'addServiceImages']);
        Route::get('/get-service-images', [BarberController::class, 'getServiceImages']);




        //user routes
        Route::get('get-barbers', [UserController::class, 'allBarbers']);

        Route::post('/find-barber', [UserController::class, 'findBarber']);
        Route::post('/add-images', [UserController::class, 'addUserImages']);
        Route::post('make-appointment', [AppointmentController::class, 'makeAppointment']);
        Route::get('/get-user-appointments', [AppointmentController::class, 'getUserAppointments']);
        Route::post('/cancel-appointment', [AppointmentController::class, 'cancelAppointment']);
        Route::post('/rate-service', [AppointmentController::class, 'rateService']);
        //notfications routes for users
        Route::get('/get-user-notifications', [AppointmentController::class, 'getUserNotifications']);
        Route::post('/read-user-notification', [AppointmentController::class, 'readUserNotification']);

        //user profile api routes
        Route::get('/get-user', [UserController::class, 'getUser']);
        //edit user
        Route::post('/edit-user', [UserController::class, 'editUser']);
        //change password
        Route::post('/change-user-password', [UserController::class, 'changeUserPassword']);
    });
});
