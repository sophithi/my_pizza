<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Show settings page.
     */
    public function index()
    {
        $settings = Setting::get();
        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'nullable|email',
            'business_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string',
            'business_city' => 'nullable|string|max:100',
            'business_postal_code' => 'nullable|string|max:20',
            'business_description' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_name' => 'required|string|max:50',
            'invoice_prefix' => 'required|string|max:10',
            'enable_notifications' => 'boolean',
            'enable_email_invoices' => 'boolean',
            'mail_driver' => 'required|string|in:smtp,mailgun,sendgrid',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|string|in:tls,ssl',
            'timezone' => 'required|string|timezone',
            'date_format' => 'required|string|max:20',
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        $settings = Setting::get();
        $settings->update($validated);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    /**
     * Update only the exchange rate (AJAX)
     */
    public function updateExchangeRate(Request $request)
    {
        $validated = $request->validate([
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        $settings = Setting::get();
        $settings->update(['exchange_rate' => $validated['exchange_rate']]);

        return response()->json(['success' => true, 'exchange_rate' => (float) $settings->exchange_rate]);
    }
}
