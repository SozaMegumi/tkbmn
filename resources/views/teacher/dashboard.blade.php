@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body p-4">
                <h2>Welcome back, {{ Auth::guard('teacher')->user()->full_name }}!</h2>
                <p class="mb-0">You are logged in to the Teacher Portal.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <a href="{{ route('teacher.attendance') }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 hover-card">
                <div class="card-body text-center p-5">
                    <h1 class="text-primary"><i class="bi bi-calendar-check"></i></h1>
                    <h4>Attendance</h4>
                    <p class="text-muted">Mark daily attendance for your class.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('teacher.grading') }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 hover-card">
                <div class="card-body text-center p-5">
                    <h1 class="text-success"><i class="bi bi-clipboard-data"></i></h1>
                    <h4>Grading</h4>
                    <p class="text-muted">Input marks for exams and hafazan.</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="{{ route('teacher.communication') }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm border-0 hover-card">
                <div class="card-body text-center p-5">
                    <h1 class="text-warning"><i class="bi bi-chat-dots"></i></h1>
                    <h4>Messages</h4>
                    <p class="text-muted">Chat with parents regarding updates.</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection