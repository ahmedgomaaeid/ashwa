<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Str;
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
        $verificationCode = "00000"; //rand(100000, 999999);

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

    public function sendAgain(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Attempt to send the verification code to the user's phone
        try {
            $this->sendVerificationCode($user);
        } catch (\Exception $e) {
            // Handle errors when sending the verification code
            return response()->json([
                'message' => 'Failed to send verification code.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['message' => 'Verification code sent successfully.']);
    }


    // Password reset
    public function requestResetCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Check if a code was already sent recently to prevent abuse
        if ($user->reset_token_expires_at && now()->lessThan($user->reset_token_expires_at)) {
            return response()->json(['message' => 'A reset code was already sent recently. Please wait before requesting again.'], 429);
        }

        $resetCode = "11111"; //rand(100000, 999999);

        $user->update([
            'verification_code' => $resetCode,
            'verification_expires_at' => now()->addMinutes(10),
        ]);

        // // Send the reset code via SMS using your SMS provider's API
        // $response = Http::post('https://sms-provider-api.com/send', [
        //     'phone' => $user->phone,
        //     'message' => "Your password reset code is: $resetCode",
        // ]);

        // if (!$response->successful()) {
        //     return response()->json(['message' => 'Failed to send reset code.'], 500);
        // }

        return response()->json(['message' => 'Reset code sent successfully.']);
    }


    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'reset_code' => 'required|string', // Accept the 6-digit code from the user
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Check if the reset code matches and is not expired
        if ($user->verification_code !== $request->reset_code || now()->greaterThan($user->verification_expires_at)) {
            return response()->json(['message' => 'Invalid or expired reset code.'], 401);
        }

        // Generate a unique reset token using a combination of phone and reset code
        $plainToken = $request->phone . $request->reset_code . "asdf123"; // Generate a unique token
        $hashedToken = Hash::make($plainToken); // Hash the token to store securely

        // Save the hashed version of the token and set an expiration time
        $user->update([
            'reset_token' => $hashedToken,
            'reset_token_expires_at' => now()->addMinutes(10), // Set an expiration time for the token
        ]);

        // Return the plain (unhashed) token to the user
        return response()->json(['message' => 'Code verified successfully.', 'reset_token' => $hashedToken]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'new_password' => 'required|string|min:8',
            'reset_token' => 'required|string', // Require the verified reset token
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Ensure that the reset token is still valid
        if (now()->greaterThan($user->reset_token_expires_at)) {
            return response()->json(['message' => 'Reset token has expired. Please request a new reset.'], 401);
        }

        // Verify the reset token to ensure it matches
        if ($request->reset_token != $user->reset_token) {
            return response()->json(['message' => 'Invalid reset token.'], 401);
        }

        // Hash the new password before saving it
        $newPassword = Hash::make($request->new_password);

        // Reset the user's password and clear the reset token
        $user->update([
            'password' => $newPassword,
            'reset_token' => null, // Clear the reset token after use
            'reset_token_expires_at' => null, // Clear the expiration time
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
