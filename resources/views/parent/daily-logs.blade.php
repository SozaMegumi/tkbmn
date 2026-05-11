@extends('layouts.app')

@section('content')
<style>
    .card-dashboard { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: transform 0.2s; }
</style>

@php
    // Fetch attendances dynamically based on the selected date
    $attendances = \App\Models\Attendance::whereIn('student_id', $students->pluck('student_id'))
                             ->where('date', $date)
                             ->get()
                             ->keyBy('student_id');
@endphp

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Aktiviti Harian</h3>
            <p class="text-muted small mb-0">Daily Activity Logs</p>
        </div>
    </div>

    <div class="card card-dashboard mb-4 bg-white">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('parent.daily-logs') }}" class="d-flex align-items-center gap-3">
                <label class="fw-bold text-muted mb-0"><i class="bi bi-calendar-event me-2"></i> Pilih Tarikh (Select Date):</label>
                <input type="date" name="date" class="form-control bg-light border-0 shadow-sm rounded-pill px-4 w-auto" 
                       value="{{ $date }}" max="{{ \Carbon\Carbon::today()->toDateString() }}" 
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    @forelse($students as $child)
        @php
            $status = $attendances->get($child->student_id)->status ?? 'Belum Ditanda';
            $log = $logs->get($child->student_id);
            $attRecord = $attendances->get($child->student_id);
            $time = $attRecord ? \Carbon\Carbon::parse($attRecord->created_at)->format('h:i A') : '--:--';
        @endphp
        
        <h5 class="fw-bold mb-3 mt-4 text-uppercase"><i class="bi bi-person-bounding-box text-primary me-2"></i> {{ $child->student_name }}</h5>
        
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-dashboard h-100 p-3 bg-white">
                    <div class="card-header bg-white border-0 p-0 mb-3">
                        <h6 class="fw-bold text-dark mb-0">Status Kehadiran</h6>
                    </div>
                    <div class="card-body p-0 d-flex flex-column justify-content-center align-items-center text-center">
                        @if($status == 'Hadir')
                            <div class="bg-success bg-opacity-10 text-success rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
                                <i class="bi bi-check2-circle fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-success mb-1">Hadir</h4>
                        @elseif($status == 'Tak Hadir')
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
                                <i class="bi bi-x-circle fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-danger mb-1">Tak Hadir</h4>
                        @else
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px;">
                                <i class="bi bi-dash-circle fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-secondary mb-1">{{ $status }}</h4>
                        @endif
                        <p class="text-muted mb-0 mt-2 fw-bold"><i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $time }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-dashboard h-100 p-3 bg-white">
                    <div class="card-header bg-white border-0 p-0 mb-3">
                        <h6 class="fw-bold text-dark mb-0">Laporan Aktiviti</h6>
                    </div>
                    <div class="card-body p-0">
                        @if($log)
                            <div class="row g-3 mb-3">
                                <div class="col-sm-4">
                                    <div class="bg-light p-3 rounded-4 h-100 text-center">
                                        <i class="bi bi-cup-hot-fill text-warning fs-2 mb-2 d-block"></i>
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">Meals</small>
                                        <span class="fw-bold text-dark">{{ $log->meals ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="bg-light p-3 rounded-4 h-100 text-center">
                                        <i class="bi bi-moon-stars-fill text-primary fs-2 mb-2 d-block"></i>
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">Nap</small>
                                        <span class="fw-bold text-dark">{{ $log->napped ? 'Yes' : 'No' }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="bg-light p-3 rounded-4 h-100 text-center">
                                        @if($log->mood == 'Happy')
                                            <i class="bi bi-emoji-smile-fill text-success fs-2 mb-2 d-block"></i>
                                        @elseif($log->mood == 'Sad')
                                            <i class="bi bi-emoji-frown-fill text-danger fs-2 mb-2 d-block"></i>
                                        @else
                                            <i class="bi bi-emoji-dizzy-fill text-warning fs-2 mb-2 d-block"></i>
                                        @endif
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.75rem;">Mood</small>
                                        <span class="fw-bold text-dark">{{ $log->mood ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-4 border-start border-primary border-4">
                                <small class="text-primary fw-bold d-block mb-1">Catatan Guru (Teacher's Note):</small>
                                <span class="text-dark fst-italic">{{ $log->notes ?? 'Tiada catatan tambahan.' }}</span>
                            </div>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 py-4 text-muted">
                                <i class="bi bi-journal-x fs-1 mb-2 opacity-50"></i>
                                <p class="mb-0">Laporan belum dimuat naik untuk tarikh ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center py-4 border-0 rounded-4">
            Tiada murid yang berdaftar di bawah akaun ini.
        </div>
    @endforelse

</div>
@endsection