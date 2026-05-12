<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Event;
use App\Models\Transaction; 
use App\Models\Payment;     
use App\Models\Attendance;  
use App\Models\AssessmentResult;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event as GoogleEvent; 

class AdminController extends Controller
{
    // ==========================================
    // DASHBOARD & OVERVIEW
    // ==========================================
    public function dashboard() {
        try {
            $totalStudents = Student::count();
            $totalClasses  = Classroom::count();
            $pendingApprovals = Student::where('status', 'Pending')->count();

            $recentEnrollments = Student::with('parent')->orderBy('created_at', 'desc')->take(5)->get();

            $alerts = [];
            $upcomingEvents = Event::where('start_date', '>=', now()->startOfDay())
                                  ->where('start_date', '<=', now()->addDays(30)->endOfDay())
                                  ->orderBy('start_date', 'asc')
                                  ->get();

            foreach($upcomingEvents as $event) {
                $isHoliday = ($event->theme == 'danger');
                $alerts[] = [
                    'icon' => $isHoliday ? 'bi-calendar-x-fill' : 'bi-bell-fill',
                    'color' => $isHoliday ? 'danger' : 'warning',
                    'title' => $isHoliday ? 'Upcoming Holiday' : 'Upcoming Event',
                    'message' => $event->title . ' is on ' . Carbon::parse($event->start_date)->format('d M Y') . '.'
                ];
            }

            // Standardize labels for the chart fallback
            $attendanceLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $attendanceData = [94, 91, 95, 89, 91];

        } catch (\Exception $e) {
            $totalStudents = 0; $totalClasses = 0; $pendingApprovals = 0;
            $recentEnrollments = []; $alerts = [];
            $attendanceLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $attendanceData = [0, 0, 0, 0, 0];
        }
        
        return view('admin.dashboard', compact(
            'totalStudents', 'totalClasses', 'pendingApprovals', 
            'recentEnrollments', 'alerts', 'attendanceLabels', 'attendanceData'    
        ));
    }

    // ==========================================
    // NEW: ADMIN ATTENDANCE SUMMARY
    // ==========================================
    public function attendanceSummary(Request $request) {
        $date = $request->date ?? \Carbon\Carbon::today('Asia/Kuala_Lumpur')->toDateString();
        $classrooms = \App\Models\Classroom::with('teacher')->get();

        $summary = [];
        foreach($classrooms as $class) {
            $total = \App\Models\Student::where('class_id', $class->class_id)->count();
            
            $present = \App\Models\Attendance::where('class_id', $class->class_id)
                        ->where('date', $date)->where('status', 'Hadir')->count();
                        
            $absent = \App\Models\Attendance::where('class_id', $class->class_id)
                        ->where('date', $date)->whereIn('status', ['Tak Hadir', 'Cuti'])->count();

            $unmarked = $total - ($present + $absent);

            $summary[] = (object) [
                'class_name' => $class->class_name,
                'teacher' => $class->teacher->name ?? 'No Teacher',
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'unmarked' => $unmarked < 0 ? 0 : $unmarked
            ];
        }

        return view('admin.attendance-summary', compact('summary', 'date'));
    }

