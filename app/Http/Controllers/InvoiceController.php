<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'paid' => (clone $statsQuery)->where('status', 'paid')->count(),
            'unpaid' => (clone $statsQuery)->where('status', '!=', 'paid')->count(),
            'amount_usd' => (clone $statsQuery)->sum('total_amount'),
            'amount_khr' => (clone $statsQuery)->get()->sum(fn($invoice) => $this->invoiceTotalKhr($invoice)),
        ];

        $invoices = $this->orderInvoices($query)->paginate(15)->withQueryString();
        $invoices->getCollection()->each(function ($invoice) {
            $invoice->total_khr = $this->invoiceTotalKhr($invoice);
        });

        return view('invoices.index', compact('invoices', 'stats'));
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
        $query = Invoice::with(['order.customer', 'order.items.product']);
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
        return $query
            ->orderByDesc('invoice_date')
            ->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC");
    }

    private function invoiceTotalKhr(Invoice $invoice): float
    {
        $itemsTotalKhr = $invoice->order?->items?->sum(function ($item) {
            return (float) ($item->product?->price_khr ?? 0) * (float) $item->quantity;
        }) ?? 0;

        return $itemsTotalKhr - ((float) $invoice->discount_amount * 4000) + (float) $invoice->delivery_fee_khr;
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
        // If no period or date filter provided, default to today's invoices.
        if (!$period && !$request->filled('date')) {
            $period = 'today';
            $query->whereDate('invoice_date', today());
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
            $invoice->update(['packing_sent_at' => now()]);
            $invoice->loadMissing('order.customer', 'order.invoice');

            if ($invoice->order) {
                User::where('role', 'staff_inventory')->get()->each(function ($user) use ($invoice) {
                    $user->notify(new NewOrderNotification($invoice->order));
                });
            }
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'វិក្ក័យប័ត្របានបញ្ជូនដាក់រៀបចំ');
    }

    public function markPackingCompleted(Invoice $invoice)
    {
        if (!$invoice->packing_completed_at) {
            $invoice->update(['packing_completed_at' => now()]);
        }

        return back()->with('success', 'បានរៀបចំរួចរាល់');
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

    return view('invoices.show', compact('invoice', 'allSameDelivery'));
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
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Delete the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Show the customer packing/invoice label.
     */
    public function print(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'order.items.delivery');
        // When printing from the invoices area, return back to the invoice view
        $backUrl = route('invoices.show', $invoice);
        return view('packing.sticker-customer', compact('invoice', 'backUrl'));
    }

    /**
     * Show the packing preparation label for staff inventory.
     */
    public function stickerPrep(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product');
        // For prep view, return to packing index by default
        $backUrl = route('packing.index');
        return view('packing.sticker-prep', compact('invoice', 'backUrl'));
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
}
