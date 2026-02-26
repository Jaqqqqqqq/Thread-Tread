<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    protected $fillable = [
        'brand_name',   
        'brand_logo',   
        'description',  
        'is_active',    
    ];

    
    
    
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    
    
    

    





    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }

    
    
    

    




    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}