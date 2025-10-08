<?php

namespace App\Http\Resources;

use App\Enums\ProductGroup;
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
            'unit_name' => $this->unit_name,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'group_id' => $this->group_id,
            'group_name' => ProductGroup::tryFrom($this->group_id)->label(),
            'source' => $this->source,
            'detail' => $this->detail,
            'quantity' => $this->quantity,
            'unit_type' => $this->unit_type,
            'status' => $this->status,
            'image' =>  $this->when($this->image, $this->image?->image_path),
        ];
    }
}
