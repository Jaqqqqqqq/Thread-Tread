@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: #f5f5f5;">
    <div style="padding: 48px 32px;">
        <div style="background: #fff; border-radius: 8px; padding: 32px; max-width: 600px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 32px 0; color: #333;">Edit Category</h2>
            
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

            <form method="POST" action="{{ route('admin.categories.update', $category->category_id) }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label for="category_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Category Name <span style="color: #d32f2f;">*</span></label>
                    <input type="text" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @error('category_name')
                        <span style="color: #d32f2f; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Description</label>
                    <textarea id="description" name="description" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: inherit; box-sizing: border-box;">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <span style="color: #d32f2f; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; padding: 12px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">Update Category</button>
                    <a href="{{ route('admin.categories') }}" style="flex: 1; padding: 12px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 16px; text-align: center; text-decoration: none; font-weight: 600;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
