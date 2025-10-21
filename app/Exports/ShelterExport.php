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

class ShelterExport implements FromArray, WithEvents, WithTitle
{
    private $headingRow = 0;
    protected $data, $title, $rows;

    // ✅ Accept the array when creating the export
    public function __construct(array $data, string $title, $rows = 7)
    {
        $this->data = $data;
        $this->title = $title;
        $this->rows = $rows;
    }

    public function array(): array
    {
        return collect($this->data)
            ->map(function ($shelter) {
                $rows = collect($shelter)
                    ->map(fn($v, $k) => [$k, $v])
                    ->values()
                    ->toArray();

                $rows[] = ['', ''];

                return $rows;
            })
            ->flatten(1)
            ->toArray();
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                // Auto column width
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(25);

                $highestRow = $sheet->getHighestRow();

                // Loop through all rows to find heading rows
                for ($i = 1; $i <= $highestRow; $i++) {

                    if ($i == ($this->headingRow * $this->rows) + 1) {
                        // Merge heading row
                        $sheet->mergeCells("A{$i}:B{$i}");

                        // Style the heading
                        $sheet->getStyle("A{$i}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 14],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['argb' => 'FBEFF8'],
                            ],
                        ]);

                        $this->headingRow++;
                    }

                    $sheet->getStyle("A{$i}")->applyFromArray([
                        'font' => ['bold' => true],
                    ]);

                    $sheet->getStyle("B1:B{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                }
            },
        ];
    }
}
