<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review (customer must have purchased the product).
     */
    public function store(Request $request, $product_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($product_id);
        $user = Auth::user();

        // Check if the user has purchased this product
        if (!$this->hasPurchased($user->user_id, $product_id)) {
            return redirect()->back()->with('error', 'You can only review products you have purchased.');
        }

        // Check if user already reviewed this product (unique constraint)
        $existing = Review::where('user_id', $user->user_id)->where('product_id', $product_id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You have already reviewed this product. You can edit your existing review.');
        }

        Review::create([
            'user_id' => $user->user_id,
            'product_id' => $product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review posted successfully!');
    }

    /**
     * Update an existing review (only the owner can update).
     */
    public function update(Request $request, $review_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::findOrFail($review_id);

        // Ensure the authenticated user owns this review
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own reviews.');
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review updated successfully!');
    }

    /**
     * Admin: Delete a review.
     */
    public function destroy($review_id)
    {
        $review = Review::findOrFail($review_id);
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully!');
    }

    /**
     * Admin: List all reviews in a datatable.
     */
    public function adminIndex()
    {
        $reviews = Review::with(['user', 'product'])->orderBy('created_at', 'desc')->get();
        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Check if a user has purchased a product (via completed orders).
     */
    private function hasPurchased($userId, $productId)
    {
        return Order::where('user_id', $userId)
            ->whereHas('items', function ($query) use ($productId) {
                $query->whereHas('variant', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                });
            })
            ->exists();
    }
}
