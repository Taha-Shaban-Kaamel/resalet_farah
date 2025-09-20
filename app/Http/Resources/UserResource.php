<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->resource->id,
            "name"=>$this->resource->name ,
            "email"=>$this->resource->email,
            "roles" => $this->whenLoaded('roles',function(){
                return $this->resource->roles->pluck('name');
            }),
            "created_at" => $this->resource->created_at->diffForHumans(),
            "updated_at" => $this->resource->updated_at->diffForHumans()
        ];
    }
}
