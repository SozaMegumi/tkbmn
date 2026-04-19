@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI CARDS --- */
    .card-soft {
        border: none; border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: #fff;
    }

    /* --- GRADING TOGGLES --- */
    .grade-group {
        display: flex; gap: 4px; background: #f8fafc;
        padding: 4px; border-radius: 8px; border: 1px solid #e2e8f0;
    }
    .grade-radio { display: none; }
    
    .btn-grade {
        flex: 1; text-align: center; border-radius: 6px; padding: 6px 0; 
        font-size: 0.75rem; font-weight: 700; color: #64748b; 
        cursor: pointer; transition: all 0.2s; border: 1px solid transparent;
    }
    .btn-grade:hover { background: #f1f5f9; }

    /* Active States based on KSPK Standard */
    /* TM: Telah Menguasai (Mastered) */
    .grade-radio[value="TM"]:checked + .btn-grade { background-color: #10b981; color: white; box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3); }
    /* SM: Sedang Maju (Progressing) */
    .grade-radio[value="SM"]:checked + .btn-grade { background-color: #3b82f6; color: white; box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3); }
    /* BM: Belum Menguasai (Needs Help) */
    .grade-radio[value="BM"]:checked + .btn-grade { background-color: #f59e0b; color: white; box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3); }

    /* Avatar */
    .avatar-placeholder {
        width: 40px; height: 40px; background: #e2e8f0; color: #64748b;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.9rem;
    }
</style>

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Student Assessments</h3>
            <p class="text-muted small mb-0">Evaluate student progress based on observational criteria.</p>
        </div>
        <div class="text-end">
            <span class="d-block fw-bold text-primary">{{ $assignedClass ?? 'Kelas Mawar' }}</span>
            <small class="text-muted">Academic Year 2026</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-soft mb-4 border-top border-info border-4">
        <div class="card-body p-4">
            <form action="{{ route('teacher.grading') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label class="small fw-bold text-muted mb-2"><i class="bi bi-journal-text me-1"></i> Select Scheduled Assessment</label>
                    <select name="assessment_id" class="form-select form-select-lg border-0 bg-light shadow-sm" required>
                        <option value="">-- Choose Evaluation --</option>
                        <option value="1" selected>Term 1: Tunjang Komunikasi (Membaca)</option>
                        <option value="2">Term 1: Tunjang Kerohanian (Hafazan Pendek)</option>
                        <option value="3">Term 1: Tunjang Fizikal (Motor Halus)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="padding: 12px; border-radius: 10px;">
                        Load Criteria <i class="bi bi-arrow-down-circle ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('teacher.grading.store') }}" method="POST">
        @csrf
        <div class="card card-soft">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Grading Sheet: Tunjang Komunikasi</h5>
                    <small class="text-muted">Evaluate reading and comprehension skills.</small>
                </div>
                <div class="d-flex gap-3 small fw-bold text-muted bg-light px-3 py-2 rounded-pill border">
                    <span class="text-success"><i class="bi bi-circle-fill small me-1"></i>TM: Mastered</span>
                    <span class="text-primary"><i class="bi bi-circle-fill small me-1"></i>SM: Progressing</span>
                    <span class="text-warning"><i class="bi bi-circle-fill small me-1"></i>BM: Needs Help</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="width: 25%;">Student</th>
                            <th class="py-3" style="width: 20%;">Mengenal Huruf (Letters)</th>
                            <th class="py-3" style="width: 20%;">Suku Kata (Syllables)</th>
                            <th class="py-3" style="width: 20%;">Faham Arahan (Comprehension)</th>
                            <th class="pe-4 py-3" style="width: 15%;">Teacher's Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-placeholder me-3">AH</div>
                                    <div>
                                        <div class="fw-bold text-dark">Ahmad Bin Ali</div>
                                        <small class="text-muted">200103101234</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="grade-group">
                                    <input type="radio" name="grades[1][c1]" value="BM" id="1_c1_bm" class="grade-radio">
                                    <label for="1_c1_bm" class="btn-grade">BM</label>
                                    
                                    <input type="radio" name="grades[1][c1]" value="SM" id="1_c1_sm" class="grade-radio" checked>
                                    <label for="1_c1_sm" class="btn-grade">SM</label>
                                    
                                    <input type="radio" name="grades[1][c1]" value="TM" id="1_c1_tm" class="grade-radio">
                                    <label for="1_c1_tm" class="btn-grade">TM</label>
                                </div>
                            </td>
                            <td>
                                <div class="grade-group">
                                    <input type="radio" name="grades[1][c2]" value="BM" id="1_c2_bm" class="grade-radio">
                                    <label for="1_c2_bm" class="btn-grade">BM</label>
                                    
                                    <input type="radio" name="grades[1][c2]" value="SM" id="1_c2_sm" class="grade-radio" checked>
                                    <label for="1_c2_sm" class="btn-grade">SM</label>
                                    
                                    <input type="radio" name="grades[1][c2]" value="TM" id="1_c2_tm" class="grade-radio">
                                    <label for="1_c2_tm" class="btn-grade">TM</label>
                                </div>
                            </td>
                            <td>
                                <div class="grade-group">
                                    <input type="radio" name="grades[1][c3]" value="BM" id="1_c3_bm" class="grade-radio">
                                    <label for="1_c3_bm" class="btn-grade">BM</label>
                                    
                                    <input type="radio" name="grades[1][c3]" value="SM" id="1_c3_sm" class="grade-radio">
                                    <label for="1_c3_sm" class="btn-grade">SM</label>
                                    
                                    <input type="radio" name="grades[1][c3]" value="TM" id="1_c3_tm" class="grade-radio" checked>
                                    <label for="1_c3_tm" class="btn-grade">TM</label>
                                </div>
                            </td>
                            <td class="pe-4">
                                <input type="text" name="grades[1][notes]" class="form-control form-control-sm bg-light border-0" placeholder="Optional notes...">
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-placeholder me-3">IM</div>
                                    <div>
                                        <div class="fw-bold text-dark">Iman bin Ali</div>
                                        <small class="text-muted">200204104321</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="grade-group">
                                    <input type="radio" name="grades[2][c1]" value="BM" id="2_c1_bm" class="grade-radio">
                                    <label for="2_c1_bm" class="btn-grade">BM</label>
                                    <input type="radio" name="grades[2][c1]" value="SM" id="2_c1_sm" class="grade-radio">
                                    <label for="2_c1_sm" class="btn-grade">SM</label>
                                    <input type="radio" name="grades[2][c1]" value="TM" id="2_c1_tm" class="grade-radio">
                                    <label for="2_c1_tm" class="btn-grade">TM</label>
                                </div>
                            </td>
                            <td>
                                <div class="grade-group">
                                    <input type="radio" name="grades[2][c2]" value="BM" id="2_c2_bm" class="grade-radio">
                                    <label for="2_c2_bm" class="btn-grade">BM</label>
                                    <input type="radio" name="grades[2][c2]" value="SM" id="2_c2_sm" class="grade-radio">
                                    <label for="2_c2_sm" class="btn-grade">SM</label>
                                    <input type="radio" name="grades[2][c2]" value="TM" id="2_c2_tm" class="grade-radio">
                                    <label for="2_c2_tm" class="btn-grade">TM</label>
                                </div>
                            </td>
                            <td>
                                <div class="grade-group">
                                    <input type="radio" name="grades[2][c3]" value="BM" id="2_c3_bm" class="grade-radio">
                                    <label for="2_c3_bm" class="btn-grade">BM</label>
                                    <input type="radio" name="grades[2][c3]" value="SM" id="2_c3_sm" class="grade-radio">
                                    <label for="2_c3_sm" class="btn-grade">SM</label>
                                    <input type="radio" name="grades[2][c3]" value="TM" id="2_c3_tm" class="grade-radio">
                                    <label for="2_c3_tm" class="btn-grade">TM</label>
                                </div>
                            </td>
                            <td class="pe-4">
                                <input type="text" name="grades[2][notes]" class="form-control form-control-sm bg-light border-0" placeholder="Optional notes...">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light p-4 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Ensure all skills are evaluated before saving.
                    </div>
                    <button type="submit" class="btn btn-success fw-bold px-5 py-2 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-journal-check me-2"></i> Save Evaluations
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection