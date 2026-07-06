@extends('admin.layouts.app')

@section('title', 'Transfer #'.$bankTransfer->id)

@push('styles')
<style>
    :root {
        --ft-grad-1: #7C3AED; --ft-grad-2: #A855F7; --ft-grad-3: #6366F1;
        --ft-border: #E9E4FA; --ft-text-muted: #6B7280;
    }
    .ft-wrap { font-family: 'Inter','Outfit',sans-serif; max-width: 960px; margin: 0 auto; }
    .ft-breadcrumb { font-size: 13px; color: var(--ft-text-muted); margin-bottom: 14px; }
    .ft-breadcrumb a { color: #6D28D9; text-decoration: none; font-weight: 600; }

    .ft-hero {
        background: linear-gradient(135deg, var(--ft-grad-1) 0%, var(--ft-grad-3) 55%, var(--ft-grad-2) 100%);
        border-radius: 20px; padding: 30px 30px; color: #fff; margin-bottom: 22px; position: relative; overflow: hidden;
        box-shadow: 0 12px 30px -10px rgba(124,58,237,.45);
    }
    .ft-hero::before { content:''; position:absolute; inset:0; background: radial-gradient(circle at 85% 15%, rgba(255,255,255,.18), transparent 55%); }
    .ft-hero-top { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; }
    .ft-hero-id { font-family:'Outfit',sans-serif; font-weight: 800; font-size: 24px; }
    .ft-hero-date { font-size: 13px; opacity: .9; margin-top: 2px; }
    .ft-hero-actions { display: flex; gap: 8px; }
    .ft-btn-glass {
        background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.35); color: #fff;
        padding: 9px 18px; border-radius: 11px; font-weight: 600; font-size: 13.5px; transition: all .2s; text-decoration: none;
    }
    .ft-btn-glass:hover { background: rgba(255,255,255,.28); color: #fff; }
    .ft-btn-glass.danger { border-color: rgba(255,255,255,.5); }

    .ft-hero-route {
        position: relative; z-index: 1; display: flex; align-items: center; gap: 20px; margin-top: 24px;
    }
    .ft-hero-box { flex: 1; background: rgba(255,255,255,.14); border-radius: 14px; padding: 16px 18px; backdrop-filter: blur(6px); }
    .ft-hero-box .tag { font-size: 11px; text-transform: uppercase; font-weight: 700; opacity: .8; letter-spacing: .04em; }
    .ft-hero-box .name { font-family:'Outfit',sans-serif; font-weight: 700; font-size: 18px; margin-top: 4px; }
    .ft-hero-box .sub { font-size: 12.5px; opacity: .85; margin-top: 2px; }
    .ft-hero-amount { text-align: center; flex-shrink: 0; }
    .ft-hero-amount .amt { font-family:'Outfit',sans-serif; font-weight: 800; font-size: 26px; }
    .ft-hero-amount i { font-size: 20px; opacity: .8; }

    .ft-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 20px; margin-bottom: 20px; }
    @media (max-width: 820px) { .ft-grid { grid-template-columns: 1fr; } }

    .ft-card { background: #fff; border: 1px solid var(--ft-border); border-radius: 18px; padding: 22px 24px; box-shadow: 0 6px 24px -14px rgba(76,29,149,.14); }
    .ft-card h3 { font-family:'Outfit',sans-serif; font-size: 15px; font-weight: 700; color: #4C1D95; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

    .ft-detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F5F3FF; font-size: 14px; }
    .ft-detail-row:last-child { border-bottom: none; }
    .ft-detail-row .k { color: var(--ft-text-muted); }
    .ft-detail-row .v { font-weight: 600; color: #374151; text-align: right; }

    .ft-method-badge { display: inline-block; padding: 4px 12px; border-radius: 999px; background: #EEF2FF; color: #4338CA; font-size: 12px; font-weight: 600; }

    table.ft-txn-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    table.ft-txn-table thead th {
        background: #F5F3FF; color: #4C1D95; font-weight: 700; text-transform: uppercase; font-size: 10.5px;
        letter-spacing: .04em; padding: 10px 14px; text-align: left;
    }
    table.ft-txn-table tbody td { padding: 12px 14px; border-top: 1px solid #F1EEFB; }
    .dir-badge { padding: 3px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
    .dir-debit { background: #FEE2E2; color: #B91C1C; }
    .dir-credit { background: #DCFCE7; color: #15803D; }
    .recon-badge { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .recon-yes { background: #E0E7FF; color: #4338CA; }
    .recon-no { background: #F3F4F6; color: #6B7280; }

    .ft-notes-box { background: #FAFAFB; border-radius: 12px; padding: 14px 16px; font-size: 14px; color: #4B5563; white-space: pre-wrap; }
</style>
@endpush

@section('content')
<div class="ft-wrap">
    <div class="ft-breadcrumb">
        <a href="{{ route('admin.finance.bank-transfers.index') }}">Bank Transfers</a> / #{{ $bankTransfer->id }}
    </div>

    <div class="ft-hero">
        <div class="ft-hero-top">
            <div>
                <div class="ft-hero-id"><i class="fas fa-right-left me-2"></i>Transfer #{{ $bankTransfer->id }}</div>
                <div class="ft-hero-date">{{ $bankTransfer->transfer_date?->format('d M Y') }} &middot; Banaya {{ $bankTransfer->creator?->name ?? 'System' }} ne</div>
            </div>
            <div class="ft-hero-actions">
                <a href="{{ route('admin.finance.bank-transfers.edit', $bankTransfer) }}" class="ft-btn-glass"><i class="fas fa-pen me-1"></i> Edit</a>
                @can('finance.approve')
                <button type="button" class="ft-btn-glass danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
                @endcan
            </div>
        </div>

        <div class="ft-hero-route">
            <div class="ft-hero-box">
                <div class="tag">From — Debit</div>
                <div class="name">{{ $bankTransfer->fromBankAccount?->name }}</div>
                <div class="sub">{{ $bankTransfer->fromBankAccount?->bank_name ?: ucfirst($bankTransfer->fromBankAccount?->type ?? '') }}</div>
            </div>
            <div class="ft-hero-amount">
                <div class="amt">₹{{ number_format($bankTransfer->amount, 2) }}</div>
                <i class="fas fa-arrow-right-long"></i>
            </div>
            <div class="ft-hero-box">
                <div class="tag">To — Credit</div>
                <div class="name">{{ $bankTransfer->toBankAccount?->name }}</div>
                <div class="sub">{{ $bankTransfer->toBankAccount?->bank_name ?: ucfirst($bankTransfer->toBankAccount?->type ?? '') }}</div>
            </div>
        </div>
    </div>

    <div class="ft-grid">
        <div class="ft-card">
            <h3><i class="fas fa-circle-info"></i> Transfer Detail</h3>
            <div class="ft-detail-row"><span class="k">Method</span><span class="v"><span class="ft-method-badge">{{ $bankTransfer->method }}</span></span></div>
            <div class="ft-detail-row"><span class="k">Reference No</span><span class="v">{{ $bankTransfer->reference_no ?: '—' }}</span></div>
            <div class="ft-detail-row"><span class="k">Transfer Date</span><span class="v">{{ $bankTransfer->transfer_date?->format('d M Y') }}</span></div>
            <div class="ft-detail-row"><span class="k">Created By</span><span class="v">{{ $bankTransfer->creator?->name ?: '—' }}</span></div>
            <div class="ft-detail-row"><span class="k">Created On</span><span class="v">{{ $bankTransfer->created_at?->format('d M Y, h:i A') }}</span></div>
            @if($bankTransfer->updated_at && !$bankTransfer->updated_at->eq($bankTransfer->created_at))
            <div class="ft-detail-row"><span class="k">Last Updated</span><span class="v">{{ $bankTransfer->updated_at?->format('d M Y, h:i A') }}</span></div>
            @endif
        </div>

        <div class="ft-card">
            <h3><i class="fas fa-note-sticky"></i> Notes</h3>
            <div class="ft-notes-box">{{ $bankTransfer->notes ?: 'Koi notes add nahi kiye gaye.' }}</div>
        </div>
    </div>

    <div class="ft-card">
        <h3><i class="fas fa-list-check"></i> Related Bank Transactions</h3>
        @if($transactions->count())
        <div class="table-responsive">
            <table class="ft-txn-table">
                <thead>
                    <tr>
                        <th>Transaction No</th>
                        <th>Account</th>
                        <th>Direction</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Reconciliation</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $txn)
                        <tr>
                            <td class="fw-semibold">{{ $txn->transaction_no }}</td>
                            <td>{{ $txn->bankAccount?->name }}</td>
                            <td><span class="dir-badge dir-{{ $txn->direction }}">{{ ucfirst($txn->direction) }}</span></td>
                            <td class="fw-semibold">₹{{ number_format($txn->amount, 2) }}</td>
                            <td>₹{{ number_format($txn->balance_after, 2) }}</td>
                            <td>
                                <span class="recon-badge {{ $txn->reconciliation_status === 'reconciled' ? 'recon-yes' : 'recon-no' }}">
                                    {{ $txn->reconciliation_status === 'reconciled' ? 'Reconciled' : 'Pending' }}
                                </span>
                            </td>
                            <td>{{ $txn->transaction_date?->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted mb-0">Koi related transaction nahi mila.</p>
        @endif
    </div>
</div>

{{-- Delete modal --}}
@can('finance.approve')
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <form method="POST" action="{{ route('admin.finance.bank-transfers.destroy', $bankTransfer) }}">
                @csrf @method('DELETE')
                <div class="modal-header" style="background: linear-gradient(135deg,#EF4444,#DC2626); color:#fff; border:none;">
                    <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2"></i>Transfer Delete Karein?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-4 pb-2">
                    <p class="mb-2">Ye transfer delete karne se <strong>dono accounts ka balance revert</strong> ho jayega:</p>
                    <ul class="mb-3" style="font-size:14px;">
                        <li><strong>{{ $bankTransfer->fromBankAccount?->name }}</strong> me ₹{{ number_format($bankTransfer->amount,2) }} wapas add hoga</li>
                        <li><strong>{{ $bankTransfer->toBankAccount?->name }}</strong> se ₹{{ number_format($bankTransfer->amount,2) }} kam hoga</li>
                    </ul>
                    <div class="alert alert-warning py-2" style="font-size:13px; border-radius:10px;">
                        Ye action wapas nahi ho sakta. Pakka confirm karo.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Haan, Delete Karo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection
