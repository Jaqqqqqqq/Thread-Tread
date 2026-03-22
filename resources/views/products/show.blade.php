@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px; max-width: 1000px; margin: 0 auto;">

    @if (session('success'))
        <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background: #ffcdd2; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('home') }}" style="color: #1976d2; text-decoration: none; font-size: 14px; display: inline-block; margin-bottom: 20px;">&larr; Back to Products</a>

    <!-- Product Details -->
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 32px;">
        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 32px;">
            <!-- Product Image -->
            <div>
                <img src="{{ $product->prod_image ? asset($product->prod_image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" alt="{{ $product->product_name }}" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">

                @if($product->images && $product->images->count() > 0)
                    <div style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;">
                        @foreach($product->images as $img)
                            <img src="{{ asset($img->image_path) }}" alt="Gallery" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: pointer;">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 style="margin: 0 0 8px 0; font-size: 1.8rem; color: #333;">{{ $product->product_name }}</h1>
                <p style="margin: 0 0 4px 0; font-size: 14px; color: #888;">
                    {{ $product->brand->brand_name ?? 'No Brand' }} &bull; {{ $product->category->category_name ?? 'Uncategorized' }}
                </p>

                <!-- Average Rating -->
                <div style="margin: 12px 0; display: flex; align-items: center; gap: 8px;">
                    @php $avgRating = $product->averageRating(); @endphp
                    <div style="display: flex; gap: 2px;">
                        @for ($i = 1; $i <= 5; $i++)
                            <span style="color: {{ $i <= round($avgRating) ? '#f9a825' : '#ddd' }}; font-size: 20px;">&#9733;</span>
                        @endfor
                    </div>
                    <span style="font-size: 14px; color: #666;">{{ $avgRating }} / 5 ({{ $product->reviews->count() }} {{ Str::plural('review', $product->reviews->count()) }})</span>
                </div>

                <p style="font-size: 1.6rem; font-weight: 700; color: #2e7d32; margin: 16px 0;">&#8369;{{ number_format($product->price, 2) }}</p>
                <p style="font-size: 14px; color: #555; line-height: 1.6;">{{ $product->prod_desc ?? 'No description available.' }}</p>

                <!-- Variants & Add to Cart -->
                @if($product->variants->count() > 0)
                    <div style="margin-top: 20px;">
                        <h4 style="margin: 0 0 12px 0; color: #333; font-size: 14px;">Available Variants</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: #f5f5f5; border-bottom: 1px solid #ddd;">
                                    <th style="padding: 8px 12px; text-align: left;">Color</th>
                                    <th style="padding: 8px 12px; text-align: left;">Size</th>
                                    <th style="padding: 8px 12px; text-align: left;">Stock</th>
                                    <th style="padding: 8px 12px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $v)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 8px 12px;">{{ $v->color ?? '—' }}</td>
                                        <td style="padding: 8px 12px;">{{ $v->size ?? '—' }}</td>
                                        <td style="padding: 8px 12px;">
                                            <span style="background: {{ $v->stock > 0 ? '#c8e6c9' : '#ffcdd2' }}; padding: 2px 8px; border-radius: 3px; font-size: 12px;">{{ $v->stock > 0 ? $v->stock . ' in stock' : 'Out of stock' }}</span>
                                        </td>
                                        <td style="padding: 8px 12px; text-align: center;">
                                            @if($v->stock > 0)
                                                <form method="POST" action="{{ route('cart.add') }}" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                    @csrf
                                                    <input type="hidden" name="variant_id" value="{{ $v->variant_id }}">
                                                    <input type="number" name="quantity" value="1" min="1" max="{{ $v->stock }}" style="width: 50px; padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; text-align: center; font-size: 12px;">
                                                    <button type="submit" style="padding: 5px 12px; background: #2e7d32; color: #fff; border: none; border-radius: 3px; font-size: 12px; cursor: pointer; font-weight: 600; white-space: nowrap;">Add to Cart</button>
                                                </form>
                                            @else
                                                <span style="color: #999; font-size: 12px;">Unavailable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="color: #888; font-size: 13px; margin-top: 20px;">No variants available for this product.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="margin: 0 0 24px 0; color: #333; font-size: 1.3rem;">Customer Reviews</h2>

        {{-- Review Form: Only show for logged-in customers who haven't reviewed yet --}}
        @auth
            @php
                $userReview = $product->reviews->where('user_id', Auth::id())->first();
                $hasPurchased = \App\Models\Order::where('user_id', Auth::id())
                    ->where('order_status', 'completed')
                    ->whereHas('items', function($q) use ($product) {
                        $q->whereHas('variant', function($vq) use ($product) {
                            $vq->where('product_id', $product->product_id);
                        });
                    })->exists();
            @endphp

            @if($hasPurchased && !$userReview)
                {{-- POST REVIEW FORM --}}
                <div style="background: #f9f9f9; border-radius: 6px; padding: 20px; margin-bottom: 24px; border: 1px solid #eee;">
                    <h4 style="margin: 0 0 16px 0; color: #333;">Write a Review</h4>
                    <form method="POST" action="{{ route('reviews.store', $product->product_id) }}">
                        @csrf
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #333;">Rating</label>
                            <div id="starRating" style="display: flex; gap: 4px; cursor: pointer;">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star" data-value="{{ $i }}" style="font-size: 28px; color: #ddd; cursor: pointer; transition: color 0.2s;">&#9733;</span>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="" required>
                            @error('rating')
                                <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label for="comment" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #333;">Comment</label>
                            <textarea id="comment" name="comment" rows="3" maxlength="1000" placeholder="Share your thoughts about this product..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; resize: vertical;">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" style="padding: 10px 24px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;">Submit Review</button>
                    </form>
                </div>
            @elseif($userReview)
                {{-- EDIT REVIEW FORM --}}
                <div style="background: #e3f2fd; border-radius: 6px; padding: 20px; margin-bottom: 24px; border: 1px solid #bbdefb;">
                    <h4 style="margin: 0 0 16px 0; color: #333;">Your Review</h4>
                    <form method="POST" action="{{ route('reviews.update', $userReview->review_id) }}">
                        @csrf
                        @method('PUT')
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #333;">Rating</label>
                            <div id="editStarRating" style="display: flex; gap: 4px; cursor: pointer;">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="edit-star" data-value="{{ $i }}" style="font-size: 28px; color: {{ $i <= $userReview->rating ? '#f9a825' : '#ddd' }}; cursor: pointer; transition: color 0.2s;">&#9733;</span>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="editRatingInput" value="{{ $userReview->rating }}" required>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label for="editComment" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #333;">Comment</label>
                            <textarea id="editComment" name="comment" rows="3" maxlength="1000" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; resize: vertical;">{{ $userReview->comment }}</textarea>
                        </div>
                        <button type="submit" style="padding: 10px 24px; background: #1976d2; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;">Update Review</button>
                    </form>
                </div>
            @elseif(!$hasPurchased)
                <div style="background: #fff3e0; padding: 12px 16px; border-radius: 4px; color: #e65100; margin-bottom: 24px; font-size: 14px;">
                    You need to purchase this product before you can leave a review.
                </div>
            @endif
        @else
            <div style="background: #f5f5f5; padding: 12px 16px; border-radius: 4px; color: #666; margin-bottom: 24px; font-size: 14px;">
                <a href="{{ route('login') }}" style="color: #1976d2; text-decoration: none;">Log in</a> to write a review.
            </div>
        @endauth

        {{-- All Reviews List --}}
        @if($product->reviews->count() > 0)
            <div>
                @foreach($product->reviews->sortByDesc('created_at') as $review)
                    <div style="padding: 16px 0; border-bottom: 1px solid #eee; {{ $loop->last ? 'border-bottom: none;' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <strong style="font-size: 14px; color: #333;">{{ $review->user->fname }} {{ $review->user->lname }}</strong>
                                    @auth
                                        @if($review->user_id === Auth::id())
                                            <span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600;">You</span>
                                        @endif
                                    @endauth
                                </div>
                                <div style="display: flex; gap: 2px; margin-bottom: 8px;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span style="color: {{ $i <= $review->rating ? '#f9a825' : '#ddd' }}; font-size: 16px;">&#9733;</span>
                                    @endfor
                                </div>
                            </div>
                            <span style="font-size: 12px; color: #999;">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($review->comment)
                            <p style="margin: 0; font-size: 14px; color: #555; line-height: 1.5;">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: #999; font-size: 14px; margin: 0;">No reviews yet. Be the first to review this product!</p>
        @endif
    </div>
</div>

<script>
    // Star rating functionality for new review
    const stars = document.querySelectorAll('#starRating .star');
    const ratingInput = document.getElementById('ratingInput');
    if (stars.length > 0) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;
                stars.forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= value ? '#f9a825' : '#ddd';
                });
            });
            star.addEventListener('mouseover', function() {
                const value = this.getAttribute('data-value');
                stars.forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= value ? '#f9a825' : '#ddd';
                });
            });
            star.addEventListener('mouseout', function() {
                const currentVal = ratingInput.value || 0;
                stars.forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= currentVal ? '#f9a825' : '#ddd';
                });
            });
        });
    }

    // Star rating functionality for editing review
    const editStars = document.querySelectorAll('#editStarRating .edit-star');
    const editRatingInput = document.getElementById('editRatingInput');
    if (editStars.length > 0) {
        editStars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                editRatingInput.value = value;
                editStars.forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= value ? '#f9a825' : '#ddd';
                });
            });
            star.addEventListener('mouseover', function() {
                const value = this.getAttribute('data-value');
                editStars.forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= value ? '#f9a825' : '#ddd';
                });
            });
            star.addEventListener('mouseout', function() {
                const currentVal = editRatingInput.value || 0;
                editStars.forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= currentVal ? '#f9a825' : '#ddd';
                });
            });
        });
    }
</script>
@endsection