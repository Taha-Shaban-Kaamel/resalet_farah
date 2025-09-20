<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class sectionContentResource extends JsonResource
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
            'section_key' => $this->section_key,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image_path ? asset($this->image_path) : null,
            'icon' => $this->icon_path ? asset($this->icon_path) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => $this->whenLoaded('children', function () {
                return sectionContentResource::collection($this->children);
            })
        ];
    }
}
