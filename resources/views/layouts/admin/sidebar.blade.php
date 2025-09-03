<ul class="navbar-nav sidebar p-0" id="sidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/admin/dashboard') }}">
        <div class="brand-text">ABSENSI</div>
    </a>

    <hr class="sidebar-divider my-2">

    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/kehadiran-guru*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.kehadiran-guru.index') }}">
            <i class="bi bi-check2-square"></i>
            <span>Kehadiran Guru</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/kehadiran-siswa*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.kehadiran-siswa.index') }}">
            <i class="bi bi-check2-square"></i>
            <span>Kehadiran Siswa</span>
        </a>
    </li>

    <!-- Menu Rekap dengan styling khusus -->
    <li class="nav-item {{ request()->is('admin/rekap*') ? 'active' : '' }}">
        <a class="nav-link rekap-menu" href="{{ route('admin.rekap.index') }}">
            <i class="bi bi-bar-chart-line-fill"></i>
            <span>Rekap Kehadiran</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/siswa*') || request()->is('admin/pengguna*') || request()->is('admin/guru*') || request()->is('admin/wali-kelas*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseData">
            <i class="bi bi-database"></i>
            <span>Data</span>
        </a>
        <div id="collapseData" class="collapse {{ request()->is('admin/siswa*') || request()->is('admin/pengguna*') || request()->is('admin/guru*') || request()->is('admin/wali-kelas*') ? 'show' : '' }}">
            <div class="collapse-inner b">
                <a class="collapse-item {{ request()->is('admin/siswa*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">
                    <i class="bi bi-person"></i> Siswa
                </a>
                <a class="collapse-item {{ request()->is('admin/pengguna*') ? 'active' : '' }}" href="{{ route('admin.pengguna.index') }}">
                    <i class="bi bi-people"></i> Pengguna
                </a>
                <a class="collapse-item {{ request()->is('admin/guru*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                    <i class="bi bi-person-gear"></i> Guru
                </a>
                <a class="collapse-item {{ request()->is('admin/wali-kelas*') ? 'active' : '' }}" href="{{ route('admin.wali-kelas.index') }}">
                    <i class="bi bi-people"></i> Wali Kelas
                </a>
            </div>
        </div>
    </li>

    <li class="nav-item {{ request()->is('admin/jadwal-piket*') || request()->is('admin/jadwal-guru*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseJadwal">
            <i class="bi bi-calendar-event"></i>
            <span>Jadwal</span>
        </a>
        <div id="collapseJadwal" class="collapse {{ request()->is('admin/jadwal-piket*') || request()->is('admin/jadwal-guru*') ? 'show' : '' }}">
            <div class="collapse-inner b">
                <a class="collapse-item {{ request()->is('admin/jadwal-piket*') ? 'active' : '' }}" href="{{ route('admin.jadwal-piket.index') }}">
                    <i class="bi bi-calendar-check"></i> Piket Guru
                </a>
                <a class="collapse-item {{ request()->is('admin/jadwal-guru*') ? 'active' : '' }}" href="{{ route('admin.jadwal-guru.index') }}">
                    <i class="bi bi-calendar-range"></i> Jadwal Guru
                </a>
            </div>
        </div>
    </li>
</ul>

<style>
/* ================= Sidebar Styling ================= */
.sidebar {
    /* Latar belakang biru tua khas sekolah */
    background-color: #004d99; 
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Brand */
.sidebar-brand {
    padding: 1.5rem 1rem;
    color: #ffffff;
    font-weight: 700;
    font-size: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* Garis Pemisah */
.sidebar-divider {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin: 1rem 0;
}

.b{
    border-radius: 20px;
}

/* Nav Link (Menu Utama) */
.nav-item .nav-link {
    color: #c0deff;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    border-radius: 8px;
    margin: 0.25rem 0.5rem;
    transition: all 0.2s ease-in-out;
}

/* Styling khusus untuk menu Rekap */
.nav-item .nav-link.rekap-menu {
    background: linear-gradient(45deg, #0066cc, #004d99);
    box-shadow: 0 2px 8px rgba(0, 102, 204, 0.3);
    position: relative;
}

.nav-item .nav-link.rekap-menu:hover {
    background: linear-gradient(45deg, #0080ff, #0066cc);
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
}

/* Badge untuk menu rekap */
.badge-info {
    background-color: #17a2b8 !important;
    color: white !important;
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
}

/* Nav Link Hover & Active State */
.nav-item .nav-link:hover,
.nav-item.active .nav-link {
    background-color: #003366;
    color: #c0deff;
}

/* Active state untuk menu rekap */
.nav-item.active .nav-link.rekap-menu {
    background: linear-gradient(45deg, #0080ff, #0066cc);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.5);
}

/* Dropdown Menu (Sub-menu) */
.collapse-inner {
    /* Menggunakan latar belakang yang lebih terang agar teks lebih menonjol */
    background-color: #c0deff;
    border-radius: 8px;
    margin: 0.5rem 1.5rem;
    padding: 0.5rem 0;
}

/* Item Dropdown (Siswa, Pengguna, Jadwal) */
.collapse-item {
    /* Warna teks dan ikon menjadi putih murni */
    color: white;
    font-weight: 400;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    margin: 0.25rem 0.5rem;
    transition: all 0.2s ease-in-out;
}

/* Dropdown Item Hover & Active State */
.collapse-item:hover,
.collapse-item.active {
    /* Latar belakang saat dihover menjadi lebih cerah */
    background-color: #005a9e;
    color: white;
    border-radius: 10px;
}

/* Ikon */
.nav-link i, .collapse-item i {
    font-size: 1.15rem;
    margin-right: 10px;
    color: black;
    transition: color 0.2s ease-in-out;
}

/* Warna ikon saat aktif/hover */
.nav-item.active .nav-link i,
.nav-link:hover i,
.collapse-item.active i,
.collapse-item:hover i {
    color: #ffffff;
}

/* Ikon khusus untuk menu rekap */
.nav-link.rekap-menu i {
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* Animasi pulse untuk badge */
@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.badge-info {
    animation: pulse 2s infinite;
}
</style>

<script>
// Toggle sidebar mobile
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    if(toggleBtn){
        toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
    }
});
</script>