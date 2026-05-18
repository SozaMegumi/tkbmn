@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft {
        border: none; 
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04); 
        background: #fff;
        overflow: hidden;
    }
    
    .notice-card { 
        border-left: 5px solid #0d6efd; 
        transition: all 0.2s; 
        background: #f8fafc; 
        border-radius: 15px; 
    }
    .notice-card.urgent { 
        border-left-color: #dc3545; 
        background: #fff5f5; 
    }
    .notice-card:hover { 
        transform: translateX(5px); 
        background: #fff; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
    }
</style>

<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">School Calendar & Notices</h3>
            <p class="text-muted small mb-0">Overview of academic events and official announcements.</p>
        </div>
        <div class="text-end">
            <span class="d-block fw-bold text-dark"><i class="bi bi-person-circle text-primary me-2"></i> {{ auth()->guard('parent')->user()->parent_name ?? 'Parent Portal' }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-soft h-100">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-google text-primary me-2"></i> Official Live Calendar</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2">Real-time Sync</span>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9" style="min-height: 600px;">
                        <iframe src="https://calendar.google.com/calendar/embed?src=d4d0cb8880723983e580a8443ce0efae85a048aef89020e4a02e5ecc0d7fa407%40group.calendar.google.com&ctz=Asia%2FKuala_Lumpur"
                                style="border: 0" 
                                width="100%" 
                                height="600" 
                                frameborder="0" 
                                scrolling="no">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft h-100" style="border-top: 5px solid #0d6efd;">
                <div class="card-header bg-white p-4 border-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-megaphone me-2 text-warning"></i> Upcoming Activities</h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div class="list-group list-group-flush px-2 pb-3">
                        @forelse($upcomingEvents ?? [] as $event)
                        <div class="notice-card p-3 mb-3 mx-2 {{ $event->theme == 'danger' ? 'urgent' : '' }}">
                            <div class="d-flex align-items-start">
                                
                                <div class="text-center me-3" style="min-width: 50px;">
                                    <h3 class="mb-0 fw-bold text-{{ $event->theme == 'danger' ? 'danger' : 'primary' }}">
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
                                    </h3>
                                    <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('M') }}
                                    </small>
                                </div>

                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $event->title }}</h6>
                                    <p class="text-muted small mb-2">{{ $event->description ?? 'No additional details provided.' }}</p>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        @if($event->theme == 'danger')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">Holiday</span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">Activity</span>
                                        @endif
                                        
                                        @if($event->end_date && $event->end_date != $event->start_date)
                                            <small class="text-muted fw-bold" style="font-size: 0.75rem;">
                                                <i class="bi bi-arrow-right-short"></i> {{ \Carbon\Carbon::parse($event->end_date)->format('d M') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                        @empty
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 opacity-25"></i>
                            <p class="mt-3 mb-0 small fw-bold">No upcoming events.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection