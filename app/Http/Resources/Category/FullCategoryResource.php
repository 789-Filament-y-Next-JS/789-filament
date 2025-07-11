<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Product\SimpleProductCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'products' => new SimpleProductCollection($this->products),
        ];
    }
}
