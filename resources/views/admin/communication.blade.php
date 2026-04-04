@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-info text-white">Post Announcement</div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" class="form-control" placeholder="e.g., School Closed">
                    </div>
                    <div class="mb-3">
                        <label>Message</label>
                        <textarea class="form-control" rows="4" placeholder="Type details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Send To:</label>
                        <select class="form-select">
                            <option>All Parents</option>
                            <option>Tabika A Parents</option>
                            <option>Tabika B Parents</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary w-100" onclick="alert('Message Sent!')">
                        <i class="bi bi-send"></i> Send Blast
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">School Events Calendar</div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active" aria-current="true">
                        <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Sports Day</h5>
                        <small>3 days ago</small>
                        </div>
                        <p class="mb-1">Annual sports day at the community field.</p>
                        <small>Status: Completed</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Parent-Teacher Meeting</h5>
                        <small class="text-muted">Next Week</small>
                        </div>
                        <p class="mb-1">Discussion on mid-year performance.</p>
                        <small class="text-warning">Upcoming</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">School Maintenance</h5>
                        <small class="text-muted">Next Month</small>
                        </div>
                        <p class="mb-1">School will be closed for repainting.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection