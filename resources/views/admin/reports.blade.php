@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">System Reports</h3>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">Attendance Summary</div>
                <div class="card-body text-center">
                    <h1 class="display-4 text-primary">92%</h1>
                    <p class="lead">Average Monthly Attendance</p>
                    <hr>
                    <button class="btn btn-outline-info">Download Full Report</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">Academic Performance</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Passed Hafazan
                            <span class="badge bg-success rounded-pill">15 Students</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Needs Improvement
                            <span class="badge bg-danger rounded-pill">3 Students</span>
                        </li>
                    </ul>
                    <div class="mt-3 text-center">
                        <button class="btn btn-outline-warning">View Grades</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection