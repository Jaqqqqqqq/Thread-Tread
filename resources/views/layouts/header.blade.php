<header style="background: #222; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-family: 'Segoe UI', Arial, sans-serif;">
    <div style="display: flex; align-items: center; gap: 30px;">
        <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.products') : route('home') }}" style="text-decoration: none;">
            <h1 style="margin: 0; color: #fff; font-size: 1.6rem; font-weight: normal; font-family: 'Segoe UI', Arial, sans-serif;">Thread & Trend</h1>
        </a>
        <nav style="display: flex; gap: 12px;">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.products') }}" style="background: transparent; color: #fff; border: none; padding: 6px 12px; text-decoration: none; font-weight: normal; font-size: 13px; cursor: pointer;">
                        <span style="margin-right: 4px;">📄</span>PRODUCTS
                    </a>
                    <a href="{{ route('admin.brands') }}" style="background: transparent; color: #fff; border: none; padding: 6px 12px; text-decoration: none; font-weight: normal; font-size: 13px; cursor: pointer;">
                        <span style="margin-right: 4px;">🏷️</span>BRANDS
                    </a>
                    <a href="{{ route('admin.categories') }}" style="background: transparent; color: #fff; border: none; padding: 6px 12px; text-decoration: none; font-weight: normal; font-size: 13px; cursor: pointer;">
                        <span style="margin-right: 4px;">📂</span>CATEGORIES
                    </a>
                    <a href="{{ route('admin.users') }}" style="background: transparent; color: #fff; border: none; padding: 6px 12px; text-decoration: none; font-weight: normal; font-size: 13px; cursor: pointer;">
                        <span style="margin-right: 4px;">👥</span>USERS
                    </a>
                    <a href="{{ route('admin.orders') }}" style="background: transparent; color: #fff; border: none; padding: 6px 12px; text-decoration: none; font-weight: normal; font-size: 13px; cursor: pointer;">
                        <span style="margin-right: 4px;">📦</span>ORDERS
                    </a>
                    <a href="{{ route('admin.reviews') }}" style="background: transparent; color: #fff; border: none; padding: 6px 12px; text-decoration: none; font-weight: normal; font-size: 13px; cursor: pointer;">
                        <span style="margin-right: 4px;">⭐</span>REVIEWS
                    </a>
                @else
                    <a href="{{ route('home') }}" style="color: #fff; text-decoration: none; font-size: 13px; font-weight: normal; padding: 6px 12px;">
                        <span style="margin-right: 4px;">🏠</span>HOME
                    </a>
                    <a href="{{ route('products.index') }}" style="color: #fff; text-decoration: none; font-size: 13px; font-weight: normal; padding: 6px 12px;">
                        <span style="margin-right: 4px;">📄</span>PRODUCTS
                    </a>
                    <a href="{{ route('cart.index') }}" style="color: #fff; text-decoration: none; font-size: 13px; font-weight: normal; padding: 6px 12px;">
                        <span style="margin-right: 4px;">🛒</span>CART
                    </a>
                    <a href="{{ route('orders.mine') }}" style="color: #fff; text-decoration: none; font-size: 13px; font-weight: normal; padding: 6px 12px;">
                        <span style="margin-right: 4px;">📦</span>MY ORDERS
                    </a>
                @endif
            @endauth
        </nav>
    </div>
    <div style="position: relative;">
        @auth
            <button id="profileBtn" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 13px; padding: 6px 12px; display: flex; align-items: center; gap: 8px; font-weight: normal;">
                <span>👤</span> {{ Auth::user()->fname }} <span>▼</span>
            </button>
            <div id="profileDropdown" style="display: none; position: absolute; top: 40px; right: 0; background: #fff; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; z-index: 100; min-width: 150px;">
                <a href="{{ route('profile.show') }}" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 13px; font-weight: normal; border-bottom: 1px solid #eee; transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent';">👤 My Profile</a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" style="width: 100%; text-align: left; padding: 12px 16px; border: none; background: none; color: #333; cursor: pointer; font-size: 13px; font-weight: normal; transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent';">🚪 Logout</button>
                </form>
            </div>
        @endauth
    </div>
</header>

<script>
    document.getElementById('profileBtn').addEventListener('click', function() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    document.addEventListener('click', function(event) {
        const profileBtn = document.getElementById('profileBtn');
        const dropdown = document.getElementById('profileDropdown');
        if (profileBtn && dropdown && !event.target.closest('[id="profileBtn"], [id="profileDropdown"]')) {
            dropdown.style.display = 'none';
        }
    });
</script>
