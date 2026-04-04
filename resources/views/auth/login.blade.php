<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TABIKA KEMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            /* Main Page Background (Behind the book) */
            background: linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* --- BOOK CONTAINER --- */
        .scene {
            width: 100%;
            max-width: 450px;
            height: 600px;
            perspective: 1500px;
            margin: 20px;
        }

        .book {
            width: 100%;
            height: 100%;
            position: relative;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            border-radius: 15px;
        }

        .book.flipped {
            transform: rotateY(-180deg);
        }

        .page {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 15px;
            backface-visibility: hidden; /* Hides back of element */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }

        /* =========================================
           FRONT PAGE (TEACHER & PARENT)
           Background: Cheerful School Theme
        ========================================= */
        .page-front {
            z-index: 2;
            /* Update url('...') with your local image if needed */
            background-image: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.85)), 
                              url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1000&auto=format&fit=crop');
        }
        
        .header-front {
            background: rgba(33, 145, 205, 0.9); /* Red with opacity */
            color: white;
            padding: 50px 30px 30px;
            text-align: center;
            border-bottom-left-radius: 50% 20px;
            border-bottom-right-radius: 50% 20px;
            backdrop-filter: blur(5px);
        }

        /* =========================================
           BACK PAGE (ADMIN)
           Background: Dark Tech/Server Theme
        ========================================= */
        .page-back {
            transform: rotateY(180deg);
            /* Update url('...') with your local image if needed */
            background-image: linear-gradient(rgba(26, 37, 47, 0.92), rgba(26, 37, 47, 0.95)), 
                              url('https://images.unsplash.com/photo-1558494949-ef526b0042a0?q=80&w=1000&auto=format&fit=crop');
            color: white;
        }

        .header-back {
            padding: 50px 30px 30px;
            text-align: center;
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        /* --- FORM LAYOUT --- */
        .form-area {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* --- BUTTONS --- */
        .btn-kemas {
            background: #2194cdff;
            color: white;
            padding: 12px;
            border-radius: 50px; /* Rounded pill buttons */
            font-weight: bold;
            border: none;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(205, 33, 34, 0.3);
        }
        .btn-kemas:hover {
            background: #a4191a;
            transform: translateY(-2px);
        }

        .btn-admin {
            background: #3498db;
            color: white;
            padding: 12px;
            border-radius: 50px;
            font-weight: bold;
            border: none;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        .btn-admin:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* --- LINKS --- */
        .flip-trigger {
            text-decoration: none;
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.3s;
        }
        .flip-trigger:hover { color: #cd2122; }

        .flip-trigger-back { color: #888; }
        .flip-trigger-back:hover { color: white; }

        /* --- INPUTS --- */
        .form-control {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        .page-back .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        .page-back .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: #3498db;
            color: white;
            box-shadow: none;
        }
        .page-back .input-group-text {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="scene">
    <div class="book" id="loginBook">
        
        <div class="page page-front">
            <div class="header-front">
                <i class="bi bi-backpack4-fill fs-1 mb-2"></i>
                <h3 class="fw-bold mb-0">TABIKA KEMAS</h3>
                <small>Parent & Teacher Portal</small>
            </div>

            <div class="form-area">
                @if($errors->any())
                    <div class="alert alert-danger py-2 text-center small mb-3 border-0 shadow-sm">
                        <i class="bi bi-exclamation-circle me-1"></i> Invalid Credentials
                    </div>
                @endif

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary ms-1">EMAIL</label>
                        <input type="email" name="email" class="form-control" placeholder="user@kemas.com" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="small fw-bold text-secondary ms-1">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-kemas">
                            LOGIN NOW
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="flip-trigger" onclick="flipBook()">
                            <i class="bi bi-arrow-repeat"></i> Switch to Admin Login
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="page page-back">
            <div class="header-back">
                <i class="bi bi-shield-lock-fill fs-1 mb-2 text-info"></i>
                <h3 class="fw-bold mb-0">ADMINISTRATOR</h3>
                <small class="text-white-50">Secure System Access</small>
            </div>

            <div class="form-area">
                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="small fw-bold text-white-50 ms-1">ADMIN EMAIL</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-person-fill"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="admin@kemas.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="small fw-bold text-white-50 ms-1">SECURE KEY</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-admin">
                            SECURE LOGIN
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="flip-trigger flip-trigger-back" onclick="unflipBook()">
                            <i class="bi bi-arrow-left-circle-fill"></i> Back to Main Portal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function flipBook() { document.getElementById('loginBook').classList.add('flipped'); }
    function unflipBook() { document.getElementById('loginBook').classList.remove('flipped'); }
</script>

</body>
</html>