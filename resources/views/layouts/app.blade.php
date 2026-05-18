<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TKBMN System</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f0f2f5; 
            margin: 0; 
        }
        
        /* --- SIDEBAR STYLING --- */
        .sidebar { 
            width: 280px; 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            /* Premium Dark Gradient */
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white; 
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto; /* Added just in case sidebar gets too long */
        }
        
        /* Sidebar Header */
        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* Navigation Links */
        .nav-item { margin-bottom: 5px; padding: 0 15px; }
        
        .sidebar .nav-link { 
            color: #94a3b8; /* Soft Text */
            padding: 12px 15px; 
            border-radius: 10px; /* Soft Corners */
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .sidebar .nav-link i:not(.bi-chevron-down) { 
            font-size: 1.1rem; 
            margin-right: 12px; 
            width: 25px; 
            text-align: center;
            transition: transform 0.3s;
        }

        /* Hover Effect */
        .sidebar .nav-link:hover { 
            background-color: rgba(255, 255, 255, 0.05); 
            color: #fff;
            transform: translateX(5px); /* Slide animation */
        }
        
        /* Active State */
        .sidebar .nav-link.active { 
            background: linear-gradient(90deg, #cd2122 0%, #ff4d4d 100%); /* KEMAS Red Theme */
            color: #ffffff; 
            box-shadow: 0 4px 10px rgba(205, 33, 34, 0.3);
            font-weight: 500;
        }

        /* --- MAIN CONTENT --- */
        .main-content { 
            margin-left: 280px; 
            padding: 40px; 
            min-height: 100vh; 
        }
        
        /* Avatar Circle in Header */
        .brand-icon {
            width: 60px; height: 60px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        /* Fix for scrollbar in sidebar */
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 5px; }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column">
        
        <div class="sidebar-header">
            <div class="brand-icon" style="background: transparent; overflow: hidden;">
                <img src="{{ asset('kemaslogo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                <i class="bi bi-backpack4-fill fs-2 text-warning" style="display:none;"></i>
            </div>
            <h6 class="fw-bold text-white mb-0" style="letter-spacing: 0.5px;">
                TABIKA KEMAS
            </h6>
            <div class="text-warning small fw-bold mt-1" style="font-size: 0.7rem; opacity: 0.9;">
                BUSTANUL MAKWAN NAJWA
            </div>
            
            <div class="mt-3 badge bg-white bg-opacity-10 text-white fw-light px-3 py-2 rounded-pill border border-white border-opacity-10">
                @if(Auth::guard('admin')->check()) 
                    <i class="bi bi-shield-lock me-1"></i> Admin Portal
                @elseif(Auth::guard('teacher')->check()) 
                    <i class="bi bi-person-video3 me-1"></i> Teacher Portal
                @elseif(Auth::guard('parent')->check()) 
                    <i class="bi bi-house-heart me-1"></i> Parent Portal
                @endif
            </div>
        </div>

        <ul class="nav flex-column mt-4 mb-auto">
            
            @if(Auth::guard('admin')->check())
                <li class="nav-item">
                    <small class="text-uppercase text-secondary fw-bold px-3 mb-2 d-block" style="font-size: 0.65rem;"></small>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}"><i class="bi bi-people-fill"></i> Accounts</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.enrolment*') ? 'active' : '' }}" href="{{ route('admin.enrolment') }}"><i class="bi bi-person-vcard-fill"></i> Enrolment</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.exams*') ? 'active' : '' }}" href="{{ route('admin.exams') }}"><i class="bi bi-journal-bookmark-fill"></i> Exam Setup</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.finance*') ? 'active' : '' }}" href="{{ route('admin.finance') }}"><i class="bi bi-wallet-fill"></i> Finance</a></li>
                
                <li class="nav-item">
                     <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        <span>Laporan & Analitik</span>
                     </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#reportsMenu" role="button" aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                        <i class="bi bi-file-earmark-text-fill"></i> 
                        <span class="flex-grow-1">PBMT Reports</span>
                        <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem; width: auto; margin-right: 0;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="reportsMenu">
                        <ul class="nav flex-column ms-3 mt-1" style="border-left: 1px solid rgba(255,255,255,0.1);">
                            <li class="nav-item ps-2 mb-1">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.reports.takwim') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.reports.takwim') }}" style="font-size: 0.85rem;">
                                    <i class="bi bi-calendar3 fs-6"></i> Takwim
                                </a>
                            </li>
                            <li class="nav-item ps-2 mb-1">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.reports.unjuran') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.reports.unjuran') }}" style="font-size: 0.85rem;">
                                    <i class="bi bi-calculator fs-6"></i> Unjuran
                                </a>
                            </li>
                            <li class="nav-item ps-2 mb-1">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.reports.berkelompok') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.reports.berkelompok') }}" style="font-size: 0.85rem;">
                                    <i class="bi bi-collection fs-6"></i> Berkelompok
                                </a>
                            </li>
                            <li class="nav-item ps-2 mb-1">
                                <a class="nav-link py-2 {{ request()->routeIs('admin.reports.prestasi') ? 'text-white fw-bold' : '' }}" href="{{ route('admin.reports.prestasi') }}" style="font-size: 0.85rem;">
                                    <i class="bi bi-graph-up fs-6"></i> Prestasi
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item mt-1"><a class="nav-link {{ request()->routeIs('admin.events*') ? 'active' : '' }}" href="{{ route('admin.events') }}"><i class="bi bi-calendar-check-fill"></i> Events</a></li>
            @endif

            @if(Auth::guard('teacher')->check())
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('teacher.attendance*') ? 'active' : '' }}" href="{{ route('teacher.attendance') }}"><i class="bi bi-check-circle-fill"></i> Attendance</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('teacher.grading*') ? 'active' : '' }}" href="{{ route('teacher.grading') }}"><i class="bi bi-mortarboard-fill"></i> Grading</a></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher.hafazan*') ? 'active' : '' }}" href="{{ route('teacher.hafazan') }}">
                        <i class="bi bi-book-half"></i> Rekod Hafazan
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('teacher.communication*') ? 'active' : '' }}" href="{{ route('teacher.communication') }}"><i class="bi bi-chat-quote-fill"></i> Chat</a></li>
                
                <li class="nav-item">
                    <a href="{{ route('teacher.events') }}" class="nav-link {{ request()->routeIs('teacher.events') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill"></i> Events
                    </a>
                </li>
            @endif

           @if(Auth::guard('parent')->check())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}" href="{{ route('parent.dashboard') }}">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parent.daily-logs') ? 'active' : '' }}" href="{{ route('parent.daily-logs') }}">
                        <i class="bi bi-journal-text me-2"></i>
                        <span>Aktiviti Harian</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parent.communication') ? 'active' : '' }}" href="{{ route('parent.communication') }}">
                        <i class="bi bi-chat-dots-fill"></i> Chat
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parent.payment') ? 'active' : '' }}" href="{{ route('parent.payment') }}">
                        <i class="bi bi-credit-card-fill"></i> Payment
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('parent.events') }}" class="nav-link {{ request()->routeIs('parent.events') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>
                        <span>Events</span>
                    </a>
                </li>
            @endif

        </ul>
        <div class="p-3 mt-auto">
            <div class="d-flex bg-dark bg-opacity-25 rounded-pill p-1 mb-3" style="border: 1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('lang.swap', 'en') }}" class="btn btn-sm w-50 rounded-pill {{ app()->getLocale() == 'en' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-secondary' }}" style="transition: all 0.3s;">
                    ENG
                </a>
                <a href="{{ route('lang.swap', 'ms') }}" class="btn btn-sm w-50 rounded-pill {{ app()->getLocale() == 'ms' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-secondary' }}" style="transition: all 0.3s;">
                    BM
                </a>
            </div>

         <div class="p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger w-100 py-2 rounded-3 d-flex align-items-center justify-content-center" style="border-color: rgba(255,255,255,0.2); color: #ffadad;">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>
        
       
            </form>
        </div>
    </div>

  <div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    @yield('content')
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>