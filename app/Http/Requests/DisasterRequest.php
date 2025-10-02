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
            'dst_eq_update' => $this->earthquakeUpdate(),
            'dst_eq_create' => $this->earthquakeCreate(),
            'dst_fl_create' => $this->floodCreate(),
            'dst_fl_update' => $this->floodUpdate(),
            'dst_ls_create' => $this->landsildeCreate(),
            'dst_wf_create' => $this->wildfireCreate(),
            'dst_sm_update' => $this->smogUpdate(),
            'dst_sm_create' => $this->smogCreate(),
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

    protected function earthquakeUpdate(): array
    {
        return [
            'address' => 'required|string|max:255',
            'latitude' => ['required', 'regex:/^-?\d{1,4}(\.\d{1,6})?$/'],
            'longitude' => ['required', 'regex:/^-?\d{1,4}(\.\d{1,6})?$/'],
            'depth' => 'required',
            'magnitude' => 'required',
            'occurrence_time' => 'required',
            'geography_id' => 'required',
            'line_oa' => 'required|in:0,1',
        ];
    }

    protected function earthquakeCreate(): array
    {
        // return [
        //     'latitude' => ['required', 'regex:/^-?\d{1,4}(\.\d{1,6})?$/'],
        //     'longitude' => ['required', 'regex:/^-?\d{1,4}(\.\d{1,6})?$/'],
        //     'depth' => 'required',
        //     'magnitude' => 'required',
        //     'line_oa' => 'required|in:0,1',
        // ];
        return [
            'address' => 'required|string|max:255',
            'latitude' => ['required', 'regex:/^-?\d{1,4}(\.\d{1,6})?$/'],
            'longitude' => ['required', 'regex:/^-?\d{1,4}(\.\d{1,6})?$/'],
            'depth' => 'required',
            'magnitude' => 'required',
            'occurrence_time' => 'required',
            'geography_id' => 'required',
            'line_oa' => 'required|in:0,1',
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
        ];
    }

    protected function floodCreate(): array
    {
        return [
            'address' => 'required|string|max:255',
            'water_source' => 'required|string|max:255',
            'water_source_type' => 'required|string|max:255',
            'geography_id' => 'required',
            'status' => 'required|in:0,1,2,3,4,5',
            'water_value' => 'required|decimal:1',
            'host' => 'max:255',
            'line_oa' => 'required|in:0,1',
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
        ];
    }

    protected function floodUpdate(): array
    {
        return [
            'address' => 'required|string|max:255',
            'water_source' => 'required|string|max:255',
            'water_source_type' => 'required|string|max:255',
            'status' => 'required|in:0,1,2,3,4,5',
            'water_value' => 'required|decimal:1',
            'line_oa' => 'required|in:0,1',
            'host' => 'max:255'
        ];
    }

    protected function landsildeCreate(): array
    {
        return [
            'name' => 'required|string|max:255',
            'line_oa' => 'required|in:0,1',
            'file' => 'required|file|max:10240',
        ];
    }

    protected function wildfireCreate(): array
    {
        $type = $this->input('type');
        $rules = [
            'type' => 'required|integer|in:1,2,3,4',
            'line_oa' => 'required|boolean|in:0,1',
            'host' => 'max:255'
        ];
        if ($type == 2) {
            $rules['model'] = 'required|array';
            $rules['model.*.district_id'] = 'nullable';
            $rules['model.*.district_name'] = 'nullable|string';
            // $rules['model.*.modis'] = 'required|numeric';
            $rules['model.*.viirs'] = 'nullable|numeric';
        }
        if ($type == 1) {
            $rules['model'] = 'required|array';
            $rules['model.*'] = 'required|numeric';
        }
        return $rules;
    }

    protected function smogUpdate(): array
    {
        return [
            "status" => 'required|integer|in:1,2,3',
            "smog_value" => 'required|regex:/^\d+(\.\d{1})?$/',
            "line_oa" => 'required|in:0,1'
        ];
    }

    protected function smogCreate(): array
    {
        return [
            "source" => 'required|string|max:255',
            "address" => 'required|string|max:255',
            'geography_id' => 'required',
            "status" => 'required|integer|in:1,2,3',
            "smog_value" => 'required|regex:/^\d+(\.\d{1})?$/',
            "line_oa" => 'required|in:0,1',
            'province_id' => 'required',
            'district_id' => 'required',
            'subdistrict_id' => 'required',
        ];
    }
}
