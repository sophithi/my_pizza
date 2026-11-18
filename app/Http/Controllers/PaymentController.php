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
            $request->validate([
                'date_from' => 'nullable|date_format:Y-m-d',
                'date_to' => 'nullable|date_format:Y-m-d',
            ]);
            $from = $request->date_from ? Carbon::parse($request->date_from)->toDateString() : null;
            $to   = $request->date_to ? Carbon::parse($request->date_to)->toDateString() : null;
            if (!$from && !$to) {
                $from = $to = $today->toDateString();
            }
            if ($from && !$to) $to = $from;
            if ($to && !$from) $from = $to;
            if ($to < $from) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'date_to' => 'The end date must be on or after the start date.',
                ]);
            }
            $request->merge(['date_from' => $from, 'date_to' => $to]);
            return [$from, $to];
        }

        return [null, null];
    }

    private function applyFilters(Request $request)
    {
        $query = Order::with([
            'customer',
            // totalKhr() reads both relations for every row, including stats and
            // exports. Loading them here avoids two extra queries per order.
            'items',
            'invoice',
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
            $query->whereHas('payments', function ($q) use ($method, $dateFrom, $dateTo) {
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
                }
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
        $payments = $order->payments;
        $payment = $payments->first();
        $orderDateStr = Carbon::parse($order->order_date)->toDateString();
        $paymentDateStr = $payment ? Carbon::parse($payment->created_at)->toDateString() : null;
        $periodPayments = collect();
        if ($dateFrom && $dateTo) {
            $periodPayments = $payments->filter(function ($item) use ($dateFrom, $dateTo) {
                $paymentDate = Carbon::parse($item->created_at)->toDateString();

                return $paymentDate >= $dateFrom && $paymentDate <= $dateTo;
            });
        }

        $isOldDebt = false;
        $settledLater = false;
        $settledDate = null;
        $hasInstallments = $payments->count() > 1;

        $totalAmountKhr = (float) $order->totalKhr();
        $totalAmount = $totalAmountKhr / self::EXCHANGE_RATE;
        $paidAmountKhr = $payments->sum(fn($item) => (float) ($item->paid_amount_khr ?: 0));
        $paidAmount = $paidAmountKhr / self::EXCHANGE_RATE;
        if ($payments->isEmpty() && $order->payment_status === 'paid') {
            $paidAmount = $totalAmount;
            $paidAmountKhr = $totalAmountKhr;
        }
        $status = $this->resolveStatus($totalAmountKhr, $paidAmountKhr);
        $displayDate = $order->order_date;

        // When viewing a bounded date period:
        if ($dateFrom && $dateTo) {
            // Case 1: Old debt paid in this period
            if ($periodPayments->isNotEmpty()) {
                $isOldDebt = $orderDateStr < $dateFrom;
                $displayDate = $periodPayments->first()->created_at;
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
            $displayDate = $payment?->created_at ?? $order->order_date;
        }

        $balanceKhr = max(0, (float) $totalAmountKhr - (float) $paidAmountKhr);
        $balance = $balanceKhr / self::EXCHANGE_RATE;
        $periodPaidAmount = null;
        $periodPaidAmountKhr = null;
        if ($dateFrom && $dateTo) {
            $periodPaidAmountKhr = $periodPayments->sum(fn($item) => (float) ($item->paid_amount_khr ?: 0));
            $periodPaidAmount = $periodPaidAmountKhr / self::EXCHANGE_RATE;
        }

        $lines = ($paidAmount > 0 && $payment) ? ($payment->lines?->map(fn($line) => [
            'method' => $line->method,
            'currency' => $line->currency,
            'amount_original' => (float) $line->amount_original,
            'amount_usd' => (float) $line->amount_usd,
            'exchange_rate' => (float) ($line->exchange_rate ?: self::EXCHANGE_RATE),
        ])->values() ?? []) : [];
        $paymentNotes = $payments
            ->filter(fn($item) => (float) $item->paid_amount > 0 || (float) ($item->paid_amount_khr ?: 0) > 0)
            ->map(fn($item) => $item->notes)
            ->filter()
            ->values()
            ->join(' | ');
        $installmentSummary = $payments
            ->filter(fn($item) => (float) $item->paid_amount > 0 || (float) ($item->paid_amount_khr ?: 0) > 0)
            ->sortBy('created_at')
            ->map(function ($item) {
                $amounts = $item->lines->map(function ($line) {
                    return $line->currency === 'KHR'
                        ? '៛' . number_format((float) $line->amount_original, 0)
                        : '$' . number_format((float) $line->amount_original, 2);
                })->values()->implode(' + ');

                if ($amounts === '') {
                    $amounts = '$' . number_format((float) $item->paid_amount, 2);
                }

                return Carbon::parse($item->created_at)->format('d M') . ': '
                    . ($item->method ?: 'ការទូទាត់') . ' ' . $amounts;
            })
            ->values()
            ->implode(' | ');

        return (object) [
            'id' => $payment?->id,
            'payment_id' => $payment?->id,
            'source_order_id' => $order->id,
            'customer_name' => $payment?->customer_name ?? $order->customer?->name ?? 'Walk-in Customer',
            'order_id' => 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            'order_date' => $displayDate,
            'order_actual_date' => $order->order_date,
            'order_status' => $order->status,
            'payment_date' => $payment?->created_at,
            'total_amount' => $totalAmount,
            'total_amount_khr' => (float) $totalAmountKhr,
            'paid_amount' => min($totalAmount, (float) $paidAmount),
            'paid_amount_khr' => (float) $paidAmountKhr,
            'installment_paid_amount_khr' => (float) ($payment?->paid_amount_khr ?? 0),
            'period_paid_amount' => $periodPaidAmount,
            'period_paid_amount_khr' => $periodPaidAmountKhr,
            'balance' => $balance,
            'balance_khr' => $balanceKhr,
            'method' => ($paidAmount > 0)
                ? $payments->filter(fn($item) => (float) $item->paid_amount > 0 || (float) ($item->paid_amount_khr ?: 0) > 0)->pluck('method')->filter()->unique()->join(' + ')
                : '—',
            'lines' => $lines,
            'status' => $status,
            'notes' => $paymentNotes ?: $order->notes,
            'installment_summary' => $installmentSummary,
            'exchange_rate' => (float) ($payment?->exchange_rate ?? 4000),
            'exchange_rate_notes' => $payment?->exchange_rate_notes,
            'has_exchange_rate_variance' => !empty($payment?->exchange_rate_notes),
            'is_old_debt' => $isOldDebt,
            'settled_later' => $settledLater,
            'settled_date' => $settledDate,
            'has_installments' => $hasInstallments,
        ];
    }

    private function buildStatsFromRows($all, ?string $dateFrom = null, ?string $dateTo = null, ?string $method = null): array
    {
        // Use the same orders as the table, including delivery, method and search filters.
        // Load every installment rather than only the latest payment shown on a row.
        $paymentQuery = Payment::with('lines')
            ->whereIn('order_id', $all->pluck('source_order_id')->unique());
        if ($dateFrom && $dateTo) {
            $paymentQuery->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        }
        $periodPayments = $paymentQuery->get();
        $methodBreakdown = $this->buildMethodBreakdownFromPayments($periodPayments, $method);

        $oldDebtRows = $all->where('is_old_debt', true);
        $currentRows = $all->filter(function ($row) {
            return !$row->is_old_debt && ($row->order_status ?? null) !== 'cancelled';
        });
        $collected = (float) $currentRows->sum(fn($row) => max(0, (float) $row->total_amount - (float) $row->balance));
        $collectedKhr = (float) $currentRows->sum(fn($row) => max(0, (float) $row->total_amount_khr - (float) $row->balance_khr));
        $oldDebtCollected = (float) $oldDebtRows->sum(fn($row) =>
            $dateFrom && $dateTo ? ($row->period_paid_amount ?? 0) : $row->paid_amount
        );
        $oldDebtCollectedKhr = (float) $oldDebtRows->sum(fn($row) =>
            $dateFrom && $dateTo ? ($row->period_paid_amount_khr ?? 0) : $row->paid_amount_khr
        );

        // Collection totals describe receipts in the selected period and method;
        // balances and paid/unpaid counts still describe the complete orders.
        if (($dateFrom && $dateTo) || $method) {
            $currentIds = $currentRows->pluck('source_order_id');
            $oldDebtIds = $oldDebtRows->pluck('source_order_id');
            $currentBreakdown = $this->buildMethodBreakdownFromPayments(
                $periodPayments->whereIn('order_id', $currentIds), $method
            );
            $debtBreakdown = $this->buildMethodBreakdownFromPayments(
                $periodPayments->whereIn('order_id', $oldDebtIds), $method
            );
            $collected = (float) array_sum(array_column($currentBreakdown, 'usd'));
            $collectedKhr = (float) array_sum(array_column($currentBreakdown, 'khr'));
            $oldDebtCollected = (float) array_sum(array_column($debtBreakdown, 'usd'));
            $oldDebtCollectedKhr = (float) array_sum(array_column($debtBreakdown, 'khr'));
        }

        $currentOutstanding = (float) $currentRows->sum('balance');
        $currentOutstandingKhr = (float) $currentRows->sum('balance_khr');

        return [
            'collected'              => $collected,
            'collected_khr'          => $collectedKhr,
            'collected_excluding_old_debt' => $collected,
            'collected_excluding_old_debt_khr' => $collectedKhr,
            'outstanding'            => $currentOutstanding,
            'outstanding_khr'        => $currentOutstandingKhr,
            'old_debt_collected'     => $oldDebtCollected,
            'old_debt_collected_khr' => $oldDebtCollectedKhr,
            'total'                  => $all->count(),
            'paid'                   => $all->where('status', 'paid')->count(),
            'partial'                => $all->where('status', 'partial')->count(),
            'unpaid'                 => $all->where('status', 'pending')->count(),
            'old_debt'               => $oldDebtRows->count(),
            'method_breakdown'       => $methodBreakdown,
        ];
    }

    private function buildMethodBreakdownFromPayments($payments, ?string $methodFilter = null): array
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
            if ((float) $payment->paid_amount_khr <= 0) {
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

                $breakdown[$matchedKey]['usd'] += (float) $payment->paid_amount_khr / self::EXCHANGE_RATE;
                $breakdown[$matchedKey]['khr'] += (float) $payment->paid_amount_khr;
                continue;
            }

            foreach ($lines as $line) {
                $method = is_array($line) ? ($line['method'] ?? 'Other') : ($line->method ?? 'Other');
                $currency = is_array($line) ? ($line['currency'] ?? 'KHR') : ($line->currency ?? 'KHR');
                $amountOriginal = is_array($line) ? (float) ($line['amount_original'] ?? 0) : (float) ($line->amount_original ?? 0);

                $key = array_key_exists($method, $methodLabels) ? $method : 'Other';
                if ($currency !== 'KHR') {
                    throw new \RuntimeException('Normalize historical receipts using payments:normalize-khr before reporting.');
                }
                $khr = $amountOriginal;

                $breakdown[$key]['usd'] += $khr / self::EXCHANGE_RATE;
                $breakdown[$key]['khr'] += $khr;
            }
        }

        if ($methodFilter) {
            $selectedKey = $methodFilter === 'other' ? 'Other' : $methodFilter;
            foreach ($breakdown as $key => &$amounts) {
                if ($key !== $selectedKey) {
                    $amounts['usd'] = 0.0;
                    $amounts['khr'] = 0.0;
                }
            }
            unset($amounts);
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
        if (!$request->has('period')) {
            $hasDateFilter = $request->filled('date_from') || $request->filled('date_to');
            $request->merge(['period' => $hasDateFilter ? 'custom' : 'today']);
        }

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
                if ($status === 'old_debt') return (bool) $row->is_old_debt;
                return true;
            });
        }

        $stats      = $this->buildStatsFromRows($allRows, $dateFrom, $dateTo, $request->get('method'));
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
        return \Illuminate\Support\Facades\DB::transaction(fn() => $this->storePayment($request));
    }

    private function storePayment(Request $request)
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
        $paidKhr = $this->sumLinesKhr($lines);
        $paidUsd = round($paidKhr / self::EXCHANGE_RATE, 2);
        $data['paid_amount'] = $paidUsd;
        $data['customer_name'] = $order->customer?->name ?? $data['customer_name'] ?? 'Walk-in Customer';
        $data['order_id'] = $order->id;
        $data['total_amount'] = round($order->totalKhr() / self::EXCHANGE_RATE, 2);
        $data['total_amount_khr'] = $order->totalKhr();
        $data['paid_amount_khr'] = $paidKhr;

        // Calculate average exchange rate from payment lines
        $avgExchangeRate = collect($lines)->avg('exchange_rate') ?? self::EXCHANGE_RATE;
        $data['exchange_rate'] = $avgExchangeRate;

        // Generate payment notes showing what was paid in each currency
        $paymentNotes = $this->generatePaymentNotes($lines);
        if ($paymentNotes) {
            $data['notes'] = $paymentNotes;
        }

        // Auto-set status — compare in KHR (ground truth) so per-line USD rounding
        // dust (e.g. 15.00 + 30.38 landing a float epsilon under 45.38) can't leave
        // an exactly-paid order stuck at "partial".
        $data['status'] = $this->resolveStatus($data['total_amount_khr'], $paidKhr);
        $data['method'] = $this->summarizeMethods($lines);
        $this->validatePaidKhr($paidKhr, (float) $data['total_amount_khr']);
        if (!$request->boolean('additional_payment') && $order->payments()->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'payment_lines' => 'Please edit an existing installment or add a new payment.',
            ]);
        }

        if ($request->boolean('additional_payment')) {
            $existing = $order->payments()->get();
            $cumulativeKhr = $existing->sum(fn($item) => (float) ($item->paid_amount_khr ?: 0)) + $paidKhr;
            if ($cumulativeKhr > (float) $data['total_amount_khr']) {
                $remainingKhr = max(0, (float) $data['total_amount_khr'] - $existing->sum(fn($item) => (float) ($item->paid_amount_khr ?: 0)));
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_lines' => 'ចំនួនបង់បន្ថែមលើសប្រាក់នៅសល់ ' . number_format($remainingKhr, 0) . '៛។',
                ]);
            }
            $data['status'] = $this->resolveStatus((float) $data['total_amount_khr'], $cumulativeKhr);

            unset($data['payment_lines']);
            $payment = Payment::create($data);
            if ($request->filled('payment_date')) {
                $payment->created_at = Carbon::parse($request->payment_date);
                $payment->save();
            }
            $this->syncPaymentLines($payment, $lines);
            $order->update(['payment_status' => $data['status'] === 'pending' ? 'unpaid' : $data['status']]);

            // Keep the linked invoice status in sync when an installment completes the order.
            try {
                $invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
                if ($invoice && $invoice->status !== 'cancelled') {
                    $invoiceStatus = $data['status'] === 'paid' ? 'paid' : 'draft';
                    $invoice->update(['status' => $invoiceStatus]);
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to update invoice status after additional payment: ' . $e->getMessage());
            }

            return back()->with('success', 'Additional payment recorded successfully.');
        }

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
        return \Illuminate\Support\Facades\DB::transaction(fn() => $this->updatePayment($request, $payment));
    }

    private function updatePayment(Request $request, Payment $payment)
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
        $paidKhr = $this->sumLinesKhr($lines);
        $paidUsd = round($paidKhr / self::EXCHANGE_RATE, 2);
        $data['paid_amount'] = $paidUsd;
        $data['customer_name'] = $order->customer?->name ?? $data['customer_name'] ?? 'Walk-in Customer';
        $data['order_id'] = $order->id;
        $data['total_amount'] = round($order->totalKhr() / self::EXCHANGE_RATE, 2);
        $data['total_amount_khr'] = $order->totalKhr();
        $data['paid_amount_khr'] = $paidKhr;

        // Calculate average exchange rate from payment lines
        $avgExchangeRate = collect($lines)->avg('exchange_rate') ?? self::EXCHANGE_RATE;
        $data['exchange_rate'] = $avgExchangeRate;

        // Generate payment notes showing what was paid in each currency
        $paymentNotes = $this->generatePaymentNotes($lines);
        if ($paymentNotes) {
            $data['notes'] = $paymentNotes;
        }

        // Auto-set status — compare in KHR (ground truth), see store() for why.
        $data['status']      = $this->resolveStatus($data['total_amount_khr'], $paidKhr);
        $data['method'] = $this->summarizeMethods($lines);
        $otherPaidKhr = (float) $order->payments()->where('id', '!=', $payment->id)->sum('paid_amount_khr');
        $this->validatePaidKhr($paidKhr + $otherPaidKhr, (float) $data['total_amount_khr']);
        $data['status'] = $this->resolveStatus((float) $data['total_amount_khr'], $paidKhr + $otherPaidKhr);

        if ($paidKhr <= 0) {
            $payment->lines()->delete();
            $payment->delete();

            $remainingPaidKhr = $order->payments()->sum('paid_amount_khr');
            $remainingStatus = $this->resolveStatus((float) $order->totalKhr(), (float) $remainingPaidKhr);
            $order->update(['payment_status' => $remainingStatus === 'pending' ? 'unpaid' : $remainingStatus]);

            return back()->with('success', 'Empty payment installment removed.');
        }

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
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);
        $payments = $this->applyFilters($request)->get()
            ->map(fn($order) => $this->mapOrderToPaymentRow($order, $dateFrom, $dateTo));
        $periodLabel = $this->periodLabel($request);

        return view('payments.export', compact('payments', 'periodLabel'));
    }

    // ─── Export PDF ───────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);
        $query       = $this->applyFilters($request);
        $payments    = $query->get()->map(fn($order) => $this->mapOrderToPaymentRow($order, $dateFrom, $dateTo));
        $stats       = $this->buildStatsFromRows($payments, $dateFrom, $dateTo, $request->get('method'));
        $periodLabel = $this->periodLabel($request);
        $statusLabel = match ($request->get('status', 'all')) {
            'paid'    => 'បានបង់',
            'partial' => 'បង់ខ្លះ',
            'pending' => 'មិនទាន់បង់',
            'old_debt'=> 'បានសងបុងចាស់',
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

    private function validatePaidKhr(float $paidKhr, float $totalKhr): void
    {
        if ($paidKhr > $totalKhr) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'payment_lines' => 'Payment exceeds the invoice balance in KHR.',
            ]);
        }
    }

    private function sumLinesKhr(array $lines): float
    {
        return (float) array_sum(array_column($lines, 'amount_original'));
    }

    private function resolveOrder(Request $request, ?int $fallbackOrderId = null): Order
    {
        $sourceOrderId = $request->integer('source_order_id') ?: $fallbackOrderId;

        if (!$sourceOrderId && $request->filled('order_id')) {
            preg_match('/(\d+)/', (string) $request->order_id, $matches);
            $sourceOrderId = isset($matches[1]) ? (int) $matches[1] : null;
        }

        abort_unless($sourceOrderId, 422, 'Please select a valid order.');

        return Order::with('customer')->lockForUpdate()->findOrFail($sourceOrderId);
    }

    private function parsePaymentLines(Request $request): array
    {
        $data = $request->validate([
            'payment_lines' => 'nullable|json',
            'paid_amount_khr' => 'nullable|integer|min:0',
        ]);
        $rawLines = json_decode($data['payment_lines'] ?? '[]', true);
        if ($rawLines === []) {
            if (!$request->filled('paid_amount_khr') && (float) $request->input('paid_amount', 0) > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'payment_lines' => 'Please enter the original amount in KHR.',
                ]);
            }
            $rawLines = [[
                'method' => $request->input('method', 'Cash'),
                'currency' => 'KHR',
                'amount' => $data['paid_amount_khr'] ?? 0,
            ]];
        }
        $validated = validator(['lines' => $rawLines], [
            'lines' => 'required|array',
            'lines.*' => 'required|array',
            'lines.*.method' => 'required|in:Cash,ABA,ACLEDA,Wing,Other',
            'lines.*.currency' => 'sometimes|in:KHR',
            'lines.*.amount' => 'required|integer|min:0',
        ])->validate();

        return collect($validated['lines'])->map(fn($line) => [
            'method' => $line['method'],
            'currency' => 'KHR',
            'amount_original' => (int) $line['amount'],
            'amount_usd' => round((int) $line['amount'] / self::EXCHANGE_RATE, 2),
            'exchange_rate' => self::EXCHANGE_RATE,
        ])->filter(fn($line) => $line['amount_original'] > 0)->values()->all();
    }

    private function summarizeMethods(array $lines): string
    {
        if (empty($lines)) {
            return '—';
        }

        return collect($lines)->pluck('method')->unique()->join(' + ');
    }

    private function generatePaymentNotes(array $lines): ?string
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
