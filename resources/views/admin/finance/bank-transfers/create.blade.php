@extends('admin.layouts.app')

@section('title', 'Naya Bank Transfer')

@push('styles')
<style>
    :root {
        --ft-grad-1: #7C3AED; --ft-grad-2: #A855F7; --ft-grad-3: #6366F1;
        --ft-border: #E9E4FA; --ft-text-muted: #6B7280;
    }
    .ft-wrap { font-family: 'Inter','Outfit',sans-serif; max-width: 880px; margin: 0 auto; }

    .ft-breadcrumb { font-size: 13px; color: var(--ft-text-muted); margin-bottom: 14px; }
    .ft-breadcrumb a { color: #6D28D9; text-decoration: none; font-weight: 600; }

    .ft-hero {
        background: linear-gradient(135deg, var(--ft-grad-1) 0%, var(--ft-grad-3) 55%, var(--ft-grad-2) 100%);
        border-radius: 20px; padding: 26px 28px; color: #fff; margin-bottom: 22px;
        box-shadow: 0 12px 30px -10px rgba(124,58,237,.45);
        display: flex; align-items: center; gap: 14px;
    }
    .ft-hero .ft-icon-badge {
        width: 46px; height: 46px; border-radius: 14px; background: rgba(255,255,255,.18);
        display: flex; align-items: center; justify-content: center; font-size: 20px; backdrop-filter: blur(6px);
    }
    .ft-hero h1 { font-family:'Outfit',sans-serif; font-weight: 700; font-size: 22px; margin: 0; }
    .ft-hero p { margin: 2px 0 0; opacity: .9; font-size: 13.5px; }

    .ft-card { background: #fff; border: 1px solid var(--ft-border); border-radius: 18px; padding: 28px; box-shadow: 0 6px 24px -14px rgba(76,29,149,.18); }

    .ft-section-label {
        font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
        color: #7C3AED; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
    }
    .ft-section-label::after { content: ''; flex: 1; height: 1px; background: var(--ft-border); }

    .form-label { font-size: 13.5px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-control, .form-select, textarea.form-control {
        border-radius: 11px; border: 1.5px solid var(--ft-border); font-size: 14.5px; padding: 10px 14px;
    }
    .form-control:focus, .form-select:focus { border-color: var(--ft-grad-1); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }

    .ft-route-visual {
        display: flex; align-items: center; justify-content: center; gap: 16px; margin: 18px 0 24px;
        padding: 18px; background: #F8F7FF; border-radius: 14px; border: 1px dashed #D8CFFA;
    }
    .ft-route-box {
        flex: 1; text-align: center; background: #fff; border-radius: 12px; padding: 14px 10px;
        border: 1px solid var(--ft-border); min-width: 0;
    }
    .ft-route-box .tag { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .ft-route-box.from .tag { color: #B91C1C; }
    .ft-route-box.to .tag { color: #15803D; }
    .ft-route-box .name { font-family:'Outfit',sans-serif; font-weight: 700; font-size: 15px; color: #312E81; margin-top: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ft-route-box .acc-no { font-size: 11.5px; color: #9CA3AF; margin-top: 2px; font-family: 'ui-monospace','SFMono-Regular',monospace; }
    .ft-route-box .bal { font-size: 12px; color: var(--ft-text-muted); margin-top: 4px; font-weight: 600; }
    .ft-route-arrow-icon { font-size: 22px; color: #A855F7; flex-shrink: 0; }

    .ft-account-select option { padding: 6px; }

    .ft-amount-wrap { position: relative; }
    .ft-amount-wrap .rs { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #7C3AED; }
    .ft-amount-wrap input { padding-left: 32px !important; font-family:'Outfit',sans-serif; font-weight: 700; font-size: 17px !important; }

    .ft-actions-bar { display: flex; justify-content: flex-end; gap: 10px; margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--ft-border); }
    .btn-ft-primary {
        background: linear-gradient(135deg, var(--ft-grad-1), var(--ft-grad-3));
        color: #fff; border: none; padding: 11px 26px; border-radius: 12px; font-weight: 700;
        box-shadow: 0 8px 20px -8px rgba(124,58,237,.5); transition: transform .15s;
    }
    .btn-ft-primary:hover { transform: translateY(-1px); color: #fff; }
    .btn-ft-cancel {
        padding: 11px 22px; border-radius: 12px; font-weight: 600; border: 1.5px solid var(--ft-border);
        color: #6B7280; background: #fff;
    }
    .btn-ft-cancel:hover { background: #F9FAFB; color: #374151; }

    .ft-hint { font-size: 12px; color: var(--ft-text-muted); margin-top: 4px; }
    .invalid-feedback { display: block; }
</style>
@endpush

@section('content')
<div class="ft-wrap">
    <div class="ft-breadcrumb">
        <a href="{{ route('admin.finance.bank-transfers.index') }}">Bank Transfers</a> / Naya Transfer
    </div>

    <div class="ft-hero">
        <div class="ft-icon-badge"><i class="fas fa-right-left"></i></div>
        <div>
            <h1>Naya Bank Transfer</h1>
            <p>Fund ek account se doosre account me move karo — balance turant update hoga.</p>
        </div>
    </div>

    <div class="ft-card">
        <form method="POST" action="{{ route('admin.finance.bank-transfers.store') }}" id="transferForm">
            @csrf

            <div class="ft-section-label"><i class="fas fa-building-columns"></i> Route Select Karo</div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">From Account (Debit hoga)</label>
                    <select name="from_bank_account_id" id="fromAccount" class="form-select @error('from_bank_account_id') is-invalid @enderror" required>
                        <option value="">-- Select karo --</option>
                        @foreach($bankAccounts as $acc)
                            <option value="{{ $acc->id }}" data-name="{{ $acc->name }}" data-balance="{{ $acc->current_balance }}"
                                data-account-number="{{ $acc->account_number ?: '—' }}"
                                @selected(old('from_bank_account_id') == $acc->id)>
                                {{ $acc->name }} ({{ $acc->bank_name ?: ucfirst($acc->type) }}) @if($acc->account_number) &middot; A/C {{ $acc->account_number }} @endif — ₹{{ number_format($acc->current_balance,2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('from_bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">To Account (Credit hoga)</label>
                    <select name="to_bank_account_id" id="toAccount" class="form-select @error('to_bank_account_id') is-invalid @enderror" required>
                        <option value="">-- Select karo --</option>
                        @foreach($bankAccounts as $acc)
                            <option value="{{ $acc->id }}" data-name="{{ $acc->name }}" data-balance="{{ $acc->current_balance }}"
                                data-account-number="{{ $acc->account_number ?: '—' }}"
                                @selected(old('to_bank_account_id') == $acc->id)>
                                {{ $acc->name }} ({{ $acc->bank_name ?: ucfirst($acc->type) }}) @if($acc->account_number) &middot; A/C {{ $acc->account_number }} @endif — ₹{{ number_format($acc->current_balance,2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('to_bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Live route visual --}}
            <div class="ft-route-visual" id="routeVisual" style="display:none;">
                <div class="ft-route-box from">
                    <div class="tag">From</div>
                    <div class="name" id="fromName">—</div>
                    <div class="acc-no" id="fromAccNo">A/C: —</div>
                    <div class="bal" id="fromBal">Balance: ₹0.00</div>
                </div>
                <i class="fas fa-arrow-right-long ft-route-arrow-icon"></i>
                <div class="ft-route-box to">
                    <div class="tag">To</div>
                    <div class="name" id="toName">—</div>
                    <div class="acc-no" id="toAccNo">A/C: —</div>
                    <div class="bal" id="toBal">Balance: ₹0.00</div>
                </div>
            </div>

            <div class="ft-section-label mt-4"><i class="fas fa-coins"></i> Transfer Detail</div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Amount</label>
                    <div class="ft-amount-wrap">
                        <span class="rs">₹</span>
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}"
                               class="form-control @error('amount') is-invalid @enderror" required>
                    </div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="ft-hint" id="balanceHint"></div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Transfer Date</label>
                    <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->toDateString()) }}"
                           class="form-control @error('transfer_date') is-invalid @enderror" required>
                    @error('transfer_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Method</label>
                    <select name="method" class="form-select @error('method') is-invalid @enderror" required>
                        <option value="">-- Select --</option>
                        @foreach(['NEFT','RTGS','IMPS','UPI','Cheque','Cash Deposit','Internal Adjustment'] as $m)
                            <option value="{{ $m }}" @selected(old('method') === $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Reference No <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="reference_no" value="{{ old('reference_no') }}"
                           class="form-control @error('reference_no') is-invalid @enderror" placeholder="UTR / Cheque No.">
                    @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Transfer ka reason ya koi extra detail...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ft-actions-bar">
                <a href="{{ route('admin.finance.bank-transfers.index') }}" class="btn-ft-cancel">Cancel</a>
                <button type="submit" class="btn-ft-primary">
                    <i class="fas fa-paper-plane me-2"></i> Transfer Confirm Karo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fromSel = document.getElementById('fromAccount');
        const toSel = document.getElementById('toAccount');
        const visual = document.getElementById('routeVisual');
        const amountInput = document.querySelector('input[name="amount"]');
        const hint = document.getElementById('balanceHint');

        function fmt(n) { return '₹' + parseFloat(n || 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}); }

        function refreshVisual() {
            const fromOpt = fromSel.options[fromSel.selectedIndex];
            const toOpt = toSel.options[toSel.selectedIndex];

            if (fromSel.value || toSel.value) visual.style.display = 'flex';

            if (fromSel.value) {
                document.getElementById('fromName').textContent = fromOpt.dataset.name;
                document.getElementById('fromAccNo').textContent = 'A/C: ' + (fromOpt.dataset.accountNumber || '—');
                document.getElementById('fromBal').textContent = 'Balance: ' + fmt(fromOpt.dataset.balance);
            }
            if (toSel.value) {
                document.getElementById('toName').textContent = toOpt.dataset.name;
                document.getElementById('toAccNo').textContent = 'A/C: ' + (toOpt.dataset.accountNumber || '—');
                document.getElementById('toBal').textContent = 'Balance: ' + fmt(toOpt.dataset.balance);
            }
            checkBalance();
        }

        function checkBalance() {
            const fromOpt = fromSel.options[fromSel.selectedIndex];
            if (!fromSel.value || !amountInput.value) { hint.textContent = ''; return; }
            const balance = parseFloat(fromOpt.dataset.balance || 0);
            const amount = parseFloat(amountInput.value || 0);
            if (amount > balance) {
                hint.innerHTML = '<span style="color:#DC2626; font-weight:600;"><i class="fas fa-triangle-exclamation"></i> Amount available balance (' + fmt(balance) + ') se zyada hai</span>';
            } else {
                hint.innerHTML = '<span style="color:#15803D;">Available balance: ' + fmt(balance) + '</span>';
            }
        }

        fromSel.addEventListener('change', function () {
            if (toSel.value === this.value) toSel.value = '';
            refreshVisual();
        });
        toSel.addEventListener('change', refreshVisual);
        amountInput.addEventListener('input', checkBalance);

        // Same account select nahi hone dena (from != to)
        document.getElementById('transferForm').addEventListener('submit', function (e) {
            if (fromSel.value && fromSel.value === toSel.value) {
                e.preventDefault();
                alert('From aur To account same nahi ho sakta.');
            }
        });

        refreshVisual();
    });
</script>
@endpush
