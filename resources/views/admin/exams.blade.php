@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Pengurusan Pentaksiran</h3>
            <p class="text-muted small mb-0">Buka atau tutup sesi pentaksiran (Report Card) untuk guru-guru.</p>
        </div>
    </div>

    <div class="row">
        <!-- LEFT: Form to Create New Assessment -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Buka Sesi Baharu</h6>
                <form action="{{ route('admin.exams.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Pentaksiran</label>
                        <input type="text" name="title" class="form-control" placeholder="Cth: Pentaksiran Penggal 1 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tarikh Mula</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tarikh Akhir</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Status Sistem</label>
                        <select name="status" class="form-select text-success fw-bold">
                            <option value="Buka">BUKA (Guru boleh key-in)</option>
                            <option value="Tutup">TUTUP (Kunci sistem)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">Sahkan Sesi</button>
                </form>
            </div>
        </div>

        <!-- RIGHT: Table of All Assessments -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3">Senarai Sesi Pentaksiran</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Sesi</th>
                                <th>Tempoh</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assessments ?? [] as $exam)
                            <tr>
                                <td class="fw-bold">{{ $exam->title }}</td>
                                <td class="small text-muted">
                                    {{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}
                                </td>
                                <td>
                                    @if($exam->status == 'Buka')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">BUKA</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3">TUTUP</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <!-- Quick Toggle Form -->
                                        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <!-- Hidden inputs to keep required fields happy -->
                                            <input type="hidden" name="title" value="{{ $exam->title }}">
                                            <input type="hidden" name="start_date" value="{{ $exam->start_date }}">
                                            <input type="hidden" name="end_date" value="{{ $exam->end_date }}">
                                            <!-- Toggle logic -->
                                            <input type="hidden" name="status" value="{{ $exam->status == 'Buka' ? 'Tutup' : 'Buka' }}">
                                            
                                            <button type="submit" class="btn btn-sm {{ $exam->status == 'Buka' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="Tukar Status">
                                                <i class="bi {{ $exam->status == 'Buka' ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.exams.delete', $exam->id) }}" method="POST" onsubmit="return confirm('Pasti mahu padam sesi ini? Semua markah guru untuk sesi ini mungkin terjejas.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada sesi pentaksiran dicipta.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection