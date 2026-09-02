<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bank Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 18px 0 8px; }
        .muted { color: #6b7280; }
        .top { border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 12px; }
        .grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .grid td { width: 25%; border: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
        .label { font-size: 9px; text-transform: uppercase; color: #6b7280; margin-bottom: 3px; }
        .value { font-weight: bold; font-size: 12px; }
        table.statement { width: 100%; border-collapse: collapse; }
        table.statement th { background: #f3f4f6; border: 1px solid #d1d5db; padding: 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
        table.statement td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: top; }
        .right { text-align: right; }
        .debit { color: #b91c1c; font-weight: bold; }
        .credit { color: #047857; font-weight: bold; }
        .balance { font-weight: bold; }
        .small { font-size: 9px; }
        .footer { margin-top: 12px; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
@php
    $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2);
@endphp

<div class="top">
    <h1>Bank Statement</h1>
    <div class="muted">Generated on {{ now()->format('d M Y, h:i A') }}</div>
</div>

<table class="grid">
    <tr>
        <td><div class="label">Account Name</div><div class="value">{{ $bankAccount->name }}</div></td>
        <td><div class="label">Bank Name</div><div class="value">{{ $bankAccount->bank_name ?: ucfirst($bankAccount->type) }}</div></td>
        <td><div class="label">Account Number</div><div class="value">{{ $bankAccount->account_number ?: '-' }}</div></td>
        <td><div class="label">Status</div><div class="value">{{ ucfirst($bankAccount->status) }}</div></td>
    </tr>
    <tr>
        <td><div class="label">Opening Balance</div><div class="value">{{ $money($bankAccount->opening_balance) }}</div></td>
        <td><div class="label">Opening Date</div><div class="value">{{ $bankAccount->opening_balance_date?->format('d M Y') ?: '-' }}</div></td>
        <td><div class="label">Current Balance</div><div class="value">{{ $money($bankAccount->current_balance) }}</div></td>
        <td><div class="label">Created By</div><div class="value">{{ $bankAccount->creator?->name ?: '-' }}</div></td>
    </tr>
    <tr>
        <td><div class="label">Total Credit</div><div class="value credit">{{ $money($summary['total_credit']) }}</div></td>
        <td><div class="label">Total Debit</div><div class="value debit">{{ $money($summary['total_debit']) }}</div></td>
        <td><div class="label">Net Movement</div><div class="value">{{ $money($summary['net_movement']) }}</div></td>
        <td><div class="label">Transactions</div><div class="value">{{ $transactions->count() }}</div></td>
    </tr>
</table>

@if($bankAccount->notes)
    <h2>Notes</h2>
    <div>{{ $bankAccount->notes }}</div>
@endif

<h2>Transactions</h2>
<table class="statement">
    <thead>
        <tr>
            <th>Date</th>
            <th>Txn No.</th>
            <th>Party</th>
            <th>Description</th>
            <th>Reference</th>
            <th>Category</th>
            <th class="right">Debit</th>
            <th class="right">Credit</th>
            <th class="right">Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $txn)
            <tr>
                <td>{{ $txn->transaction_date?->format('d M Y') }}</td>
                <td class="small">{{ $txn->transaction_no }}</td>
                <td>{{ $txn->party_name ?: '-' }}</td>
                <td>{{ $txn->description ?: '-' }}</td>
                <td>{{ $txn->reference_no ?: '-' }}</td>
                <td>{{ $txn->category ?: '-' }}</td>
                <td class="right debit">{{ $txn->direction === 'debit' ? $money($txn->amount) : '-' }}</td>
                <td class="right credit">{{ $txn->direction === 'credit' ? $money($txn->amount) : '-' }}</td>
                <td class="right balance">{{ $money($txn->display_balance_after ?? $txn->balance_after) }}</td>
            </tr>
        @empty
            <tr><td colspan="9" style="text-align:center;">No transactions found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Transactions are ordered by transaction date and posting order so debit, credit, and running balance match the bank statement flow.
</div>
</body>
</html>
