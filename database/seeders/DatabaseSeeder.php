<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // ORDER IS CRITICAL — respects FK constraints:
        //
        // 1.  Users              (no FK dependencies)
        // 2.  Brands             (no FK dependencies)
        // 3.  Categories         (no FK dependencies)
        // 4.  PaymentMethods     (no FK dependencies)
        // 5.  Products           (needs categories, brands)
        // 6.  ProductVariants    (needs products)
        // 7.  Carts              (needs users)
        // 8.  CartItems          (needs carts, product_variants)
        // 9.  Wishlists          (needs users)
        // 10. WishlistItems      (needs wishlists, products)
        // 11. Orders             (needs users, payment_methods)
        // 12. Payments           (needs orders)
        // 13. OrderItems         (needs orders, product_variants)
        // 14. Reviews            (needs users, products)
        // 15. UserAddresses      (needs users)
        // =====================================================
        $this->call([
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            PaymentMethodSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            CartSeeder::class,
            CartItemSeeder::class,
            WishlistSeeder::class,
            WishlistItemSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            OrderItemSeeder::class,
            ReviewSeeder::class,
            UserAddressSeeder::class,
        ]);
    }
}
