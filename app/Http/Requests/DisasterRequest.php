<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisasterRequest extends FormRequest
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
            'name' => 'required|string|unique:disasters,name|max:255',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'name' => 'required|string|unique:disasters,name,' . $this->route('disaster')->id . '|max:255',
        ];
    }
}
