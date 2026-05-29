@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><h6 class="text-uppercase mb-1">{{ __('messages.total_teachers') }}</h6><h2 class="mb-0">{{ $teachers->count() }}</h2></div>
                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><h6 class="text-uppercase mb-1">{{ __('messages.total_parents') }}</h6><h2 class="mb-0">{{ $parents->count() }}</h2></div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">{{ __('messages.user_accounts_directory') }}</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-lg"></i> {{ __('messages.create_new_account') }}
            </button>
        </div>
        
        <div class="card-body">
            <ul class="nav nav-tabs nav-fill mb-3" id="userTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers">{{ __('messages.teachers_list') }}</button></li>
                <li class="nav-item"><button class="nav-link" id="parents-tab" data-bs-toggle="tab" data-bs-target="#parents">{{ __('messages.parents_list') }}</button></li>
            </ul>

            <div class="tab-content" id="userTabsContent">
                
                <div class="tab-pane fade show active" id="teachers">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>{{ __('messages.name') }}</th><th>{{ __('messages.email') }}</th><th>{{ __('messages.phone') }}</th><th>{{ __('messages.gender') }}</th><th>{{ __('messages.address') }}</th><th class="text-end">{{ __('messages.action') }}</th></tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $teacher)
                                <tr>
                                    <td class="fw-bold">{{ $teacher->full_name }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>{{ $teacher->phone_number ?? '-' }}</td>
                                    <td>{{ $teacher->gender ?? '-' }}</td>
                                    <td title="{{ $teacher->address }}">{{ Str::limit($teacher->address, 15) }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary edit-btn" 
                                            data-type="teacher"
                                            data-id="{{ $teacher->teacher_id }}"
                                            data-name="{{ $teacher->full_name }}"
                                            data-email="{{ $teacher->email }}"
                                            data-phone="{{ $teacher->phone_number }}"
                                            data-gender="{{ $teacher->gender }}"
                                            data-address="{{ $teacher->address }}"
                                            data-join="{{ $teacher->join_date }}"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.users.delete', ['id' => $teacher->teacher_id, 'type' => 'teacher']) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_teacher_confirm') }}');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>{{ __('messages.name') }}</th><th>{{ __('messages.email') }}</th><th>{{ __('messages.phone') }}</th><th>{{ __('messages.gender') }}</th><th class="text-end">{{ __('messages.action') }}</th></tr>
                            </thead>
                            <tbody>
                                @foreach($parents as $parent)
                                <tr>
                                    <td class="fw-bold">{{ $parent->parent_name }}</td>
                                    <td>{{ $parent->email }}</td>
                                    <td>{{ $parent->phone_number }}</td>
                                    <td>{{ $parent->gender ?? '-' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary edit-btn" 
                                            data-type="parent"
                                            data-id="{{ $parent->parent_id }}"
                                            data-name="{{ $parent->parent_name }}"
                                            data-email="{{ $parent->email }}"
                                            data-phone="{{ $parent->phone_number }}"
                                            data-gender="{{ $parent->gender }}"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.users.delete', ['id' => $parent->parent_id, 'type' => 'parent']) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_parent_confirm') }}');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">{{ __('messages.create_account') }}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">{{ __('messages.account_type') }}</label>
                        <select name="type" class="form-select" id="createType" onchange="toggleCreateFields()">
                            <option value="teacher">{{ __('messages.teacher') }}</option>
                            <option value="parent">{{ __('messages.parent') }}</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6"><label>{{ __('messages.full_name') }}</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-6"><label>{{ __('messages.email') }}</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-6"><label>{{ __('messages.phone') }}</label><input type="text" name="phone" class="form-control" required></div>
                        <div class="col-6"><label>{{ __('messages.gender') }}</label><select name="gender" class="form-select"><option>{{ __('messages.male') }}</option><option>{{ __('messages.female') }}</option></select></div>
                        <div class="col-12 teacher-field"><label>{{ __('messages.address') }}</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                        <div class="col-6 teacher-field"><label>{{ __('messages.join_date') }}</label><input type="date" name="join_date" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark"><h5 class="modal-title">{{ __('messages.edit_user_details') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" id="edit_type_input">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6"><label>{{ __('messages.full_name') }}</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                        <div class="col-6"><label>{{ __('messages.email') }}</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                        <div class="col-6"><label>{{ __('messages.phone') }}</label><input type="text" name="phone" id="edit_phone" class="form-control" required></div>
                        <div class="col-6"><label>{{ __('messages.gender') }}</label><select name="gender" id="edit_gender" class="form-select"><option value="Male">{{ __('messages.male') }}</option><option value="Female">{{ __('messages.female') }}</option></select></div>
                        
                        <div class="col-12" id="edit_address_div"><label>{{ __('messages.address') }}</label><textarea name="address" id="edit_address" class="form-control" rows="2"></textarea></div>
                        <div class="col-6" id="edit_join_div"><label>{{ __('messages.join_date') }}</label><input type="date" name="join_date" id="edit_join" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-warning">{{ __('messages.save_changes') }}</button></div>
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