<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class DisasterExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected array $rows;
    protected array $columns;

    // รับ columns ก่อน แล้วค่อย rows ให้ตรงกับการเรียกใช้ของพี่
    public function __construct(array $columns, array $rows)
    {
        $this->columns = $columns;
        $this->rows    = $rows;
    }

    public function array(): array
    {
        return $this->rows; // ต้องเป็น array of array
    }

    public function headings(): array
    {
        return $this->columns; // ต้องเป็น array ของ string
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // สไตล์เส้นขอบ
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // หัวคอลัมน์ (row 1) → พื้นหลังเหลือง ตัวหนา กึ่งกลาง + เส้นขอบ
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray(array_merge($borderStyle, [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'], // เหลือง
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]));

        // Row 2 เป็นต้นไป → ทำสีสลับ (ขาว / เทาอ่อน) + เส้นขอบ
        for ($row = 2; $row <= $highestRow; $row++) {
            $color = ($row % 2 == 0) ? 'FFFFFF' : '919191'; // even=white, odd=light gray
            $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray(array_merge($borderStyle, [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]));
        }

        // จัดข้อความคอลัมน์แรก → ชิดซ้าย
        if ($highestRow > 1) {
            $sheet->getStyle("A2:A{$highestRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // คอลัมน์อื่น ๆ → กึ่งกลาง
        if ($highestRow > 1) {
            $sheet->getStyle("B2:{$highestColumn}{$highestRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach (range(1, count($this->columns)) as $i) {
            $col = Coordinate::stringFromColumnIndex($i);
            $widths[$col] = $i === 1 ? 20 : 20;
        }
        return $widths;
    }
}
