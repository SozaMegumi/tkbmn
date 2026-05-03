@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Jana Unjuran Permohonan PBMT</h3>
            <p class="text-muted small mb-0">Pengiraan peruntukan Bantuan Makanan Tambahan.</p>
        </div>
    </div>

    <!-- THE GENERATION FORM -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <form action="{{ route('admin.reports.unjuran.generate') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Tahun</label>
                    <input type="number" name="year" class="form-control" value="2026" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Fasa</label>
                    <select name="phase" id="fasaSelect" class="form-select">
                        <option value="FASA 1">Fasa 1 (Jan - Jun)</option>
                        <option value="FASA 2">Fasa 2 (Jul - Dis)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold text-danger">Baki Tahun Lepas (RM)</label>
                    <input type="number" step="0.01" name="baki_lepas" class="form-control fw-bold" placeholder="100" required>
                </div>
            </div>

            <!-- ... (Maklumat Akaun Section could go here if you need it, based on previous steps) ... -->

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>Bulan</th>
                            <th>Kadar (RM)</th>
                            <th>Bil. Kanak-kanak</th>
                            <th>Hari Sekolah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop 6 times since every phase has exactly 6 months -->
                        @for ($i = 0; $i < 6; $i++)
                        <tr>
                            <td>
                                <input type="text" name="months[]" class="form-control text-center fw-bold month-input bg-light" readonly>
                            </td>
                            <td><input type="number" step="0.01" name="kadar[]" class="form-control text-center" value="4.00" required></td>
                            <td><input type="number" name="kanak[]" class="form-control text-center" required></td>
                            <td><input type="number" name="hari[]" class="form-control text-center" required></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm">
                    <i class="bi bi-calculator me-2"></i> Kira & Jana Dokumen
                </button>
            </div>
        </form>
    </div>

    <!-- THE HISTORY LIST -->
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-3">Sejarah Unjuran Dijana</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Tarikh Dijana</th>
                        <th>Tahun / Fasa</th>
                        <th>Dijana Oleh</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history ?? [] as $record)
                    <tr>
                        <td class="text-muted small">{{ $record->created_at->format('d/m/Y h:i A') }}</td>
                        <td class="fw-bold text-dark">{{ $record->year }} - {{ $record->phase }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $record->generated_by }}</span></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.reports.unjuran.print', $record->id) }}" target="_blank" class="btn btn-sm btn-dark">
                                    <i class="bi bi-printer me-1"></i> Cetak
                                </a>
                                <!-- Fixed the route here -->
                                <form action="{{ route('admin.reports.unjuran.delete', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pasti mahu padam?');">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada unjuran dijana.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JAVASCRIPT FOR AUTO-FILLING MONTHS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fasaSelect = document.getElementById('fasaSelect');
        const monthInputs = document.querySelectorAll('.month-input');

        // Define the months for each phase
        const fasa1Months = ['JANUARI', 'FEBRUARI', 'MAC', 'APRIL', 'MEI', 'JUN'];
        const fasa2Months = ['JULAI', 'OGOS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DISEMBER'];

        function updateMonths() {
            // Check which phase is selected
            const selectedPhase = fasaSelect.value;
            const targetMonths = selectedPhase === 'FASA 1' ? fasa1Months : fasa2Months;

            // Update all 6 inputs
            monthInputs.forEach((input, index) => {
                input.value = targetMonths[index];
            });
        }

        // Run once when the page loads to fill Fasa 1 automatically
        updateMonths();

        // Run every time the user changes the dropdown
        fasaSelect.addEventListener('change', updateMonths);
    });
</script>
@endsection