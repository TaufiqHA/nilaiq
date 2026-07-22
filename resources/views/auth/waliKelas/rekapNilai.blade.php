@extends('layouts.waliKelas')

@section('title', 'Rekap Nilai Kelas')

@section('content')
<!-- CSS Helper for Printing -->
<style>
@media print {
    /* Hide layout chrome and interactive controls */
    #default-sidebar, 
    nav, 
    .no-print,
    #page-alert-container,
    .breadcrumb-nav,
    .stats-grid,
    .toolbar-section,
    button,
    header {
        display: none !important;
    }

    body {
        background: white !important;
        color: black !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: sans-serif !important;
        font-size: 9pt !important;
    }

    .sm\:ml-64 {
        margin-left: 0 !important;
    }

    .p-0, .sm\:p-6, .p-4 {
        padding: 0 !important;
    }

    .border-dashed, .border, .shadow-xs, .bg-white, .dark\:bg-neutral-primary-soft {
        border: none !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }

    /* Print Table Styling */
    .print-table-container {
        width: 100% !important;
        overflow: visible !important;
        border: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
    }

    table#rekap-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 15px !important;
        border: 1px solid #000000 !important;
    }

    table#rekap-table th, table#rekap-table td {
        border: 1px solid #000000 !important;
        padding: 4px 6px !important;
        font-size: 8.5pt !important;
        color: #000000 !important;
        background-color: transparent !important;
    }

    table#rekap-table th {
        font-weight: bold !important;
        text-align: center !important;
        background-color: #f2f2f2 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Specific borderless override for meta info table at top */
    table.print-info-table, table.print-info-table tr, table.print-info-table td {
        border: none !important;
        padding: 2px 4px 2px 0 !important;
        background-color: transparent !important;
        color: #000000 !important;
    }
    
    .print-header-title {
        display: block !important;
        text-align: left !important;
        margin-bottom: 20px !important;
    }
    
    @page {
        size: landscape;
        margin: 1cm;
    }
}

/* Hide print title in normal screen view */
.print-header-title {
    display: none;
}
</style>

