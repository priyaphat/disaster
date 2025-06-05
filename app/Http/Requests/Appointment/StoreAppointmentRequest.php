<?php

namespace App\Http\Requests\Appointment;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'appointment_date' => 'required|date|date_format:d-m-Y',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'shelter_id' => 'required|exists:shelters,id',
            'team_id' => 'required|exists:teams,id'
        ];
    }

    public function normalized(): array
    {
        $date = $this->input('appointment_date');
        $start = $this->input('start_time');
        $end = $this->input('end_time');

        $startTime = Carbon::createFromFormat('d-m-Y H:i', "$date $start");
        $endTime = Carbon::createFromFormat('d-m-Y H:i', "$date $end");

        return [
            'appointment_date' => $date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'shelter_id' =>  $this->input('shelter_id'),
            'team_id' => $this->input('team_id')
        ];
    }
}
