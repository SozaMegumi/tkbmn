@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        background: #fff;
    }
    .bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
    
    .invoice-item {
        border-left: 4px solid transparent;
        transition: all 0.2s;
    }
    .invoice-item:hover {
        background: #f8fafc;
        border-left-color: #0d6efd;
    }
    .file-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
    }
    .file-upload-box:hover { border-color: #0d6efd; background: #eff6ff; }
</style>

<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Billing & Payments</h3>
            <p class="text-muted small mb-0">Manage fees for your registered children.</p>
        </div>
        <div class="text-end">
            <span class="d-block fw-bold text-dark">{{ auth()->user()->parent_name }}</span>
            <small class="text-muted">Parent Portal</small>
        </div>
    </div>

    <!-- FLASH MESSAGES (Success / System Errors) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $totalOutstanding = $pendingInvoices->sum('amount');
        $lastPayment = $paymentHistory->first();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-soft bg-gradient-danger h-100 p-4 position-relative overflow-hidden">
                <span class="text-uppercase fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="bi bi-exclamation-circle me-2"></i> Total Outstanding / Pending</span>
                <h1 class="display-5 fw-bold mb-0">RM {{ number_format($totalOutstanding, 2) }}</h1>
                <p class="mb-0 mt-2 opacity-75 small">Verify payments promptly to avoid arrears.</p>
                <i class="bi bi-wallet2 position-absolute text-white" style="font-size: 8rem; opacity: 0.1; right: -20px; bottom: -20px;"></i>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-soft bg-white border border-light h-100 p-4">
                <span class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="bi bi-check-circle-fill text-success me-2"></i> Last Payment Received</span>
                @if($lastPayment)
                    <h2 class="fw-bold text-dark mb-0 mt-2">RM {{ number_format($lastPayment->amount, 2) }}</h2>
                    <p class="text-muted mb-0 mt-1 small">Approved on {{ $lastPayment->updated_at->format('d M Y') }}</p>
                @else
                    <h2 class="fw-bold text-dark mb-0 mt-2">RM 0.00</h2>
                    <p class="text-muted mb-0 mt-1 small">No previous payment records found.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-soft">
                <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Current / Pending Invoices</h5>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">{{ count($pendingInvoices) }} Items</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        
                        @forelse($pendingInvoices as $invoice)
                        <div class="list-group-item invoice-item p-4 border-top border-bottom-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $invoice->admin_remarks ?? 'Monthly Fee' }}</h6>
                                    <p class="text-muted small mb-0">Student: <strong>{{ $invoice->student->student_name ?? 'N/A' }}</strong></p>
                                    @if($invoice->status == 'Pending')
                                        <span class="badge bg-warning text-dark mt-2">Processing by Admin</span>
                                    @else
                                        <span class="badge bg-danger mt-2">Unpaid</span>
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
                            You have no pending payments!
                        </div>
                        @endforelse

                    </div>
                </div>
                <div class="card-footer bg-light p-4 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Please make payment to: <strong>Maybank 1623 4567 8910 (Tabika Kemas BMN)</strong></span>
                        <button class="btn btn-primary fw-bold px-4 py-2 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="bi bi-upload me-2"></i> Submit Payment Proof
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft h-100">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark">Payment History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        
                        @forelse($paymentHistory as $history)
                        <div class="list-group-item p-4 border-top border-bottom-0">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle me-3">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">RM {{ number_format($history->amount, 2) }}</h6>
                                    <p class="text-muted small mb-1">{{ $history->admin_remarks ?? 'School Fee' }}</p>
                                    <span class="badge bg-success small">Approved</span>
                                    <small class="text-muted d-block mt-1">{{ $history->updated_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted small">
                            No payment history found.
                        </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" method="POST" action="{{ route('parent.payment.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-up-fill me-2"></i> Submit Payment Receipt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                
                <!-- VALIDATION ERROR CATCHER -->
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 small">
                        <strong>Please fix the following issues:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <small>
                        <strong>Bank Details:</strong><br>
                        Bank: Maybank<br>
                        Acc Name: Tabika Kemas Bustanul Makwan Najwa<br>
                        Acc No: 1623 4567 8910
                    </small>
                </div>

                <!-- CHILD SELECTION DROPDOWN -->
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Payment For (Student)</label>
                    <select name="student_id" class="form-select border-0 shadow-sm py-2" required>
                        <option value="">-- Select Child --</option>
                        @php
                            $student = \App\Models\Student::where('parent_id', auth()->user()->parent_id)->first();
                        @endphp
                        @if($student)
                            <option value="{{ $student->student_id }}">{{ $student->student_name }}</option>
                        @endif
                    </select>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Payment Amount (RM)</label>
                    <input type="number" step="0.01" name="amount" class="form-control border-0 shadow-sm py-2" placeholder="e.g. 150.00" required>
                </div>
                
                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">Reference / Notes</label>
                    <input type="text" name="reference" class="form-control border-0 shadow-sm py-2" placeholder="e.g. Yuran Ahmad Mei & Buku" required>
                </div>

                <label class="small fw-bold text-muted mb-2">Upload Bank Receipt (JPG/PNG/PDF)</label>
                <div class="file-upload-box" onclick="document.getElementById('receiptInput').click()">
                    <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold mt-2 text-dark">Click to browse files</h6>
                    <small class="text-muted">Max file size: 2MB</small>
                    <input type="file" name="receipt" id="receiptInput" class="d-none" accept=".jpg,.jpeg,.png,.pdf" onchange="updateFileName(this)" required>
                </div>
                <div id="fileNameDisplay" class="text-center mt-2 small fw-bold text-success"></div>

            </div>
            
            <div class="modal-footer border-0 bg-white">
                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2 shadow-sm">Submit for Admin Approval</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileNameDisplay').innerText = "Selected file: " + input.files[0].name;
            document.querySelector('.file-upload-box').style.borderColor = '#11998e';
            document.querySelector('.file-upload-box').style.backgroundColor = '#f0fdf4';
        }
    }

    // AUTO-OPEN MODAL IF THERE ARE ERRORS
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            myModal.show();
        @endif
    });
</script>
@endsection