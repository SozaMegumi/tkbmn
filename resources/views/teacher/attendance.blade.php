@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI CARDS --- */
    .card-soft {
        border: none; border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: #fff; transition: transform 0.2s;
    }

    /* --- CALENDAR WIDGET (GHOST INPUT) --- */
    .date-card-wrapper {
        background: #fff; border-radius: 15px; padding: 20px;
        border: 2px solid #e2e8f0; text-align: center;
        transition: all 0.3s ease; cursor: pointer; position: relative;
    }
    .date-card-wrapper:hover, .date-card-wrapper:focus-within {
        border-color: #cd2122; 
        background-color: #fff5f5;
        box-shadow: 0 4px 15px rgba(205, 33, 34, 0.1);
        transform: translateY(-2px);
    }
    
    .ghost-date-input {
        position: absolute; top: 0; left: 0; 
        width: 100%; height: 100%;
        opacity: 0; cursor: pointer; z-index: 10;
    }

    .calendar-icon { font-size: 2.5rem; color: #cd2122; margin-bottom: 10px; display: block; }
    .selected-date-display { font-weight: 800; font-size: 1.3rem; color: #334155; line-height: 1.2; }

    /* --- ATTENDANCE TOGGLES --- */
    .status-group {
        display: inline-flex; gap: 0; background: #f1f5f9;
        padding: 4px; border-radius: 50px; 
    }
    .status-radio { display: none; }
    
    .btn-status-custom {
        border-radius: 50px; padding: 6px 16px; font-size: 0.85rem;
        border: 1px solid transparent; color: #64748b; 
        transition: all 0.2s; cursor: pointer; display: flex; align-items: center; gap: 5px;
    }
    .btn-status-custom:hover { background: #e2e8f0; }

    .status-radio:checked + .btn-present { background-color: #10b981; color: white; box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3); }
    .status-radio:checked + .btn-absent  { background-color: #ef4444; color: white; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3); }
    .status-radio:checked + .btn-late    { background-color: #f59e0b; color: white; box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3); }

    .avatar-placeholder {
        width: 42px; height: 42px; background: #e2e8f0; color: #64748b;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.95rem;
    }
    
    /* Upload Button Styles */
    .btn-upload-circle {
        width: 35px; height: 35px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-upload-circle:hover { background-color: #f1f5f9; }
</style>

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Attendance Management</h3>
            <p class="text-muted small mb-0">Record daily attendance for your students.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(isset($selectedClass) && isset($selectedDate))
                <a href="{{ route('teacher.attendance.print', ['date' => $selectedDate, 'class_id' => $selectedClass->class_id]) }}" target="_blank" class="btn btn-outline-danger shadow-sm px-3 py-2 rounded-pill fw-bold bg-white">
                    <i class="bi bi-file-pdf-fill"></i> Cetak PDF
                </a>
            @endif
            
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill border">
                {{ date('l, d M Y') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-soft mb-4">
        <div class="card-body p-4">
            <form action="{{ route('teacher.attendance') }}" method="GET">
                <div class="row g-4 align-items-center">
                    
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-2">1. Select Date</label>
                        <div class="date-card-wrapper" onclick="openDatePicker()">
                            <i class="bi bi-calendar-check calendar-icon"></i>
                            <div class="selected-date-display" id="dateDisplay">
                                {{ \Carbon\Carbon::parse(request('attendance_date', date('Y-m-d')))->format('d M Y') }}
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Tap to change</small>

                            <input type="date" name="attendance_date" id="dateInput"
                                   class="ghost-date-input" 
                                   value="{{ request('attendance_date', date('Y-m-d')) }}" 
                                   onchange="updateDateDisplay(this)" required>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <label class="small fw-bold text-muted mb-2">2. Select Class</label>
                        <select name="class_id" class="form-select form-select-lg border-0 bg-light shadow-sm py-3" style="height: auto;" required>
                            <option value="">-- Choose a Class --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->class_id }}" {{ (request('class_id') == $c->class_id) ? 'selected' : '' }}>
                                    {{ $c->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 text-end">
                        <label class="d-block mb-2">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-3" style="border-radius: 12px;">
                            Load List <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($selectedClass) && isset($selectedDate))
    <form action="{{ route('teacher.attendance.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClass->class_id }}">
        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">

        <div class="card card-soft">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Student List</h5>
                    <small class="text-muted">
                        Class: <strong class="text-primary">{{ $selectedClass->class_name }}</strong> | 
                        Date: <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</strong>
                    </small>
                </div>
                <div>
                    <button type="button" onclick="markAllPresent()" class="btn btn-outline-success btn-sm rounded-pill fw-bold me-2">
                        <i class="bi bi-check-all"></i> All Present
                    </button>
                    <span class="badge bg-light text-dark border">{{ count($students) }} Students</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Student Name</th>
                            <th class="text-center py-3">Status</th>
                            <th class="pe-4 py-3" style="width: 35%;">Reason & MC/Surat (If Absent)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        @php 
                            $status = $student->attendance->status ?? 'Tak Hadir'; 
                            $reason = $student->attendance->reason ?? ''; 
                            $attachment = $student->attendance->attachment ?? null;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-placeholder me-3">
                                        {{ substr($student->student_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark text-uppercase">{{ $student->student_name }}</div>
                                        <small class="text-muted">{{ $student->mykid }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="status-group">
                                    <input type="radio" class="status-radio" 
                                           name="attendances[{{ $student->student_id }}][status]" 
                                           id="p_{{ $student->student_id }}" value="Hadir" 
                                           {{ $status == 'Hadir' ? 'checked' : '' }}>
                                    <label class="btn-status-custom btn-present" for="p_{{ $student->student_id }}">
                                        Hadir
                                    </label>

                                    <input type="radio" class="status-radio" 
                                           name="attendances[{{ $student->student_id }}][status]" 
                                           id="a_{{ $student->student_id }}" value="Tak Hadir" 
                                           {{ $status == 'Tak Hadir' ? 'checked' : '' }}>
                                    <label class="btn-status-custom btn-absent" for="a_{{ $student->student_id }}">
                                        Tak Hadir
                                    </label>

                                    <input type="radio" class="status-radio" 
                                           name="attendances[{{ $student->student_id }}][status]" 
                                           id="l_{{ $student->student_id }}" value="Cuti" 
                                           {{ $status == 'Cuti' ? 'checked' : '' }}>
                                    <label class="btn-status-custom btn-late" for="l_{{ $student->student_id }}">
                                        Cuti
                                    </label>
                                </div>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" name="attendances[{{ $student->student_id }}][reason]" 
                                           class="form-control bg-light border-0 rounded-pill px-3 w-100" 
                                           placeholder="Cth: Demam, Balik Kampung..." value="{{ $reason }}">
                                    
                                    <label class="btn btn-light rounded-circle shadow-sm border mb-0 btn-upload-circle" title="Muat Naik MC / Surat">
                                        <i class="bi bi-paperclip text-secondary fs-5" id="icon_{{ $student->student_id }}"></i>
                                        <input type="file" name="attendances[{{ $student->student_id }}][attachment]" class="d-none" accept=".pdf,.jpg,.jpeg,.png" 
                                               onchange="document.getElementById('icon_{{ $student->student_id }}').className = 'bi bi-file-earmark-check-fill text-success fs-5';">
                                    </label>

                                    @if($attachment)
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="btn btn-info rounded-circle shadow-sm mb-0 btn-upload-circle text-white" title="Lihat MC">
                                            <i class="bi bi-file-medical-fill fs-5"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 opacity-50"></i>
                                <p class="mt-2">No students enrolled in this class.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($students) > 0)
            <div class="card-footer bg-light p-4 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Make sure to check before saving.
                    </div>
                    <button type="submit" class="btn btn-success fw-bold px-5 py-2 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Save Attendance & Docs
                    </button>
                </div>
            </div>
            @endif
        </div>
    </form>
    @endif

</div>

<script>
    function openDatePicker() {
        const input = document.getElementById('dateInput');
        if (typeof input.showPicker === 'function') {
            input.showPicker();
        } else {
            input.focus();
            input.click();
        }
    }

    function updateDateDisplay(input) {
        const date = new Date(input.value);
        if (!isNaN(date.getTime())) {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            document.getElementById('dateDisplay').innerText = date.toLocaleDateString('en-GB', options);
        }
    }

    function markAllPresent() {
        document.querySelectorAll('input[value="Hadir"]').forEach(radio => {
            radio.checked = true;
        });
    }
</script>
@endsection