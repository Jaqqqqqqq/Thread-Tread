@extends('layouts.minimal')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="auth-container">
                <h2>Login</h2>

                @if (session('success'))
                    <div class="alert alert-success" style="margin-bottom: 1rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required class="form-control">
                    </div>

                    <div class="form-group mt-3">
                        <label>
                            <input type="checkbox" name="remember" value="1">
                            Remember Me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Login
                    </button>

                    <div class="mt-3">
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    </div>
                </form>

                <p class="mt-3">
                    Don't have an account?
                    <a href="{{ route('register') }}">Register</a>
                </p>

            </div>

        </div>
    </div>
</div>
@endsection