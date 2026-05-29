@extends('layouts.app')

@section('content')
<style>
    .card-soft { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); background: #fff; }
    .table-logs th { background-color: #f8fafc; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: middle; }
    .table-logs td { vertical-align: middle; }
</style>

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.daily_student_logs') ?? 'Aktiviti Harian Murid' }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.record_daily_logs') ?? 'Rekod perkembangan emosi, waktu makan dan tidur murid.' }}</p>
        </div>
        
        <form method="GET" action="{{ route('teacher.daily-logs') }}" class="d-flex align-items-center bg-white p-2 rounded-pill shadow-sm border">
            <i class="bi bi-calendar-event text-primary ms-3 me-2"></i>
            <input type="date" name="date" class="form-control border-0 shadow-none fw-bold text-dark" value="{{ $date }}" onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-soft overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-check text-primary me-2"></i> Senarai Murid</h5>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('teacher.daily-logs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle table-logs">
                        <thead class="text-center">
                            <tr>
                                <th class="text-start ps-4">{{ __('messages.student_name') ?? 'Nama Murid' }}</th>
                                <th width="15%">{{ __('messages.mood') ?? 'Emosi (Mood)' }}</th>
                                <th width="15%">{{ __('messages.meals') ?? 'Makan (Meals)' }}</th>
                                <th width="10%" class="text-center">{{ __('messages.napped') ?? 'Tidur?' }}</th>
                                <th width="30%" class="pe-4">{{ __('messages.short_note_optional') ?? 'Catatan Ringkas (Pilihan)' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php $log = $logs->get($student->student_id ?? $student->id); @endphp
                                <tr>
                                    <td class="fw-bold text-primary ps-4">{{ $student->student_name }}</td>
                                    
                                    <td>
                                        <select name="logs[{{ $student->student_id ?? $student->id }}][mood]" class="form-select form-select-sm border-secondary-subtle fw-bold">
                                            <option value="" class="text-muted">{{ __('messages.select') ?? '-- Pilih --' }}</option>
                                            <option value="Happy" {{ ($log->mood ?? '') == 'Happy' ? 'selected' : '' }}>😊 {{ __('messages.happy') ?? 'Gembira' }}</option>
                                            <option value="Tired" {{ ($log->mood ?? '') == 'Tired' ? 'selected' : '' }}>🥱 {{ __('messages.tired') ?? 'Letih' }}</option>
                                            <option value="Crying" {{ ($log->mood ?? '') == 'Crying' ? 'selected' : '' }}>😢 {{ __('messages.crying') ?? 'Menangis' }}</option>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="logs[{{ $student->student_id ?? $student->id }}][meals]" class="form-select form-select-sm border-secondary-subtle fw-bold">
                                            <option value="" class="text-muted">{{ __('messages.select') ?? '-- Pilih --' }}</option>
                                            <option value="Ate All" {{ ($log->meals ?? '') == 'Ate All' ? 'selected' : '' }}>🍛 {{ __('messages.ate_all') ?? 'Makan Habis' }}</option>
                                            <option value="Ate Half" {{ ($log->meals ?? '') == 'Ate Half' ? 'selected' : '' }}>🍱 {{ __('messages.ate_half') ?? 'Makan Separuh' }}</option>
                                            <option value="Did Not Eat" {{ ($log->meals ?? '') == 'Did Not Eat' ? 'selected' : '' }}>❌ {{ __('messages.did_not_eat') ?? 'Tidak Makan' }}</option>
                                        </select>
                                    </td>

                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center mb-0">
                                            <input class="form-check-input border-secondary-subtle shadow-sm" style="width: 1.5rem; height: 1.5rem; cursor: pointer;" type="checkbox" name="logs[{{ $student->student_id ?? $student->id }}][napped]" value="1" {{ ($log->napped ?? false) ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    <td class="pe-4">
                                        <input type="text" name="logs[{{ $student->student_id ?? $student->id }}][notes]" class="form-control form-control-sm border-secondary-subtle" placeholder="{{ __('messages.eg_played_well') ?? 'Cth: Bermain dengan baik hari ini' }}" value="{{ $log->notes ?? '' }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-1 opacity-50 d-block mb-2"></i>
                                        {{ __('messages.tiada_pelajar_didaftarkan') ?? 'Tiada pelajar didaftarkan dalam kelas ini.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($students) > 0)
                <div class="p-4 bg-light border-top text-end mt-3 rounded-bottom-4">
                    <button type="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-save2-fill me-2"></i> {{ __('messages.save_daily_logs') ?? 'Simpan Aktiviti Harian' }}
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection