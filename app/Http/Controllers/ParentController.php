<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Event;     
use App\Models\Student;   
use App\Models\Message;   
use App\Models\Teacher; 
use App\Models\Guardian; 
use App\Models\Payment;    
use App\Models\Attendance; 
use App\Models\Classroom;  
use App\Models\DailyLog;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentController extends Controller
{
    // ==========================================
    // 1. DASHBOARD PAGE
    // ==========================================
    public function dashboard()
    {
        $parent = Auth::guard('parent')->user();
        if (!$parent) return redirect()->route('login');

        // Fetch ALL children belonging to this parent
        $children = Student::where('parent_id', $parent->parent_id)->with('classroom')->get();
        $childIds = $children->pluck('student_id');

        if($children->isEmpty()) {
            return view('parent.dashboard', [
                'parent' => $parent, 'children' => collect(), 'pendingPayments' => collect(), 
                'attendances' => collect(), 'upcomingEvents' => collect(), 'today' => now()->toDateString()
            ]);
        }

        $today = Carbon::today('Asia/Kuala_Lumpur')->toDateString();

        // 1. Get today's attendance for these children
        $attendances = Attendance::whereIn('student_id', $childIds)
                                 ->where('date', $today)
                                 ->get()
                                 ->keyBy('student_id');

        // 2. Check for unpaid/pending fees across all children
        $pendingPayments = Payment::whereIn('student_id', $childIds)
                                  ->whereIn('status', ['Unpaid', 'Pending'])
                                  ->get();

        // 3. Fetch upcoming school events
        $upcomingEvents = Event::where('start_date', '>=', now()->startOfDay())
                               ->orderBy('start_date', 'asc')
                               ->take(3)
                               ->get();

        return view('parent.dashboard', compact('parent', 'children', 'attendances', 'pendingPayments', 'upcomingEvents', 'today'));
    }

    // ==========================================
    // 2. DAILY LOGS VIEWER
    // ==========================================
    public function dailyLogs(Request $request) {
        $parent = Auth::guard('parent')->user();
        if (!$parent) return redirect()->route('login');

        $students = Student::where('parent_id', $parent->parent_id)->get();
        $date = $request->get('date', Carbon::today('Asia/Kuala_Lumpur')->toDateString());

        $logs = DailyLog::whereIn('student_id', $students->pluck('student_id'))
                        ->where('date', $date)
                        ->get()
                        ->keyBy('student_id');

        return view('parent.daily-logs', compact('students', 'date', 'logs'));
    }

    // ==========================================
    // 3. REPORT CARDS DASHBOARD
    // ==========================================
    public function reportCards() {
        $parent = Auth::guard('parent')->user();
        if (!$parent) return redirect()->route('login');

        $students = Student::where('parent_id', $parent->parent_id)->get();
        $assessments = Assessment::all();

        return view('parent.report-cards', compact('students', 'assessments'));
    }

    // ==========================================
    // 4. DOWNLOAD REPORT CARD PDF
    // ==========================================
    public function downloadReportCard(int $student_id, int $assessment_id)  {
        $parent = Auth::guard('parent')->user();
        $student = Student::where('student_id', $student_id)->where('parent_id', $parent->parent_id)->firstOrFail();
        $assessment = Assessment::findOrFail($assessment_id);
        
        $results = AssessmentResult::with('subject')
                    ->where('student_id', $student_id)
                    ->where('assessment_id', $assessment_id)
                    ->get();

        if($results->isEmpty()) {
            return back()->with('error', 'Belum ada gred yang dimuat naik untuk penggal ini.');
        }

        $pdf = Pdf::loadView('reports.kspk-report-card', compact('student', 'assessment', 'results'));
        return $pdf->download('Laporan_'.$student->student_name.'_'.$assessment->name.'.pdf');
    }

    // ==========================================
    // 5. CHAT PAGE
    // ==========================================
    public function chat(Request $request) {
        $parent = Auth::guard('parent')->user();
        $teachers = Teacher::all(); 
        $teacherId = $request->get('teacher_id', $teachers->first()->teacher_id ?? null);
        $activeTeacher = $teachers->where('teacher_id', $teacherId)->first();

        $messages = [];
        if($activeTeacher) {
            $messages = Message::conversation(
                $parent->parent_id, 'App\Models\Guardian',          
                $activeTeacher->teacher_id, 'App\Models\Teacher'     
            )
            ->orderBy('created_at', 'asc')
            ->get();
        }

        return view('parent.chat', compact('teachers', 'activeTeacher', 'messages'));
    }

    // ==========================================
    // 6. SEND MESSAGE (Updated for Attachments)
    // ==========================================
    public function sendMessage(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'message'     => 'nullable|string',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
        ]);

        $parent = Auth::guard('parent')->user();
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        if (!$request->message && !$attachmentPath) {
            return back()->with('error', 'Sila masukkan mesej atau muat naik fail.');
        }

        Message::create([
            'sender_id'       => $parent->parent_id, 
            'sender_type'     => 'App\Models\Guardian', 
            'receiver_id'     => $request->receiver_id,
            'receiver_type'   => 'App\Models\Teacher', 
            'message_content' => $request->message,
            'attachment'      => $attachmentPath,
            'read_at'         => null
        ]);

        return back(); 
    }

    // ==========================================
    // 7. NOTICES PAGE
    // ==========================================
    public function notices() {
        $notices = Event::orderBy('start_date', 'desc')->paginate(10);
        return view('parent.notices', compact('notices'));
    }

    // ==========================================
    // 8. PAYMENT PAGE 
    // ==========================================
    public function payment() {
        $parent = Auth::guard('parent')->user();
        $student = Student::where('parent_id', $parent->parent_id)->first();

        if(!$student) {
            return view('parent.payment', ['pendingInvoices' => [], 'paymentHistory' => []]);
        }

        $pendingInvoices = Payment::where('student_id', $student->student_id)
                                  ->whereIn('status', ['Unpaid', 'Pending']) 
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        $paymentHistory = Payment::where('student_id', $student->student_id)
                                 ->where('status', 'Paid')
                                 ->orderBy('created_at', 'desc') 
                                 ->get();

        return view('parent.payment', compact('pendingInvoices', 'paymentHistory'));
    }

    // ==========================================
    // 9. UPLOAD RECEIPT
    // ==========================================
    public function uploadReceipt(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);
        
        $parent = Auth::guard('parent')->user();
        $student = Student::where('parent_id', $parent->parent_id)->first();

        if(!$student) {
            return back()->with('error', 'Gagal memproses. Tiada rekod murid di bawah akaun anda.');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Payment::create([
            'student_id' => $student->student_id,
            'title' => $request->reference ?? 'Bayaran Yuran Bulanan', 
            'amount' => $request->amount,
            'receipt_path' => $receiptPath,
            'status' => 'Pending', 
            'admin_remarks' => $request->reference ?? 'Bayaran Yuran Bulanan' 
        ]);

        return back()->with('success', 'Resit berjaya dimuat naik! Sila tunggu pengesahan Admin.');
    }

    // ==========================================
    // 10. EVENTS CALENDAR
    // ==========================================
    public function events() {
        $upcomingEvents = Event::where('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($event) {
                return [
                    'title' => $event->title,
                    'description' => $event->description,
                    'date_only' => Carbon::parse($event->start_date)->toDateString(),
                    'theme' => $event->theme,
                    'start_date_raw' => $event->start_date 
                ];
            });

        $allNotices = Event::latest()->paginate(5);
        return view('parent.events', compact('upcomingEvents', 'allNotices'));
    }
}