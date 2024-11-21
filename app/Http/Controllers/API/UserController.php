<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Service;
use App\Models\UserGallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //home screan api
    public function allBarbers()
    {
        $user = Auth::user();
        $barbers = Service::with('services_images')->get();
        $images = UserGallery::where('user_id', $user->id)->get();
        return response()->json([
            'Barbers ' => $barbers,
            'user_images ' => $images,
            'message' => 'barbers retrieved successfully.',
            'status' => 'success',
        ], 200);
    }
    //X------------X---------------X
    public function findBarber(Request $request)
    {
        $user = Auth::user();
        $name = $request->name;
        $location = $request->location;
        if ($location || $name) {
            $barber = Service::where('shop_name', 'LIKE', "%$name%")
                ->orWhere('city', 'LIKE', "%$location%")
                ->get();
            return response()->json([
                'message' => 'Barbers based on search.',
                'status' => 'success',
                'data' => $barber,
            ], 200);
        } else {
            return response()->json([
                'message' => 'no barber found',
                'status' => 'error',
            ], 404);
        }
    }
    //user profile api
    //get auth user
    public function getUser()
    {
        $user = Auth::user();
        if ($user) {
            return response()->json([
                'data' => $user,
                'message' => 'user retreived successfully.',
                'status' => 'success',
            ], 200);
        }
    }
    public function EditUser(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        if ($user) {
            // Handle the image upload
            if ($request->hasFile('image')) {
                if ($user->image) {
                    $url_path = parse_url($user->image, PHP_URL_PATH);
                    $basename = pathinfo($url_path, PATHINFO_BASENAME);
                    $file_path = public_path("assets/images/users/$basename");
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                $extension = $request->image->extension();
                $filename = time() . '.' . $extension;
                $request->image->move(public_path('assets/images/users'), $filename);
                $input['image'] = $filename;
            }
            // Update user fields
            $user->update($input);
            return response()->json([
                'data' => $user,
                'message' => 'User profile updated successfully',
                'status' => 'success',
            ], 200);
        }
        return response()->json([
            'message' => 'User not found',
            'status' => 'error',
        ], 404);
    }
    //X---------------X-----------X
    public function changeUserPassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 'error'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8|max:11',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 'error'
            ], 422);
        }
        //check if old password and user->password match are not
        if (Hash::check($request->old_password, $user->password)) {
            if ($request->old_password == $request->password) {
                $error['password'] = ['your new password cannot be the same as your old password'];
                return response()->json([
                    'message' => $error,
                    'status' => 'error'
                ], 422);
            }
            $user->fill(['password' => Hash::make($request->password)])->save();
            return response()->json([
                'message' => 'password change successfully.',
                'status' => 'success',
            ]);
        } else {
            $error['old_password'] = ['Your old password was incorrect'];
            return response()->json([
                'message' => $error,
                'status' => 'error',
            ], 422);
        }
    }
    //X-----------X----------X
    public function addUserImages(Request $request)
    {
        $user = Auth::user();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $extension = $image->extension();
                $filename = time()  . '.' . $extension;
                $image->move(public_path('assets/images/user'), $filename);
                // Store the image
                UserGallery::create([
                    'user_id' => $user->id,
                    'image' => $filename,
                ]);
                $images = UserGallery::where('user_id', $user->id)->get();
            }
            return response()->json([
                'images' => $images,
                'message' => 'Image uploaded successfully!',
                'status' => 'success',
            ], 200);
        }
        return response()->json([
            'message' => 'No image found.',
            'status' => 'error',
        ], 400);
    }
}
