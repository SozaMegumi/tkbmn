@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.pbmt_expenditure_perf') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.expenditure_perf_summary') }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.reports.prestasi.generate') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-2 mb-2"><label class="form-label fw-bold">{{ __('messages.year') }}</label><input type="number" name="year" class="form-control" value="2026" required></div>
                <div class="col-md-2 mb-2"><label class="form-label fw-bold">{{ __('messages.phase') }}</label>
                    <select name="phase" id="fasaSelect" class="form-select"><option value="FASA 1">{{ __('messages.phase_1') }}</option><option value="FASA 2">{{ __('messages.phase_2') }}</option></select>
                </div>
                
                <div class="col-md-4 mb-2">
                    <label class="form-label fw-bold">{{ __('messages.category_class_name') }}</label>
                    <div class="input-group">
                        <select name="kategori" class="form-select bg-light fw-bold text-primary" style="max-width: 110px;">
                            <option value="TABIKA">TABIKA</option>
                            <option value="TASKA">TASKA</option>
                        </select>
                        <input type="text" name="nama_tabika" class="form-control text-uppercase" placeholder="BUSTANUL MAKWAN NAJWA" required>
                    </div>
                </div>
                
                <div class="col-md-2 mb-2"><label class="form-label fw-bold">{{ __('messages.parliament_district') }}</label><input type="text" name="daerah" class="form-control text-uppercase" required></div>
                <div class="col-md-2 mb-2"><label class="form-label fw-bold">{{ __('messages.state') }}</label><input type="text" name="negeri" class="form-control text-uppercase" value="JOHOR" required></div>
            </div>

            <div class="table-responsive mb-4 mt-4">
                <table class="table table-bordered align-middle text-center" style="font-size: 13px;">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2" width="12%" class="align-middle">{{ __('messages.month_item') }}</th>
                            <th colspan="3" class="text-primary">{{ __('messages.allocation_received') }}</th>
                            <th colspan="3" class="text-success">{{ __('messages.actual_expenditure') }}</th>
                            <th rowspan="2" class="align-middle">{{ __('messages.balance_rm') }}</th>
                            <th rowspan="2" class="align-middle" width="15%">{{ __('messages.notes') }}</th>
                        </tr>
                        <tr>
                            <th width="7%">{{ __('messages.children') }}</th><th width="7%">{{ __('messages.days') }}</th><th>{{ __('messages.total_rm') }}</th>
                            <th width="7%">{{ __('messages.children') }}</th><th width="7%">{{ __('messages.days') }}</th><th>{{ __('messages.total_rm') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            // 6 Dynamic Months + 3 Static KEMAS Rows
                            $defaultRows = ['', '', '', '', '', '', 'Kokurikulum', 'Aktiviti PDP Tambahan', 'CUTI PERISTIWA']; 
                        @endphp
                        
                        @foreach($defaultRows as $i => $defaultLabel)
                        <tr>
                            <td>
                                <input type="text" name="row_labels[]" class="form-control form-control-sm text-center fw-bold row-label bg-light" value="{{ $defaultLabel }}" {{ $i > 5 ? 'readonly' : 'readonly' }}>
                            </td>
                            <td><input type="number" name="kanak_p[]" class="form-control form-control-sm text-center"></td>
                            <td><input type="number" name="hari_p[]" class="form-control form-control-sm text-center"></td>
                            <td><input type="number" step="0.01" name="peruntukan[]" class="form-control form-control-sm text-center calc-p"></td>
                            
                            <td><input type="number" name="kanak_b[]" class="form-control form-control-sm text-center"></td>
                            <td><input type="number" name="hari_b[]" class="form-control form-control-sm text-center"></td>
                            <td><input type="number" step="0.01" name="perbelanjaan[]" class="form-control form-control-sm text-center calc-b"></td>
                            
                            <td><input type="text" class="form-control form-control-sm text-center text-danger fw-bold calc-baki" readonly value="0.00"></td>
                            <td><textarea name="catatan[]" class="form-control form-control-sm" rows="1"></textarea></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill"><i class="bi bi-file-earmark-bar-graph me-2"></i> {{ __('messages.generate_perf_report') }}</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3">{{ __('messages.perf_report_history') }}</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr><th>{{ __('messages.generated_date') }}</th><th>{{ __('messages.year_phase') }}</th><th>{{ __('messages.category_class_name') }}</th><th class="text-end">{{ __('messages.action') }}</th></tr>
                </thead>
                <tbody>
                    @forelse($history ?? [] as $record)
                    <tr>
                        <td class="text-muted small">{{ $record->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="fw-bold">{{ $record->year }} - {{ $record->phase }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary me-1">{{ json_decode($record->data_snapshot)->kategori ?? 'TABIKA' }}</span> 
                            {{ json_decode($record->data_snapshot)->nama_tabika ?? '-' }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.reports.prestasi.print', $record->id) }}" target="_blank" class="btn btn-sm btn-dark"><i class="bi bi-printer me-1"></i> {{ __('messages.print') }}</a>
                                <form action="{{ route('admin.reports.prestasi.delete', $record->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_record_confirm') }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">{{ __('messages.no_report_generated') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(session('auto_print'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.open("{{ route('admin.reports.prestasi.print', session('auto_print')) }}", "_blank");
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fasaSelect = document.getElementById('fasaSelect');
        const rowLabels = document.querySelectorAll('.row-label');
        
        const fasa1Months = ['JANUARI', 'FEBRUARI', 'MAC', 'APRIL', 'MEI', 'JUN'];
        const fasa2Months = ['JULAI', 'OGOS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DISEMBER'];

        function updateMonths() {
            const months = fasaSelect.value === 'FASA 1' ? fasa1Months : fasa2Months;
            for(let i = 0; i < 6; i++) {
                rowLabels[i].value = months[i];
            }
        }
        updateMonths();
        fasaSelect.addEventListener('change', updateMonths);

        // LIVE BAKI CALCULATION
        const peruntukanInputs = document.querySelectorAll('.calc-p');
        const perbelanjaanInputs = document.querySelectorAll('.calc-b');
        const bakiInputs = document.querySelectorAll('.calc-baki');

        function calculateBaki(index) {
            let p = parseFloat(peruntukanInputs[index].value) || 0;
            let b = parseFloat(perbelanjaanInputs[index].value) || 0;
            bakiInputs[index].value = (p - b).toFixed(2);
        }

        peruntukanInputs.forEach((input, index) => {
            input.addEventListener('input', () => calculateBaki(index));
        });
        perbelanjaanInputs.forEach((input, index) => {
            input.addEventListener('input', () => calculateBaki(index));
        });
    });
</script>
@endsection