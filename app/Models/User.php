<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    
    
    
    
    
    
    
    
    
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    
    
    
    
    
    
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
    ];

    
    
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    
    
    
    
    
    
    
    
    
    
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'deleted_at'        => 'datetime',
        'role'              => 'string',
    ];

    
    
    

    



    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    



    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    




    public function wishlist()
    {
        return $this->hasOne(Wishlist::class, 'user_id', 'user_id');
    }

    



    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'user_id');
    }

    




    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id', 'user_id');
    }

    
    
    

    



    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    



    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    



    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    
    
    
    
    
    

    






    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->fname} {$this->lname}",
        );
    }

    




    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}