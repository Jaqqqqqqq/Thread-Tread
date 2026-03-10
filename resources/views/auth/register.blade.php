@extends('layouts.minimal')

@section('content')
<div class="auth-container">
    <h2>Register</h2>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" name="fname" id="fname" value="{{ old('fname') }}" required autofocus>
            @error('fname')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" name="lname" id="lname" value="{{ old('lname') }}" required>
            @error('lname')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required>
            @error('phone')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="profile_photo">Profile Photo (Optional)</label>
            <input type="file" name="profile_photo" id="profile_photo" accept="image/*">
            <small style="color: #666;">Accepted formats: JPG, PNG, GIF, WebP (Max 2MB)</small>
            @error('profile_photo')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            @error('password')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
</div>
@endsection
