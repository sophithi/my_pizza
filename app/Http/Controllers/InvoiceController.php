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
    public function index()
    {
        $invoices = Invoice::with('order.customer')->paginate(15);
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
        $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax_amount,
            'discount_amount' => $order->discount_amount,
            'total_amount' => $order->total_amount,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product');
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
        $invoice->load('order.customer', 'order.items.product');
        return view('invoices.print', compact('invoice'));
    }
}
