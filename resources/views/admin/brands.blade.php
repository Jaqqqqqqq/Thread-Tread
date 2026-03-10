@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: #f5f5f5;">
    <div style="padding: 48px 32px;">
        <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="margin: 0; color: #333;">Brands</h2>
                <a href="{{ route('admin.brands.create') }}" style="background: #2e7d32; color: #fff; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px;">+ Add Brand</a>
            </div>

            @if (session('success'))
                <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($brands->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Brand ID</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Brand Name</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Logo</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Description</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 12px; color: #666;">{{ $brand->brand_id }}</td>
                                    <td style="padding: 12px; color: #333;">{{ $brand->brand_name }}</td>
                                    <td style="padding: 12px;">
                                        @if ($brand->brand_logo)
                                            <img src="{{ asset($brand->brand_logo) }}" alt="{{ $brand->brand_name }}" style="height: 40px; object-fit: contain; border-radius: 4px;">
                                        @else
                                            <span style="color: #999; font-size: 13px;">No logo</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; color: #666; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $brand->description ?? 'N/A' }}</td>
                                    <td style="padding: 12px;">
                                        <div style="display: flex; gap: 10px;">
                                            <a href="{{ route('admin.brands.edit', $brand->brand_id) }}" style="background: #0052cc; color: #fff; padding: 6px 14px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">Edit</a>
                                            <form method="POST" action="{{ route('admin.brands.destroy', $brand->brand_id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: #d32f2f; color: #fff; padding: 6px 14px; border-radius: 4px; border: none; font-size: 13px; font-weight: 500; cursor: pointer;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p style="margin: 0;">No brands found. <a href="{{ route('admin.brands.create') }}" style="color: #0052cc; text-decoration: none;">Create one</a></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
