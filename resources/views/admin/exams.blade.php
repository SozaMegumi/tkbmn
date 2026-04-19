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
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: white; }
    
    .stat-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; font-weight: 600;}
    .display-amount { font-size: 2.5rem; font-weight: 800; line-height: 1;}

    .table-custom thead th {
        background: #f8f9fa; color: #8898aa; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 1px; padding: 15px; border: none;
    }
    .table-custom tbody td {
        padding: 16px 15px; vertical-align: middle; 
        border-bottom: 1px solid #f0f0f0; font-size: 0.95rem;
    }
</style>

<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Assessments & Evaluations</h3>
            <p class="text-muted small mb-0">Manage student performance tracking and grading schedules.</p>
        </div>
        <button class="btn btn-primary fw-bold shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addExamModal">
            <i class="bi bi-calendar-plus me-2"></i> Schedule Assessment
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-soft bg-gradient-info h-100 p-4">
                <span class="stat-label mb-2"><i class="bi bi-journal-text me-2"></i> Active Assessments</span>
                <h1 class="display-amount mb-0">2</h1>
                <i class="bi bi-activity position-absolute text-white" style="font-size: 6rem; opacity: 0.15; right: -10px; bottom: -10px;"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft bg-gradient-warning h-100 p-4">
                <span class="stat-label mb-2"><i class="bi bi-hourglass-split me-2"></i> Pending Teacher Grading</span>
                <h1 class="display-amount mb-0">1</h1>
                <i class="bi bi-pencil-square position-absolute text-white" style="font-size: 6rem; opacity: 0.15; right: -10px; bottom: -10px;"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft bg-gradient-success h-100 p-4">
                <span class="stat-label mb-2"><i class="bi bi-check-circle me-2"></i> Completed This Term</span>
                <h1 class="display-amount mb-0">4</h1>
                <i class="bi bi-award position-absolute text-white" style="font-size: 6rem; opacity: 0.15; right: -10px; bottom: -10px;"></i>
            </div>
        </div>
    </div>

    <div class="card card-soft bg-white">
        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Scheduled Evaluations</h5>
            <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-muted">
                <option>Term 1 (2026)</option>
                <option>Term 2 (2026)</option>
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Assessment Title</th>
                        <th>Subject / Type</th>
                        <th>Target Class</th>
                        <th>Date & Status</th>
                        <th style="width: 20%;">Grading Progress</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 fw-bold text-dark">Mid-Year Hafazan Evaluation</td>
                        <td><span class="badge bg-light text-secondary border">Kerohanian</span></td>
                        <td>All 6-Year Classes</td>
                        <td>
                            <div class="text-dark small"><i class="bi bi-calendar-event me-1"></i> 15 - 20 May 2026</div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill mt-1">Active</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Graded: 15/50</span>
                                <span>30%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 30%;"></div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</button>
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-4 fw-bold text-dark">Motor Skills Checklist</td>
                        <td><span class="badge bg-light text-secondary border">Fizikal</span></td>
                        <td>Kelas Melur (5 Yrs)</td>
                        <td>
                            <div class="text-dark small"><i class="bi bi-calendar-event me-1"></i> 01 - 05 Jun 2026</div>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill mt-1">Upcoming</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Not Started</span>
                                <span>0%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-light" style="width: 0%;"></div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</button>
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-4 fw-bold text-muted">Term 1 Reading Assessment</td>
                        <td><span class="badge bg-light text-secondary border">Komunikasi</span></td>
                        <td>All Classes</td>
                        <td>
                            <div class="text-muted small"><i class="bi bi-calendar-check me-1"></i> 10 Feb 2026</div>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill mt-1">Completed</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Graded: 75/75</span>
                                <span>100%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-secondary" style="width: 100%;"></div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light text-muted" disabled><i class="bi bi-lock-fill"></i> Locked</button>
                            <button class="btn btn-sm btn-outline-info ms-1" title="View Results"><i class="bi bi-bar-chart-fill"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow" method="POST" action="#">
            @csrf
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i> Schedule New Assessment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Assessment Title</label>
                    <input type="text" name="title" class="form-control border-0 shadow-sm py-2" placeholder="e.g., Mid-Year Hafazan Test" required>
                </div>
                
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Subject / Module</label>
                        <select name="subject" class="form-select border-0 shadow-sm py-2">
                            <option value="Kerohanian">Tunjang Kerohanian (Islamic)</option>
                            <option value="Komunikasi">Tunjang Komunikasi (Reading/Speech)</option>
                            <option value="Fizikal">Tunjang Fizikal (Motor Skills)</option>
                            <option value="Sains">Tunjang Sains & Awal Matematik</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Term / Session</label>
                        <select name="term" class="form-select border-0 shadow-sm py-2">
                            <option value="Term 1">Term 1</option>
                            <option value="Term 2">Term 2</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted">Target Class</label>
                    <select name="class_id" class="form-select border-0 shadow-sm py-2">
                        <option value="all">All Classes</option>
                        <option value="1">Kelas Mawar (6 Years)</option>
                        <option value="2">Kelas Melur (5 Years)</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Start Date</label>
                        <input type="date" name="start_date" class="form-control border-0 shadow-sm py-2" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">End Date</label>
                        <input type="date" name="end_date" class="form-control border-0 shadow-sm py-2" required>
                    </div>
                </div>
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Teachers will be able to input grades during this date range.</small>
            </div>
            
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2 shadow-sm">Save Schedule</button>
            </div>
        </form>
    </div>
</div>
@endsection