@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">{{ __('messages.daily_student_logs') }}</h3>
        
        <form method="GET" action="{{ route('teacher.daily-logs') }}" class="d-flex">
            <input type="date" name="date" class="form-control me-2 shadow-sm border-0" value="{{ $date }}" onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <form action="{{ route('teacher.daily-logs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">{{ __('messages.student_name') }}</th>
                                <th class="py-3">{{ __('messages.mood') }}</th>
                                <th class="py-3">{{ __('messages.meals') }}</th>
                                <th class="text-center py-3">{{ __('messages.napped') ?? 'Tidur?' }}</th>
                                <th class="pe-4 py-3">{{ __('messages.short_note_optional') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php $log = $logs->get($student->student_id); @endphp
                                <tr>
                                    <td class="fw-bold text-primary ps-4">{{ $student->student_name }}</td>
                                    
                                    <td>
                                        <select name="logs[{{ $student->student_id }}][mood]" class="form-select form-select-sm border-secondary-subtle">
                                            <option value="">{{ __('messages.select') }}</option>
                                            <option value="Happy" {{ ($log->mood ?? '') == 'Happy' ? 'selected' : '' }}>😊 {{ __('messages.happy') }}</option>
                                            <option value="Tired" {{ ($log->mood ?? '') == 'Tired' ? 'selected' : '' }}>🥱 {{ __('messages.tired') }}</option>
                                            <option value="Crying" {{ ($log->mood ?? '') == 'Crying' ? 'selected' : '' }}>😢 {{ __('messages.crying') }}</option>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="logs[{{ $student->student_id }}][meals]" class="form-select form-select-sm border-secondary-subtle">
                                            <option value="">{{ __('messages.select') }}</option>
                                            <option value="Ate All" {{ ($log->meals ?? '') == 'Ate All' ? 'selected' : '' }}>🍛 {{ __('messages.ate_all') }}</option>
                                            <option value="Ate Half" {{ ($log->meals ?? '') == 'Ate Half' ? 'selected' : '' }}>🍱 {{ __('messages.ate_half') }}</option>
                                            <option value="Did Not Eat" {{ ($log->meals ?? '') == 'Did Not Eat' ? 'selected' : '' }}>❌ {{ __('messages.did_not_eat') }}</option>
                                        </select>
                                    </td>

                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center mb-0">
                                            <input class="form-check-input border-secondary-subtle" style="width: 1.5rem; height: 1.5rem;" type="checkbox" name="logs[{{ $student->student_id }}][napped]" value="1" {{ ($log->napped ?? false) ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    <td class="pe-4">
                                        <input type="text" name="logs[{{ $student->student_id }}][notes]" class="form-control form-control-sm border-secondary-subtle" placeholder="{{ __('messages.eg_played_well') }}" value="{{ $log->notes ?? '' }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-1 opacity-50 d-block mb-2"></i>
                                        {{ __('messages.tiada_pelajar_didaftarkan') ?? 'Tiada pelajar didaftarkan.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($students) > 0)
                <div class="p-4 bg-light border-top text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm">
                        <i class="bi bi-save2-fill me-2"></i> {{ __('messages.save_daily_logs') }}
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection