<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Traits\ExportableSpreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    use ExportableSpreadsheet;

    private const EXCHANGE_RATE = 4000;

    public function daily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $reportDate = Carbon::createFromFormat('Y-m-d', $date);

        $orders = Order::query()
            ->with('customer')
            ->whereDate('order_date', $reportDate)
            ->latest('order_date');

        $totalOrders = (clone $orders)->count();
        $grossSales = (clone $orders)->where('status', '!=', 'cancelled')->sum('total_amount');

        $dayOrders = (clone $orders)->where('status', '!=', 'cancelled')
            ->with(['items', 'payments'])
            ->get();
        $grossSalesKhr = $dayOrders->sum(fn($order) => $order->totalKhr());

        // Outstanding balance owed on today's orders specifically (not to be
        // confused with $income, which is payments recorded today for any order).
        $unpaid = 0.0;
        $unpaidKhr = 0.0;
        foreach ($dayOrders as $order) {
            $payment = $order->payments->first();
            $totalAmount = (float) $order->total_amount;
            $totalKhrAmount = $order->totalKhr();
            $paidAmount = $payment?->paid_amount ?? ($order->payment_status === 'paid' ? $totalAmount : 0);
            $paidKhrAmount = $payment?->paid_amount_khr ?? ($order->payment_status === 'paid' ? $totalKhrAmount : 0);

            $unpaid += max(0, $totalAmount - $paidAmount);
            $unpaidKhr += max(0, $totalKhrAmount - $paidKhrAmount);
        }

        $income = Payment::whereDate('created_at', $reportDate)->sum('paid_amount');
        $incomeKhr = Payment::whereDate('created_at', $reportDate)->sum('paid_amount_khr');
        $expenses = Purchase::whereDate('purchase_date', $reportDate)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        // Purchase has no stored KHR figure, so this is a rate conversion (not a
        // ground-truth KHR value like the other totals above).
        $expensesKhr = $expenses * self::EXCHANGE_RATE;
        $netIncome = $income - $expenses;
        $netIncomeKhr = $incomeKhr - $expensesKhr;

        $soldItems = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.order_date', $reportDate)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('order_items.product_id', 'products.name', 'products.unit')
            ->selectRaw('products.name, products.unit, SUM(order_items.quantity) as quantity, SUM(order_items.total_price) as total')
            ->selectRaw(
                'SUM(COALESCE(order_items.unit_price_khr, order_items.unit_price * ?) * order_items.quantity * (1 - COALESCE(order_items.discount_percent, 0) / 100)) as total_khr',
                [self::EXCHANGE_RATE]
            )
            ->orderByDesc('total_khr')
            ->get();

        $dayPayments = Payment::with(['order.customer', 'lines'])
            ->whereDate('created_at', $reportDate)
            ->latest()
            ->get();
        $payments = $dayPayments->take(10);
        $paymentMethodBreakdown = $this->buildMethodBreakdown($dayPayments);

        $purchases = Purchase::whereDate('purchase_date', $reportDate)
            ->latest()
            ->limit(10)
            ->get();

        $stockMovement = InventoryMovement::query()
            ->join('inventories', 'inventory_movements.inventory_id', '=', 'inventories.id')
            ->leftJoin('products', 'inventory_movements.product_id', '=', 'products.id')
            ->whereDate('inventory_movements.created_at', $reportDate)
            ->groupBy('inventory_movements.inventory_id', 'products.name', 'products.unit', 'inventories.quantity')
            ->selectRaw('products.name, products.unit, inventories.quantity as current_quantity')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change < 0 THEN ABS(inventory_movements.quantity_change) ELSE 0 END) as stock_out')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change > 0 THEN inventory_movements.quantity_change ELSE 0 END) as stock_in')
            ->selectRaw('MAX(inventory_movements.created_at) as last_movement_at')
            ->orderByDesc('last_movement_at')
            ->get();

        $lowStockCount = Inventory::whereColumn('quantity', '<=', 'reorder_level')->count();
        $lowStock = Inventory::with('product')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        $exchangeRate = self::EXCHANGE_RATE;

        return view('reports.daily', compact(
            'date',
            'reportDate',
            'totalOrders',
            'grossSales',
            'grossSalesKhr',
            'unpaid',
            'unpaidKhr',
            'income',
            'incomeKhr',
            'expenses',
            'expensesKhr',
            'netIncome',
            'netIncomeKhr',
            'soldItems',
            'payments',
            'paymentMethodBreakdown',
            'purchases',
            'stockMovement',
            'lowStock',
            'lowStockCount',
            'exchangeRate'
        ));
    }

    /**
     * Break down a set of payments (any date range — a single day or a whole
     * report period) by method (Cash/ABA/ACLEDA/Wing/Other), summing both KHR
     * and USD. Mirrors PaymentController's breakdown but works directly off
     * Payment models rather than mapped order rows.
     */
    private function buildMethodBreakdown($payments): array
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
            $lines = $payment->lines;

            // Legacy payments recorded before the per-line breakdown existed have
            // no lines — fall back to the payment-level (possibly "+"-joined) method.
            if ($lines->isEmpty() && (float) $payment->paid_amount > 0) {
                $matchedKey = 'Other';
                foreach ($methodLabels as $key => $label) {
                    if (str_contains((string) $payment->method, $key)) {
                        $matchedKey = $key;
                        break;
                    }
                }

                $breakdown[$matchedKey]['usd'] += (float) $payment->paid_amount;
                $breakdown[$matchedKey]['khr'] += (float) $payment->paid_amount_khr;
                continue;
            }

            foreach ($lines as $line) {
                $key = array_key_exists($line->method, $methodLabels) ? $line->method : 'Other';
                $rate = (float) ($line->exchange_rate ?: self::EXCHANGE_RATE);
                $khr = $line->currency === 'KHR'
                    ? (float) $line->amount_original
                    : round((float) $line->amount_usd * $rate);

                $breakdown[$key]['usd'] += (float) $line->amount_usd;
                $breakdown[$key]['khr'] += $khr;
            }
        }

        return $breakdown;
    }

    /**
     * Display sales report.
     */
    public function sales(Request $request)
    {
        // Get filter parameters
        $period = $request->input('period', 'all'); // all, today, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build date range query
        $query = Order::query();
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        // Overall metrics
        $totalOrders = $query->count();
        $nonCancelledCount = (clone $query)->where('status', '!=', 'cancelled')->count();
        $totalRevenue = (clone $query)->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenueKhr = (clone $query)->where('status', '!=', 'cancelled')
            ->with('items')
            ->get()
            ->sum(fn($order) => $order->totalKhr());
        // Average order value must divide by the same (non-cancelled) basis the
        // revenue was summed over — dividing by $totalOrders would silently
        // understate the average whenever any order in the period is cancelled.
        $averageOrderValue = $nonCancelledCount > 0 ? ($totalRevenue / $nonCancelledCount) : 0;
        $averageOrderValueKhr = $nonCancelledCount > 0 ? ($totalRevenueKhr / $nonCancelledCount) : 0;
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $pendingOrders = (clone $query)->where('status', 'pending')->count();

        // Period-over-period comparison — only meaningful for a bounded period
        // (today/week/month/year/custom); "all" has no "previous all" to compare
        // against, so comparison stays null and the view shows plain totals.
        $previousRange = $this->getPreviousDateRange($period, $dateRange['start'], $dateRange['end']);
        $comparisonLabel = [
            'today' => 'ម្សិលមិញ',
            'yesterday' => 'ថ្ងៃមុន',
            'week' => 'សប្ដាហ៍មុន',
            'month' => 'ខែមុន',
            'year' => 'ឆ្នាំមុន',
            'custom' => 'រយៈពេលមុន',
        ][$period] ?? null;

        $isPacedComparison = false;
        $revenueDeltaPct = $ordersDeltaPct = $aovDeltaPct = null;
        if ($previousRange['start']) {
            $isOngoing = $dateRange['start'] && $dateRange['end']
                && $dateRange['start']->lte(today()) && $dateRange['end']->gte(today());

            if ($isOngoing) {
                // The current period isn't finished — "today" only has data up to
                // right now. Comparing it to a *complete* previous period repeats
                // the same "partial vs. full day" mistake at a bigger scale (e.g.
                // 2 days into this week vs. a full 7-day last week). Match the
                // previous period's final day to the same time-of-day as now,
                // rather than counting it as a complete day.
                $elapsedDays = $dateRange['start']->diffInDays(today()) + 1;
                $previousRange['end'] = $previousRange['start']->copy()->addDays($elapsedDays - 1);
                $isPacedComparison = true;
                $nowTime = now()->format('H:i:s');

                $prevQuery = Order::query()->where(function ($q) use ($previousRange, $nowTime) {
                    $q->whereDate('order_date', '>=', $previousRange['start'])
                        ->whereDate('order_date', '<', $previousRange['end'])
                        ->orWhere(function ($q2) use ($previousRange, $nowTime) {
                            $q2->whereDate('order_date', $previousRange['end'])
                                ->whereTime('order_date', '<=', $nowTime);
                        });
                });
            } else {
                $prevQuery = Order::query()
                    ->whereDate('order_date', '>=', $previousRange['start'])
                    ->whereDate('order_date', '<=', $previousRange['end']);
            }

            $prevTotalOrders = (clone $prevQuery)->count();
            $prevNonCancelledCount = (clone $prevQuery)->where('status', '!=', 'cancelled')->count();
            $prevRevenue = (clone $prevQuery)->where('status', '!=', 'cancelled')->sum('total_amount');
            $prevAov = $prevNonCancelledCount > 0 ? ($prevRevenue / $prevNonCancelledCount) : 0;

            $pctDelta = fn($current, $previous) => $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : null;
            $revenueDeltaPct = $pctDelta($totalRevenue, $prevRevenue);
            $ordersDeltaPct = $pctDelta($totalOrders, $prevTotalOrders);
            $aovDeltaPct = $pctDelta($averageOrderValue, $prevAov);
        }

        // Revenue by product (cancelled orders don't count as sold)
        $productRevenue = Product::with(['orderItems' => function ($q) use ($dateRange) {
            $q->whereHas('order', fn($oq) => $oq->where('status', '!=', 'cancelled'));
            if ($dateRange['start']) $q->whereDate('created_at', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('created_at', '<=', $dateRange['end']);
        }])
            ->withCount(['orderItems' => function ($q) use ($dateRange) {
                $q->whereHas('order', fn($oq) => $oq->where('status', '!=', 'cancelled'));
                if ($dateRange['start']) $q->whereDate('created_at', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('created_at', '<=', $dateRange['end']);
            }])
            ->orderByDesc('order_items_count')
            ->limit(10)
            ->get();

        // Orders by status
        $ordersByStatus = (clone $query)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->get();

        // Revenue by customer (top 10, cancelled orders excluded)
        $customerRevenue = Customer::withSum(['orders' => function ($q) use ($dateRange) {
            $q->where('status', '!=', 'cancelled');
            if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
        }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->limit(10)
            ->get();

        // Distinct paying customers in the period (cancelled orders don't count).
        $uniqueCustomers = (clone $query)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('customer_id')
            ->distinct('customer_id')
            ->count('customer_id');

        // Payment method breakdown for the period — same categorisation logic
        // buildDailyMethodBreakdown() already uses for a single day, just fed a
        // range-filtered payments collection instead of one day's.
        $periodPayments = Payment::with('lines')
            ->when($dateRange['start'], fn($q) => $q->whereDate('created_at', '>=', $dateRange['start']))
            ->when($dateRange['end'], fn($q) => $q->whereDate('created_at', '<=', $dateRange['end']))
            ->get();
        $paymentMethodBreakdown = $this->buildMethodBreakdown($periodPayments);

        // Most recent orders in the period, for a quick-glance activity list.
        $recentOrders = (clone $query)->with('customer')->latest('order_date')->limit(10)->get();

        // Employee performance — who took the sale (user_id, set at order
        // creation), not who packed it (prepared_by is a different role/step).
        $employeePerformance = (clone $query)
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('users.id', 'users.name', 'users.role')
            ->selectRaw('users.id, users.name, users.role, COUNT(orders.id) as orders_count, SUM(orders.total_amount) as revenue')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // Daily revenue data for chart
        $dailyRevenue = $this->getDailyRevenue($dateRange);
        $exchangeRate = self::EXCHANGE_RATE;

        return view('reports.sales', compact(
            'totalOrders',
            'totalRevenue',
            'totalRevenueKhr',
            'averageOrderValue',
            'averageOrderValueKhr',
            'completedOrders',
            'pendingOrders',
            'uniqueCustomers',
            'paymentMethodBreakdown',
            'recentOrders',
            'employeePerformance',
            'productRevenue',
            'ordersByStatus',
            'customerRevenue',
            'dailyRevenue',
            'period',
            'startDate',
            'endDate',
            'exchangeRate',
            'comparisonLabel',
            'revenueDeltaPct',
            'ordersDeltaPct',
            'aovDeltaPct',
            'isPacedComparison'
        ));
    }

    /**
     * Sales report — Excel export. Respects whatever period/date filter is
     * currently applied on the page, so the file matches what's on screen.
     */
    public function exportSalesExcel(Request $request)
    {
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateRange = $this->getDateRange($period, $startDate, $endDate);
        $orders = $this->getOrdersForSalesExport($dateRange);
        $periodLabel = $this->getPeriodLabel($period, $startDate, $endDate);

        $spreadsheet = $this->createBrandedSpreadsheet('Sales', 'របាយការណ៍ការលក់', 7);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A5', 'រយៈពេល: ' . $periodLabel . '   |   ចំនួនកម្មង់: ' . $orders->count());
        $sheet->mergeCells('A5:G5');
        $sheet->getStyle('A5')->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF64748B');

        $headers = ['ល.រ', 'លេខកម្មង់', 'អតិថិជន', 'កាលបរិច្ឆេទ', 'ស្ថានភាព', 'តម្លៃ (៛)', 'តម្លៃ ($)'];
        $headerRow = 6;
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . $headerRow, $header);
        }

        $row = 7;
        $number = 1;
        $totalKhr = 0;
        $totalUsd = 0;
        foreach ($orders as $order) {
            $khr = round($order->totalKhr(), 0);
            $usd = (float) $order->total_amount;
            $totalKhr += $khr;
            $totalUsd += $usd;

            $sheet->setCellValue("A{$row}", $number);
            $sheet->setCellValue("B{$row}", '#' . $order->id);
            $sheet->setCellValue("C{$row}", $order->customer?->name ?? '—');
            $sheet->setCellValue("D{$row}", optional($order->order_date)->format('d/m/Y'));
            $sheet->setCellValue("E{$row}", ucfirst($order->status));
            $sheet->setCellValue("F{$row}", $khr);
            $sheet->setCellValue("G{$row}", $usd);
            $row++;
            $number++;
        }

        $lastRow = max(7, $row - 1);
        $this->styleTableHeaders($sheet, "A{$headerRow}:G{$headerRow}", "A{$headerRow}:G{$lastRow}");
        $this->applyStripeRows($sheet, 7, $lastRow);

        // Real numeric cells with a display format — not plain text — so the
        // columns can still be summed/filtered directly inside Excel.
        $sheet->getStyle("F7:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("G7:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        if ($orders->count()) {
            $totalRow = $lastRow + 1;
            $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
            $sheet->setCellValue("A{$totalRow}", 'សរុប');
            $sheet->setCellValue("F{$totalRow}", $totalKhr);
            $sheet->setCellValue("G{$totalRow}", $totalUsd);
            $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$totalRow}:G{$totalRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$totalRow}:G{$totalRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF7ED');
        }

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(12);

        return $this->downloadSpreadsheet($spreadsheet, 'sales_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Sales report — printable PDF (opened in a new tab, browser print-to-PDF —
     * same pattern used by inventory/deliveries/customers exports in this app).
     */
    public function exportSalesPdf(Request $request)
    {
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateRange = $this->getDateRange($period, $startDate, $endDate);
        $orders = $this->getOrdersForSalesExport($dateRange);
        $exchangeRate = self::EXCHANGE_RATE;
        $periodLabel = $this->getPeriodLabel($period, $startDate, $endDate);

        return view('reports.sales-pdf', compact('orders', 'exchangeRate', 'periodLabel'));
    }

    /**
     * Human-readable label for a report period filter — shared by the sales
     * view and both of its exports so the three never drift out of sync.
     */
    private function getPeriodLabel(string $period, ?string $startDate, ?string $endDate): string
    {
        return [
            'all' => 'ទាំងអស់',
            'today' => 'ថ្ងៃនេះ',
            'yesterday' => 'ម្សិលមិញ',
            'week' => 'សប្ដាហ៍នេះ',
            'month' => 'ខែនេះ',
            'year' => 'ឆ្នាំនេះ',
            'custom' => trim(($startDate ?? '') . ' - ' . ($endDate ?? '')),
        ][$period] ?? 'ទាំងអស់';
    }

    private function getOrdersForSalesExport(array $dateRange)
    {
        return Order::with(['customer', 'items'])
            ->where('status', '!=', 'cancelled')
            ->when($dateRange['start'], fn($q) => $q->whereDate('order_date', '>=', $dateRange['start']))
            ->when($dateRange['end'], fn($q) => $q->whereDate('order_date', '<=', $dateRange['end']))
            ->latest('order_date')
            ->get();
    }

    /**
     * Display inventory report.
     */
    public function inventory(Request $request)
    {
        $totalProducts = Product::count();
        $lowStockProducts = Inventory::whereRaw('quantity <= reorder_level')
            ->with('product')
            ->get();
        $outOfStockCount = Inventory::where('quantity', '<=', 0)->count();
        $totalInventoryValue = Inventory::query()
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->selectRaw('SUM(GREATEST(inventories.quantity, 0) * COALESCE(NULLIF(inventories.cost_per_unit, 0), products.price_usd, 0)) as total')
            ->value('total') ?? 0;
        // KHR ground truth mirrors the USD calc above, but reads products.price_khr
        // (the stored KHR catalog price) instead of deriving it from price_usd.
        $totalInventoryValueKhr = Inventory::query()
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->selectRaw(
                'SUM(GREATEST(inventories.quantity, 0) * COALESCE(NULLIF(inventories.cost_per_unit, 0) * ?, products.price_khr, 0)) as total',
                [self::EXCHANGE_RATE]
            )
            ->value('total') ?? 0;

        // All inventory with stock status
        $inventory = Inventory::with('product')
            ->orderBy('quantity', 'asc')
            ->paginate(20);

        // Get period for display, and use it to scope the stock flow below
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $stockMovementQuery = InventoryMovement::query()
            ->join('inventories', 'inventory_movements.inventory_id', '=', 'inventories.id')
            ->leftJoin('products', 'inventory_movements.product_id', '=', 'products.id')
            ->groupBy('inventory_movements.inventory_id', 'products.name', 'products.unit', 'inventories.quantity')
            ->selectRaw('products.name, products.unit, inventories.quantity as current_quantity')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change < 0 THEN ABS(inventory_movements.quantity_change) ELSE 0 END) as stock_out')
            ->selectRaw('SUM(CASE WHEN inventory_movements.quantity_change > 0 THEN inventory_movements.quantity_change ELSE 0 END) as stock_in')
            ->selectRaw('MAX(inventory_movements.created_at) as last_movement_at');

        if ($dateRange['start']) {
            $stockMovementQuery->whereDate('inventory_movements.created_at', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $stockMovementQuery->whereDate('inventory_movements.created_at', '<=', $dateRange['end']);
        }

        $stockMovement = $stockMovementQuery->orderByDesc('last_movement_at')->get();

        return view('reports.inventory', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockCount',
            'totalInventoryValue',
            'totalInventoryValueKhr',
            'inventory',
            'stockMovement',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display customer report.
     */
    public function customers(Request $request)
    {
        // Get filter parameters
        $period = $request->input('period', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build date range query
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();

        // Customer activity (orders placed) with date filtering
        $customerActivity = Customer::withCount(['orders' => function ($q) use ($dateRange) {
            if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
            if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
        }])
            ->withSum(['orders' => function ($q) use ($dateRange) {
                $q->where('status', '!=', 'cancelled');
                if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
            }], 'total_amount')
            // Eager-loaded alongside the aggregates above so the KHR ground-truth
            // total (Order::totalKhr(), computed from items) can be summed per
            // customer without re-querying — same filter as the withSum above.
            ->with(['orders' => function ($q) use ($dateRange) {
                $q->where('status', '!=', 'cancelled')->with('items');
                if ($dateRange['start']) $q->whereDate('order_date', '>=', $dateRange['start']);
                if ($dateRange['end']) $q->whereDate('order_date', '<=', $dateRange['end']);
            }])
            ->orderByDesc('orders_count')
            ->paginate(20);

        return view('reports.customers', compact(
            'totalCustomers',
            'activeCustomers',
            'customerActivity',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display dashboard reports.
     */
    public function dashboard(Request $request)
    {
        // Get filter parameters
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build date range query
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Overall metrics
        $query = Order::query();
        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        $totalRevenue = (clone $query)->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenueKhr = (clone $query)->where('status', '!=', 'cancelled')
            ->with('items')
            ->get()
            ->sum(fn($order) => $order->totalKhr());
        $totalOrders = (clone $query)->count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        $recentOrders = (clone $query)->with(['customer', 'items'])->latest('order_date')->limit(5)->get();
        $lowStockAlerts = Inventory::whereRaw('quantity <= reorder_level')
            ->with('product')
            ->limit(5)
            ->get();

        // Revenue data based on period
        $chartData = $this->getChartData($period, $dateRange);

        $exchangeRate = self::EXCHANGE_RATE;

        return view('reports.dashboard', compact(
            'totalRevenue',
            'totalRevenueKhr',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'lowStockAlerts',
            'chartData',
            'period',
            'startDate',
            'endDate',
            'exchangeRate'
        ));
    }

    /**
     * Get chart data based on period selection.
     */
    private function getChartData($period, $dateRange)
    {
        $query = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->where('status', '!=', 'cancelled')
            ->groupByRaw('DATE(order_date)');

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        return $query->orderBy('date', 'asc')->get();
    }

    /**
     * Get date range based on period selection.
     */
    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        $start = null;
        $end = null;

        switch ($period) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today();
                break;
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::yesterday();
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $start = $startDate ? Carbon::createFromFormat('Y-m-d', $startDate) : null;
                $end = $endDate ? Carbon::createFromFormat('Y-m-d', $endDate) : null;
                break;
            case 'all':
            default:
                $start = null;
                $end = null;
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * The period immediately preceding the given one, same length — used to
     * give sales-report KPIs an honest "vs previous period" comparison.
     * Returns null start/end when there's no meaningful "previous" (e.g. "all").
     */
    private function getPreviousDateRange(string $period, ?Carbon $start, ?Carbon $end): array
    {
        switch ($period) {
            case 'today':
                return ['start' => Carbon::yesterday(), 'end' => Carbon::yesterday()];
            case 'yesterday':
                return ['start' => Carbon::yesterday()->subDay(), 'end' => Carbon::yesterday()->subDay()];
            case 'week':
                return [
                    'start' => Carbon::now()->subWeek()->startOfWeek(),
                    'end' => Carbon::now()->subWeek()->endOfWeek(),
                ];
            case 'month':
                return [
                    'start' => Carbon::now()->subMonthNoOverflow()->startOfMonth(),
                    'end' => Carbon::now()->subMonthNoOverflow()->endOfMonth(),
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->subYear()->startOfYear(),
                    'end' => Carbon::now()->subYear()->endOfYear(),
                ];
            case 'custom':
                if ($start && $end) {
                    $days = $start->diffInDays($end) + 1;
                    return ['start' => $start->copy()->subDays($days), 'end' => $start->copy()->subDay()];
                }
                return ['start' => null, 'end' => null];
            case 'all':
            default:
                return ['start' => null, 'end' => null];
        }
    }

    /**
     * Get daily revenue data for chart.
     */
    private function getDailyRevenue($dateRange)
    {
        $query = Order::selectRaw(
            'DATE(order_date) as date, SUM(total_amount) as total, COUNT(*) as count'
        )->where('status', '!=', 'cancelled')
            ->groupByRaw('DATE(order_date)');

        if ($dateRange['start']) {
            $query->whereDate('order_date', '>=', $dateRange['start']);
        }
        if ($dateRange['end']) {
            $query->whereDate('order_date', '<=', $dateRange['end']);
        }

        return $query->orderBy('date', 'asc')->get();
    }
}
