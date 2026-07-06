@extends('admin.layout')

@section('title', 'Edit Bank Transfer')

@push('styles')
<style>
    :root {
        --ft-grad-1: #7C3AED; --ft-grad-2: #A855F7; --ft-grad-3: #6366F1;
        --ft-border: #E9E4FA; --ft-text-muted: #6B7280;
    }
    .ft-wrap { font-family: 'Inter','Outfit',sans-serif;  margin: 0 auto; }
    .ft-breadcrumb { font-size: 13px; color: var(--ft-text-muted); margin-bottom: 14px; }
    .ft-breadcrumb a { color: #6D28D9; text-decoration: none; font-weight: 600; }

    .ft-hero {
        background: linear-gradient(135deg, #F59E0B 0%, #F97316 60%, #EA580C 100%);
        border-radius: 20px; padding: 26px 28px; color: #fff; margin-bottom: 22px;
        box-shadow: 0 12px 30px -10px rgba(234,88,12,.4);
        display: flex; align-items: center; gap: 14px;
    }
    .ft-hero .ft-icon-badge {
        width: 46px; height: 46px; border-radius: 14px; background: rgba(255,255,255,.18);
        display: flex; align-items: center; justify-content: center; font-size: 20px; backdrop-filter: blur(6px);
    }
    .ft-hero h1 { font-family:'Outfit',sans-serif; font-weight: 700; font-size: 22px; margin: 0; }
    .ft-hero p { margin: 2px 0 0; opacity: .92; font-size: 13.5px; }

    .ft-card { background: #fff; border: 1px solid var(--ft-border); border-radius: 18px; padding: 28px; box-shadow: 0 6px 24px -14px rgba(76,29,149,.18); }

    .ft-locked-panel {
        background: #F9FAFB; border: 1px dashed #E5E7EB; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;
    }
    .ft-locked-panel .heading { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9CA3AF; margin-bottom: 12px; display:flex; align-items:center; gap:6px; }
    .ft-locked-route { display: flex; align-items: center; gap: 14px; }
    .ft-locked-box { flex: 1; text-align: center; }
    .ft-locked-box .tag { font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .ft-locked-box.from .tag { color: #B91C1C; }
    .ft-locked-box.to .tag { color: #15803D; }
    .ft-locked-box .name { font-family:'Outfit',sans-serif; font-weight: 700; font-size: 15px; color: #374151; margin-top: 2px; }
    .ft-locked-amount { text-align: center; }
    .ft-locked-amount .amt { font-family:'Outfit',sans-serif; font-weight: 800; font-size: 22px; color: #4C1D95; }
    .ft-locked-amount .lbl { font-size: 11px; color: #9CA3AF; text-transform: uppercase; font-weight: 600; }

    .form-label { font-size: 13.5px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-control, .form-select, textarea.form-control {
        border-radius: 11px; border: 1.5px solid var(--ft-border); font-size: 14.5px; padding: 10px 14px;
    }
    .form-control:focus, .form-select:focus { border-color: var(--ft-grad-1); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }

    .ft-section-label {
        font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
        color: #7C3AED; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
    }
    .ft-section-label::after { content: ''; flex: 1; height: 1px; background: var(--ft-border); }

    .ft-actions-bar { display: flex; justify-content: flex-end; gap: 10px; margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--ft-border); }
    .btn-ft-primary {
        background: linear-gradient(135deg, #F59E0B, #EA580C);
        color: #fff; border: none; padding: 11px 26px; border-radius: 12px; font-weight: 700;
        box-shadow: 0 8px 20px -8px rgba(234,88,12,.5);
    }
    .btn-ft-primary:hover { color: #fff; transform: translateY(-1px); }
    .btn-ft-cancel {
        padding: 11px 22px; border-radius: 12px; font-weight: 600; border: 1.5px solid var(--ft-border);
        color: #6B7280; background: #fff;
    }
    .btn-ft-cancel:hover { background: #F9FAFB; color: #374151; }
    .invalid-feedback { display: block; }
</style>
@endpush

@section('content')
<div class="ft-wrap">
    <div class="ft-breadcrumb">
        <a href="{{ route('admin.finance.bank-transfers.index') }}">Bank Transfers</a> /
        <a href="{{ route('admin.finance.bank-transfers.show', $bankTransfer) }}">#{{ $bankTransfer->id }}</a> / Edit
    </div>

    <div class="ft-hero">
        <div class="ft-icon-badge"><i class="fas fa-pen"></i></div>
        <div>
            <h1>Transfer Edit Karo</h1>
            <p>Safety ke liye amount aur accounts lock hai — sirf date, method, reference aur notes edit ho sakte hain.</p>
        </div>
    </div>

    <div class="ft-card">

        {{-- Locked financial summary --}}
        <div class="ft-locked-panel">
            <div class="heading"><i class="fas fa-lock"></i> Financial Detail (Locked)</div>
            <div class="ft-locked-route">
                <div class="ft-locked-box from">
                    <div class="tag">From</div>
                    <div class="name">{{ $bankTransfer->fromBankAccount?->name }}</div>
                </div>
                <i class="fas fa-arrow-right" style="color:#D1D5DB;"></i>
                <div class="ft-locked-amount">
                    <div class="amt">₹{{ number_format($bankTransfer->amount, 2) }}</div>
                    <div class="lbl">Amount</div>
                </div>
                <i class="fas fa-arrow-right" style="color:#D1D5DB;"></i>
                <div class="ft-locked-box to">
                    <div class="tag">To</div>
                    <div class="name">{{ $bankTransfer->toBankAccount?->name }}</div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning py-2 px-3 mb-4" style="border-radius: 10px; font-size: 13px;">
            <i class="fas fa-circle-info me-1"></i>
            Agar amount ya account galat ho gaya hai, to ye transfer <strong>delete</strong> karke naya transfer banao —
            isse balance hamesha sahi rahega.
        </div>

        <form method="POST" action="{{ route('admin.finance.bank-transfers.update', $bankTransfer) }}">
            @csrf @method('PUT')

            <div class="ft-section-label"><i class="fas fa-pen-to-square"></i> Editable Detail</div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Transfer Date</label>
                    <input type="date" name="transfer_date"
                           value="{{ old('transfer_date', $bankTransfer->transfer_date?->format('Y-m-d')) }}"
                           class="form-control @error('transfer_date') is-invalid @enderror" required>
                    @error('transfer_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Method</label>
                    <select name="method" class="form-select @error('method') is-invalid @enderror" required>
                        @foreach(['NEFT','RTGS','IMPS','UPI','Cheque','Cash Deposit','Internal Adjustment'] as $m)
                            <option value="{{ $m }}" @selected(old('method', $bankTransfer->method) === $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Reference No</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no', $bankTransfer->reference_no) }}"
                           class="form-control @error('reference_no') is-invalid @enderror" placeholder="UTR / Cheque No.">
                    @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $bankTransfer->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ft-actions-bar">
                <a href="{{ route('admin.finance.bank-transfers.show', $bankTransfer) }}" class="btn-ft-cancel">Cancel</a>
                <button type="submit" class="btn-ft-primary">
                    <i class="fas fa-check me-2"></i> Update Karo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
