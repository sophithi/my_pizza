<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\ExportableSpreadsheet;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    use ExportableSpreadsheet;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('inventory')->paginate(15);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exchangeRate = 4000;
        return view('products.create', compact('exchangeRate'));
    }

    public function image(string $filename): BinaryFileResponse
    {
        $relativePath = str_replace('\\', '/', ltrim($filename, '/'));

        abort_if(str_contains($relativePath, '..'), 404);

        if (!str_starts_with($relativePath, 'products/')) {
            $relativePath = 'products/' . $relativePath;
        }

        $candidates = [
            storage_path('app/public/' . $relativePath),
            public_path('storage/' . $relativePath),
            base_path('storage/' . $relativePath),
            base_path('public/storage/' . $relativePath),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return response()->file($path);
            }
        }

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product = Product::create($data);
        return redirect()->route('products.show', $product)->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('inventory', 'orderItems');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $exchangeRate = 4000;
        return view('products.edit', compact('product', 'exchangeRate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);
        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if it exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function exportExcel()
    {
        $products = Product::with('inventory')->latest()->get();
        $spreadsheet = $this->createBrandedSpreadsheet('Products', 'របាយការណ៍ទំនិញ', 8);
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ល.រ', 'កូដ', 'ឈ្មោះ', 'ប្រភេទ', 'ខ្នាត', 'តម្លៃ USD', 'តម្លៃ KHR', 'ស្តុក'];
        $headerRow = 6;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . $headerRow, $header);
        }

        $row = 7;
        $number = 1;
        foreach ($products as $product) {
            $sheet->setCellValue("A{$row}", $number);
            $sheet->setCellValueExplicit("B{$row}", (string) $product->sku, DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $product->name);
            $sheet->setCellValue("D{$row}", $product->category ?? '—');
            $sheet->setCellValue("E{$row}", $product->unit ?? '—');
            $sheet->setCellValue("F{$row}", $product->price_usd ?? 0);
            $sheet->setCellValue("G{$row}", $product->price_khr ?? 0);
            $sheet->setCellValue("H{$row}", $product->inventory?->quantity ?? 0);
            $row++;
            $number++;
        }

        $lastRow = max(7, $row - 1);
        $tableRange = "A{$headerRow}:H{$lastRow}";

        $this->styleTableHeaders($sheet, "A{$headerRow}:H{$headerRow}", $tableRange);
        $this->applyStripeRows($sheet, 7, $lastRow);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(10);

        // Format currency columns
        $sheet->getStyle("F7:F{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("G7:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        return $this->downloadSpreadsheet($spreadsheet, 'products_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf()
    {
        $products = Product::with('inventory')->latest()->get();
        return view('products.pdf', compact('products'));
    }
}
