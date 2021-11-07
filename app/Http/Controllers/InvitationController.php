<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\InviteNotification;
use App\Notifications\PINConfirmationNotification;
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

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $invitation = Invitation::where('invitation_token', $request->token)->first();

        if (!$invitation) {
            return response()->json(['message' => 'invalid token'], 401);
        }

        User::updateOrCreate([
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
            'user_role' => 'user',
        ]);

        $route = route('register.confirm', ['token' => $invitation->token]);
        Notification::route('mail', $invitation->email)
            ->notify(new PINConfirmationNotification($invitation->invitation_pin, $route));

        return response()->json([
            'message' => 'Password created. Please check your inbox and confirm pin to login',
            'confirm_token' => $route,
        ], 201);
    }

    public function confirmRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'pin' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $invitation = Invitation::where('invitation_token', $request->token)->first();
        $user = User::where('email', $invitation->email)->first();

        if (!$invitation || !$user) {
            return response()->json(['message' => 'invalid token'], 401);
        }

        if ($user->registered_at) {
            return response()->json(['message' => 'User already registered'], 401);
        }

        if ($invitation->invitation_pin !== $request->pin) {
            return response()->json(['message' => 'invalid pin'], 401);
        }

        User::where('email', $invitation->email)->update([
            'registered_at' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Registration confirmed successfully. You can login'
        ], 201);
    }
}
