<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShelterProductRequest extends FormRequest
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
            default => [],
        };
    }

    protected function storeRules(): array
    {
        return [
            'product_id' => 'required|unique:products,id',
            'shelter_id' => 'required|unique:shelters,id',
            'quantity' => 'required|numeric|min:0',
        ];
    }
}
