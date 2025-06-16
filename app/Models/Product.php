<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected static function booted()
    {
        static::creating(function ($product){
            $product->slug = Str::slug($product->name);
        });

        static::updating(function ($product){
            $product->slug = Str::slug($product->name);
        });
    }

    protected $fillable = [
        "name",
        "slug",
        "summary",
        "description",
        "price",
        "code",
        "image",
        "is_active",
        "category_id",
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

}
