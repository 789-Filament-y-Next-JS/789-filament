<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\SimpleProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        
        // return response()->json([
        //     "products" => $products,
        // ]);

        return new SimpleProductCollection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->first();

        if(!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        return new ProductResource($product);
    }

}
