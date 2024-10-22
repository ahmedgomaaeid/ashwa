<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = ['image'];

    public function getImageAttribute($value)
    {
        if (!str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }else
        {
            return $value;
        }
    }
}
