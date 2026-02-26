<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'cart_items';
    protected $primaryKey = 'cart_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    protected $fillable = [
        'cart_id',      
        'variant_id',   
        'quantity',     
    ];

    
    
    
    
    
    
    
    
    
    
    
    
    protected $casts = [
        'quantity' => 'integer',
    ];

    
    
    

    



    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    






    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    
    
    

    



















    public function subtotal(): float
    {
        
        $price = $this->variant?->product?->price ?? 0;

        
        return (float) ($this->quantity * (float) $price);
    }
}