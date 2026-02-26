<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    
    
    
    
    
    
    protected $table = 'user_addresses';
    protected $primaryKey = 'address_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    protected $fillable = [
        'user_id',      
        'label',        
        'street',       
        'city',         
        'province',     
        'postal_code',  
        'country',      
        'is_default',   
    ];

    
    
    
    
    protected $casts = [
        'is_default' => 'boolean',
    ];

    
    
    

    



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    
    
    

    



    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    
    
    
    
    
    
    
    
    
    
    

    




    public function getFormattedAddressAttribute(): string
    {
        
        $prefix = $this->label ? "{$this->label}: " : '';

        return "{$prefix}{$this->street}, {$this->city}, "
             . "{$this->province} {$this->postal_code}, {$this->country}";
    }
}