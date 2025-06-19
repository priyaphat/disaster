<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'unit_id' => $this->unit_id,
            'unit_name' => $this->when($this->unit, $this->unit?->name),
            'category_id' => $this->category_id,
            'category_name' => $this->when($this->category, $this->category?->name),
            'detail' => $this->detail,
            'quantity' => $this->quantity,
            'no_quantity' => $this->no_quantity,
            'status' => $this->status,
            'image' =>  $this->when($this->image, $this->image?->image_path),
        ];
    }
}
