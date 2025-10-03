<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardOfDirctorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'position' => $this->resource->position,
            'image_path' => asset('storage/' . $this->resource->image_path),
            'description' => $this->resource->description,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
        ];
    }
}
