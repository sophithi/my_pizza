@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">System Settings</h2>
            <p style="color: #666; margin-top: 8px;">Configure your Pizza POS system</p>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger" style="border-radius: 8px; padding: 16px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
        <strong>Please fix the following errors:</strong>
        <ul style="margin: 8px 0 0 20px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success" style="border-radius: 8px; padding: 16px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Business Settings -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                    <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                        <h5 style="color: #333; font-weight: 600; margin: 0;">
                            <i class="fas fa-building" style="color: #e85d24; margin-right: 8px;"></i>Business Information
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 24px;">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Business Name *</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                value="{{ old('business_name', $settings->business_name) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('business_name') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Email</label>
                            <input type="email" name="business_email" class="form-control @error('business_email') is-invalid @enderror" 
                                value="{{ old('business_email', $settings->business_email) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('business_email') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Phone</label>
                            <input type="text" name="business_phone" class="form-control @error('business_phone') is-invalid @enderror" 
                                value="{{ old('business_phone', $settings->business_phone) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('business_phone') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Address</label>
                            <textarea name="business_address" class="form-control @error('business_address') is-invalid @enderror" 
                                rows="3" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">{{ old('business_address', $settings->business_address) }}</textarea>
                            @error('business_address') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">City</label>
                            <input type="text" name="business_city" class="form-control @error('business_city') is-invalid @enderror" 
                                value="{{ old('business_city', $settings->business_city) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('business_city') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Postal Code</label>
                            <input type="text" name="business_postal_code" class="form-control @error('business_postal_code') is-invalid @enderror" 
                                value="{{ old('business_postal_code', $settings->business_postal_code) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('business_postal_code') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Description</label>
                            <textarea name="business_description" class="form-control @error('business_description') is-invalid @enderror" 
                                rows="3" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">{{ old('business_description', $settings->business_description) }}</textarea>
                            @error('business_description') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Settings -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                    <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                        <h5 style="color: #333; font-weight: 600; margin: 0;">
                            <i class="fas fa-coins" style="color: #28a745; margin-right: 8px;"></i>Financial Settings
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 24px;">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Currency *</label>
                            <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" 
                                value="{{ old('currency', $settings->currency) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('currency') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Tax Rate (%) *</label>
                            <input type="number" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" 
                                value="{{ old('tax_rate', $settings->tax_rate) }}" step="0.01" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('tax_rate') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Tax Name *</label>
                            <input type="text" name="tax_name" class="form-control @error('tax_name') is-invalid @enderror" 
                                value="{{ old('tax_name', $settings->tax_name) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('tax_name') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Exchange Rate (KHR per 1 USD) *</label>
                            <input type="number" name="exchange_rate" class="form-control @error('exchange_rate') is-invalid @enderror" 
                                value="{{ old('exchange_rate', $settings->exchange_rate ?? 4000) }}" step="0.0001" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('exchange_rate') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Invoice Prefix *</label>
                            <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" 
                                value="{{ old('invoice_prefix', $settings->invoice_prefix) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('invoice_prefix') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Timezone *</label>
                            <select name="timezone" class="form-control @error('timezone') is-invalid @enderror" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                                <option value="Asia/Manila" {{ old('timezone', $settings->timezone) === 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila (PHT)</option>
                                <option value="Asia/Bangkok" {{ old('timezone', $settings->timezone) === 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok (ICT)</option>
                                <option value="America/New_York" {{ old('timezone', $settings->timezone) === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                <option value="Europe/London" {{ old('timezone', $settings->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                <option value="UTC" {{ old('timezone', $settings->timezone) === 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                            @error('timezone') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Date Format *</label>
                            <select name="date_format" class="form-control @error('date_format') is-invalid @enderror" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                                <option value="Y-m-d" {{ old('date_format', $settings->date_format) === 'Y-m-d' ? 'selected' : '' }}>2026-03-26</option>
                                <option value="m/d/Y" {{ old('date_format', $settings->date_format) === 'm/d/Y' ? 'selected' : '' }}>03/26/2026</option>
                                <option value="d/m/Y" {{ old('date_format', $settings->date_format) === 'd/m/Y' ? 'selected' : '' }}>26/03/2026</option>
                                <option value="M d, Y" {{ old('date_format', $settings->date_format) === 'M d, Y' ? 'selected' : '' }}>Mar 26, 2026</option>
                            </select>
                            @error('date_format') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
            <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                <h5 style="color: #333; font-weight: 600; margin: 0;">
                    <i class="fas fa-envelope" style="color: #17a2b8; margin-right: 8px;"></i>Email Configuration
                </h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Mail Driver *</label>
                            <select name="mail_driver" class="form-control @error('mail_driver') is-invalid @enderror" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                                <option value="smtp" {{ old('mail_driver', $settings->mail_driver) === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="mailgun" {{ old('mail_driver', $settings->mail_driver) === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="sendgrid" {{ old('mail_driver', $settings->mail_driver) === 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                            </select>
                            @error('mail_driver') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Encryption *</label>
                            <select name="mail_encryption" class="form-control @error('mail_encryption') is-invalid @enderror" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                                <option value="tls" {{ old('mail_encryption', $settings->mail_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('mail_encryption', $settings->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                            @error('mail_encryption') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Host</label>
                            <input type="text" name="mail_host" class="form-control @error('mail_host') is-invalid @enderror" 
                                value="{{ old('mail_host', $settings->mail_host) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('mail_host') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Port</label>
                            <input type="number" name="mail_port" class="form-control @error('mail_port') is-invalid @enderror" 
                                value="{{ old('mail_port', $settings->mail_port) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('mail_port') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Username</label>
                            <input type="text" name="mail_username" class="form-control @error('mail_username') is-invalid @enderror" 
                                value="{{ old('mail_username', $settings->mail_username) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('mail_username') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Password</label>
                            <input type="password" name="mail_password" class="form-control @error('mail_password') is-invalid @enderror" 
                                value="{{ old('mail_password', $settings->mail_password) }}" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px;">
                            @error('mail_password') <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check" style="padding: 12px; background: #f8f9fa; border-radius: 8px;">
                            <input class="form-check-input" type="checkbox" name="enable_notifications" value="1" 
                                {{ old('enable_notifications', $settings->enable_notifications) ? 'checked' : '' }} id="enable_notifications">
                            <label class="form-check-label" for="enable_notifications" style="color: #333; font-weight: 500; margin: 0;">
                                Enable Notifications
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check" style="padding: 12px; background: #f8f9fa; border-radius: 8px;">
                            <input class="form-check-input" type="checkbox" name="enable_email_invoices" value="1" 
                                {{ old('enable_email_invoices', $settings->enable_email_invoices) ? 'checked' : '' }} id="enable_email_invoices">
                            <label class="form-check-label" for="enable_email_invoices" style="color: #333; font-weight: 500; margin: 0;">
                                Send Email Invoices
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div style="margin-bottom: 30px;">
            <button type="submit" class="btn" style="background: #e85d24; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px;">
                <i class="fas fa-save"></i> Save Settings
            </button>
            <a href="{{ route('dashboard') }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; margin-left: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
