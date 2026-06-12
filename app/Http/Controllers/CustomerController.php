<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Traits\ExportableSpreadsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class CustomerController extends Controller
{
    use ExportableSpreadsheet;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->withMax('orders as last_order_at', 'order_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $phoneSearch = preg_replace('/\D+/', '', $search);

            $query->where(function ($q) use ($search, $phoneSearch) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");

                if ($phoneSearch !== '') {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$phoneSearch}%"]);
                }
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'with_orders' => Customer::has('orders')->count(),
        ];

        $customers = $query->latest()->paginate(15)->withQueryString();
        return view('customers.index', compact('customers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders' => fn($q) => $q->with(['items', 'invoice'])->latest('order_date')]);
        $summary = [
            'orders' => $customer->orders->count(),
            'spent' => $customer->orders->sum('total_amount'),
            'paid' => $customer->orders->where('payment_status', 'paid')->count(),
            'unpaid' => $customer->orders->where('payment_status', 'unpaid')->count(),
        ];
        return view('customers.show', compact('customer', 'summary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        $returnUrl = $request->input('return_url');
        if ($returnUrl && (Str::startsWith($returnUrl, '/') || parse_url($returnUrl, PHP_URL_HOST) === $request->getHost())) {
            return redirect($returnUrl)->with('success', 'Customer updated successfully.');
        }

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function exportExcel(Request $request)
    {
        $query = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->withMax('orders as last_order_at', 'order_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $customers = $query->latest()->get();
        $spreadsheet = $this->createBrandedSpreadsheet('Customers', 'របាយការណ៍អតិថិជន', 9);
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ល.រ', 'ឈ្មោះ', 'ប្រភព', 'ទូរស័ព្ទ', 'ទីតាំង', 'ការបញ្ជាទិញ', 'ចំណាយសរុប', 'បង្កើតថ្ងៃ', 'ស្ថានភាព'];
        $headerRow = 6;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . $headerRow, $header);
        }

        $row = 7;
        $number = 1;
        foreach ($customers as $customer) {
            $sheet->setCellValue("A{$row}", $number);
            $sheet->setCellValue("B{$row}", $customer->name);
            $sheet->setCellValue("C{$row}", ucfirst($customer->type ?? '—'));
            $sheet->setCellValueExplicit("D{$row}", (string) ($customer->phone ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("E{$row}", $customer->city ?? $customer->address ?? '—');
            $sheet->setCellValue("F{$row}", $customer->orders_count);
            $sheet->setCellValue("G{$row}", $customer->total_spent ?? 0);
            $sheet->setCellValue("H{$row}", $customer->created_at?->format('d/m/Y') ?? '—');
            $sheet->setCellValue("I{$row}", ucfirst($customer->status ?? '—'));
            $row++;
            $number++;
        }

        $lastRow = max(7, $row - 1);
        $tableRange = "A{$headerRow}:I{$lastRow}";

        $this->styleTableHeaders($sheet, "A{$headerRow}:I{$headerRow}", $tableRange);
        $this->applyStripeRows($sheet, 7, $lastRow);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(13);
        $sheet->getColumnDimension('I')->setWidth(12);

        // Format currency columns
        $sheet->getStyle("G7:G{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');

        return $this->downloadSpreadsheet($spreadsheet, 'customers_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->withMax('orders as last_order_at', 'order_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $customers = $query->latest()->get();

        return view('customers.pdf', compact('customers'));
    }
}
