<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Classroom; 
use App\Models\Message;   
use App\Models\Guardian;  
use App\Models\Teacher;
use App\Models\DailyLog;
use App\Models\Subject;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\HafazanRecord; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class TeacherController extends Controller
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard() {
        try {
            $teacher = Auth::guard('teacher')->user(); 

            if (!$teacher) {
                return redirect()->route('login');
            }

            $assignedClass = 'No Class Assigned';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;

            // Find the classroom where teacher_id matches this teacher
            $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();

            if ($classroom) {
                $assignedClass = $classroom->class_name;
                $totalStudents = Student::where('class_id', $classroom->class_id)->count();

                // Force to date string for exact matching so the dashboard button turns green
                $todayString = Carbon::today('Asia/Kuala_Lumpur')->toDateString();

                $attendanceMarked = Attendance::where('class_id', $classroom->class_id)
                    ->where('date', $todayString)
                    ->exists();
            }

            $unreadMessages = Message::where('receiver_id', $teacher->teacher_id)
                ->where('receiver_type', 'App\Models\Teacher')
                ->whereNull('read_at')
                ->count();

            // Fetch upcoming events for the sidebar/dashboard
            $events = \App\Models\Event::where('start_date', '>=', now()->startOfDay())
                       ->orderBy('start_date', 'asc')
                       ->take(3)
                       ->get();

        } catch (\Exception $e) {
            $assignedClass = 'System Error';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;
            $events = [];
        }

        return view('teacher.dashboard', compact(
            'assignedClass', 
            'totalStudents', 
            'attendanceMarked', 
            'unreadMessages',
            'events',
            'classroom'
        ));
    }

    // ==========================================
    // ATTENDANCE
    // ==========================================
    public function attendance(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classes = Classroom::all();
        $selectedClass = null;
        $selectedDate = $request->get('attendance_date', Carbon::today('Asia/Kuala_Lumpur')->toDateString());
        $students = [];

        $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();
        $teacherClassId = $classroom ? $classroom->class_id : null;

        if ($request->has('class_id') || $teacherClassId) {
            $classId = $request->has('class_id') ? $request->class_id : $teacherClassId;
            $selectedClass = Classroom::find($classId);

            $students = Student::where('class_id', $classId)
                ->get()
                ->map(function($student) use ($selectedDate) {
                    $attendance = Attendance::where('student_id', $student->student_id)
                        ->where('date', $selectedDate)
                        ->first();
                    $student->setRelation('attendance', $attendance);
                    return $student;
                });
        }

        return view('teacher.attendance', compact('classes', 'students', 'selectedClass', 'selectedDate'));
    }

    public function storeAttendance(Request $request) {
        $request->validate([
            'class_id'        => 'required',
            'attendance_date' => 'required|date',
            'attendances'     => 'required|array'
        ]);

        foreach ($request->attendances as $studentId => $data) {
            $attendance = Attendance::where('student_id', $studentId)
                                    ->where('date', $request->attendance_date)
                                    ->first();

            $attachmentPath = $attendance ? $attendance->attachment : null;

            if (isset($data['attachment']) && $data['attachment']->isValid()) {
                $attachmentPath = $data['attachment']->store('attendances/mc_letters', 'public');
            }

            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $request->attendance_date],
                [
                    'class_id'   => $request->class_id, 
                    'status'     => $data['status'], 
                    'reason'     => $data['reason'] ?? null,
                    'attachment' => $attachmentPath
                ]
            );
        }

        return back()->with('success', 'Attendance & Documents updated successfully!');
    }

    public function printAttendance(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $date = $request->get('date', Carbon::today('Asia/Kuala_Lumpur')->toDateString());
        
        $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();
        $teacherClassId = $classroom ? $classroom->class_id : null;
        $classId = $request->get('class_id', $teacherClassId);
        
        $selectedClassroom = Classroom::find($classId);

        if (!$selectedClassroom) {
            return back()->with('error', 'Sila pastikan kelas telah dipilih sebelum mencetak PDF.');
        }

        $students = Student::where('class_id', $classId)->get()->map(function($student) use ($date) {
            $student->attendance = Attendance::where('student_id', $student->student_id)
                                             ->where('date', $date)->first();
            return $student;
        });

        $pdf = Pdf::loadView('reports.attendance-report', compact('students', 'date', 'selectedClassroom', 'teacher'));
        return $pdf->stream('Kehadiran_'.$selectedClassroom->class_name.'_'.$date.'.pdf');
    }

    // ==========================================
    // DAILY LOGS
    // ==========================================
    public function dailyLogs(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();
        $classId = $classroom ? $classroom->class_id : null;
        
        $date = $request->date ?? Carbon::today('Asia/Kuala_Lumpur')->toDateString();
        $students = $classId ? Student::where('class_id', $classId)->get() : collect();
        
        $logs = DailyLog::whereIn('student_id', $students->pluck('student_id'))
                        ->where('date', $date)
                        ->get()
                        ->keyBy('student_id');

        return view('teacher.daily-logs', compact('students', 'date', 'logs'));
    }

    public function storeDailyLogs(Request $request) {
        $date = $request->date;
        $logs = $request->logs; 

        if($logs) {
            foreach ($logs as $student_id => $data) {
                DailyLog::updateOrCreate(
                    ['student_id' => $student_id, 'date' => $date],
                    [
                        'mood' => $data['mood'] ?? null,
                        'meals' => $data['meals'] ?? null,
                        'napped' => isset($data['napped']) ? true : false,
                        'notes' => $data['notes'] ?? null,
                    ]
                );
            }
        }
        return back()->with('success', 'Daily logs saved successfully for ' . $date . '!');
    }

    // ==========================================
    // KSPK GRADING
    // ==========================================
    public function grading(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();
        $classId = $classroom ? $classroom->class_id : null;
        
        $students = $classId ? Student::where('class_id', $classId)->orderBy('student_name', 'asc')->get() : collect();
        
        $assessments = Assessment::all(); 
        $subjects = Subject::all(); 
        
        $selectedAssessmentId = $request->has('assessment_id') ? $request->assessment_id : null;
        
        $results = [];
        $teacherRemarks = [];

        if ($selectedAssessmentId) {
            $allResults = AssessmentResult::where('assessment_id', $selectedAssessmentId)
                ->whereIn('student_id', $students->pluck('student_id'))
                ->get();
                
            foreach ($allResults as $res) {
                $results[$res->student_id][$res->subject_id] = $res->mastery_level;
                
                if ($res->teacher_remarks) {
                    $teacherRemarks[$res->student_id] = $res->teacher_remarks;
                }
            }
        }

        return view('teacher.grading', compact(
            'students', 'subjects', 'assessments', 'selectedAssessmentId', 'results', 'teacherRemarks', 'teacher'
        ));
    }

    public function storeGrade(Request $request) {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'grades' => 'required|array'
        ]);

        $assessment_id = $request->assessment_id;
        $count = 0;

        foreach ($request->grades as $student_id => $studentGrades) {
            $remark = $request->remarks[$student_id] ?? null;

            foreach ($studentGrades as $subject_id => $mastery_level) {
                if (!empty($mastery_level)) { 
                    AssessmentResult::updateOrCreate(
                        [
                            'student_id' => $student_id, 
                            'subject_id' => $subject_id, 
                            'assessment_id' => $assessment_id
                        ],
                        [
                            'mastery_level' => $mastery_level,
                            'teacher_remarks' => $remark,
                        ]
                    );
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            return back()->with('success', 'Penilaian KSPK berjaya disimpan secara berkelompok!');
        } else {
            return back()->with('warning', 'Tiada penilaian disimpan. Sila pastikan sekurang-kurangnya satu gred dipilih.');
        }
    }

   public function reportCards()
{
    $teacher = auth()->guard('teacher')->user();
    
    // Cari kelas yang diajar oleh guru ini
    $classroom = \App\Models\Classroom::where('teacher_id', $teacher->teacher_id)->first();
    
    // Dapatkan senarai murid dalam kelas tersebut
    $students = $classroom ? \App\Models\Student::where('class_id', $classroom->class_id)->get() : collect();
    
    // Dapatkan senarai sesi pentaksiran
    $assessments = \App\Models\Assessment::all();

    // Hantar data ke fail view teacher/report-cards.blade.php
    return view('teacher.report-cards', compact('students', 'assessments'));
}

    public function printReportCards($assessment_id) {
        $teacher = Auth::guard('teacher')->user();
        $assessment = Assessment::findOrFail($assessment_id);
        
        $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();
        $classId = $classroom ? $classroom->class_id : null;
        $students = $classId ? Student::where('class_id', $classId)->get() : collect();

        $allResults = AssessmentResult::with('subject')
            ->whereIn('student_id', $students->pluck('student_id'))
            ->where('assessment_id', $assessment_id)
            ->get()
            ->groupBy('student_id');

        $pdf = Pdf::loadView('reports.kspk-class-report', compact('students', 'assessment', 'allResults'));
        return $pdf->stream('Class_Report_Cards_'.$assessment->name.'.pdf');
    }

    // ==========================================
    // HAFAZAN MANAGEMENT
    // ==========================================
    public function hafazan() {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');
        
        $teacherId = $teacher->teacher_id;
        
        $classIds = Classroom::where('teacher_id', $teacherId)->pluck('class_id');
        $students = Student::whereIn('class_id', $classIds)->orderBy('student_name')->get();
        
        $records = HafazanRecord::with(['student', 'evaluator'])
            ->whereIn('student_id', $students->pluck('student_id'))
            ->orderBy('date_recorded', 'desc')
            ->get();
            
        return view('teacher.hafazan', compact('students', 'records'));
    }

    public function storeHafazan(Request $request) {
        $request->validate([
            'student_id'    => 'required|exists:students,student_id',
            'surah_name'    => 'required|string|max:255',
            'verse_range'   => 'nullable|string|max:255',
            'fluency_level' => 'required|string', 
            'date'          => 'required|date'
        ]);

        // Combine Juz Number into Remarks since DB doesn't have a juz_number column
        $remarks = $request->tajweed_notes;
        if (!empty($request->juz_number)) {
            $remarks = "Juz " . $request->juz_number . ($remarks ? " - " . $remarks : "");
        }

        HafazanRecord::create([
            'student_id'    => $request->student_id,
            'teacher_id'    => Auth::guard('teacher')->user()->teacher_id,
            'surah'         => $request->surah_name,      // Mapped to DB
            'verses'        => $request->verse_range,     // Mapped to DB
            'status'        => $request->fluency_level,   // Mapped to DB
            'remarks'       => $remarks,                  // Mapped to DB
            'date_recorded' => $request->date,
        ]);

        return back()->with('success', 'Rekod Hafazan berjaya disimpan!');
    }

    public function storeBulkHafazan(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'records' => 'required|array'
        ]);

        $teacherId = Auth::guard('teacher')->user()->teacher_id;
        $date = $request->date;
        $count = 0;

        foreach ($request->records as $studentId => $data) {
            if (!empty($data['surah_name'])) {
                
                // Combine Juz Number into Remarks
                $remarks = $data['tajweed_notes'] ?? null;
                if (!empty($data['juz_number'])) {
                    $remarks = "Juz " . $data['juz_number'] . ($remarks ? " - " . $remarks : "");
                }

                HafazanRecord::create([
                    'student_id'    => $studentId,
                    'teacher_id'    => $teacherId,
                    'surah'         => $data['surah_name'],      // Mapped to DB
                    'verses'        => $data['verse_range'] ?? null, // Mapped to DB
                    'status'        => $data['fluency_level'],   // Mapped to DB
                    'remarks'       => $remarks,                 // Mapped to DB
                    'date_recorded' => $date
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            return back()->with('success', "$count rekod hafazan murid berjaya disimpan secara berkelompok!");
        } else {
            return back()->with('warning', 'Tiada rekod disimpan. Sila pastikan sekurang-kurangnya satu Nama Surah diisi.');
        }
    }

    public function deleteHafazan($id) {
        $record = HafazanRecord::findOrFail($id);
        $record->delete(); 
        return back()->with('success', 'Rekod berjaya dipadam.');
    }

    // ==========================================
    // COMMUNICATION
    // ==========================================
    public function communication(Request $request) {
        $teacher = Auth::guard('teacher')->user(); 
        if (!$teacher) return redirect()->route('login');

        $parents = Guardian::orderBy('parent_name', 'asc')->get(); 
        $parentId = $request->get('parent_id', $parents->first()->parent_id ?? null);
        $activeParent = $parents->where('parent_id', $parentId)->first();

        $messages = [];
        if($activeParent) {
            Message::where('sender_id', $activeParent->parent_id)
                ->where('receiver_id', $teacher->teacher_id)
                ->where('receiver_type', 'App\Models\Teacher')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::conversation(
                $teacher->teacher_id, 'App\Models\Teacher', 
                $activeParent->parent_id, 'App\Models\Guardian'
            )
            ->orderBy('created_at', 'asc')
            ->get();
        }

        return view('teacher.communication', compact('parents', 'activeParent', 'messages'));
    }

    public function sendMessage(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'message'     => 'nullable|string',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
        ]);

        $teacher = Auth::guard('teacher')->user(); 
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        // FIX FOR CHAT ATTACHMENTS: Allow sending if there is NO text but there IS an attachment
        if (empty($request->message) && !$attachmentPath) {
            return back()->with('error', 'Sila masukkan mesej atau muat naik fail.');
        }

        Message::create([
            'sender_id'       => $teacher->teacher_id,
            'sender_type'     => 'App\Models\Teacher',
            'receiver_id'     => $request->receiver_id,
            'receiver_type'   => 'App\Models\Guardian', 
            
            // DATABASE FIX: Pass empty string instead of NULL
            'message_content' => $request->message ?? '', 
            
            'attachment'      => $attachmentPath,
            'read_at'         => null
        ]);

        return back();
    }
    
    // ==========================================
    // EVENTS (READ-ONLY)
    // ==========================================
    public function events() {
        $teacher = Auth::guard('teacher')->user(); 
        if (!$teacher) return redirect()->route('login');

        // Fetch all upcoming events, ordered by date
        $events = \App\Models\Event::where('start_date', '>=', now()->startOfDay())
                                  ->orderBy('start_date', 'asc')
                                  ->get();

        return view('teacher.events', compact('events'));
    }


}