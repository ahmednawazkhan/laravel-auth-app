<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255',
            'password' => 'string|min:6|confirmed',
            'user_name' => 'string|min:4|max:20|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user = User::find(auth()->user()->id);

        $user->update($validator->validated());

        return response()->json(['success' => 'Profile updated successfully'], 200);
    }
}
