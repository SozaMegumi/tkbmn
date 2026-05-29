@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-pdf"></i> Pengurusan Kad Laporan (KEMAS)</h3>
        <a href="{{ route('teacher.grading') }}" class="btn btn-outline-secondary rounded-pill fw-bold">
            <i class="bi bi-arrow-left"></i> Kembali ke Penilaian
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger shadow-sm rounded-4">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4 bg-light bg-gradient rounded-4">
            <h5 class="fw-bold mb-3">Pilih Sesi Pentaksiran untuk Dicetak:</h5>
            <div class="row g-3">
                @forelse($assessments as $assessment)
                    <div class="col-md-4">
                        <div class="card border-primary border-opacity-25 shadow-sm h-100 transition-hover">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-journal-text fs-1 text-primary mb-2 d-block"></i>
                                <h5 class="fw-bold text-dark">{{ $assessment->title ?? $assessment->name ?? 'Pentaksiran' }}</h5>
                                <p class="text-muted small mb-3">Cetak Kad Laporan untuk semua murid bagi sesi ini.</p>
                                
                                <a href="{{ route('teacher.report-cards.print', $assessment->id) }}" class="btn btn-primary rounded-pill w-100 fw-bold shadow-sm" target="_blank">
                                    <i class="bi bi-printer-fill me-2"></i> Cetak Borang KEMAS
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted p-5">
                        <i class="bi bi-folder-x fs-1 opacity-50 d-block mb-3"></i>
                        Tiada sesi pentaksiran dijumpai.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i> Senarai Murid dalam Kelas Anda</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @forelse($students as $student)
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center">
                            <div class="bg-white border rounded-circle text-primary fw-bold d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:40px; height:40px;">
                                {{ substr($student->student_name, 0, 1) }}
                            </div>
                            <div class="text-truncate">
                                <span class="fw-bold d-block text-truncate" style="font-size:0.9rem;">{{ $student->student_name }}</span>
                                <small class="text-muted">{{ $student->mykid }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        Tiada pelajar didaftarkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover { transition: transform 0.2s, box-shadow 0.2s; }
    .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border-color: #0d6efd !important; }
</style>
@endsection