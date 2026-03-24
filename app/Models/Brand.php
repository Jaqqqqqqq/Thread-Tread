<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $timestamps = false;
    
    
    
    
    
    
    
    
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    protected $fillable = [
        'name',   
        'brand_name',   
        'brand_logo',   
        'description',  
        'is_active',    
    ];

    
    
    
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getNameAttribute()
    {
        return $this->brand_name ?? $this->attributes['name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['brand_name'] = $value;
    }

    
    
    

    





    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }

    
    
    

    




    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}