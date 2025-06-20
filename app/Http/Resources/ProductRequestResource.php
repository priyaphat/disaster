<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRequestResource extends JsonResource
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
            'name' => $this->product->name,
            'unit_id' => $this->product->unit_id,
            'unit_name' => $this->when($this->product->unit, $this->product->unit?->name),
            'category_id' => $this->product->category_id,
            'category_name' => $this->when($this->product->category, $this->product->category?->name),
            'detail' => $this->product->detail,
            'quantity' => $this->quantity,
            'no_quantity' => $this->no_quantity,
            'status' => $this->status,
            'image' =>  $this->when($this->product->image, $this->product->image?->image_path),
        ];
    }
}
