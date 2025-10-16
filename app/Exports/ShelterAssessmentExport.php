<?php

namespace App\Exports;

use App\Invoice;
use App\Models\Shelter;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ShelterAssessmentExport implements FromArray, WithTitle
{
    protected $data, $title;

    public function __construct(array $data, string $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return $this->title;
    }
}
