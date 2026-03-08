@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: #f5f5f5;">
    <div style="padding: 48px 32px;">
        <div style="background: #fff; border-radius: 8px; padding: 32px; max-width: 600px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 32px 0; color: #333;">Edit Brand</h2>
            
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

            <form method="POST" action="{{ route('admin.brands.update', $brand->brand_id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label for="brand_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Brand Name <span style="color: #d32f2f;">*</span></label>
                    <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name', $brand->brand_name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Description</label>
                    <textarea id="description" name="description" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">{{ old('description', $brand->description) }}</textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="brand_logo" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Brand Logo</label>
                    @if($brand->brand_logo)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ asset($brand->brand_logo) }}" alt="Brand Logo" style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                            <p style="margin: 8px 0 0 0; font-size: 12px; color: #666;">Current logo</p>
                        </div>
                    @endif
                    <input type="file" id="brand_logo" name="brand_logo" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    <small style="color: #666; display: block; margin-top: 6px;">Leave blank to keep current logo. Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="is_active" style="display: flex; align-items: center; font-weight: 600; color: #333; cursor: pointer;">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ $brand->is_active || old('is_active') ? 'checked' : '' }} style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                        Active
                    </label>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; padding: 12px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">Update Brand</button>
                    <a href="{{ route('admin.brands') }}" style="flex: 1; padding: 12px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 16px; text-align: center; text-decoration: none; font-weight: 600;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
