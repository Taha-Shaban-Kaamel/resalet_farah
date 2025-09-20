<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "permissions" => $this->whenLoaded('permissions', fn() => $this->permissions->pluck('name')),
            "created_at" => optional($this->created_at)->diffForHumans(),
            "updated_at" => optional($this->updated_at)->diffForHumans(),
        ];
    }
}
