<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\InviteNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

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
        // TODO: dont send email if this fails
        Invitation::create([
            'email' => $request->email,
            'invitation_token' =>  $token,
            'invitation_pin' =>  Str::random(6),
        ]);

        $route = route('invite.register', ['token' => $token]);

        Notification::route('mail', $request->email)
            ->notify(new InviteNotification($route));

        return response()->json([
            'message' => 'Invitation created successfully',
            'invitation-link' => $route,
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
