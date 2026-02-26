<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    
    
    
    
    protected $fillable = [
        'order_id',     
        'variant_id',   
        'oi_quantity',  
        'oi_price',     
    ];

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    protected $casts = [
        'oi_price'    => 'decimal:2',
        'oi_quantity' => 'integer',
    ];

    
    
    

    



    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    











    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    
    
    

    
















    public function subtotal(): float
    {
        
        
        
        return (float) ($this->oi_quantity * (float) $this->oi_price);
    }
}