@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Penilaian KSPK</h3>
        <p class="text-muted small mb-0">Sistem Pentaksiran Tabika Kemas (Pengisian Berkelompok)</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-gradient rounded-4">
            <form action="{{ route('teacher.grading') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-bold text-dark mb-0 whitespace-nowrap"><i class="bi bi-journal-check text-primary me-2"></i> Sesi Pentaksiran:</label>
                <select name="assessment_id" class="form-select border-0 shadow-sm w-auto min-w-200" onchange="this.form.submit()" required>
                    <option value="" disabled {{ !$selectedAssessmentId ? 'selected' : '' }}>-- Sila Pilih Pentaksiran --</option>
                    @foreach($assessments as $assessment)
                        <option value="{{ $assessment->id }}" {{ $selectedAssessmentId == $assessment->id ? 'selected' : '' }}>
                            {{ $assessment->title }}
                        </option>
                    @endforeach
                </select>
                @if($selectedAssessmentId)
                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Pentaksiran Aktif</span>
                    <div class="ms-auto text-muted small"><i class="bi bi-info-circle text-primary"></i> Pilih TP 1 hingga TP 3.</div>
                @endif
            </form>
        </div>
    </div>

    @if($selectedAssessmentId)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i> Borang Pentaksiran Kelas</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('teacher.grading.store') }}" method="POST">
                @csrf
                <input type="hidden" name="assessment_id" value="{{ $selectedAssessmentId }}">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle table-sm" style="min-width: 1200px;">
                        <thead class="table-light text-center align-middle" style="font-size: 0.75rem;">
                            <tr>
                                <th rowspan="2" width="15%" class="text-start ps-3 align-middle text-uppercase">NAMA MURID</th>
                                <th colspan="{{ $subjects->count() }}" class="bg-primary bg-opacity-10 text-primary fw-bold text-uppercase">
                                    TUNJANG KOMPONEN KSPK (TAHAP PENGUASAAN)
                                </th>
                                <th rowspan="2" width="18%" class="align-middle text-uppercase">ULASAN GURU</th>
                            </tr>
                            <tr>
                                @foreach($subjects as $subject)
                                    <th width="6%" title="{{ $subject->subject_name }}" class="text-uppercase text-secondary">
                                        {{ $subject->komponen ?? $subject->subject_name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td class="fw-bold text-primary ps-3" style="font-size: 0.85rem;">{{ $student->student_name }}</td>
                                
                                @foreach($subjects as $subject)
                                    @php
                                        $currentGrade = $results[$student->student_id][$subject->subject_id] ?? '';
                                    @endphp
                                    <td class="p-1">
                                        <select name="grades[{{ $student->student_id }}][{{ $subject->subject_id }}]" class="form-select form-select-sm border-secondary-subtle fw-bold text-center" style="font-size: 0.8rem;">
                                            <option value="" class="text-muted">-</option>
                                            <option value="1" class="text-danger" {{ $currentGrade == '1' ? 'selected' : '' }}>TP 1</option>
                                            <option value="2" class="text-warning text-dark" {{ $currentGrade == '2' ? 'selected' : '' }}>TP 2</option>
                                            <option value="3" class="text-success" {{ $currentGrade == '3' ? 'selected' : '' }}>TP 3</option>
                                        </select>
                                    </td>
                                @endforeach
                                
                                <td class="p-1">
                                    <input type="text" name="remarks[{{ $student->student_id }}]" class="form-control form-control-sm border-secondary-subtle" style="font-size: 0.8rem;" placeholder="Catatan/Ulasan..." value="{{ $teacherRemarks[$student->student_id] ?? '' }}">
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $subjects->count() + 2 }}" class="text-center py-4 text-muted">Tiada pelajar didaftarkan di dalam kelas anda.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-5 rounded-pill shadow-sm fw-bold">
                        <i class="bi bi-save2-fill me-2"></i> Simpan Semua Pentaksiran
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="text-center p-5 text-muted bg-white rounded-4 shadow-sm border border-light-subtle">
        <i class="bi bi-arrow-up-circle fs-1 d-block mb-3 text-primary opacity-50"></i>
        <h5 class="fw-bold">Sila Pilih Pentaksiran</h5>
        <p>Pilih sesi pentaksiran di atas untuk mula memasukkan gred KSPK murid secara berkelompok.</p>
    </div>
    @endif

</div>
@endsection