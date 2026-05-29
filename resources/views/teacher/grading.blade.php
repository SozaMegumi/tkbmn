@extends('layouts.app')

@section('content')
<style>
    .card-soft { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); background: #fff; }
    .table-grading th { background-color: #f8fafc; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: middle; }
    .table-grading td { vertical-align: middle; }
    .select-grade { font-weight: bold; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center; cursor: pointer; transition: all 0.2s; }
    .select-grade:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); }
    .select-grade option.tp1 { color: #dc3545; font-weight: bold; }
    .select-grade option.tp2 { color: #ffc107; font-weight: bold; }
    .select-grade option.tp3 { color: #198754; font-weight: bold; }
</style>

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Penilaian KSPK</h3>
            <p class="text-muted small mb-0">Sistem Pentaksiran Tabika Kemas (Pengisian Berkelompok)</p>
        </div>
        
        <a href="{{ route('teacher.report-cards') ?? '#' }}" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i> Jana Kad Laporan (Report Card)
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-4" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i> Ralat Penyimpanan!</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-4"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
    @endif

    <div class="card card-soft mb-4">
        <div class="card-body p-4 bg-light bg-gradient rounded-4 border border-light-subtle">
            <form action="{{ route('teacher.grading') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-bold text-dark mb-0 whitespace-nowrap"><i class="bi bi-journal-check text-primary me-2"></i> Sesi Pentaksiran:</label>
                <select name="assessment_id" class="form-select border-0 shadow-sm w-auto min-w-200" onchange="this.form.submit()" required>
                    <option value="" disabled {{ !$selectedAssessmentId ? 'selected' : '' }}>-- Sila Pilih Pentaksiran --</option>
                    @foreach($assessments ?? [] as $assessment)
                        <option value="{{ $assessment->id }}" {{ $selectedAssessmentId == $assessment->id ? 'selected' : '' }}>
                            {{ $assessment->title ?? 'Pentaksiran' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($selectedAssessmentId)
        @if(empty($subjects) || count($subjects) == 0)
            <div class="alert alert-danger shadow-sm rounded-4 text-center p-4">
                <i class="bi bi-x-circle-fill fs-1 d-block mb-2"></i>
                <h5 class="fw-bold">Tiada Subjek Ditemui!</h5>
                <p class="mb-0">Pangkalan data anda belum mempunyai sebarang subjek KSPK. Sila hubungi Admin.</p>
            </div>
        @else
            <div class="card card-soft">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i> Borang Pentaksiran Kelas</h5>
                </div>
                <div class="card-body p-4">
                    
                    <form action="{{ route('teacher.grading.store') }}" method="POST" id="gradingForm">
                        @csrf
                        <input type="hidden" name="assessment_id" value="{{ $selectedAssessmentId }}">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle table-sm table-grading" style="min-width: 1200px;">
                                <thead class="text-center align-middle">
                                    <tr>
                                        <th rowspan="2" width="15%" class="text-start ps-3 align-middle text-dark">NAMA MURID</th>
                                        <th colspan="{{ count($subjects) }}" class="bg-primary bg-opacity-10 text-primary fw-bold">
                                            TUNJANG KOMPONEN KSPK (TAHAP PENGUASAAN)
                                        </th>
                                        <th rowspan="2" width="18%" class="align-middle text-dark">ULASAN GURU</th>
                                    </tr>
                                    <tr>
                                        @foreach($subjects as $subject)
                                            <th width="6%" title="{{ $subject->subject_name ?? '' }}" class="text-secondary">
                                                {{ $subject->komponen ?? $subject->subject_name ?? 'Subjek' }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                    @php $sId = $student->student_id ?? $student->id; @endphp
                                    <tr>
                                        <td class="fw-bold text-primary ps-3" style="font-size: 0.85rem;">
                                            {{ $student->student_name }}
                                        </td>
                                        
                                        @foreach($subjects as $subject)
                                            @php
                                                $subId = $subject->subject_id ?? $subject->id;
                                                $currentGrade = $results[$sId][$subId] ?? '';
                                            @endphp
                                            <td class="p-1">
                                                <select name="grades[{{ $sId }}][{{ $subId }}]" class="form-select form-select-sm select-grade" required>
                                                    <option value="" class="text-muted">-</option>
                                                    <option value="1" class="tp1" {{ $currentGrade == '1' ? 'selected' : '' }}>TP 1</option>
                                                    <option value="2" class="tp2" {{ $currentGrade == '2' ? 'selected' : '' }}>TP 2</option>
                                                    <option value="3" class="tp3" {{ $currentGrade == '3' ? 'selected' : '' }}>TP 3</option>
                                                </select>
                                            </td>
                                        @endforeach
                                        
                                        <td class="p-1">
                                            <input type="text" name="remarks[{{ $sId }}]" class="form-control form-control-sm border-secondary-subtle" style="font-size: 0.8rem;" placeholder="Catatan/Ulasan..." value="{{ $teacherRemarks[$sId] ?? '' }}">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="{{ count($subjects) + 2 }}" class="text-center py-5 text-muted">Tiada pelajar didaftarkan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(count($students) > 0)
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success px-5 py-2 rounded-pill shadow-sm fw-bold">
                                <i class="bi bi-save2-fill me-2"></i> Simpan Semua Pentaksiran
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection