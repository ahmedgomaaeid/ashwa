<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'order_price',
        'shipping_price',
        'shipping_address',
        'contact_number',
        'note',
        'payment_method',
        'trx',
    ];

    // Create unique trx when adding new order
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->trx = uniqid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
