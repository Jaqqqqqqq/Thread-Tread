<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
        ]);

        // Make the first user an admin
        $userCount = User::count();
        $role = $userCount === 0 ? 'admin' : 'customer';

        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
        ]);

        // Send email verification (to Mailtrap when MAIL_MAILER=smtp and Mailtrap credentials are in .env)
        $user->sendEmailVerificationNotification();

        // Redirect to login page – user must verify email before they can log in
        return redirect()->route('login')->with('success', 'Registration successful! Please check your email (and Mailtrap inbox) to verify your account before logging in.');
    }
}
