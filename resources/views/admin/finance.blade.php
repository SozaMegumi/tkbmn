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
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.financial_management') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.overview_cashflow') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-action text-white" data-bs-toggle="modal" data-bs-target="#incomeModal">
                <i class="bi bi-arrow-down-circle-fill fs-5"></i> <span>{{ __('messages.record_income') }}</span>
            </button>
            <button class="btn btn-danger btn-action text-white" data-bs-toggle="modal" data-bs-target="#expenseModal">
                <i class="bi bi-arrow-up-circle-fill fs-5"></i> <span>{{ __('messages.record_expense') }}</span>
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card card-soft bg-gradient-primary h-100 p-4">
                <div class="d-flex flex-column h-100 justify-content-center position-relative">
                    <span class="stat-label mb-2"><i class="bi bi-wallet2 me-2"></i> {{ __('messages.current_budget') }}</span>
                    
                    @if(isset($currentBalance) && $currentBalance < 0)
                        <h1 class="display-amount mb-0">-RM {{ number_format(abs($currentBalance), 2) }}</h1>
                    @else
                        <h1 class="display-amount mb-0">RM {{ number_format($currentBalance ?? 0.00, 2) }}</h1>
                    @endif
                    
                    <p class="mt-2 mb-0 opacity-75 small"><i class="bi bi-check-circle me-1"></i> {{ __('messages.funds_available') }}</p>
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
                    <span class="text-muted small text-uppercase fw-bold">{{ __('messages.total_income') }}</span>
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
                    <span class="text-muted small text-uppercase fw-bold">{{ __('messages.total_expenses') }}</span>
                    <h3 class="fw-bold text-dark mt-1">RM {{ number_format($totalExpense ?? 0.00, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card card-soft h-100 bg-white">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('messages.cash_flow_overview') }}</h5>
                        <small class="text-muted">{{ __('messages.income_vs_expense') }}</small>
                    </div>
                    <select id="timeframeFilter" class="form-select form-select-sm w-auto border-0 bg-light text-muted fw-bold" onchange="updateChartData()">
                        <option value="6months">{{ __('messages.last_6_months') }}</option>
                        <option value="thisyear">{{ __('messages.this_year') }}</option>
                    </select>
                </div>
                <div class="card-body p-4 pt-0">
                    <canvas id="cashFlowChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-soft h-100 bg-white">
                <div class="card-header bg-white p-4 border-0 pb-2">
                    <h5 class="fw-bold mb-0 text-dark">{{ __('messages.expense_breakdown') }}</h5>
                    <small class="text-muted">{{ __('messages.current_month_spending') }}</small>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="position-relative d-flex justify-content-center align-items-center mb-4" style="height: 220px;">
                        <canvas id="expenseChart"></canvas>
                        <div class="position-absolute text-center" style="pointer-events: none;">
                            <span class="d-block text-muted small fw-bold mb-1" style="font-size: 0.7rem; text-transform: uppercase;">{{ __('messages.spent_so_far') }}</span>
                            <h4 class="fw-bold text-dark mb-0" id="expenseChartTotal">RM 0.00</h4>
                        </div>
                    </div>
                    <div id="customExpenseLegend" class="d-flex flex-wrap justify-content-center gap-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-soft bg-white mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10 p-4 border-0">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2"></i> {{ __('messages.pending_parent_payments') }}</h5>
            <small class="text-muted">{{ __('messages.verify_receipts') }}</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="ps-4">{{ __('messages.student') }}</th>
                        <th>{{ __('messages.amount_rm') }}</th>
                        <th>{{ __('messages.receipt_reference') }}</th>
                        <th class="text-end pe-4">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments ?? [] as $p)
                    <tr>
                        <td class="ps-4 font-weight-bold">{{ $p->student->student_name ?? 'Unknown' }}</td>
                        <td>RM {{ number_format($p->amount, 2) }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $p->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-image"></i> {{ __('messages.view_receipt') }}
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.finance.approve', $p->payment_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">{{ __('messages.approve') }}</button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{$p->payment_id}}">{{ __('messages.reject') }}</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted small">{{ __('messages.no_pending_payments') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill text-primary me-2"></i> {{ __('messages.student_fee_tracker') }}</h5>
            <form action="{{ route('admin.finance.generate-bills') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary fw-bold rounded-pill" onclick="return confirm('{{ __('messages.generate_bills_confirm') }}');">
                    <i class="bi bi-receipt me-1"></i> {{ __('messages.generate_bills') }}
                </button>
            </form>
        </div>
        <div class="card-body p-4">
            <div class="accordion accordion-flush" id="classAccordion">
                @foreach($classrooms ?? [] as $index => $class)
                <div class="accordion-item border rounded mb-3 shadow-sm">
                    <h2 class="accordion-header" id="heading{{ $class->class_id }}">
                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} fw-bold bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $class->class_id }}">
                            
                            <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>
                            <span class="text-dark me-3 fs-6">
                                {{ $class->class_name ?? $class->name ?? 'Class ' . $class->class_id }}
                            </span>
                            
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill">
                                {{ $class->students->count() }} {{ __('messages.students') }}
                            </span>

                        </button>
                    </h2>
                    <div id="collapse{{ $class->class_id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#classAccordion">
                        <div class="accordion-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">{{ __('messages.student_name') }}</th>
                                        <th>{{ __('messages.parent_guardian') }}</th>
                                        <th>{{ __('messages.outstanding_balance') }}</th>
                                        <th class="text-end pe-4">{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($class->students as $student)
                                        @php
                                            $outstanding = $student->payments->sum('amount');
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">{{ $student->student_name }}</td>
                                            <td class="text-muted small">{{ $student->parent->parent_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($outstanding > 0)
                                                    <span class="text-danger fw-bold">RM {{ number_format($outstanding, 2) }}</span>
                                                @else
                                                    <span class="text-success fw-bold">RM 0.00</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                @if($outstanding > 0)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">{{ __('messages.outstanding_balance') }}</span>
                                                @else
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> {{ __('messages.cleared') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">{{ __('messages.no_students_enrolled') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card card-soft bg-white mb-4">
        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">{{ __('messages.transaction_history') }}</h5>
            <span class="badge bg-light text-secondary border">{{ count($transactions ?? []) }} {{ __('messages.records') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('messages.date') }}</th>
                        <th>{{ __('messages.description_category') }}</th>
                        <th>{{ __('messages.method') }}</th>
                        <th class="text-end pe-4">{{ __('messages.amount_rm') }}</th>
                        <th class="text-end pe-4">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions ?? [] as $t)
                    <tr>
                        <td class="ps-4 text-muted font-monospace">{{ $t->date ? \Carbon\Carbon::parse($t->date)->format('d M Y') : '-' }}</td>
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
                            <small class="text-muted"><i class="bi bi-credit-card me-1"></i> {{ $t->payment_method ?? 'Transfer' }}</small>
                        </td>
                        <td class="text-end fw-bold {{ $t->type == 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $t->type == 'income' ? '+' : '-' }} RM {{ number_format($t->amount, 2) }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                
                                @if(!empty($t->receipt_path))
                                    <a href="{{ asset('storage/' . $t->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Uploaded Receipt">
                                        <i class="bi bi-file-earmark-text"></i> {{ __('messages.view') }}
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary disabled" title="No Receipt Attached" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="bi bi-file-earmark-x"></i> {{ __('messages.none') }}
                                    </button>
                                @endif
                                
                                <form action="{{ route('admin.finance.delete', $t->transaction_id ?? $t->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_confirm') }}');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-muted hover-danger p-0" title="Delete">
                                        <i class="bi bi-trash-fill fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt-cutoff fs-1 opacity-50"></i>
                            <p class="mt-2 mb-0">{{ __('messages.no_transactions') }}</p>
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
        <form class="modal-content border-0 shadow" action="{{ route('admin.finance.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="income">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-wallet-fill me-2"></i> {{ __('messages.record_income') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                
                <div class="mb-3">
                    <label class="small fw-bold text-muted">{{ __('messages.category') }}</label>
                    <select name="category" id="incomeCategory" class="form-select border-0 shadow-sm py-2">
                        <option value="School Fees">{{ __('messages.monthly_school_fees') }}</option>
                        <option value="Registration Fee">{{ __('messages.registration_fee') }}</option>
                        <option value="Uniform/Books">{{ __('messages.uniform_books') }}</option>
                        <option value="Donation">{{ __('messages.donation') }}</option>
                        <option value="Other">{{ __('messages.other_income') }}</option>
                    </select>
                </div>

                <div class="mb-3" id="studentInvoiceWrapper">
                    <label class="small fw-bold text-muted">{{ __('messages.select_unpaid_invoice') }}</label>
                    <select name="payment_id" id="admin_payment_id" class="form-select border-0 shadow-sm py-2">
                        <option value="" data-amount="" data-desc="">{{ __('messages.or_select_unpaid') }}</option>
                        @foreach($pendingPayments ?? [] as $invoice)
                            <option value="{{ $invoice->payment_id }}" 
                                    data-amount="{{ $invoice->amount }}" 
                                    data-desc="{{ $invoice->student->student_name ?? 'Student' }} - {{ $invoice->admin_remarks ?? 'Fee' }}">
                                {{ $invoice->student->student_name ?? 'Unknown' }} (RM {{ number_format($invoice->amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted">{{ __('messages.description_who_paid') }}</label>
                    <input type="text" id="admin_desc" name="description" class="form-control border-0 shadow-sm py-2" placeholder="e.g. Ali Bin Abu (Jan Fee)" required>
                </div>
                
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">{{ __('messages.amount_rm') }}</label>
                        <input type="number" step="0.01" id="admin_amount" name="amount" class="form-control border-0 shadow-sm py-2 text-success fw-bold" placeholder="0.00" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">{{ __('messages.date') }}</label>
                        <input type="date" name="date" class="form-control border-0 shadow-sm py-2" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="small fw-bold text-muted">{{ __('messages.payment_method') }}</label>
                    <select name="payment_method" class="form-select border-0 shadow-sm py-2">
                        <option value="Cash">{{ __('messages.cash_handover') }}</option>
                        <option value="Online Transfer">{{ __('messages.online_transfer') }}</option>
                        <option value="Cheque">{{ __('messages.cheque') }}</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="small fw-bold text-muted">{{ __('messages.upload_receipt_optional') }}</label>
                    <input type="file" name="receipt_file" class="form-control border-0 shadow-sm" accept=".jpg,.jpeg,.png,.pdf">
                    <small class="text-muted mt-1" style="font-size: 0.75rem;">{{ __('messages.supported_formats') }}</small>
                </div>

            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill py-2 shadow">{{ __('messages.save_income_record') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" action="{{ route('admin.finance.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="expense">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-cart-fill me-2"></i> {{ __('messages.record_expense') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">{{ __('messages.category') }}</label>
                    <select id="expenseCategory" name="category" class="form-select border-0 shadow-sm py-2">
                        <option value="Food & Kitchen">{{ __('messages.kitchen_supplies') }}</option>
                        <option value="Stationary">{{ __('messages.classroom_stationary') }}</option>
                        <option value="Maintenance">{{ __('messages.repairs_maintenance') }}</option>
                        <option value="Utility Bills">{{ __('messages.utilities') }}</option>
                        <option value="Salary">{{ __('messages.staff_salary') }}</option>
                        <option value="Event Cost">{{ __('messages.event_expenses') }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">{{ __('messages.description_details') }}</label>
                    <input type="text" id="expenseDesc" name="description" class="form-control border-0 shadow-sm py-2" placeholder="e.g. Bought 10kg Rice" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">{{ __('messages.amount_rm') }}</label>
                        <input type="number" id="expenseAmount" step="0.01" name="amount" class="form-control border-0 shadow-sm py-2 text-danger fw-bold" placeholder="0.00" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">{{ __('messages.date') }}</label>
                        <input type="date" name="date" class="form-control border-0 shadow-sm py-2" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">{{ __('messages.payment_method') }}</label>
                    <select name="payment_method" class="form-select border-0 shadow-sm py-2">
                        <option value="Cash">{{ __('messages.cash') }}</option>
                        <option value="Online Transfer">{{ __('messages.online_transfer') }}</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="small fw-bold text-muted">{{ __('messages.upload_receipt_optional') }}</label>
                    <input type="file" name="receipt_file" class="form-control border-0 shadow-sm" accept=".jpg,.jpeg,.png,.pdf">
                    <small class="text-muted mt-1" style="font-size: 0.75rem;">{{ __('messages.supported_formats') }}</small>
                </div>

            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill py-2 shadow">{{ __('messages.save_expense_record') }}</button>
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
                <h5 class="modal-title">{{ __('messages.reject') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="small fw-bold">{{ __('messages.reason_for_rejection') }}</label>
                <textarea name="remarks" class="form-control" placeholder="e.g. Blurry receipt, incorrect amount" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger w-100 rounded-pill">{{ __('messages.confirm_reject') }}</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// --- AUTO FILL INCOME FORM ---
document.addEventListener("DOMContentLoaded", function() {
    const paymentSelect = document.getElementById('admin_payment_id');
    const amountInput = document.getElementById('admin_amount');
    const descInput = document.getElementById('admin_desc');

    if(paymentSelect && amountInput && descInput) {
        paymentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const amount = selectedOption.getAttribute('data-amount');
            const desc = selectedOption.getAttribute('data-desc');
            
            if (amount) {
                amountInput.value = parseFloat(amount).toFixed(2);
                descInput.value = desc; // Auto isi deskripsi 
            } else {
                amountInput.value = ''; 
                descInput.value = ''; 
            }
        });
    }
});

// --- EXISTING CHART JS ---
let cashFlowChartInstance = null;
let expenseChartInstance = null; 

async function updateChartData() {
    const timeframe = document.getElementById('timeframeFilter').value;
    
    try {
        const response = await fetch(`{{ route('admin.finance.chart-data') }}?timeframe=${timeframe}`);
        const data = await response.json();

        // 1. DRAW CASH FLOW BAR CHART
        if (cashFlowChartInstance) cashFlowChartInstance.destroy();

        const ctxCashFlow = document.getElementById('cashFlowChart').getContext('2d');
        cashFlowChartInstance = new Chart(ctxCashFlow, {
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
                scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4] } } }
            }
        });

        // 2. DRAW EXPENSE BREAKDOWN DOUGHNUT CHART
        if (expenseChartInstance) expenseChartInstance.destroy();

        const ctxExpense = document.getElementById('expenseChart').getContext('2d');
        
        const breakdownData = data.expenseBreakdownData ? Object.values(data.expenseBreakdownData) : [];
        const breakdownLabels = data.expenseBreakdownLabels ? Object.values(data.expenseBreakdownLabels) : [];
        
        const hasData = breakdownData.length > 0 && breakdownData.some(val => val > 0);
        
        const totalSpent = breakdownData.reduce((a, b) => a + parseFloat(b), 0);
        document.getElementById('expenseChartTotal').innerText = 'RM ' + totalSpent.toLocaleString('en-MY', {minimumFractionDigits: 2});

        const chartColors = ['#ff4b2b', '#ffc107', '#667eea', '#11998e', '#0dcaf0', '#d63384'];

        expenseChartInstance = new Chart(ctxExpense, {
            type: 'doughnut',
            data: {
                labels: hasData ? breakdownLabels : ['No Expenses Yet'],
                datasets: [{
                    data: hasData ? breakdownData : [1], 
                    backgroundColor: hasData ? chartColors.slice(0, breakdownData.length) : ['#e9ecef'],
                    borderWidth: 0,
                    cutout: '80%' 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if(!hasData) return 'RM 0.00';
                                return ' RM ' + parseFloat(context.raw).toLocaleString('en-MY', {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });

        // 3. RENDER CUSTOM HTML LEGEND
        const legendContainer = document.getElementById('customExpenseLegend');
        legendContainer.innerHTML = ''; 

        if (hasData) {
            breakdownLabels.forEach((label, index) => {
                const amount = parseFloat(breakdownData[index]).toLocaleString('en-MY', {minimumFractionDigits: 2});
                const colorHex = chartColors[index % chartColors.length];
                
                let iconClass = 'bi-receipt'; 
                if (label.includes('Food') || label.includes('Kitchen')) iconClass = 'bi-cup-straw';
                else if (label.includes('Stationary')) iconClass = 'bi-pencil-square';
                else if (label.includes('Maintenance')) iconClass = 'bi-tools';
                else if (label.includes('Utility')) iconClass = 'bi-lightning-charge-fill';
                else if (label.includes('Salary')) iconClass = 'bi-person-badge-fill';
                else if (label.includes('Event')) iconClass = 'bi-calendar-star-fill';

                legendContainer.innerHTML += `
                    <div class="d-flex flex-column align-items-center text-center mx-1 mt-2">
                        <div class="rounded-circle d-flex justify-content-center align-items-center mb-2" 
                             style="width: 45px; height: 45px; background-color: ${colorHex}20; color: ${colorHex};">
                            <i class="bi ${iconClass} fs-5"></i>
                        </div>
                        <span class="fw-bold text-dark" style="font-size: 0.75rem;">${label}</span>
                        <span class="text-danger fw-bold" style="font-size: 0.7rem;">-RM ${amount}</span>
                    </div>
                `;
            });
        } else {
            legendContainer.innerHTML = `<span class="text-muted small">No category data to display.</span>`;
        }

    } catch(e) {
        console.error("Chart rendering error:", e);
    }
}

document.addEventListener('DOMContentLoaded', updateChartData);
</script>
@endsection