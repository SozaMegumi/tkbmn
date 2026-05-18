<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
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
            'message_content' => $request->message ?? '',
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
    // 8. EVENTS CALENDAR
    // ==========================================
    public function events() {
        $parent = Auth::guard('parent')->user();
        if (!$parent) return redirect()->route('login');

        $upcomingEvents = \App\Models\Event::where('start_date', '>=', now()->startOfDay())
                                  ->orderBy('start_date', 'asc')
                                  ->get();

        return view('parent.events', compact('upcomingEvents'));
    }

    // ==========================================
    // 9. PAYMENT PAGE (VIEW)
    // ==========================================
    public function payment() {
        $parent = Auth::guard('parent')->user();
        
        // Ambil SEMUA anak di bawah parent ini
        $students = Student::where('parent_id', $parent->parent_id)->get();
        
        // Jika parent tiada anak berdaftar lagi
        if($students->isEmpty()) {
            return view('parent.payment', ['pendingInvoices' => collect(), 'paymentHistory' => collect()]);
        }

        // Dapatkan array ID untuk semua anak-anak tersebut
        $studentIds = $students->pluck('student_id');

        $pendingInvoices = Payment::with('student')
                                  ->whereIn('student_id', $studentIds)
                                  ->whereIn('status', ['Unpaid', 'Pending']) 
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        $paymentHistory = Payment::with('student')
                                 ->whereIn('student_id', $studentIds)
                                 ->where('status', 'Paid')
                                 ->orderBy('created_at', 'desc') 
                                 ->get();

        return view('parent.payment', compact('pendingInvoices', 'paymentHistory'));
    }

    // ==========================================
    // 10. PAYMENT GATEWAY (STRIPE) & MANUAL UPLOAD
    // ==========================================

    // --- CARA 1: MANUAL UPLOAD RESIT ---
    public function uploadReceipt(Request $request) {
        $request->validate([
            'payment_id' => 'required', 
            'amount' => 'required|numeric|min:1',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);
        
        $payment = Payment::where('payment_id', $request->payment_id)->first();

        if (!$payment) {
            return back()->with('error', 'Ralat: Invois tidak dijumpai.');
        }

        if ($request->hasFile('receipt')) {
            $payment->update([
                'receipt_path' => $request->file('receipt')->store('receipts', 'public'),
                'status' => 'Pending', 
                'admin_remarks' => $request->reference ?? $payment->admin_remarks 
            ]);
        }

        return back()->with('success', 'Resit berjaya dimuat naik! Sila tunggu pengesahan Admin.');
    }

    // --- CARA 2: STRIPE ONLINE PAYMENT (FPX / KAD) ---
    public function createPayment(Request $request) {
        $request->validate([
            'payment_id' => 'required' 
        ]);

        $payment = Payment::where('payment_id', $request->payment_id)->first();

        if (!$payment) {
            return back()->with('error', 'Ralat: Invois tidak dijumpai.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $checkout_session = StripeSession::create([
            'payment_method_types' => ['card', 'fpx'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => $payment->title ?? 'Yuran Tabika',
                        'description' => 'Yuran pelajar: ' . ($payment->student->student_name ?? 'Pelajar'),
                    ],
                    'unit_amount' => (int)($payment->amount * 100), // Stripe kira dalam sen
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('parent.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('parent.payment.cancel'),
            'metadata' => [
                'payment_id' => $payment->payment_id, 
            ],
        ]);

        return redirect($checkout_session->url);
    }

    public function paymentSuccess(Request $request) {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionId = $request->get('session_id');

        try {
            $session = StripeSession::retrieve($sessionId);
            
            if ($session->payment_status == 'paid') {
                $paymentId = $session->metadata->payment_id;
                
                $payment = Payment::where('payment_id', $paymentId)->first();
                
                if ($payment) {
                    $payment->update([
                        'status' => 'Paid',
                        'admin_remarks' => 'Telah dibayar melalui Stripe (FPX/Kad)'
                    ]);
                    
                    return redirect()->route('parent.payment')->with('success', 'Alhamdulillah! Pembayaran anda telah berjaya.');
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('parent.payment')->with('error', 'Ralat pengesahan dari Stripe. Sila hubungi admin.');
        }

        return redirect()->route('parent.payment')->with('warning', 'Pembayaran anda sedang diproses.');
    }

    public function paymentCancel() {
        return redirect()->route('parent.payment')->with('error', 'Pembayaran telah dibatalkan.');
    }
}