<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'image'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
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
