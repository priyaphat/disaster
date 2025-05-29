<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\EmergencyPost;

class EmergencyPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->route()->getActionMethod();
        return match ($action) {
            'store' => $this->storeRules(),
            'update' => $this->updateRules(),
            default => [],
        };
    }

    protected function storeRules(): array
    {
        return [
            'disaster_id' => [
                'required',
                'exists:disasters,id',
                function ($attribute, $value, $fail) {
                    $exists = EmergencyPost::where('disaster_id', $value)
                        ->where('status', EmergencyPost::active)
                        ->exists();

                    if ($exists) {
                        $fail('มีการประกาศภัยพิบัติซ้ำอยู่แล้ว');
                    }
                },
            ],
            'area' => 'required|array',
            'area.*.district_id' => 'required|exists:geography,district_code',
            'level' => 'required|in:1,2,3,4,5',
            'province_id' => 'required|exists:geography,province_code',
            'gis_map' => 'nullable|string',
            'status' => 'nullable|in:0,1',
            'line_oa' => 'nullable|in:0,1',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'disaster_id' => [
                'required',
                'exists:disasters,id',
                function ($attribute, $value, $fail) {
                    $exists = EmergencyPost::where('disaster_id', $value)
                        ->where('status', EmergencyPost::active)
                        ->where('id', '!=', $this->route('post')->id)
                        ->exists();

                    if ($exists) {
                        $fail('มีการประกาศภัยพิบัติซ้ำอยู่แล้ว');
                    }
                },
            ],
            'area' => 'required|array',
            'area.*.district_id' => 'required|exists:geography,district_code',
            'level' => 'required|in:1,2,3,4,5',
            'province_id' => 'required|exists:geography,province_code',
            'gis_map' => 'nullable|string',
            'status' => 'nullable|in:0,1',
            'line_oa' => 'nullable|in:0,1',
        ];
    }
}
