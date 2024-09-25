<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'type', 'verification_code', 'verified', 'verification_expires_at', 'reset_token', 'reset_token_expires_at'
    ];

    protected $hidden = [
        'password', 'remember_token', 'verification_code', 'verified', 'verification_expires_at', 'reset_token', 'reset_token_expires_at','email_verified_at'
    ];

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
}

