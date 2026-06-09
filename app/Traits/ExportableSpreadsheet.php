<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportableSpreadsheet
{
    /**
     * Create a branded spreadsheet with header
     */
    protected function createBrandedSpreadsheet(
        string $sheetTitle = 'Report',
        string $reportTitle = 'របាយការណ៍',
        int $headerCols = 7
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Khmer OS Battambang')->setSize(9);
        $sheet->setShowGridlines(false);

        // Page setup
        $sheet->getPageSetup()
            ->setOrientation('landscape')
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()
            ->setTop(0.25)
            ->setRight(0.2)
            ->setBottom(0.25)
            ->setLeft(0.2);

        // Add logo
        $logoPath = public_path('assets/logos/logo_pizza.png');
        if (file_exists($logoPath)) {
            $logo = new Drawing();
            $logo->setName('Pizza Happy Family');
            $logo->setPath($logoPath);
            $logo->setHeight(42);
            $logo->setCoordinates('A1');
            $logo->setOffsetX(8);
            $logo->setOffsetY(7);
            $logo->setWorksheet($sheet);
        }

        // Header background
        $headerRange = "A1:" . chr(64 + $headerCols) . "4";
        $sheet->mergeCells($headerRange);
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF7ED'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFF3D6C7'],
                ],
            ],
        ]);

        // Brand name
        $sheet->unmergeCells($headerRange);
        $sheet->mergeCells("B1:" . chr(64 + $headerCols) . "1");
        $sheet->setCellValue("B1", 'Pizza Happy Family');

        // Report title
        $sheet->mergeCells("B2:" . chr(64 + $headerCols) . "2");
        $sheet->setCellValue("B2", $reportTitle);

        // Export date
        $sheet->mergeCells("B3:" . chr(64 + $headerCols) . "3");
        $sheet->setCellValue("B3", 'កាលបរិច្ឆេទនាំចេញ: ' . now()->format('d/m/Y H:i'));

        // Spacer row
        $sheet->mergeCells("A4:" . chr(64 + $headerCols) . "4");
        $sheet->setCellValue("A4", ' ');

        // Style header rows
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(19);
        $sheet->getRowDimension(3)->setRowHeight(17);
        $sheet->getRowDimension(4)->setRowHeight(7);

        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FFE85D24');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF111827');
        $sheet->getStyle('B3')->getFont()->setSize(8)->getColor()->setARGB('FF64748B');
        $sheet->getStyle('B1:B3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A4:" . chr(64 + $headerCols) . "4")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFE85D24');

        return $spreadsheet;
    }

    /**
     * Apply header styling to spreadsheet
     */
    protected function styleTableHeaders($sheet, string $headerRow, string $headerRange): void
    {
        $sheet->getStyle("$headerRow")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE85D24'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($headerRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD9DEE7'],
                ],
            ],
        ]);
    }

    /**
     * Apply striped rows styling
     */
    protected function applyStripeRows($sheet, int $startRow, int $endRow): void
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            if ($row % 2 === 0) {
                $lastCol = $sheet->getHighestColumn();
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFAFAFA');
            }
        }
    }

    /**
     * Download spreadsheet as Excel file
     */
    protected function downloadSpreadsheet(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
            ]
        );
    }
}
