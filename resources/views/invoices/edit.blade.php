@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Edit Invoice -
                    {{ $invoice->invoice_number }}</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 24px;">
                        <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="status"
                                    style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Status</label>
                                <select name="status" id="status" class="form-control"
                                    style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                                    <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>
                                        មិនទាន់បង់ប្រាក់</option>

                                    <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>បានបង់ប្រាក់
                                    </option>
                                    <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancel
                                    </option>
                                </select>
                            </div>


                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="notes"
                                    style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="4"
                                    style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">{{ $invoice->notes }}</textarea>
                            </div>

                            <div style="display: flex; gap: 8px;">
                                <button type="submit" class="btn"
                                    style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn"
                                    style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                                    Cancel
                                </a>
                                <button type="button"
                                    onclick="if(confirm('លុបវិក្កយបត្រនេះ?')) document.getElementById('delete-invoice-form').submit();"
                                    class="btn"
                                    style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </form>

                        <form id="delete-invoice-form" action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection