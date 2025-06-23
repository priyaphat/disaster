<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class FilterTeamRequest extends FormRequest
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
             * ค้นหา
             * @var string
             * @example "ทีมงาน"
             */
            'search' => 'nullable',
             /**
             * ถ้าส่งตัวแปร paginate มาจะเป็น paginate ถ้าไม่ส่งจะเป็น list
             * @var integer
             * @example 10
             */
            'paginate' => 'nullable',
        ];
    }
}
