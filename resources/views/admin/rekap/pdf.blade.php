<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran {{ ucfirst($jenisRekap) }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        
        .info-table {
            margin-bottom: 20px;
        }
        
        .info-table table {
            border: none;
        }
        
        .info-table td {
            padding: 3px 10px 3px 0;
            border: none;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .data-table td:first-child {
            text-align: center;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            display: inline-block;
            min-width: 50px;
        }
        
        .status-hadir { background-color: #28a745; }
        .status-izin { background-color: #ffc107; color: #000; }
        .status-sakit { background-color: #17a2b8; }
        .status-alpa { background-color: #dc3545; }
        
        .statistik {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .statistik h3 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stats-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ccc;
        }
        
        .stats-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }
        
        .stats-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Rekap Kehadiran {{ ucfirst(str_replace('_', ' ', $jenisRekap)) }}</h1>
        <h2>Sistem Absensi Sekolah</h2>
    </div>

    <!-- Info Periode -->
    <div class="info-table">
        <table>
            <tr>
                <td style="width: 120px;"><strong>Periode</strong></td>
                <td>: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Rekap</strong></td>
                <td>: {{ ucfirst(str_replace('_', ' ', $jenisRekap)) }}</td>
            </tr>
            @if($kelas)
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ $kelas }}</td>
            </tr>
            @endif
            @if($status)
            <tr>
                <td><strong>Status Filter</strong></td>
                <td>: {{ ucfirst($status) }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ $tanggal_cetak }}</td>
            </tr>
        </table>
    </div>

    @if($jenisRekap == 'gabungan')
        <!-- Rekap Gabungan -->
        
        <!-- Data Siswa -->
        <h3 style="color: #333; border-bottom: 2px solid #333; padding-bottom: 5px;">KEHADIRAN SISWA</h3>
        @if($dataSiswa && $dataSiswa->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        @if($jenisRekap == 'siswa')
                            <th style="width: 25%;">Nama Siswa</th>
                            <th style="width: 15%;">NIS</th>
                            <th style="width: 10%;">Kelas</th>
                        @else
                            <th style="width: 25%;">Nama Guru</th>
                            <th style="width: 15%;">NIP</th>
                            <th style="width: 20%;">Mata Pelajaran</th>
                        @endif
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataRekap as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        @if($jenisRekap == 'siswa')
                            <td>{{ $item->siswa->nama_siswa ?? '-' }}</td>
                            <td>{{ $item->siswa->nis ?? '-' }}</td>
                            <td>{{ $item->siswa->kelas ?? '-' }}</td>
                        @else
                            <td>{{ $item->guru->nama_guru ?? '-' }}</td>
                            <td>{{ $item->guru->nip ?? '-' }}</td>
                            <td>{{ $item->guru->mata_pelajaran ?? '-' }}</td>
                        @endif
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($item->status) }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; font-style: italic; margin: 50px 0;">
                Tidak ada data kehadiran yang ditemukan untuk periode yang dipilih
            </p>
        @endif
    @endif

    <!-- Statistik -->
    @if(($jenisRekap != 'gabungan' && $dataRekap && $dataRekap->count() > 0) || 
        ($jenisRekap == 'gabungan' && (($dataSiswa && $dataSiswa->count() > 0) || ($dataGuru && $dataGuru->count() > 0))))
    <div class="statistik">
        <h3>STATISTIK KEHADIRAN</h3>
        
        @if($jenisRekap == 'gabungan')
            <!-- Statistik untuk Gabungan -->
            @php
                $totalDataSiswa = $dataSiswa ? $dataSiswa->count() : 0;
                $totalDataGuru = $dataGuru ? $dataGuru->count() : 0;
                $allData = collect();
                if($dataSiswa) $allData = $allData->merge($dataSiswa);
                if($dataGuru) $allData = $allData->merge($dataGuru);
                
                $hadirTotal = $allData->where('status', 'Hadir')->count();
                $izinTotal = $allData->where('status', 'Izin')->count();
                $sakitTotal = $allData->where('status', 'Sakit')->count();
                $alpaTotal = $allData->where('status', 'Alpa')->count();
            @endphp
            
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <strong>SISWA</strong><br>
                        @if($dataSiswa)
                            @php
                                $hadirSiswa = $dataSiswa->where('status', 'Hadir')->count();
                                $izinSiswa = $dataSiswa->where('status', 'Izin')->count();
                                $sakitSiswa = $dataSiswa->where('status', 'Sakit')->count();
                                $alpaSiswa = $dataSiswa->where('status', 'Alpa')->count();
                            @endphp
                            Hadir: {{ $hadirSiswa }}<br>
                            Izin: {{ $izinSiswa }}<br>
                            Sakit: {{ $sakitSiswa }}<br>
                            Alpa: {{ $alpaSiswa }}<br>
                            <strong>Total: {{ $totalDataSiswa }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <strong>GURU</strong><br>
                        @if($dataGuru)
                            @php
                                $hadirGuru = $dataGuru->where('status', 'Hadir')->count();
                                $izinGuru = $dataGuru->where('status', 'Izin')->count();
                                $sakitGuru = $dataGuru->where('status', 'Sakit')->count();
                                $alpaGuru = $dataGuru->where('status', 'Alpa')->count();
                            @endphp
                            Hadir: {{ $hadirGuru }}<br>
                            Izin: {{ $izinGuru }}<br>
                            Sakit: {{ $sakitGuru }}<br>
                            Alpa: {{ $alpaGuru }}<br>
                            <strong>Total: {{ $totalDataGuru }}</strong>
                        @else
                            Tidak ada data
                        @endif
                    </td>
                </tr>
            </table>
            
            <!-- Total Keseluruhan -->
            <div style="border-top: 2px solid #333; padding-top: 10px; margin-top: 20px;">
                <strong>TOTAL KESELURUHAN</strong><br>
                <div class="stats-grid">
                    <div class="stats-item">
                        <span class="stats-number" style="color: #28a745;">{{ $hadirTotal }}</span>
                        <div class="stats-label">Hadir</div>
                    </div>
                    <div class="stats-item">
                        <span class="stats-number" style="color: #ffc107;">{{ $izinTotal }}</span>
                        <div class="stats-label">Izin</div>
                    </div>
                    <div class="stats-item">
                        <span class="stats-number" style="color: #17a2b8;">{{ $sakitTotal }}</span>
                        <div class="stats-label">Sakit</div>
                    </div>
                    <div class="stats-item">
                        <span class="stats-number" style="color: #dc3545;">{{ $alpaTotal }}</span>
                        <div class="stats-label">Alpa</div>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 15px; font-size: 14px;">
                    <strong>Grand Total: {{ $hadirTotal + $izinTotal + $sakitTotal + $alpaTotal }} Data</strong>
                </div>
            </div>
        @else
            <!-- Statistik untuk Siswa atau Guru -->
            @php
                $hadirCount = $dataRekap->where('status', 'Hadir')->count();
                $izinCount = $dataRekap->where('status', 'Izin')->count();
                $sakitCount = $dataRekap->where('status', 'Sakit')->count();
                $alpaCount = $dataRekap->where('status', 'Alpa')->count();
                $totalCount = $dataRekap->count();
                
                $hadirPersen = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100, 1) : 0;
                $izinPersen = $totalCount > 0 ? round(($izinCount / $totalCount) * 100, 1) : 0;
                $sakitPersen = $totalCount > 0 ? round(($sakitCount / $totalCount) * 100, 1) : 0;
                $alpaPersen = $totalCount > 0 ? round(($alpaCount / $totalCount) * 100, 1) : 0;
            @endphp
            
            <div class="stats-grid">
                <div class="stats-item">
                    <span class="stats-number" style="color: #28a745;">{{ $hadirCount }}</span>
                    <div class="stats-label">Hadir ({{ $hadirPersen }}%)</div>
                </div>
                <div class="stats-item">
                    <span class="stats-number" style="color: #ffc107;">{{ $izinCount }}</span>
                    <div class="stats-label">Izin ({{ $izinPersen }}%)</div>
                </div>
                <div class="stats-item">
                    <span class="stats-number" style="color: #17a2b8;">{{ $sakitCount }}</span>
                    <div class="stats-label">Sakit ({{ $sakitPersen }}%)</div>
                </div>
                <div class="stats-item">
                    <span class="stats-number" style="color: #dc3545;">{{ $alpaCount }}</span>
                    <div class="stats-label">Alpa ({{ $alpaPersen }}%)</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 15px; font-size: 14px;">
                <strong>Total Data: {{ $totalCount }}</strong>
            </div>
            
            <!-- Ringkasan Kehadiran -->
            @if($jenisRekap == 'siswa')
                @php
                    $uniqueSiswa = $dataRekap->groupBy('siswa.id_siswa')->map(function($items) {
                        return [
                            'nama' => $items->first()->siswa->nama_siswa,
                            'kelas' => $items->first()->siswa->kelas,
                            'hadir' => $items->where('status', 'Hadir')->count(),
                            'izin' => $items->where('status', 'Izin')->count(),
                            'sakit' => $items->where('status', 'Sakit')->count(),
                            'alpa' => $items->where('status', 'Alpa')->count(),
                            'total' => $items->count()
                        ];
                    })->sortByDesc('total');
                @endphp
                
                @if($uniqueSiswa->count() > 0)
                <div style="margin-top: 25px;">
                    <h4 style="font-size: 12px; margin-bottom: 10px;">RINGKASAN PER SISWA (Top 10)</h4>
                    <table class="data-table" style="font-size: 10px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Alpa</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uniqueSiswa->take(10) as $index => $siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $siswa['nama'] }}</td>
                                <td>{{ $siswa['kelas'] }}</td>
                                <td>{{ $siswa['hadir'] }}</td>
                                <td>{{ $siswa['izin'] }}</td>
                                <td>{{ $siswa['sakit'] }}</td>
                                <td>{{ $siswa['alpa'] }}</td>
                                <td><strong>{{ $siswa['total'] }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endif
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>Sistem Absensi Sekolah - Laporan Rekap Kehadiran</p>
    </div>
</body>
</html>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Siswa</th>
                        <th style="width: 15%;">NIS</th>
                        <th style="width: 10%;">Kelas</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataSiswa as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->siswa->nama_siswa ?? '-' }}</td>
                        <td>{{ $item->siswa->nis ?? '-' }}</td>
                        <td>{{ $item->siswa->kelas ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($item->status) }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; font-style: italic;">Tidak ada data kehadiran siswa</p>
        @endif

        <div class="page-break"></div>

        <!-- Data Guru -->
        <h3 style="color: #333; border-bottom: 2px solid #333; padding-bottom: 5px;">KEHADIRAN GURU</h3>
        @if($dataGuru && $dataGuru->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Guru</th>
                        <th style="width: 15%;">NIP</th>
                        <th style="width: 20%;">Mata Pelajaran</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 5%;">Ket.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataGuru as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->guru->nama_guru ?? '-' }}</td>
                        <td>{{ $item->guru->nip ?? '-' }}</td>
                        <td>{{ $item->guru->mata_pelajaran ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($item->status) }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; font-style: italic;">Tidak ada data kehadiran guru</p>
        @endif

    @else
        <!-- Rekap Siswa atau Guru -->
        @if($dataRekap && $dataRekap->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        