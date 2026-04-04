@extends('layouts.app')
@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-success text-white d-flex justify-content-between">
        <h5 class="mb-0">Daily Attendance</h5>
        <span>{{ date('d M Y') }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.attendance.store') }}" method="POST">
            @csrf
            <table class="table align-middle">
                <thead><tr><th>Student</th><th>Status</th><th>Remarks</th></tr></thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->student_name }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="att[{{$student->student_id}}]" id="p_{{$student->student_id}}" value="present" checked>
                                <label class="btn btn-outline-success btn-sm" for="p_{{$student->student_id}}">Present</label>

                                <input type="radio" class="btn-check" name="att[{{$student->student_id}}]" id="a_{{$student->student_id}}" value="absent">
                                <label class="btn btn-outline-danger btn-sm" for="a_{{$student->student_id}}">Absent</label>
                            </div>
                        </td>
                        <td><input type="text" class="form-control form-control-sm" placeholder="Reason if absent..."></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-primary">Save Today's Log</button>
        </form>
    </div>
</div>
@endsection