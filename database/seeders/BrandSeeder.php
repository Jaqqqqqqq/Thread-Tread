<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'brand_name'  => 'Nike',
                'description' => 'Just Do It — global sportswear brand.',
                'brand_logo'  => null,
                'is_active'   => true,
            ],
            [
                'brand_name'  => 'Adidas',
                'description' => 'Impossible is Nothing — performance apparel.',
                'brand_logo'  => null,
                'is_active'   => true,
            ],
            [
                'brand_name'  => 'Penshoppe',
                'description' => 'Popular Filipino fashion brand.',
                'brand_logo'  => null,
                'is_active'   => true,
            ],
            [
                'brand_name'  => 'Bench',
                'description' => 'Proudly Filipino lifestyle brand.',
                'brand_logo'  => null,
                'is_active'   => true,
            ],
            [
                'brand_name'  => 'Uniqlo',
                'description' => 'LifeWear — simple, high-quality everyday clothing.',
                'brand_logo'  => null,
                'is_active'   => false, // Inactive brand for admin toggle testing
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}