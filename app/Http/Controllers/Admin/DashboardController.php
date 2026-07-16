<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CashflowPlan;
use App\Models\ExpensePayment;
use App\Models\ExpensePlan;
use App\Models\Item;
use App\Models\Ledger;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats['total_users']   = User::count();
            $stats['total_items']   = Item::count();
            $stats['total_roles']   = Role::count();
            $stats['active_users']  = User::where('is_active', true)->count();
        } elseif ($user->isAdmin()) {
            $myUserIds = $user->createdUsers()->pluck('id')->push($user->id);
            $stats['total_users']   = User::whereIn('id', $myUserIds)->count();
            $stats['total_items']   = Item::whereIn('created_by', $myUserIds)->count();
            $stats['total_roles']   = Role::count();
            $stats['active_users']  = User::whereIn('id', $myUserIds)->where('is_active', true)->count();
        } else {
            $stats['total_items']  = Item::where('created_by', $user->id)->count();
            $stats['active_items'] = Item::where('created_by', $user->id)->where('status', 'active')->count();
            $stats['draft_items']  = Item::where('created_by', $user->id)->where('status', 'draft')->count();
        }

        // ── Recent Activity ───────────────────────────────────────────────
        $recentActivity = ActivityLog::with('user')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(8)
            ->get();

        $recentItems = Item::with('creator')
            ->forUser($user)
            ->latest()
            ->take(5)
            ->get();

        // ── Finance core data ─────────────────────────────────────────────
        $bankAccounts  = BankAccount::where('status', 'active')->orderBy('name')->get();
        $ledgers       = Ledger::where('status', 'active')->orderBy('name')->get();
        $expenseLedgers = $ledgers->whereIn('type', ['expense', 'salary', 'vendor', 'other']);
        $incomeLedgers  = $ledgers->whereIn('type', ['income', 'customer', 'other']);
        $chartFrom = request()->date('chart_from');
        $chartTo = request()->date('chart_to');
        $chartStatus = request('chart_status');

        // ── Expense plans (priority-sorted, active statuses) ──────────────
        $expensePlans = ExpensePlan::with(['ledger', 'bankAccount', 'creator.roles'])
            ->when(request()->filled('dash_role'), fn($q) => $q->whereHas('creator.roles', fn($r) => $r->where('name', request('dash_role'))))
            ->when(request()->filled('dash_user_id'), fn($q) => $q->where('created_by', request()->integer('dash_user_id')))
            ->when(request()->filled('dash_from'), fn($q) => $q->whereDate('expense_month', '>=', request()->date('dash_from')))
            ->when(request()->filled('dash_to'), fn($q) => $q->whereDate('expense_month', '<=', request()->date('dash_to')))
            ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->take(15)
            ->get();

        // ── Salary ledger plans grouped per ledger (carry-forward view) ───
        $salaryPlans = ExpensePlan::with('ledger')
            ->whereHas('ledger', fn($q) => $q->where('type', 'salary'))
            ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
            ->orderBy('expense_month')
            ->get()
            ->groupBy(fn($e) => $e->ledger?->name ?? 'Unknown');

        // ── Payments pending approval ─────────────────────────────────────
        $recentPayments = ExpensePayment::with(['expensePlan.ledger', 'bankAccount'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(4, ['*'], 'payments_page')
            ->withQueryString();

        // ── Cashflow plans ────────────────────────────────────────────────
        $cashflowPlans = CashflowPlan::with(['ledger', 'bankAccount'])
            ->whereIn('status', ['submitted', 'draft', 'approved'])
            ->orderBy('expected_date')
            ->paginate(4, ['*'], 'cashflows_page')
            ->withQueryString();

        // ── Finance KPIs ──────────────────────────────────────────────────
        $financeStats = [
            'bank_balance'    => BankAccount::where('status', 'active')->sum('current_balance'),
            'planned_income'  => CashflowPlan::whereIn('status', ['draft', 'submitted', 'approved'])->sum('expected_amount'),
            'planned_expense' => ExpensePlan::whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END), 0) as total')
                ->value('total') ?? 0,
            'outstanding'     => ExpensePlan::whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount), 0) as total')
                ->value('total') ?? 0,
            'salary_due'      => ExpensePlan::whereHas('ledger', fn($q) => $q->where('type', 'salary'))
                ->whereIn('status', ['submitted', 'approved', 'partial', 'deferred'])
                ->selectRaw('COALESCE(SUM((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount), 0) as total')
                ->value('total') ?? 0,
            'unreconciled_count' => BankTransaction::where('reconciliation_status', 'unreconciled')->count(),
        ];

        // ── Monthly expense chart (last 6 months) ────────────────────────
        $monthlyExpense = ExpensePlan::selectRaw(
                "DATE_FORMAT(expense_month, '%Y-%m') as month_key, SUM(CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) as total"
            )
            ->whereNotNull('expense_month')
            ->when($chartFrom, fn($q) => $q->whereDate('expense_month', '>=', $chartFrom))
            ->when($chartTo, fn($q) => $q->whereDate('expense_month', '<=', $chartTo))
            ->when($chartStatus, fn($q) => $q->where('status', $chartStatus))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->take(6)
            ->pluck('total', 'month_key');

        // ── Monthly cashflow chart ────────────────────────────────────────
        $monthlyCashflow = CashflowPlan::selectRaw(
                "DATE_FORMAT(expected_date, '%Y-%m') as month_key, SUM(expected_amount) as total"
            )
            ->whereNotNull('expected_date')
            ->when($chartFrom, fn($q) => $q->whereDate('expected_date', '>=', $chartFrom))
            ->when($chartTo, fn($q) => $q->whereDate('expected_date', '<=', $chartTo))
            ->when($chartStatus, fn($q) => $q->where('status', $chartStatus))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->take(6)
            ->pluck('total', 'month_key');

        // ── Expense by status (pie chart) ─────────────────────────────────
        $expenseByStatus = ExpensePlan::selectRaw('status, COUNT(*) as total')
            ->when($chartFrom, fn($q) => $q->whereDate('expense_month', '>=', $chartFrom))
            ->when($chartTo, fn($q) => $q->whereDate('expense_month', '<=', $chartTo))
            ->when($chartStatus, fn($q) => $q->where('status', $chartStatus))
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Expenses the current bank balance can cover ───────────────────
        $affordableExpenses = ExpensePlan::with('ledger')
            ->whereIn('status', ['approved', 'partial'])
            ->whereRaw(
                '((CASE WHEN net_amount > 0 THEN net_amount ELSE planned_amount END) - paid_amount) <= ?',
                [max(0, $financeStats['bank_balance'])]
            )
            ->orderBy('due_date')
            ->take(6)
            ->get();

        // ── Recent bank transactions ──────────────────────────────────────
        $recentTransactions = BankTransaction::with('bankAccount')
            ->latest('transaction_date')
            ->latest()
            ->paginate(4, ['*'], 'transactions_page')
            ->withQueryString();

        // ── Cashflows approved but not yet received ───────────────────────
        $awaitingReceipts = CashflowPlan::with(['ledger', 'bankAccount'])
            ->where('status', 'approved')
            ->orderBy('expected_date')
            ->paginate(3, ['*'], 'receipts_page')
            ->withQueryString();

        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->pluck('name');
        $isReport = $request->input('view') === 'report';
        $reportData = $this->buildDashboardReport($request, $ledgers);

        return view('admin.dashboard.index', array_merge(compact(
            'stats',
            'recentActivity',
            'recentItems',
            'bankAccounts',
            'ledgers',
            'expenseLedgers',
            'incomeLedgers',
            'expensePlans',
            'salaryPlans',
            'recentPayments',
            'cashflowPlans',
            'financeStats',
            'monthlyExpense',
            'monthlyCashflow',
            'expenseByStatus',
            'affordableExpenses',
            'recentTransactions',
            'awaitingReceipts',
            'users',
            'roles'
        ), $reportData, [
            'isReport' => $isReport,
        ]));
    }

    private function buildDashboardReport(Request $request, $ledgers): array
    {
        $reportType = $request->input('report_type', 'all');
        $reportType = in_array($reportType, ['all', 'expense', 'cash_in'], true) ? $reportType : 'all';
        $ledgerId = $request->integer('report_ledger_id');
        $period = $request->input('report_period', 'this_month');
        $period = in_array($period, ['today', 'yesterday', 'this_week', 'this_month', '3_month', '6_month', '9_month', 'this_year', 'custom'], true)
            ? $period
            : 'this_month';

        [$from, $to, $periodLabel] = $this->resolveReportPeriod($request, $period);

        $baseQuery = BankTransaction::query()
            ->with([
                'bankAccount',
                'creator',
                'transactionable' => function ($morphTo) {
                    $morphTo->morphWith([
                        ExpensePayment::class => ['expensePlan.ledger', 'bankAccount'],
                        CashflowPlan::class => ['ledger', 'bankAccount'],
                    ]);
                },
            ])
            ->whereDate('transaction_date', '>=', $from)
            ->whereDate('transaction_date', '<=', $to)
            ->whereHasMorph(
                'transactionable',
                [ExpensePayment::class, CashflowPlan::class],
                function ($query, string $type) use ($ledgerId) {
                    if ($type === ExpensePayment::class) {
                        $query->whereHas('expensePlan', function ($planQuery) use ($ledgerId) {
                            if ($ledgerId) {
                                $planQuery->where('ledger_id', $ledgerId);
                            }
                        });
                        return;
                    }

                    if ($ledgerId) {
                        $query->where('ledger_id', $ledgerId);
                    }
                }
            );

        if ($reportType === 'expense') {
            $baseQuery->where('transactionable_type', ExpensePayment::class);
        } elseif ($reportType === 'cash_in') {
            $baseQuery->where('transactionable_type', CashflowPlan::class);
        }

        $transactions = $baseQuery
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        $rows = $transactions->map(fn ($txn) => $this->mapDashboardReportRow($txn))->values();

        $totalExpense = (float) $rows->where('kind', 'expense')->sum('amount');
        $totalCashIn = (float) $rows->where('kind', 'cash_in')->sum('amount');
        $netMovement = $totalCashIn - $totalExpense;

        $summaryCards = [
            'transactions' => $rows->count(),
            'expense_total' => $totalExpense,
            'cashin_total' => $totalCashIn,
            'net_total' => $netMovement,
            'expense_count' => $rows->where('kind', 'expense')->count(),
            'cashin_count' => $rows->where('kind', 'cash_in')->count(),
        ];

        $byDate = $rows->groupBy('date_key')->sortKeys();
        $dateLabels = $byDate->keys()->values();
        $dailyExpense = $dateLabels->map(fn ($date) => (float) ($byDate[$date]->where('kind', 'expense')->sum('amount')));
        $dailyCashIn = $dateLabels->map(fn ($date) => (float) ($byDate[$date]->where('kind', 'cash_in')->sum('amount')));

        $pieLabels = ['Expense', 'Cash In'];
        $pieValues = [(float) $totalExpense, (float) $totalCashIn];

        $candleSeries = [];
        $runningBalance = 0.0;
        foreach ($dateLabels as $dateKey) {
            $dayRows = $byDate[$dateKey]->sortBy('sort_key')->values();
            $open = $runningBalance;
            $high = $runningBalance;
            $low = $runningBalance;
            foreach ($dayRows as $row) {
                $runningBalance += $row['signed_amount'];
                $high = max($high, $runningBalance);
                $low = min($low, $runningBalance);
            }
            $close = $runningBalance;
            $candleSeries[] = [
                'x' => $dateKey,
                'y' => [round($open, 2), round($high, 2), round($low, 2), round($close, 2)],
            ];
        }

        $statusCounts = $rows->groupBy('status_key')->map->count();
        $radarLabels = ['Draft', 'Submitted', 'Approved', 'Partial', 'Paid / Received', 'Rejected'];
        $radarValues = [
            (int) ($statusCounts['draft'] ?? 0),
            (int) ($statusCounts['submitted'] ?? 0),
            (int) ($statusCounts['approved'] ?? 0),
            (int) ($statusCounts['partial'] ?? 0),
            (int) (($statusCounts['paid'] ?? 0) + ($statusCounts['received'] ?? 0)),
            (int) ($statusCounts['rejected'] ?? 0),
        ];

        $topLedgers = $rows->groupBy('ledger_name')
            ->map(fn ($group, $name) => [
                'name' => $name,
                'amount' => (float) $group->sum('amount'),
                'count' => $group->count(),
            ])
            ->sortByDesc('amount')
            ->take(6)
            ->values();

        return [
            'reportMode' => true,
            'reportPeriod' => $period,
            'reportPeriodLabel' => $periodLabel,
            'reportType' => $reportType,
            'reportLedgerId' => $ledgerId,
            'reportLedger' => $ledgerId ? $ledgers->firstWhere('id', $ledgerId) : null,
            'reportFrom' => $from->toDateString(),
            'reportTo' => $to->toDateString(),
            'reportRows' => $rows,
            'reportTransactions' => $transactions,
            'reportSummary' => $summaryCards,
            'reportFilters' => [
                'type' => $reportType,
                'period' => $period,
                'ledger_id' => $ledgerId,
            ],
            'reportChartData' => [
                'pie' => [
                    'labels' => $pieLabels,
                    'values' => $pieValues,
                ],
                'wave' => [
                    'labels' => $dateLabels->map(fn ($date) => Carbon::parse($date)->format('d M'))->values(),
                    'expense' => $dailyExpense->values(),
                    'cash_in' => $dailyCashIn->values(),
                ],
                'candle' => [
                    'series' => $candleSeries,
                ],
                'radar' => [
                    'labels' => $radarLabels,
                    'values' => $radarValues,
                ],
                'topLedgers' => $topLedgers,
            ],
        ];
    }

    private function resolveReportPeriod(Request $request, string $period): array
    {
        $now = Carbon::now();

        if ($period === 'custom') {
            $customFrom = Carbon::parse($request->input('report_from', $now->copy()->startOfMonth()->toDateString()))->startOfDay();
            $customTo = Carbon::parse($request->input('report_to', $now->copy()->toDateString()))->endOfDay();

            if ($customFrom->gt($customTo)) {
                [$customFrom, $customTo] = [$customTo, $customFrom];
            }

            return [$customFrom, $customTo, 'Custom Range'];
        }

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Today'],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(), 'Yesterday'],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfDay(), 'This Week'],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay(), 'This Month'],
            '3_month' => [$now->copy()->subMonthsNoOverflow(2)->startOfMonth(), $now->copy()->endOfDay(), 'Last 3 Months'],
            '6_month' => [$now->copy()->subMonthsNoOverflow(5)->startOfMonth(), $now->copy()->endOfDay(), 'Last 6 Months'],
            '9_month' => [$now->copy()->subMonthsNoOverflow(8)->startOfMonth(), $now->copy()->endOfDay(), 'Last 9 Months'],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfDay(), 'This Year'],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfDay(), 'This Month'],
        };
    }

    private function mapDashboardReportRow(BankTransaction $txn): array
    {
        $kind = 'other';
        $title = $txn->description ?: $txn->party_name ?: 'Transaction';
        $ledgerName = $txn->category ?: '-';
        $status = $txn->reconciliation_status ?: 'unreconciled';
        $statusKey = 'draft';

        if ($txn->transactionable_type === ExpensePayment::class && $txn->transactionable) {
            $kind = 'expense';
            $payment = $txn->transactionable;
            $title = $payment->expensePlan?->title ?: $title;
            $ledgerName = $payment->expensePlan?->ledger?->name ?: 'Expense';
            $status = $payment->status ?: $status;
            $statusKey = $payment->status ?: 'draft';
        } elseif ($txn->transactionable_type === CashflowPlan::class && $txn->transactionable) {
            $kind = 'cash_in';
            $plan = $txn->transactionable;
            $title = $plan->title ?: $title;
            $ledgerName = $plan->ledger?->name ?: 'Cash In';
            $status = $plan->status ?: $status;
            $statusKey = $plan->status ?: 'draft';
        }

        return [
            'id' => $txn->id,
            'kind' => $kind,
            'title' => $title,
            'ledger_name' => $ledgerName,
            'bank_name' => $txn->bankAccount?->name ?: '-',
            'direction' => $txn->direction,
            'direction_label' => $txn->direction === 'credit' ? 'Cash In' : 'Expense',
            'amount' => (float) $txn->amount,
            'signed_amount' => $txn->direction === 'credit' ? (float) $txn->amount : -(float) $txn->amount,
            'balance_after' => (float) $txn->balance_after,
            'status' => $status,
            'status_key' => strtolower((string) $statusKey),
            'date' => optional($txn->transaction_date)->format('d M Y'),
            'date_key' => optional($txn->transaction_date)->toDateString(),
            'sort_key' => $txn->transaction_date?->timestamp ?? $txn->id,
            'reference_no' => $txn->reference_no ?: '-',
            'party_name' => $txn->party_name ?: '-',
            'description' => $txn->description ?: '-',
            'reconciliation_status' => $txn->reconciliation_status ?: 'unreconciled',
            'created_by' => $txn->creator?->name ?: '-',
        ];
    }
}
