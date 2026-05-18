@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Pengurusan Hafazan</h3>
        <p class="text-muted small mb-0">Penilaian bacaan dan hafazan surah murid-murid secara berkelompok.</p>
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

    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm border border-light-subtle" id="hafazanTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-bold px-4" id="bulk-tab" data-bs-toggle="pill" data-bs-target="#bulk" type="button" role="tab">
                <i class="bi bi-ui-checks-grid me-2"></i> Penilaian Berkelompok (Bulk)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-bold px-4" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab">
                <i class="bi bi-clock-history me-2"></i> Sejarah Rekod
            </button>
        </li>
    </ul>

    <div class="tab-content" id="hafazanTabsContent">
        
        <div class="tab-pane fade show active" id="bulk" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('teacher.hafazan.bulk_store') }}" method="POST">
                        @csrf
                        
                        <div class="d-flex flex-wrap align-items-center gap-3 bg-light p-3 rounded-4 border border-light-subtle mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <label class="fw-bold text-dark mb-0 whitespace-nowrap"><i class="bi bi-calendar-check text-primary me-1"></i> Tarikh Penilaian:</label>
                                <input type="date" name="date" class="form-control bg-white border-0 shadow-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="text-muted small ms-auto border-start ps-3 border-2 border-primary">
                                <i class="bi bi-info-circle-fill text-primary"></i> <b>Nota:</b> Hanya baris yang mempunyai <b>Nama Surah</b> akan disimpan. Biarkan kosong untuk murid yang tidak disemak hari ini.
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light text-center small text-muted">
                                    <tr>
                                        <th width="18%" class="text-start ps-3">Nama Murid</th>
                                        <th width="20%">Surah <span class="text-danger">*</span></th>
                                        <th width="8%">Juzuk</th>
                                        <th width="15%">Ayat / M.S.</th>
                                        <th width="15%">Kelancaran <span class="text-danger">*</span></th>
                                        <th width="24%">Catatan Tajwid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                    <tr>
                                        <td class="fw-bold small text-primary ps-3">{{ $student->student_name }}</td>
                                        <td>
                                            <input type="text" name="records[{{ $student->student_id }}][surah_name]" class="form-control form-control-sm border-secondary-subtle" placeholder="Cth: Al-Fatihah">
                                        </td>
                                        <td>
                                            <input type="number" name="records[{{ $student->student_id }}][juz_number]" class="form-control form-control-sm border-secondary-subtle text-center" min="1" max="30">
                                        </td>
                                        <td>
                                            <input type="text" name="records[{{ $student->student_id }}][verse_range]" class="form-control form-control-sm border-secondary-subtle" placeholder="Cth: 1-5">
                                        </td>
                                        <td>
                                            <select name="records[{{ $student->student_id }}][fluency_level]" class="form-select form-select-sm border-secondary-subtle fw-bold">
                                                <option value="Cemerlang" class="text-success">Cemerlang</option>
                                                <option value="Baik" class="text-primary">Baik</option>
                                                <option value="Sederhana" class="text-warning">Sederhana</option>
                                                <option value="Lemah" class="text-danger">Lemah</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="records[{{ $student->student_id }}][tajweed_notes]" class="form-control form-control-sm border-secondary-subtle" placeholder="Catatan...">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Tiada pelajar dalam kelas anda.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success px-5 rounded-pill shadow-sm fw-bold">
                                <i class="bi bi-save2-fill me-2"></i> Simpan Semua Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i> Sejarah Rekod Terkini</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th>Tarikh</th>
                                    <th>Nama Murid</th>
                                    <th>Surah</th>
                                    <th>Ayat</th>
                                    <th>Tahap Kelancaran</th>
                                    <th>Catatan Tajwid & Juz</th>
                                    <th class="text-end">Padam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                <tr>
                                    <td><span class="badge bg-light text-dark border"><i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($record->date_recorded)->format('d M Y') }}</span></td>
                                    <td class="fw-bold text-primary">{{ $record->student->student_name ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $record->surah }}</span>
                                    </td>
                                    <td>{{ $record->verses ?? 'Keseluruhan' }}</td>
                                    <td>
                                        @if(in_array($record->status, ['Cemerlang', 'Baik']))
                                            <span class="badge bg-success rounded-pill px-3">{{ $record->status }}</span>
                                        @elseif($record->status == 'Sederhana')
                                            <span class="badge bg-warning text-dark rounded-pill px-3">{{ $record->status }}</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3">{{ $record->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $record->remarks }}">
                                        {{ $record->remarks ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('teacher.hafazan.delete', $record->id) }}" method="POST" onsubmit="return confirm('Padam rekod ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-journal-x fs-3 d-block mb-2 opacity-50"></i> Belum ada rekod hafazan direkodkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection