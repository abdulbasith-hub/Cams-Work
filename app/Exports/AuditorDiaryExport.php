<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AuditorDiaryExport implements FromArray, WithStyles, WithEvents
{
    private $metaData;
    private $calendarData;
    private $summaryData;

    public function __construct($metaData, $calendarData, $summaryData)
    {
        $this->metaData = $metaData;       // (Name, Designation, Office)
        $this->calendarData = $calendarData; // Calendar table data
        $this->summaryData = $summaryData; // Summary table data
    }

    public function array(): array
    {
        // Add metadata to the array
        $currentMonthYear=date('F Y');

        $data = [
                    ['Auditor Diary for '.$currentMonthYear.''],
                    ['Name', $this->metaData['name'] ?? '', '', ''],
                    ['Designation', $this->metaData['designation'] ?? '', '', ''],
                    ['Working Office', $this->metaData['working_office'] ?? '', '', ''],
                    ['', '', '', ''], // Empty row for spacing
                    ['Date', 'Day', 'Details of Work Done'], // Table header
                ];

        // Add calendar data
        foreach ($this->calendarData as $row) {
            $data[] = [$row['date'], $row['day'], $row['details']];
        }

        // Add an empty row for spacing
        $data[] = ['', '', '', ''];

        // Add summary data
        $data[] = ['Gist', '','Total Man Days'];
        foreach ($this->summaryData as $row) {
            if($row['gist'] == 'Total')
            {
                $data[] = ['', 'Total',$row['total_days']];

            }else{
                $data[] = [$row['gist'], '',$row['total_days']];

            }
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $calendarRowsCount = count($this->calendarData) + 5;
        $summaryStartRow = $calendarRowsCount + 2;

        // Apply styles
        return [
            6 => ['font' => ['bold' => true]],
           
        ];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge cells A1 to C1 and center the content
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:C1')->getFont()->setBold(true); // Optionally bold the text

                // Merge cells for metadata
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('A3')->getFont()->setBold(true);
                $sheet->getStyle('A4')->getFont()->setBold(true);

                 // Set column widths for better readability
                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(65);

                // Apply borders
                $calendarRowsCount = count($this->calendarData) + 6;
                $summaryStartRow = $calendarRowsCount + 2;
                $summaryEndRow = $summaryStartRow + count($this->summaryData);

                // Apply borders only to the outer edges (A1 to D4)
                $sheet->getStyle('A1:C5')->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Black color for top border
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Black color for bottom border
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Black color for left border
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Black color for right border
                        ],
                    ],
                ]);

                // Apply borders to the calendar table
                $sheet->getStyle("A6:C$calendarRowsCount")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Apply borders to the summary table
                $sheet->getStyle("A$summaryStartRow:C$summaryEndRow")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle("A$summaryStartRow:C$summaryStartRow")->getFont()->setBold(true);

            },
        ];
    }
}
