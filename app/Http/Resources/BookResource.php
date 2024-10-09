<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'author' => $this->author,
            'image' => asset('storage/' .  $this->image),
            'published_at' => $this->published_at,
            'count' => $this->count,
            'price' => $this->price,
            'categoryName' => $this->category->name
        ];
    }
}
