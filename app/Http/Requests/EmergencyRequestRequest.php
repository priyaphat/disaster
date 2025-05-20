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

    // public function rules(): array
    // {
    //     // Define validation rules for the emergency request
    //     return [
    //         'prefix_id' => 'required|integer',
    //         'first_name' => 'required|string|max:100',
    //         'last_name' => 'required|string|max:100',
    //         'phone' => 'required|string|max:10',
    //         'range_age' => 'nullable',
    //         'victim_name' => 'nullable',
    //         'help_id' => 'required|integer',
    //         'description' => 'nullable|string',
    //         'tambon' => 'required|integer',
    //         'address' => 'nullable|string|max:255',
    //         'status' => 'nullable|integer',
    //         'emergency_team_id' => 'nullable|integer',
    //     ];
    // }

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
            'phone' => 'required|string|max:10',
            'range_age' => 'required|array',
            'victim_name' => 'required|array',
            'help_id' => 'required|integer',
            'description' => 'nullable|string',
            'tambon' => 'required|integer',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'emergency_team_id' => 'nullable|integer',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'prefix_id' => 'required|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:10',
            'range_age' => 'required|array',
            'victim_name' => 'required|array',
            'help_id' => 'required|integer',
            'description' => 'nullable|string',
            'tambon' => 'required|integer',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'emergency_team_id' => 'nullable|integer',
        ];
    }
}
