<?php

namespace App\Exports;

use App\Models\DamagedUtility;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class DamageUtilityExport implements FromArray, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'สาธารณประโยชน์ที่ได้รับความเสียหาย';
    }
}
