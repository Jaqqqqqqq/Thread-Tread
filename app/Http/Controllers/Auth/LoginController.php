<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate required fields first
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            \Illuminate\Support\Facades\Log::info('Login attempt', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'is_null' => $user->email_verified_at === null
            ]);
            
            // Check if email is verified
            if ($user->email_verified_at === null) {
                \Illuminate\Support\Facades\Log::warning('Login denied - email not verified', [
                    'user_id' => $user->user_id,
                    'email' => $user->email
                ]);
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Please verify your email address before logging in. Check your inbox for the verification link.',
                ])->withInput($request->except('password'));
            }
            
            $request->session()->regenerate();
            if ($user->role === 'admin') {
                return redirect()->route('admin.products');
            }
            return redirect()->route('home');
        }
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }
}
