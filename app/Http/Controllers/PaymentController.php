<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\PaymentsExport;

class PaymentController extends Controller
{
    private const EXCHANGE_RATE = 4000;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function applyFilters(Request $request)
    {
        $query = Order::with([
            'customer',
            'payments' => fn($q) => $q->with('lines')->latest('id'),
        ]);

        // Date / period filter
        $period = $request->get('period', 'all');
        $today  = Carbon::today();

        match ($period) {
            'today'  => $query->whereDate('order_date', $today),
            'week'   => $query->whereBetween('order_date', [
                            $today->copy()->startOfWeek(),
                            $today->copy()->endOfWeek(),
                        ]),
            'month'  => $query->whereMonth('order_date', $today->month)
                               ->whereYear('order_date', $today->year),
            'custom' => $query->when($request->date_from, fn($q) =>
                                $q->whereDate('order_date', '>=', $request->date_from))
                               ->when($request->date_to, fn($q) =>
                                $q->whereDate('order_date', '<=', $request->date_to)),
            default  => null,   // 'all' — no filter
        };

        // Status filter
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $orderStatus = match ($status) {
                'pending' => 'unpaid',
                default => $status,
            };

            $query->where(function ($q) use ($status, $orderStatus) {
                $q->where('payment_status', $orderStatus)
                    ->orWhereHas('payments', fn($p) => $p->where('status', $status));
            });
        }

        // Search
        if ($search = $request->get('search')) {
            $orderId = preg_match('/(\d+)/', $search, $matches) ? (int) $matches[1] : null;

            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($customer) =>
                    $customer->where('name', 'like', "%{$search}%"));

                if (preg_match('/(\d+)/', $search, $matches)) {
                    $q->orWhere('id', (int) $matches[1]);
                }
            });
        }

        return $query->latest('order_date');
    }

    private function mapOrderToPaymentRow(Order $order): object
    {
        $payment = $order->payments->first();

        $status = $payment?->status ?? match ($order->payment_status) {
            'paid' => 'paid',
            'partial' => 'partial',
            default => 'pending',
        };

        $paidAmount = $payment?->paid_amount ?? ($status === 'paid' ? (float) $order->total_amount : 0);

        return (object) [
            'id' => $payment?->id,
            'payment_id' => $payment?->id,
            'source_order_id' => $order->id,
            'customer_name' => $payment?->customer_name ?? $order->customer?->name ?? 'Walk-in Customer',
            'order_id' => 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            'order_date' => $order->order_date,
            'total_amount' => (float) $order->total_amount,
            'paid_amount' => min((float) $order->total_amount, (float) $paidAmount),
            'balance' => max(0, (float) $order->total_amount - (float) $paidAmount),
            'method' => $payment?->method ?? '—',
            'lines' => $payment?->lines?->map(fn($line) => [
                'method' => $line->method,
                'currency' => $line->currency,
                'amount_original' => (float) $line->amount_original,
                'amount_usd' => (float) $line->amount_usd,
            ])->values() ?? [],
            'status' => $status,
            'notes' => $payment?->notes ?? $order->notes,
        ];
    }

    private function buildStats($query): array
    {
        $all = (clone $query)->get()->map(fn($order) => $this->mapOrderToPaymentRow($order));

        return [
            'collected'   => $all->sum('paid_amount'),
            'outstanding' => $all->sum('balance'),
            'total'       => $all->count(),
            'paid'        => $all->where('status', 'paid')->count(),
            'partial'     => $all->where('status', 'partial')->count(),
            'unpaid'      => $all->where('status', 'pending')->count(),
        ];
    }

    private function periodLabel(Request $request): string
    {
        return match ($request->get('period', 'all')) {
            'today'  => 'ថ្ងៃនេះ (' . Carbon::today()->format('d M Y') . ')',
            'week'   => 'សប្តាហ៍នេះ',
            'month'  => 'ខែនេះ (' . Carbon::today()->format('F Y') . ')',
            'custom' => ($request->date_from ?? '—') . ' ដល់ ' . ($request->date_to ?? '—'),
            default  => 'គ្រប់ពេល',
        };
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query    = $this->applyFilters($request);
        $stats    = $this->buildStats($query);
        $payments = $query->paginate(20);
        $payments->setCollection(
            $payments->getCollection()->map(fn($order) => $this->mapOrderToPaymentRow($order))
        );

        return view('payments.index', compact('payments', 'stats'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $order = $this->resolveOrder($request);

        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'order_id'      => 'nullable|string|max:50',
            'order_date'    => 'nullable|date',
            'total_amount'  => 'nullable|numeric|min:0',
            'paid_amount'   => 'nullable|numeric|min:0',
            'payment_lines' => 'nullable|string',
            'method'        => 'nullable|string|max:50',
            'notes'         => 'nullable|string|max:500',
            'source_order_id' => 'nullable|integer',
        ]);

        $lines = $this->parsePaymentLines($request);
        $data['paid_amount'] = collect($lines)->sum('amount_usd');
        $data['customer_name'] = $order->customer?->name ?? $data['customer_name'] ?? 'Walk-in Customer';
        $data['order_id'] = $order->id;
        $data['order_date'] = $order->order_date;
        $data['total_amount'] = $order->total_amount;
        abort_if((float) $data['paid_amount'] > (float) $data['total_amount'], 422, 'Paid amount cannot be greater than the order total.');

        // Auto-set status
        $data['status'] = $this->resolveStatus($data['total_amount'], $data['paid_amount']);
        $data['method'] = $this->summarizeMethods($lines);
        unset($data['payment_lines']);

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            $data
        );
        $this->syncPaymentLines($payment, $lines);

        $order->update([
            'payment_status' => match ($data['status']) {
                'pending' => 'unpaid',
                default => $data['status'],
            },
        ]);

        // Also update linked invoice status (if an invoice exists for this order)
        try {
            $invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
            if ($invoice) {
                $invoiceStatus = match ($data['status']) {
                    'paid' => 'paid',
                    'partial' => 'pending',
                    default => 'pending',
                };
                $invoice->update(['status' => $invoiceStatus]);
            }
        } catch (\Throwable $e) {
            // don't break on invoice update failure; log if necessary
            logger()->warning('Failed to update invoice status after payment store: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment recorded successfully.');
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Payment $payment)
    {
        $order = $this->resolveOrder($request, $payment->order_id);

        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'order_id'      => 'nullable|string|max:50',
            'order_date'    => 'nullable|date',
            'total_amount'  => 'nullable|numeric|min:0',
            'paid_amount'   => 'nullable|numeric|min:0',
            'payment_lines' => 'nullable|string',
            'method'        => 'nullable|string|max:50',
            'notes'         => 'nullable|string|max:500',
            'source_order_id' => 'nullable|integer',
        ]);

        $lines = $this->parsePaymentLines($request);
        $data['paid_amount'] = collect($lines)->sum('amount_usd');
        $data['customer_name'] = $order->customer?->name ?? $data['customer_name'] ?? 'Walk-in Customer';
        $data['order_id'] = $order->id;
        $data['order_date'] = $order->order_date;
        $data['total_amount'] = $order->total_amount;
        abort_if((float) $data['paid_amount'] > (float) $data['total_amount'], 422, 'Paid amount cannot be greater than the order total.');
        $data['status']      = $this->resolveStatus($data['total_amount'], $data['paid_amount']);
        $data['method'] = $this->summarizeMethods($lines);
        unset($data['payment_lines']);

        $payment->update($data);
        $this->syncPaymentLines($payment, $lines);

        $order->update([
            'payment_status' => match ($data['status']) {
                'pending' => 'unpaid',
                default => $data['status'],
            },
        ]);

            // Also update linked invoice status (if an invoice exists for this order)
            try {
                $invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
                if ($invoice) {
                    $invoiceStatus = match ($data['status']) {
                        'paid' => 'paid',
                        'partial' => 'pending',
                        default => 'pending',
                    };
                    $invoice->update(['status' => $invoiceStatus]);
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to update invoice status after payment update: ' . $e->getMessage());
            }

        return back()->with('success', 'Payment updated successfully.');
    }

    // ─── Export Excel ─────────────────────────────────────────────────────────

    public function exportExcel(Request $request)
    {
        $payments = $this->applyFilters($request)->get()
            ->map(fn($order) => $this->mapOrderToPaymentRow($order));
        $label    = $this->periodLabel($request);

        return (new PaymentsExport($payments, $label))->download('Pizza_Happy_Family_Payments.csv');
    }

    // ─── Export PDF ───────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $query       = $this->applyFilters($request);
        $payments    = $query->get()->map(fn($order) => $this->mapOrderToPaymentRow($order));
        $stats       = $this->buildStats($query);
        $periodLabel = $this->periodLabel($request);
        $statusLabel = match ($request->get('status', 'all')) {
            'paid'    => 'បានបង់',
            'partial' => 'បង់ខ្លះ',
            'pending' => 'មិនទាន់បង់',
            default   => null,
        };

        return view('payments.pdf', compact('payments', 'stats', 'periodLabel', 'statusLabel'));
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function resolveStatus(float $total, float $paid): string
    {
        if ($paid <= 0)       return 'pending';
        if ($paid >= $total)  return 'paid';
        return 'partial';
    }

    private function resolveOrder(Request $request, ?int $fallbackOrderId = null): Order
    {
        $sourceOrderId = $request->integer('source_order_id') ?: $fallbackOrderId;

        if (!$sourceOrderId && $request->filled('order_id')) {
            preg_match('/(\d+)/', (string) $request->order_id, $matches);
            $sourceOrderId = isset($matches[1]) ? (int) $matches[1] : null;
        }

        abort_unless($sourceOrderId, 422, 'Please select a valid order.');

        return Order::with('customer')->findOrFail($sourceOrderId);
    }

    private function parsePaymentLines(Request $request): array
    {
        $rawLines = json_decode($request->input('payment_lines', '[]'), true);

        if (!is_array($rawLines) || empty($rawLines)) {
            $amount = (float) $request->input('paid_amount', 0);

            return $amount > 0 ? [[
                'method' => $request->input('method', 'Cash'),
                'currency' => 'USD',
                'amount_original' => $amount,
                'amount_usd' => $amount,
                'exchange_rate' => self::EXCHANGE_RATE,
            ]] : [];
        }

        return collect($rawLines)
            ->map(function ($line) {
                $currency = strtoupper($line['currency'] ?? 'USD') === 'KHR' ? 'KHR' : 'USD';
                $amountOriginal = max(0, (float) ($line['amount'] ?? 0));
                $amountUsd = $currency === 'KHR'
                    ? round($amountOriginal / self::EXCHANGE_RATE, 2)
                    : round($amountOriginal, 2);

                return [
                    'method' => $line['method'] ?? 'Cash',
                    'currency' => $currency,
                    'amount_original' => $amountOriginal,
                    'amount_usd' => $amountUsd,
                    'exchange_rate' => self::EXCHANGE_RATE,
                ];
            })
            ->filter(fn($line) => $line['amount_original'] > 0)
            ->values()
            ->all();
    }

    private function summarizeMethods(array $lines): string
    {
        if (empty($lines)) {
            return '—';
        }

        return collect($lines)->pluck('method')->unique()->join(' + ');
    }

    private function syncPaymentLines(Payment $payment, array $lines): void
    {
        $payment->lines()->delete();

        foreach ($lines as $line) {
            $payment->lines()->create($line);
        }
    }
}
