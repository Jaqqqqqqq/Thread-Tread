<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    
    
    
    
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    protected $fillable = [
        'name',
        'category_name',    
        'description',      
    ];

    public function getNameAttribute()
    {
        return $this->category_name ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['category_name'] = $value;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function productCount(): int
    {
        return $this->products()->count();
    }
}