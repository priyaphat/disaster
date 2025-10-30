<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            //
            'type' => 'required',
            'date' => 'required_if:type,1',
            'month' => 'required_if:type,1,2',
            'year' => 'required_if:type,1,2,3,4',
            'start_month' => 'required_if:type,3|integer|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'end_month' => 'required_if:type,3|integer|in:1,2,3,4,5,6,7,8,9,10,11,12|gte:start_month',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => __('validation.attribute.report_type')
        ];
    }
}
