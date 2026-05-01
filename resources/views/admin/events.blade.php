@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold text-dark">School Events</h3>
        <button class="btn btn-warning fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="bi bi-megaphone-fill"></i> Post Announcement
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100 overflow-hidden">
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
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark">Upcoming Activities</h6>
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
                                <h6 class="fw-bold mb-1">{{ $event->title }}</h6>
                                <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">{{ $event->description }}</p>
                                
                                <div class="d-flex align-items-center gap-2">
                                    @if($event->theme == 'danger')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">Holiday</span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">Activity</span>
                                    @endif
                                    
                                    @if($event->end_date && $event->end_date != $event->start_date)
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-arrow-right-short"></i> {{ \Carbon\Carbon::parse($event->end_date)->format('d M') }}
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="dropdown ms-2">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <!-- FIXED: Changed event_id to id -->
                                        <button class="dropdown-item edit-btn small" 
                                            data-id="{{ $event->id }}"
                                            data-title="{{ $event->title }}"
                                            data-desc="{{ $event->description }}"
                                            data-start="{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}"
                                            data-end="{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') : '' }}"
                                            data-theme="{{ $event->theme }}"
                                            data-bs-toggle="modal" data-bs-target="#editEventModal">
                                            <i class="bi bi-pencil-square me-2 text-muted"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <!-- FIXED: Changed event_id to id -->
                                        <form action="{{ route('admin.events.delete', $event->id) }}" method="POST" onsubmit="return confirm('Delete this event?');">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger small"><i class="bi bi-trash me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
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

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow" action="{{ route('admin.events.store') }}" method="POST">
            @csrf
            <div class="modal-header bg-warning border-0">
                <h5 class="modal-title text-dark fw-bold">New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Event Title</label>
                    <input type="text" name="title" class="form-control border-0 shadow-sm" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Description</label>
                    <textarea name="description" class="form-control border-0 shadow-sm" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Start Date</label>
                        <input type="date" name="start_date" class="form-control border-0 shadow-sm" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">End Date (Optional)</label>
                        <input type="date" name="end_date" class="form-control border-0 shadow-sm">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Type / Theme</label>
                    <select name="theme" class="form-select border-0 shadow-sm">
                        <option value="primary">Activity (Blue)</option>
                        <option value="danger">Holiday (Red)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm rounded-pill">Post Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow" id="editEventForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Edit Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Event Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control border-0 shadow-sm" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Description</label>
                    <textarea name="description" id="edit_desc" class="form-control border-0 shadow-sm" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">Start Date</label>
                        <input type="date" name="start_date" id="edit_start" class="form-control border-0 shadow-sm" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold text-muted">End Date</label>
                        <input type="date" name="end_date" id="edit_end" class="form-control border-0 shadow-sm">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Type / Theme</label>
                    <select name="theme" id="edit_theme" class="form-select border-0 shadow-sm">
                        <option value="primary">Activity (Blue)</option>
                        <option value="danger">Holiday (Red)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm rounded-pill">Update Event</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editEventForm');

        // FIXED: Generating a safe Base URL for Laragon
        const baseUrl = "{{ url('/admin/events/update') }}";

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_title').value = this.dataset.title;
                document.getElementById('edit_desc').value = this.dataset.desc;
                document.getElementById('edit_start').value = this.dataset.start;
                document.getElementById('edit_end').value = this.dataset.end;
                document.getElementById('edit_theme').value = this.dataset.theme;
                
                // FIXED: Safely append the event ID to the base URL
                editForm.action = baseUrl + "/" + this.dataset.id;
            });
        });
    });
</script>
@endsection