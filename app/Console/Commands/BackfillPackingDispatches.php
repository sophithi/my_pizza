<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\PackingDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillPackingDispatches extends Command
{
    protected $signature = 'packing:backfill-dispatches {--apply : Save known historical dispatch timestamps}';
    protected $description = 'Recover first and latest known packing dispatches without inventing missing send times';

    public function handle(PackingDispatchService $service): int
    {
        $count = 0;
        Invoice::withTrashed()->where(function ($q) {
            $q->whereNotNull('packing_first_sent_at')->orWhereNotNull('packing_sent_at');
        })->chunkById(100, function ($invoices) use ($service, &$count) {
            foreach ($invoices as $invoice) {
                DB::transaction(function () use ($invoice, $service, &$count) {
                    $invoice = Invoice::withTrashed()->lockForUpdate()->findOrFail($invoice->id);
                    $dates = collect([$invoice->packing_first_sent_at, $invoice->packing_sent_at])
                        ->filter()->unique(fn($date) => $date->format('Y-m-d H:i:s'));
                    foreach ($dates as $sentAt) {
                        if ($invoice->packingDispatches()->where('sent_at', $sentAt)->exists()) {
                            continue;
                        }
                        $count++;
                        if (!$this->option('apply')) {
                            continue;
                        }
                        $current = $invoice->packing_sent_at?->equalTo($sentAt)
                            && !$invoice->trashed() && $invoice->status !== 'cancelled';
                        $invoice->packingDispatches()->create([
                            'sent_at' => $sentAt,
                            'state' => $current ? ($invoice->packing_completed_at ? 'completed' : 'pending') : 'superseded',
                            'completed_at' => $current ? $invoice->packing_completed_at : null,
                            'completed_by' => $current ? $invoice->packing_completed_by : null,
                            'source' => 'legacy_known_timestamp',
                            'snapshot' => $service->snapshot($invoice),
                        ]);
                    }
                });
            }
        });
        $this->info($count . ($this->option('apply') ? ' historical dispatches recovered.' : ' known dispatches to recover; use --apply.'));
        return self::SUCCESS;
    }
}
