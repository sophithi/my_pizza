<?php

namespace App\Services;

use App\Models\Invoice;

class PackingDispatchService
{
    public function markEdited(Invoice $invoice): void
    {
        if ($invoice->packing_sent_at) {
            $invoice->update([
                'packing_edited' => true,
                'packing_revision' => ($invoice->packing_revision ?? 0) + 1,
            ]);
        }
    }

    public function snapshot(Invoice $invoice): array
    {
        $invoice->loadMissing('order.customer', 'order.items.product', 'order.delivery');

        return [
            'invoice_number' => $invoice->invoice_number,
            'invoice_date' => $invoice->invoice_date?->toDateString(),
            'customer_name' => $invoice->order?->customer?->name,
            'invoice' => $invoice->attributesToArray(),
            'order' => $invoice->order?->toArray(),
        ];
    }

    public function recordChanges(Invoice $invoice): void
    {
        if ($invoice->wasChanged('packing_sent_at')) {
            $previous = $invoice->getRawOriginal('packing_sent_at');
            if ($previous) {
                $invoice->packingDispatches()->where('sent_at', $previous)
                    ->where('state', 'pending')->update(['state' => 'superseded']);
            }
            if ($invoice->packing_sent_at) {
                $invoice->packingDispatches()->create([
                    'sent_at' => $invoice->packing_sent_at,
                    'state' => 'pending',
                    'source' => 'live',
                    'snapshot' => $this->snapshot($invoice),
                ]);
            }
        }

        if ($invoice->wasChanged('packing_completed_at') && $invoice->packing_completed_at) {
            $current = $invoice->packingDispatches()->where('sent_at', $invoice->packing_sent_at)->latest('id')->first();
            $current?->update([
                'state' => 'completed',
                'completed_at' => $invoice->packing_completed_at,
                'completed_by' => $invoice->packing_completed_by,
            ]);
        }
        if ($invoice->wasChanged('status') && $invoice->status === 'cancelled') {
            $this->archivePending($invoice);
        }
    }

    public function archivePending(Invoice $invoice): void
    {
        $invoice->packingDispatches()->where('state', 'pending')->update(['state' => 'superseded']);
    }
}
