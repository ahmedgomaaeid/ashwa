<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'trx',
        'user_id',
        'order_id',
        'payment_method',
        'status',
        'amount',
        'notes',
    ];
}
