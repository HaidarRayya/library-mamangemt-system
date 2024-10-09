<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => asset('storage/' .  $this->book->image),
            'count' => $this->count,
            'price' => $this->price,
            'title' => $this->book->title,
            'author' => $this->book->author,
            'categoryName' => $this->book->category->name
        ];
    }
}