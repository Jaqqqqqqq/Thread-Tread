<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    protected $fillable = [
        'user_id',           
        'method_id',         
        'order_status',      
        'shipping_address',  
        'total_amount',      
    ];

    
    
    
    
    
    
    
    
    
    
    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_status' => 'string',
    ];

    
    
    

    






    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    







    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id', 'method_id');
    }

    







    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    





    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    
    
    

    








    public function scopeWithStatus($query, string $status)
    {
        
        $allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $allowed, strict: true)) {
            
            
            return $query->whereRaw('0 = 1');
        }

        return $query->where('order_status', $status);
    }

    




    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    
    
    
    
    
    

    





    public function isCancellable(): bool
    {
        return in_array($this->order_status, ['pending', 'processing'], strict: true);
    }

    





    public function isShipped(): bool
    {
        return $this->order_status === 'shipped';
    }

    






    public function isCompleted(): bool
    {
        return $this->order_status === 'completed';
    }

    






    public function isCancelled(): bool
    {
        return $this->order_status === 'cancelled';
    }
}