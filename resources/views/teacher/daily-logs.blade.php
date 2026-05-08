@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Daily Student Logs</h3>
        
        <form method="GET" action="{{ route('teacher.daily-logs') }}" class="d-flex">
            <input type="date" name="date" class="form-control me-2" value="{{ $date }}" onchange="this.form.submit()">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <form action="{{ route('teacher.daily-logs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Mood</th>
                            <th>Meals</th>
                            <th class="text-center">Napped?</th>
                            <th>Short Note (Optional)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php $log = $logs->get($student->student_id); @endphp
                            <tr>
                                <td class="fw-bold">{{ $student->student_name }}</td>
                                
                                <td>
                                    <select name="logs[{{ $student->student_id }}][mood]" class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Happy" {{ ($log->mood ?? '') == 'Happy' ? 'selected' : '' }}>😊 Happy</option>
                                        <option value="Tired" {{ ($log->mood ?? '') == 'Tired' ? 'selected' : '' }}>🥱 Tired</option>
                                        <option value="Crying" {{ ($log->mood ?? '') == 'Crying' ? 'selected' : '' }}>😢 Crying</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="logs[{{ $student->student_id }}][meals]" class="form-select form-select-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Ate All" {{ ($log->meals ?? '') == 'Ate All' ? 'selected' : '' }}>🍛 Ate All</option>
                                        <option value="Ate Half" {{ ($log->meals ?? '') == 'Ate Half' ? 'selected' : '' }}>🍱 Ate Half</option>
                                        <option value="Did Not Eat" {{ ($log->meals ?? '') == 'Did Not Eat' ? 'selected' : '' }}>❌ Did Not Eat</option>
                                    </select>
                                </td>

                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="logs[{{ $student->student_id }}][napped]" value="1" {{ ($log->napped ?? false) ? 'checked' : '' }}>
                                </td>

                                <td>
                                    <input type="text" name="logs[{{ $student->student_id }}][notes]" class="form-control form-control-sm" placeholder="e.g. Played well today" value="{{ $log->notes ?? '' }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 bg-light text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4">Save Daily Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection