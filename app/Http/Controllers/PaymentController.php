<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLine;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private const EXCHANGE_RATE = 4000;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'all');
        $today  = Carbon::today();

        if ($period === 'today') {
            return [$today->toDateString(), $today->toDateString()];
        } elseif ($period === 'week') {
            return [$today->copy()->startOfWeek()->toDateString(), $today->copy()->endOfWeek()->toDateString()];
        } elseif ($period === 'month') {
            return [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()];
        } elseif ($period === 'custom') {
            $from = $request->date_from ? Carbon::parse($request->date_from)->toDateString() : null;
            $to   = $request->date_to ? Carbon::parse($request->date_to)->toDateString() : null;
            if ($from && !$to) $to = $from;
            if ($to && !$from) $from = $to;
            return [$from, $to];
        }

        return [null, null];
    }

    private function applyFilters(Request $request)
    {
        $query = Order::with([
            'customer',
            'payments' => fn($q) => $q->with('lines')->latest('id'),
        ]);

        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        // Filter orders that either:
        // a) Were placed in this date range, OR
        // b) Had a payment collected in this date range (including old debts settled during this period)
        if ($dateFrom && $dateTo) {
            $query->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('order_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                  ->orWhereHas('payments', fn($p) => $p->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']));
            });
        }

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($customer) =>
                    $customer->where('name', 'like', "%{$search}%"));

                if (preg_match('/(\d+)/', $search, $matches)) {
                    $q->orWhere('id', (int) $matches[1]);
                }
            });
        }

        // Delivery filter
        if ($deliveryId = $request->get('delivery_id')) {
            $query->where('delivery_id', $deliveryId);
        }

        // Payment method filter
        if ($method = $request->get('method')) {
            $query->whereHas('payments', function ($q) use ($method) {
                if ($method === 'other') {
                    $q->where('method', 'not like', '%Cash%')
                        ->where('method', 'not like', '%ABA%')
                        ->where('method', 'not like', '%ACLEDA%')
                        ->where('method', 'not like', '%Wing%')
                        ->where('method', '!=', '—');
                } else {
                    $q->where('method', 'like', "%{$method}%");
                }
            });
        }

        return $query->orderByRaw(
            "COALESCE((select max(created_at) from payments where payments.order_id = orders.id), orders.order_date) desc"
        );
    }

    private function mapOrderToPaymentRow(Order $order, ?string $dateFrom = null, ?string $dateTo = null): object
    {
        $payment = $order->payments->first();
        $orderDateStr = Carbon::parse($order->order_date)->toDateString();
        $paymentDateStr = $payment ? Carbon::parse($payment->created_at)->toDateString() : null;

        $isOldDebt = false;
        $settledLater = false;
        $settledDate = null;

        $status = $payment?->status ?? match ($order->payment_status) {
            'paid' => 'paid',
            'partial' => 'partial',
            default => 'pending',
        };

        $paidAmount = $payment?->paid_amount ?? ($status === 'paid' ? (float) $order->total_amount : 0);
        $totalAmountKhr = $payment?->total_amount_khr ?? $order->totalKhr();
        $paidAmountKhr = $payment?->paid_amount_khr ?? ($status === 'paid' ? (float) $totalAmountKhr : 0);
        $displayDate = $order->order_date;

        // When viewing a bounded date period:
        if ($dateFrom && $dateTo) {
            // Case 1: Old debt paid in this period
            if ($payment && $paymentDateStr >= $dateFrom && $paymentDateStr <= $dateTo) {
                $isOldDebt = ($orderDateStr < $dateFrom);
                $displayDate = $payment->created_at;
            }
            // Case 2: Order created in this period, but settled on a LATER date
            elseif ($orderDateStr >= $dateFrom && $orderDateStr <= $dateTo && $payment && $paymentDateStr > $dateTo) {
                $settledLater = true;
                $settledDate = $payment->created_at;
                $displayDate = $order->order_date;
            }
            // Case 3: Order created in this period and not yet settled
            else {
                $displayDate = $payment?->created_at ?? $order->order_date;
            }
        } else {
            // Unbounded ('all')
            if ($payment && $orderDateStr < $paymentDateStr) {
                $isOldDebt = true;
                $displayDate = $payment->created_at;
            } else {
                $displayDate = $payment?->created_at ?? $order->order_date;
            }
        }

        $totalAmount = (float) $order->total_amount;
        $balance = max(0, $totalAmount - (float) $paidAmount);
        $balanceKhr = max(0, (float) $totalAmountKhr - (float) $paidAmountKhr);

        $lines = ($paidAmount > 0 && $payment) ? ($payment->lines?->map(fn($line) => [
            'method' => $line->method,
            'currency' => $line->currency,
            'amount_original' => (float) $line->amount_original,
            'amount_usd' => (float) $line->amount_usd,
            'exchange_rate' => (float) ($line->exchange_rate ?: self::EXCHANGE_RATE),
        ])->values() ?? []) : [];

        return (object) [
            'id' => $payment?->id,
            'payment_id' => $payment?->id,
            'source_order_id' => $order->id,
            'customer_name' => $payment?->customer_name ?? $order->customer?->name ?? 'Walk-in Customer',
            'order_id' => 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            'order_date' => $displayDate,
            'order_actual_date' => $order->order_date,
            'payment_date' => $payment?->created_at,
            'total_amount' => $totalAmount,
            'total_amount_khr' => (float) $totalAmountKhr,
            'paid_amount' => min($totalAmount, (float) $paidAmount),
            'paid_amount_khr' => (float) $paidAmountKhr,
            'balance' => $balance,
            'balance_khr' => $balanceKhr,
            'method' => ($paidAmount > 0) ? ($payment?->method ?? '—') : '—',
            'lines' => $lines,
            'status' => $status,
            'notes' => $payment?->notes ?? $order->notes,
            'exchange_rate' => (float) ($payment?->exchange_rate ?? 4000),
            'exchange_rate_notes' => $payment?->exchange_rate_notes,
            'has_exchange_rate_variance' => !empty($payment?->exchange_rate_notes),
            'is_old_debt' => $isOldDebt,
            'settled_later' => $settledLater,
            'settled_date' => $settledDate,
        ];
    }

    private function buildStatsFromRows($all, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        // Actual collected income in this period (payments recorded in this date range)
        if ($dateFrom && $dateTo) {
            $periodPayments = Payment::with('lines')
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->get();
            $collected = (float) $periodPayments->sum('paid_amount');
            $collectedKhr = (float) $periodPayments->sum('paid_amount_khr');
            $methodBreakdown = $this->buildMethodBreakdownFromPayments($periodPayments);
        } else {
            $collected = (float) $all->sum('paid_amount');
            $collectedKhr = (float) $all->sum('paid_amount_khr');
            $methodBreakdown = $this->buildMethodBreakdown($all);
        }

        return [
            'collected'       => $collected,
            'collected_khr'   => $collectedKhr,
            'outstanding'     => $all->sum('balance'),
            'outstanding_khr' => $all->sum('balance_khr'),
            'total'           => $all->count(),
            'paid'            => $all->where('status', 'paid')->count(),
            'partial'         => $all->where('status', 'partial')->count(),
            'unpaid'          => $all->where('status', 'pending')->count(),
            'old_debt'        => $all->where('is_old_debt', true)->count(),
            'method_breakdown' => $methodBreakdown,
        ];
    }

    private function buildMethodBreakdownFromPayments($payments): array
    {
        $methodLabels = [
            'Cash'   => 'លុយក្រៅ',
            'ABA'    => 'ABA Bank',
            'ACLEDA' => 'ACLEDA Bank',
            'Wing'   => 'Wing',
        ];

        $breakdown = [];
        foreach ($methodLabels as $key => $label) {
            $breakdown[$key] = ['label' => $label, 'usd' => 0.0, 'khr' => 0.0];
        }
        $breakdown['Other'] = ['label' => 'ផ្សេងៗ', 'usd' => 0.0, 'khr' => 0.0];

        foreach ($payments as $payment) {
            if ((float) $payment->paid_amount <= 0) {
                continue;
            }

            $lines = $payment->lines;

            if ($lines->isEmpty()) {
                $matchedKey = 'Other';
                foreach ($methodLabels as $key => $label) {
                    if (str_contains((string) $payment->method, $key)) {
                        $matchedKey = $key;
                        break;
                    }
                }

                $breakdown[$matchedKey]['usd'] += (float) $payment->paid_amount;
                $breakdown[$matchedKey]['khr'] += (float) ($payment->paid_amount_khr ?: ($payment->paid_amount * self::EXCHANGE_RATE));
                continue;
            }

            foreach ($lines as $line) {
                $method = is_array($line) ? ($line['method'] ?? 'Other') : ($line->method ?? 'Other');
                $currency = is_array($line) ? ($line['currency'] ?? 'USD') : ($line->currency ?? 'USD');
                $amountOriginal = is_array($line) ? (float) ($line['amount_original'] ?? 0) : (float) ($line->amount_original ?? 0);
                $amountUsd = is_array($line) ? (float) ($line['amount_usd'] ?? 0) : (float) ($line->amount_usd ?? 0);
                $rate = is_array($line) ? (float) ($line['exchange_rate'] ?? self::EXCHANGE_RATE) : (float) ($line->exchange_rate ?? self::EXCHANGE_RATE);

                $key = array_key_exists($method, $methodLabels) ? $method : 'Other';
                $khr = $currency === 'KHR'
                    ? $amountOriginal
                    : round($amountUsd * $rate);

                $breakdown[$key]['usd'] += $amountUsd;
                $breakdown[$key]['khr'] += $khr;
            }
        }

        return $breakdown;
    }

    private function buildMethodBreakdown($all): array
    {
        $methodLabels = [
            'Cash'   => 'លុយក្រៅ',
            'ABA'    => 'ABA Bank',
            'ACLEDA' => 'ACLEDA Bank',
            'Wing'   => 'Wing',
        ];

        $breakdown = [];
        foreach ($methodLabels as $key => $label) {
            $breakdown[$key] = ['label' => $label, 'usd' => 0.0, 'khr' => 0.0];
        }
        $breakdown['Other'] = ['label' => 'ផ្សេងៗ', 'usd' => 0.0, 'khr' => 0.0];

        foreach ($all as $row) {
            if ($row->paid_amount <= 0) {
                continue;
            }

            $lines = $row->lines;

            if (count($lines) === 0) {
                $matchedKey = 'Other';
                foreach ($methodLabels as $key => $label) {
                    if (str_contains($row->method, $key)) {
                        $matchedKey = $key;
                        break;
                    }
                }

                $breakdown[$matchedKey]['usd'] += $row->paid_amount;
                $breakdown[$matchedKey]['khr'] += $row->paid_amount_khr;
                continue;
            }

            foreach ($lines as $line) {
                $method = is_array($line) ? ($line['method'] ?? 'Other') : ($line->method ?? 'Other');
                $currency = is_array($line) ? ($line['currency'] ?? 'USD') : ($line->currency ?? 'USD');
                $amountOriginal = is_array($line) ? (float) ($line['amount_original'] ?? 0) : (float) ($line->amount_original ?? 0);
                $amountUsd = is_array($line) ? (float) ($line['amount_usd'] ?? 0) : (float) ($line->amount_usd ?? 0);
                $rate = is_array($line) ? (float) ($line['exchange_rate'] ?? self::EXCHANGE_RATE) : (float) ($line->exchange_rate ?? self::EXCHANGE_RATE);

                $key = array_key_exists($method, $methodLabels) ? $method : 'Other';
                $khr = $currency === 'KHR'
                    ? $amountOriginal
                    : round($amountUsd * $rate);

                $breakdown[$key]['usd'] += $amountUsd;
                $breakdown[$key]['khr'] += $khr;
            }
        }

        return $breakdown;
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
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);
        $query      = $this->applyFilters($request);
        
        $allOrders  = $query->get();
        $allRows    = $allOrders->map(fn($order) => $this->mapOrderToPaymentRow($order, $dateFrom, $dateTo));

        // Filter by status tab if specified
        $status = $request->get('status', 'all');
        $filteredRows = $allRows;
        if ($status !== 'all') {
            $filteredRows = $allRows->filter(function ($row) use ($status) {
                if ($status === 'paid') return $row->status === 'paid';
                if ($status === 'partial') return $row->status === 'partial';
                if ($status === 'pending') return $row->status === 'pending';
                return true;
            });
        }

        $stats      = $this->buildStatsFromRows($allRows, $dateFrom, $dateTo);
        $deliveries = Delivery::orderBy('delivery_name')->get();

        // Paginate manually from collection
        $page       = (int) $request->get('page', 1);
        $perPage    = 20;
        $payments   = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredRows->forPage($page, $perPage)->values(),
            $filteredRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('payments.index', compact('payments', 'stats', 'deliveries'));
    }

    // ─── Cash Count ───────────────────────────────────────────────────────────

    public function cashCount(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $exchangeRate = (float) $request->get('exchange_rate', self::EXCHANGE_RATE);

        // Find payments recorded on this date with Cash method lines
        $cashLines = PaymentLine::with(['payment.order.customer'])
            ->whereHas('payment', function ($q) use ($date) {
                $q->whereDate('created_at', $date);
            })
            ->where('method', 'Cash')
            ->get();

        $systemCashUsd = (float) $cashLines->where('currency', 'USD')->sum('amount_original');
        $systemCashKhr = (float) $cashLines->where('currency', 'KHR')->sum('amount_original');

        // Also check legacy payments without payment lines
        $legacyPayments = Payment::with(['order.customer'])
            ->whereDate('created_at', $date)
            ->whereDoesntHave('lines')
            ->where('method', 'like', '%Cash%')
            ->get();

        foreach ($legacyPayments as $legacy) {
            $methods = array_filter(array_map('trim', explode('+', $legacy->method ?: 'Cash')));
            $factor = count($methods) > 1 ? (1 / count($methods)) : 1;
            $isKhr = str_contains((string) $legacy->notes, 'KHR') || str_contains((string) $legacy->notes, '៛');

            if ($isKhr) {
                $systemCashKhr += ((float) $legacy->paid_amount_khr) * $factor;
            } else {
                $systemCashUsd += ((float) $legacy->paid_amount) * $factor;
            }
        }

        $cashTransactionsCount = $cashLines->pluck('payment_id')->merge($legacyPayments->pluck('id'))->unique()->count();

        // Pass detailed cash transaction items for audit list
        $cashTransactions = $cashLines->map(function ($line) {
            $payment = $line->payment;
            $order = $payment?->order;
            return (object) [
                'order_code' => $order ? ('ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT)) : 'N/A',
                'customer_name' => $payment?->customer_name ?? $order?->customer?->name ?? 'Walk-in Customer',
                'method_summary' => $payment?->method ?? 'Cash',
                'line_method' => $line->method,
                'currency' => $line->currency,
                'amount_original' => (float) $line->amount_original,
                'amount_usd' => (float) $line->amount_usd,
                'time' => $payment?->created_at ? $payment->created_at->format('h:i A') : '',
            ];
        });

        // Find cash purchases (expenses) paid out of drawer on this date
        $cashPurchases = Purchase::whereDate('purchase_date', $date)
            ->where('status', 'received')
            ->where('payment_method', 'cash')
            ->get();

        $systemCashPurchaseUsd = (float) $cashPurchases->filter(function($p) {
            return ($p->currency ?? 'USD') === 'USD';
        })->sum('total_amount');
        
        $systemCashPurchaseKhr = (float) $cashPurchases->filter(function($p) {
            return ($p->currency ?? 'USD') === 'KHR';
        })->sum('total_amount_khr');

        return view('payments.cash', compact(
            'date',
            'exchangeRate',
            'systemCashUsd',
            'systemCashKhr',
            'cashTransactionsCount',
            'cashTransactions',
            'cashPurchases',
            'systemCashPurchaseUsd',
            'systemCashPurchaseKhr'
        ));
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
            'payment_date' => 'nullable|date',
            'source_order_id' => 'nullable|integer',
        ]);

        $lines = $this->parsePaymentLines($request);
        $paidUsd = collect($lines)->sum('amount_usd');
        $paidKhr = $this->sumLinesKhr($lines);
        $data['paid_amount'] = $paidUsd;
        $data['customer_name'] = $order->customer?->name ?? $data['customer_name'] ?? 'Walk-in Customer';
        $data['order_id'] = $order->id;
        $data['total_amount'] = $order->total_amount;
        $data['total_amount_khr'] = $order->totalKhr();
        $data['paid_amount_khr'] = $paidKhr;

        // Calculate average exchange rate from payment lines
        $avgExchangeRate = collect($lines)->avg('exchange_rate') ?? self::EXCHANGE_RATE;
        $data['exchange_rate'] = $avgExchangeRate;

        // Generate payment notes showing what was paid in each currency
        $paymentNotes = $this->generatePaymentNotes($lines, $avgExchangeRate);
        if ($paymentNotes) {
            $data['notes'] = $paymentNotes;
        }

        // Check for overpayment with exchange rate tolerance
        $overage = (float) $data['paid_amount'] - (float) $data['total_amount'];
        if ($overage > 0) {
            // Check if payment includes multiple currencies (more lenient tolerance)
            $hasMultipleCurrencies = count(collect($lines)->pluck('currency')->unique()) > 1;

            // Allow generous overage tolerance for exchange rate fluctuations
            if ($hasMultipleCurrencies) {
                // For multi-currency: 15% or $100 USD (whichever is smaller)
                $tolerance = min((float) $data['total_amount'] * 0.15, 100);
            } else {
                // For single currency: 10% or $50 USD
                $tolerance = min((float) $data['total_amount'] * 0.10, 50);
            }

            if ($overage > $tolerance) {
                abort(422, "Paid amount cannot exceed order total by more than \${$tolerance}. Overage: \${$overage}");
            } else {
                // Store note about overpayment for display in payment table
                $data['exchange_rate_notes'] = "Overpaid: +\${$overage} (within tolerance)";
            }
        }

        // Auto-set status — compare in KHR (ground truth) so per-line USD rounding
        // dust (e.g. 15.00 + 30.38 landing a float epsilon under 45.38) can't leave
        // an exactly-paid order stuck at "partial".
        $data['status'] = $this->resolveStatus($data['total_amount_khr'], $paidKhr);
        $data['method'] = $this->summarizeMethods($lines);
        unset($data['payment_lines']);

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            $data
        );

        // If a payment date was provided, set the created_at timestamp accordingly
        if ($request->filled('payment_date')) {
            try {
                $payment->created_at = Carbon::parse($request->payment_date);
                $payment->save();
            } catch (\Throwable $e) {
                logger()->warning('Failed to set payment created_at: ' . $e->getMessage());
            }
        }

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
            if ($invoice && $invoice->status !== 'cancelled') {
                $invoiceStatus = $data['status'] === 'paid' ? 'paid' : 'draft';
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
            'payment_date'  => 'nullable|date',
            'source_order_id' => 'nullable|integer',
        ]);

        $lines = $this->parsePaymentLines($request);
        $paidUsd = collect($lines)->sum('amount_usd');
        $paidKhr = $this->sumLinesKhr($lines);
        $data['paid_amount'] = $paidUsd;
        $data['customer_name'] = $order->customer?->name ?? $data['customer_name'] ?? 'Walk-in Customer';
        $data['order_id'] = $order->id;
        $data['total_amount'] = $order->total_amount;
        $data['total_amount_khr'] = $order->totalKhr();
        $data['paid_amount_khr'] = $paidKhr;

        // Calculate average exchange rate from payment lines
        $avgExchangeRate = collect($lines)->avg('exchange_rate') ?? self::EXCHANGE_RATE;
        $data['exchange_rate'] = $avgExchangeRate;

        // Generate payment notes showing what was paid in each currency
        $paymentNotes = $this->generatePaymentNotes($lines, $avgExchangeRate);
        if ($paymentNotes) {
            $data['notes'] = $paymentNotes;
        }

        // Check for overpayment with exchange rate tolerance
        $overage = (float) $data['paid_amount'] - (float) $data['total_amount'];
        if ($overage > 0) {
            // Check if payment includes multiple currencies (more lenient tolerance)
            $hasMultipleCurrencies = count(collect($lines)->pluck('currency')->unique()) > 1;

            // Allow generous overage tolerance for exchange rate fluctuations
            if ($hasMultipleCurrencies) {
                // For multi-currency: 15% or $100 USD (whichever is smaller)
                $tolerance = min((float) $data['total_amount'] * 0.15, 100);
            } else {
                // For single currency: 10% or $50 USD
                $tolerance = min((float) $data['total_amount'] * 0.10, 50);
            }

            if ($overage > $tolerance) {
                abort(422, "Paid amount cannot exceed order total by more than \${$tolerance}. Overage: \${$overage}");
            } else {
                // Store note about overpayment for display in payment table
                $data['exchange_rate_notes'] = "Overpaid: +\${$overage} (within tolerance)";
            }
        }

        // Auto-set status — compare in KHR (ground truth), see store() for why.
        $data['status']      = $this->resolveStatus($data['total_amount_khr'], $paidKhr);
        $data['method'] = $this->summarizeMethods($lines);
        unset($data['payment_lines']);

        $payment->update($data);

        // If a payment date was provided, set the created_at timestamp accordingly
        if ($request->filled('payment_date')) {
            try {
                $payment->created_at = Carbon::parse($request->payment_date);
                $payment->save();
            } catch (\Throwable $e) {
                logger()->warning('Failed to set payment created_at after update: ' . $e->getMessage());
            }
        }

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
                if ($invoice && $invoice->status !== 'cancelled') {
                    $invoiceStatus = $data['status'] === 'paid' ? 'paid' : 'draft';
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
        $periodLabel = $this->periodLabel($request);

        return view('payments.export', compact('payments', 'periodLabel'));
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

    // Sums the real KHR amount of each line (KHR lines as-is, USD lines converted),
    // instead of round-tripping the already-rounded USD total back through the
    // exchange rate — that round trip invents/loses a few KHR and can misfire the
    // paid/partial status on an exactly-paid order.
    private function sumLinesKhr(array $lines): float
    {
        return round(collect($lines)->sum(function ($line) {
            return $line['currency'] === 'KHR'
                ? $line['amount_original']
                : round($line['amount_original'] * $line['exchange_rate']);
        }), 2);
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
        $exchangeRate = (float) $request->input('exchange_rate', self::EXCHANGE_RATE);
        $rawLines = json_decode($request->input('payment_lines', '[]'), true);

        if (!is_array($rawLines) || empty($rawLines)) {
            $amount = (float) $request->input('paid_amount', 0);

            return $amount > 0 ? [[
                'method' => $request->input('method', 'Cash'),
                'currency' => 'USD',
                'amount_original' => $amount,
                'amount_usd' => $amount,
                'exchange_rate' => $exchangeRate,
            ]] : [];
        }

        return collect($rawLines)
            ->map(function ($line) use ($exchangeRate) {
                $currency = strtoupper($line['currency'] ?? 'USD') === 'KHR' ? 'KHR' : 'USD';
                $amountOriginal = max(0, (float) ($line['amount'] ?? 0));
                $amountUsd = $currency === 'KHR'
                    ? round($amountOriginal / $exchangeRate, 2)
                    : round($amountOriginal, 2);

                return [
                    'method' => $line['method'] ?? 'Cash',
                    'currency' => $currency,
                    'amount_original' => $amountOriginal,
                    'amount_usd' => $amountUsd,
                    'exchange_rate' => $exchangeRate,
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

    private function generatePaymentNotes(array $lines, float $exchangeRate): ?string
    {
        if (empty($lines)) {
            return null;
        }

        $parts = [];
        foreach ($lines as $line) {
            $method = $line['method'] ?? 'Cash';
            $currency = $line['currency'] ?? 'USD';
            $orig = (float) ($line['amount_original'] ?? 0);
            if ($currency === 'KHR') {
                $parts[] = sprintf("%s: ៛%s KHR", $method, number_format($orig, 0));
            } else {
                $parts[] = sprintf("%s: $%.2f USD", $method, $orig);
            }
        }

        if (count($parts) > 1) {
            return 'Paid: ' . implode(' + ', $parts);
        } elseif (count($parts) === 1) {
            return 'Paid: ' . $parts[0];
        }

        return null;
    }

    private function syncPaymentLines(Payment $payment, array $lines): void
    {
        $payment->lines()->delete();

        foreach ($lines as $line) {
            if ($payment->created_at) {
                $line['created_at'] = $payment->created_at;
                $line['updated_at'] = $payment->created_at;
            }
            $payment->lines()->create($line);
        }
    }
}