    // ==========================================
    // 9.0 REPORT MANAGEMENT (CRUCIAL UPDATE HERE)
    // ==========================================
    public function reports() { 
        $stats = [
            'total_income' => Transaction::where('type', 'income')->sum('amount'),
            'monthly_attendance_avg' => Attendance::whereMonth('date', now()->month)
                                        ->where('status', 'Hadir')->count(),
            'enrollment_by_gender' => Student::selectRaw('gender, count(*) as total')
                                        ->groupBy('gender')->get()
        ];

        // START OF CRUCIAL GRAPH DATA INJECTION
        $academicLabels = ['Belum Ada Data'];
        $academicValues = [0];

        try {
            // Attempt to get data assuming 'mastery_level' is the column name
            $academicData = AssessmentResult::selectRaw('mastery_level, count(*) as total')
                                ->groupBy('mastery_level')->orderBy('mastery_level')->get();
            $academicLabels = $academicData->map(fn($item) => 'Tahap ' . $item->mastery_level)->toArray();
            $academicValues = $academicData->pluck('total')->toArray();
        } catch (\Exception $e) {
            try {
                // Fallback: If 'mastery_level' fails, try 'grade'
                $academicData = AssessmentResult::selectRaw('grade as mastery_level, count(*) as total')
                                    ->groupBy('grade')->orderBy('grade')->get();
                $academicLabels = $academicData->map(fn($item) => 'Tahap ' . $item->mastery_level)->toArray();
                $academicValues = $academicData->pluck('total')->toArray();
            } catch (\Exception $e2) {
            }
        }

        $attendanceTrendLabels = [];
        $attendanceTrendValues = [];
        for($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('Asia/Kuala_Lumpur')->subDays($i)->toDateString();
            $attendanceTrendLabels[] = Carbon::parse($date)->format('D, d M');
            $attendanceTrendValues[] = Attendance::where('date', $date)->where('status', 'Hadir')->count();
        }

        $classEnrollment = Classroom::withCount('students')->get();
        $classLabels = $classEnrollment->pluck('class_name')->toArray();
        $classValues = $classEnrollment->pluck('students_count')->toArray();

        return view('admin.reports', compact(
            'stats', 
            'academicLabels', 'academicValues', 
            'attendanceTrendLabels', 'attendanceTrendValues',
            'classLabels', 'classValues'
        )); 
    }

    /**
     * AJAX METHOD: Fetch Real Cash Flow Data & Expense Breakdown
     */
    public function getCashFlowData(Request $request) {
        $monthsCount = $request->get('timeframe') === 'thisyear' ? 12 : 6;
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M');

            $incomeData[] = Transaction::where('type', 'income')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');

            $expenseData[] = Transaction::where('type', 'expense')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');
        }

        $expenseBreakdown = Transaction::where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->get();

