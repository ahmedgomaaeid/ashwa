<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject , FilamentUser
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'type', 'verification_code', 'verified', 'verification_expires_at', 'reset_token', 'reset_token_expires_at'
    ];

    protected $hidden = [
        'password', 'remember_token', 'verification_code', 'verification_expires_at', 'reset_token', 'reset_token_expires_at','email_verified_at'
    ];
    public function canAccessPanel(Panel $panel): bool
    {
        // Check if the user is an admin based on a field like `role` or `type`
        return $this->type == 2;
    }
    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : asset('img/default-user.png');
    }


}

