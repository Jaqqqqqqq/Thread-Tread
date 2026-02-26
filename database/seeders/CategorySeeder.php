<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'T-Shirts',
                'description'   => 'Casual and graphic tees for everyday wear.',
            ],
            [
                'category_name' => 'Polo Shirts',
                'description'   => 'Smart casual polo shirts for men and women.',
            ],
            [
                'category_name' => 'Jackets',
                'description'   => 'Outerwear jackets for all weather conditions.',
            ],
            [
                'category_name' => 'Jeans',
                'description'   => 'Denim jeans in various cuts and washes.',
            ],
            [
                'category_name' => 'Shorts',
                'description'   => 'Casual and athletic shorts.',
            ],
            [
                'category_name' => 'Dresses',
                'description'   => 'Casual and formal dresses for women.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}