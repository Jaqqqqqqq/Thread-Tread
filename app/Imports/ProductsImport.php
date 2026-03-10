<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Brand;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * Import a single row of data from the Excel file
     * 
     * Expected columns:
     * - product_name (required)
     * - prod_desc
     * - price (required)
     * - brand_name
     * - category_name (required)
     * - prod_image (file path or URL)
     * 
     * @param array $row
     * @return Product|null
     */
    public function model(array $row)
    {
        try {
            // Skip rows with missing required fields
            if (empty($row['product_name']) || empty($row['price']) || empty($row['category_name'])) {
                Log::warning('Skipped row with missing required fields', $row);
                return null;
            }

            // Get or find the category
            $category = Category::where('category_name', trim($row['category_name']))->first();
            if (!$category) {
                Log::warning('Category not found', ['category_name' => $row['category_name']]);
                return null;
            }

            // Get or find the brand (optional)
            $brand_id = null;
            if (!empty($row['brand_name'])) {
                $brand = Brand::where('brand_name', trim($row['brand_name']))->first();
                if ($brand) {
                    $brand_id = $brand->brand_id;
                } else {
                    Log::warning('Brand not found', ['brand_name' => $row['brand_name']]);
                }
            }

            // Create the product
            $product = Product::create([
                'product_name' => trim($row['product_name']),
                'prod_desc' => !empty($row['prod_desc']) ? trim($row['prod_desc']) : null,
                'price' => (float) $row['price'],
                'brand_id' => $brand_id,
                'category_id' => $category->category_id,
                'prod_image' => null, // Will be set from gallery images if available
            ]);

            // Handle product image if provided
            if (!empty($row['prod_image'])) {
                $this->handleProductImage($product, $row['prod_image']);
            }

            Log::info('Product imported successfully', ['product_id' => $product->product_id, 'product_name' => $product->product_name]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Error importing product', ['row' => $row, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Handle product image - converts file path to a local image
     * For simplicity, this just stores the path. In production, you might want to
     * download remote images.
     * 
     * @param Product $product
     * @param string $imagePath
     */
    private function handleProductImage(Product $product, string $imagePath)
    {
        try {
            // Check if it's a local file that exists
            if (file_exists($imagePath) && is_file($imagePath)) {
                // Copy the file to the products folder
                $filename = time() . '_' . uniqid() . '.' . pathinfo($imagePath, PATHINFO_EXTENSION);
                $destination = public_path('images/products/gallery/' . $filename);
                
                // Ensure directory exists
                if (!is_dir(public_path('images/products/gallery'))) {
                    mkdir(public_path('images/products/gallery'), 0755, true);
                }
                
                if (copy($imagePath, $destination)) {
                    $storagePath = 'images/products/gallery/' . $filename;
                    $product->update(['prod_image' => $storagePath]);
                    
                    // Also add to product_images gallery
                    ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path' => $storagePath,
                        'sort_order' => 0,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not handle product image', ['path' => $imagePath, 'error' => $e->getMessage()]);
        }
    }
}
