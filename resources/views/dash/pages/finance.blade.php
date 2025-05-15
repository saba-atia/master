@extends('dash.dash')
@section('title', 'Financial Transactions')
@section('contentdash')

<style>
    .transaction-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .transaction-table {
        width: 100%;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-paid {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .status-pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .status-rejected {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .amount-income {
        color: #28a745;
        font-weight: 600;
    }
    
    .amount-expense {
        color: #dc3545;
        font-weight: 600;
    }
    
    .action-btns .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .no-transactions {
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background: #f8f9fa;
        border-radius: 12px;
    }
    
    .transaction-type-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 12px;
    }
    
    .type-salary { background-color: #e3f2fd; color: #1976d2; }
    .type-expense { background-color: #ffebee; color: #d32f2f; }
    .type-bonus { background-color: #e8f5e9; color: #388e3c; }
    .type-advance { background-color: #fff3e0; color: #ffa000; }
    .type-other { background-color: #f3e5f5; color: #7b1fa2; }
    
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        
        .transaction-table thead {
            display: none;
        }
        
        .transaction-table tr {
            display: block;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .transaction-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }
        
        .transaction-table td::before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
            color: #6c757d;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 transaction-card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Financial Transactions</h6>
                        @can('manage-transactions')
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                <i class="fas fa-plus me-1"></i> Add Transaction
                            </button>
                        @endcan
                    </div>
                    <p class="text-sm mb-0">View all financial transactions including salaries, expenses, and other payments</p>
                </div>
                
                <div class="card-body px-0 pt-0 pb-2">
                    @if($transactions->count() > 0)
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 transaction-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Employee</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        @can('manage-transactions')
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td data-label="ID" class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">#{{ $transaction->id }}</p>
                                        </td>
                                        <td data-label="Employee">
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ $transaction->employee->photo_url ?? asset('assets/img/default-avatar.png') }}" 
                                                         class="avatar avatar-sm me-3" alt="user">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $transaction->employee->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $transaction->employee->department }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Type">
                                            <div class="d-flex align-items-center">
                                                <div class="transaction-type-icon type-{{ $transaction->type }}">
                                                    @if($transaction->type == 'salary')
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    @elseif($transaction->type == 'expense')
                                                        <i class="fas fa-receipt"></i>
                                                    @elseif($transaction->type == 'bonus')
                                                        <i class="fas fa-gift"></i>
                                                    @elseif($transaction->type == 'advance')
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    @else
                                                        <i class="fas fa-exchange-alt"></i>
                                                    @endif
                                                </div>
                                                <span>{{ ucfirst($transaction->type) }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Amount">
                                            <p class="text-xs font-weight-bold mb-0 
                                                {{ $transaction->amount >= 0 ? 'amount-income' : 'amount-expense' }}">
                                                {{ number_format($transaction->amount, 2) }} {{ config('app.currency', 'SAR') }}
                                            </p>
                                        </td>
                                        <td data-label="Date">
                                            <p class="text-xs font-weight-bold mb-0">{{ $transaction->date->format('d M Y') }}</p>
                                        </td>
                                        <td data-label="Status">
                                            @if($transaction->status == 'paid')
                                                <span class="status-badge status-paid">
                                                    <i class="fas fa-check-circle me-1"></i> Paid
                                                </span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @else
                                                <span class="status-badge status-rejected">
                                                    <i class="fas fa-times-circle me-1"></i> Rejected
                                                </span>
                                            @endif
                                        </td>
                                        @can('manage-transactions')
                                            <td data-label="Actions" class="align-middle text-center action-btns">
                                                <button class="btn btn-sm btn-outline-info me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editTransactionModal"
                                                    data-id="{{ $transaction->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $transaction->id }})">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        @endcan
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 pt-3">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="no-transactions py-5">
                            <i class="fas fa-wallet fa-3x text-secondary mb-3"></i>
                            <h5 class="mb-2">No Transactions</h5>
                            <p class="text-sm text-secondary mb-0">There are no financial transactions to display at this time</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@can('manage-transactions')
<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee</label>
                                <select class="form-select" name="employee_id" required>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->department }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Transaction Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="salary">Salary</option>
                                    <option value="expense">Expense</option>
                                    <option value="bonus">Bonus</option>
                                    <option value="advance">Advance</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                    <input type="number" class="form-control" name="amount" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTransactionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
    // Confirm deletion
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/transactions/${id}`)
                    .then(response => {
                        Swal.fire(
                            'Deleted!',
                            'The transaction has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'An error occurred while trying to delete.',
                            'error'
                        );
                    });
            }
        });
    }

    // Load edit form via AJAX
    $('#editTransactionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var transactionId = button.data('id');
        var modal = $(this);
        
        axios.get(`/transactions/${transactionId}/edit`)
            .then(response => {
                modal.find('.modal-body').html(response.data);
                modal.find('form').attr('action', `/transactions/${transactionId}`);
            })
            .catch(error => {
                modal.find('.modal-body').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load transaction data. Please try again.
                    </div>
                `);
            });
    });
</script>

@endsection