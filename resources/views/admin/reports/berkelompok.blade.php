@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.group_summary') }}</h3>
            <p class="text-muted small mb-0">{{ __('messages.summary_of_claims') }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.reports.berkelompok.generate') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-2 mb-2">
                    <label class="form-label fw-bold">{{ __('messages.year') }}</label>
                    <input type="number" name="year" class="form-control" value="2026" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">{{ __('messages.phase') }}</label>
                    <select name="phase" class="form-select">
                        <option value="FASA 1 (JANUARI HINGGA JUN 2026)">{{ __('messages.phase_1') }}</option>
                        <option value="FASA 2 (JULAI HINGGA DISEMBER 2026)">{{ __('messages.phase_2') }}</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label fw-bold">{{ __('messages.parliament_district') }}</label>
                    <input type="text" name="parlimen" class="form-control text-uppercase" placeholder="Cth: PARIT RAJA" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">{{ __('messages.state') }}</label>
                    <input type="text" name="negeri" class="form-control text-uppercase" value="JOHOR" required>
                </div>
            </div>

            <div class="alert alert-warning border-0 small mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> <strong>{{ __('messages.warning_limit') }}</strong>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle text-center" style="font-size: 13px;">
                    <thead class="bg-light">
                        <tr>
                            <th width="3%">{{ __('messages.no') }}</th>
                            <th width="15%">{{ __('messages.vendor_no') }}</th>
                            <th width="25%">{{ __('messages.tabika_taska_name') }}</th>
                            <th width="15%">{{ __('messages.bank_name') }}</th>
                            <th width="15%">{{ __('messages.account_no') }}</th>
                            <th width="15%">{{ __('messages.total_rm') }}</th>
                            <th>{{ __('messages.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td class="fw-bold">{{ $i + 1 }}</td>
                            <td><input type="text" name="kod_vendor[]" class="form-control form-control-sm text-center text-uppercase"></td>
                            <td><input type="text" name="nama_tabika[]" class="form-control form-control-sm text-uppercase" placeholder="{{ $i == 0 ? __('messages.mandatory_fill') : __('messages.optional_fill') }}" {{ $i == 0 ? 'required' : '' }}></td>
                            <td><input type="text" name="nama_bank[]" class="form-control form-control-sm text-center text-uppercase" placeholder="Cth: MAYBANK"></td>
                            <td><input type="text" name="no_akaun[]" class="form-control form-control-sm text-center"></td>
                            <td><input type="number" step="0.01" name="jumlah[]" class="form-control form-control-sm text-center calc-jumlah"></td>
                            <td><input type="text" name="catatan[]" class="form-control form-control-sm"></td>
                        </tr>
                        @endfor
                        <tr>
                            <td colspan="5" class="text-end fw-bold align-middle pe-3">{{ __('messages.overall_total') }}</td>
                            <td>
                                <input type="text" id="totalKeseluruhan" class="form-control form-control-sm text-center text-danger fw-bold" readonly value="0.00">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div id="limitWarning" class="text-danger fw-bold text-end mt-1" style="display: none; font-size: 12px;">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ __('messages.limit_exceeded_warning') }}
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm"><i class="bi bi-file-earmark-spreadsheet me-2"></i> {{ __('messages.generate_attachment_2') }}</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3">{{ __('messages.group_summary_history') }}</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr><th>{{ __('messages.generated_date') }}</th><th>{{ __('messages.year_phase') }}</th><th>{{ __('messages.parliament') }}</th><th>{{ __('messages.total_amount_rm') }}</th><th class="text-end">{{ __('messages.action') }}</th></tr>
                </thead>
                <tbody>
                    @forelse($history ?? [] as $record)
                    <tr>
                        <td class="text-muted small">{{ $record->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="fw-bold">{{ $record->year }} - {{ explode('(', $record->phase)[0] }}</td>
                        <td class="text-uppercase">{{ json_decode($record->data_snapshot)->parlimen ?? '-' }}</td>
                        <td class="fw-bold text-success">RM {{ number_format(json_decode($record->data_snapshot)->jumlah_keseluruhan ?? 0, 2) }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.reports.berkelompok.print', $record->id) }}" target="_blank" class="btn btn-sm btn-dark"><i class="bi bi-printer me-1"></i> {{ __('messages.print') }}</a>
                                <form action="{{ route('admin.reports.berkelompok.delete', $record->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_record_confirm') }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_summary_generated') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(session('auto_print'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.open("{{ route('admin.reports.berkelompok.print', session('auto_print')) }}", "_blank");
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahInputs = document.querySelectorAll('.calc-jumlah');
        const totalOutput = document.getElementById('totalKeseluruhan');
        const limitWarning = document.getElementById('limitWarning');

        function calculateTotal() {
            let total = 0;
            jumlahInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            
            totalOutput.value = total.toFixed(2);

            // Show warning if over 50k
            if(total > 50000) {
                limitWarning.style.display = 'block';
            } else {
                limitWarning.style.display = 'none';
            }
        }

        jumlahInputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });
    });
</script>
@endsection