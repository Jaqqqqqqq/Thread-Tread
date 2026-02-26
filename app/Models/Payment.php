<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;















class Payment extends Model
{
    
    
    
    
    
    
    
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    protected $fillable = [
        'order_id',         
        'payment_status',   
        'transaction_ref',  
        'amount',           
        'paid_at',          
    ];

    
    
    
    
    
    
    
    
    
    
    
    
    
    protected $casts = [
        'amount'         => 'decimal:2',
        'paid_at'        => 'datetime',
        'payment_status' => 'string',
    ];

    
    
    

    











    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    
    
    
    

    





    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    




    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    




    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    




    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }

    
    
    

    




    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    



    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    



    public function hasFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    



    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    











    public function markAsPaid(?string $transactionRef = null): bool
    {
        return $this->update([
            'payment_status'  => 'paid',
            'transaction_ref' => $transactionRef,
            'paid_at'         => now(),
        ]);
    }

    




    public function markAsFailed(): bool
    {
        return $this->update([
            'payment_status' => 'failed',
        ]);
    }

    




    public function markAsRefunded(): bool
    {
        return $this->update([
            'payment_status' => 'refunded',
        ]);
    }
}