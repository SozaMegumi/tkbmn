@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <span>Input Student Marks</span>
        <select class="form-select form-select-sm w-auto">
            <option>Mid-Year Assessment</option>
            <option>Final Exam</option>
        </select>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.grading.store') }}" method="POST">
            @csrf
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th width="150">Bahasa Melayu</th>
                        <th width="150">English</th>
                        <th width="150">Mathematics</th>
                        <th width="150">Jawi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td><strong>{{ $student->student_name }}</strong></td>
                        <td><input type="number" name="marks[{{$student->id}}][bm]" class="form-control" max="100" placeholder="0-100"></td>
                        <td><input type="number" name="marks[{{$student->id}}][bi]" class="form-control" max="100" placeholder="0-100"></td>
                        <td><input type="number" name="marks[{{$student->id}}][math]" class="form-control" max="100" placeholder="0-100"></td>
                        <td><input type="number" name="marks[{{$student->id}}][jawi]" class="form-control" max="100" placeholder="0-100"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">Save All Marks</button>
            </div>
        </form>
    </div>
</div>
@endsection