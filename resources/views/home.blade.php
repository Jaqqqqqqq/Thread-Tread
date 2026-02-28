@extends('layouts.minimal')

@section('content')
<header style="display: flex; justify-content: space-between; align-items: center; padding: 18px 32px; background: #222; color: #fff;">
    <div style="display: flex; align-items: center; gap: 32px;">
        <h2 style="font-size: 1.7rem; font-weight: 600; letter-spacing: 1px; margin: 0;">Thread & Trend</h2>
        <nav>
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" style="color: #fff; text-decoration: none; font-size: 1.1rem; font-weight: 500; margin-left: 12px;">Products</a>
                @else
                    <a href="#products" style="color: #fff; text-decoration: none; font-size: 1.1rem; font-weight: 500; margin-left: 12px;">Products</a>
                @endif
            @else
                <a href="#products" style="color: #fff; text-decoration: none; font-size: 1.1rem; font-weight: 500; margin-left: 12px;">Products</a>
            @endauth
        </nav>
    </div>
    <div style="position: relative;">
        @auth
            <button id="profileBtn" style="background: none; border: none; color: #fff; display: flex; align-items: center; cursor: pointer;">
                <span style="margin-right: 8px;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 8-4 8-4s8 0 8 4"/></svg>
                </span>
                {{ Auth::user()->fname }}
                <span style="margin-left: 8px;">▼</span>
            </button>
            <div id="profileDropdown" style="display: none; position: absolute; right: 0; top: 40px; background: #fff; color: #222; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); min-width: 120px;">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="width: 100%; background: none; border: none; padding: 10px 18px; text-align: left; cursor: pointer; color: #222; font-size: 16px;">Log out</button>
                </form>
            </div>
        @else
            <button id="profileBtn" style="background: none; border: none; color: #fff; display: flex; align-items: center; cursor: pointer;">
                <span style="margin-right: 8px;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 8-4 8-4s8 0 8 4"/></svg>
                </span>
                <span style="margin-left: 8px;">▼</span>
            </button>
            <div id="profileDropdown" style="display: none; position: absolute; right: 0; top: 40px; background: #fff; color: #222; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); min-width: 120px;">
                <a href="{{ route('login') }}" style="display: block; padding: 10px 18px; text-decoration: none; color: #222;">Log in</a>
                <a href="{{ route('register') }}" style="display: block; padding: 10px 18px; text-decoration: none; color: #222;">Register</a>
            </div>
        @endauth
    </div>
</header>
<div style="padding: 48px 32px;">
    <h1>Welcome to Thread & Trend!</h1>
    <p>Your shop homepage is now active.</p>

    <h2 style="margin-top: 40px; font-size: 1.3rem; font-weight: 500;">Products</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; margin-top: 24px;">
        @foreach($products ?? [] as $product)
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 18px; text-align: center;">
            <img src="{{ $product->image_url ?? 'https://via.placeholder.com/180x180?text=No+Image' }}" alt="{{ $product->name }}" style="width: 180px; height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 12px;">
            <div style="font-size: 1.1rem; font-weight: 500; margin-bottom: 6px;">{{ $product->name }}</div>
            <div style="color: #444; font-size: 1rem; margin-bottom: 10px;">₱{{ number_format($product->price, 2) }}</div>
            <button title="Add to Cart" style="background: #222; color: #fff; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61l1.38-7.39H6"/></svg>
                <span style="margin-left: 8px; font-size: 15px;">Add to Cart</span>
            </button>
        </div>
        @endforeach
    </div>
</div>
<script>
    const btn = document.getElementById('profileBtn');
    const dropdown = document.getElementById('profileDropdown');
    if(btn && dropdown) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function() {
            dropdown.style.display = 'none';
        });
    }
</script>
@endsection
