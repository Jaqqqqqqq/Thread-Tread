<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'wishlist_items';
    protected $primaryKey = 'wishlist_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    protected $fillable = [
        'wishlist_id',  
        'product_id',   
    ];

    
    
    
    
    

    
    
    

    






    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class, 'wishlist_id', 'wishlist_id');
    }

    






    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    
    
    
    
    
    

    










    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }
}