@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; background: #f5f5f5;">
    <div style="padding: 48px 32px;">
        <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="margin: 0; color: #333;">Reviews</h2>
                <span style="font-size: 14px; color: #666;">{{ $reviews->count() }} total {{ Str::plural('review', $reviews->count()) }}</span>
            </div>

            @if (session('success'))
                <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($reviews->count() > 0)
                <div style="overflow-x: auto;">
                    <table id="reviewsTable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">ID</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Product</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Customer</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Rating</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Comment</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Date</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $review)
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 12px; color: #666;">{{ $review->review_id }}</td>
                                    <td style="padding: 12px; color: #333;">
                                        @if($review->product)
                                            <a href="{{ route('products.show', $review->product->product_id) }}" style="color: #1976d2; text-decoration: none;">{{ $review->product->product_name }}</a>
                                        @else
                                            <span style="color: #999;">Deleted Product</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; color: #333;">
                                        @if($review->user)
                                            {{ $review->user->fname }} {{ $review->user->lname }}
                                        @else
                                            <span style="color: #999;">Deleted User</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px;">
                                        <div style="display: flex; gap: 1px;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span style="color: {{ $i <= $review->rating ? '#f9a825' : '#ddd' }}; font-size: 14px;">&#9733;</span>
                                            @endfor
                                        </div>
                                    </td>
                                    <td style="padding: 12px; color: #555; font-size: 13px; max-width: 300px;">
                                        {{ Str::limit($review->comment, 80) ?? '—' }}
                                    </td>
                                    <td style="padding: 12px; color: #666; font-size: 13px; white-space: nowrap;">{{ $review->created_at->format('M d, Y') }}</td>
                                    <td style="padding: 12px;">
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review->review_id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 4px 10px; background: #d32f2f; color: #fff; border: none; border-radius: 3px; font-size: 12px; cursor: pointer; font-weight: 500;" onclick="return confirm('Are you sure you want to delete this review?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background: #f0f0f0; padding: 20px; border-radius: 4px; text-align: center; color: #666;">
                    No reviews found.
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#reviewsTable').DataTable({
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: "Search reviews:"
            }
        });
    });
</script>
@endsection
