<?php

namespace App\Http\Requests\ProductRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductReqRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|in:0,1',
            'request' => 'required_if:status,1|nullable|min:-1',
            'quantity' => 'nullable|min:0'
        ];
    }
}
