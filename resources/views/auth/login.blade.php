@extends('layouts.minimal')

@section('content')
<div class="auth-container">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group" style="margin-bottom: 12px;">
            <label style="display: flex; align-items: center;">
                <input type="checkbox" name="remember" style="margin-right: 8px;">
                Remember Me
            </label>
        </div>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
</div>
@endsection
