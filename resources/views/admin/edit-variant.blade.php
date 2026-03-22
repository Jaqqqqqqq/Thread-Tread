@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); max-width: 600px; margin: 0 auto;">
        <h2 style="margin: 0 0 32px 0; background: #333; color: #fff; padding: 16px; margin: -32px -32px 32px -32px; border-radius: 8px 8px 0 0;">
            Edit Variant - {{ $product->product_name }}
        </h2>
        
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

        <form method="POST" action="{{ route('admin.variants.update', $variant->variant_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Color -->
            <div style="margin-bottom: 20px;">
                <label for="color" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Color</label>
                <input type="text" id="color" name="color" value="{{ old('color', $variant->color) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Size -->
            <div style="margin-bottom: 20px;">
                <label for="size" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Size</label>
                <input type="text" id="size" name="size" value="{{ old('size', $variant->size) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Stock -->
            <div style="margin-bottom: 20px;">
                <label for="stock" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Stock Quantity</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $variant->stock) }}" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Current Color Image Preview + Upload New -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Color Image</label>
                @if($variant->color_image)
                    <div style="margin-bottom: 12px;">
                        <img src="{{ asset($variant->color_image) }}" alt="{{ $variant->color }}" style="max-width: 150px; max-height: 150px; border-radius: 6px; border: 1px solid #ddd;">
                        <p style="margin: 8px 0 0 0; font-size: 12px; color: #666;">Current color image</p>
                    </div>
                @endif
                <input type="file" id="color_image" name="color_image" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">Leave blank to keep current image. Accepted formats: JPG, PNG, GIF, WebP (Max 2MB)</small>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">Update Variant</button>
                <a href="{{ route('admin.products.edit', $product->product_id) }}" style="flex: 1; padding: 14px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 16px; text-align: center; text-decoration: none; font-weight: 600; line-height: 1.2;">Cancel</a>
            </div>
        </form>

    </div>
</div>
@endsection