<!-- Container Utama Rekap Nilai -->
<div class="p-0 sm:p-6 border-0 sm:border border-default border-dashed rounded-none sm:rounded-base bg-transparent sm:bg-white/40 dark:sm:bg-neutral-secondary-medium/20 backdrop-blur-none sm:backdrop-blur-md space-y-4 sm:space-y-6 w-full">

    <!-- Print Title Header -->
    <div class="print-header-title">
        <h1 class="text-base font-bold tracking-wide uppercase text-black">REKAPITULASI HASIL CAPAIAN KOMPETENSI</h1>
        <h2 class="text-base font-normal uppercase text-black">{{ $settingsWaliKelas->school_name ?? 'SMP NEGERI 1 TOMPOBULU' }}</h2>
        <h3 class="text-base font-normal uppercase text-black">TAHUN PELAJARAN {{ $classWaliKelas && $classWaliKelas->academicYear ? $classWaliKelas->academicYear->year : '-' }}</h3>
        
        <table class="w-auto border-none mt-4 text-xs font-bold text-black print-info-table">
            <tr class="border-none">
                <td class="border-none p-0 pr-2 pb-1 text-left whitespace-nowrap text-black">Kelas</td>
                <td class="border-none p-0 pr-2 pb-1 text-black">:</td>
                <td class="border-none p-0 pb-1 text-left text-black">{{ $classWaliKelas ? $classWaliKelas->name : '-' }}</td>
            </tr>
            <tr class="border-none">
                <td class="border-none p-0 pr-2 text-left whitespace-nowrap text-black">Semester</td>
                <td class="border-none p-0 pr-2 text-black">:</td>
                <td class="border-none p-0 text-left text-black">{{ $classWaliKelas && $classWaliKelas->academicYear ? ucfirst(strtolower($classWaliKelas->academicYear->semester)) : '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Header Section & Breadcrumb -->
    <div class="border-b border-default pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 breadcrumb-nav">
        <div>
            <!-- Breadcrumb -->
            <nav class="flex mb-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse text-xs">
                    <li class="inline-flex items-center">
                        <a href="{{ Route::has('wali-kelas.dashboard') ? route('wali-kelas.dashboard') : '#' }}" class="inline-flex items-center text-xs font-medium text-body hover:text-fg-brand">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/></svg>
                            Wali Kelas
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5 rtl:rotate-180 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                            <span class="inline-flex items-center text-xs font-bold text-heading">Rekap Nilai</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Rekap Nilai Siswa</h1>
            <p class="text-xs text-body mt-0.5">Lihat rangkuman nilai seluruh mata pelajaran, jumlah nilai, rata-rata, dan peringkat kelas.</p>
        </div>

        <!-- Class Badge Banner -->
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-neutral-primary-soft border border-default shadow-xs rounded-base">
            <div class="p-2.5 bg-brand text-white rounded-lg shadow-xs shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] uppercase font-extrabold tracking-wider text-body">Kelas yang Diampu</span>
                <p class="text-sm font-extrabold text-heading">
                    {{ $classWaliKelas ? $classWaliKelas->name : 'Belum Dikonfigurasi' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="page-alert-container" class="w-full space-y-3 no-print">
        @if(!$classWaliKelas)
            <div class="p-4 text-sm text-amber-800 bg-amber-50 border border-amber-300 rounded-base flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Anda belum mengatur informasi kelas. Silakan atur informasi kelas terlebih dahulu.</span>
                </div>
                <a href="{{ route('wali-kelas.informasi-kelas') }}" class="px-3 py-1.5 bg-amber-600 text-white text-xs font-bold rounded-base hover:bg-amber-700 transition-colors">Atur Kelas</a>
            </div>
        @endif
    </div>

    @if($classWaliKelas)
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 stats-grid">
            <div class="p-4 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
                <div class="p-2.5 bg-brand-soft text-fg-brand rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-body tracking-wider block">Total Siswa</span>
                    <span class="text-lg font-extrabold text-heading">{{ $students->count() }} <span class="text-xs font-normal text-body">Siswa</span></span>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
                <div class="p-2.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300 rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-body tracking-wider block">Rata-rata Kelas</span>
                    <span class="text-lg font-extrabold text-heading">{{ number_format($classAverage, 2) }}</span>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300 rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 11l7-7 7 7M12 4v16"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-body tracking-wider block">Rata-rata Tertinggi</span>
                    <span class="text-lg font-extrabold text-heading">{{ number_format($highestAverage, 2) }}</span>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
                <div class="p-2.5 bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-300 rounded-xl">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m7 7V4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-body tracking-wider block">Rata-rata Terendah</span>
                    <span class="text-lg font-extrabold text-heading">{{ number_format($lowestAverage, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Toolbar Action Section -->
        <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 toolbar-section">
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-body">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                </div>
                <input type="text" id="student-search" oninput="filterStudentTable()" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 p-2.5 placeholder:text-body" placeholder="Cari NIS, nama siswa..." />
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="w-full md:w-auto px-4 py-2.5 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 00-2 2v2z"/>
                    </svg>
                    <span>Cetak Rekap Nilai</span>
                </button>
            </div>
        </div>

        <!-- Grade Recap Data Table -->
        <div class="rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs overflow-hidden print-table-container">
            <div class="overflow-x-auto scrollbar-thin">
                <table id="rekap-table" class="w-full text-xs md:text-sm text-left text-body border-collapse">
                    <thead class="text-[10px] md:text-xs font-bold text-heading uppercase bg-neutral-tertiary">
                        <tr class="border-b border-default">
                            <th scope="colgroup" colspan="3" class="px-3 py-2 text-center border-e border-default">Nomor</th>
                            <th scope="col" rowspan="2" class="px-4 py-3 border-e border-default text-left align-middle">Nama Peserta Didik</th>
                            <th scope="colgroup" colspan="{{ max(1, $mapelSettings->count()) }}" class="px-3 py-2 text-center border-e border-default">Mata Pelajaran</th>
                            <th scope="col" rowspan="2" class="px-3 py-3 border-e border-default text-center align-middle w-20">Jumlah</th>
                            <th scope="col" rowspan="2" class="px-3 py-3 border-e border-default text-center align-middle w-20">Rata-rata</th>
                            <th scope="col" rowspan="2" class="px-3 py-3 text-center align-middle w-20">Peringkat</th>
                        </tr>
                        <tr class="border-b border-default">
                            <th scope="col" class="px-2 py-2 text-center border-e border-default w-12">Urut</th>
                            <th scope="col" class="px-3 py-2 border-e border-default w-20">NIS</th>
                            <th scope="col" class="px-3 py-2 border-e border-default w-24">NISN</th>
                            @forelse($mapelSettings as $mapel)
                                <th scope="col" class="px-3 py-2 text-center border-e border-default min-w-16" title="{{ $mapel->mapel }}">{{ $mapel->mapel }}</th>
                            @empty
                                <th scope="col" class="px-3 py-2 text-center border-e border-default">-</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default">
                        @forelse($students as $index => $student)
                            @php
                                $scoreInfo = $studentScoresData[$student->id] ?? [
                                    'scores' => [],
                                    'jumlah' => 0,
                                    'rata_rata' => 0,
                                    'count' => 0
                                ];
                                $rank = $ranks[$student->id] ?? '-';
                            @endphp
                            <tr class="student-row hover:bg-neutral-secondary-medium/50 transition-colors"
                                data-search="{{ strtolower($student->name . ' ' . $student->nis . ' ' . $student->nisn) }}">
                                <td class="px-2 py-3 text-center font-bold text-heading border-e border-default">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 text-heading border-e border-default whitespace-nowrap">{{ $student->nis ?? '-' }}</td>
                                <td class="px-3 py-3 text-body border-e border-default whitespace-nowrap">{{ $student->nisn ?? '-' }}</td>
                                <td class="px-4 py-3 font-extrabold text-heading border-e border-default whitespace-nowrap">
                                    {{ $student->name }}
                                </td>
                                
                                <!-- Subjects Scores -->
                                @forelse($mapelSettings as $mapel)
                                    @php
                                        $scoreVal = $scoreInfo['scores'][$mapel->id] ?? null;
                                    @endphp
                                    <td class="px-3 py-3 text-center font-bold text-heading border-e border-default">
                                        {{ !is_null($scoreVal) ? $scoreVal : '' }}
                                    </td>
                                @empty
                                    <td class="px-3 py-3 text-center border-e border-default">-</td>
                                @endforelse

                                <td class="px-3 py-3 text-center font-extrabold text-heading border-e border-default">
                                    {{ $scoreInfo['count'] > 0 ? $scoreInfo['jumlah'] : '' }}
                                </td>
                                <td class="px-3 py-3 text-center font-extrabold text-brand border-e border-default">
                                    {{ $scoreInfo['count'] > 0 ? number_format($scoreInfo['rata_rata'], 2) : '' }}
                                </td>
                                <td class="px-3 py-3 text-center font-extrabold text-heading">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full {{ $rank == 1 ? 'bg-amber-100 text-amber-800 font-black' : ($rank <= 3 ? 'bg-neutral-tertiary text-heading' : '') }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 7 + max(1, $mapelSettings->count()) }}" class="px-4 py-8 text-center text-body">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <svg class="w-10 h-10 text-body/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="font-bold text-heading">Belum Ada Data Siswa</p>
                                        <p class="text-xs text-body">Tambahkan data siswa terlebih dahulu di menu <strong>Data Siswa</strong>.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty Class State -->
        <div class="p-8 rounded-base bg-white dark:bg-neutral-primary-soft border border-default text-center">
            <svg class="w-12 h-12 text-body/40 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-base font-bold text-heading mb-1">Informasi Kelas Belum Diatur</h3>
            <p class="text-xs text-body max-w-md mx-auto mb-4">Anda harus mengatur kelas yang Anda ampu dan mengisi data siswa terlebih dahulu sebelum dapat mengakses halaman rekap nilai.</p>
            <a href="{{ route('wali-kelas.informasi-kelas') }}" class="px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors inline-flex items-center gap-2">
                <span>Atur Informasi Kelas</span>
            </a>
        </div>
    @endif
</div>

<!-- JavaScript Table Filter -->
<script>
function filterStudentTable() {
    const query = document.getElementById('student-search').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.student-row');
    
    rows.forEach(row => {
        const searchText = row.getAttribute('data-search');
        if (searchText.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
