<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    
    
    
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
    ];

    
    
    

    



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

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    










    public static function withStockInfo()
    {
        return static::withSum('variants', 'stock');
    }

    













    public static function withRatingInfo()
    {
        return static::withAvg('reviews', 'rating');
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