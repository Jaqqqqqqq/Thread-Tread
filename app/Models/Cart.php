<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'carts';
    protected $primaryKey = 'cart_id';
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
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    
    
    

    



















    public function calculateTotal(): float
    {
        return (float) $this->items->sum(function ($item) {
            
            
            $price = $item->variant?->product?->price ?? 0;

            return $item->quantity * (float) $price;
        });
    }

    









    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }
}