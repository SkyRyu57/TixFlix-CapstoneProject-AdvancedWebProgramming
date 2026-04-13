<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard Penyelenggara') | TixFlix</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #0b1120;
            color: #f1f5f9;
            line-height: 1.5;
        }
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100%;
            background: #1e293b;
            transition: 0.3s ease;
            z-index: 1050;
            padding-top: 1.5rem;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
        }
        .sidebar.open {
            left: 0;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1.5rem;
            transition: 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(249,115,22,0.2);
            color: #f97316;
            border-left: 3px solid #f97316;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .sidebar-overlay.show {
            display: block;
        }
        .main-content {
            margin-left: 0;
            transition: 0.3s;
            min-height: 100vh;
        }
        /* Navbar */
        .navbar-custom {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 0.8rem 1.5rem;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: #f97316;
        }
        .sidebar-toggler {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
        }
        .avatar-dropdown img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #f97316;
            cursor: pointer;
        }
        /* Cards */
        .stat-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 1.25rem;
            transition: transform 0.2s, box-shadow 0.2s;
            color: #f1f5f9;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -12px rgba(0,0,0,0.4);
        }
        .stat-card .text-muted {
            color: #94a3b8 !important;
        }
        .stat-card h3 {
            color: #f1f5f9;
        }
        /* Table */
        .table-custom {
            background: #1e293b;
            border-radius: 1rem;
            overflow: hidden;
        }
        .table-custom th {
            border-bottom: 1px solid #334155;
            color: #94a3b8;
            font-weight: 500;
        }
        .table-custom td {
            vertical-align: middle;
            color: #f1f5f9;
        }
        .progress {
            background-color: #2d3748;
            height: 6px;
        }
        .btn-outline-accent {
            border: 1px solid #f97316;
            color: #f97316;
            border-radius: 2rem;
            padding: 0.25rem 1rem;
            font-size: 0.8rem;
        }
        .btn-outline-accent:hover {
            background: #f97316;
            color: white;
        }
        footer {
            border-top: 1px solid #334155;
            padding: 1rem 0;
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        /* Utility */
        .text-accent {
            color: #f97316;
        }
        a, a:hover {
            text-decoration: none;
        }
        .dropdown-menu.bg-dark {
            background-color: #1e293b !important;
        }
        .dropdown-item.text-white:hover {
            background-color: #334155;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="spinner" style="position:fixed; top:0; left:0; width:100%; height:100%; background:#0b1120; z-index:9999; display:flex; align-items:center; justify-content:center;">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat...</span></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="px-3 mb-4">
            <h5 class="text-accent mb-0">Menu</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('organizer.events.index') }}" class="nav-link {{ request()->routeIs('organizer.events.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt me-2"></i> Events
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('organizer.waiting-requests.index') }}" class="nav-link {{ request()->routeIs('organizer.waiting-list.*') ? 'active' : '' }}">
                    <i class="fas fa-clock me-2"></i> Waiting List
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('organizer.scan.index') }}" class="nav-link {{ request()->routeIs('organizer.scan.*') ? 'active' : '' }}  ">
                    <i class="fas fa-qrcode me-2"></i> Scan Ticket
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-start w-100" style="background: none; border: none;">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center gap-3">
                    <button class="sidebar-toggler" id="sidebarToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <!-- Logo TixFlix -->
                    <div class="d-flex align-items-center gap-2" style="cursor: pointer;" onclick="window.location.href='{{ route('dashboard') }}'">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #ff2d55, #ff5e3a); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-ticket-alt fa-lg text-white" style="transform: rotate(-12deg);"></i>
                        </div>
                        <span style="font-size: 1.25rem; font-weight: bold; color: white;">Tix<span style="background: linear-gradient(135deg, #ff2d55, #ff5e3a); -webkit-background-clip: text; background-clip: text; color: transparent;">flix</span></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-white position-relative">
                        <i class="far fa-bell fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">3</span>
                    </a>
                    <div class="dropdown">
                        <div class="avatar-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=f97316&color=fff" alt="avatar">
                            @endif
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                            <li><a class="dropdown-item text-white" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-white"><i class="fas fa-sign-out-alt me-2"></i> Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-4 py-4">
            @yield('content')
        </div>

        <footer>
            <div class="container">
                <p>&copy; {{ date('Y') }} TixFlix. Semua hak dilindungi.</p>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('load', function() {
            const spinner = document.getElementById('spinner');
            if (spinner) spinner.style.display = 'none';
        });
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }
        toggleBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts')
</body>
</html>