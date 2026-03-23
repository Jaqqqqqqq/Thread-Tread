<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use SoftDeletes, Searchable;
    public $timestamps = false;

    // ... everything else stays the same
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'category_id',
        'brand_id',
        'product_name',
        'prod_desc',
        'price',
        'prod_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // ===== Relationships (unchanged) =====

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    public function orderItems()
    {
        return $this->hasManyThrough(
            OrderItem::class,
            ProductVariant::class,
            'product_id',
            'variant_id',
            'product_id',
            'variant_id'
        );
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class, 'product_id', 'product_id');
    }

    // NEW: relationship for multiple product images
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    // ===== Scout Search Configuration =====

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray()
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'prod_desc' => $this->prod_desc,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'price' => $this->price,
        ];
    }

    // ===== Scopes (unchanged) =====

    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, int $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeUnbranded($query)
    {
        return $query->whereNull('brand_id');
    }

    // ===== SEARCH SCOPES (NEW) =====

    // 8pts: LIKE query search
    public function scopeSearchLike($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }
        
        return $query->where('product_name', 'LIKE', "%{$search}%")
                     ->orWhere('prod_desc', 'LIKE', "%{$search}%");
    }

    // 10pts: Model-based search (scope)
    public function scopeSearchByName($query, ?string $searchQuery)
    {
        if (!$searchQuery) {
            return $query;
        }

        return $query->where('product_name', 'LIKE', "%{$searchQuery}%")
                     ->orWhere('prod_desc', 'LIKE', "%{$searchQuery}%")
                     ->orderByRaw("
                         CASE 
                             WHEN product_name LIKE ? THEN 1
                             ELSE 2
                         END
                     ", ["%{$searchQuery}%"]);
    }

    public function scopeWithStockInfo($query)
    {
        return $query->withSum('variants', 'stock');
    }

    public function scopeWithRatingInfo($query)
    {
        return $query->withAvg('reviews', 'rating');
    }

    public function totalStock(): int
    {
        if (array_key_exists('variants_sum_stock', $this->getAttributes())) {
            return (int) ($this->variants_sum_stock ?? 0);
        }

        return (int) $this->variants()->sum('stock');
    }

    public function inStock(): bool
    {
        return $this->totalStock() > 0;
    }

    public function averageRating(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->getAttributes())) {
            return round((float) ($this->reviews_avg_rating ?? 0), 1);
        }

        return round((float) ($this->reviews()->avg('rating') ?? 0), 1);
    }
}