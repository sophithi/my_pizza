<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\PackingDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestoreEditedPackingInvoices extends Command
{
    protected $signature = 'packing:restore-edited {--apply : Restore edited invoices with a private backup}';
    protected $description = 'Keep previously sent edited invoices in packing and mark them as edited';

    public function handle(PackingDispatchService $service): int
    {
        return DB::transaction(function () use ($service) {
            $invoices = Invoice::whereNotNull('packing_first_sent_at')->where('status', '!=', 'cancelled')
                ->whereHas('order', fn($q) => $q->whereNull('orders.deleted_at')->where('status', '!=', 'cancelled'))
                ->where(function ($q) {
                    $q->whereNull('packing_sent_at')->orWhere(function ($edited) {
                        $edited->where('packing_edited', false)->whereColumn('packing_first_sent_at', '!=', 'packing_sent_at');
                    });
                })->orderBy('id')->lockForUpdate()->get();
            if (!$this->option('apply') || $invoices->isEmpty()) {
                $this->info($invoices->count() . ' edited invoices to restore or mark. Use --apply to save.');
                return self::SUCCESS;
            }
            $before = $invoices->map(fn($invoice) => [
                'invoice' => $invoice->attributesToArray(),
                'dispatches' => $invoice->packingDispatches()->get()->toArray(),
            ])->all();
            $path = 'packing-backups/restore-edited-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.json';
            if (!Storage::disk('local')->put($path, json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
                throw new \RuntimeException('Backup failed; invoices were not changed.');
            }
            $restored = 0;
            foreach ($invoices as $invoice) {
                $fields = ['packing_edited' => true, 'packing_revision' => ($invoice->packing_revision ?? 0) + 1];
                if (!$invoice->packing_sent_at) {
                    $latest = $invoice->packingDispatches()->orderByDesc('sent_at')->orderByDesc('id')->first();
                    $sentAt = $latest?->sent_at ?? $invoice->packing_first_sent_at;
                    $fields['packing_sent_at'] = $sentAt->format('Y-m-d H:i:s');
                    if ($latest) {
                        $latest->update(['state' => $invoice->packing_completed_at ? 'completed' : 'pending']);
                    } else {
                        $invoice->packingDispatches()->create([
                            'sent_at' => $sentAt, 'state' => 'pending',
                            'source' => 'legacy_known_timestamp', 'snapshot' => $service->snapshot($invoice),
                        ]);
                    }
                    $restored++;
                }
                // Restoring a known send time is not a new dispatch.
                DB::table('invoices')->where('id', $invoice->id)->update($fields);
            }
            $this->info("Restored {$restored} invoices; marked {$invoices->count()} as edited. Backup: " . Storage::disk('local')->path($path));
            return self::SUCCESS;
        });
    }
}
