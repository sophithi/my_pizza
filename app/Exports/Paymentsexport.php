<?php

namespace App\Exports;

class PaymentsExport
{
    public function __construct(
        private $payments,
        private string $periodLabel = 'All Time'
    ) {}

    /**
     * Stream a UTF-8 CSV (opens perfectly in Excel, LibreOffice, Google Sheets).
     * Zero extra packages required.
     */
    public function download(string $filename = 'payments.csv')
    {
        $handle = fopen('php://temp', 'r+');

        // UTF-8 BOM — makes Excel auto-detect encoding (important for Khmer names)
        fputs($handle, "\xEF\xBB\xBF");

        // Heading row
        fputcsv($handle, [
            'លេខការបញ្ជាទិញ',
            'ឈ្មោះអតិថិជន',
            'កាលបរិច្ឆេទបញ្ជាទិញ',
            'សរុបការបញ្ជាទិញ ($)',
            'ចំនួនបានបង់ ($)',
            'ចំនួននៅសល់ ($)',
            'វិធីបង់ប្រាក់',
            'ស្ថានភាព',
            'កំណត់ចំណាំ',
        ]);

        // Data rows
        foreach ($this->payments as $p) {
            fputcsv($handle, [
                $this->asExcelText($p->order_id),
                $p->customer_name,
                $this->asExcelText(\Carbon\Carbon::parse($p->order_date)->format('Y-m-d')),
                number_format($p->total_amount, 2),
                number_format($p->paid_amount, 2),
                number_format($p->balance ?? max(0, $p->total_amount - $p->paid_amount), 2),
                $p->method,
                $this->statusLabel($p->status),
                $p->notes ?? '',
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    private function asExcelText(string $value): string
    {
        return '="' . str_replace('"', '""', $value) . '"';
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'បានបង់',
            'partial' => 'បង់ខ្លះ',
            default => 'មិនទាន់បង់',
        };
    }
}
