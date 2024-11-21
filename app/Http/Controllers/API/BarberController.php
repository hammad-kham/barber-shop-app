<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Models\SubService;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\RatingService;
use App\Models\ServicesImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BarberController extends Controller
{
    //method to add barber initial info
    public function addServiceWithDetails(Request $request)
    {
        $user = Auth::user();
        $service_input = $request->except('sub_services', 'images');
        $service_input['user_id'] = $user->id;
        if ($user->type === 'barber') {
            // Validate and create the Service
            $validatorService = Validator::make($service_input, [
                'shop_name' => 'required',
                'time_open_close' => 'required'
            ]);
            if ($validatorService->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validatorService->errors(),
                ], 422);
            }
            // Create the sub services
            $service = Service::create($service_input);
            if ($service) {
                $sub_services = $request->sub_services;
                if (!empty($sub_services)) {
                    foreach ($sub_services as $sub_service) {
                        //foreach sub_service validate and create
                        $validatorSubService = Validator::make($sub_service, [
                            'name' => 'required',
                            'price' => 'required',
                            'time' => 'required',
                        ]);
                        if ($validatorSubService->fails()) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $validatorSubService->errors(),
                            ], 422);
                        }
                        // Create the sub-service linked to the main service and user ID
                        SubService::create([
                            'service_id' => $service->id,
                            'user_id' => $user->id,
                            'name' => $sub_service['name'],
                            'price' => $sub_service['price'],
                            'time' => $sub_service['time'],
                            'description' => $sub_service['description'],
                        ]);
                    }
                }
                // Check if the 'images' field contains files
                $images = $request->file('images');
                if ($images) {
                    foreach ($images as $image) {
                        // Generate a unique filename and store the image in the desired directory
                        $filename = time() . '_' . '.' . $image->extension();
                        $image->move(public_path('/assets/images/services'), $filename);
                        // Add the image path to the array
                        ServicesImage::create([
                            'user_id' => $user->id,
                            'service_id' => $service->id,
                            'image' => $filename,
                        ]);
                    }
                }
                $data = Service::with(['subServices', 'services_images'])->find($service->id);
                return response()->json([
                    'data' => $data,
                    'message' => 'Service and details added successfully!',
                    'status' => 'success',
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'pls add shop details before add further details.',
                ], 422);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be a barber to add details.',
            ], 403);
        }
    }
    //X--------------X--------------X
    //home screen methods
    public function homeScreen(Request $request)
    {
        $user = Auth::user();
        $month = $request->month;
        //get total appointments
        if ($user->type == 'barber') {
            $service_id = Service::where('user_id', $user->id)->pluck('id');
            $count_appointments = Appointment::where('status', 'completed')
                ->whereIn('service_id', $service_id)->count();

            //get all customers of a specific service
            $count_customers = Appointment::whereIn('service_id', $service_id)
                ->distinct('user_id')
                ->count();

            //sum all appointments price that are completed
            $sum_amount = Appointment::whereIn('service_id', $service_id)
                ->where('status', 'completed')->sum('amount');

            //sale of month
            $sale_of_month = Appointment::whereIn('service_id', $service_id)
                ->where('status', 'completed')
                ->whereMonth('updated_at', $month)
                ->sum('amount');

            //reviews
            //all reviews
            $reviews = RatingService::whereIn('service_id', $service_id)
                ->get();
            //avaerage of reviews
            $avg = RatingService::whereIn('service_id', $service_id)
                ->avg('rating');
            //count of reviews
            $total_rating = RatingService::whereIn('service_id', $service_id)
                ->count();

            return response()->json([
                'total_appointments' => $count_appointments,
                'total_customers' => $count_customers,
                'total_income' => $sum_amount,
                'sale_of_month' => $sale_of_month,
                'avg_rating' => $avg,
                'total_rating' => $total_rating,
                'reviews' => $reviews,
                'message' => 'Service stats retrieved successfully',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'message' => 'Barber Not Found',
                'status' => 'error',
            ], 400);
        }
    }
    //X------------X------------X
    //edit sub_services
    public function editSubService(Request $request)
    {
        $user = Auth::user();
        $input = $request->all();
        $sub_service_id = $request->id;
        $sub_service = SubService::find($sub_service_id);
        if ($sub_service) {
            $validator = Validator::make($input, [
                'name' => 'required',
                'price' => 'required',
                'time' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'status' => 'error',
                ], 422);
            }
            $sub_service->update($input);
            return $sub_service;
        } else {
            return response()->json([
                'message' => 'Sub-service not found.',
                'status' => 'error'
            ], 404);
        }
    }
    //X--------------X------------X
    public function addServiceImages(Request $request)
    {
        $user = Auth::user();
        $service_id = Service::where('user_id', $user->id)->pluck('id')->first();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $extension = $image->extension();
                $filename = time()  . '.' . $extension;
                $image->move(public_path('assets/images/services'), $filename);
                // Store the image
                ServicesImage::create([
                    'user_id' => $user->id,
                    'service_id' => $service_id,
                    'image' => $filename,
                ]);
            }
            return response()->json([
                'message' => 'Images uploaded successfully!',
                'status' => 'success',
            ], 200);
        }
        return response()->json([
            'message' => 'No image found.',
            'status' => 'error',
        ], 400);
    }
    //X-------------X------------X
    public function getServiceImages()
    {
        $user = Auth::user();
        $service_id = Service::where('user_id', $user->id)->pluck('id')->first();
        $images = ServicesImage::where('user_id', $user->id)
            ->where('service_id', $service_id)
            ->get();
        return response()->json([
            'images' => $images,
            'message' => 'Images retrieved successfully!',
            'status' => 'success',
        ], 200);
    }
}
