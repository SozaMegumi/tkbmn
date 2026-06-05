@extends('layouts.app')

@section('content')
<style>
    .card-soft { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); background: #fff; }
    .bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
    .invoice-item { border-left: 4px solid transparent; transition: all 0.2s; }
    .invoice-item:hover { background: #f8fafc; border-left-color: #0d6efd; }
    .file-upload-box { border: 2px dashed #cbd5e1; border-radius: 15px; padding: 30px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s; }
    .file-upload-box:hover { border-color: #0d6efd; background: #eff6ff; }
</style>

<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.billing_payments') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.manage_fees_children') }}</p>
        </div>
        <div class="text-end">
            <span class="d-block fw-bold text-dark">{{ auth()->guard('parent')->user()->parent_name }}</span>
            <small class="text-muted">{{ __('messages.parent_portal') }}</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $totalOutstanding = collect($pendingInvoices)->sum('amount');
        $lastPayment = collect($paymentHistory)->first();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-soft bg-gradient-danger h-100 p-4 position-relative overflow-hidden">
                <span class="text-uppercase fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="bi bi-exclamation-circle me-2"></i> {{ __('messages.total_outstanding_pending') }}</span>
                <h1 class="display-5 fw-bold mb-0">RM {{ number_format($totalOutstanding, 2) }}</h1>
                <p class="mb-0 mt-2 opacity-75 small">{{ __('messages.verify_payments_promptly') }}</p>
                <i class="bi bi-wallet2 position-absolute text-white" style="font-size: 8rem; opacity: 0.1; right: -20px; bottom: -20px;"></i>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-soft bg-white border border-light h-100 p-4">
                <span class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="bi bi-check-circle-fill text-success me-2"></i> {{ __('messages.last_payment_received') }}</span>
                @if($lastPayment)
                    <h2 class="fw-bold text-dark mb-0 mt-2">RM {{ number_format($lastPayment->amount, 2) }}</h2>
                    <p class="text-muted mb-0 mt-1 small">{{ __('messages.approved_on', ['date' => $lastPayment->updated_at->format('d M Y')]) }}</p>
                @else
                    <h2 class="fw-bold text-dark mb-0 mt-2">RM 0.00</h2>
                    <p class="text-muted mb-0 mt-1 small">{{ __('messages.no_previous_payment') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-soft">
                <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('messages.current_pending_invoices') }}</h5>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 mt-1">{{ count($pendingInvoices) }} {{ __('messages.items') }}</span>
                    </div>
                    @if(count($pendingInvoices) > 0)
                        <button class="btn btn-primary fw-bold px-4 py-2 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="bi bi-wallet2 me-2"></i> {{ __('messages.pay_now') }}
                        </button>
                    @endif
                </div>
                
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendingInvoices as $invoice)
                        <div class="list-group-item invoice-item p-4 border-top border-bottom-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $invoice->admin_remarks ?? __('messages.monthly_fee') }}</h6>
                                    <p class="text-muted small mb-0">{{ __('messages.student') }}: <strong>{{ $invoice->student->student_name ?? 'N/A' }}</strong></p>
                                    @if($invoice->status == 'Pending')
                                        <span class="badge bg-warning text-dark mt-2">{{ __('messages.processing_by_admin') }}</span>
                                    @else
                                        <span class="badge bg-danger mt-2">{{ __('messages.unpaid') }}</span>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <h5 class="fw-bold text-danger mb-1">RM {{ number_format($invoice->amount, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success opacity-50 mb-3 d-block"></i>
                            {{ __('messages.no_pending_payments_parent') }}
                        </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="card-footer bg-light p-4 border-top text-center">
                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> {{ __('messages.clear_outstanding_promptly') }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft h-100">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark">{{ __('messages.payment_history') }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($paymentHistory as $history)
                        <div class="list-group-item p-4 border-top border-bottom-0">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle me-3"><i class="bi bi-receipt"></i></div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">RM {{ number_format($history->amount, 2) }}</h6>
                                    <p class="text-muted small mb-1">{{ $history->admin_remarks ?? __('messages.school_fee') }}</p>
                                    <span class="badge bg-success small">{{ __('messages.approved') }}</span>
                                    <small class="text-muted d-block mt-1">{{ $history->updated_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted small">{{ __('messages.no_payment_history') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-white border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i> {{ __('messages.payment_options') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 bg-white">
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 small rounded-3">
                        <strong>{{ __('messages.error_occurred') }}</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

               <ul class="nav nav-pills mb-4 d-flex bg-light p-1 rounded-pill" id="paymentTabs" role="tablist">
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link active w-100 rounded-pill fw-bold" id="stripe-tab" data-bs-toggle="pill" data-bs-target="#stripe-payment" type="button" role="tab">
                            <i class="bi bi-credit-card-fill me-1"></i> Credit Card & FPX
                        </button>
                    </li>
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link w-100 rounded-pill fw-bold" id="manual-tab" data-bs-toggle="pill" data-bs-target="#manual-payment" type="button" role="tab">
                            <i class="bi bi-receipt me-1"></i> {{ __('messages.manual_receipt') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="paymentTabsContent">
                    <div class="tab-pane fade show active" id="stripe-payment" role="tabpanel">
                        <div class="alert alert-primary bg-primary bg-opacity-10 border-0 shadow-sm rounded-3 mb-4">
                            <small class="text-primary fw-bold"><i class="bi bi-shield-check me-1"></i> {{ __('messages.secure_payment_stripe') }}</small>
                        </div>
                        <form method="POST" action="{{ route('parent.payment.pay') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-2">{{ __('messages.select_outstanding_invoice') }}</label>
                                <select name="payment_id" class="form-select border-light shadow-sm py-2 bg-light" required>
                                    <option value="">{{ __('messages.select_invoice') }}</option>
                                    @foreach($pendingInvoices as $invoice)
                                        <option value="{{ $invoice->payment_id }}">
                                            {{ $invoice->admin_remarks ?? __('messages.fee') }} - {{ $invoice->student->student_name ?? '' }} (RM {{ number_format($invoice->amount, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-3 shadow-sm mt-3">
                                <i class="bi bi-lock-fill me-2"></i> {{ __('messages.proceed_secure_payment') }}
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="manual-payment" role="tabpanel">
                        <div class="alert alert-info bg-info bg-opacity-10 border-0 shadow-sm rounded-3 mb-4">
                            <small class="text-dark">
                                <strong>{{ __('messages.kindergarten_bank_info') }}</strong><br>
                                {{ __('messages.bank') }} Maybank<br>
                                {{ __('messages.name') }} Tabika Kemas Bustanul Makwan Najwa<br>
                                {{ __('messages.account_no_label') }} 1623 4567 8910
                            </small>
                        </div>
                        <form method="POST" action="{{ route('parent.payment.upload') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-2">{{ __('messages.select_relevant_invoice') }}</label>
                                <select name="payment_id" class="form-select border-light shadow-sm py-2 bg-light" required>
                                    <option value="">{{ __('messages.select_invoice') }}</option>
                                    @foreach($pendingInvoices as $invoice)
                                        <option value="{{ $invoice->payment_id }}">
                                            {{ $invoice->admin_remarks ?? __('messages.fee') }} - {{ $invoice->student->student_name ?? '' }} (RM {{ number_format($invoice->amount, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-2">{{ __('messages.amount_paid_rm') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control border-light shadow-sm py-2 bg-light" placeholder="{{ __('messages.eg_150') }}" required>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold text-muted mb-2">{{ __('messages.additional_notes_optional') }}</label>
                                <input type="text" name="reference" class="form-control border-light shadow-sm py-2 bg-light" placeholder="{{ __('messages.eg_may_fee') }}">
                            </div>

                            <label class="small fw-bold text-muted mb-2">{{ __('messages.upload_transfer_proof') }}</label>
                            <div class="file-upload-box" onclick="document.getElementById('receiptInput').click()">
                                <i class="bi bi-cloud-arrow-up text-secondary" style="font-size: 2.5rem;"></i>
                                <h6 class="fw-bold mt-2 text-dark">{{ __('messages.click_to_browse') }}</h6>
                                <small class="text-muted">{{ __('messages.max_size_2mb') }}</small>
                                <input type="file" name="receipt" id="receiptInput" class="d-none" accept=".jpg,.jpeg,.png,.pdf" onchange="updateFileName(this)" required>
                            </div>
                            <div id="fileNameDisplay" class="text-center mt-2 small fw-bold text-success"></div>

                            <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill py-3 shadow-sm mt-4">
                                <i class="bi bi-upload me-2"></i> {{ __('messages.submit_receipt_verification') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileNameDisplay').innerText = "{{ __('messages.file_selected') }}" + input.files[0].name;
            document.querySelector('.file-upload-box').style.borderColor = '#11998e';
            document.querySelector('.file-upload-box').style.backgroundColor = '#f0fdf4';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            myModal.show();
        @endif
    });
</script>
@endsection