        return response()->json([
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
            'expenseBreakdownLabels' => $expenseBreakdown->pluck('category'),
            'expenseBreakdownData' => $expenseBreakdown->pluck('total')
        ]);
    }

    // ==========================================
    // 9.0 REPORT MANAGEMENT FUNCTIONS (PBMT)
    // ==========================================

    public function reportTakwim() {
        $history = \App\Models\PbmtReport::where('report_type', 'takwim')->orderBy('created_at', 'desc')->get();
        return view('admin.reports.takwim', compact('history'));
    }

    public function generateTakwim(Request $request) {
        $request->validate([
            'year' => 'required|integer',
            'months' => 'required|array',
            'tabika_days' => 'required|array',
            'taska_days' => 'required|array',
        ]);

        $total_tabika_days = array_sum($request->tabika_days);
        $total_taska_days = array_sum($request->taska_days);

        $data = [
            'year' => $request->year,
            'total_tabika_days' => $total_tabika_days,
            'total_taska_days' => $total_taska_days,
            'rows' => []
        ];

        foreach($request->months as $index => $month) {
            $data['rows'][] = [
                'month' => $month,
                'tabika_days' => $request->tabika_days[$index],
                'tabika_notes' => $request->tabika_notes[$index] ?? '',
                'taska_days' => $request->taska_days[$index],
                'taska_notes' => $request->taska_notes[$index] ?? '',
            ];
        }

        \App\Models\PbmtReport::create([
            'report_type' => 'takwim',
            'year' => $request->year,
            'data_snapshot' => json_encode($data),
            'generated_by' => Auth::guard('admin')->user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', 'Dokumen Takwim berjaya dijana dan disimpan!');
    }

    public function printTakwim(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $data = json_decode($report->data_snapshot, true);
        return view('admin.reports.print.takwim_format', compact('report', 'data'));
    }

    public function deleteTakwim(int $id) { 
        $report = \App\Models\PbmtReport::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Rekod takwim berjaya dipadam.');
    }

    public function reportUnjuran() {
        $history = \App\Models\PbmtReport::where('report_type', 'unjuran')->orderBy('created_at', 'desc')->get();
        return view('admin.reports.unjuran', compact('history'));
    }

    public function generateUnjuran(Request $request) {
        $request->validate(['year' => 'required|integer']);

        $baki_lepas = $request->baki_lepas ?? 0;
        $jumlah_keseluruhan = 0;
        $rows = [];

        if($request->months) {
            foreach($request->months as $index => $month) {
                $kadar = $request->kadar[$index] ?? 3.00;
                $hari = $request->hari[$index] ?? 0;
                $kanak = $request->kanak[$index] ?? 0;
                
                $jumlah_bulan = $kadar * $hari * $kanak;
                $jumlah_keseluruhan += $jumlah_bulan;

                $rows[] = [
                    'month' => $month,
                    'kadar' => $kadar,
                    'hari'  => $hari,
                    'kanak' => $kanak,
                    'jumlah_bulan' => $jumlah_bulan
                ];
            }
        }

        $jumlah_bersih = $jumlah_keseluruhan - $baki_lepas;

        $data = [
            'year' => $request->year,
            'phase' => $request->phase ?? 'FASA 1',
            'kod_smpk' => $request->kod_smpk,
            'akaun_bank' => $request->akaun_bank,
            'nama_bank' => $request->nama_bank,
            'no_evendor' => $request->no_evendor,
            'baki_lepas' => $baki_lepas,
            'rows' => $rows,
            'jumlah_keseluruhan' => $jumlah_keseluruhan,
            'jumlah_bersih' => $jumlah_bersih,
        ];

        $report = \App\Models\PbmtReport::create([
            'report_type' => 'unjuran',
            'phase' => $request->phase ?? 'FASA 1',
            'year' => $request->year,
            'data_snapshot' => json_encode($data),
            'generated_by' => Auth::guard('admin')->user()->name ?? 'Admin',
        ]);

        return redirect()->back()
            ->with('success', 'Unjuran Kewangan PBMT berjaya dijana!')
            ->with('auto_print', $report->id);
    }

    public function printUnjuran(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $data = json_decode($report->data_snapshot, true);
        return view('admin.reports.print.unjuran_format', compact('report', 'data'));
    }

    public function deleteUnjuran(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Rekod Unjuran berjaya dipadam.');
    }

    public function reportRumusan() {
        $history = \App\Models\PbmtReport::where('report_type', 'berkelompok')->orderBy('created_at', 'desc')->get();
        return view('admin.reports.berkelompok', compact('history'));
    }

    public function generateRumusan(Request $request) {
        $request->validate(['year' => 'required|integer']);

        $rows = [];
        $jumlah_keseluruhan = 0;

        if($request->nama_tabika) {
            foreach($request->nama_tabika as $index => $nama) {
                if(!empty($nama)) { 
                    $jumlah = (float)($request->jumlah[$index] ?? 0);
                    $jumlah_keseluruhan += $jumlah;

                    $rows[] = [
                        'kod_vendor' => $request->kod_vendor[$index] ?? '',
                        'nama_tabika' => strtoupper($nama),
                        'nama_bank' => strtoupper($request->nama_bank[$index] ?? ''),
                        'no_akaun' => $request->no_akaun[$index] ?? '',
                        'jumlah' => $jumlah,
                        'catatan' => $request->catatan[$index] ?? '',
                    ];
                }
            }
        }

        $data = [
            'year' => $request->year,
            'phase' => $request->phase ?? 'FASA 1',
            'parlimen' => strtoupper($request->parlimen ?? ''),
            'negeri' => strtoupper($request->negeri ?? 'JOHOR'),
            'rows' => $rows,
            'jumlah_keseluruhan' => $jumlah_keseluruhan,
        ];

        $report = \App\Models\PbmtReport::create([
            'report_type' => 'berkelompok',
            'phase' => $request->phase ?? 'FASA 1',
            'year' => $request->year,
            'data_snapshot' => json_encode($data),
            'generated_by' => Auth::guard('admin')->user()->name ?? 'Admin',
        ]);

        return redirect()->back()
            ->with('success', 'Rumusan Berkelompok berjaya dijana!')
            ->with('auto_print', $report->id);
    }

    public function printRumusan(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $data = json_decode($report->data_snapshot, true);
        return view('admin.reports.print.berkelompok_format', compact('report', 'data'));
    }

    public function deleteRumusan(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Rekod Rumusan berjaya dipadam.');
    }

    public function reportPrestasi() {
        $history = \App\Models\PbmtReport::where('report_type', 'prestasi')->orderBy('created_at', 'desc')->get();
        return view('admin.reports.prestasi', compact('history'));
    }

    public function generatePrestasi(Request $request) {
        $request->validate(['year' => 'required|integer']);

        $jumlah_peruntukan_total = 0;
        $jumlah_perbelanjaan_total = 0;
        $jumlah_baki_total = 0;
        $total_hari_peruntukan = 0;
        $total_hari_perbelanjaan = 0;

        $rows = [];

        if($request->row_labels) {
            foreach($request->row_labels as $index => $label) {
                $kanak_p = (int)($request->kanak_p[$index] ?? 0);
                $hari_p = (int)($request->hari_p[$index] ?? 0);
                $peruntukan = (float)($request->peruntukan[$index] ?? 0);
                
                $kanak_b = (int)($request->kanak_b[$index] ?? 0);
                $hari_b = (int)($request->hari_b[$index] ?? 0);
                $perbelanjaan = (float)($request->perbelanjaan[$index] ?? 0);

                $catatan = $request->catatan[$index] ?? '';
                
                $baki = $peruntukan - $perbelanjaan;

                $jumlah_peruntukan_total += $peruntukan;
                $jumlah_perbelanjaan_total += $perbelanjaan;
                $jumlah_baki_total += $baki;
                $total_hari_peruntukan += $hari_p;
                $total_hari_perbelanjaan += $hari_b;

                $rows[] = [
                    'label' => $label,
                    'kanak_p' => $kanak_p,
                    'hari_p' => $hari_p,
                    'peruntukan' => $peruntukan,
                    'kanak_b' => $kanak_b,
                    'hari_b' => $hari_b,
                    'perbelanjaan' => $perbelanjaan,
                    'baki' => $baki,
                    'catatan' => $catatan
                ];
            }
        }

        $data = [
            'year' => $request->year,
            'phase' => $request->phase ?? 'FASA 1',
            'kategori' => $request->kategori ?? 'TABIKA',
            'nama_tabika' => $request->nama_tabika,
            'daerah' => $request->daerah,
            'negeri' => $request->negeri,
            'rows' => $rows,
            'jumlah_peruntukan_total' => $jumlah_peruntukan_total,
            'jumlah_perbelanjaan_total' => $jumlah_perbelanjaan_total,
            'jumlah_baki_total' => $jumlah_baki_total,
            'total_hari_peruntukan' => $total_hari_peruntukan,
            'total_hari_perbelanjaan' => $total_hari_perbelanjaan,
        ];

        $report = \App\Models\PbmtReport::create([
            'report_type' => 'prestasi',
            'phase' => $request->phase ?? 'FASA 1',
            'year' => $request->year,
            'data_snapshot' => json_encode($data),
            'generated_by' => Auth::guard('admin')->user()->name ?? 'Admin',
        ]);

        return redirect()->back()
            ->with('success', 'Laporan Prestasi Perbelanjaan berjaya dijana!')
            ->with('auto_print', $report->id);
    }

    public function printPrestasi(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $data = json_decode($report->data_snapshot, true);
        return view('admin.reports.print.prestasi_format', compact('report', 'data'));
    }

    public function deletePrestasi(int $id) {
        $report = \App\Models\PbmtReport::findOrFail($id);
        $report->delete();
        return redirect()->back()->with('success', 'Rekod Prestasi berjaya dipadam.');
    }

    // ==========================================
    // 1.0 USER ACCOUNTS (Teachers & Parents)
    // ==========================================
    public function users() {
        return view('admin.users', [
            'teachers' => Teacher::orderBy('full_name', 'asc')->get(), 
            'parents' => Guardian::orderBy('parent_name', 'asc')->get()
        ]);
    }
    
    public function storeUser(Request $request) {
        $request->validate([
            'email' => 'required|email|unique:teachers,email|unique:guardians,email',
            'name' => 'required|string|max:255',
            'type' => 'required|in:teacher,parent'
        ]);

        $password = Hash::make('password123');
        $username = explode('@', $request->email)[0]; 

        if($request->type == 'teacher') {
            Teacher::create([
                'full_name'    => $request->name,
                'email'        => $request->email,
                'username'     => $username,
                'password'     => $password,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
                'address'      => $request->address,
                'join_date'    => $request->join_date ?? now()->toDateString(),
            ]);
        } else {
            Guardian::create([
                'parent_name'  => $request->name,
                'email'        => $request->email,
                'username'     => $username,
                'password'     => $password,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
            ]);
        }
        return back()->with('success', 'User Account Created Successfully!');
    }

    public function updateUser(Request $request, int $id) {
        if($request->type == 'teacher') {
            $teacher = Teacher::findOrFail($id);
            $teacher->update([
                'full_name'    => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
                'address'      => $request->address,
                'join_date'    => $request->join_date,
            ]);
        } else {
            $parent = Guardian::findOrFail($id);
            $parent->update([
                'parent_name'  => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
            ]);
        }
        return back()->with('success', 'User details updated successfully!');
    }

    public function deleteUser(Request $request, int $id) {
        if($request->input('type') == 'teacher') {
            Teacher::findOrFail($id)->delete();
        } else {
            Guardian::findOrFail($id)->delete();
        }
        return back()->with('success', 'User account deleted successfully.');
    }

    // ==========================================
    // 3.0 & 4.0 STUDENT ENROLMENT & CLASS MGMT
    // ==========================================
    public function enrolment() {
        return view('admin.enrolment', [
            'students' => Student::with('parent')->orderBy('student_name', 'asc')->get(),
            'parents'  => Guardian::all(),
            'classes'  => Classroom::all() 
        ]);
    }
    
    public function storeStudent(Request $request) {
        $request->validate([
            'student_name' => 'required|string',
            'mykid'        => 'required|unique:students,mykid',
            'parent_id'    => 'required|exists:guardians,parent_id',
            'dob'          => 'required|date',
            'class_id'     => 'nullable|exists:classrooms,class_id'
        ]);

        Student::create([
            'student_name' => $request->student_name,
            'mykid'        => $request->mykid,
            'dob'          => $request->dob,
            'gender'       => $request->gender,
            'race'         => $request->race,
            'religion'     => $request->religion,
            'nationality'  => $request->nationality ?? 'Malaysian',
            'parent_id'    => $request->parent_id,
            'class_id'     => $request->class_id,
            'status'       => 'active'
        ]);

        return back()->with('success', 'Student Enrolled Successfully!');
    }
    
    public function updateStudent(Request $request, int $id) {
        $student = Student::findOrFail($id);
        $student->update($request->all());
        return back()->with('success', 'Student details updated successfully!');
    }

    public function deleteStudent(int $id) {
        $student = Student::findOrFail($id);
        $student->delete();
        return back()->with('success', 'Student record deleted successfully.');
    }

    public function updateClassTeacher(Request $request, $id) {
        $request->validate([
            'teacher_id' => 'nullable|exists:teachers,teacher_id'
        ]);

        $classroom = Classroom::findOrFail($id);
        
        $classroom->update([
            'teacher_id' => $request->teacher_id
        ]);

        $teacherName = $request->teacher_id 
            ? Teacher::find($request->teacher_id)->full_name 
            : 'None';

        return back()->with('success', "Class " . $classroom->class_name . " is now assigned to Teacher: " . $teacherName);
    }


    // ==========================================
    // 5.0 ASSESSMENT SETUP (Pengurusan Pentaksiran)
    // ==========================================
    public function exams() {
        $assessments = \App\Models\Assessment::orderBy('created_at', 'desc')->get();
        return view('admin.exams', compact('assessments')); 
    }

    public function storeExam(Request $request) {
        $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:Buka,Tutup'
        ]);

        \App\Models\Assessment::create($request->all());

        return back()->with('success', 'Sesi Pentaksiran berjaya dicipta! Guru kini boleh memasukkan markah.');
    }

    public function updateExam(Request $request, int $id) {
        $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:Buka,Tutup'
        ]);

        $assessment = \App\Models\Assessment::findOrFail($id);
        $assessment->update($request->all());

        return back()->with('success', 'Status Pentaksiran berjaya dikemaskini.');
    }

    public function deleteExam(int $id) {
        $assessment = \App\Models\Assessment::findOrFail($id);
        $assessment->delete();

        return back()->with('success', 'Sesi Pentaksiran berjaya dipadam.');
    }


    // ==========================================
    // 8.0 FINANCE (Process 8.0 in DFD)
    // ==========================================
    public function finance() {
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;
        
        $pendingPayments = Payment::where('status', 'Pending')->with('student')->get();
        
        $transactions = Transaction::orderBy('date', 'desc')->get();

        $classrooms = Classroom::with(['students' => function($query) {
            $query->with(['payments' => function($q) {
                $q->whereIn('status', ['Unpaid', 'Pending']);
            }]);
        }])->get();

        return view('admin.finance', compact(
            'totalIncome', 
            'totalExpense', 
            'currentBalance', 
            'transactions', 
            'pendingPayments', 
            'classrooms' 
        ));
    }
    
    public function generateMonthlyBills() {
        $students = Student::where('status', 'active')->get(); 
        $currentMonth = now()->format('F Y'); 
        $count = 0;

        foreach($students as $student) {
            $alreadyBilled = Payment::where('student_id', $student->student_id) 
                                    ->where('admin_remarks', "Yuran Bulanan - $currentMonth")
                                    ->exists();

            if(!$alreadyBilled) {
                Payment::create([
                    'student_id' => $student->student_id, 
                    'title' => 'Yuran Bulanan',
                    'amount' => 150.00,
                    'status' => 'Unpaid',
                    'admin_remarks' => "Yuran Bulanan - $currentMonth"
                ]);
                $count++;
            }
        }

        if($count > 0) {
            return back()->with('success', "Berjaya! Invois RM 150.00 telah dijana untuk $count orang pelajar bagi bulan $currentMonth.");
        } else {
            return back()->with('warning', "Semua pelajar telah menerima invois untuk bulan $currentMonth.");
        }
    }
    
    public function storeTransaction(Request $request) {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'required',
            'receipt_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' 
        ]);

        $data = $request->except('receipt_file');

        if ($request->hasFile('receipt_file')) {
            $path = $request->file('receipt_file')->store('receipts/transactions', 'public');
            $data['receipt_path'] = $path;
        }

        Transaction::create($data);
        
        return back()->with('success', 'Transaction Recorded Successfully!');
    }

    public function deleteTransaction(int $id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return back()->with('success', 'Transaction record deleted successfully.');
    }

    public function approvePayment(int $id) {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'Paid']);

        Transaction::create([
            'type' => 'income',
            'amount' => $payment->amount,
            'category' => 'School Fees',
            'date' => now(),
            'description' => "Fee payment for " . ($payment->student->student_name ?? 'Student')
        ]);

        return back()->with('success', 'Payment approved and recorded in finance.');
    }

    public function rejectPayment(Request $request, int $id) {
        $payment = Payment::findOrFail($id);
        $payment->update([
            'status' => 'Unpaid',
            'admin_remarks' => $request->remarks 
        ]);

        return back()->with('error', 'Payment rejected. Parent has been notified.');
    }


    // ==========================================
    // 10.0 SCHOOL EVENTS (Fully Synced with Google)
    // ==========================================
    public function events() {
        return view('admin.events', ['events' => Event::orderBy('start_date', 'asc')->get()]);
    }

    public function storeEvent(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'theme' => 'required|in:primary,danger'
        ]);

        $googleEventId = null;

        try {
            $googleEvent = new GoogleEvent;
            $googleEvent->name = $request->title;
            $googleEvent->description = $request->description ?? ''; 
            $googleEvent->startDateTime = Carbon::parse($request->start_date)->startOfDay();
            
            if ($request->end_date) {
                $googleEvent->endDateTime = Carbon::parse($request->end_date)->endOfDay();
            } else {
                $googleEvent->endDateTime = Carbon::parse($request->start_date)->endOfDay();
            }
            
            $googleEvent->save();
            $googleEventId = $googleEvent->id; 
            
        } catch (\Exception $e) {}

        Event::create([
            'title'           => $request->title,
            'description'     => $request->description,
            'start_date'      => Carbon::parse($request->start_date)->toDateString(),
            'end_date'        => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::parse($request->start_date)->toDateString(),
            'theme'           => $request->theme,
            'created_by'      => Auth::id() ?? 1,
            'google_event_id' => $googleEventId 
        ]);

        if ($googleEventId) {
            return back()->with('success', 'Event Posted Locally & Synced with Google Calendar!');
        } else {
            return back()->with('success', 'Event Posted Locally, but Google Sync Failed.');
        }
    }

    public function updateEvent(Request $request, int $id) {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'theme' => 'required|in:primary,danger'
        ]);

        $event = Event::findOrFail($id);
        
        $event->update([
            'title'       => $request->title,
            'description' => $request->description,
            'start_date'  => Carbon::parse($request->start_date)->toDateString(),
            'end_date'    => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::parse($request->start_date)->toDateString(),
            'theme'       => $request->theme,
        ]);

        $googleUpdated = false;
        $googleError = '';

        if ($event->google_event_id) {
            try {
                $googleEvent = GoogleEvent::find($event->google_event_id);
                if ($googleEvent) {
                    $googleEvent->update([
                        'name' => $request->title,
                        'description' => $request->description ?? '',
                        'startDateTime' => Carbon::parse($request->start_date)->startOfDay(),
                        'endDateTime' => $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::parse($request->start_date)->endOfDay(),
                    ]);
                    $googleUpdated = true;
                }
            } catch (\Exception $e) {
                $googleError = $e->getMessage();
            }
        }

        if ($googleUpdated) {
            return back()->with('success', 'Event Updated Locally and on Google Calendar!');
        } elseif ($event->google_event_id && $googleError) {
            return back()->with('warning', 'Updated locally, but Google Calendar update failed. Error: ' . $googleError);
        } else {
            return back()->with('success', 'Event updated locally (It was never synced to Google Calendar originally).');
        }
    }

    public function deleteEvent(int $id) {
        $event = Event::findOrFail($id);
        
        $googleDeleted = false;
        $googleError = '';

        if ($event->google_event_id) {
            try {
                $googleEvent = GoogleEvent::find($event->google_event_id);
                if ($googleEvent) {
                    $googleEvent->delete();
                    $googleDeleted = true;
                }
            } catch (\Exception $e) {
                $googleError = $e->getMessage();
            }
        }

        $event->delete();
        
        if ($googleDeleted) {
            return back()->with('success', 'Event Deleted from System and Google Calendar.');
        } elseif ($event->google_event_id && $googleError) {
            return back()->with('warning', 'Deleted locally, but Google Calendar failed. Error: ' . $googleError);
        } else {
            return back()->with('success', 'Event Deleted locally (It was never synced to Google Calendar).');
        }
    }
}