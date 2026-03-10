@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: #f5f5f5;">
    <div style="padding: 48px 32px;">
        <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="margin: 0; color: #333;">Users</h2>
            </div>

            @if (session('success'))
                <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($users->count() > 0)
                <div style="overflow-x: auto;">
                    <table id="usersTable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">ID</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Photo</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Name</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Email</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Phone</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Role</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Status</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Created</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 12px; color: #666;">{{ $user->user_id }}</td>
                                    <td style="padding: 12px;">
                                        @if ($user->profile_photo)
                                            <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->fname }}" style="height: 40px; width: 40px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <div style="height: 40px; width: 40px; background: #e0e0e0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #999;">No Photo</div>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; color: #333;">{{ $user->fname }} {{ $user->lname }}</td>
                                    <td style="padding: 12px; color: #333;">{{ $user->email }}</td>
                                    <td style="padding: 12px; color: #666;">{{ $user->phone ?? '—' }}</td>
                                    <td style="padding: 12px;">
                                        <span style="display: inline-block; background: {{ $user->role === 'admin' ? '#ffc107' : '#2e7d32' }}; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <span style="display: inline-block; background: {{ $user->is_active ? '#4caf50' : '#f44336' }}; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td style="padding: 12px; color: #666; font-size: 13px;">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td style="padding: 12px;">
                                        <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                                                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                            <select name="is_active" style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            <button type="submit" style="background: #2e7d32; color: #fff; padding: 6px 12px; border-radius: 4px; border: none; font-size: 12px; font-weight: 500; cursor: pointer;">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p style="margin: 0;">No users found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var table = document.getElementById('usersTable');
        if (table && typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
            jQuery('#usersTable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 8 }
                ]
            });
        }
    });
</script>
@endsection
