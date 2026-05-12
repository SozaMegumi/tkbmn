@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold text-dark">Student Enrolment</h3>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="bi bi-person-plus-fill me-2"></i> Register New Student
        </button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Registration Failed!</h5>
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
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-easel-fill text-primary me-2"></i> Class & Teacher Management</h5>
            <p class="text-muted small">Assign a lead teacher to each classroom.</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @forelse($classesWithStudents as $class)
                    <div class="col-md-6">
                        <div class="border border-light-subtle rounded-4 p-3 bg-light bg-gradient">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark">{{ $class->class_name }}</h6>
                                <span class="badge bg-secondary rounded-pill">{{ $class->students->count() }} Students</span>
                            </div>
                            
                            <form action="{{ route('admin.enrolment.assign-teacher', $class->class_id) }}" method="POST" class="d-flex gap-2 mt-3">
                                @csrf
                                @method('PUT')
                                <select name="teacher_id" class="form-select form-select-sm border-0 shadow-sm" required>
                                    <option value="" disabled {{ !$class->teacher_id ? 'selected' : '' }}>-- Select Teacher --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->teacher_id }}" {{ $class->teacher_id == $teacher->teacher_id ? 'selected' : '' }}>
                                            {{ $teacher->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm rounded-3">Assign</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3">
                        No classes found in the database.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @if(isset($unassignedStudents) && $unassignedStudents->count() > 0)
    <div class="card shadow-sm border-0 border-start border-warning border-5 mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-warning bg-opacity-10 text-dark border-0 pt-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-circle-fill text-warning me-2"></i> Pending Applications (Unassigned)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-top">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">MyKid</th>
                            <th>Student Name</th>
                            <th>Parent Info</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
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
                                    <span class="text-danger small">No Parent</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary rounded-pill">Pending</span></td>
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
                                    <i class="bi bi-check-circle me-1"></i> Assign Class
                                </button>
                                <form action="{{ route('admin.enrolment.delete', $student->student_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete record?');">
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
                    <span class="badge bg-light text-dark fw-bold me-2"><i class="bi bi-person-badge text-primary me-1"></i> {{ $classroom->teacher->full_name ?? 'No Teacher Assigned' }}</span>
                    <span class="badge bg-primary fw-bold rounded-pill">{{ $classroom->students->count() }} / {{ $classroom->capacity }} Students</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4">MyKid</th>
                                <th>Student Name</th>
                                <th>Parent Info</th>
                                <th>Race/Religion</th>
                                <th class="text-end pe-4">Action</th>
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
                                        <span class="text-danger small">No Parent</span>
                                    @endif
                                </td>
                                <td><small>{{ $student->race }} / {{ $student->religion }}</small></td>
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
                                    <form action="{{ route('admin.enrolment.delete', $student->student_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this student?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i> No students in this class yet.</td></tr>
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
            <p class="mt-2">No students enrolled in the system yet.</p>
        </div>
    @endif

</div>

<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-success text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Register New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.enrolment.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Student Name</label>
                            <input type="text" name="student_name" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">MyKid ID</label>
                            <input type="text" name="mykid" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Date of Birth</label>
                            <input type="date" name="dob" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Gender</label>
                            <select name="gender" class="form-select bg-light border-0">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nationality</label>
                            <select name="nationality" class="form-select bg-light border-0">
                                <option value="Malaysian" selected>Malaysian</option>
                                <option value="Non-Malaysian">Non-Malaysian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Race</label>
                            <select name="race" class="form-select bg-light border-0">
                                <option value="Malay">Malay</option>
                                <option value="Chinese">Chinese</option>
                                <option value="Indian">Indian</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Religion</label>
                            <select name="religion" class="form-select bg-light border-0">
                                <option value="Islam">Islam</option>
                                <option value="Buddhism">Buddhism</option>
                                <option value="Hinduism">Hinduism</option>
                                <option value="Christianity">Christianity</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4">
                            <div class="card bg-primary bg-opacity-10 border-0 rounded-4">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-primary mb-1"><i class="bi bi-person-heart me-1"></i> Assign Parent (Guardian)</label>
                                    <select name="parent_id" class="form-select border-0 shadow-sm" required>
                                        <option value="">-- Select Parent from Database --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id ?? $parent->parent_id }}">{{ $parent->name ?? $parent->parent_name }} ({{ $parent->phone_number }})</option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 small text-muted"><i class="bi bi-info-circle me-1"></i> Parent not listed? Go to <a href="{{ route('admin.users') }}">Accounts</a> to create them first.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-muted small">Assign Class</label>
                            <select name="class_id" class="form-select bg-light border-0">
                                <option value="">-- No Class Yet (Pending) --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">Save Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Student Name</label>
                            <input type="text" name="student_name" id="edit_name" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">MyKid ID</label>
                            <input type="text" name="mykid" id="edit_mykid" class="form-control bg-light border-0" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">DOB</label>
                            <input type="date" name="dob" id="edit_dob" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Gender</label>
                            <select name="gender" id="edit_gender" class="form-select bg-light border-0">
                                <option value="Male">Male</option><option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nationality</label>
                            <select name="nationality" id="edit_nationality" class="form-select bg-light border-0">
                                <option value="Malaysian">Malaysian</option>
                                <option value="Non-Malaysian">Non-Malaysian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Race</label>
                            <select name="race" id="edit_race" class="form-select bg-light border-0">
                                <option value="Malay">Malay</option><option value="Chinese">Chinese</option><option value="Indian">Indian</option><option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Religion</label>
                            <select name="religion" id="edit_religion" class="form-select bg-light border-0">
                                <option value="Islam">Islam</option><option value="Buddhism">Buddhism</option><option value="Hinduism">Hinduism</option><option value="Christianity">Christianity</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold text-muted small">Parent</label>
                            <select name="parent_id" id="edit_parent" class="form-select bg-light border-0" required>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id ?? $parent->parent_id }}">{{ $parent->name ?? $parent->parent_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold text-muted small">Class</label>
                            <select name="class_id" id="edit_class" class="form-select bg-light border-0">
                                <option value="">-- No Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Update Changes</button>
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