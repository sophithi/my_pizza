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
     * Redirect to create order instead (invoices are auto-created).
     */
    public function create()
    {
        return redirect()->route('orders.create')->with('info', 'វិក្ក័យប័ត្របានបង្កើតដោយស្វយប្រវត្តិ នៅពេលដែលលទ្ធផលបញ្ចប់។');
    }

    /**
     * Display invoices for packing labels.
     */
    public function printIndex(Request $request)
    {
        $query = Invoice::with(['order', 'order.customer']);
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

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();
        return view('packing.index', compact('invoices'));
    }

    /**
     * Store a newly created invoice.
     */
    /**
     * Store a newly created invoice (kept for API/flexibility but redirects).
     * Invoices are now auto-created when orders are marked ready.
     */
    public function store(Request $request)
    {
        return redirect()->route('orders.index')->with('info', 'វិក្ក័យប័ត្របានបង្កើតដោយស្វយប្រវត្តិរួចហើយ។');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
         $invoice->load('order.delivery', 'items.product');

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
        return view('packing.sticker-customer', compact('invoice'));
    }

    /**
     * Show the packing preparation label for staff inventory.
     */
    public function stickerPrep(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.items.product');
        return view('packing.sticker-prep', compact('invoice'));
    }

    /**
     * Show the customer label.
     */
    public function stickerCustomer(Invoice $invoice)
    {
        $invoice->load('order.customer', 'order.delivery', 'order.items.product', 'order.items.delivery');
        return view('packing.sticker-customer', compact('invoice'));
    }
}
