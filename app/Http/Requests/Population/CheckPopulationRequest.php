<?php

namespace App\Http\Requests\Population;

use Illuminate\Foundation\Http\FormRequest;

class CheckPopulationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * ID ของ prefixs ได้จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/prefix.index
             * @var string
             * @example 1
             */
            'prefix_id' => 'required|integer|exists:prefixes,id',
            /**
             * ชื่อ
             * @var string
             * @example มา
             */
            'first_name' => 'required|string|max:255',
            /**
             * นามสกุล
             * @var string
             * @example รวย
             */
            'last_name' => 'required|string|max:255',
        ];
    }
}
