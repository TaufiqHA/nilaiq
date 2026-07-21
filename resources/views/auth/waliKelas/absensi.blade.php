@extends('layouts.waliKelas')

@section('title', 'Rekap Absensi Siswa')

@section('content')
<!-- Container Utama Rekap Absensi -->
<div class="p-0 sm:p-6 border-0 sm:border border-default border-dashed rounded-none sm:rounded-base bg-transparent sm:bg-white/40 dark:sm:bg-neutral-secondary-medium/20 backdrop-blur-none sm:backdrop-blur-md space-y-4 sm:space-y-6 w-full">

    <!-- Header Section & Breadcrumb -->
    <div class="border-b border-default pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
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
                            <span class="inline-flex items-center text-xs font-bold text-heading">Absensi</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Rekap Absensi Siswa</h1>
            <p class="text-xs text-body mt-0.5">Kelola data rekapitulasi kehadiran (hadir, izin, sakit, alpa) untuk setiap siswa.</p>
        </div>

        <!-- Class Badge Banner -->
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-neutral-primary-soft border border-default shadow-xs rounded-base">
            <div class="p-2.5 bg-brand text-white rounded-lg shadow-xs shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
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
    <div id="page-alert-container" class="w-full space-y-3">
        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 text-sm text-fg-success-strong bg-success-soft border border-emerald-300/40 dark:bg-emerald-950/90 dark:text-emerald-300 dark:border-emerald-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="flex-1">{{ session('success') }}</p>
                <button type="button" onclick="dismissAlert(this.closest('[role=alert]'))" class="ms-auto text-emerald-600 hover:text-emerald-800 p-1 rounded-base transition-colors cursor-pointer" aria-label="Close">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error') || session('danger'))
            <div class="flex items-start sm:items-center p-4 text-sm text-fg-danger-strong bg-danger-soft border border-red-300/40 dark:bg-red-950/90 dark:text-red-300 dark:border-red-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="flex-1">{{ session('error') ?? session('danger') }}</p>
                <button type="button" onclick="dismissAlert(this.closest('[role=alert]'))" class="ms-auto text-red-600 hover:text-red-800 p-1 rounded-base transition-colors cursor-pointer" aria-label="Close">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 text-sm text-fg-danger-strong bg-danger-soft border border-red-300/40 dark:bg-red-950/90 dark:text-red-300 dark:border-red-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <div class="flex items-center justify-between mb-1">
                    <div class="font-bold">Terjadi kesalahan validasi:</div>
                    <button type="button" onclick="dismissAlert(this.closest('[role=alert]'))" class="text-red-600 hover:text-red-800 p-1 rounded-base transition-colors cursor-pointer" aria-label="Close">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

    <!-- Statistics Chips -->
    @php
        $totalStudents = $students->count();
        $hasAbsensiCount = $students->filter(function($s) {
            return $s->absensi !== null;
        })->count();
        $noAbsensiCount = $totalStudents - $hasAbsensiCount;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="p-3.5 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
            <div class="p-2 bg-brand-soft text-fg-brand rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-body uppercase tracking-wider">Total Siswa</p>
                <p class="text-lg font-extrabold text-heading">{{ $totalStudents }} <span class="text-xs font-normal text-body">siswa</span></p>
            </div>
        </div>

        <div class="p-3.5 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
            <div class="p-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-body uppercase tracking-wider">Absensi Terisi</p>
                <p class="text-lg font-extrabold text-heading">{{ $hasAbsensiCount }} <span class="text-xs font-normal text-body">siswa</span></p>
            </div>
        </div>

        <div class="p-3.5 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
            <div class="p-2 bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-body uppercase tracking-wider">Belum Ada Rekap</p>
                <p class="text-lg font-extrabold text-heading">{{ $noAbsensiCount }} <span class="text-xs font-normal text-body">siswa</span></p>
            </div>
        </div>
    </div>

    <!-- Action Toolbar (Search & Filter) -->
    <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <!-- Search Input -->
        <div class="flex items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative w-full sm:max-w-md">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-body">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                </div>
                <input type="text" id="absensi-search" oninput="filterAbsensiTable()" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 p-2.5 placeholder:text-body" placeholder="Cari NIS, nama siswa..." />
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table id="absensi-table" class="w-full text-xs md:text-sm text-left text-body">
                <thead class="text-[11px] md:text-xs font-bold text-heading uppercase bg-neutral-tertiary border-b border-default">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3">NIS / NISN</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3 text-center">Hadir</th>
                        <th scope="col" class="px-4 py-3 text-center">Izin</th>
                        <th scope="col" class="px-4 py-3 text-center">Sakit</th>
                        <th scope="col" class="px-4 py-3 text-center">Alpa</th>
                        <th scope="col" class="px-4 py-3 text-center">Total Hari</th>
                        <th scope="col" class="px-4 py-3 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse($students as $index => $student)
                        @php
                            $absensi = $student->absensi;
                            $hadir = $absensi->hadir ?? 0;
                            $izin = $absensi->izin ?? 0;
                            $sakit = $absensi->sakit ?? 0;
                            $alpa = $absensi->alpa ?? 0;
                            $totalDays = $hadir + $izin + $sakit + $alpa;
                        @endphp
                        <tr class="student-row hover:bg-neutral-secondary-medium/50 transition-colors"
                            data-search="{{ strtolower($student->name . ' ' . $student->nis . ' ' . $student->nisn) }}">
                            <td class="px-4 py-3 text-center font-bold text-heading">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-heading">{{ $student->nis ?? '-' }}</div>
                                <div class="text-[11px] text-body/70">{{ $student->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-extrabold text-heading whitespace-nowrap">
                                {{ $student->name }}
                            </td>
                            <!-- Hadir -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300 rounded-base">
                                    {{ $hadir }}
                                </span>
                            </td>
                            <!-- Izin -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold text-blue-700 bg-blue-100 dark:bg-blue-950 dark:text-blue-300 rounded-base">
                                    {{ $izin }}
                                </span>
                            </td>
                            <!-- Sakit -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold text-amber-700 bg-amber-100 dark:bg-amber-950 dark:text-amber-300 rounded-base">
                                    {{ $sakit }}
                                </span>
                            </td>
                            <!-- Alpa -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold text-red-700 bg-red-100 dark:bg-red-950 dark:text-red-300 rounded-base">
                                    {{ $alpa }}
                                </span>
                            </td>
                            <!-- Total Hari -->
                            <td class="px-4 py-3 text-center font-bold text-heading">
                                {{ $totalDays }} hari
                            </td>
                            <!-- Action Buttons -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        onclick="openEditAbsensiModal({{ $student->id }}, '{{ addslashes($student->name) }}', {{ $hadir }}, {{ $izin }}, {{ $sakit }}, {{ $alpa }}, {{ $absensi->id ?? 'null' }})"
                                        class="px-2.5 py-1.5 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors cursor-pointer inline-flex items-center gap-1"
                                        title="Kelola Rekap Absensi">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span>Kelola</span>
                                    </button>

                                    @if($absensi)
                                        <form action="{{ route('wali-kelas.absensis.destroy', $absensi->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data rekap absensi siswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:text-white hover:bg-red-600 border border-red-300 dark:border-red-800 rounded-base transition-colors cursor-pointer" title="Hapus Data Absensi">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-body">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-10 h-10 text-body/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
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
</div>

<!-- Modal Form Input / Edit Absensi -->
<div id="absensi-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-lg max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white dark:bg-neutral-primary-soft rounded-base shadow-lg border border-default overflow-hidden">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-default bg-neutral-tertiary">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-brand-soft text-fg-brand rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-heading">Kelola Rekap Absensi</h3>
                        <p id="modal-student-name" class="text-xs font-semibold text-fg-brand">Nama Siswa</p>
                    </div>
                </div>
                <button type="button" onclick="closeAbsensiModal()" class="text-body hover:text-heading rounded-base p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal body -->
            <form id="absensi-form" action="{{ route('wali-kelas.absensis.store') }}" method="POST" class="p-4 space-y-4 max-h-[75vh] overflow-y-auto">
                @csrf
                <input type="hidden" id="form-student-id" name="student_id" value="">

                <div class="grid grid-cols-2 gap-4">
                    <!-- 1. Hadir -->
                    <div>
                        <label for="hadir" class="block mb-1 text-xs font-bold text-heading">Hadir (Hari)</label>
                        <input type="number" id="hadir" name="hadir" min="0" value="0" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                    </div>

                    <!-- 2. Izin -->
                    <div>
                        <label for="izin" class="block mb-1 text-xs font-bold text-heading">Izin (Hari)</label>
                        <input type="number" id="izin" name="izin" min="0" value="0" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                    </div>

                    <!-- 3. Sakit -->
                    <div>
                        <label for="sakit" class="block mb-1 text-xs font-bold text-heading">Sakit (Hari)</label>
                        <input type="number" id="sakit" name="sakit" min="0" value="0" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                    </div>

                    <!-- 4. Alpa -->
                    <div>
                        <label for="alpa" class="block mb-1 text-xs font-bold text-heading">Tanpa Keterangan / Alpa (Hari)</label>
                        <input type="number" id="alpa" name="alpa" min="0" value="0" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-2 border-t border-default pt-3 mt-4">
                    <button type="button" onclick="closeAbsensiModal()" class="px-4 py-2 text-xs font-bold text-body bg-neutral-secondary-medium hover:bg-neutral-tertiary rounded-base border border-default transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function dismissAlert(alertEl) {
        if (alertEl) {
            alertEl.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => alertEl.remove(), 300);
        }
    }

    function filterAbsensiTable() {
        const query = document.getElementById('absensi-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.student-row');

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search') || '';
            if (searchText.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function openEditAbsensiModal(studentId, studentName, hadir, izin, sakit, alpa, absensiId) {
        document.getElementById('form-student-id').value = studentId;
        document.getElementById('modal-student-name').textContent = studentName;
        document.getElementById('hadir').value = hadir !== null && hadir !== undefined ? hadir : 0;
        document.getElementById('izin').value = izin !== null && izin !== undefined ? izin : 0;
        document.getElementById('sakit').value = sakit !== null && sakit !== undefined ? sakit : 0;
        document.getElementById('alpa').value = alpa !== null && alpa !== undefined ? alpa : 0;

        const modal = document.getElementById('absensi-modal');
        modal.classList.remove('hidden');
    }

    function closeAbsensiModal() {
        const modal = document.getElementById('absensi-modal');
        modal.classList.add('hidden');
    }
</script>
@endsection
