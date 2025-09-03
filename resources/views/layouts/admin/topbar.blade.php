<nav class="navbar navbar-expand-lg navbar-light topbar mb-4 shadow-sm"
     style="backdrop-filter: blur(12px); background: rgba(255,255,255,0.7); border-bottom: 1px solid rgba(0,0,0,0.05);">

    <!-- Tombol Toggle Sidebar (Mobile) -->
    <button id="sidebarToggleTop" class="btn btn-light d-md-none rounded-circle mr-3 shadow-sm">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Brand kecil (optional, mobile view) -->
    <a class="navbar-brand d-lg-none font-weight-bold text-primary" href="{{ url('/') }}">
        ABSENSI
    </a>

    <!-- Navbar Right -->
    <ul class="navbar-nav ml-auto align-items-center">
        <!-- Garis Pemisah -->
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Info -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <!-- Nama User -->
                @if(Auth::check())
                    <div class="rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow"
                        style="width:40px; height:40px; font-weight:600;">
                        {{ strtoupper(substr(Auth::user()->nama_pengguna, 0, 1)) }}
                    </div>
                    <span class="ml-2 d-none d-lg-inline text-gray-700 font-weight-bold">
                        {{ Auth::user()->nama_pengguna }}
                    </span>
                @else
                    <span class="ml-2 text-gray-700 font-weight-bold">Guest</span>
                @endif
                <i class="fas fa-chevron-down ml-1 text-muted"></i>
            </a>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="GET" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>

<style>
    /* Hover halus */
    .nav-link:hover {
        color: #224abe !important;
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }

    /* Animasi dropdown */
    .dropdown-menu {
        border-radius: 0.75rem;
    }

    .dropdown-item:hover {
        background-color: #f8f9fc;
        color: #224abe !important;
        font-weight: 600;
    }
</style>
