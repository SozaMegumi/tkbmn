@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft {
        border: none;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        overflow: hidden;
    }
    .card-soft:hover { transform: translateY(-3px); }

    /* Beautiful Gradients */
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); color: white; }
    .bg-gradient-danger  { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%); color: white; }

    /* Stats & Text */
    .stat-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
    .display-amount { font-size: 2.5rem; font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    
    /* Custom Action Buttons */
    .btn-action {
        border-radius: 50px; padding: 10px 24px; font-weight: 700; border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .btn-action:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }

    /* Modern Table */
    .table-custom thead th {
        background: #f8f9fa; color: #8898aa; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 1px; padding: 15px; border: none;
    }
    .table-custom tbody td {
        padding: 16px 15px; vertical-align: middle; 
        border-bottom: 1px solid #f0f0f0; font-size: 0.95rem;
    }
    .icon-box {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-right: 15px;
    }
</style>

<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Financial Management</h3>
            <p class="text-muted small mb-0">Overview of Cash Flow & Budget.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-action text-white" data-bs-toggle="modal" data-bs-target="#incomeModal">
                <i class="bi bi-arrow-down-circle-fill fs-5"></i> <span>Record Income</span>
            </button>
            <button class="btn btn-danger btn-action text-white" data-bs-toggle="modal" data-bs-target="#expenseModal">
                <i class="bi bi-arrow-up-circle-fill fs-5"></i> <span>Record Expense</span>
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card card-soft bg-gradient-primary h-100 p-4">
                <div class="d-flex flex-column h-100 justify-content-center position-relative">
                    <span class="stat-label mb-2"><i class="bi bi-wallet2 me-2"></i> Current School Budget</span>
                    <h1 class="display-amount mb-0">RM {{ number_format($currentBalance ?? 0.00, 2) }}</h1>
                    <p class="mt-2 mb-0 opacity-75 small"><i class="bi bi-check-circle me-1"></i> Funds available for use.</p>
                    <i class="bi bi-cash-stack position-absolute text-white" style="font-size: 10rem; opacity: 0.1; right: -20px; bottom: -30px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-soft h-100 bg-white">
                <div class="card-body p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mb-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <span class="text-muted small text-uppercase fw-bold">Total Income</span>
                    <h3 class="fw-bold text-dark mt-1">RM {{ number_format($totalIncome ?? 0.00, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card card-soft h-100 bg-white">
                <div class="card-body p-4">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger mb-3">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                    <span class="text-muted small text-uppercase fw-bold">Total Expenses</span>
                    <h3 class="fw-bold text-dark mt-1">RM {{ number_format($totalExpense ?? 0.00, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft bg-white mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10 p-4 border-0">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2"></i> Pending Parent Payments</h5>
            <small class="text-muted">Verify receipts before approving income into finance.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="ps-4">Student</th>
                        <th>Amount (RM)</th>
                        <th>Receipt Reference</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments ?? [] as $p)
                    <tr>
                        <td class="ps-4 font-weight-bold">{{ $p->student->student_name }}</td>
                        <td>RM {{ number_format($p->amount, 2) }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $p->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-image"></i> View Receipt
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.finance.approve', $p->payment_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Approve</button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{$p->payment_id}}">Reject</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted small">No pending parent payments to verify.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card card-soft h-100 bg-white">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Cash Flow Overview</h5>
                        <small class="text-muted">Income vs Expense tracking</small>
                    </div>
                    <select id="timeframeFilter" class="form-select form-select-sm w-auto border-0 bg-light text-muted fw-bold" onchange="updateChartData()">
                        <option value="6months">Last 6 Months</option>
                        <option value="thisyear">This Year</option>
                    </select>
                </div>
                <div class="card-body p-4 pt-0">
                    <canvas id="cashFlowChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft h-100 bg-white">
                <div class="card-header bg-white p-4 border-0">
                    <h5 class="fw-bold mb-0 text-dark">Expense Breakdown</h5>
                    <small class="text-muted">Current Month Spending</small>
                </div>
                <div class="card-body p-4 pt-0 d-flex justify-content-center align-items-center">
                    <canvas id="expenseChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft bg-white">
        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Transaction History</h5>
            <span class="badge bg-light text-secondary border">{{ count($transactions ?? []) }} Records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Description / Category</th>
                        <th>Method</th>
                        <th class="text-end pe-4">Amount (RM)</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions ?? [] as $t)
                    <tr>
                        <td class="ps-4 text-muted font-monospace">{{ $t->date->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-box {{ $t->type == 'income' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" 
                                     style="width: 35px; height: 35px; font-size: 1rem;">
                                    <i class="bi {{ $t->type == 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold text-dark">{{ $t->description ?? '-' }}</span>
                                    <span class="badge bg-light text-secondary border fw-normal">{{ $t->category }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted"><i class="bi bi-credit-card me-1"></i> {{ $t->payment_method }}</small>
                        </td>
                        <td class="text-end fw-bold {{ $t->type == 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $t->type == 'income' ? '+' : '-' }} RM {{ number_format($t->amount, 2) }}
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.finance.delete', $t->transaction_id ?? $t->id) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-link text-muted hover-danger p-0" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt-cutoff fs-1 opacity-50"></i>
                            <p class="mt-2 mb-0">No transactions recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="incomeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="{{ route('admin.finance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="income">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-wallet-fill me-2"></i> Record Income</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Category</label>
                    <select name="category" class="form-select border-0 shadow-sm py-2">
                        <option value="School Fees">Monthly School Fees</option>
                        <option value="Registration Fee">Registration Fee</option>
                        <option value="Uniform/Books">Uniform & Books Sales</option>
                        <option value="Donation">Donation / Grant</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Description (Who Paid?)</label>
                    <input type="text" name="description" class="form-control border-0 shadow-sm py-2" placeholder="e.g. Ali Bin Abu (Jan Fee)">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Amount (RM)</label>
                        <input type="number" step="0.01" name="amount" class="form-control border-0 shadow-sm py-2 text-success fw-bold" placeholder="0.00" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Date</label>
                        <input type="date" name="date" class="form-control border-0 shadow-sm py-2" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Payment Method</label>
                    <select name="payment_method" class="form-select border-0 shadow-sm py-2">
                        <option value="Cash">Cash Handover</option>
                        <option value="Online Transfer">Online Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill py-2 shadow">Save Income Record</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="{{ route('admin.finance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="expense">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-cart-fill me-2"></i> Record Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Category</label>
                    <select name="category" class="form-select border-0 shadow-sm py-2">
                        <option value="Food & Kitchen">Kitchen Supplies (Rice, etc.)</option>
                        <option value="Stationary">Classroom Stationary</option>
                        <option value="Maintenance">Repairs & Maintenance</option>
                        <option value="Utility Bills">Utilities (Electric/Water)</option>
                        <option value="Salary">Staff Salary</option>
                        <option value="Event Cost">Event Expenses</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Description (Details)</label>
                    <input type="text" name="description" class="form-control border-0 shadow-sm py-2" placeholder="e.g. Bought 10kg Rice">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Amount (RM)</label>
                        <input type="number" step="0.01" name="amount" class="form-control border-0 shadow-sm py-2 text-danger fw-bold" placeholder="0.00" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Date</label>
                        <input type="date" name="date" class="form-control border-0 shadow-sm py-2" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Payment Method</label>
                    <select name="payment_method" class="form-select border-0 shadow-sm py-2">
                        <option value="Cash">Cash</option>
                        <option value="Online Transfer">Online Transfer</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill py-2 shadow">Save Expense Record</button>
            </div>
        </form>
    </div>
</div>

@foreach($pendingPayments ?? [] as $p)
<div class="modal fade" id="rejectModal{{$p->payment_id}}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="{{ route('admin.finance.reject', $p->payment_id) }}" method="POST">
            @csrf
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Reject Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="small fw-bold">Reason for Rejection</label>
                <textarea name="remarks" class="form-control" placeholder="e.g. Blurry receipt, incorrect amount" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger w-100 rounded-pill">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

let cashFlowChartInstance = null;

async function updateChartData() {
    const timeframe = document.getElementById('timeframeFilter').value;
    
    // Fetch real data from the controller via AJAX
    const response = await fetch(`{{ route('admin.finance.chart-data') }}?timeframe=${timeframe}`);
    const data = await response.json();

    if (cashFlowChartInstance) {
        cashFlowChartInstance.destroy(); // Clean old chart before re-drawing
    }

    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    cashFlowChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Income (RM)',
                    data: data.income,
                    backgroundColor: 'rgba(25, 135, 84, 0.8)',
                    borderRadius: 4
                },
                {
                    label: 'Expenses (RM)',
                    data: data.expense,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } }
            }
        }
    });
}

// Load default data on page startup
document.addEventListener('DOMContentLoaded', updateChartData);
</script>
@endsection