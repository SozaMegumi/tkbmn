@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold text-dark">School Events & Calendar</h3>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100 overflow-hidden rounded-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-google text-primary me-2"></i> Official Live Calendar</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">Synced</span>
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
            <div class="card shadow-sm border-0 h-100 rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-stars text-warning me-2"></i> Upcoming Activities</h6>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($events as $event)
                        <div class="list-group-item p-4 d-flex align-items-start border-bottom-0 border-top">
                            
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
                        @empty
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 opacity-50"></i>
                            <p class="mt-3 mb-0 small">No upcoming events posted.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection