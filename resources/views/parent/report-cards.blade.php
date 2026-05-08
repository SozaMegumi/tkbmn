@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4 text-primary"><i class="bi bi-file-earmark-pdf"></i> Academic Report Cards</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @foreach($students as $student)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i> {{ $student->student_name }}</h5>
                        
                        <ul class="list-group list-group-flush">
                            @foreach($assessments as $assessment)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-medium">{{ $assessment->name }}</span>
                                    <a href="{{ route('parent.report-cards.download', ['student_id' => $student->student_id, 'assessment_id' => $assessment->id]) }}" class="btn btn-sm btn-outline-danger fw-bold rounded-pill">
                                        <i class="bi bi-download"></i> Download PDF
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection