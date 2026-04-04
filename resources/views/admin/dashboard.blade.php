@extends('layouts.app')

@section('content')
    <h2>Dashboard Overview</h2>
    
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card p-3 bg-primary text-white h-100">
                <h3>{{ $totalStudents }}</h3>
                <span>Students Enrolled</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-success text-white h-100">
                <h3>{{ $totalClasses }}</h3>
                <span>Active Classes</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 bg-warning text-dark h-100">
                <h3>0</h3>
                <span>Pending Approvals</span>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <a href="{{ route('admin.enrolment') }}" class="btn btn-outline-primary">Go to Enrolment</a>
                </div>
            </div>
        </div>
    </div>
@endsection