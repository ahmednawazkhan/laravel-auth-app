<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{
    public function createInvitationLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:invitations',
        ], [
            'email.unique' => 'Invitation already created for this email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $token = substr(md5(rand(0, 9) . $request->email . time()), 0, 32);
        Invitation::create([
            'email' => $request->email,
            'invitation_token' =>  $token,
            'invitation_pin' =>  Str::random(6),
        ]);

        return response()->json([
            'message' => 'Invitation created successfully',
            'invitation-link' => route('invite.register', ['token' => $token]),
        ], 201);
    }

    public function register(Request $request)
    {
        $invitation = Invitation::where('invitation_token', $request->token)->first();

        if (!$invitation) {
            return response()->json(['message' => 'invalid token'], 401);
        }
    }
}
