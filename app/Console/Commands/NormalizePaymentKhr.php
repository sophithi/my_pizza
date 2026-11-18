<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NormalizePaymentKhr extends Command
{
    protected $signature = 'payments:normalize-khr {--apply : Save changes with a private backup} {--correction=* : Explicit payment ID:correct KHR amount for a single-line receipt}';
    protected $description = 'Normalize historical USD payment lines to the base KHR currency; preview by default';

    public function handle(): int
    {
        $corrections = [];
        foreach ($this->option('correction') as $value) {
            if (!preg_match('/^(\d+):(\d+)$/', $value, $matches)) {
                $this->error('Corrections must use payment ID:integer KHR.');
                return self::FAILURE;
            }
            $corrections[(int) $matches[1]] = (int) $matches[2];
        }

        return DB::transaction(function () use ($corrections) {
            $payments = Payment::with('lines')->where(function ($q) use ($corrections) {
                $q->whereHas('lines', fn($lines) => $lines->where('currency', 'USD'))
                    ->orWhereIn('id', array_keys($corrections));
            })->lockForUpdate()->get();
            $changes = [];
            foreach ($payments as $payment) {
                $lines = $payment->lines->map(function ($line) {
                    $amount = $line->currency === 'KHR'
                        ? (float) $line->amount_original
                        : round($line->amount_original * $line->exchange_rate);
                    return ['id' => $line->id, 'method' => $line->method, 'amount' => $amount];
                })->all();
                $reason = 'Normalize historical receipt to KHR';
                // Recover the exact KHR invoice when the only receipt is its rounded
                // USD equivalent. This is a one-time, audited repair, never reporting logic.
                if (count($lines) === 1 && $payment->lines[0]->currency === 'USD'
                    && Payment::where('order_id', $payment->order_id)->count() === 1) {
                    $line = $payment->lines[0];
                    $invoiceKhr = (float) $payment->total_amount_khr;
                    if ($invoiceKhr > 0 && $line->exchange_rate > 0
                        && abs($lines[0]['amount'] - $invoiceKhr) <= $line->exchange_rate * 0.005 + 0.000001
                        && abs($line->amount_original - round($invoiceKhr / $line->exchange_rate, 2)) < 0.000001) {
                        $lines[0]['amount'] = $invoiceKhr;
                        $reason = 'Recover original KHR invoice from rounded USD equivalent';
                    }
                }
                if (isset($corrections[$payment->id])) {
                    if (count($lines) !== 1) {
                        throw new \RuntimeException('Explicit correction requires a single-line receipt.');
                    }
                    $lines[0]['amount'] = $corrections[$payment->id];
                    $reason = 'Explicit correction of recorded KHR amount';
                }
                $paidKhr = array_sum(array_column($lines, 'amount'));
                if ($lines === [] || $paidKhr < 0 || floor($paidKhr) !== (float) $paidKhr) {
                    throw new \RuntimeException('Invalid KHR amount for payment ' . $payment->id);
                }
                if (!$payment->lines->contains(fn($line) => $line->currency !== 'KHR')
                    && (float) $payment->paid_amount_khr === (float) $paidKhr
                    && (float) $payment->lines->sum('amount_original') === (float) $paidKhr) {
                    continue;
                }
                $changes[] = ['payment_id' => $payment->id, 'order_id' => $payment->order_id,
                    'before' => $payment->toArray(), 'lines' => $lines, 'paid_khr' => $paidKhr, 'reason' => $reason];
                $this->line("Payment {$payment->id}: {$payment->paid_amount_khr} -> {$paidKhr} KHR ({$reason})");
            }
            if (!$this->option('apply') || $changes === []) {
                $this->info(count($changes) . ' receipts planned. Use --apply to save.');
                return self::SUCCESS;
            }
            $path = 'payment-khr-backups/' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.json';
            if (!Storage::disk('local')->put($path, json_encode($changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
                throw new \RuntimeException('Backup could not be saved; no receipts changed.');
            }
            foreach ($changes as $change) {
                foreach ($change['lines'] as $line) {
                    DB::table('payment_lines')->where('id', $line['id'])->update([
                        'currency' => 'KHR', 'amount_original' => $line['amount'],
                        'amount_usd' => round($line['amount'] / 4000, 2), 'exchange_rate' => 4000,
                    ]);
                }
                $notes = 'Paid: ' . collect($change['lines'])->map(fn($line) => $line['method'] . ': ៛' . number_format($line['amount'], 0) . ' KHR')->join(' + ');
                DB::table('payments')->where('id', $change['payment_id'])->update([
                    'paid_amount_khr' => $change['paid_khr'], 'paid_amount' => round($change['paid_khr'] / 4000, 2),
                    'exchange_rate' => 4000, 'notes' => $notes,
                ]);
            }
            $this->info('Saved ' . count($changes) . ' receipts. Backup: ' . Storage::disk('local')->path($path));
            return self::SUCCESS;
        });
    }
}
