@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="margin: 0; color: #333;">My Profile</h2>
            <a href="{{ route('profile.edit') }}" style="background: #2e7d32; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Edit Profile</a>
        </div>

        @if (session('success'))
            <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 40px; margin-bottom: 40px;">
            <!-- Profile Photo Section -->
            <div style="text-align: center;">
                @if ($user->profile_photo)
                    <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->fname }}" style="width: 180px; height: 180px; border-radius: 8px; object-fit: cover; border: 2px solid #ddd;">
                @else
                    <div style="width: 180px; height: 180px; border-radius: 8px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #999;">
                        No Photo
                    </div>
                @endif
                <p style="margin-top: 12px; font-size: 13px; color: #666;">Member since {{ $user->created_at->format('M d, Y') }}</p>
            </div>

            <!-- Profile Info Section -->
            <div>
                <div style="margin-bottom: 24px;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">Personal Information</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #999; margin-bottom: 4px; text-transform: uppercase;">First Name</label>
                            <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->fname }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #999; margin-bottom: 4px; text-transform: uppercase;">Last Name</label>
                            <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->lname }}</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #999; margin-bottom: 4px; text-transform: uppercase;">Email</label>
                        <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->email }}</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #999; margin-bottom: 4px; text-transform: uppercase;">Phone</label>
                        <p style="margin: 0; font-size: 14px; color: #333;">{{ $user->phone ?? '—' }}</p>
                    </div>
                </div>

                <div style="border-top: 1px solid #eee; padding-top: 20px;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">Account Status</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #999; margin-bottom: 4px; text-transform: uppercase;">Role</label>
                            <span style="display: inline-block; background: {{ $user->role === 'admin' ? '#ffc107' : '#2e7d32' }}; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 600;">{{ ucfirst($user->role) }}</span>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #999; margin-bottom: 4px; text-transform: uppercase;">Status</label>
                            <span style="display: inline-block; background: {{ $user->is_active ? '#4caf50' : '#f44336' }}; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 600;">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid #eee; padding-top: 20px;">
            <h3 style="margin: 0 0 16px 0; color: #333; font-size: 16px;">Change Password</h3>
            <p style="color: #666; margin-bottom: 16px; font-size: 14px;">Update your password to keep your account secure.</p>
            <form method="POST" action="{{ route('profile.change-password') }}">
                @csrf
                
                <div style="margin-bottom: 16px;">
                    <label for="current_password" style="display: block; margin-bottom: 6px; font-weight: 500; color: #333; font-size: 14px;">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @error('current_password')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="password" style="display: block; margin-bottom: 6px; font-weight: 500; color: #333; font-size: 14px;">New Password</label>
                    <input type="password" name="password" id="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @error('password')<span style="color: red; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password_confirmation" style="display: block; margin-bottom: 6px; font-weight: 500; color: #333; font-size: 14px;">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <button type="submit" style="background: #2e7d32; color: #fff; padding: 10px 20px; border-radius: 4px; border: none; font-size: 14px; cursor: pointer; font-weight: 600;">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
