<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\FullCategoryResource;
use App\Http\Resources\Category\SimpleCategoryCollection;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(5);

        return new SimpleCategoryCollection(
            $categories
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return new FullCategoryResource(
            $category
        );
    }
}
