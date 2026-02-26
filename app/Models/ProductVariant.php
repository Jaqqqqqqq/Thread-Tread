<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    
    
    
    protected $table = 'product_variants';
    protected $primaryKey = 'variant_id';

    
    
    
    
    
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'stock',
    ];

    
    
    
    
    
    
    

    




    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    



    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id', 'variant_id');
    }

    



    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id', 'variant_id');
    }

    
    
    

    




    public function scopeAvailableStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    



    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }
}