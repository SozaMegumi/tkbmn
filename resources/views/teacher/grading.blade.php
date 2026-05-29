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
    
    @media print {
        body { background-color: #fff; }
        .d-print-none { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        select { -webkit-appearance: none; -moz-appearance: none; appearance: none; border: none !important; background: transparent !important; text-align: center;}
        input { border: none !important; background: transparent !important; }
    }
</style>

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.penilaian_kspk') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.sistem_pentaksiran_kemas') }}</p>
        </div>
        </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i> Ralat Penyimpanan!</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

    <div class="card card-soft mb-4 d-print-none">
        <div class="card-body p-4 bg-light bg-gradient rounded-4 border border-light-subtle">
            <form action="{{ route('teacher.grading') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-bold text-dark mb-0 whitespace-nowrap"><i class="bi bi-journal-check text-primary me-2"></i> {{ __('messages.sesi_pentaksiran') }}</label>
                <select name="assessment_id" class="form-select border-0 shadow-sm w-auto min-w-200" onchange="this.form.submit()" required>
                    <option value="" disabled {{ !$selectedAssessmentId ? 'selected' : '' }}>{{ __('messages.sila_pilih_pentaksiran') }}</option>
                    @foreach($assessments as $assessment)
                        <option value="{{ $assessment->id }}" {{ $selectedAssessmentId == $assessment->id ? 'selected' : '' }}>
                            {{ $assessment->title }}
                        </option>
                    @endforeach
                </select>
                @if($selectedAssessmentId)
                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> {{ __('messages.pentaksiran_aktif') }}</span>
                    <div class="ms-auto text-muted small"><i class="bi bi-info-circle text-primary"></i> {{ __('messages.pilih_tp_1_hingga_3') }}</div>
                @endif
            </form>
        </div>
    </div>

    @if($selectedAssessmentId)
    <div class="card card-soft">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i> {{ __('messages.borang_pentaksiran_kelas') }}</h5>
            
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill fw-bold shadow-sm d-print-none" onclick="window.print()">
                <i class="bi bi-printer-fill me-1"></i> Cetak Jadual
            </button>
        </div>
        <div class="card-body p-4">
            
            <form action="{{ route('teacher.grading.store') }}" method="POST">
                @csrf
                <input type="hidden" name="assessment_id" value="{{ $selectedAssessmentId }}">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle table-sm table-grading" style="min-width: 1200px;">
                        <thead class="text-center align-middle">
                            <tr>
                                <th rowspan="2" width="15%" class="text-start ps-3 align-middle text-dark">{{ __('messages.nama_murid') }}</th>
                                <th colspan="{{ $subjects->count() }}" class="bg-primary bg-opacity-10 text-primary fw-bold">
                                    {{ __('messages.tunjang_komponen_kspk') }}
                                </th>
                                <th rowspan="2" width="18%" class="align-middle text-dark">{{ __('messages.ulasan_guru') }}</th>
                            </tr>
                            <tr>
                                @foreach($subjects as $subject)
                                    <th width="6%" title="{{ $subject->subject_name }}" class="text-secondary">
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
                                        // Semak jika gred sedia ada wujud di pangkalan data
                                        $currentGrade = $results[$student->student_id][$subject->subject_id] ?? '';
                                    @endphp
                                    <td class="p-1">
                                        <select name="grades[{{ $student->student_id }}][{{ $subject->subject_id }}]" class="form-select form-select-sm select-grade" required>
                                            <option value="" class="text-muted">-</option>
                                            <option value="1" class="tp1" {{ $currentGrade == '1' ? 'selected' : '' }}>TP 1</option>
                                            <option value="2" class="tp2" {{ $currentGrade == '2' ? 'selected' : '' }}>TP 2</option>
                                            <option value="3" class="tp3" {{ $currentGrade == '3' ? 'selected' : '' }}>TP 3</option>
                                        </select>
                                    </td>
                                @endforeach
                                
                                <td class="p-1">
                                    <input type="text" name="remarks[{{ $student->student_id }}]" class="form-control form-control-sm border-secondary-subtle" style="font-size: 0.8rem;" placeholder="{{ __('messages.catatan_ulasan_placeholder') }}" value="{{ $teacherRemarks[$student->student_id] ?? '' }}">
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $subjects->count() + 2 }}" class="text-center py-5 text-muted"><i class="bi bi-folder-x fs-1 opacity-50 d-block mb-2"></i> {{ __('messages.tiada_pelajar_didaftarkan') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($students) > 0)
                <div class="text-end mt-4 d-print-none">
                    <button type="submit" class="btn btn-success px-5 py-2 rounded-pill shadow-sm fw-bold" onclick="return confirmSave()">
                        <i class="bi bi-save2-fill me-2"></i> {{ __('messages.simpan_semua_pentaksiran') }}
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
    @else
    <div class="text-center p-5 text-muted bg-white rounded-4 shadow-sm border border-light-subtle d-print-none">
        <i class="bi bi-arrow-up-circle fs-1 d-block mb-3 text-primary opacity-50"></i>
        <h5 class="fw-bold">{{ __('messages.sila_pilih_pentaksiran_tajuk') }}</h5>
        <p>{{ __('messages.pilih_sesi_mula_masuk') }}</p>
    </div>
    @endif

</div>

<script>
    // FUNGSI UNTUK MEMASTIKAN TIADA KOTAK KOSONG DITINGGALKAN SEBELUM SAVE
    function confirmSave() {
        let selects = document.querySelectorAll('.select-grade');
        let uncompleted = 0;
        
        selects.forEach(function(select) {
            if(select.value === "") {
                uncompleted++;
                select.style.borderColor = "red";
                select.style.boxShadow = "0 0 5px rgba(220, 53, 69, 0.5)";
            } else {
                select.style.borderColor = "#e2e8f0";
                select.style.boxShadow = "none";
            }
        });

        if (uncompleted > 0) {
            alert("Terdapat " + uncompleted + " kotak penilaian KSPK yang belum diisi. Sila pastikan setiap murid mendapat sekurang-kurangnya TP1.");
            return false; // Halang form dari hantar
        }

        return confirm("Adakah anda pasti mahu menyimpan semua markah ini ke dalam pangkalan data?");
    }

    // FUNGSI UNTUK MENUKAR WARNA DROPDOWN MENGIKUT GRED SECARA LIVE
    document.addEventListener("DOMContentLoaded", function() {
        const selects = document.querySelectorAll('.select-grade');
        
        function updateColor(select) {
            select.style.color = "black";
            select.style.backgroundColor = "white";
            
            if(select.value === "1") {
                select.style.color = "#dc3545"; // Red
                select.style.backgroundColor = "#fff5f5";
            } else if(select.value === "2") {
                select.style.color = "#d97706"; // Dark orange
                select.style.backgroundColor = "#fffbeb";
            } else if(select.value === "3") {
                select.style.color = "#198754"; // Green
                select.style.backgroundColor = "#f0fdf4";
            }
        }

        selects.forEach(function(select) {
            updateColor(select); // Run on load
            select.addEventListener('change', function() {
                updateColor(this); // Run on change
            });
        });
    });
</script>
@endsection