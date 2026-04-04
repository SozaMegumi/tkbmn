@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">New Assessment</div>
            <div class="card-body">
                <form action="{{ route('admin.hafazan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Student</label>
                        <select name="student_id" class="form-select">
                            @foreach($students as $student)
                                <option value="{{ $student->student_id }}">{{ $student->student_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Surah</label>
                        <select name="surah_name" class="form-select">
                            <option>Al-Fatihah</option><option>An-Nas</option><option>Al-Falaq</option><option>Al-Ikhlas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Performance</label>
                        <select name="grade" class="form-select">
                            <option value="Mumtaz">Excellent (Mumtaz)</option>
                            <option value="Jayyid">Good (Jayyid)</option>
                            <option value="Maqbul">Pass (Maqbul)</option>
                        </select>
                    </div>
                    <button class="btn btn-warning w-100">Save Record</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">History</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Student</th><th>Surah</th><th>Grade</th></tr></thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->created_at)->format('d M') }}</td>
                            <td>{{ $record->student_name }}</td>
                            <td>{{ $record->surah_name }}</td>
                            <td><span class="badge bg-secondary">{{ $record->grade_id }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection