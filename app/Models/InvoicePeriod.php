<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoicePeriod extends Model
{
    protected $fillable = [
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * The currently active (open) period that new invoices attach to.
     */
    public static function current(): self
    {
        return static::whereNull('closed_at')->latest('id')->first()
            ?? static::create([]);
    }

    /**
     * Close the active period (invoice numbering for it stops) and open a
     * fresh one starting back at INV-000001. Locks the row so two admins
     * clicking the button at once can't both create a new period.
     */
    public static function closeCurrentAndStartNew(?int $closedByUserId = null): self
    {
        return DB::transaction(function () use ($closedByUserId) {
            $current = static::whereNull('closed_at')->latest('id')->lockForUpdate()->first();

            if ($current) {
                $current->update([
                    'closed_at' => now(),
                    'closed_by' => $closedByUserId,
                ]);
            }

            return static::create([]);
        });
    }

    /**
     * Whether an accidental "close" click can still be undone: only true
     * while nothing has been invoiced under the new (active) period yet.
     * Once an invoice exists there, undoing would mean renumbering an
     * invoice number someone may have already printed/handed over — not safe.
     */
    public static function canUndoLastClose(): bool
    {
        $current = static::whereNull('closed_at')->latest('id')->first();

        if (!$current || $current->invoices()->withTrashed()->exists()) {
            return false;
        }

        return static::whereNotNull('closed_at')->where('id', '<', $current->id)->exists();
    }

    /**
     * Undo the last close: reopen the most recently closed period and
     * discard the empty period the button opened. See canUndoLastClose()
     * for when this is allowed.
     */
    public static function undoLastClose(): bool
    {
        return DB::transaction(function () {
            $current = static::whereNull('closed_at')->latest('id')->lockForUpdate()->first();

            if (!$current || $current->invoices()->withTrashed()->exists()) {
                return false;
            }

            $previous = static::whereNotNull('closed_at')
                ->where('id', '<', $current->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$previous) {
                return false;
            }

            $previous->update(['closed_at' => null, 'closed_by' => null]);
            $current->delete();

            return true;
        });
    }

    /**
     * Preview what mergeActiveIntoPrevious() would do, without changing
     * anything — the old/new invoice_number pairs, for showing the user
     * exactly what will be renumbered before they confirm.
     */
    public static function previewMergeBackIntoPrevious(): array
    {
        $current = static::whereNull('closed_at')->latest('id')->first();
        if (!$current) {
            return [];
        }

        $invoices = Invoice::withTrashed()->where('invoice_period_id', $current->id)->orderBy('id')->get();
        if ($invoices->isEmpty()) {
            return [];
        }

        $previous = static::whereNotNull('closed_at')->where('id', '<', $current->id)->latest('id')->first();
        if (!$previous) {
            return [];
        }

        $lastNumber = Invoice::withTrashed()
            ->where('invoice_period_id', $previous->id)
            ->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")
            ->value('invoice_number');
        $next = $lastNumber ? (int) substr($lastNumber, 4) + 1 : 1;

        $preview = [];
        foreach ($invoices as $invoice) {
            $preview[] = [
                'id' => $invoice->id,
                'old_number' => $invoice->invoice_number,
                'new_number' => 'INV-' . str_pad($next, 6, '0', STR_PAD_LEFT),
            ];
            $next++;
        }

        return $preview;
    }

    /**
     * Force-merge the active period back into the last closed period,
     * renumbering each of its invoices to continue that period's sequence.
     * Unlike undoLastClose(), this works even when the active period
     * already has invoices — use once the safe-undo window has passed and
     * you still want to collapse the accidental extra period away.
     */
    public static function mergeActiveIntoPrevious(): array
    {
        return DB::transaction(function () {
            $current = static::whereNull('closed_at')->latest('id')->lockForUpdate()->first();
            if (!$current) {
                return [];
            }

            $invoices = Invoice::withTrashed()->where('invoice_period_id', $current->id)->orderBy('id')->lockForUpdate()->get();
            if ($invoices->isEmpty()) {
                return [];
            }

            $previous = static::whereNotNull('closed_at')
                ->where('id', '<', $current->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();
            if (!$previous) {
                return [];
            }

            $merged = [];
            foreach ($invoices as $invoice) {
                $oldNumber = $invoice->invoice_number;
                $newNumber = Invoice::generateInvoiceNumber($previous->id);
                $invoice->update([
                    'invoice_period_id' => $previous->id,
                    'invoice_number' => $newNumber,
                ]);
                $merged[] = ['id' => $invoice->id, 'old_number' => $oldNumber, 'new_number' => $newNumber];
            }

            $previous->update(['closed_at' => null, 'closed_by' => null]);
            $current->delete();

            return $merged;
        });
    }
}
