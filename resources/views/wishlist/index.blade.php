@extends('layouts.app')
@section('content')
<h2>Wishlist</h2>
@if($wishlist && $wishlist->items->count())
    <ul>
    @foreach($wishlist->items as $item)
        <li>
            {{ $item->product->product_name }}
            <form method="POST" action="{{ route('wishlist.remove', $item->wishlist_item_id) }}" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
            </form>
        </li>
    @endforeach
    </ul>
@else
    <p>No items in wishlist.</p>
@endif
@endsection