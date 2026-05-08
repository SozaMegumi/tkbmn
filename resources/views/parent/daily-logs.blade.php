@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="bi bi-calendar-heart"></i> Daily Logs</h3>
        <form method="GET" action="{{ route('parent.daily-logs') }}">
            <input type="date" name="date" class="form-control bg-white shadow-sm border-0" value="{{ $date }}" onchange="this.form.submit()">
        </form>
    </div>

    <div class="row">
        @foreach($students as $student)
            @php $log = $logs->get($student->student_id); @endphp
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header bg-primary text-white py-3 rounded-top-4">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2"></i> {{ $student->student_name }}</h5>
                    </div>
                    <div class="card-body">
                        @if($log)
                            <div class="d-flex align-items-center mb-3">
                                <div class="fs-1 me-3">
                                    @if($log->mood == 'Happy') 😊 
                                    @elseif($log->mood == 'Tired') 🥱 
                                    @elseif($log->mood == 'Crying') 😢 
                                    @else 😐 @endif
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0">Mood Today</h6>
                                    <h5 class="fw-bold">{{ $log->mood ?? 'Not recorded' }}</h5>
                                </div>
                            </div>
                            
                            <hr class="text-muted">

                            <div class="row text-center my-3">
                                <div class="col-6 border-end">
                                    <h6 class="text-muted mb-1"><i class="bi bi-cup-hot"></i> Meals</h6>
                                    <span class="fw-bold {{ $log->meals == 'Did Not Eat' ? 'text-danger' : 'text-success' }}">{{ $log->meals ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted mb-1"><i class="bi bi-moon-stars"></i> Napped?</h6>
                                    <span class="fw-bold {{ $log->napped ? 'text-success' : 'text-warning' }}">{{ $log->napped ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>

                            @if($log->notes)
                                <div class="alert alert-info mt-3 border-0 shadow-sm">
                                    <strong>Teacher's Note:</strong> {{ $log->notes }}
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-journal-x fs-1 opacity-50 mb-2 d-block"></i>
                                No daily log recorded for this date.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection