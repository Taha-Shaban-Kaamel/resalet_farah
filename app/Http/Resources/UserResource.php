<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

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
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'roles_permissions' => $this->whenLoaded('roles', function () {
                return [
                    'roles' => $this->resource->roles->pluck('name'),
                    'permissions' => $this->getAllPermissions()->pluck('name')
                ];
            }),
            'title' => $this->resource->title ?? null,
            'phone' => $this->resource->phone ?? null,
            'address' => $this->resource->address ?? null,
            'image' => $this->resource->image ? asset('storage/app/public' . '/' . $this->resource->image) : null,
            'created_at' => $this->resource->created_at->diffForHumans(),
            'updated_at' => $this->resource->updated_at->diffForHumans()
        ];
    }
}
