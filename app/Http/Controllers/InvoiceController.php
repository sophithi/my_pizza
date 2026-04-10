<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('order.customer');
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

        $invoices = $query->latest('invoice_date')->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form to create a new invoice from an order.
     */
    public function create()
    {
        $orders = Order::whereDoesntHave('invoice')
            ->with('customer')
            ->paginate(10);
        return view('invoices.create', compact('orders'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id|unique:invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after:invoice_date',
            'notes' => 'nullable|string',
        ]);

        $order = Order::find($validated['order_id']);
        $lastInvoice = Invoice::orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")->first();
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 4) + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'] ?? null,
            'subtotal' => $order->subtotal,
            'discount_amount' => $order->discount_amount,
            'total_amount' => $order->total_amount,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product', 'order.items.delivery');
        return view('invoices.show', compact('invoice'));
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
     * Print/export the invoice as PDF view.
     */
    public function print(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product', 'order.items.delivery');
        return view('invoices.print', compact('invoice'));
    }

    /**
     * Print preparation sticker for staff inventory.
     */
    public function stickerPrep(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product');
        return view('invoices.sticker-prep', compact('invoice'));
    }

    /**
     * Print customer sticker.
     */
    public function stickerCustomer(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product', 'order.items.delivery');
        return view('invoices.sticker-customer', compact('invoice'));
    }
}
