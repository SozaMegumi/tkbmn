@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Examination Scheduling</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExamModal">
            <i class="bi bi-calendar-plus"></i> Schedule New Exam
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Exam Title</th>
                            <th>Term / Session</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Mid-Year Assessment 2024</td>
                            <td>Term 1</td>
                            <td>15 June 2024</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td><button class="btn btn-sm btn-outline-secondary">Edit</button></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Hafazan Evaluation 1</td>
                            <td>Term 1</td>
                            <td>20 May 2024</td>
                            <td><span class="badge bg-secondary">Completed</span></td>
                            <td><button class="btn btn-sm btn-outline-secondary" disabled>Locked</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Schedule New Assessment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Exam Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., Final Exam 2024" required>
                    </div>
                    <div class="mb-3">
                        <label>Term/Session</label>
                        <select name="term" class="form-select">
                            <option>Term 1</option>
                            <option>Term 2</option>
                            <option>Final</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection