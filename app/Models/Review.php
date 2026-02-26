<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    
    
    
    
    
    
    
    
    protected $table = 'reviews';
    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    protected $fillable = [
        'user_id',      
        'product_id',   
        'rating',       
        'comment',      
    ];

    
    
    
    
    
    protected $casts = [
        'rating' => 'integer',
    ];

    
    
    

    









    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    






    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    
    
    

    












    public function scopeWithRating($query, int $rating)
    {
        
        
        
        $rating = max(1, min(5, $rating));

        return $query->where('rating', $rating);
    }

    










    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}