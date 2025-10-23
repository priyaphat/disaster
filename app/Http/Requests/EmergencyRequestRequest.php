<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmergencyRequestRequest extends FormRequest
{
    public function authorize()
    {
        // Allow all users, adjust as needed
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
            'prefix_id' => 'required|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|digits:10',
            'range_age' => 'required|array',
            'victim_name' => 'required|array',
            'disaster_id' => 'required|integer',
            'description' => 'nullable|string',
            'subdistrict_id' => 'required|integer',
            'district_id' => 'required|integer',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'emergency_team' => 'nullable|array',
            'reason' => 'nullable|nullable|max:255',
            'other' => 'required_if:disaster_id,99|string|max:255'
        ];
    }

    protected function updateRules(): array
    {
        return [
            'prefix_id' => 'required|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|digits:10',
            'range_age' => 'required|array',
            'victim_name' => 'required|array',
            'disaster_id' => 'required|integer',
            'description' => 'nullable|string',
            'subdistrict_id' => 'required|integer',
            'district_id' => 'required|integer',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'emergency_team' => 'array|nullable',
            'emergency_team.*.id' => 'required_with:emergency_team|exists:teams,id',
            'reason' => 'nullable|max:255',
            'other' => 'required_if:disaster_id,99|nullable|string|max:255',
            'hospital_id' => 'required_if:status,7|nullable|exists:hospitals,id'
        ];
    }

    protected function passedValidation()
    {
        if ($this->route()->getActionMethod() === 'update' && !empty($this->emergency_team)) {
            $teams = collect($this->emergency_team)->map(function ($team) {
                return [
                    'team_id' => $team['id'] ?? null,
                    'name' => $team['name'] ?? null,
                ];
            })->toArray();

            $this->merge([
                'emergency_team' => $teams,
            ]);
        }
    }
}
