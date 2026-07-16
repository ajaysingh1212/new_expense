@extends('admin.layouts.app')

@section('title', $isReport ? 'Finance Report Center' : 'Dashboard')
@section('page-title', $isReport ? 'Finance Report Center' : 'Dashboard')

@section('breadcrumbs')
    @if($isReport)
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Report</li>
    @else
        <li class="breadcrumb-item active">Overview</li>
    @endif
@endsection

@push('styles')
<style>
    :root {
        --dash-bg: #eef2ff;
        --dash-card: rgba(255,255,255,.92);
        --dash-border: rgba(148,163,184,.18);
        --dash-text: #0f172a;
        --dash-muted: #64748b;
        --dash-primary: #2563eb;
        --dash-primary-2: #0f4c81;
        --dash-success: #059669;
        --dash-warning: #d97706;
        --dash-danger: #dc2626;
        --dash-shadow: 0 18px 50px rgba(15,23,42,.08);
        --dash-radius: 22px;
    }

    .dashboard-shell {
        position: relative;
    }

    .dashboard-shell::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at top left, rgba(37,99,235,.10), transparent 32%),
            radial-gradient(circle at top right, rgba(5,150,105,.09), transparent 30%),
            linear-gradient(180deg, rgba(255,255,255,.45), rgba(255,255,255,0));
        z-index: 0;
    }

    .dashboard-shell > * {
        position: relative;
        z-index: 1;
    }

    .dash-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 45%, #0f766e 100%);
        color: #fff;
        border-radius: 28px;
        padding: 28px;
        box-shadow: var(--dash-shadow);
        overflow: hidden;
        position: relative;
        margin-bottom: 22px;
    }

    .dash-hero::before,
    .dash-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.10);
        filter: blur(6px);
    }

    .dash-hero::before {
        width: 220px;
        height: 220px;
        right: -70px;
        top: -70px;
    }

    .dash-hero::after {
        width: 160px;
        height: 160px;
        left: 35%;
        bottom: -65px;
    }

    .dash-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 999px;
        padding: 7px 12px;
        font-size: .76rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .dash-title {
        font-size: clamp(1.55rem, 2.4vw, 2.35rem);
        font-weight: 800;
        line-height: 1.1;
        margin: 14px 0 8px;
    }

    .dash-subtitle {
        color: rgba(226,232,240,.92);
        font-size: .96rem;
        max-width: 760px;
        margin: 0;
    }

    .dash-switch {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .dash-switch .btn {
        border-radius: 999px;
        padding: 10px 18px;
        font-weight: 700;
        border: 1px solid rgba(255,255,255,.18);
        background: rgba(255,255,255,.10);
        color: #fff;
        box-shadow: none;
    }

    .dash-switch .btn.active {
        background: #fff;
        color: #0f172a;
    }

    .glass-card {
        background: var(--dash-card);
        backdrop-filter: blur(10px);
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        box-shadow: var(--dash-shadow);
        overflow: hidden;
    }

    .glass-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(148,163,184,.16);
        background: linear-gradient(180deg, rgba(248,250,252,.94), rgba(255,255,255,.92));
    }

    .glass-card-header h3 {
        margin: 0;
        font-size: .95rem;
        font-weight: 800;
        color: var(--dash-text);
    }

    .glass-card-body {
        padding: 20px;
    }

    .metric-card {
        position: relative;
        border-radius: 22px;
        padding: 18px;
        background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.94));
        border: 1px solid var(--dash-border);
        box-shadow: var(--dash-shadow);
        overflow: hidden;
        height: 100%;
    }

    .metric-card::after {
        content: '';
        position: absolute;
        right: -18px;
        bottom: -18px;
        width: 96px;
        height: 96px;
        border-radius: 30px;
        background: rgba(37,99,235,.08);
        transform: rotate(16deg);
    }

    .metric-label {
        font-size: .74rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--dash-muted);
        font-weight: 800;
        margin-bottom: 10px;
    }

    .metric-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--dash-text);
        margin-bottom: 6px;
    }

    .metric-meta {
        color: var(--dash-muted);
        font-size: .82rem;
    }

    .metric-accent {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 14px;
        color: #fff;
    }

    .accent-primary { background: linear-gradient(135deg, #2563eb, #0f4c81); }
    .accent-success { background: linear-gradient(135deg, #059669, #0f766e); }
    .accent-warning { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .accent-danger  { background: linear-gradient(135deg, #dc2626, #b91c1c); }
    .accent-purple  { background: linear-gradient(135deg, #7c3aed, #4f46e5); }

    .report-toolbar {
        display: grid;
        gap: 14px;
    }

    .report-toolbar .form-control,
    .report-toolbar .custom-select {
        border-radius: 14px;
        min-height: 48px;
        border-color: #dbe3ef;
        box-shadow: none;
        font-weight: 600;
    }

    .report-period-chip {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .report-period-chip .btn {
        border-radius: 999px;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
        padding: 8px 13px;
    }

    .report-period-chip .btn.active {
        background: #0f172a;
        color: #fff;
        border-color: #0f172a;
    }

    .chart-card {
        min-height: 420px;
    }

    .chart-frame {
        height: 330px;
    }

    .chart-frame.tall {
        height: 360px;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--dash-muted);
        background: #f8fafc;
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .report-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        font-size: .88rem;
        color: #0f172a;
    }

    .report-table tbody tr:hover {
        background: #f8fafc;
    }

    .report-type-pill,
    .report-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .pill-expense { background: #fef2f2; color: #b91c1c; }
    .pill-cashin  { background: #ecfdf5; color: #047857; }
    .pill-draft   { background: #f1f5f9; color: #475569; }
    .pill-submitted { background: #eff6ff; color: #1d4ed8; }
    .pill-approved  { background: #ecfeff; color: #0f766e; }
    .pill-partial   { background: #fffbeb; color: #b45309; }
    .pill-paid      { background: #dcfce7; color: #15803d; }
    .pill-received  { background: #dcfce7; color: #15803d; }
    .pill-rejected  { background: #fef2f2; color: #dc2626; }
    .pill-unreconciled { background: #fff7ed; color: #c2410c; }
    .pill-reconciled   { background: #ecfdf5; color: #047857; }

    .mini-list-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .mini-list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .mini-badge {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
    }

    .mini-badge.in { background: linear-gradient(135deg, #059669, #0f766e); }
    .mini-badge.out { background: linear-gradient(135deg, #dc2626, #b91c1c); }

    .mini-title {
        font-weight: 800;
        color: var(--dash-text);
        font-size: .92rem;
        margin-bottom: 4px;
    }

    .mini-meta {
        color: var(--dash-muted);
        font-size: .8rem;
    }

    .custom-range-wrap {
        display: none;
    }

    .custom-range-wrap.show {
        display: flex;
    }

    .empty-state {
        text-align: center;
        padding: 56px 24px;
        color: var(--dash-muted);
    }

    .empty-state i {
        display: block;
        font-size: 2rem;
        margin-bottom: 10px;
        opacity: .45;
    }

    @media (max-width: 991px) {
        .dash-hero { padding: 22px; }
        .dash-switch { justify-content: flex-start; margin-top: 14px; }
        .chart-card { min-height: auto; }
        .chart-frame { height: 280px; }
    }
</style>
@endpush

@section('content')
@php
    $money = fn ($value) => 'Rs ' . number_format((float) $value, 2);
    $isReport = (bool) $isReport;
    $reportSummary = $reportSummary ?? [];
    $reportChartData = $reportChartData ?? [];
    $reportRows = collect($reportRows ?? []);
    $reportLedgerName = $reportLedger?->name ?: 'All Ledgers';
    $typeLabel = match($reportType ?? 'all') {
        'expense' => 'Expense only',
        'cash_in' => 'Cash In only',
        default => 'Expense + Cash In',
    };
    $periodLabel = $reportPeriodLabel ?? 'This Month';
    $pillClass = fn ($value) => match(strtolower((string) $value)) {
        'draft' => 'pill-draft',
        'submitted' => 'pill-submitted',
        'approved' => 'pill-approved',
        'partial' => 'pill-partial',
        'paid' => 'pill-paid',
        'received' => 'pill-received',
        'rejected' => 'pill-rejected',
        'unreconciled' => 'pill-unreconciled',
        'reconciled' => 'pill-reconciled',
        default => 'pill-draft',
    };
    $directionBadge = fn ($kind) => $kind === 'cash_in' ? 'pill-cashin' : 'pill-expense';
@endphp

<div class="dashboard-shell">
    <div class="dash-hero">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <span class="dash-kicker">
                    <i class="fas fa-chart-line"></i>
                    {{ $isReport ? 'Report mode' : 'Overview mode' }}
                </span>
                <h1 class="dash-title">
                    {{ $isReport ? 'Ledger-wise finance report center' : 'Admin dashboard overview' }}
                </h1>
                <p class="dash-subtitle">
                    {{ $isReport ? 'Ledger aur date range select karke expense, cash in, chart analytics aur detailed transactions ek hi screen par dekhiye.' : 'Quick summary, recent finance activity aur shortcut actions ek clean overview me.' }}
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="dash-switch">
                    <a href="{{ route('admin.dashboard') }}" class="btn {{ $isReport ? '' : 'active' }}">
                        <i class="fas fa-house mr-1"></i> Overview
                    </a>
                    <a href="{{ route('admin.dashboard', array_merge(request()->except(['view', 'page', 'report_page']), ['view' => 'report'])) }}" class="btn {{ $isReport ? 'active' : '' }}">
                        <i class="fas fa-file-alt mr-1"></i> Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($isReport)
        <div class="glass-card mb-4">
            <div class="glass-card-header">
                <h3><i class="fas fa-sliders-h mr-2 text-primary"></i>Report filters</h3>
                <div class="text-muted small font-weight-bold">
                    {{ $periodLabel }} · {{ $typeLabel }} · {{ $reportLedgerName }}
                </div>
            </div>
            <div class="glass-card-body">
                <form method="GET" action="{{ route('admin.dashboard') }}" id="dashboardReportForm" class="report-toolbar">
                    <input type="hidden" name="view" value="report">
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">Ledger</label>
                            <select name="report_ledger_id" class="form-control">
                                <option value="">All Ledgers</option>
                                @foreach($ledgers as $ledger)
                                    <option value="{{ $ledger->id }}" @selected((int) $reportLedgerId === (int) $ledger->id)>{{ $ledger->name }} ({{ ucfirst($ledger->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">View type</label>
                            <select name="report_type" class="form-control">
                                <option value="all" @selected(($reportType ?? 'all') === 'all')>Expense + Cash In</option>
                                <option value="expense" @selected(($reportType ?? '') === 'expense')>Expense only</option>
                                <option value="cash_in" @selected(($reportType ?? '') === 'cash_in')>Cash In only</option>
                            </select>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">Time period</label>
                            <div class="report-period-chip">
                                @foreach([
                                    'today' => 'Today',
                                    'yesterday' => 'Yesterday',
                                    'this_week' => 'This Week',
                                    'this_month' => 'This Month',
                                    '3_month' => '3 Month',
                                    '6_month' => '6 Month',
                                    '9_month' => '9 Month',
                                    'this_year' => 'This Year',
                                    'custom' => 'Custom Date',
                                ] as $value => $label)
                                    <button type="button" class="btn period-btn {{ ($reportPeriod ?? 'this_month') === $value ? 'active' : '' }}" data-period="{{ $value }}">{{ $label }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" name="report_period" id="report_period" value="{{ $reportPeriod ?? 'this_month' }}">
                        </div>
                    </div>

                    <div class="row custom-range-wrap {{ ($reportPeriod ?? '') === 'custom' ? 'show' : '' }}" id="customRangeWrap">
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">From date</label>
                            <input type="date" name="report_from" value="{{ $reportFrom ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold small text-uppercase text-muted">To date</label>
                            <input type="date" name="report_to" value="{{ $reportTo ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end justify-content-end">
                            <div class="w-100 d-flex flex-wrap justify-content-end" style="gap:10px;">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-filter mr-1"></i> Apply report
                                </button>
                                <a href="{{ route('admin.dashboard', ['view' => 'report']) }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-rotate-left mr-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-primary"><i class="fas fa-receipt"></i></div>
                    <div class="metric-label">Transactions</div>
                    <div class="metric-value">{{ number_format((int) ($reportSummary['transactions'] ?? 0)) }}</div>
                    <div class="metric-meta">{{ $periodLabel }} report rows</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-danger"><i class="fas fa-arrow-up-right-dots"></i></div>
                    <div class="metric-label">Expense total</div>
                    <div class="metric-value" style="color:var(--dash-danger)">{{ $money($reportSummary['expense_total'] ?? 0) }}</div>
                    <div class="metric-meta">{{ number_format((int) ($reportSummary['expense_count'] ?? 0)) }} expense rows</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-success"><i class="fas fa-arrow-down"></i></div>
                    <div class="metric-label">Cash in total</div>
                    <div class="metric-value" style="color:var(--dash-success)">{{ $money($reportSummary['cashin_total'] ?? 0) }}</div>
                    <div class="metric-meta">{{ number_format((int) ($reportSummary['cashin_count'] ?? 0)) }} cash-in rows</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-warning"><i class="fas fa-scale-balanced"></i></div>
                    <div class="metric-label">Net movement</div>
                    <div class="metric-value">{{ $money($reportSummary['net_total'] ?? 0) }}</div>
                    <div class="metric-meta">Cash in minus expense</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="glass-card chart-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-chart-pie mr-2 text-info"></i>Expense vs Cash In</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="reportPieChart" class="chart-frame"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-3">
                <div class="glass-card chart-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-wave-square mr-2 text-success"></i>Wave trend</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="reportWaveChart" class="chart-frame"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-7 mb-3">
                <div class="glass-card chart-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-chart-candlestick mr-2 text-warning"></i>Candle movement</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="reportCandleChart" class="chart-frame tall"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-3">
                <div class="glass-card chart-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-chart-radar mr-2 text-primary"></i>Status radar</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="reportRadarChart" class="chart-frame tall"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="glass-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-table mr-2 text-dark"></i>Transaction details</h3>
                        <span class="text-muted small font-weight-bold">{{ $reportRows->count() }} rows</span>
                    </div>
                    <div class="table-responsive">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Ledger</th>
                                    <th>Title</th>
                                    <th>Bank</th>
                                    <th>Status</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportRows as $row)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $row['date'] }}</div>
                                            <div class="text-muted small">{{ $row['reference_no'] }}</div>
                                        </td>
                                        <td><span class="report-type-pill {{ $directionBadge($row['kind']) }}">{{ $row['kind'] === 'cash_in' ? 'Cash In' : 'Expense' }}</span></td>
                                        <td>
                                            <div class="font-weight-bold">{{ $row['ledger_name'] }}</div>
                                            <div class="text-muted small">{{ $row['party_name'] }}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $row['title'] }}</div>
                                            <div class="text-muted small">{{ $row['description'] }}</div>
                                        </td>
                                        <td>{{ $row['bank_name'] }}</td>
                                        <td>
                                            <span class="report-status-pill {{ $pillClass($row['status_key']) }}">
                                                {{ ucfirst(str_replace('_', ' ', $row['status'])) }}
                                            </span>
                                            <div class="text-muted small mt-1">{{ ucfirst($row['reconciliation_status']) }}</div>
                                        </td>
                                        <td class="text-right font-weight-bold">{{ $money($row['amount']) }}</td>
                                        <td class="text-right">{{ $money($row['balance_after']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                <i class="fas fa-filter-circle-dollar"></i>
                                                <div class="font-weight-bold text-dark">No transaction found</div>
                                                <div>Selected filters ke hisab se koi expense ya cash-in entry nahi mili.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="glass-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-layer-group mr-2 text-primary"></i>Top ledgers</h3>
                    </div>
                    <div class="glass-card-body">
                        @forelse(collect($reportChartData['topLedgers'] ?? []) as $item)
                            <div class="mini-list-item">
                                <div class="mini-badge in">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="mini-title">{{ $item['name'] }}</div>
                                    <div class="mini-meta">{{ number_format((int) $item['count']) }} entries</div>
                                </div>
                                <div class="text-right font-weight-bold">{{ $money($item['amount']) }}</div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-layer-group"></i>
                                <div>No ledger summary available</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-primary"><i class="fas fa-wallet"></i></div>
                    <div class="metric-label">Bank balance</div>
                    <div class="metric-value">{{ $money($financeStats['bank_balance'] ?? 0) }}</div>
                    <div class="metric-meta">All active bank accounts</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-danger"><i class="fas fa-file-invoice"></i></div>
                    <div class="metric-label">Planned expense</div>
                    <div class="metric-value">{{ $money($financeStats['planned_expense'] ?? 0) }}</div>
                    <div class="metric-meta">{{ number_format((int) ($financeStats['outstanding'] ?? 0)) }} pending</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-success"><i class="fas fa-arrow-trend-up"></i></div>
                    <div class="metric-label">Planned cash in</div>
                    <div class="metric-value">{{ $money($financeStats['planned_income'] ?? 0) }}</div>
                    <div class="metric-meta">Expected collections</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="metric-card">
                    <div class="metric-accent accent-warning"><i class="fas fa-circle-exclamation"></i></div>
                    <div class="metric-label">Unreconciled</div>
                    <div class="metric-value">{{ number_format((int) ($financeStats['unreconciled_count'] ?? 0)) }}</div>
                    <div class="metric-meta">Transactions pending review</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="glass-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-bolt mr-2 text-primary"></i>Quick finance snapshot</h3>
                    </div>
                    <div class="glass-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="mini-list-item">
                                    <div class="mini-badge in"><i class="fas fa-building-columns"></i></div>
                                    <div>
                                        <div class="mini-title">Bank & cash</div>
                                        <div class="mini-meta">{{ $bankAccounts->count() }} active accounts</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="mini-list-item">
                                    <div class="mini-badge out"><i class="fas fa-receipt"></i></div>
                                    <div>
                                        <div class="mini-title">Expense plans</div>
                                        <div class="mini-meta">{{ $expensePlans->count() }} highlighted for approval</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="mini-list-item">
                                    <div class="mini-badge in"><i class="fas fa-sack-dollar"></i></div>
                                    <div>
                                        <div class="mini-title">Cashflow plans</div>
                                        <div class="mini-meta">{{ $cashflowPlans->count() }} upcoming receipts</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="mini-list-item">
                                    <div class="mini-badge out"><i class="fas fa-triangle-exclamation"></i></div>
                                    <div>
                                        <div class="mini-title">Awaiting receipts</div>
                                        <div class="mini-meta">{{ $awaitingReceipts->count() }} approved cash-ins pending receive</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.dashboard', ['view' => 'report']) }}" class="btn btn-primary px-4">
                                <i class="fas fa-file-chart-column mr-1"></i> Open report view
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="glass-card h-100">
                    <div class="glass-card-header">
                        <h3><i class="fas fa-bell mr-2 text-warning"></i>Recent activity</h3>
                    </div>
                    <div class="glass-card-body">
                        @forelse($recentActivity as $activity)
                            <div class="mini-list-item">
                                <div class="mini-badge in"><i class="fas fa-clock"></i></div>
                                <div class="flex-grow-1">
                                    <div class="mini-title">{{ $activity->action ?? 'Activity' }}</div>
                                    <div class="mini-meta">{{ $activity->description ?? 'Recent system update' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-clock"></i>
                                <div>No recent activity</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
    const isReport = @json($isReport);
    if (isReport) {
        const periodInput = document.getElementById('report_period');
        const customRange = document.getElementById('customRangeWrap');
        const periodButtons = Array.from(document.querySelectorAll('.period-btn'));
        const form = document.getElementById('dashboardReportForm');
        const chartData = @json($reportChartData ?? []);
        const money = value => 'Rs ' + Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const setActivePeriod = (value) => {
            periodInput.value = value;
            periodButtons.forEach(btn => btn.classList.toggle('active', btn.dataset.period === value));
            customRange.classList.toggle('show', value === 'custom');
        };

        periodButtons.forEach(btn => {
            btn.addEventListener('click', () => setActivePeriod(btn.dataset.period));
            btn.addEventListener('click', () => {
                if (btn.dataset.period !== 'custom') {
                    form.submit();
                }
            });
        });

        form.addEventListener('submit', () => {
            if (periodInput.value !== 'custom') {
                customRange.querySelectorAll('input[type="date"]').forEach(input => input.disabled = true);
            }
        });

        const pieSeries = Array.isArray(chartData?.pie?.values) ? chartData.pie.values : [];
        const pieLabels = Array.isArray(chartData?.pie?.labels) ? chartData.pie.labels : [];

        new ApexCharts(document.querySelector('#reportPieChart'), {
            chart: {
                type: 'donut',
                height: 330,
                toolbar: { show: false },
            },
            series: pieSeries,
            labels: pieLabels,
            colors: ['#dc2626', '#059669'],
            dataLabels: { enabled: true },
            legend: {
                position: 'bottom',
                fontSize: '13px',
                markers: { radius: 12 }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { show: true },
                            value: {
                                show: true,
                                formatter: money
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return money(w.globals.seriesTotals.reduce((a, b) => a + b, 0));
                                }
                            }
                        }
                    }
                }
            }
        }).render();

        const waveLabels = Array.isArray(chartData?.wave?.labels) ? chartData.wave.labels : [];
        const waveExpense = Array.isArray(chartData?.wave?.expense) ? chartData.wave.expense : [];
        const waveCashIn = Array.isArray(chartData?.wave?.cash_in) ? chartData.wave.cash_in : [];

        new ApexCharts(document.querySelector('#reportWaveChart'), {
            chart: {
                type: 'area',
                height: 330,
                toolbar: { show: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 900
                }
            },
            series: [
                { name: 'Expense', data: waveExpense },
                { name: 'Cash In', data: waveCashIn },
            ],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.06,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#dc2626', '#059669'],
            xaxis: { categories: waveLabels },
            yaxis: {
                labels: {
                    formatter: value => 'Rs ' + Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })
                }
            },
            tooltip: {
                y: {
                    formatter: value => money(value)
                }
            },
            legend: {
                position: 'bottom'
            }
        }).render();

        new ApexCharts(document.querySelector('#reportCandleChart'), {
            chart: {
                type: 'candlestick',
                height: 360,
                toolbar: { show: false }
            },
            series: [{
                data: Array.isArray(chartData?.candle?.series) ? chartData.candle.series : []
            }],
            plotOptions: {
                candlestick: {
                    colors: {
                        upward: '#059669',
                        downward: '#dc2626'
                    }
                }
            },
            xaxis: {
                type: 'category'
            },
            yaxis: {
                tooltip: { enabled: true },
                labels: {
                    formatter: value => 'Rs ' + Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })
                }
            }
        }).render();

        new ApexCharts(document.querySelector('#reportRadarChart'), {
            chart: {
                type: 'radar',
                height: 360,
                toolbar: { show: false }
            },
            series: [{
                name: 'Transactions',
                data: Array.isArray(chartData?.radar?.values) ? chartData.radar.values : []
            }],
            labels: Array.isArray(chartData?.radar?.labels) ? chartData.radar.labels : [],
            fill: {
                opacity: 0.18
            },
            stroke: {
                width: 2
            },
            markers: {
                size: 4
            },
            colors: ['#2563eb']
        }).render();

        if (periodInput.value === 'custom') {
            customRange.classList.add('show');
        }
    }
})();
</script>
@endpush
