@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Laporan Perkembangan Murid (Grading)</h3>

    <div class="card shadow-sm border-0 mb-4 p-3 bg-light">
        <form method="GET" action="{{ route('teacher.grading') }}" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label class="form-label fw-bold">Pilih Penggal (Select Term)</label>
                <select name="assessment_id" class="form-select border-secondary" required>
                    <option value="">-- Sila Pilih Penggal --</option>
                    @foreach($assessments as $assessment)
                        @php $assId = $assessment->assessment_id ?? $assessment->id; @endphp
                        <option value="{{ $assId }}" {{ $selectedAssessment == $assId ? 'selected' : '' }}>
                            {{ $assessment->name ?? $assessment->assessment_name ?? $assessment->term_name ?? $assessment->title ?? 'Term ' . $assId }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100 fw-bold"><i class="bi bi-people-fill"></i> Load Class List</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold shadow-sm"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif

    @if(!$selectedAssessment)
        <div class="alert alert-secondary text-center py-5 shadow-sm border-0">
            <i class="bi bi-calendar3 fs-1 d-block mb-3 text-muted"></i>
            <h5>Sila pilih Penggal di atas</h5>
            <p class="text-muted mb-0">Senarai kelas anda akan dipaparkan selepas penggal dipilih.</p>
        </div>

    @elseif($selectedAssessment && !$selectedStudentId)
        <div class="card shadow-sm border-0 border-top border-primary border-4">
            <div class="card-header bg-white pt-3 pb-2">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-person-lines-fill"></i> Senarai Nama Kelas (Class Roster)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Nama Murid</th>
                            <th>No. MyKid</th>
                            <th>Jantina</th>
                            <th class="text-end pe-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-uppercase">{{ $student->student_name }}</td>
                                <td>{{ $student->mykid ?? '-' }}</td>
                                <td>{{ $student->gender ?? '-' }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('teacher.grading', ['assessment_id' => $selectedAssessment, 'student_id' => $student->student_id]) }}" class="btn btn-sm btn-primary fw-bold px-3">
                                        <i class="bi bi-pencil-square"></i> Isi Borang
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($selectedAssessment && $selectedStudentId)
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="fw-bold text-primary mb-0"><i class="bi bi-file-earmark-text"></i> Borang Penilaian Murid</h5>
            
            <a href="{{ route('teacher.grading', ['assessment_id' => $selectedAssessment]) }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Senarai Kelas
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4 border-top border-success border-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><th style="width: 120px;">Nama Murid</th><td>: <span class="fw-bold text-uppercase">{{ $selectedStudent->student_name }}</span></td></tr>
                            <tr><th>No. MyKid</th><td>: {{ $selectedStudent->mykid ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><th style="width: 120px;">Kelas</th><td>: <span class="text-uppercase">{{ $selectedStudent->classroom->class_name ?? 'TABIKA KEMAS' }}</span></td></tr>
                            <tr><th>Tarikh</th><td>: {{ \Carbon\Carbon::now()->format('d F Y') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info py-2 mb-3 shadow-sm border-0">
            <strong class="d-block mb-1"><i class="bi bi-info-circle"></i> Panduan Tahap Penguasaan (Mastery Levels):</strong>
            <div class="d-flex flex-wrap gap-4" style="font-size: 0.9rem;">
                <div><span class="badge bg-danger">Tahap 1</span> Belum Menguasai (Perlu bimbingan)</div>
                <div><span class="badge bg-warning text-dark">Tahap 2</span> Sedang Maju (Boleh buat dengan sikit bimbingan)</div>
                <div><span class="badge bg-success">Tahap 3</span> Telah Menguasai (Boleh buat dengan baik/konsisten)</div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-5">
            <form action="{{ route('teacher.grading.store') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $selectedStudentId }}">
                <input type="hidden" name="assessment_id" value="{{ $selectedAssessment }}">

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">KOMPONEN</th>
                                <th style="width: 25%;">KEMAHIRAN</th>
                                <th style="width: 20%;">TAFSIRAN (Tahap)</th>
                                <th>ULASAN GURU (Catatan)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedSubjects as $komponen => $subjects)
                                @foreach($subjects as $index => $subject)
                                    @php $result = $results->get($subject->subject_id); @endphp
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ count($subjects) }}" class="fw-bold text-center bg-light text-uppercase" style="vertical-align: middle;">
                                                {{ $komponen }}
                                            </td>
                                        @endif
                                        
                                        <td class="fw-bold text-secondary" style="font-size: 0.95rem;">{{ $subject->subject_name }}</td>
                                        
                                        <td>
                                            <select name="grades[{{ $subject->subject_id }}][mastery_level]" class="form-select form-select-sm border-secondary shadow-none">
                                                <option value="">-- Pilih Tahap --</option>
                                                <option value="1" {{ ($result->mastery_level ?? '') == 1 ? 'selected' : '' }}>Tahap 1</option>
                                                <option value="2" {{ ($result->mastery_level ?? '') == 2 ? 'selected' : '' }}>Tahap 2</option>
                                                <option value="3" {{ ($result->mastery_level ?? '') == 3 ? 'selected' : '' }}>Tahap 3</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="text" name="grades[{{ $subject->subject_id }}][remarks]" class="form-control form-control-sm shadow-none" placeholder="Ulasan guru (pilihan)..." value="{{ $result->teacher_remarks ?? '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <button type="submit" class="btn btn-success fw-bold px-5 shadow-sm"><i class="bi bi-save"></i> Simpan Laporan (Save)</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection