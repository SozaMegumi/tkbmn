@extends('layouts.app')

@section('content')
<style>
    .card-dashboard { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: transform 0.2s; }
    .card-dashboard:hover { transform: translateY(-5px); }
    .icon-box { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .notice-item { border-left: 4px solid #3b82f6; background: #f8fafc; border-radius: 8px; padding: 15px; margin-bottom: 15px; transition: all 0.2s;}
    .notice-item:hover { background: #f1f5f9; border-left-color: #0d6efd;}
    .border-danger-theme { border-left-color: #dc3545 !important; }
    .border-warning-theme { border-left-color: #ffc107 !important; }
</style>

@php
    $totalFees = $pendingPayments->sum('amount');
    
    $totalChildren = $children->count();
    $presentCount = $attendances->where('status', 'Hadir')->count();
    $attendancePerc = $totalChildren > 0 ? round(($presentCount / $totalChildren) * 100) : 0;

    $firstStudent = $children->first();
    $teacher = null;
    if($firstStudent && $firstStudent->class_id) {
        $teacher = \App\Models\Teacher::find($firstStudent->classroom->teacher_id ?? 0);
    }

    $parentId = Auth::guard('parent')->user()->parent_id;
    $latestMsg = \App\Models\Message::where(function($q) use ($parentId) {
        $q->where('sender_id', $parentId)->where('sender_type', 'App\Models\Guardian');
    })->orWhere(function($q) use ($parentId) {
        $q->where('receiver_id', $parentId)->where('receiver_type', 'App\Models\Guardian');
    })->latest()->first();
@endphp

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Selamat Datang, {{ strtoupper(Auth::guard('parent')->user()->parent_name) }}!</h3>
            <p class="text-muted small mb-0">Papan Pemuka Utama (Overview for <span class="fw-bold text-primary">{{ strtoupper($children->pluck('student_name')->join(', ') ?: 'Child') }}</span>)</p>
        </div>
        <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill shadow-sm">{{ \Carbon\Carbon::parse($today)->format('d M Y') }}</span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-dashboard h-100 p-3 bg-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3"><i class="bi bi-wallet2"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Yuran Tertunggak (Outstanding Fees)</small>
                            <h2 class="fw-bold text-dark mb-0">RM {{ number_format($totalFees, 2) }}</h2>
                        </div>
                    </div>
                    @if($totalFees > 0)
                        <a href="{{ route('parent.payment') }}" class="btn btn-danger w-100 rounded-pill fw-bold">Bayar Sekarang</a>
                    @else
                        <button class="btn btn-success w-100 rounded-pill fw-bold" disabled><i class="bi bi-check-circle me-1"></i> Telah Dibayar (All Paid)</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashboard h-100 p-3 bg-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3"><i class="bi bi-calendar-check"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Kehadiran Hari Ini (Today's Attendance)</small>
                            <h2 class="fw-bold text-dark mb-0">{{ $attendancePerc }}%</h2>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;"><div class="progress-bar bg-success" style="width: {{ $attendancePerc }}%"></div></div>
                    <small class="text-muted mt-2 d-block">{{ $presentCount }} daripada {{ $totalChildren }} anak hadir hari ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashboard h-100 p-3 bg-dark text-white" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight:bold;">
                            {{ substr($firstStudent->student_name ?? 'S', 0, 2) }}
                        </div>
                        <div>
                            <small class="text-white-50 text-uppercase fw-bold">Profil Guru (Teacher Profile)</small>
                            <h5 class="fw-bold mb-0 mt-1">{{ $teacher->full_name ?? 'Belum Ditetapkan' }}</h5>
                            <small class="text-light"><i class="bi bi-door-open-fill"></i> {{ $firstStudent->classroom->class_name ?? 'Tiada Kelas' }}</small>
                        </div>
                    </div>
                    <div class="mt-3"><a href="{{ route('parent.communication') }}" class="btn btn-outline-light btn-sm rounded-pill w-100 fw-bold">Mesej Guru</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold text-dark mb-0"><i class="bi bi-megaphone me-2 text-primary"></i> Papan Notis (Notice Board)</h5></div>
                <div class="card-body px-4">
                    @forelse($upcomingEvents as $notice)
                        <div class="notice-item {{ $notice->theme == 'danger' ? 'border-danger-theme' : ($notice->theme == 'warning' ? 'border-warning-theme' : '') }}">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $notice->title }}</h6>
                                    @if($notice->theme == 'danger') <small class="text-danger fw-bold" style="font-size: 0.7rem;">PENTING (URGENT)</small> @endif
                                </div>
                                <span class="badge {{ $notice->theme == 'danger' ? 'bg-danger' : 'bg-primary' }} shadow-sm">{{ \Carbon\Carbon::parse($notice->start_date)->format('d M') }}</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">{{ \Illuminate\Support\Str::limit($notice->description, 80) }}</p>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted"><i class="bi bi-info-circle fs-1 opacity-50"></i><p class="mt-2 mb-0">Tiada notis terbaru.</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold text-dark mb-0">Sembang Bersama Guru</h5><small class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Mesej Terkini</small></div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center px-4">
                    @if($latestMsg)
                        <div class="bg-light p-3 rounded-4 w-100 mb-3 text-start">
                            <small class="text-muted fw-bold d-block mb-1">{{ $latestMsg->sender_type == 'App\Models\Guardian' ? 'Anda (You):' : 'Guru (Teacher):' }}</small>
                            <p class="text-dark mb-0 fst-italic">"{{ \Illuminate\Support\Str::limit($latestMsg->message_content, 60) }}"</p>
                            <small class="text-muted d-block text-end mt-1" style="font-size: 0.7rem;">{{ $latestMsg->created_at->diffForHumans() }}</small>
                        </div>
                        <a href="{{ route('parent.communication') }}" class="btn btn-primary rounded-pill w-100 fw-bold">Balas (Reply Now)</a>
                    @else
                        <div class="bg-light rounded-circle p-3 mb-3 text-primary bg-opacity-10"><i class="bi bi-chat-heart fs-1"></i></div>
                        <p class="text-muted small">Tiada mesej terbaru bersama guru kelas.</p>
                        @if($teacher) <a href="{{ route('parent.communication', ['teacher_id' => $teacher->teacher_id]) }}" class="btn btn-primary rounded-pill px-4 fw-bold">Mula Sembang</a> @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection