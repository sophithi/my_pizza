<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = $this->filteredInvoiceQuery($request)->withCount('items');
        $statsQuery = $this->filteredInvoiceQuery($request);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'paid' => (clone $statsQuery)->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))->count(),
            'unpaid' => (clone $statsQuery)->whereHas('order', fn($q) => $q->where('payment_status', '!=', 'paid'))->count(),
            'amount_usd' => (clone $statsQuery)->sum('total_amount'),
            'amount_khr' => (clone $statsQuery)->get()->sum(fn($invoice) => $this->invoiceTotalKhr($invoice)),
        ];

        $invoices = $this->orderInvoices($query)->paginate(15)->withQueryString();
        $invoices->getCollection()->each(function ($invoice) {
            $invoice->total_khr = $this->invoiceTotalKhr($invoice);
        });

        // Staff filter dropdown — everyone who has ever created an order,
        // so the list stays accurate as staff are added/removed.
        $staffUsers = User::whereHas('orders')->orderBy('name')->get(['id', 'name']);

        // Only admin/manager see the close/undo/merge-back buttons, so skip
        // the extra queries entirely for other roles.
        $isAdminOrManager = auth()->user()->isAdmin() || auth()->user()->isManager();
        $canUndoClosePeriod = $isAdminOrManager && \App\Models\InvoicePeriod::canUndoLastClose();
        $mergeBackPreview = $isAdminOrManager && !$canUndoClosePeriod
            ? \App\Models\InvoicePeriod::previewMergeBackIntoPrevious()
            : [];

        return view('invoices.index', compact('invoices', 'stats', 'staffUsers', 'canUndoClosePeriod', 'mergeBackPreview'));
    }

    public function exportReport(Request $request)
    {
        $exportQuery = $this->filteredInvoiceQuery($request)->withCount('items');
        $invoices = $this->orderInvoices($exportQuery)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Invoice Report');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Khmer OS Battambang')->setSize(9);
        $sheet->setShowGridlines(false);
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A5)
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()
            ->setTop(0.25)
            ->setRight(0.2)
            ->setBottom(0.25)
            ->setLeft(0.2);

        $logoPath = public_path('assets/logos/logo_pizza.png');
        if (file_exists($logoPath)) {
            $logo = new Drawing();
            $logo->setName('Pizza Happy Family');
            $logo->setPath($logoPath);
            $logo->setHeight(42);
            $logo->setCoordinates('A1');
            $logo->setOffsetX(8);
            $logo->setOffsetY(7);
            $logo->setWorksheet($sheet);
        }

        $sheet->mergeCells('A1:G4');
        $sheet->getStyle('A1:G4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF7ED'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFF3D6C7'],
                ],
            ],
        ]);

        $sheet->unmergeCells('A1:G4');
        $sheet->mergeCells('B1:G1');
        $sheet->setCellValue('B1', 'Pizza Happy Family');
        $sheet->mergeCells('B2:G2');
        $sheet->setCellValue('B2', 'របាយការណ៍វិក្ក័យប័ត្រ');
        $sheet->mergeCells('B3:G3');
        $sheet->setCellValue('B3', 'កាលបរិច្ឆេទនាំចេញ: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A4:G4');
        $sheet->setCellValue('A4', ' ');

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(19);
        $sheet->getRowDimension(3)->setRowHeight(17);
        $sheet->getRowDimension(4)->setRowHeight(7);
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FFE85D24');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF111827');
        $sheet->getStyle('B3')->getFont()->setSize(8)->getColor()->setARGB('FF64748B');
        $sheet->getStyle('B1:B3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE85D24');

        $headers = [
            'ល.រ',
            'អតិថិជន',
            'លេខទូរស័ព្ទ',
            'ចំនួនទំនិញ',
            'ទឹកប្រាក់',
            'កាលបរិច្ឆេទ',
            'ស្ថានភាព',
        ];

        $headerRow = 6;
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . $headerRow, $header);
        }

        $row = 7;
        $number = 1;
        foreach ($invoices as $invoice) {
            $sheet->setCellValue("A{$row}", $number);
            $sheet->setCellValue("B{$row}", $invoice->order?->customer?->name ?? 'N/A');
            $sheet->setCellValueExplicit("C{$row}", (string) ($invoice->order?->customer?->phone ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $invoice->items_count);
            $sheet->setCellValue("E{$row}", $this->invoiceTotalKhr($invoice));
            $sheet->setCellValue("F{$row}", $invoice->invoice_date?->format('d/m/Y') ?? '');
            $sheet->setCellValue("G{$row}", $this->invoiceStatusLabel($invoice->status));
            $row++;
            $number++;
        }

        $lastRow = max(7, $row - 1);
        $tableRange = "A{$headerRow}:G{$lastRow}";

        $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE85D24'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD9DEE7'],
                ],
            ],
        ]);

        for ($stripeRow = 7; $stripeRow <= $lastRow; $stripeRow++) {
            if ($stripeRow % 2 === 0) {
                $sheet->getStyle("A{$stripeRow}:G{$stripeRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFAFAFA');
            }
        }

        $totalRow = $lastRow + 2;
        $sheet->mergeCells("A{$totalRow}:D{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'សរុបទឹកប្រាក់');
        $sheet->setCellValue("E{$totalRow}", $invoices->sum(fn($invoice) => $this->invoiceTotalKhr($invoice)));
        $sheet->mergeCells("F{$totalRow}:G{$totalRow}");
        $sheet->setCellValue("F{$totalRow}", 'វិក្ក័យប័ត្រ: ' . $invoices->count());
        $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FF111827'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF7ED'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFF3D6C7'],
                ],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("E{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$totalRow}")->getNumberFormat()->setFormatCode('"៛"#,##0');
        $sheet->getStyle("F{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A7:G{$lastRow}")->getFont()->setSize(9);
        $sheet->getStyle("A7:G{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D7:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E7:E{$lastRow}")->getNumberFormat()->setFormatCode('"៛"#,##0');
        $sheet->getStyle("A7:A{$lastRow}")->getFont()->setBold(true);

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(13);

        $sheet->getRowDimension($headerRow)->setRowHeight(22);
        for ($currentRow = 7; $currentRow <= $lastRow; $currentRow++) {
            $sheet->getRowDimension($currentRow)->setRowHeight(20);
        }

        $sheet->freezePane('A7');
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setPrintArea('A1:G' . max(22, $totalRow + 2));

        $filename = 'invoice_report_' . now()->format('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . $filename);

        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function filteredInvoiceQuery(Request $request)
    {
        $query = Invoice::with(['order.customer', 'order.user', 'order.items.product']);
        $period = $request->get('period');

        if ($period === 'today') {
            $query->whereDate('invoice_date', today());
        } elseif ($period === 'yesterday') {
            $query->whereDate('invoice_date', today()->subDay());
        } elseif ($period === 'month') {
            $query->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('invoice_date', now()->year);
        } elseif ($request->filled('date')) {
            $query->whereDate('invoice_date', $request->date);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->whereHas('order', fn($q) => $q->where('user_id', $request->user_id));
        }

        if ($request->filled('printed') && $request->printed !== 'all') {
            if ($request->printed === 'printed') {
                $query->whereNotNull('printed_at');
            } else {
                $query->whereNull('printed_at');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('order.customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    private function orderInvoices($query)
    {
        // Tiebreaker is row id (true creation order), not the invoice
        // number's numeric part — that no longer tracks creation order once
        // a period close resets it mid-day (see invoice_periods).
        return $query
            ->orderByDesc('invoice_date')
            ->orderByDesc('invoices.id');
    }

    private function invoiceTotalKhr(Invoice $invoice): float
    {
        // Canonical total — see Order::totalKhr(), so this always matches
        // the order/invoice/packing pages instead of re-deriving its own
        // formula (which previously converted the USD discount at a flat
        // rate, drifting whenever a custom KHR price was used).
        return $invoice->order?->totalKhr() ?? 0;
    }

    private function invoiceStatusLabel(?string $status): string
    {
        return match ($status) {
            'paid' => 'បានទូទាត់',
            'sent', 'draft', 'pending' => 'មិនទាន់ទូទាត់',
            'cancelled' => 'មិនទូទាត់',
            default => ucfirst((string) $status),
        };
    }


    public function create()
    {
        return redirect()->route('orders.create')->with('info', 'វិក្ក័យប័ត្របានបង្កើតដោយស្វយប្រវត្តិ នៅពេលដែលលទ្ធផលបញ្ចប់។');
    }


    public function printIndex(Request $request)
    {
        $query = Invoice::with(['order', 'order.customer']);
        $query->whereNotNull('packing_sent_at');
        $period = $request->get('period');

        if ($period === 'today') {
            $query->whereDate('packing_sent_at', today());
        } elseif ($period === 'yesterday') {
            $query->whereDate('packing_sent_at', today()->subDay());
        } elseif ($period === 'month') {
            $query->whereMonth('packing_sent_at', now()->month)->whereYear('packing_sent_at', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('packing_sent_at', now()->year);
        } elseif ($request->filled('date')) {
            $query->whereDate('packing_sent_at', $request->date);
        }
        // If no period or date filter provided, default to today's invoices.
        if (!$period && !$request->filled('date')) {
            $period = 'today';
            $query->whereDate('packing_sent_at', today());
        }

        $invoices = $query->orderByDesc('packing_sent_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        return view('packing.index', compact('invoices'));
    }

    public function sendToPacking(Invoice $invoice)
    {
        if (!$invoice->packing_sent_at) {
            $sentAt = now();
            $invoice->update([
                'packing_sent_at' => $sentAt,
                // Never overwritten again — lets packing/index tell a brand-new
                // send apart from a resend that followed an order edit.
                'packing_first_sent_at' => $invoice->packing_first_sent_at ?? $sentAt,
            ]);
            $invoice->loadMissing('order.customer', 'order.invoice');
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'វិក្ក័យប័ត្របានបញ្ជូនដាក់រៀបចំ')
            ->with('packing_refresh_url', route('packing.index', ['period' => 'today']));
    }

    public function markPackingCompleted(Invoice $invoice)
    {
        if (!$invoice->packing_completed_at) {
            $invoice->update(['packing_completed_at' => now()]);
        }

        return back()->with('success', 'បានរៀបចំរួចរាល់');
    }

    /**
     * Toggle whether an invoice has been printed (បានព្រីន / មិនទាន់បានព្រីន).
     */
    public function togglePrinted(Invoice $invoice)
    {
        $invoice->update(['printed_at' => $invoice->printed_at ? null : now()]);

        return back();
    }


    public function store(Request $request)
    {
        return redirect()->route('orders.index')->with('info', 'វិក្ក័យប័ត្របានបង្កើតដោយស្វ័យប្រវត្តិ');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'items.product');

        $allSameDelivery = false;
        $backUrl = $this->resolveReturnUrl(route('invoices.index'));

        return view('invoices.show', compact('invoice', 'allSameDelivery', 'backUrl'));
    }

    /**
     * Resolve the "return" URL passed through the query string, falling back
     * to $default. Only same-origin URLs are honored to avoid open redirects.
     */
    private function resolveReturnUrl(string $default): string
    {
        $return = request('return');

        if (!$return) {
            return $default;
        }

        $decoded = urldecode($return);

        return str_starts_with($decoded, url('/')) ? $decoded : $default;
    }

    /**
     * Show the form for editing the invoice.
     */
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'due_date' => 'nullable|date|after:invoice_date',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        // Keep the order's payment status in sync — every other page (orders,
        // payments, dashboard, customer/user views) reads order.payment_status,
        // not invoice.status, so this edit was previously invisible everywhere else.
        // Only touch it when the status actually changed, and only downgrade an
        // existing "paid" order back to "unpaid" — never clobber a genuine
        // "partial" payment_status set from a real recorded payment.
        if ($invoice->order && $invoice->wasChanged('status')) {
            $order = $invoice->order;
            if ($validated['status'] === 'paid') {
                $order->update(['payment_status' => 'paid']);
            } elseif ($order->payment_status === 'paid') {
                $order->update(['payment_status' => 'unpaid']);
            }
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Delete the specified invoice (soft delete — recoverable from trash).
     * Its order is soft-deleted alongside it, so the order also disappears
     * from orders/payments listings until the invoice is restored.
     */
    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $invoice->update(['deleted_by' => auth()->id()]);
            $invoice->delete();

            if ($order = $invoice->order) {
                $order->update(['deleted_by' => auth()->id()]);
                $order->delete();
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Close the active invoice numbering period and start a new one, so the
     * next invoice restarts from INV-000001 (admin & manager only). Past
     * invoice numbers are untouched and stay unique because they belong to
     * the now-closed period.
     */
    public function closePeriod()
    {
        \App\Models\InvoicePeriod::closeCurrentAndStartNew(auth()->id());

        return redirect()->route('invoices.index')
            ->with('success', 'បិទបញ្ជីវិក្ក័យបត្រខែនេះបានជោគជ័យ។ វិក្ក័យបត្របន្ទាប់នឹងចាប់ផ្តើមពី INV-000001 ។');
    }

    /**
     * Undo an accidental close (admin & manager only). Only possible while
     * no invoice has been created yet under the new period — see
     * InvoicePeriod::canUndoLastClose().
     */
    public function undoClosePeriod()
    {
        if (!\App\Models\InvoicePeriod::undoLastClose()) {
            return redirect()->route('invoices.index')
                ->with('error', 'មិនអាចមិនធ្វើវិញបានទេ — មានវិក្ក័យបត្រត្រូវបានបង្កើតរួចហើយក្នុងខែថ្មី។');
        }

        return redirect()->route('invoices.index')
            ->with('success', 'បានលុបចោលការបិទបញ្ជីវិក្ក័យបត្រ។ លេខវិក្ក័យបត្របន្តដូចមុន។');
    }

    /**
     * Force-merge the active period back into the last closed one, even
     * after invoices already exist there (admin & manager only) — for when
     * the safe undoClosePeriod() window has passed. Every invoice currently
     * in the active period is renumbered to continue the previous period's
     * sequence, so the accidental extra period collapses away entirely.
     */
    public function mergeBackPeriod()
    {
        $merged = \App\Models\InvoicePeriod::mergeActiveIntoPrevious();

        if (empty($merged)) {
            return redirect()->route('invoices.index')
                ->with('error', 'មិនមានអ្វីត្រូវបញ្ចូលមកវិញទេ។');
        }

        return redirect()->route('invoices.index')
            ->with('success', 'បានបញ្ចូលវិក្ក័យបត្រ ' . count($merged) . ' ត្រឡប់ទៅខែមុន ហើយបន្តលេខរៀងតាមដដែល។');
    }

    /**
     * Display soft-deleted invoices (admin & manager only).
     */
    public function trashed(Request $request)
    {
        $query = Invoice::onlyTrashed()
            ->with(['order.customer', 'deletedBy']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('order.customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return view('invoices.trashed', compact('invoices'));
    }

    /**
     * Restore a soft-deleted invoice (admin & manager only). Its order is
     * restored alongside it, bringing it back into orders/payments listings.
     */
    public function restore($id)
    {
        $invoice = Invoice::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($invoice) {
            $invoice->restore();
            $invoice->update(['deleted_by' => null]);

            if ($order = Order::withTrashed()->find($invoice->order_id)) {
                $order->restore();
                $order->update(['deleted_by' => null]);
            }
        });

        return redirect()->route('invoices.trash')->with('success', 'Invoice restored successfully.');
    }

    /**
     * Permanently delete an invoice (admin only — cannot be undone). Its
     * trashed order is force-deleted too, since orders have no separate
     * trash UI — leaving it behind would strand it unrecoverably.
     */
    public function forceDelete($id)
    {
        $invoice = Invoice::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($invoice) {
            $order = Order::withTrashed()->find($invoice->order_id);
            $invoice->forceDelete();
            $order?->forceDelete();
        });

        return redirect()->route('invoices.trash')->with('success', 'Invoice permanently deleted.');
    }

    /**
     * Show the customer packing/invoice label.
     */
    public function print(Invoice $invoice)
    {
        $invoice->load('order.customer.salesperson', 'order.delivery', 'order.items.product', 'order.items.delivery');
        // Return to the filtered invoice list the user came from, if provided
        $backUrl = $this->resolveReturnUrl(route('invoices.index'));
        return view('packing.sticker-customer', compact('invoice', 'backUrl'));
    }

    /**
     * Print multiple invoices at once, one sticker per printed page.
     */
    public function printBulk(Request $request)
    {
        $ids = collect($request->query('ids', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        abort_if($ids->isEmpty(), 404);

        $invoices = Invoice::with('order.customer.salesperson', 'order.delivery', 'order.items.product', 'order.items.delivery')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn($invoice) => $ids->search($invoice->id))
            ->values();

        $backUrl = $this->resolveReturnUrl(route('invoices.index'));

        return view('packing.sticker-customer-bulk', compact('invoices', 'backUrl'));
    }

    /**
     * Show the packing preparation label for staff inventory.
     */
    public function stickerPrep(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product', 'order.delivery');
        // For prep view, return to packing index by default
        $backUrl = route('packing.index');
        return view('packing.sticker-prep', compact('invoice', 'backUrl'));
    }

    /**
     * Show the delivery sticker for pizza/all-products orders.
     */
    public function stickerReady(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product', 'order.items.delivery');
        $backUrl = route('packing.index');
        return view('packing.delivery_pizza', compact('invoice', 'backUrl'));
    }

    /**
     * Show the delivery sticker for mayonnaise-only orders.
     */
    public function stickerMayo(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product');
        $backUrl = route('packing.index');
        return view('packing.delivery_mayo', compact('invoice', 'backUrl'));
    }

    /**
     * Show the delivery sticker for Tamon deliveries.
     */
    public function stickerTamon(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product');
        $backUrl = route('packing.index');
        return view('packing.delivery_tamon', compact('invoice', 'backUrl'));
    }

    /**
     * Show the customer label.
     */
    public function stickerCustomer(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'order.items.delivery');
        $backUrl = auth()->user()?->isStaffInventory()
            ? route('packing.index')
            : route('invoices.show', $invoice);

        return view('packing.sticker-customer', compact('invoice', 'backUrl'));
    }

    /**
     * Show the customer label branded for the mayo shop.
     */
    public function stickerCustomerMayo(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'order.items.delivery');
        $backUrl = auth()->user()?->isStaffInventory()
            ? route('packing.index')
            : route('invoices.show', $invoice);

        return view('packing.sticker-customer_mayo', compact('invoice', 'backUrl'));
    }

    /**
     * Show the customer label branded for the Tamon shop.
     */
    public function stickerCustomerTamon(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'order.items.delivery');
        $backUrl = auth()->user()?->isStaffInventory()
            ? route('packing.index')
            : route('invoices.show', $invoice);

        return view('packing.sticker-customer_tamon', compact('invoice', 'backUrl'));
    }

    public function downloadSticker(Invoice $invoice)
    {
        try {
            $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'order.items.delivery');
            
            $pdf = Pdf::loadView('packing.sticker-customer', [
                'invoice' => $invoice,
                'backUrl' => null,
            ])->setPaper('a5', 'portrait');

            // invoice_number alone can repeat across closed periods, so
            // append the row id to keep downloaded filenames collision-free.
            $filename = $invoice->invoice_number . '-' . $invoice->id . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Download Error: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'error' => $e->getFile() . ':' . $e->getLine()
            ]);
            return response()->json(['error' => 'Failed to generate PDF'], 500);
        }
    }
}
