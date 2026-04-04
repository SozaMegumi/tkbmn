@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">School Events</h3>
        <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="bi bi-megaphone-fill"></i> Post Announcement
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-muted">Upcoming Activities</h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($events as $event)
                <div class="list-group-item p-4 d-flex align-items-start">
                    <div class="text-center me-4">
                        <h2 class="mb-0 fw-bold text-{{ $event->theme == 'danger' ? 'danger' : 'primary' }}">
                            {{ $event->start_date->format('d') }}
                        </h2>
                        <small class="text-uppercase fw-bold text-muted">{{ $event->start_date->format('M') }}</small>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $event->title }}</h5>
                        <p class="text-muted mb-2">{{ $event->description }}</p>
                        
                        @if($event->theme == 'danger')
                            <span class="badge bg-danger">Holiday</span>
                        @else
                            <span class="badge bg-primary">Activity</span>
                        @endif
                        
                        @if($event->end_date && $event->end_date != $event->start_date)
                            <small class="text-muted ms-2">
                                <i class="bi bi-clock"></i> Until {{ $event->end_date->format('d M') }}
                            </small>
                        @endif
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item edit-btn" 
                                    data-id="{{ $event->event_id }}"
                                    data-title="{{ $event->title }}"
                                    data-desc="{{ $event->description }}"
                                    data-start="{{ $event->start_date->format('Y-m-d') }}"
                                    data-end="{{ $event->end_date ? $event->end_date->format('Y-m-d') : '' }}"
                                    data-theme="{{ $event->theme }}"
                                    data-bs-toggle="modal" data-bs-target="#editEventModal">
                                    Edit
                                </button>
                            </li>
                            <li>
                                <form action="{{ route('admin.events.delete', $event->event_id) }}" method="POST" onsubmit="return confirm('Delete this event?');">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                @empty
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">No upcoming events posted.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.events.store') }}" method="POST">
            @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Event Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label>End Date (Optional)</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label>Type / Theme</label>
                    <select name="theme" class="form-select">
                        <option value="primary">Activity (Blue)</option>
                        <option value="danger">Holiday (Red)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning fw-bold">Post Event</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="editEventForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Event Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="edit_start" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="edit_end" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label>Type / Theme</label>
                    <select name="theme" id="edit_theme" class="form-select">
                        <option value="primary">Activity (Blue)</option>
                        <option value="danger">Holiday (Red)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update Event</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editEventForm');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Populate Modal
                document.getElementById('edit_title').value = this.dataset.title;
                document.getElementById('edit_desc').value = this.dataset.desc;
                document.getElementById('edit_start').value = this.dataset.start;
                document.getElementById('edit_end').value = this.dataset.end;
                document.getElementById('edit_theme').value = this.dataset.theme;

                // Update Action URL
                editForm.action = "/admin/events/update/" + this.dataset.id;
            });
        });
    });
</script>
@endsection