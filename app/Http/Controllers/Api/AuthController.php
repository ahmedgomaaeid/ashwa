<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Registration
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new user with the provided details
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ]);

        // Generate JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Attempt to send the verification code to the user's phone
        try {
            $this->sendVerificationCode($user);
        } catch (\Exception $e) {
            // Handle errors when sending the verification code
            return response()->json([
                'message' => 'Registration successful, but failed to send verification code.',
                'token' => $token,
                'user' => $user,
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Registration successful. A verification code has been sent to your phone.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function sendVerificationCode($user)
    {
        // Generate a 6-digit random verification code
        $verificationCode = "00000";//rand(100000, 999999);

        // Save the verification code and its expiration time
        $user->update([
            'verification_code' => $verificationCode,
            'verification_expires_at' => now()->addMinutes(10),
        ]);

        // Send the verification code using your SMS provider's API
        // $response = Http::post('https://sms-provider-api.com/send', [
        //     'phone' => $user->phone,
        //     'message' => "Your verification code is: $verificationCode",
        // ]);

        // if (!$response->successful()) {
        //     throw new \Exception('Failed to send verification code.');
        // }
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function phoneLogin(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        $validator = Validator::make($credentials, [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by phone number
        $user = User::where('phone', $request->phone)->first();

        // Check if user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a JWT token for the authenticated user
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }


    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'verification_code' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Check if the code matches and is not expired
        if ($user->verification_code !== $request->verification_code || now()->greaterThan($user->verification_expires_at)) {
            return response()->json(['message' => 'Invalid or expired verification code.'], 401);
        }

        // Clear the verification code and mark as verified
        $user->update([
            'verification_code' => null,
            'verification_expires_at' => null,
            'verified' => true, // Mark user as verified
        ]);

        return response()->json(['message' => 'Phone number verified successfully.']);
    }

}
