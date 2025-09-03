<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru | @yield('title', 'Dashboard')</title>

    {{-- Tailwind CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100">
{{-- Main Content --}}
<main class="flex-1 p-6">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Menampilkan error validasi --}}
    @if ($errors->any())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</main>
    {{-- Navbar --}}
    <nav class="bg-blue-700 text-white px-6 py-4 flex justify-between items-center shadow">
        <h1 class="text-lg font-bold flex items-center gap-2">
            <i class="fas fa-chalkboard-teacher"></i> Sistem Absensi - Guru
        </h1>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('guru.absen.guru', 'Hadir') }}" class="btn btn-success">Absen Hadir</a>
                <a href="{{ route('guru.absen.guru', 'Izin') }}" class="btn btn-warning">Absen Izin</a>
                <a href="{{ route('guru.absen.guru', 'Sakit') }}" class="btn btn-info">Absen Sakit</a>
            </div>
            <span>Halo,<b>{{ Auth::user()->nama_pengguna ?? 'Guru' }}</b></span>
            <a href="{{ route('logout') }}" 
               class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white shadow h-screen p-6">
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-2 hover:text-blue-600">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('guru.absensi.index') }}" class="flex items-center gap-2 hover:text-blue-600">
                        <i class="fas fa-user-check"></i> Absensi Saya
                    </a>
                </li>
                <li>
                    <a href="{{ route('guru.absensi_siswa.index') }}" class="flex items-center gap-2 hover:text-blue-600">
                        <i class="fas fa-users"></i> Kehadiran Siswa
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('guru.jadwal.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('guru.jadwal.index') }}">
                        <i class="fas fa-calendar-alt"></i> Jadwal
                    </a>
                </li>
                <li>
                    <a href="{{ route('guru.rekap.absen') }}" class="flex items-center gap-2 hover:text-blue-600">
                        <i class="fas fa-clipboard-list"></i> Rekap
                    </a>
                </li>
                <li>
                    <a href="{{ route('guru.agenda.kelas') }}" class="flex items-center gap-2 hover:text-blue-600">
                        <i class="fas fa-book"></i> Agenda Kelas
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" class="flex items-center gap-2 hover:text-red-600">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
