@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft { border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: transform 0.2s; overflow: hidden; }
    .card-soft:hover { transform: translateY(-3px); }
    .bg-gradient-primary { background: linear-gradient(135deg, #007bff 0%, #00d2ff 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #20c997 0%, #84fab0 100%); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); color: white; }
    .stat-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;}
    .display-amount { font-size: 2.5rem; font-weight: 800; line-height: 1;}
</style>

<div class="container-fluid pb-5">

    <div class="card card-soft bg-primary text-white mb-4" style="background: url('https://www.transparenttextures.com/patterns/cubes.png'), linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">{{ __('messages.welcome_teacher', ['name' => auth()->user()->full_name ?? 'Cikgu Munirah']) }}</h3>
                <p class="mb-0 opacity-75">
                    <i class="bi bi-shop-window me-1"></i> {{ __('messages.assigned_class') }} <strong>{{ $assignedClass ?? 'Kelas Mawar (6 Years)' }}</strong>
                </p>
            </div>
            <div class="text-end d-none d-md-block">
                <h5 class="fw-bold mb-0">{{ now()->format('l, d M Y') }}</h5>
                <p class="mb-0 opacity-75">Tabika Kemas Bustanul Makwan Najwa</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-soft bg-gradient-primary h-100 p-4 position-relative">
                <span class="stat-label mb-2"><i class="bi bi-people-fill me-2"></i> {{ __('messages.my_students') }}</span>
                <h1 class="display-amount mb-0">{{ $totalStudents ?? 25 }}</h1>
                <i class="bi bi-person-bounding-box position-absolute text-white" style="font-size: 6rem; opacity: 0.15; right: -10px; bottom: -10px;"></i>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-soft {{ ($attendanceMarked ?? false) ? 'bg-gradient-success' : 'bg-gradient-warning' }} h-100 p-4 position-relative">
                <span class="stat-label mb-2"><i class="bi bi-calendar-check me-2"></i> {{ __('messages.todays_attendance') }}</span>
                <h3 class="fw-bold mb-0 mt-2">
                    {{ ($attendanceMarked ?? false) ? __('messages.completed') : __('messages.action_required') }}
                </h3>
                <i class="bi bi-clipboard-check position-absolute text-white" style="font-size: 6rem; opacity: 0.15; right: -10px; bottom: -10px;"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-soft bg-white border border-light h-100 p-4 position-relative">
                <span class="stat-label text-muted mb-2"><i class="bi bi-chat-dots-fill text-primary me-2"></i> {{ __('messages.unread_messages') }}</span>
                <h1 class="display-amount text-dark mb-0">{{ $unreadMessages ?? 0 }}</h1>
                <a href="{{ route('teacher.communication') }}" class="stretched-link"></a> 
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-soft bg-white h-100">
                <div class="card-header bg-white p-4 border-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task text-primary me-2"></i> {{ __('messages.my_pending_tasks') }}</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        
                        @if(!($attendanceMarked ?? false))
                        <li class="list-group-item p-4 d-flex justify-content-between align-items-center border-top border-bottom-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
                                    <i class="bi bi-person-check-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ __('messages.mark_daily_attendance') }}</h6>
                                    <p class="text-muted small mb-0">{{ __('messages.please_mark_attendance', ['class' => $assignedClass ?? 'your class']) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('teacher.attendance') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">{{ __('messages.do_it_now') }}</a>
                        </li>
                        @endif

                        <li class="list-group-item p-4 d-flex justify-content-between align-items-center border-top border-bottom-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle me-3">
                                    <i class="bi bi-journal-check fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ __('messages.kspk_student_grading') }}</h6>
                                    <p class="text-muted small mb-0">{{ __('messages.update_tahap_penguasaan') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('teacher.grading') }}" class="btn btn-outline-info fw-bold rounded-pill px-4">{{ __('messages.key_in_marks') }}</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft bg-white h-100">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">{{ __('messages.school_announcements') }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        
                        @forelse($events ?? [] as $event)
                        <div class="list-group-item p-4 border-top border-bottom-0">
                            <div class="d-flex align-items-start">
                                <div class="text-center me-3" style="min-width: 45px;">
                                    <h4 class="mb-0 fw-bold text-{{ $event->theme == 'danger' ? 'danger' : 'primary' }}">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</h4>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</small>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $event->title }}</h6>
                                    <span class="badge bg-{{ $event->theme == 'danger' ? 'danger' : 'primary' }} bg-opacity-10 text-{{ $event->theme == 'danger' ? 'danger' : 'primary' }} rounded-pill">{{ $event->theme == 'danger' ? __('messages.holiday') : __('messages.activity') }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted small">{{ __('messages.no_upcoming_events') }}</div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection