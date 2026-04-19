@extends('layouts.app')

@section('content')
<style>
    /* --- SOFT UI DESIGN SYSTEM --- */
    .card-soft {
        border: none; border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04); background: #fff;
    }
    
    /* --- HUMAN-LOGIC CALENDAR STYLES --- */
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center; }
    .calendar-day-header { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 15px; }
    
    .calendar-day { 
        height: 75px; display: flex; flex-direction: column; align-items: center; justify-content: center; 
        font-size: 1.1rem; font-weight: 700; border-radius: 15px; color: #334155; 
        position: relative; cursor: pointer; transition: all 0.2s; border: 2px solid transparent;
        background: #ffffff;
    }
    .calendar-day:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .calendar-day.inactive { color: #cbd5e1; cursor: default; background: transparent; }

    /* Logic: Visual indicators for Holidays and Activities */
    .has-event-danger { background-color: #fff5f5; color: #dc3545; border: 1px dashed #feb2b2; }
    .has-event-danger::after { content: 'HOLIDAY'; font-size: 0.55rem; font-weight: 800; margin-top: 4px; opacity: 0.8; }

    .has-event-primary { background-color: #ebf8ff; color: #3182ce; border: 1px dashed #bee3f8; }
    .has-event-primary::after { content: 'ACTIVITY'; font-size: 0.55rem; font-weight: 800; margin-top: 4px; opacity: 0.8; }

    /* States */
    .calendar-day.today { border: 2px solid #0d6efd; }
    .calendar-day.selected { background: #0d6efd !important; color: white !important; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
    .calendar-day.selected::after { color: white !important; }

    /* Notice Cards (Announcement Feed) */
    .notice-card { border-left: 5px solid #0d6efd; transition: all 0.2s; background: #f8fafc; border-radius: 15px; cursor: pointer; }
    .notice-card.urgent { border-left-color: #dc3545; background: #fff5f5; }
    .notice-card:hover { transform: translateX(5px); background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
</style>

<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">School Calendar & Notices</h3>
            <p class="text-muted small mb-0">Overview of academic events and official announcements.</p>
        </div>
        <div class="text-end">
            <span class="d-block fw-bold text-dark">{{ auth()->guard('parent')->user()->parent_name ?? 'Parent Portal' }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card card-soft h-100">
                <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-light btn-sm rounded-circle border" id="prevMonthBtn"><i class="bi bi-chevron-left"></i></button>
                        <h4 class="fw-bold mb-0 text-dark" id="calendarMonthYear" style="min-width: 160px; text-align: center;"></h4>
                        <button class="btn btn-light btn-sm rounded-circle border" id="nextMonthBtn"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="d-flex gap-3 small fw-bold">
                        <span class="text-primary"><i class="bi bi-square-fill me-1"></i> Activity</span>
                        <span class="text-danger"><i class="bi bi-square-fill me-1"></i> Holiday</span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="calendar-grid">
                        <div class="calendar-day-header">Sun</div><div class="calendar-day-header">Mon</div>
                        <div class="calendar-day-header">Tue</div><div class="calendar-day-header">Wed</div>
                        <div class="calendar-day-header">Thu</div><div class="calendar-day-header">Fri</div>
                        <div class="calendar-day-header">Sat</div>
                    </div>
                    <div class="calendar-grid" id="calendarDays"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-soft h-100" style="border-top: 5px solid #0d6efd;">
                <div class="card-header bg-white p-4 border-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-info-circle me-2 text-primary"></i> Day Summary</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="summary-placeholder" id="summaryPlaceholder" style="min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: #94a3b8;">
                        <i class="bi bi-cursor-fill fs-1 mb-3 opacity-25"></i>
                        <h6 class="fw-bold">Click a colored date</h6>
                        <p class="small">Tap any date with an event label to read details.</p>
                    </div>

                    <div id="summaryContent" class="d-none">
                        <span class="badge rounded-pill mb-2 px-3 py-2" id="summaryThemeBadge"></span>
                        <h4 class="fw-bold text-dark mb-1" id="summaryTitle"></h4>
                        <p class="text-muted small fw-bold mb-3"><i class="bi bi-calendar3 me-1"></i> <span id="summaryDate"></span></p>
                        <div class="p-4 rounded-4 border bg-light">
                            <h6 class="fw-bold text-dark mb-2 small text-uppercase">Description</h6>
                            <p class="text-muted mb-0" id="summaryDescription" style="line-height: 1.6;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card card-soft">
                <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-megaphone me-2 text-warning"></i> Recent Announcements</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row">
                        @forelse($upcomingEvents as $notice)
                        <div class="col-md-6 mb-3">
                            <div class="notice-card p-4 h-100 {{ $notice->theme == 'danger' ? 'urgent' : '' }}" onclick="scrollToDate('{{ \Carbon\Carbon::parse($notice->start_date)->format('Y-m-d') }}')">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-dark mb-0">{{ $notice->title }}</h6>
                                    <span class="badge bg-white text-dark border shadow-sm">{{ \Carbon\Carbon::parse($notice->start_date)->format('d M') }}</span>
                                </div>
                                <p class="text-muted small mb-0">{{ Str::limit($notice->description, 150) }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 opacity-25"></i>
                            <p class="text-muted">No notices at the moment.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawEvents = @json($upcomingEvents ?? []);
    
    // Logic: String-based normalization to prevent UTC+8 timezone shifts
    const schoolEvents = rawEvents.map(e => ({
        ...e,
        dateOnly: e.start_date.substring(0, 10), 
        theme: e.theme || 'primary'
    }));

    const today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    
    window.renderCalendar = function(month, year) {
        document.getElementById('calendarMonthYear').innerText = `${monthNames[month]} ${year}`;
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const grid = document.getElementById('calendarDays');
        grid.innerHTML = '';

        for(let i = 0; i < firstDay; i++) {
            const blank = document.createElement('div');
            blank.className = 'calendar-day inactive';
            grid.appendChild(blank);
        }

        for(let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day day-clickable';
            dayEl.innerText = day;
            dayEl.dataset.date = dateStr;

            if(day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                dayEl.classList.add('today');
            }

            const event = schoolEvents.find(e => e.dateOnly === dateStr);
            if(event) {
                dayEl.classList.add(`has-event-${event.theme}`);
            }

            dayEl.addEventListener('click', () => selectDay(event, dateStr, dayEl));
            grid.appendChild(dayEl);
        }
    }

    function selectDay(event, dateStr, element) {
        document.querySelectorAll('.day-clickable').forEach(d => d.classList.remove('selected'));
        element.classList.add('selected');

        const placeholder = document.getElementById('summaryPlaceholder');
        const content = document.getElementById('summaryContent');

        if(event) {
            placeholder.classList.add('d-none');
            content.classList.remove('d-none');
            document.getElementById('summaryTitle').innerText = event.title;
            document.getElementById('summaryDescription').innerText = event.description || 'No description provided.';
            
            const d = new Date(event.start_date);
            document.getElementById('summaryDate').innerText = d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

            const badge = document.getElementById('summaryThemeBadge');
            if(event.theme === 'danger') {
                badge.className = 'badge rounded-pill mb-2 px-3 py-2 bg-danger bg-opacity-10 text-danger border border-danger';
                badge.innerHTML = '<i class="bi bi-x-circle me-1"></i> Public Holiday';
            } else {
                badge.className = 'badge rounded-pill mb-2 px-3 py-2 bg-primary bg-opacity-10 text-primary border border-primary';
                badge.innerHTML = '<i class="bi bi-star me-1"></i> School Activity';
            }
        } else {
            placeholder.classList.remove('d-none');
            content.classList.add('d-none');
            placeholder.innerHTML = `<i class="bi bi-calendar-x fs-1 mb-3 opacity-25"></i><h6 class="fw-bold">No events for this date</h6>`;
        }
    }

    // Logic: Function to jump to a specific date from an announcement click
    window.scrollToDate = function(targetDate) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        const parts = targetDate.split('-');
        currentYear = parseInt(parts[0]);
        currentMonth = parseInt(parts[1]) - 1;
        renderCalendar(currentMonth, currentYear);
        
        setTimeout(() => {
            const el = document.querySelector(`.day-clickable[data-date="${targetDate}"]`);
            if(el) el.click();
        }, 300);
    };

    document.getElementById('prevMonthBtn').onclick = () => { currentMonth--; if(currentMonth < 0) { currentMonth = 11; currentYear--; } renderCalendar(currentMonth, currentYear); };
    document.getElementById('nextMonthBtn').onclick = () => { currentMonth++; if(currentMonth > 11) { currentMonth = 0; currentYear++; } renderCalendar(currentMonth, currentYear); };

    renderCalendar(currentMonth, currentYear);
});
</script>
@endsection