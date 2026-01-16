<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Integer;

class AssessmentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $detail = $this->detail;

        $assessment = $detail['assessment'] ?? null;

        if (is_array($assessment)) {
            $assessment = array_merge($assessment, [
                'p_postal_code'      => isset($assessment['p_postal_code'])
                    ? (int) $assessment['p_postal_code']
                    : null,

                'p_geography_id'     => isset($assessment['p_geography_id'])
                    ? (int) $assessment['p_geography_id']
                    : null,

                'p_district_code'    => isset($assessment['p_district_code'])
                    ? (int) $assessment['p_district_code']
                    : null,

                'p_province_code'    => isset($assessment['p_province_code'])
                    ? (int) $assessment['p_province_code']
                    : null,

                'p_subdistrict_code' => isset($assessment['p_subdistrict_code'])
                    ? (int) $assessment['p_subdistrict_code']
                    : null,
            ]);

            $detail['assessment'] = $assessment;
        }

        return [
            'id' => $this->id,
            'population_id' => $this->population_id,
            'shelter_id' => $this->shelter_id,
            'name' => $this->name,
            'type' => $this->type,
            'detail' =>  $detail ?? null,
            'datetime' =>  $this->datetime ?? null,
            'team_id' => $this->team_id ?? null,
            'team_name' => $this->team_name ?? null,
            'count' => $this->count ?? 0,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
            'created_by' => $this->created_by ?? null,
            'updated_by' => $this->updated_by ?? null,
            'created_name' => $this->created_name ?? null,
            'updated_name' => $this->updated_name ?? null,
            'name_of_assessment' => $this->name_of_assessment ?? null,
            // 'image_name' => $this->when($this->image, $this->image?->original_name ?? $this->image?->image_name),
            // 'image_url' => $this->when($this->image, $this->image?->image_url),
            'image_size' => $this->when($this->image, $this->image?->image_size),
            'image' => $this->when($this->image, $this->image?->image_path),
            'download' => [
                'pdf_download' => url('api/download-assessment/' . $this->id),
                'image_download' => $this->when($this->image, url('api/' . $this->image?->image_url))
            ]
        ];
    }
}
