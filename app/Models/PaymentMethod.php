<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    
    
    
    
    
    
    
    protected $table = 'payment_methods';
    protected $primaryKey = 'method_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    protected $fillable = [
        'method_name',  
        'is_active',    
    ];

    
    
    
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    
    
    

    











    public function orders()
    {
        return $this->hasMany(Order::class, 'method_id', 'method_id');
    }

    
    
    

    




    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    

    






    public static function withOrderCount()
    {
        return static::withCount('orders');
    }

    









    public static function withRevenueInfo()
    {
        return static::withSum('orders', 'total_amount');
    }

    
    
    

    









    public function orderCount(): int
    {
        
        
        if (array_key_exists('orders_count', $this->getAttributes())) {
            return (int) ($this->orders_count ?? 0);
        }

        return $this->orders()->count();
    }

    









    public function totalRevenue(): float
    {
        
        
        if (array_key_exists('orders_sum_total_amount', $this->getAttributes())) {
            return (float) ($this->orders_sum_total_amount ?? 0);
        }

        return (float) $this->orders()->sum('total_amount');
    }

    












    public function canBeDeleted(): bool
    {
        return !$this->orders()->exists();
    }
}