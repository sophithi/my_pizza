<?php

namespace App\Exports;

class PaymentsExport
{
    public function __construct(
        private $payments,
        private string $periodLabel = 'All Time'
    ) {}

    /**
     * Stream a UTF-8 CSV file optimized for Excel
     */
    public function download(string $filename = 'payments.csv')
    {
        $handle = fopen('php://temp', 'r+');

        // UTF-8 BOM — makes Excel auto-detect encoding (important for Khmer names)
        fputs($handle, "\xEF\xBB\xBF");

        // Professional Heading row
        fputcsv($handle, [
            'លេខការបញ្ជាទិញ',
            'ឈ្មោះអតិថិជន',
            'កាលបរិច្ឆេទ',
            'សរុប ($)',
            'សរុប (៛)',
            'បានបង់ ($)',
            'បានបង់ (៛)',
            'នៅសល់ ($)',
            'នៅសល់ (៛)',
            'វិធីបង់',
            'ស្ថានភាព',
            'កំណត់ចំណាំ',
        ]);

        $exchangeRate = 4000;

        // Data rows with dual currency
        foreach ($this->payments as $p) {
            $totalKhr = ($p->total_amount ?? 0) * $exchangeRate;
            $paidKhr = ($p->paid_amount ?? 0) * $exchangeRate;
            $balance = $p->balance ?? max(0, ($p->total_amount ?? 0) - ($p->paid_amount ?? 0));
            $balanceKhr = $balance * $exchangeRate;

            fputcsv($handle, [
                $p->order_id ?? '',
                $p->customer_name ?? '',
                \Carbon\Carbon::parse($p->order_date)->format('Y-m-d'),
                number_format($p->total_amount ?? 0, 2),
                number_format($totalKhr, 0),
                number_format($p->paid_amount ?? 0, 2),
                number_format($paidKhr, 0),
                number_format($balance, 2),
                number_format($balanceKhr, 0),
                $p->method ?? '',
                $this->statusLabel($p->status ?? ''),
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

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'បានបង់',
            'partial' => 'បង់ខ្លះ',
            default => 'មិនទាន់បង់',
        };
    }
}
