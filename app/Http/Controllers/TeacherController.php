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
// --- NEW MODELS FOR LOGS & GRADING ---
use App\Models\DailyLog;
use App\Models\Subject;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // <-- NEW: Added PDF package for Teacher printing

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

            // FIX: Use assigned_class_id to find the classroom
            $classroom = Classroom::where('class_id', $teacher->assigned_class_id)->first();

            if ($classroom) {
                $assignedClass = $classroom->class_name;
                $totalStudents = Student::where('class_id', $classroom->class_id)->count();

                $attendanceMarked = Attendance::where('class_id', $classroom->class_id)
                    ->whereDate('date', Carbon::today('Asia/Kuala_Lumpur'))
                    ->exists();
            }

            $unreadMessages = Message::where('receiver_id', $teacher->teacher_id)
                ->where('receiver_type', 'App\Models\Teacher')
                ->whereNull('read_at')
                ->count();

        } catch (\Exception $e) {
            $assignedClass = 'System Error';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;
        }

        return view('teacher.dashboard', compact(
            'assignedClass', 
            'totalStudents', 
            'attendanceMarked', 
            'unreadMessages'
        ));
    }

    // ==========================================
    // ATTENDANCE MANAGEMENT 
    // ==========================================
    public function attendance(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classes = Classroom::all();
        $selectedClass = null;
        $selectedDate = $request->get('attendance_date', Carbon::today('Asia/Kuala_Lumpur')->toDateString());
        $students = [];

        if ($request->has('class_id') || $teacher->assigned_class_id) {
            $classId = $request->has('class_id') ? $request->class_id : $teacher->assigned_class_id;
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
            // Find existing record to preserve old attachments if no new one is uploaded
            $attendance = Attendance::where('student_id', $studentId)
                                    ->where('date', $request->attendance_date)
                                    ->first();

            $attachmentPath = $attendance ? $attendance->attachment : null;

            // Handle file upload if a new MC/Letter is attached
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

    // ==========================================
    // NEW: PDF Export for Attendance (FIXED)
    // ==========================================
    public function printAttendance(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $date = $request->get('date', Carbon::today('Asia/Kuala_Lumpur')->toDateString());
        
        // FIX: Grab the class_id from the URL parameter
        $classId = $request->get('class_id', $teacher->assigned_class_id);
        
        $classroom = Classroom::find($classId);

        if (!$classroom) {
            return back()->with('error', 'Sila pastikan kelas telah dipilih sebelum mencetak PDF.');
        }

        $students = Student::where('class_id', $classId)->get()->map(function($student) use ($date) {
            $student->attendance = Attendance::where('student_id', $student->student_id)
                                             ->where('date', $date)->first();
            return $student;
        });

        $pdf = Pdf::loadView('reports.attendance-report', compact('students', 'date', 'classroom', 'teacher'));
        return $pdf->stream('Kehadiran_'.$classroom->class_name.'_'.$date.'.pdf');
    }

    // ==========================================
    // DAILY LOGS (KINDERGARTEN SPECIFIC)
    // ==========================================
    public function dailyLogs(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classId = $teacher->assigned_class_id;
        $date = $request->date ?? Carbon::today('Asia/Kuala_Lumpur')->toDateString();
        $students = Student::where('class_id', $classId)->get();
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
    // KSPK GRADING (BY STUDENT)
    // ==========================================
    public function grading(Request $request) {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classId = $teacher->assigned_class_id;
        $students = Student::where('class_id', $classId)->get();
        $assessments = Assessment::all(); 
        $groupedSubjects = Subject::all()->groupBy('komponen');
        
        $selectedStudentId = $request->student_id;
        $selectedAssessment = $request->assessment_id;
        
        $selectedStudent = null;
        $results = collect();

        if ($selectedStudentId && $selectedAssessment) {
            $selectedStudent = Student::with('classroom')->where('student_id', $selectedStudentId)->first();
            $results = AssessmentResult::where('student_id', $selectedStudentId)
                ->where('assessment_id', $selectedAssessment)
                ->get()
                ->keyBy('subject_id');
        }

        return view('teacher.grading', compact(
            'students', 'groupedSubjects', 'assessments', 
            'selectedStudentId', 'selectedAssessment', 'selectedStudent', 'results', 'teacher'
        ));
    }

    public function storeGrade(Request $request) {
        $student_id = $request->student_id;
        $assessment_id = $request->assessment_id;
        $grades = $request->grades; 

        if($grades) {
            foreach ($grades as $subject_id => $data) {
                if (!empty($data['mastery_level'])) { 
                    AssessmentResult::updateOrCreate(
                        [
                            'student_id' => $student_id, 
                            'subject_id' => $subject_id, 
                            'assessment_id' => $assessment_id
                        ],
                        [
                            'mastery_level' => $data['mastery_level'],
                            'teacher_remarks' => $data['remarks'] ?? null,
                        ]
                    );
                }
            }
        }
        return back()->with('success', 'Student Report Card saved successfully!');
    }

    public function reportCards() {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $assessments = Assessment::all();
        return view('teacher.report-cards', compact('assessments'));
    }

    public function printReportCards($assessment_id) {
        $teacher = Auth::guard('teacher')->user();
        $assessment = Assessment::findOrFail($assessment_id);
        $students = Student::where('class_id', $teacher->assigned_class_id)->get();

        $allResults = AssessmentResult::with('subject')
            ->whereIn('student_id', $students->pluck('student_id'))
            ->where('assessment_id', $assessment_id)
            ->get()
            ->groupBy('student_id');

        $pdf = Pdf::loadView('reports.kspk-class-report', compact('students', 'assessment', 'allResults'));
        return $pdf->stream('Class_Report_Cards_'.$assessment->name.'.pdf');
    }

    // ==========================================
    // COMMUNICATION (Chat)
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

    // ==========================================
    // SEND MESSAGE (Updated for Attachments)
    // ==========================================
    public function sendMessage(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'message'     => 'nullable|string',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048' // Max 2MB
        ]);

        $teacher = Auth::guard('teacher')->user(); 
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        // Prevent sending empty content
        if (!$request->message && !$attachmentPath) {
            return back()->with('error', 'Sila masukkan mesej atau muat naik fail.');
        }

        Message::create([
            'sender_id'       => $teacher->teacher_id,
            'sender_type'     => 'App\Models\Teacher',
            'receiver_id'     => $request->receiver_id,
            'receiver_type'   => 'App\Models\Guardian', 
            'message_content' => $request->message,
            'attachment'      => $attachmentPath,
            'read_at'         => null
        ]);

        return back();
    }
}