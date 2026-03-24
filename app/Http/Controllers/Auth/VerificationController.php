<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Email verification attempt', [
            'user_id' => $request->id,
            'hash' => $request->hash
        ]);

        $user = User::findOrFail($request->id);

        \Illuminate\Support\Facades\Log::info('User found', [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at
        ]);

        if (sha1($user->email) !== $request->hash) {
            \Illuminate\Support\Facades\Log::warning('Hash mismatch', [
                'expected' => sha1($user->email),
                'received' => $request->hash
            ]);
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if ($user->email_verified_at !== null) {
            return redirect()->route('login')->with('success', 'Email already verified. You can now log in.');
        }

        $user->update(['email_verified_at' => now()]);

        $user->refresh();
        \Illuminate\Support\Facades\Log::info('Email verified successfully', [
            'user_id' => $user->user_id,
            'email_verified_at' => $user->email_verified_at
        ]);

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now log in to your account.');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at !== null) {
            return back()->with('info', 'This email address is already verified.');
        }

        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\EmailVerification($user));

        return back()->with('success', 'Verification email sent! Please check your inbox.');
    }
}
