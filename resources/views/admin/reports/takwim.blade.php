@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.generate_school_calendar') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.select_period_fill_info') }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.reports.takwim.generate') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">{{ __('messages.calendar_year') }}</label>
                    <select name="year" class="form-select border-0 shadow-sm bg-light">
                        <option value="2025">2025</option>
                        <option value="2026" selected>2026</option>
                        <option value="2027">2027</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary">{{ __('messages.period') }}</label>
                    <select name="duration" id="takwimDuration" class="form-select border-0 shadow-sm bg-primary bg-opacity-10 text-primary fw-bold">
                        <option value="6">{{ __('messages.6_months') }}</option>
                        <option value="12" selected>{{ __('messages.12_months') }}</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle text-center">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2" class="align-middle" width="5%">{{ __('messages.no') }}</th>
                            <th rowspan="2" class="align-middle" width="10%">{{ __('messages.month') }}</th>
                            <th colspan="2" class="text-primary">TABIKA</th>
                            <th colspan="2" class="text-success">TASKA</th>
                        </tr>
                        <tr>
                            <th width="15%">{{ __('messages.school_days') }}</th>
                            <th>{{ __('messages.notes_holidays') }}</th>
                            <th width="15%">{{ __('messages.school_days') }}</th>
                            <th>{{ __('messages.notes_holidays') }}</th>
                        </tr>
                    </thead>
                    <tbody id="takwimTableBody">
                        @php
                            $months = [
                                'JANUARI', 'FEBRUARI', 'MAC', 'APRIL', 'MEI', 'JUN', 
                                'JULAI', 'OGOS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DISEMBER'
                            ];
                        @endphp
                        
                        @foreach($months as $index => $month)
                        <tr class="month-row" data-index="{{ $index }}">
                            <td class="fw-bold text-muted" style="font-size: 0.2rem;">{{ $index + 1 }}</td>
                            <td class="fw-bold" style="font-size: 1.1rem;">{{ $month }}
                                <input type="hidden" name="months[]" value="{{ $month }}" class="month-input">
                            </td>
                            
                            <td>
                                <input type="number" name="tabika_days[]" class="form-control form-control-lg text-center fw-bold month-input" required>
                            </td>
                            <td>
                                <textarea name="tabika_notes[]" class="form-control month-input" rows="2" placeholder="{{ __('messages.state_holiday') }}"></textarea>
                            </td>

                            <td>
                                <input type="number" name="taska_days[]" class="form-control form-control-lg text-center fw-bold month-input" required>
                            </td>
                            <td>
                                <textarea name="taska_notes[]" class="form-control month-input" rows="2" placeholder="{{ __('messages.state_holiday') }}"></textarea>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm">
                    <i class="bi bi-file-earmark-check me-2"></i> {{ __('messages.generate_official_doc') }}
                </button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3">{{ __('messages.calendar_history') }}</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('messages.generated_date') }}</th>
                        <th>{{ __('messages.year') }}</th>
                        <th>{{ __('messages.generated_by') }}</th>
                        <th class="text-end">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history ?? [] as $record)
                    <tr>
                        <td class="text-muted small">{{ $record->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="fw-bold text-dark">{{ $record->year }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $record->generated_by }}</span></td>
                        
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.reports.takwim.print', $record->id) }}" target="_blank" class="btn btn-sm btn-dark">
                                    <i class="bi bi-printer me-1"></i> {{ __('messages.print') }}
                                </a>
                                
                                <form action="{{ route('admin.reports.takwim.delete', $record->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_record_warning') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">{{ __('messages.no_calendar_generated') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const durationSelect = document.getElementById('takwimDuration');
        const rows = document.querySelectorAll('.month-row');

        function toggleMonths() {
            const duration = parseInt(durationSelect.value); // 6 or 12

            rows.forEach((row) => {
                const index = parseInt(row.getAttribute('data-index'));
                const inputs = row.querySelectorAll('.month-input');

                if (index < duration) {
                    // Show row and ENABLE inputs
                    row.style.display = '';
                    inputs.forEach(input => input.disabled = false);
                } else {
                    // Hide row and DISABLE inputs (so they aren't sent to the controller)
                    row.style.display = 'none';
                    inputs.forEach(input => input.disabled = true);
                }
            });
        }

        // Run on page load
        toggleMonths();

        // Run whenever the dropdown changes
        durationSelect.addEventListener('change', toggleMonths);
    });
</script>
@endsection