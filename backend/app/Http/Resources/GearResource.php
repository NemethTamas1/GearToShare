<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GearResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "title" => $this->title,
            "description" => $this->description,
            "type" => $this->type,
            "price_per_day" => $this->price_per_day,
            "city" => $this->city,
            "address" => $this->address,
            "image_path" => $this->image_path,
            "status" => $this->status,
        ];
    }
}
