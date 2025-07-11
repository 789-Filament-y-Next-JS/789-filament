<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


class Category extends Model
{
    protected $fillable = [
        "name",
        "slug",
        "summary",
    ];

    /**
     * Get the products associated with the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::creating(function ($category){
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category){
            $category->slug = Str::slug($category->name);
        });
    }
}
