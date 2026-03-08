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
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            // Block login until email is verified
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'You must verify your email address before you can log in. Please check your inbox (and Mailtrap) for the verification link.',
                ]);
            }
            $request->session()->regenerate();
            if ($user->role === 'admin') {
                return redirect()->route('admin.products');
            }
            return redirect()->route('home');
        }
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }
}
