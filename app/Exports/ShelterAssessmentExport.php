<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ShelterAssessmentExport implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $events, $summary, $title;

    public function __construct($events, $report, $title)
    {
        $this->events = $events;
        $this->summary = $report;
        $this->title = $title;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->events as $code => $eventName) {
            $row = [];

            if ($eventName['type'] === 'topic') {
                // หมวดใหญ่ row
                $row[] = $eventName['content'];
                // ใส่ค่าว่างสำหรับอำเภอและรวม
                $row[] = '';
            } else {
                // หมวดย่อย
                $row[] = $eventName['type'] === 'total' ? $eventName['content'] : '    ' . $eventName['content']; // indent
                $row[] = empty($this->summary->$code) || $this->summary->$code == 0  ? '0' : $this->summary->$code; // รวมทั้งหมด

            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B4')->getFont()->setBold(true);

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
                $currentRow = 1; // เริ่มจาก row แรกหลัง headings
                foreach ($this->events as $code => $eventName) {
                    if ($eventName['type'] === 'topic') {
                        $startRow = $currentRow;
                        $currentRow++;

                        if ($code === 'sheet_topic') {
                            $sheet->setCellValue("A$startRow", $this->summary->$code);
                            $sheet->getStyle("A$startRow")->getAlignment()->setWrapText(true);
                        }

                        $sheet->mergeCells("A$startRow:B$startRow");
                        $sheet->getStyle("A$startRow")->getFont()->setBold(true);
                    } else if ($eventName['type'] === 'total') {
                        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
                        $currentRow++;
                    } else {
                        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(false);
                        $currentRow++;
                    }
                }

                $sheet->getRowDimension('1')->setRowHeight(60);
                $sheet->getStyle("A1:B1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FBEFF8'],
                    ],
                ]);
                $sheet->getStyle('A1')->getAlignment()->setWrapText(true);


                // เส้นกรอบ
                $sheet->getStyle("A1:$highestColumn$highestRow")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}
