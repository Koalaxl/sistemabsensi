<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guru | @yield('title', 'Dashboard')</title>

  {{-- Tailwind CSS --}}
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
    }
  </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex flex-col font-sans dark:bg-gray-900 dark:text-gray-100">

  {{-- Navbar --}}
  <nav class="fixed top-0 left-0 w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 sm:px-6 py-3 flex justify-between items-center shadow z-50">
    <div class="flex items-center gap-3">
      <button onclick="toggleSidebar()" class="md:hidden text-white text-2xl focus:outline-none">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="text-lg sm:text-xl font-bold flex items-center gap-2">
        <i class="fas fa-school"></i> Sistem Absensi Guru
      </h1>
    </div>
    <div class="hidden md:flex gap-2">
      <a href="{{ route('guru.absen.guru', 'Hadir') }}" class="px-3 py-2 rounded-full bg-green-500 hover:bg-green-600 transition text-white font-medium shadow text-sm sm:text-base">
        <i class="fas fa-check-circle"></i> Hadir
      </a>
      <a href="{{ route('guru.absen.guru', 'Izin') }}" class="px-3 py-2 rounded-full bg-yellow-400 hover:bg-yellow-500 transition text-gray-900 font-medium shadow text-sm sm:text-base">
        <i class="fas fa-envelope-open"></i> Izin
      </a>
      <a href="{{ route('guru.absen.guru', 'Sakit') }}" class="px-3 py-2 rounded-full bg-blue-500 hover:bg-blue-600 transition text-white font-medium shadow text-sm sm:text-base">
        <i class="fas fa-user-md"></i> Sakit
      </a>
    </div>
    <div class="flex items-center gap-3">
      <span class="hidden sm:block text-sm sm:text-base">👋 Halo, <b>{{ session('user.nama_pengguna') ?? 'Guru' }}</b></span>
      <a href="{{ route('logout') }}" class="px-3 py-2 rounded-full bg-red-500 hover:bg-red-600 transition text-white font-medium shadow text-sm sm:text-base">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </nav>

<div class="flex pt-20 min-h-screen">
  {{-- Sidebar --}}
  <aside id="sidebar"
    class="fixed md:static top-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-xl min-h-screen p-6 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40 rounded-r-3xl">

    {{-- Sidebar Header Mobile --}}
    <div class="flex justify-between items-center mb-6 md:hidden">
      <h2 class="text-lg font-bold"><i class="fas fa-bars"></i> Menu</h2>
      <button onclick="toggleSidebar()" class="text-gray-600 text-2xl dark:text-gray-200">
        <i class="fas fa-times"></i>
      </button>
    </div>

    {{-- Profile --}}
    <div class="mb-6 text-center">
      <img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="Guru"
        class="w-16 h-16 mx-auto rounded-full shadow">
      <p class="mt-2 font-semibold">{{ session('user.nama_pengguna') ?? 'Guru' }}</p>
      <small class="text-gray-500 dark:text-gray-400">Pengajar</small>
    </div>

    {{-- Menu --}}
    <ul class="space-y-2 text-sm sm:text-base">
      <li><a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900 transition"><i class="fas fa-home text-blue-600"></i> Dashboard</a></li>
      <li><a href="{{ route('guru.absensi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-100 dark:hover:bg-green-900 transition"><i class="fas fa-user-check text-green-600"></i> Absensi Saya</a></li>
      <li><a href="{{ route('guru.absensi_siswa.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900 transition"><i class="fas fa-users text-purple-600"></i> Kehadiran Siswa</a></li>
      <li><a href="{{ route('guru.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900 transition"><i class="fas fa-calendar-alt text-yellow-600"></i> Jadwal</a></li>
      <li><a href="{{ route('guru.rekap_siswa.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition"><i class="fas fa-clipboard-list text-indigo-600"></i> Rekap</a></li>
      <li><a href="{{ route('guru.agenda.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-pink-100 dark:hover:bg-pink-900 transition"><i class="fas fa-book text-pink-600"></i> Agenda Kelas</a></li>
      <li><a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-red-600 hover:bg-red-100 dark:hover:bg-red-900 transition"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </aside>

  {{-- Main Content --}}
  <main class="flex-1 p-4 sm:p-6 md:pl-35">
    {{-- Notifikasi --}}
    @if(session('success'))
      <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700 flex items-center gap-2 shadow">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700 flex items-center gap-2 shadow">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="mb-4 p-4 rounded-lg bg-yellow-100 border border-yellow-400 text-yellow-700 shadow">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li><i class="fas fa-info-circle"></i> {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Hero Section --}}
    <div class="mb-6 p-6 bg-gradient-to-r from-blue-100 to-green-100 dark:from-gray-700 dark:to-gray-800 rounded-3xl shadow flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="text-center sm:text-left">
        <h2 class="text-xl sm:text-2xl font-bold text-blue-800 dark:text-white"><i class="fas fa-graduation-cap"></i> Selamat Datang, Guru!</h2>
        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">Kelola absensi & kehadiran siswa dengan mudah.</p>
      </div>
      <img src="https://cdn-icons-png.flaticon.com/512/1995/1995574.png" alt="Education" class="w-20 h-20 sm:w-28 sm:h-28">
    </div>

    @yield('content')

    {{-- Footer --}}
    <footer class="mt-6 py-4 text-center text-gray-500 border-t dark:border-gray-700 text-sm sm:text-base">
      <p>&copy; {{ date('Y') }} Sistem Absensi | Dibuat dengan ❤️ untuk pendidikan</p>
    </footer>
  </main>
</div>
</body>
</html>
