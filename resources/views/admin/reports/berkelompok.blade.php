@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Rumusan Berkelompok</h3>
            <p class="text-muted small mb-0">Borang Rumusan Berkelompok Tabika/Taska KEMAS (Lampiran 2).</p>
        </div>
    </div>

    <!-- FORM SECTION -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.reports.berkelompok.generate') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-2 mb-2">
                    <label class="form-label fw-bold">Tahun</label>
                    <input type="number" name="year" class="form-control" value="2026" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Fasa</label>
                    <select name="phase" class="form-select">
                        <option value="FASA 1 (JANUARI HINGGA JUN 2026)">Fasa 1 (Jan - Jun)</option>
                        <option value="FASA 2 (JULAI HINGGA DISEMBER 2026)">Fasa 2 (Jul - Dis)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label fw-bold">Parlimen / Daerah</label>
                    <input type="text" name="parlimen" class="form-control text-uppercase" placeholder="Cth: PARIT RAJA" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Negeri</label>
                    <input type="text" name="negeri" class="form-control text-uppercase" value="JOHOR" required>
                </div>
            </div>

            <div class="alert alert-warning border-0 small mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> <strong>Peringatan:</strong> Hanya LIMA (5) Tabika/Taska sahaja dalam satu borang ATAU jumlah tidak melebihi RM 50,000.
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle text-center" style="font-size: 13px;">
                    <thead class="bg-light">
                        <tr>
                            <th width="3%">Bil</th>
                            <th width="15%">No e-Vendor</th>
                            <th width="25%">Nama Tabika / Taska</th>
                            <th width="15%">Nama Bank</th>
                            <th width="15%">No Akaun</th>
                            <th width="15%">Jumlah (RM)</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Strict 5 rows limit -->
                        @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td class="fw-bold">{{ $i + 1 }}</td>
                            <td><input type="text" name="kod_vendor[]" class="form-control form-control-sm text-center text-uppercase"></td>
                            <td><input type="text" name="nama_tabika[]" class="form-control form-control-sm text-uppercase" placeholder="{{ $i == 0 ? 'Wajib diisi...' : 'Pilihan...' }}" {{ $i == 0 ? 'required' : '' }}></td>
                            <td><input type="text" name="nama_bank[]" class="form-control form-control-sm text-center text-uppercase" placeholder="Cth: MAYBANK"></td>
                            <td><input type="text" name="no_akaun[]" class="form-control form-control-sm text-center"></td>
                            <td><input type="number" step="0.01" name="jumlah[]" class="form-control form-control-sm text-center calc-jumlah"></td>
                            <td><input type="text" name="catatan[]" class="form-control form-control-sm"></td>
                        </tr>
                        @endfor
                        <tr>
                            <td colspan="5" class="text-end fw-bold align-middle pe-3">JUMLAH KESELURUHAN (RM)</td>
                            <td>
                                <input type="text" id="totalKeseluruhan" class="form-control form-control-sm text-center text-danger fw-bold" readonly value="0.00">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div id="limitWarning" class="text-danger fw-bold text-end mt-1" style="display: none; font-size: 12px;">
                    <i class="bi bi-exclamation-triangle-fill"></i> AMARAN: Jumlah melebihi had maksimum RM50,000!
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm"><i class="bi bi-file-earmark-spreadsheet me-2"></i> Jana Lampiran 2</button>
            </div>
        </form>
    </div>

    <!-- HISTORY TABLE -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3">Sejarah Rumusan Berkelompok</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr><th>Tarikh Dijana</th><th>Tahun/Fasa</th><th>Parlimen</th><th>Jumlah RM</th><th class="text-end">Tindakan</th></tr>
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
                                <a href="{{ route('admin.reports.berkelompok.print', $record->id) }}" target="_blank" class="btn btn-sm btn-dark"><i class="bi bi-printer me-1"></i> Cetak</a>
                                <form action="{{ route('admin.reports.berkelompok.delete', $record->id) }}" method="POST" onsubmit="return confirm('Pasti mahu padam?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada rumusan dijana.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- AUTO PRINT TRIGGER -->
@if(session('auto_print'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.open("{{ route('admin.reports.berkelompok.print', session('auto_print')) }}", "_blank");
    });
</script>
@endif

<!-- JAVASCRIPT FOR LIVE TOTAL -->
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