@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft {
        border: none;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        overflow: hidden;
    }
    .card-soft:hover { transform: translateY(-3px); }

    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #343a40; }

    .stat-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; font-weight: 600;}
    .display-amount { font-size: 3rem; font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.1); line-height: 1;}
    
    .table-custom thead th {
        background: #f8f9fa; color: #8898aa; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 1px; padding: 15px; border: none;
    }
    .table-custom tbody td {
        padding: 16px 15px; vertical-align: middle; 
        border-bottom: 1px solid #f0f0f0; font-size: 0.95rem;
    }

    .action-box {
        border-radius: 15px;
        padding: 20px 10px;
        text-align: center;
        text-decoration: none;
        display: block;
        transition: all 0.2s;
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .action-box:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        color: #0d6efd;
    }
    .action-box i { font-size: 1.8rem; margin-bottom: 10px; display: block;}
</style>

<div class="container-fluid pb-5">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">{{ __('messages.admin_dashboard_title') }}</h3>
        <p class="text-muted small mb-0">{{ __('messages.admin_welcome') }}</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card card-soft bg-gradient-primary h-100 p-4">
                <div class="d-flex flex-column justify-content-center position-relative h-100">
                    <span class="stat-label mb-2"><i class="bi bi-people-fill me-2"></i> {{ __('messages.total_students') }}</span>
                    <h1 class="display-amount mb-0">{{ $totalStudents ?? 1 }}</h1>
                    <i class="bi bi-person-badge position-absolute text-white" style="font-size: 7rem; opacity: 0.15; right: -10px; bottom: -20px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card card-soft bg-gradient-success h-100 p-4">
                <div class="d-flex flex-column justify-content-center position-relative h-100">
                    <span class="stat-label mb-2"><i class="bi bi-easel-fill me-2"></i> {{ __('messages.active_classes') }}</span>
                    <h1 class="display-amount mb-0">{{ $totalClasses ?? 4 }}</h1>
                    <i class="bi bi-book position-absolute text-white" style="font-size: 7rem; opacity: 0.15; right: -10px; bottom: -20px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card card-soft bg-gradient-warning h-100 p-4">
                <div class="d-flex flex-column justify-content-center position-relative h-100">
                    <span class="stat-label mb-2"><i class="bi bi-hourglass-split me-2"></i> {{ __('messages.pending_approvals') }}</span>
                    <h1 class="display-amount mb-0">{{ $pendingApprovals ?? 0 }}</h1>
                    <i class="bi bi-file-earmark-check position-absolute text-dark" style="font-size: 7rem; opacity: 0.1; right: -10px; bottom: -20px;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="card card-soft bg-white mb-4">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ __('messages.weekly_attendance') }}</h6>
                        <small class="text-muted">{{ __('messages.last_5_days') }}</small>
                    </div>
                </div>
                <div class="card-body p-4 pt-0" style="position: relative; height: 300px;">
                    <canvas id="dashboardAttendanceChart"></canvas>
                </div>
            </div>

            <div class="card card-soft bg-white">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">{{ __('messages.recent_enrollments') }}</h6>
                    <a href="{{ route('admin.enrolment') }}" class="btn btn-sm btn-light">{{ __('messages.view_all') }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('messages.student_name') }}</th>
                                <th>{{ __('messages.target_class') }}</th>
                                <th>{{ __('messages.date_applied') }}</th>
                                <th class="text-end pe-4">{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEnrollments ?? [] as $enrolment)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $enrolment->student_name ?? $enrolment->name }}</td>
                                <td>{{ $enrolment->target_class ?? $enrolment->class_name ?? 'N/A' }}</td>
                                <td class="text-muted small">{{ $enrolment->created_at ? $enrolment->created_at->format('d M Y') : 'N/A' }}</td>
                                <td class="text-end pe-4">
                                    {{-- Logik Status Dibaiki --}}
                                    @if(in_array(strtolower($enrolment->status ?? ''), ['enrolled', 'approved', 'success']) || ($enrolment->target_class && $enrolment->target_class != 'N/A'))
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">{{ __('messages.enrolled') }}</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill">{{ __('messages.pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">{{ __('messages.no_recent_enroll') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft bg-white mb-4">
                <div class="card-header bg-white p-4 border-0">
                    <h6 class="fw-bold mb-0 text-dark">{{ __('messages.quick_actions') }}</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row g-3">
                        <div class="col-6"><a href="{{ route('admin.enrolment') }}" class="action-box"><i class="bi bi-person-plus text-primary"></i>{{ __('messages.new_student') }}</a></div>
                        <div class="col-6"><a href="{{ route('admin.finance') }}" class="action-box"><i class="bi bi-wallet2 text-success"></i>{{ __('messages.record_fee') }}</a></div>
                        <div class="col-6"><a href="{{ route('admin.events') }}" class="action-box"><i class="bi bi-megaphone text-warning"></i>{{ __('messages.post_event') }}</a></div>
                        <div class="col-6"><a href="{{ route('admin.reports') }}" class="action-box"><i class="bi bi-file-earmark-bar-graph text-info"></i>{{ __('messages.reports') }}</a></div>
                    </div>
                </div>
            </div>

            <div class="card card-soft bg-white">
                <div class="card-header bg-white p-4 border-0">
                    <h6 class="fw-bold mb-0 text-dark">{{ __('messages.alerts_tasks') }}</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($alerts ?? [] as $alert)
                        <li class="list-group-item p-4 d-flex align-items-start border-top border-bottom-0">
                            <i class="bi {{ $alert['icon'] ?? 'bi-info-circle-fill' }} text-{{ $alert['color'] ?? 'info' }} fs-5 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">{{ $alert['title'] }}</h6>
                                <p class="mb-0 text-muted small">{{ $alert['message'] }}</p>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item p-4 text-center text-muted border-top border-bottom-0"><p class="mb-0 small">{{ __('messages.no_alerts') }}</p></li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('dashboardAttendanceChart').getContext('2d');
        
        // Memastikan variable ada fallback kalau dari controller tiada
        const labels = {!! json_encode($attendanceLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']) !!};
        const dataValues = {!! json_encode($attendanceData ?? [0, 0, 0, 0, 0]) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Overall Attendance (%)',
                    data: dataValues, 
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4, 
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: false, min: 0, max: 100, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection