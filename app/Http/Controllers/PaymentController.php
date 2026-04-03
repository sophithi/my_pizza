<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['order', 'invoice'])->latest()->paginate(15);
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::all();
        $orders = Order::all();
        return view('payments.create', compact('invoices', 'orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank_transfer,check,other',
            'reference' => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Payment amount must be greater than 0.',
        ]);

        // Ensure either order_id or invoice_id is provided
        if (!$request->order_id && !$request->invoice_id) {
            return back()->withErrors(['order_id' => 'Please select an order or invoice.']);
        }

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'invoice_id' => $request->invoice_id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'status' => 'confirmed',
        ]);

        // Update order payment status if payment is for an order
        if ($request->order_id) {
            $this->updateOrderPaymentStatus($request->order_id);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['order', 'invoice']);
        return view('payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $orderId = $payment->order_id;
        $payment->delete();

        if ($orderId) {
            $this->updateOrderPaymentStatus($orderId);
        }

        return back()->with('success', 'Payment deleted successfully.');
    }

    /**
     * Record payment for an order (AJAX endpoint)
     */
    public function recordOrderPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank_transfer,check,other',
            'reference' => 'nullable|string|max:255',
        ]);

        $amountDue = $order->total_amount - $order->payments->sum('amount');

        if ($request->amount > $amountDue) {
            return response()->json([
                'success' => false,
                'message' => "Payment cannot exceed amount due (៛{$amountDue})",
            ], 422);
        }

        $payment = $order->payments()->create([
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'status' => 'confirmed',
        ]);

        $this->updateOrderPaymentStatus($order->id);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully.',
            'payment' => $payment,
        ]);
    }

    /**
     * Update order payment status based on payments received
     */
    private function updateOrderPaymentStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        $totalPaid = $order->payments->sum('amount');
        $totalAmount = $order->total_amount;

        if ($totalPaid >= $totalAmount) {
            $order->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $order->update(['payment_status' => 'partial']);
        } else {
            $order->update(['payment_status' => 'unpaid']);
        }
    }
}