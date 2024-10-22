<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

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
