<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public $timestamps = false;
    /**
     * ===================================================
     * Table Configuration
     * ===================================================
     * Custom table name, primary key, and incrementing
     * to match our migration schema.
     */
    protected $table = 'product_images';
    protected $primaryKey = 'image_id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * ===================================================
     * Mass Assignable Fields
     * ===================================================
     * These are the only fields that can be filled
     * using Product::create([...]) or $product->fill([...]).
     */
    protected $fillable = [
        'product_id',   // FK to products table
        'image_path',   // File path of the uploaded image
        'sort_order',   // Display order (0 = primary/first)
    ];

    /**
     * ===================================================
     * Type Casting
     * ===================================================
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * ===================================================
     * Relationships
     * ===================================================
     */

    /**
     * The product this image belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}