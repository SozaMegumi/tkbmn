@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Student Enrolment</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="bi bi-person-plus"></i> Register New Student
        </button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
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
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($classesWithStudents))
        @foreach($classesWithStudents as $classroom)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shop-window me-2"></i> {{ $classroom->class_name }}</h5>
                <span class="badge bg-light text-primary fw-bold">{{ $classroom->students->count() }} / {{ $classroom->capacity }} Students</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
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
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No students in this class yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    @if(isset($unassignedStudents) && $unassignedStudents->count() > 0)
    <div class="card shadow-sm border-0 border-start border-warning border-5 mb-4">
        <div class="card-header bg-warning bg-opacity-10 text-dark">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-circle-fill text-warning me-2"></i> Unassigned Students</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                        <td>{{ $student->parent->parent_name ?? 'No Parent' }}</td>
                        <td><span class="badge bg-secondary">Unassigned</span></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary edit-btn"
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
                                Assign Class
                            </button>
                            <form action="{{ route('admin.enrolment.delete', $student->student_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete record?');">
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
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Register New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.enrolment.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student Name</label>
                            <input type="text" name="student_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">MyKid ID</label>
                            <input type="text" name="mykid" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Nationality</label>
                            <select name="nationality" class="form-select">
                                <option value="Malaysian" selected>Malaysian</option>
                                <option value="Non-Malaysian">Non-Malaysian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Race</label>
                            <select name="race" class="form-select">
                                <option value="Malay">Malay</option>
                                <option value="Chinese">Chinese</option>
                                <option value="Indian">Indian</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Religion</label>
                            <select name="religion" class="form-select">
                                <option value="Islam">Islam</option>
                                <option value="Buddhism">Buddhism</option>
                                <option value="Hinduism">Hinduism</option>
                                <option value="Christianity">Christianity</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <label class="form-label fw-bold text-primary">Assign Parent (Guardian)</label>
                                    <select name="parent_id" class="form-select" required>
                                        <option value="">-- Select Parent from Database --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->parent_id }}">{{ $parent->parent_name }} ({{ $parent->phone_number }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Parent not listed? Go to "1.0 Accounts" to create them first.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Assign Class</label>
                            <select name="class_id" class="form-select">
                                <option value="">-- No Class Yet --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Student Name</label>
                            <input type="text" name="student_name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>MyKid ID</label>
                            <input type="text" name="mykid" id="edit_mykid" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4"><label>DOB</label><input type="date" name="dob" id="edit_dob" class="form-control"></div>
                        <div class="col-md-4">
                            <label>Gender</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="Male">Male</option><option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Nationality</label>
                            <select name="nationality" id="edit_nationality" class="form-select">
                                <option value="Malaysian">Malaysian</option>
                                <option value="Non-Malaysian">Non-Malaysian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Race</label>
                            <select name="race" id="edit_race" class="form-select">
                                <option value="Malay">Malay</option><option value="Chinese">Chinese</option><option value="Indian">Indian</option><option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Religion</label>
                            <select name="religion" id="edit_religion" class="form-select">
                                <option value="Islam">Islam</option><option value="Buddhism">Buddhism</option><option value="Hinduism">Hinduism</option><option value="Christianity">Christianity</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Parent</label>
                            <select name="parent_id" id="edit_parent" class="form-select" required>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->parent_id }}">{{ $parent->parent_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Class</label>
                            <select name="class_id" id="edit_class" class="form-select">
                                <option value="">-- No Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Changes</button>
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
                
                // Updated Nationality Dropdown Population
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