<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();
        // Validate common fields
        $validator = Validator::make($input, [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
                'error' => true,
            ], 422);
        }
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $extension = $request->image->extension();
            $filename = time() . "_." . $extension;
            $request->image->move(public_path('/assets/images/users'), $filename);
            $input['image'] = $filename;
        }
        // Create the user record in the database
        $input['password'] = Hash::make($request->password);
        $user = User::create($input);
        // Create and update API token for the user
        $api_token = $user->createToken($user->email)->accessToken;
        $user->update(['api_token' => $api_token]);

        return response()->json([
            "userdata" => $user,
            "message" => "User Registered Successfully",
            "status" => 'success',
        ], 200);
    }

    //Z---------X--------------X
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => "required",
            'password' => 'required',
        ]);
        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }
        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $api_token = $user->createToken($user->email)->accessToken;
            $user->update(['api_token' => $api_token]);

            return response()->json([
                'data' => $user,
                'message' => 'Login successful',
                'status' => 'success',
            ], 200);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
    }
    //X--------------X-------------X
    //forget password
    //the password reset is done in three steps
    //step1: request  email, generate pin and send it to mail
    //step2: validate pin and email and expiry
    //step3: change password, update user and delete pin, and notify user with success mail

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User with this email does not exist.',
            ], 404);
        }
        try {
            $pin = mt_rand(1000, 9999);
            $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email],
            ['email' => $user->email, 'pin' => $pin,]);

            if ($user && $passwordReset)
                $user->notify(
                    new PasswordResetRequest($passwordReset->pin)
                );
            return response()->json([
                'status' => 'success',
                'message' => 'Code has been sent to your email.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to process request. Try again later.',
            ], 500);
        }
    }
    //X-----------X-------X
    //step2
    public function ConfirmPIN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'pin' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        //get record from PasswordReset model by email coming in request
        $passwordReset = PasswordReset::where('email', $request->email)->first();
        // dd($passwordReset);
        if (!$passwordReset || $request->pin !== $passwordReset->pin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid pin.',
            ], 404);
        }

        // Check if the PIN is expired (e.g., 15 minutes expiration)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(15)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'This password reset token has expired.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Code verified successfully.',
        ], 200);
    }
    //X-------------X----------X
    //step3
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'pin' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }
        //get user email from PasswordReset model
        $passwordReset = PasswordReset::where('email', $request->email)->first();

        //check if pin and email are correct if not through error
        if (!$passwordReset || $request->pin !== $passwordReset->pin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials or pin.',
            ], 404);
        }
        //Retrieve the User
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials or token.',
            ], 404);
        }
        // Update user's password and save it
        $user->password = Hash::make($request->password);
        $user->save();
        // Delete the password reset record after successful reset
        $passwordReset->delete();
        // Send password reset success notification to the user
        $user->notify(new PasswordResetSuccess());

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been reset successfully.',
        ], 200);
    }


}
