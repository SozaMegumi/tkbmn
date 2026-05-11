@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Admin Reports & Analytics</h3>
            <p class="text-muted small mb-0">Overview of school performance and statistics.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i> Trend Kehadiran (Last 7 Days)</h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px; position: relative;">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i> Pencapaian KSPK (Mastery Levels)</h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px; position: relative;">
                        <canvas id="achievementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-cash-stack fs-1 mb-2 opacity-75"></i>
                    <h6 class="fw-bold mb-1">Total Income (All Time)</h6>
                    <h2 class="fw-bold mb-0">RM {{ number_format($stats['total_income'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0">Kapasiti Murid Mengikut Kelas (Class Enrollment)</h6>
                </div>
                <div class="card-body p-4">
                    <div style="height: 200px; position: relative;">
                        <canvas id="classEnrollmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Penjanaan Laporan PBMT</h4>
    
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3 hover-lift">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar3 fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Takwim Sesi Persekolahan</h6>
                    <p class="text-muted small">Jana jadual operasi tahunan tabika.</p>
                    <a href="{{ route('admin.reports.takwim') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2 px-4">Jana Laporan</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3 hover-lift">
                <div class="card-body">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-calculator fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Unjuran Permohonan</h6>
                    <p class="text-muted small">Jana unjuran peruntukan kewangan.</p>
                    <a href="{{ route('admin.reports.unjuran') }}" class="btn btn-outline-success btn-sm rounded-pill mt-2 px-4">Jana Laporan</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3 hover-lift">
                <div class="card-body">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-clipboard-data fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Rumusan Berkelompok</h6>
                    <p class="text-muted small">Rumusan tuntutan bagi pelbagai cawangan.</p>
                    <a href="{{ route('admin.reports.berkelompok') }}" class="btn btn-outline-warning btn-sm rounded-pill mt-2 px-4">Jana Laporan</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3 hover-lift">
                <div class="card-body">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-pie-chart fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Prestasi Perbelanjaan</h6>
                    <p class="text-muted small">Pemantauan baki dan prestasi belanja.</p>
                    <a href="{{ route('admin.reports.prestasi') }}" class="btn btn-outline-danger btn-sm rounded-pill mt-2 px-4">Jana Laporan</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. RENDER ATTENDANCE TREND (Line Chart)
        const ctxTrend = document.getElementById('attendanceTrendChart');
        if(ctxTrend) {
            new Chart(ctxTrend.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($attendanceTrendLabels) !!},
                    datasets: [{
                        label: 'Jumlah Kehadiran (Hadir)',
                        data: {!! json_encode($attendanceTrendValues) !!},
                        borderColor: '#4f46e5', // Indigo color
                        backgroundColor: 'rgba(79, 70, 229, 0.1)', // Light indigo fill
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4, // Smooth curve
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e293b', padding: 10 }
                    },
                    scales: { 
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 2. RENDER ACHIEVEMENT DISTRIBUTION (Bar Chart)
        const ctxAchieve = document.getElementById('achievementChart');
        if(ctxAchieve) {
            new Chart(ctxAchieve.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($academicLabels) !!},
                    datasets: [{
                        label: 'Bilangan Murid',
                        data: {!! json_encode($academicValues) !!},
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',   // Red
                            'rgba(249, 115, 22, 0.8)',  // Orange
                            'rgba(234, 179, 8, 0.8)',   // Yellow
                            'rgba(59, 130, 246, 0.8)',  // Blue
                            'rgba(99, 102, 241, 0.8)',  // Indigo
                            'rgba(34, 197, 94, 0.8)'    // Green
                        ],
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 3. RENDER CLASS ENROLLMENT (Bar Chart)
        const ctxClass = document.getElementById('classEnrollmentChart');
        if(ctxClass) {
            new Chart(ctxClass.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($classLabels) !!},
                    datasets: [{
                        label: 'Jumlah Murid Berdaftar',
                        data: {!! json_encode($classValues) !!},
                        backgroundColor: '#0dcaf0',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
        
    });
</script>
@endsection