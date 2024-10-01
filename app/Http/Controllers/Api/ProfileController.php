<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    //change profile image
    public function updateImage(Request $request)
    {
        $user = $request->user();

        // Validate the request to ensure it has an image file
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in a public storage folder
            $path = $image->storeAs('profile_images', $imageName, 'public');

            // Delete the old image if it exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // Update the user's image path
            $user->image = $path;
            $user->save();

            return response()->json([
                'message' => 'Image updated successfully',
                'image_url' => asset('storage/' . $user->image),
            ]);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }
}
