@extends('admin.layouts.app')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank & Cash')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Bank & Cash</li>
@endsection

@section('content')

@php
    $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2);
@endphp

{{-- =========================================================
     PAGE CSS
========================================================= --}}
<style>
    /* ==============================
       Pagination / DataTable Arrow Fix
    ============================== */

    .bank-pagination {
        width: 100%;
        margin-top: 20px;
        overflow: hidden;
    }

    .bank-pagination nav {
        width: 100%;
    }

    .bank-pagination .pagination {
        display: flex !important;
        flex-wrap: wrap;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .bank-pagination .page-item {
        margin: 0 !important;
    }

    .bank-pagination .page-link {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        min-width: 38px !important;
        width: auto !important;
        height: 38px !important;

        padding: 6px 12px !important;
        margin: 0 !important;

        font-size: 14px !important;
        line-height: 1 !important;

        box-sizing: border-box !important;
    }

    /*
     * IMPORTANT:
     * Laravel pagination SVG arrows ko global CSS
     * bahut bada kar raha tha.
     */
    .bank-pagination svg {
        width: 16px !important;
        height: 16px !important;

        min-width: 16px !important;
        min-height: 16px !important;

        max-width: 16px !important;
        max-height: 16px !important;

        display: inline-block !important;
        vertical-align: middle !important;

        position: static !important;
        transform: none !important;
    }

    .bank-pagination a svg,
    .bank-pagination span svg,
    .bank-pagination button svg {
        width: 16px !important;
        height: 16px !important;

        min-width: 16px !important;
        min-height: 16px !important;

        max-width: 16px !important;
        max-height: 16px !important;
    }

    /* Laravel Tailwind pagination compatibility */
    .bank-pagination nav[role="navigation"] {
        display: block !important;
    }

    .bank-pagination nav[role="navigation"] svg {
        width: 16px !important;
        height: 16px !important;

        min-width: 16px !important;
        min-height: 16px !important;

        max-width: 16px !important;
        max-height: 16px !important;
    }

    /* Remove unwanted huge line-height */
    .bank-pagination svg {
        line-height: 1 !important;
    }

    /* ==============================
       Bank Account Cards
    ============================== */

    .bank-account-card {
        border-left: 4px solid #0f766e !important;
        transition: all 0.2s ease;
    }

    .bank-account-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .bank-account-card .card-body {
        padding: 20px;
    }

    .bank-account-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .bank-account-info {
        font-size: 13px;
        color: #6c757d;
    }

    .bank-account-balance {
        font-size: 24px;
        font-weight: 600;
        line-height: 1.2;
    }

    /* ==============================
       Modal
    ============================== */

    .fin-modal .modal-header {
        align-items: center;
    }

    .fin-modal .modal-header h5 {
        margin-bottom: 0;
    }

    .fin-modal .form-group label {
        font-weight: 500;
    }

    /* Prevent global SVG CSS from affecting modal icons */
    .fin-modal svg {
        max-width: 16px;
        max-height: 16px;
    }

    /* ==============================
       Mobile Pagination
    ============================== */

    @media (max-width: 576px) {

        .bank-pagination .page-link {
            min-width: 34px !important;
            height: 34px !important;
            padding: 5px 9px !important;
            font-size: 13px !important;
        }

        .bank-pagination svg,
        .bank-pagination a svg,
        .bank-pagination span svg {
            width: 14px !important;
            height: 14px !important;

            min-width: 14px !important;
            min-height: 14px !important;

            max-width: 14px !important;
            max-height: 14px !important;
        }

        .bank-account-balance {
            font-size: 21px;
        }
    }
</style>


{{-- =========================================================
     MAIN CARD
========================================================= --}}
<div class="card">

    {{-- CARD HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">

        <h3 class="mb-0">
            <i class="fas fa-building-columns mr-2 text-success"></i>
            Bank & Cash Accounts
        </h3>

        @can('finance.bank.create')
            <button
                type="button"
                class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#bankModal"
            >
                <i class="fas fa-plus mr-1"></i>
                New Account
            </button>
        @endcan

    </div>


    {{-- CARD BODY --}}
    <div class="card-body">

        <div class="row">

            @forelse($bankAccounts as $account)

                <div class="col-md-6 col-xl-4 mb-3">

                    <div
                        class="card h-100 bank-account-card"
                    >

                        <div class="card-body">

                            {{-- ACCOUNT NAME + STATUS --}}
                            <div class="d-flex justify-content-between align-items-start">

                                <h5 class="bank-account-name">
                                    {{ $account->name }}
                                </h5>

                                <span
                                    class="badge badge-{{ $account->status === 'active' ? 'success' : 'secondary' }}"
                                >
                                    {{ ucfirst($account->status) }}
                                </span>

                            </div>


                            {{-- BANK / ACCOUNT NUMBER --}}
                            <div class="bank-account-info mb-3">

                                {{ $account->bank_name ?: ucfirst($account->type) }}

                                <span class="mx-1">·</span>

                                {{ $account->account_number ?: 'No account number' }}

                            </div>


                            {{-- CURRENT BALANCE --}}
                            <div class="bank-account-balance mb-1">
                                {{ $money($account->current_balance) }}
                            </div>


                            {{-- OPENING BALANCE --}}
                            <div class="text-muted small">
                                Opening:
                                {{ $money($account->opening_balance) }}
                            </div>


                            {{-- ACTION BUTTONS --}}
                            <div class="mt-3">

                                @can('finance.bank.show')

                                    <a
                                        href="{{ route('admin.finance.bank-accounts.show', $account) }}"
                                        class="btn btn-sm btn-outline-dark"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>

                                @endcan


                                @can('finance.bank.edit')

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-toggle="modal"
                                        data-target="#editBank{{ $account->id }}"
                                    >
                                        <i class="fas fa-pen"></i>
                                        Edit
                                    </button>

                                @endcan


                                @can('finance.bank.delete')

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-toggle="modal"
                                        data-target="#deleteBank{{ $account->id }}"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>

                                @endcan

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="text-center text-muted py-5">

                        <i class="fas fa-building-columns fa-3x mb-3 d-block"></i>

                        <div>
                            No accounts found.
                        </div>

                    </div>

                </div>

            @endforelse

        </div>


        {{-- =================================================
             PAGINATION
        ================================================== --}}
        @if($bankAccounts->hasPages())

            <div class="bank-pagination">

                {{ $bankAccounts->onEachSide(1)->links('pagination::bootstrap-4') }}

            </div>

        @endif

    </div>

</div>
<style>
    .bank-pagination .pagination {
    margin-bottom: 0 !important;
}

.bank-pagination .page-link {
    width: auto !important;
    min-width: 38px !important;
    height: 38px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    line-height: 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.bank-pagination .page-link svg {
    width: 16px !important;
    height: 16px !important;
    max-width: 16px !important;
    max-height: 16px !important;
}
</style>

{{-- =========================================================
     CREATE ACCOUNT MODAL
========================================================= --}}
@include('admin.finance.partials.modals')


{{-- =========================================================
     EDIT + DELETE MODALS
========================================================= --}}
@foreach($bankAccounts as $account)

    {{-- =====================================================
         EDIT ACCOUNT MODAL
    ====================================================== --}}
    <div
        class="modal fade fin-modal"
        id="editBank{{ $account->id }}"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <form
                class="modal-content"
                method="POST"
                action="{{ route('admin.finance.bank-accounts.update', $account) }}"
            >

                @csrf
                @method('PUT')


                {{-- MODAL HEADER --}}
                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="fas fa-pen mr-2"></i>
                        Edit Account
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>


                {{-- MODAL BODY --}}
                <div class="modal-body">

                    <div class="row">

                        {{-- ACCOUNT NAME --}}
                        <div class="col-md-6 form-group">

                            <label>
                                Account Name *
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                                value="{{ $account->name }}"
                            >

                        </div>


                        {{-- TYPE --}}
                        <div class="col-md-3 form-group">

                            <label>
                                Type *
                            </label>

                            <select
                                name="type"
                                class="form-control"
                                required
                            >

                                @foreach(['bank', 'cash', 'wallet'] as $type)

                                    <option
                                        value="{{ $type }}"
                                        @selected($account->type === $type)
                                    >
                                        {{ ucfirst($type) }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- STATUS --}}
                        <div class="col-md-3 form-group">

                            <label>
                                Status *
                            </label>

                            <select
                                name="status"
                                class="form-control"
                                required
                            >

                                <option
                                    value="active"
                                    @selected($account->status === 'active')
                                >
                                    Active
                                </option>

                                <option
                                    value="inactive"
                                    @selected($account->status === 'inactive')
                                >
                                    Inactive
                                </option>

                            </select>

                        </div>


                        {{-- BANK NAME --}}
                        <div class="col-md-6 form-group">

                            <label>
                                Bank Name
                            </label>

                            <input
                                type="text"
                                name="bank_name"
                                class="form-control"
                                value="{{ $account->bank_name }}"
                            >

                        </div>


                        {{-- ACCOUNT NUMBER --}}
                        <div class="col-md-6 form-group">

                            <label>
                                Account Number
                            </label>

                            <input
                                type="text"
                                name="account_number"
                                class="form-control"
                                value="{{ $account->account_number }}"
                            >

                        </div>


                        {{-- OPENING BALANCE --}}
                        <div class="col-md-4 form-group">

                            <label>
                                Opening Balance *
                            </label>

                            <input
                                type="number"
                                name="opening_balance"
                                step="0.01"
                                min="0"
                                class="form-control"
                                required
                                value="{{ $account->opening_balance }}"
                            >

                        </div>


                        {{-- CURRENT BALANCE --}}
                        <div class="col-md-4 form-group">

                            <label>
                                Current Balance *
                            </label>

                            <input
                                type="number"
                                name="current_balance"
                                step="0.01"
                                min="0"
                                class="form-control"
                                required
                                value="{{ $account->current_balance }}"
                            >

                        </div>


                        {{-- OPENING DATE --}}
                        <div class="col-md-4 form-group">

                            <label>
                                Opening Date
                            </label>

                            <input
                                type="date"
                                name="opening_balance_date"
                                class="form-control"
                                value="{{ $account->opening_balance_date?->toDateString() }}"
                            >

                        </div>


                        {{-- NOTES --}}
                        <div class="col-12 form-group">

                            <label>
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                rows="2"
                                class="form-control"
                            >{{ $account->notes }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- MODAL FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-save mr-1"></i>
                        Update
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =====================================================
         DELETE ACCOUNT MODAL
    ====================================================== --}}
    <div
        class="modal fade fin-modal"
        id="deleteBank{{ $account->id }}"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <form
                class="modal-content"
                method="POST"
                action="{{ route('admin.finance.bank-accounts.destroy', $account) }}"
            >

                @csrf
                @method('DELETE')


                {{-- MODAL HEADER --}}
                <div class="modal-header bg-danger text-white">

                    <h5 class="modal-title">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Account
                    </h5>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>


                {{-- MODAL BODY --}}
                <div class="modal-body">

                    <p>
                        Delete
                        <strong>{{ $account->name }}</strong>?
                    </p>


                    {{-- KEEP TRANSACTIONS --}}
                    <div class="form-check mb-2">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="transaction_action"
                            value="keep"
                            checked
                            id="bankKeep{{ $account->id }}"
                        >

                        <label
                            class="form-check-label"
                            for="bankKeep{{ $account->id }}"
                        >
                            Transactions rakhna hai
                        </label>

                    </div>


                    {{-- DELETE + REVERT --}}
                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="transaction_action"
                            value="delete_revert"
                            id="bankRevert{{ $account->id }}"
                        >

                        <label
                            class="form-check-label"
                            for="bankRevert{{ $account->id }}"
                        >
                            Transactions delete karke balance revert karna hai
                        </label>

                    </div>

                </div>


                {{-- MODAL FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>

                </div>

            </form>

        </div>

    </div>

@endforeach

@endsection
