<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Category extends Model
{
    protected $fillable = [
        "name",
        "slug",
        "summary",
    ];

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
