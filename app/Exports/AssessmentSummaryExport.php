<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssessmentSummaryExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $districts;
    protected $summary;

    protected $events = [
        'a'  => 'อุบัติเหตุ (Trauma)',
        'a1' => 'บาดเจ็บบริเวณศีรษะ/กระดูกสันหลังอย่างรุนแรง',
        'a2' => 'บาดเจ็บบริเวณลำตัวอย่างรุนแรง',
        'a3' => 'บาดเจ็บบริเวณส่วนปลายแขนขา',
        'a4' => 'บาดเจ็บปานกลาง',
        'a5' => 'บาดเจ็บเล็กน้อย',

        'b'  => 'โรคติดเชื้อ (Infectious disease)',
        'b1' => 'การติดเชื้อทางเดินหายใจเฉียบพลัน',
        'b2' => 'ท้องเสียเฉียบพลัน',
        'b3' => 'ท้องเสียเป็นเลือดเฉียบพลัน',
        'b4' => 'โรคดีซ่านเฉียบพลัน',
        'b5' => 'สงสัยเป็นโรคหัด (Measles)',
        'b6' => 'สงสัยเยื่อหุ้มสมองอักเสบ',
        'b7' => 'สงสัยบาดทะยัก',
        'b8' => 'อัมพาตเฉียบพลัน',
        'b9' => 'ไข้เลือดออกเฉียบพลัน',
        'b10' => 'ไข้ไม่ทราบสาเหตุ',

        'c'  => 'โรคฉุกเฉิน',
        'c1' => 'โรคทางศัลยกรรมฉุกเฉิน (ไม่ใช่การบาดเจ็บ)',
        'c2' => 'โรคทางอายุรกรรมฉุกเฉิน (ไม่ติดเชื้อ)',

        'd'  => 'โรคสำคัญอื่นๆ',
        'd1' => 'โรคผิวหนัง',
        'd2' => 'ปัญหาสุขภาพจิตเฉียบพลัน',
        'd3' => 'ภาวะแทรกซ้อนทางสูติกรรม',
        'd4' => 'ภาวะทุพโภชนาการเฉียบพลันรุนแรง (SAM)',
        'd5' => 'การวินิจฉัยอื่น ๆ ที่ไม่ได้ระบุไว้ข้างต้น',

        'e'  => 'หัตถการ',
        'e1' => 'ฉีดยา',
        'e2' => 'ทำแผล',
        'e3' => 'ผ่าตัดเล็กรวมเย็บแผล',

        'f' => 'ผลการรักษา',
        'f1' => 'Discharge (OPD)',
        'f2' => 'Admit (IPD)',
        'f3' => 'Refer',
    ];

    public function __construct($districts, $summary)
    {
        $this->districts = $districts;
        $this->summary   = $summary;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->events as $code => $eventName) {
            $row = [];

            if (strlen($code) == 1) {
                // หมวดใหญ่ row
                $row[] = $eventName;
                // ใส่ค่าว่างสำหรับอำเภอและรวม
                foreach ($this->districts as $district => $values) {
                    $row[] = '';
                }
                $row[] = '';
            } else {
                // หมวดย่อย
                $row[] = '    ' . $eventName; // indent
                foreach ($this->districts as $district => $values) {
                    $row[] = $values[$code] ?? ''; // ค่าแต่ละอำเภอ
                }
                $row[] = $this->summary[$code] ?? ''; // รวมทั้งหมด
            }

            $rows[] = $row;
        }
        return $rows;
    }

    public function headings(): array
    {
        return array_merge(
            ['Health events'],
            array_keys($this->districts),
            ['รวม']
        );
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // ตัวอักษรในคอลัมน์ A (เหตุการณ์) ชิดซ้าย
        $sheet->getStyle("A2:A$highestRow")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        // คอลัมน์อื่นๆ กึ่งกลาง
        $sheet->getStyle("B2:$highestColumn$highestRow")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        return [];
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 50];
        $i = 1;
        foreach ($this->districts as $district => $values) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $widths[$col] = 20;
            $i++;
        }
        $colSum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->districts) + 2);
        $widths[$colSum] = 15;
        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // วน merge หมวดใหญ่
                $currentRow = 2; // เริ่มจาก row แรกหลัง headings
                foreach ($this->events as $code => $eventName) {
                    if (strlen($code) == 1) {
                        $startRow = $currentRow;
                        $currentRow++;
                        while ($currentRow <= $highestRow) {
                            $next = trim($sheet->getCell("A$currentRow")->getValue());
                            if ($next != '' && substr($next, 0, 4) != '    ') break;
                            $currentRow++;
                        }
                        $endRow = $currentRow - 1;

                        // Merge ข้ามคอลัมน์
                        $colEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->districts) + 2);
                        $sheet->mergeCells("A$startRow:$colEnd$startRow");
                        $sheet->getStyle("A$startRow")->getFont()->setBold(true);

                        // หมวดใหญ่ยังคงชิดซ้าย
                        $sheet->getStyle("A$startRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    } else {
                        $currentRow++;
                    }
                }

                // เส้นกรอบ
                $sheet->getStyle("A1:$highestColumn$highestRow")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        ];
    }
}
