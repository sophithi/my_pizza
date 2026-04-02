<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'business_name',
        'business_email',
        'business_phone',
        'business_address',
        'business_city',
        'business_postal_code',
        'business_description',
        'currency',
        'tax_rate',
        'tax_name',
        'invoice_prefix',
        'enable_notifications',
        'enable_email_invoices',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'timezone',
        'date_format',
        'exchange_rate',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'enable_notifications' => 'boolean',
        'enable_email_invoices' => 'boolean',
    ];

    /**
     * Get the first (or only) settings record.
     */
    public static function get()
    {
        return self::first() ?? self::create([
            'business_name' => 'Pizza Happy Family',
            'currency' => 'PHP',
            'tax_rate' => 10,
            'exchange_rate' => 4000,
        ]);
    }
}
