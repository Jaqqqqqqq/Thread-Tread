@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="margin: 0 0 32px 0; background: #333; color: #fff; padding: 16px; margin: -32px -32px 32px -32px; border-radius: 8px 8px 0 0;">Edit Profile</h2>
        
        @if ($errors->any())
            <div style="background: #ffebee; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="fname" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">First Name</label>
                    <input type="text" id="fname" name="fname" value="{{ old('fname', $user->fname) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div>
                    <label for="lname" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Last Name</label>
                    <input type="text" id="lname" name="lname" value="{{ old('lname', $user->lname) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 30px;">
                <label for="profile_photo" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Profile Photo</label>
                @if ($user->profile_photo)
                    <div style="margin-bottom: 12px;">
                        <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->fname }}" style="max-width: 200px; max-height: 200px; border-radius: 6px; border: 1px solid #ddd;">
                        <p style="margin: 8px 0 0 0; font-size: 12px; color: #666;">Current photo</p>
                    </div>
                @endif
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">Leave blank to keep current photo. Accepted formats: JPG, PNG, GIF, WebP (Max 2MB)</small>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">Save Changes</button>
                <a href="{{ route('profile.show') }}" style="flex: 1; padding: 14px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 16px; text-align: center; text-decoration: none; font-weight: 600; line-height: 1.2;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
