@extends('layouts.guru.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-calendar-alt"></i> Jadwal Guru</h2>
    
    <div id="calendar"></div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek', // tampil mingguan
        slotMinTime: "07:00:00",     // jam mulai sekolah
        slotMaxTime: "17:00:00",     // jam selesai sekolah
        allDaySlot: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($jadwal->map(function($item) {
            $mapHari = [
                'Senin'  => '2025-09-01',
                'Selasa' => '2025-09-02',
                'Rabu'   => '2025-09-03',
                'Kamis'  => '2025-09-04',
                'Jumat'  => '2025-09-05',
                'Sabtu'  => '2025-09-06',
                'Minggu' => '2025-09-07',
            ];

            $tanggal = $mapHari[$item->hari] ?? now()->toDateString();

            return [
                'title' => $item->mata_pelajaran . ' - ' . $item->kelas,
                'start' => $tanggal . 'T' . $item->jam_mulai,
                'end'   => $tanggal . 'T' . $item->jam_selesai,
            ];
        }))
    });
    calendar.render();
});
</script>
@endpush
