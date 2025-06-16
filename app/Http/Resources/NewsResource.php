<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'description' => $this->description,
            'image_name' => $this->when($this->image, $this->image?->image_name),
            'image_url' => $this->when($this->image, $this->image?->image_url),
            'image' => $this->when($this->image, $this->image?->image_path),
            'line_oa' => $this->line_oa,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'thai_created_at' =>$this->when($this->created_at, $this->thai_created_at),
            'created_name' => $this->when($this->relationLoaded('creator'),fn() => $this->creator->firstname.' '.$this->creator->lastname ),
            'updated_name' => $this->when($this->relationLoaded('editor'),fn() => $this->editor->firstname.' '.$this->editor->lastname),
        ];
    }
}
