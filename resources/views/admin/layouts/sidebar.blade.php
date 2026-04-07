<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-secondary navbar-dark">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand mx-4 mb-3">
            <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='{{ route('organizer.dashboard') }}'">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ff2d55] to-[#ff5e3a] flex items-center justify-center">
                    <i data-lucide="ticket" class="w-6 h-6 text-white transform -rotate-12"></i>
                </div>
                <span class="text-xl font-bold">Tix<span class="text-gradient">flix</span></span>
            </div>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="{{ asset('img/user.jpg') }}" alt="" style="width: 40px; height: 40px;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                <span>Admin</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="{{ route('admin.dashboard') }}" class="nav-item nav-link @if(request()->routeIs('admin.dashboard')) active @endif">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-laptop me-2"></i>Elements
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="{{ route('admin.buttons') }}" class="dropdown-item">Buttons</a>
                    <a href="{{ route('admin.typography') }}" class="dropdown-item">Typography</a>
                    <a href="{{ route('admin.elements') }}" class="dropdown-item">Other Elements</a>
                </div>
            </div>
            <a href="{{ route('admin.widgets') }}" class="nav-item nav-link @if(request()->routeIs('admin.widgets')) active @endif">
                <i class="fa fa-th me-2"></i>Widgets
            </a>
            <a href="{{ route('admin.forms') }}" class="nav-item nav-link @if(request()->routeIs('admin.forms')) active @endif">
                <i class="fa fa-keyboard me-2"></i>Forms
            </a>
            <a href="{{ route('admin.tables') }}" class="nav-item nav-link @if(request()->routeIs('admin.tables')) active @endif">
                <i class="fa fa-table me-2"></i>Tables
            </a>
            <a href="{{ route('admin.charts') }}" class="nav-item nav-link @if(request()->routeIs('admin.charts')) active @endif">
                <i class="fa fa-chart-bar me-2"></i>Charts
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-item nav-link @if(request()->routeIs('admin.events.*')) active @endif">
                <i class="fa fa-calendar me-2"></i>Events
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="nav-item nav-link @if(request()->routeIs('admin.transactions.*')) active @endif">
                <i class="fa fa-credit-card me-2"></i>Transactions
            </a>
            <a href="{{ route('admin.waiting-lists.index') }}" class="nav-item nav-link @if(request()->routeIs('admin.waiting-lists.*')) active @endif">
                <i class="fa fa-clock me-2"></i>Waiting List
            </a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="far fa-file-alt me-2"></i>Pages
                </a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="signin.html" class="dropdown-item">Sign In</a>
                    <a href="signup.html" class="dropdown-item">Sign Up</a>
                    <a href="404.html" class="dropdown-item">404 Error</a>
                    <a href="blank.html" class="dropdown-item">Blank Page</a>
                </div>
            </div>
        </div>
    </nav>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>