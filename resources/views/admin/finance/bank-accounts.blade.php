@extends('admin.layouts.app')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank & Cash')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Bank & Cash</li>
@endsection

@section('content')

@php
    $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2);

    /*
    |--------------------------------------------------------------------------
    | Pagination Variables
    |--------------------------------------------------------------------------
    */
    $currentPage = $bankAccounts->currentPage();
    $lastPage = $bankAccounts->lastPage();
    $totalResults = $bankAccounts->total();
    $perPage = $bankAccounts->perPage();

    $from = $totalResults > 0
        ? (($currentPage - 1) * $perPage) + 1
        : 0;

    $to = min($currentPage * $perPage, $totalResults);
@endphp


{{-- =========================================================
     PAGE CSS
========================================================= --}}
<style>

    /* =========================================================
       BANK ACCOUNT CARD
    ========================================================= */

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


    /* =========================================================
       CUSTOM PAGINATION
       IMPORTANT: NO SVG USED
    ========================================================= */

    .bank-pagination-wrapper {
        width: 100%;
        margin-top: 25px;
        padding-top: 18px;
        border-top: 1px solid #e9ecef;
    }

    .bank-pagination-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 15px;
    }

    .bank-pagination-info {
        color: #6c757d;
        font-size: 14px;
    }

    .bank-pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 5px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .bank-pagination-item {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .bank-pagination-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;

        min-width: 38px !important;
        height: 38px !important;

        padding: 6px 12px !important;

        border: 1px solid #dee2e6 !important;
        border-radius: 4px !important;

        background: #fff !important;
        color: #343a40 !important;

        font-size: 14px !important;
        font-weight: 400 !important;
        line-height: 1 !important;

        text-decoration: none !important;

        box-sizing: border-box !important;

        transition:
            background-color 0.15s ease,
            border-color 0.15s ease,
            color 0.15s ease;
    }

    .bank-pagination-link:hover {
        background: #f8f9fa !important;
        border-color: #adb5bd !important;
        color: #212529 !important;
        text-decoration: none !important;
    }

    .bank-pagination-link.active {
        background: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
        cursor: default;
    }

    .bank-pagination-link.disabled {
        background: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #adb5bd !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }

    /* Previous / Next buttons */
    .bank-pagination-prev,
    .bank-pagination-next {
        min-width: 85px !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
    }

    /*
     * VERY IMPORTANT:
     * Pagination ke andar koi SVG / pseudo arrow nahi chahiye.
     */
    .bank-pagination svg,
    .bank-pagination-wrapper svg {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    .bank-pagination-link::before,
    .bank-pagination-link::after {
        content: none !important;
        display: none !important;
    }


    /* =========================================================
       MODALS
    ========================================================= */

    .fin-modal .modal-header {
        align-items: center;
    }

    .fin-modal .modal-header h5,
    .fin-modal .modal-title {
        margin-bottom: 0;
    }

    .fin-modal .form-group label {
        font-weight: 500;
    }


    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 767px) {

        .bank-pagination-top {
            display: block;
        }

        .bank-pagination-info {
            margin-bottom: 12px;
        }

        .bank-pagination {
            justify-content: flex-start;
        }

        .bank-pagination-link {
            min-width: 34px !important;
            height: 34px !important;
            padding: 5px 9px !important;
            font-size: 13px !important;
        }

        .bank-pagination-prev,
        .bank-pagination-next {
            min-width: 75px !important;
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

    {{-- =====================================================
         CARD HEADER
    ====================================================== --}}
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


    {{-- =====================================================
         CARD BODY
    ====================================================== --}}
    <div class="card-body">

        <div class="row">

            @forelse($bankAccounts as $account)

                <div class="col-md-6 col-xl-4 mb-3">

                    <div class="card h-100 bank-account-card">

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
             CUSTOM PAGINATION
             NO LARAVEL LINKS()
             NO SVG ARROWS
        ================================================== --}}
        @if($totalResults > 0)

            <div class="bank-pagination-wrapper">

                {{-- PAGINATION INFO --}}
                <div class="bank-pagination-top">

                    <div class="bank-pagination-info">

                        Showing
                        <strong>{{ $from }}</strong>
                        to
                        <strong>{{ $to }}</strong>
                        of
                        <strong>{{ $totalResults }}</strong>
                        results

                    </div>


                    {{-- PAGINATION BUTTONS --}}
                    @if($lastPage > 1)

                        <ul class="bank-pagination">

                            {{-- PREVIOUS --}}
                            <li class="bank-pagination-item">

                                @if($currentPage > 1)

                                    <a
                                        href="{{ $bankAccounts->url($currentPage - 1) }}"
                                        class="bank-pagination-link bank-pagination-prev"
                                    >
                                        Previous
                                    </a>

                                @else

                                    <span
                                        class="bank-pagination-link bank-pagination-prev disabled"
                                    >
                                        Previous
                                    </span>

                                @endif

                            </li>


                            {{-- PAGE NUMBERS --}}
                            @php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp


                            {{-- FIRST PAGE --}}
                            @if($startPage > 1)

                                <li class="bank-pagination-item">

                                    <a
                                        href="{{ $bankAccounts->url(1) }}"
                                        class="bank-pagination-link"
                                    >
                                        1
                                    </a>

                                </li>

                                @if($startPage > 2)

                                    <li class="bank-pagination-item">

                                        <span class="bank-pagination-link disabled">
                                            ...
                                        </span>

                                    </li>

                                @endif

                            @endif


                            {{-- PAGE LOOP --}}
                            @for($page = $startPage; $page <= $endPage; $page++)

                                <li class="bank-pagination-item">

                                    @if($page == $currentPage)

                                        <span
                                            class="bank-pagination-link active"
                                        >
                                            {{ $page }}
                                        </span>

                                    @else

                                        <a
                                            href="{{ $bankAccounts->url($page) }}"
                                            class="bank-pagination-link"
                                        >
                                            {{ $page }}
                                        </a>

                                    @endif

                                </li>

                            @endfor


                            {{-- LAST PAGE --}}
                            @if($endPage < $lastPage)

                                @if($endPage < $lastPage - 1)

                                    <li class="bank-pagination-item">

                                        <span class="bank-pagination-link disabled">
                                            ...
                                        </span>

                                    </li>

                                @endif

                                <li class="bank-pagination-item">

                                    <a
                                        href="{{ $bankAccounts->url($lastPage) }}"
                                        class="bank-pagination-link"
                                    >
                                        {{ $lastPage }}
                                    </a>

                                </li>

                            @endif


                            {{-- NEXT --}}
                            <li class="bank-pagination-item">

                                @if($currentPage < $lastPage)

                                    <a
                                        href="{{ $bankAccounts->url($currentPage + 1) }}"
                                        class="bank-pagination-link bank-pagination-next"
                                    >
                                        Next
                                    </a>

                                @else

                                    <span
                                        class="bank-pagination-link bank-pagination-next disabled"
                                    >
                                        Next
                                    </span>

                                @endif

                            </li>

                        </ul>

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>


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
                        <div class="col-md-6 form-group">

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


                        {{-- OPENING DATE --}}
                        <div class="col-md-6 form-group">

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
