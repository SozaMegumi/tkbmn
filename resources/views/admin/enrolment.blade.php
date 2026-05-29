@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold text-dark">{{ __('messages.student_enrolment') }}</h3>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="bi bi-person-plus-fill me-2"></i> {{ __('messages.register_new_student') }}
        </button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> {{ __('messages.registration_failed') }}</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-easel-fill text-primary me-2"></i> {{ __('messages.class_teacher_management') }}</h5>
            <p class="text-muted small">{{ __('messages.assign_lead_teacher') }}</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @forelse($classesWithStudents as $class)
                    <div class="col-md-6">
                        <div class="border border-light-subtle rounded-4 p-3 bg-light bg-gradient">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark">{{ $class->class_name }}</h6>
                                <span class="badge bg-secondary rounded-pill">{{ $class->students->count() }} {{ __('messages.students_count') }}</span>
                            </div>
                            
                            <form action="{{ route('admin.enrolment.assign-teacher', $class->class_id) }}" method="POST" class="d-flex gap-2 mt-3">
                                @csrf
                                @method('PUT')
                                <select name="teacher_id" class="form-select form-select-sm border-0 shadow-sm" required>
                                    <option value="" disabled {{ !$class->teacher_id ? 'selected' : '' }}>{{ __('messages.select_teacher') }}</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->teacher_id }}" {{ $class->teacher_id == $teacher->teacher_id ? 'selected' : '' }}>
                                            {{ $teacher->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm rounded-3">{{ __('messages.assign') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3">
                        {{ __('messages.no_classes_found') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @if(isset($unassignedStudents) && $unassignedStudents->count() > 0)
    <div class="card shadow-sm border-0 border-start border-warning border-5 mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-warning bg-opacity-10 text-dark border-0 pt-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-circle-fill text-warning me-2"></i> {{ __('messages.pending_applications') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-top">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">{{ __('messages.mykid') }}</th>
                            <th>{{ __('messages.student_name') }}</th>
                            <th>{{ __('messages.parent_info') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th class="text-end pe-4">{{ __('messages.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unassignedStudents as $student)
                        <tr>
                            <td class="ps-4 font-monospace">{{ $student->mykid }}</td>
                            <td class="fw-bold">{{ $student->student_name }}</td>
                            <td>
                                @if($student->parent)
                                    {{ $student->parent->parent_name }}<br>
                                    <small class="text-muted"><i class="bi bi-telephone"></i> {{ $student->parent->phone_number }}</small>
                                @else
                                    <span class="text-danger small">{{ __('messages.no_parent') }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary rounded-pill">{{ __('messages.pending') }}</span></td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-success fw-bold edit-btn rounded-pill px-3 me-1"
                                    data-id="{{ $student->student_id }}"
                                    data-name="{{ $student->student_name }}"
                                    data-mykid="{{ $student->mykid }}"
                                    data-dob="{{ $student->dob }}"
                                    data-gender="{{ $student->gender }}"
                                    data-race="{{ $student->race }}"
                                    data-religion="{{ $student->religion }}"
                                    data-nationality="{{ $student->nationality }}"
                                    data-parent="{{ $student->parent_id }}"
                                    data-class="{{ $student->class_id }}"
                                    data-bs-toggle="modal" data-bs-target="#editStudentModal">
                                    <i class="bi bi-check-circle me-1"></i> {{ __('messages.assign_class') }}
                                </button>
                                <form action="{{ route('admin.enrolment.delete', $student->student_id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_record_confirm') }}');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($classesWithStudents))
        @foreach($classesWithStudents as $classroom)
        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3 px-4 border-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shop-window me-2 text-primary"></i> {{ $classroom->class_name }}</h5>
                <div>
                    <span class="badge bg-light text-dark fw-bold me-2"><i class="bi bi-person-badge text-primary me-1"></i> {{ $classroom->teacher->full_name ?? __('messages.no_teacher_assigned') }}</span>
                    <span class="badge bg-primary fw-bold rounded-pill">{{ $classroom->students->count() }} / {{ $classroom->capacity }} {{ __('messages.students_count') }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4">{{ __('messages.mykid') }}</th>
                                <th>{{ __('messages.student_name') }}</th>
                                <th>{{ __('messages.parent_info') }}</th>
                                <th>{{ __('messages.race_religion') }}</th>
                                <th class="text-end pe-4">{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classroom->students as $student)
                            <tr>
                                <td class="ps-4 font-monospace">{{ $student->mykid }}</td>
                                <td class="fw-bold">{{ $student->student_name }}</td>
                                <td>
                                    @if($student->parent)
                                        {{ $student->parent->parent_name }}<br>
                                        <small class="text-muted"><i class="bi bi-telephone"></i> {{ $student->parent->phone_number }}</small>
                                    @else
                                        <span class="text-danger small">{{ __('messages.no_parent') }}</span>
                                    @endif
                                </td>
                                <td><small>{{ __('messages.' . strtolower($student->race)) ?? $student->race }} / {{ __('messages.' . strtolower($student->religion)) ?? $student->religion }}</small></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary edit-btn rounded-circle me-1"
                                        data-id="{{ $student->student_id }}"
                                        data-name="{{ $student->student_name }}"
                                        data-mykid="{{ $student->mykid }}"
                                        data-dob="{{ $student->dob }}"
                                        data-gender="{{ $student->gender }}"
                                        data-race="{{ $student->race }}"
                                        data-religion="{{ $student->religion }}"
                                        data-nationality="{{ $student->nationality }}"
                                        data-parent="{{ $student->parent_id }}"
                                        data-class="{{ $student->class_id }}"
                                        data-bs-toggle="modal" data-bs-target="#editStudentModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.enrolment.delete', $student->student_id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_student_confirm') }}');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i> {{ __('messages.no_students_in_class') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    @if((!isset($classesWithStudents) || $classesWithStudents->isEmpty()) && (!isset($unassignedStudents) || $unassignedStudents->isEmpty()))
        <div class="text-center p-5 text-muted">
            <i class="bi bi-folder2-open fs-1"></i>
            <p class="mt-2">{{ __('messages.no_students_enrolled_system') }}</p>
        </div>
    @endif

</div>

<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-success text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> {{ __('messages.register_new_student') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.enrolment.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.student_name') }}</label>
                            <input type="text" name="student_name" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.mykid') }}</label>
                            <input type="text" name="mykid" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.dob') }}</label>
                            <input type="date" name="dob" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.gender') }}</label>
                            <select name="gender" class="form-select bg-light border-0">
                                <option value="Male">{{ __('messages.male') }}</option>
                                <option value="Female">{{ __('messages.female') }}</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.nationality') }}</label>
                            <select name="nationality" class="form-select bg-light border-0">
                                <option value="Malaysian" selected>{{ __('messages.malaysian') }}</option>
                                <option value="Non-Malaysian">{{ __('messages.non_malaysian') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.race') }}</label>
                            <select name="race" class="form-select bg-light border-0">
                                <option value="Malay">{{ __('messages.malay') }}</option>
                                <option value="Chinese">{{ __('messages.chinese') }}</option>
                                <option value="Indian">{{ __('messages.indian') }}</option>
                                <option value="Other">{{ __('messages.other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.religion') }}</label>
                            <select name="religion" class="form-select bg-light border-0">
                                <option value="Islam">{{ __('messages.islam') }}</option>
                                <option value="Buddhism">{{ __('messages.buddhism') }}</option>
                                <option value="Hinduism">{{ __('messages.hinduism') }}</option>
                                <option value="Christianity">{{ __('messages.christianity') }}</option>
                                <option value="Other">{{ __('messages.other') }}</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4">
                            <div class="card bg-primary bg-opacity-10 border-0 rounded-4">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-person-heart me-1"></i> {{ __('messages.assign_parent_guardian') }}</label>
                                    <select name="parent_id" class="form-select border-0 shadow-sm" required>
                                        <option value="">{{ __('messages.select_parent_db') }}</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id ?? $parent->parent_id }}">{{ $parent->name ?? $parent->parent_name }} ({{ $parent->phone_number }})</option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 small text-muted"><i class="bi bi-info-circle me-1"></i> {{ __('messages.parent_not_listed') }} <a href="{{ route('admin.users') }}">{{ __('messages.accounts') }}</a></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.assign_class') }}</label>
                            <select name="class_id" class="form-select bg-light border-0">
                                <option value="">{{ __('messages.no_class_yet') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">{{ __('messages.save_registration') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> {{ __('messages.edit_student_details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.student_name') }}</label>
                            <input type="text" name="student_name" id="edit_name" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.mykid') }}</label>
                            <input type="text" name="mykid" id="edit_mykid" class="form-control bg-light border-0" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.dob') }}</label>
                            <input type="date" name="dob" id="edit_dob" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.gender') }}</label>
                            <select name="gender" id="edit_gender" class="form-select bg-light border-0">
                                <option value="Male">{{ __('messages.male') }}</option><option value="Female">{{ __('messages.female') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.nationality') }}</label>
                            <select name="nationality" id="edit_nationality" class="form-select bg-light border-0">
                                <option value="Malaysian">{{ __('messages.malaysian') }}</option>
                                <option value="Non-Malaysian">{{ __('messages.non_malaysian') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.race') }}</label>
                            <select name="race" id="edit_race" class="form-select bg-light border-0">
                                <option value="Malay">{{ __('messages.malay') }}</option><option value="Chinese">{{ __('messages.chinese') }}</option><option value="Indian">{{ __('messages.indian') }}</option><option value="Other">{{ __('messages.other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.religion') }}</label>
                            <select name="religion" id="edit_religion" class="form-select bg-light border-0">
                                <option value="Islam">{{ __('messages.islam') }}</option><option value="Buddhism">{{ __('messages.buddhism') }}</option><option value="Hinduism">{{ __('messages.hinduism') }}</option><option value="Christianity">{{ __('messages.christianity') }}</option><option value="Other">{{ __('messages.other') }}</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.parent') }}</label>
                            <select name="parent_id" id="edit_parent" class="form-select bg-light border-0" required>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id ?? $parent->parent_id }}">{{ $parent->name ?? $parent->parent_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold text-muted small">{{ __('messages.class') }}</label>
                            <select name="class_id" id="edit_class" class="form-select bg-light border-0">
                                <option value="">{{ __('messages.no_class_yet') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">{{ __('messages.update_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editStudentForm');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get data attributes
                const id = this.getAttribute('data-id');
                
                // Fill form fields
                document.getElementById('edit_name').value = this.getAttribute('data-name');
                document.getElementById('edit_mykid').value = this.getAttribute('data-mykid');
                document.getElementById('edit_dob').value = this.getAttribute('data-dob');
                document.getElementById('edit_gender').value = this.getAttribute('data-gender');
                document.getElementById('edit_race').value = this.getAttribute('data-race');
                document.getElementById('edit_religion').value = this.getAttribute('data-religion');
                document.getElementById('edit_nationality').value = this.getAttribute('data-nationality');
                document.getElementById('edit_parent').value = this.getAttribute('data-parent');
                document.getElementById('edit_class').value = this.getAttribute('data-class');

                // Update Form Action
                editForm.action = "/admin/enrolment/update/" + id;
            });
        });
    });
</script>
@endsection