<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'wishlists';
    protected $primaryKey = 'wishlist_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    protected $fillable = [
        'user_id',  
    ];

    
    
    
    
    

    
    
    

    



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    






    public function items()
    {
        return $this->hasMany(WishlistItem::class, 'wishlist_id', 'wishlist_id');
    }

    
    
    
    
    

    












    public function itemCount(): int
    {
        
        if (array_key_exists('items_count', $this->getAttributes())) {
            return (int) ($this->items_count ?? 0);
        }

        return $this->items()->count();
    }

    

















    public function hasProduct(int $productId): bool
    {
        
        
        if ($this->relationLoaded('items')) {
            return $this->items
                ->contains('product_id', $productId);
        }

        
        return $this->items()
            ->where('product_id', $productId)
            ->exists();
    }
}