@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease-in-out;
    }
    .card-soft:hover { transform: translateY(-3px); }

    /* Custom Gradients Matching the Image */
    .bg-gradient-purple { background: linear-gradient(135deg, #7b68ee 0%, #9370db 100%); color: white; }
    .bg-gradient-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }

    /* Typography inside Cards */
    .stat-label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
    .display-amount { font-size: 4rem; font-weight: 800; line-height: 1; margin-top: 5px; text-shadow: 0 2px 10px rgba(0,0,0,0.1); }

    /* Giant Watermark Icons */
    .watermark-icon {
        position: absolute;
        right: -15px;
        bottom: -30px;
        font-size: 10rem;
        opacity: 0.15;
        color: white;
        line-height: 1;
        pointer-events: none;
        z-index: 0;
    }

    /* Modern Table & Tabs */
    .card-directory { border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.03); background: #fff; }
    .nav-tabs { border-bottom: 2px solid #f1f5f9; }
    .nav-tabs .nav-link { font-weight: 600; border: none; color: #64748b; padding: 15px 20px; transition: all 0.2s; }
    .nav-tabs .nav-link:hover { color: #0d6efd; background-color: #f8fafc; border-radius: 15px 15px 0 0; }
    .nav-tabs .nav-link.active { color: #0d6efd; background-color: #eff6ff; border-bottom: 3px solid #0d6efd; border-radius: 15px 15px 0 0; }
    .table-hover tbody tr:hover { background-color: #f8fafc; }
</style>

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('messages.user_accounts_directory') }}</h3>
            <p class="text-muted small mb-0">Manage teachers and parents access.</p>
        </div>
        <button class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-lg me-1"></i> {{ __('messages.create_new_account') }}
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-soft bg-gradient-purple p-4 h-100">
                <div class="position-relative" style="z-index: 1;">
                    <span class="stat-label d-flex align-items-center">
                        <i class="bi bi-person-video3 me-2 fs-6"></i> {{ __('messages.total_teachers') }}
                    </span>
                    <h1 class="display-amount">{{ $teachers->count() }}</h1>
                </div>
                <i class="bi bi-person-badge watermark-icon"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-soft bg-gradient-blue p-4 h-100">
                <div class="position-relative" style="z-index: 1;">
                    <span class="stat-label d-flex align-items-center">
                        <i class="bi bi-people-fill me-2 fs-6"></i> {{ __('messages.total_parents') }}
                    </span>
                    <h1 class="display-amount">{{ $parents->count() }}</h1>
                </div>
                <i class="bi bi-people watermark-icon"></i>
            </div>
        </div>
    </div>

    <div class="card card-directory">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill px-4 pt-3" id="userTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers">
                        <i class="bi bi-briefcase-fill me-2"></i>{{ __('messages.teachers_list') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="parents-tab" data-bs-toggle="tab" data-bs-target="#parents">
                        <i class="bi bi-house-door-fill me-2"></i>{{ __('messages.parents_list') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4" id="userTabsContent">
                
                <div class="tab-pane fade show active" id="teachers">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th>{{ __('messages.gender') }}</th>
                                    <th>{{ __('messages.address') }}</th>
                                    <th class="text-end pe-3">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $teacher)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $teacher->full_name }}</td>
                                    <td class="text-muted">{{ $teacher->email }}</td>
                                    <td>{{ $teacher->phone_number ?? '-' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $teacher->gender ?? '-' }}</span></td>
                                    <td title="{{ $teacher->address }}"><small class="text-muted">{{ Str::limit($teacher->address, 20) }}</small></td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-light text-primary rounded-circle me-1 edit-btn" 
                                            data-type="teacher"
                                            data-id="{{ $teacher->teacher_id }}"
                                            data-name="{{ $teacher->full_name }}"
                                            data-email="{{ $teacher->email }}"
                                            data-phone="{{ $teacher->phone_number }}"
                                            data-gender="{{ $teacher->gender }}"
                                            data-address="{{ $teacher->address }}"
                                            data-join="{{ $teacher->join_date }}"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.users.delete', ['id' => $teacher->teacher_id, 'type' => 'teacher']) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_teacher_confirm') }}');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger rounded-circle"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="parents">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th>{{ __('messages.gender') }}</th>
                                    <th class="text-end pe-3">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parents as $parent)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $parent->parent_name }}</td>
                                    <td class="text-muted">{{ $parent->email }}</td>
                                    <td>{{ $parent->phone_number }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $parent->gender ?? '-' }}</span></td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-light text-primary rounded-circle me-1 edit-btn" 
                                            data-type="parent"
                                            data-id="{{ $parent->parent_id }}"
                                            data-name="{{ $parent->parent_name }}"
                                            data-email="{{ $parent->email }}"
                                            data-phone="{{ $parent->phone_number }}"
                                            data-gender="{{ $parent->gender }}"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.users.delete', ['id' => $parent->parent_id, 'type' => 'parent']) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_parent_confirm') }}');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger rounded-circle"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>{{ __('messages.create_account') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="fw-bold text-muted small mb-2">{{ __('messages.account_type') }}</label>
                        <select name="type" class="form-select border-0 bg-light" id="createType" onchange="toggleCreateFields()">
                            <option value="teacher">{{ __('messages.teacher') }}</option>
                            <option value="parent">{{ __('messages.parent') }}</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.full_name') }}</label><input type="text" name="name" class="form-control border-0 bg-light" required></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.email') }}</label><input type="email" name="email" class="form-control border-0 bg-light" required></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.phone') }}</label><input type="text" name="phone" class="form-control border-0 bg-light" required></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.gender') }}</label><select name="gender" class="form-select border-0 bg-light"><option>{{ __('messages.male') }}</option><option>{{ __('messages.female') }}</option></select></div>
                        <div class="col-12 teacher-field"><label class="text-muted small fw-bold">{{ __('messages.address') }}</label><textarea name="address" class="form-control border-0 bg-light" rows="2"></textarea></div>
                        <div class="col-md-6 teacher-field"><label class="text-muted small fw-bold">{{ __('messages.join_date') }}</label><input type="date" name="join_date" class="form-control border-0 bg-light"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">{{ __('messages.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>{{ __('messages.edit_user_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" id="edit_type_input">
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.full_name') }}</label><input type="text" name="name" id="edit_name" class="form-control border-0 bg-light" required></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.email') }}</label><input type="email" name="email" id="edit_email" class="form-control border-0 bg-light" required></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.phone') }}</label><input type="text" name="phone" id="edit_phone" class="form-control border-0 bg-light" required></div>
                        <div class="col-md-6"><label class="text-muted small fw-bold">{{ __('messages.gender') }}</label><select name="gender" id="edit_gender" class="form-select border-0 bg-light"><option value="Male">{{ __('messages.male') }}</option><option value="Female">{{ __('messages.female') }}</option></select></div>
                        
                        <div class="col-12" id="edit_address_div"><label class="text-muted small fw-bold">{{ __('messages.address') }}</label><textarea name="address" id="edit_address" class="form-control border-0 bg-light" rows="2"></textarea></div>
                        <div class="col-md-6" id="edit_join_div"><label class="text-muted small fw-bold">{{ __('messages.join_date') }}</label><input type="date" name="join_date" id="edit_join" class="form-control border-0 bg-light"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">{{ __('messages.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Toggle Fields for Create Modal
    function toggleCreateFields() {
        var type = document.getElementById('createType').value;
        var fields = document.querySelectorAll('.teacher-field');
        fields.forEach(f => f.style.display = (type === 'teacher') ? 'block' : 'none');
    }

    // 2. Handle Edit Button Click
    document.addEventListener("DOMContentLoaded", function() {
        toggleCreateFields(); // Run once on load

        const editBtns = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editUserForm');

        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Get data from button
                const id = this.dataset.id;
                const type = this.dataset.type;

                // Populate Form
                document.getElementById('edit_type_input').value = type;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_email').value = this.dataset.email;
                document.getElementById('edit_phone').value = this.dataset.phone;
                document.getElementById('edit_gender').value = this.dataset.gender;

                // Handle specific Teacher fields
                if(type === 'teacher') {
                    document.getElementById('edit_address_div').style.display = 'block';
                    document.getElementById('edit_join_div').style.display = 'block';
                    document.getElementById('edit_address').value = this.dataset.address;
                    document.getElementById('edit_join').value = this.dataset.join;
                } else {
                    document.getElementById('edit_address_div').style.display = 'none';
                    document.getElementById('edit_join_div').style.display = 'none';
                }

                // Update Form Action URL
                editForm.action = "/admin/users/update/" + id;
            });
        });
    });
</script>
@endsection