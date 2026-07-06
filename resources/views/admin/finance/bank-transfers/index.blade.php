@extends('admin.layouts.app')

@section('title', 'Bank Transfers')

@push('styles')
<style>
    :root {
        --ft-grad-1: #7C3AED;
        --ft-grad-2: #A855F7;
        --ft-grad-3: #6366F1;
        --ft-bg-soft: #F5F3FF;
        --ft-border: #E9E4FA;
        --ft-text-muted: #6B7280;
        --ft-glass: rgba(255, 255, 255, 0.65);
    }

    .ft-wrap { font-family: 'Inter', 'Outfit', sans-serif; }

    .ft-hero {
        background: linear-gradient(135deg, var(--ft-grad-1) 0%, var(--ft-grad-3) 55%, var(--ft-grad-2) 100%);
        border-radius: 20px;
        padding: 28px 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 30px -10px rgba(124, 58, 237, 0.45);
        margin-bottom: 24px;
    }
    .ft-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(255,255,255,0.18), transparent 55%);
    }
    .ft-hero h1 {
        font-family: 'Outfit', sans-serif;
        font-weight: 700; font-size: 26px; margin: 0 0 4px;
        display: flex; align-items: center; gap: 12px;
    }
    .ft-hero p { margin: 0; opacity: .9; font-size: 14px; }
    .ft-hero .ft-icon-badge {
        width: 46px; height: 46px; border-radius: 14px;
        background: rgba(255,255,255,0.18);
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(6px);
        font-size: 20px;
    }
    .ft-hero-actions { position: relative; z-index: 1; }

    .ft-btn-glass {
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.35);
        color: #fff; padding: 10px 20px; border-radius: 12px;
        font-weight: 600; font-size: 14px; transition: all .2s;
        backdrop-filter: blur(6px);
    }
    .ft-btn-glass:hover { background: rgba(255,255,255,0.28); color: #fff; transform: translateY(-1px); }

    /* Stat cards */
    .ft-stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .ft-stat {
        background: var(--ft-glass);
        border: 1px solid var(--ft-border);
        border-radius: 16px; padding: 18px 20px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 14px -8px rgba(124,58,237,0.15);
    }
    .ft-stat .label { font-size: 12px; color: var(--ft-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .ft-stat .value { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 700; color: #4C1D95; margin-top: 4px; }

    /* Filter panel */
    .ft-filters {
        background: #fff; border: 1px solid var(--ft-border); border-radius: 16px;
        padding: 18px 20px; margin-bottom: 20px;
        box-shadow: 0 4px 16px -12px rgba(0,0,0,0.08);
    }
    .ft-filters label { font-size: 12px; font-weight: 600; color: var(--ft-text-muted); margin-bottom: 6px; display: block; }
    .ft-filters .form-control, .ft-filters .form-select {
        border-radius: 10px; border: 1px solid var(--ft-border); font-size: 14px;
    }
    .ft-filters .form-control:focus, .ft-filters .form-select:focus {
        border-color: var(--ft-grad-1); box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
    }

    /* Table card */
    .ft-table-card {
        background: #fff; border-radius: 18px; border: 1px solid var(--ft-border);
        overflow: hidden; box-shadow: 0 6px 24px -14px rgba(76, 29, 149, 0.18);
    }
    .ft-table-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--ft-border);
        gap: 12px; flex-wrap: wrap;
    }
    .ft-search { position: relative; width: 280px; max-width: 100%; }
    .ft-search input {
        width: 100%; padding: 9px 14px 9px 36px; border-radius: 10px;
        border: 1px solid var(--ft-border); font-size: 14px; outline: none;
    }
    .ft-search input:focus { border-color: var(--ft-grad-1); box-shadow: 0 0 0 3px rgba(124,58,237,0.12); }
    .ft-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #A1A1AA; font-size: 13px; }

    table.ft-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    table.ft-table thead th {
        background: var(--ft-bg-soft); color: #4C1D95; font-weight: 700;
        text-transform: uppercase; font-size: 11px; letter-spacing: .04em;
        padding: 13px 18px; text-align: left; white-space: nowrap;
        cursor: pointer; user-select: none;
    }
    table.ft-table thead th:hover { background: #EDE7FE; }
    table.ft-table thead th .sort-icon { opacity: .35; margin-left: 4px; font-size: 10px; }
    table.ft-table tbody td { padding: 14px 18px; border-top: 1px solid #F1EEFB; vertical-align: middle; color: #374151; }
    table.ft-table tbody tr { transition: background .15s; }
    table.ft-table tbody tr:hover { background: #FBFAFF; }

    .ft-route-cell { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #312E81; }
    .ft-route-cell .acc-chip {
        font-size: 12px; padding: 4px 10px; border-radius: 999px; font-weight: 600;
    }
    .acc-from { background: #FEE2E2; color: #B91C1C; }
    .acc-to   { background: #DCFCE7; color: #15803D; }
    .ft-route-arrow { color: #A855F7; font-size: 15px; }

    .ft-amount { font-family: 'Outfit', sans-serif; font-weight: 700; color: #4C1D95; }
    .ft-method-badge {
        display: inline-block; padding: 4px 12px; border-radius: 999px;
        background: #EEF2FF; color: #4338CA; font-size: 12px; font-weight: 600;
    }
    .ft-ref { color: #9CA3AF; font-size: 12.5px; }

    .ft-actions { display: flex; gap: 6px; }
    .ft-icon-btn {
        width: 33px; height: 33px; border-radius: 9px; display: inline-flex;
        align-items: center; justify-content: center; border: 1px solid var(--ft-border);
        color: #6D28D9; background: #fff; transition: all .15s; text-decoration: none;
    }
    .ft-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }
    .ft-icon-btn.danger { color: #DC2626; }
    .ft-icon-btn.danger:hover { background: #DC2626; border-color: #DC2626; color: #fff; }

    .ft-empty { text-align: center; padding: 60px 20px; color: var(--ft-text-muted); }
    .ft-empty i { font-size: 42px; color: #DDD6FE; margin-bottom: 12px; display: block; }

    .ft-pagination { padding: 16px 20px; border-top: 1px solid var(--ft-border); }
</style>
@endpush

@section('content')
<div class="ft-wrap">

    {{-- Hero header --}}
    <div class="ft-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="ft-icon-badge"><i class="fas fa-right-left"></i></div>
            <div>
                <h1>Bank Transfers</h1>
                <p>Ek bank/cash account se doosre me fund move karo — balance dono taraf auto-update hoga.</p>
            </div>
        </div>
        <div class="ft-hero-actions">
            <a href="{{ route('admin.finance.bank-transfers.create') }}" class="ft-btn-glass">
                <i class="fas fa-plus me-2"></i> Naya Transfer
            </a>
        </div>
    </div>

    {{-- Quick stats (current page ke transfers par based) --}}
    <div class="ft-stat-row">
        <div class="ft-stat">
            <div class="label">Total Transfers</div>
            <div class="value">{{ $transfers->total() }}</div>
        </div>
        <div class="ft-stat">
            <div class="label">Is Page Ka Amount</div>
            <div class="value">₹{{ number_format($transfers->getCollection()->sum('amount'), 2) }}</div>
        </div>
        <div class="ft-stat">
            <div class="label">Active Accounts</div>
            <div class="value">{{ $bankAccounts->count() }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="ft-filters">
        <form method="GET" action="{{ route('admin.finance.bank-transfers.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3 col-6">
                <label>From Account</label>
                <select name="from_bank_account_id" class="form-select">
                    <option value="">Sabhi</option>
                    @foreach($bankAccounts as $acc)
                        <option value="{{ $acc->id }}" @selected(request('from_bank_account_id') == $acc->id)>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-6">
                <label>To Account</label>
                <select name="to_bank_account_id" class="form-select">
                    <option value="">Sabhi</option>
                    @foreach($bankAccounts as $acc)
                        <option value="{{ $acc->id }}" @selected(request('to_bank_account_id') == $acc->id)>{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label>From Date</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-2 col-6">
                <label>To Date</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm w-100" style="background: var(--ft-grad-1); color:#fff; border-radius:10px; font-weight:600;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['from_bank_account_id','to_bank_account_id','from','to']))
                    <a href="{{ route('admin.finance.bank-transfers.index') }}" class="btn btn-sm btn-light" style="border-radius:10px;" title="Clear">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="ft-table-card">
        <div class="ft-table-toolbar">
            <div class="ft-search">
                <i class="fas fa-search"></i>
                <input type="text" id="ftQuickSearch" placeholder="Is page me dhoondo... (account, method, reference)">
            </div>
            <span class="ft-ref">Total {{ $transfers->total() }} records mile</span>
        </div>

        @if($transfers->count())
        <div class="table-responsive">
            <table class="ft-table" id="ftTable">
                <thead>
                    <tr>
                        <th data-sort="date">Date <i class="fas fa-sort sort-icon"></i></th>
                        <th data-sort="route">Route</th>
                        <th data-sort="amount">Amount <i class="fas fa-sort sort-icon"></i></th>
                        <th data-sort="method">Method</th>
                        <th data-sort="reference">Reference</th>
                        <th data-sort="by">Created By</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                        <tr>
                            <td data-val="{{ $transfer->transfer_date?->format('Y-m-d') }}">
                                {{ $transfer->transfer_date?->format('d M Y') }}
                            </td>
                            <td>
                                <div class="ft-route-cell">
                                    <span class="acc-chip acc-from">{{ $transfer->fromBankAccount?->name ?? '—' }}</span>
                                    <i class="fas fa-arrow-right ft-route-arrow"></i>
                                    <span class="acc-chip acc-to">{{ $transfer->toBankAccount?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td data-val="{{ $transfer->amount }}">
                                <span class="ft-amount">₹{{ number_format($transfer->amount, 2) }}</span>
                            </td>
                            <td><span class="ft-method-badge">{{ $transfer->method }}</span></td>
                            <td><span class="ft-ref">{{ $transfer->reference_no ?: '—' }}</span></td>
                            <td>{{ $transfer->creator?->name ?: '—' }}</td>
                            <td>
                                <div class="ft-actions justify-content-end">
                                    <a href="{{ route('admin.finance.bank-transfers.show', $transfer) }}" class="ft-icon-btn" title="Dekho">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.finance.bank-transfers.edit', $transfer) }}" class="ft-icon-btn" title="Edit karo">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @can('finance.approve')
                                    <button type="button" class="ft-icon-btn danger" title="Delete karo"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transfer->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Delete confirmation modal — Hinglish two-step flow --}}
                        @can('finance.approve')
                        <div class="modal fade" id="deleteModal{{ $transfer->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
                                    <form method="POST" action="{{ route('admin.finance.bank-transfers.destroy', $transfer) }}">
                                        @csrf @method('DELETE')
                                        <div class="modal-header" style="background: linear-gradient(135deg,#EF4444,#DC2626); color:#fff; border:none;">
                                            <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2"></i>Transfer Delete Karein?</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body pt-4 pb-2">
                                            <p class="mb-2">Ye transfer delete karne se <strong>dono accounts ka balance revert</strong> ho jayega:</p>
                                            <ul class="mb-3" style="font-size:14px;">
                                                <li><strong>{{ $transfer->fromBankAccount?->name }}</strong> me ₹{{ number_format($transfer->amount,2) }} wapas add hoga</li>
                                                <li><strong>{{ $transfer->toBankAccount?->name }}</strong> se ₹{{ number_format($transfer->amount,2) }} kam hoga</li>
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
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="ft-pagination">
            {{ $transfers->onEachSide(1)->links() }}
        </div>
        @else
        <div class="ft-empty">
            <i class="fas fa-right-left"></i>
            <p class="mb-2 fw-semibold">Abhi tak koi bank transfer nahi hua</p>
            <a href="{{ route('admin.finance.bank-transfers.create') }}" class="btn btn-sm" style="background:var(--ft-grad-1); color:#fff; border-radius:10px;">
                Pehla Transfer Banao
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Halka client-side quick search (current page ke rows par) — sorting bhi
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('ftQuickSearch');
        const table = document.getElementById('ftTable');
        if (!table) return;

        const tbody = table.querySelector('tbody');

        input?.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            tbody.querySelectorAll('tr').forEach(function (row) {
                if (row.querySelector('.modal')) return; // skip stray nodes
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        // Column sorting
        table.querySelectorAll('thead th[data-sort]').forEach(function (th, idx) {
            th.addEventListener('click', function () {
                const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => !r.classList.contains('modal'));
                const asc = !th.classList.contains('asc');
                table.querySelectorAll('thead th').forEach(h => h.classList.remove('asc', 'desc'));
                th.classList.add(asc ? 'asc' : 'desc');

                rows.sort(function (a, b) {
                    const cellA = a.children[idx];
                    const cellB = b.children[idx];
                    const valA = cellA?.dataset.val ?? cellA?.innerText ?? '';
                    const valB = cellB?.dataset.val ?? cellB?.innerText ?? '';
                    const numA = parseFloat(valA), numB = parseFloat(valB);
                    let cmp;
                    if (!isNaN(numA) && !isNaN(numB) && cellA?.dataset.val) {
                        cmp = numA - numB;
                    } else {
                        cmp = valA.localeCompare(valB);
                    }
                    return asc ? cmp : -cmp;
                });

                rows.forEach(r => tbody.appendChild(r));
            });
        });
    });
</script>
@endpush
