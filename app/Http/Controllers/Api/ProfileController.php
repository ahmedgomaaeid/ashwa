<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $user->update($request->all());
        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        //check if the old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'The old password is incorrect'], 400);
        }
        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Password updated successfully']);
    }
}
