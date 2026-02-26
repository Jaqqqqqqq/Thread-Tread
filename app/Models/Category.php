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
        'category_name',    
        'description',      
    ];

    
    
    
    
    
    

    
    
    

    






    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    
    
    

    
    

    
    
    
    
    

    









    public function productCount(): int
    {
        return $this->products()->count();
    }
}