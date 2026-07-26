@extends('layouts.waliKelas')

@section('title', 'Nilai Mata Pelajaran')

@section('content')
<!-- Container Utama Nilai Mata Pelajaran -->
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
                            <span class="inline-flex items-center text-xs font-bold text-heading">Nilai Mata Pelajaran</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Nilai Mata Pelajaran</h1>
            <p class="text-xs text-body mt-0.5">Pilih mata pelajaran untuk menginput nilai dan capaian pembelajaran siswa.</p>
        </div>

        <!-- Class Badge Banner -->
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-neutral-primary-soft border border-default shadow-xs rounded-base">
            <div class="p-2.5 bg-brand text-white rounded-lg shadow-xs shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
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

    <!-- Mapel Selector & Info Card -->
    <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-default pb-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-soft text-fg-brand rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.03v13m0-13c-2.819-.831-7.5-3-7.5-3v13.75c0 .104.058.2.15.25 1.125.625 5.432 2.378 7.35 3M12 6.03c2.819-.831 7.5-3 7.5-3v13.75c0 .104-.058.2-.15.25-1.125.625-5.432 2.378-7.35 3M12 19.03V20" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-body tracking-wider">Mata Pelajaran Aktif</span>
                    <h2 class="text-base font-extrabold text-heading">{{ $selectedMapel ? $selectedMapel->mapel : 'Pilih Mata Pelajaran' }}</h2>
                </div>
            </div>

            <!-- Detail Info Mapel Terpilih -->
            @if($selectedMapel)
                <div class="flex items-center gap-4 bg-neutral-tertiary px-4 py-2.5 rounded-base border border-default">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-body">Guru Pengampu</span>
                        <p class="text-xs font-extrabold text-heading">{{ $selectedMapel->guru ?? '-' }}</p>
                    </div>
                    <div class="h-6 w-px bg-default"></div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-body">KKM</span>
                        <p class="text-xs font-extrabold text-brand">{{ $selectedMapel->kkm ?? 75 }}</p>
                    </div>
                </div>
            @endif
        </div>

        @if($mapelSettings->isNotEmpty())
            <!-- Quick Mapel Pills Navigation -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                @foreach($mapelSettings as $mapel)
                    <a href="{{ route('wali-kelas.nilai-mapel', ['mapel_id' => $mapel->id]) }}"
                       class="px-3 py-1.5 text-xs font-bold whitespace-nowrap rounded-base transition-colors border {{ $selectedMapel && $selectedMapel->id == $mapel->id ? 'bg-brand text-white border-brand shadow-xs' : 'bg-neutral-secondary-medium text-body border-default hover:bg-neutral-tertiary hover:text-heading' }}">
                        {{ $mapel->mapel }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if(!$selectedMapel)
        <div class="p-8 rounded-base bg-white dark:bg-neutral-primary-soft border border-default text-center">
            <svg class="w-12 h-12 text-body/40 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.03v13m0-13c-2.819-.831-7.5-3-7.5-3v13.75c0 .104.058.2.15.25 1.125.625 5.432 2.378 7.35 3M12 6.03c2.819-.831 7.5-3 7.5-3v13.75c0 .104-.058.2-.15.25-1.125.625-5.432 2.378-7.35 3M12 19.03V20"/>
            </svg>
            <h3 class="text-base font-bold text-heading mb-1">Belum Ada Pengaturan Mata Pelajaran</h3>
            <p class="text-xs text-body max-w-md mx-auto mb-4">Silakan atur mata pelajaran di menu <strong>Master Data > Mapel Settings</strong> untuk mulai menginput nilai siswa.</p>
            <a href="{{ Route::has('wali-kelas.mapel-settings.index') ? route('wali-kelas.mapel-settings.index') : (Route::has('settings-wali-kelas.index') ? route('settings-wali-kelas.index') : (Route::has('master-data.index') ? route('master-data.index') : '#')) }}" class="px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors inline-flex items-center gap-2">
                <span>Atur Master Data</span>
            </a>
        </div>
    @else
        <!-- Form Batch Entry Nilai Siswa -->
        <form action="{{ route('wali-kelas.nilai-mapels.batch') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="mapel_id" value="{{ $selectedMapel->id }}">

            <!-- Statistics Chips & Search/Actions Toolbar -->
            @php
                $totalStudents = $students->count();
                $filledCount = $students->filter(function($s) use ($nilaiMapelsKeyed) {
                    $item = $nilaiMapelsKeyed->get($s->id);
                    return $item && !is_null($item->nilai);
                })->count();
                $emptyCount = $totalStudents - $filledCount;
            @endphp
            <div class="hidden sm:grid grid-cols-3 gap-3">
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
                        <p class="text-[11px] font-bold text-body uppercase tracking-wider">Nilai Terisi</p>
                        <p class="text-lg font-extrabold text-heading">{{ $filledCount }} <span class="text-xs font-normal text-body">siswa</span></p>
                    </div>
                </div>

                <div class="p-3.5 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
                    <div class="p-2 bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-body uppercase tracking-wider">Belum Ada Nilai</p>
                        <p class="text-lg font-extrabold text-heading">{{ $emptyCount }} <span class="text-xs font-normal text-body">siswa</span></p>
                    </div>
                </div>
            </div>

            <!-- Toolbar Action -->
            <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-body">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                    </div>
                    <input type="text" id="student-search" oninput="filterStudentTable()" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 p-2.5 placeholder:text-body" placeholder="Cari NIS, nama siswa..." />
                </div>
 
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button type="button" onclick="openImportModal()" class="w-full md:w-auto text-white bg-emerald-600 hover:bg-emerald-700 box-border border border-transparent focus:ring-4 focus:ring-emerald-300 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        <span>Import Excel</span>
                    </button>
                    
                    <button type="button" onclick="document.getElementById('scan-camera-input').click()" class="w-full md:w-auto text-white bg-indigo-600 hover:bg-indigo-700 box-border border border-transparent focus:ring-4 focus:ring-indigo-300 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        <span>Scan Foto Nilai</span>
                    </button>
                    <input type="file" id="scan-camera-input" accept="image/*" capture="environment" class="hidden" onchange="scanHandwrittenGrades(this)">
                </div>
            </div>

            <!-- Data Table Input Nilai Siswa -->
            <div class="rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="nilai-table" class="w-full text-xs md:text-sm text-left text-body">
                        <thead class="text-[11px] md:text-xs font-bold text-heading uppercase bg-neutral-tertiary border-b border-default whitespace-nowrap">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                                <th scope="col" class="px-4 py-3">NIS / NISN</th>
                                <th scope="col" class="px-4 py-3">Nama Siswa</th>
                                <th scope="col" class="px-4 py-3 text-center w-32">Nilai Akhir</th>
                                <th scope="col" class="px-4 py-3 text-center w-28">Status KKM</th>
                                <th scope="col" class="px-4 py-3">Capaian Pembelajaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-default">
                            @forelse($students as $index => $student)
                                @php
                                    $item = $nilaiMapelsKeyed->get($student->id);
                                    $currentNilai = $item ? $item->nilai : '';
                                    $currentCapaian = $item ? $item->capaian : '';
                                    $kkm = $selectedMapel->kkm ?? 75;
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
                                        <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <!-- Input Nilai -->
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <input type="number"
                                               id="score-{{ $student->id }}"
                                               name="scores[{{ $index }}][nilai]"
                                               value="{{ $currentNilai }}"
                                               min="0"
                                               max="100"
                                               oninput="updateKkmBadge(this, {{ $kkm }})"
                                               placeholder="0-100"
                                               class="score-input bg-neutral-secondary-medium border border-default text-heading font-extrabold text-center text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors" />
                                    </td>
                                    <!-- Status KKM Badge -->
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="kkm-badge inline-flex items-center px-2.5 py-1 text-xs font-extrabold rounded-full {{ is_numeric($currentNilai) ? ($currentNilai >= $kkm ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300') : 'bg-neutral-tertiary text-body' }}">
                                            @if(is_numeric($currentNilai))
                                                {{ $currentNilai >= $kkm ? 'Tuntas' : 'Belum' }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                    <!-- Input Capaian -->
                                    <td class="px-4 py-3">
                                        <input type="text"
                                               name="scores[{{ $index }}][capaian]"
                                               value="{{ $currentCapaian }}"
                                               placeholder="Deskripsi capaian pembelajaran..."
                                               class="min-w-[200px] bg-neutral-secondary-medium border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-body">
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

                @if($students->isNotEmpty())
                    <div class="p-4 bg-neutral-tertiary border-t border-default flex items-center justify-between">
                        <span class="text-xs text-body font-medium">Menampilkan {{ $students->count() }} siswa</span>
                        <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan Nilai</span>
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @endif
</div>

<!-- Scan Loading Overlay -->
<div id="scan-loading-overlay" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-xs">
    <div class="bg-white dark:bg-neutral-primary-soft p-6 rounded-base border border-default shadow-lg max-w-sm w-full text-center mx-4">
        <svg class="animate-spin h-10 w-10 text-brand mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <h3 class="text-lg font-bold text-heading mb-1">Menganalisis Foto Nilai</h3>
        <p class="text-sm text-body">Membaca tulisan tangan nilai siswa menggunakan AI. Mohon tunggu beberapa saat...</p>
    </div>
</div>
 
<!-- MODAL IMPORT NILAI VIA EXCEL -->
@if($selectedMapel)
<div id="nilai-import-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default p-4 md:p-6 flex flex-col max-h-[90vh]">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5 shrink-0">
                <h3 id="import-modal-title" class="text-lg font-bold text-heading">
                    Import Nilai via Excel
                </h3>
                <button type="button" onclick="closeModal('nilai-import-modal')" class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <form action="{{ route('wali-kelas.nilai-mapels.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="mapel_id" value="{{ $selectedMapel->id }}">
                
                <!-- Modal body -->
                <div class="py-4 md:py-6 overflow-y-auto flex-1 space-y-6">
                    <!-- Instructions and Template download -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start bg-neutral-secondary-medium/50 dark:bg-neutral-primary-soft border border-default p-4 rounded-base">
                        <div>
                            <h4 class="font-bold text-heading text-sm mb-2">Petunjuk Penggunaan:</h4>
                            <ul class="list-disc pl-5 text-xs text-body space-y-1">
                                <li>Format file harus berupa Excel (<strong>.xlsx</strong> / <strong>.xls</strong>).</li>
                                <li>Pastikan baris pertama berisi header kolom yang sesuai dengan template: <strong>ID Siswa (Jangan Diubah)</strong>, <strong>NIS</strong>, <strong>NISN</strong>, <strong>Nama Siswa</strong>, <strong>Nilai Akhir (0-100)</strong>, dan <strong>Capaian Pembelajaran</strong>.</li>
                                <li>Jangan mengubah nilai kolom <strong>ID Siswa</strong> karena kolom tersebut digunakan sebagai identifikasi utama data siswa di database.</li>
                                <li>Nilai akhir harus berupa angka bulat antara <strong>0 - 100</strong>.</li>
                            </ul>
                        </div>
                        <div class="flex flex-col justify-center items-center h-full border-t md:border-t-0 md:border-l border-default pt-4 md:pt-0 md:pl-6 text-center">
                            <p class="text-xs text-body mb-3 font-semibold">Gunakan template di bawah untuk mendapatkan daftar siswa terbaru:</p>
                            <a href="{{ route('wali-kelas.nilai-mapels.export', ['mapel_id' => $selectedMapel->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-default bg-neutral-secondary-medium dark:bg-neutral-primary-soft text-heading hover:bg-neutral-tertiary-medium dark:hover:bg-neutral-tertiary text-xs font-bold rounded-base transition-colors duration-150 shadow-xs cursor-pointer">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh Template Excel
                            </a>
                        </div>
                    </div>
 
                    <!-- Dropzone / File input -->
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-heading">Pilih File Spreadsheet</label>
                        <div class="flex items-center justify-center w-full">
                            <label id="import-dropzone" for="import-file-input" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-default rounded-base cursor-pointer bg-neutral-secondary-medium/20 hover:bg-neutral-secondary-medium/40 transition-colors duration-150">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-2.5 text-neutral-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-1 text-xs text-body"><span class="font-bold">Klik untuk unggah</span> atau drag and drop</p>
                                    <p class="text-[10px] text-body opacity-80">XLSX atau XLS (Maks. 5MB)</p>
                                </div>
                                <input id="import-file-input" name="file" type="file" accept=".xlsx, .xls" required class="hidden" onchange="handleFileSelect(event)" />
                            </label>
                        </div>
                        <div id="file-name-display" class="hidden mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 text-center"></div>
                    </div>
 
                    <!-- Validation Warnings Container -->
                    <div id="import-errors-container" class="hidden text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/20 p-4 rounded-base border border-red-200 dark:border-red-900/30">
                        <div class="flex items-center gap-1.5 mb-2 font-bold">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Ditemukan beberapa kesalahan:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-1 text-xs" id="import-errors-list"></ul>
                    </div>
 
                    <!-- Preview Table Container -->
                    <div id="import-preview-section" class="hidden space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-heading text-sm">Preview Data Yang Akan Diimport:</h4>
                            <span id="preview-count" class="px-2 py-0.5 rounded text-xs font-bold bg-brand/10 text-brand border border-brand/20">0 Data</span>
                        </div>
                        <div class="relative overflow-x-auto border border-default rounded-base max-h-[30vh]">
                            <table class="w-full text-xs text-left text-body">
                                <thead class="text-[10px] font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-4 py-2.5 text-center w-10">Baris</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[80px]">ID Siswa</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[80px]">NIS</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[150px]">Nama Siswa</th>
                                        <th scope="col" class="px-4 py-2.5 text-center min-w-[80px]">Nilai Akhir</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[200px]">Capaian Pembelajaran</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[150px]">Status Validasi</th>
                                    </tr>
                                </thead>
                                <tbody id="import-preview-body" class="divide-y divide-default">
                                    <!-- JS will render preview rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-3 border-t border-default pt-4 md:pt-5 shrink-0">
                    <button onclick="closeModal('nilai-import-modal')" type="button" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-semibold leading-5 rounded-base text-sm px-5 py-2.5 focus:outline-none cursor-pointer">Batal</button>
                    <button type="submit" id="btn-confirm-import" class="inline-flex items-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-bold leading-5 rounded-base text-sm px-5 py-2.5 focus:outline-none cursor-pointer disabled:bg-neutral-tertiary disabled:text-fg-disabled disabled:cursor-not-allowed" disabled>
                        <svg class="w-4 h-4 me-1.5 -ms-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
 
<script>
    function dismissAlert(alertEl) {
        if (alertEl) {
            alertEl.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => alertEl.remove(), 300);
        }
    }

    function filterStudentTable() {
        const query = document.getElementById('student-search').value.toLowerCase().trim();
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

    function updateKkmBadge(inputEl, kkm) {
        const row = inputEl.closest('tr');
        const badgeEl = row.querySelector('.kkm-badge');
        const val = inputEl.value;

        if (val === '' || isNaN(val)) {
            badgeEl.textContent = '-';
            badgeEl.className = 'kkm-badge inline-flex items-center px-2.5 py-1 text-xs font-extrabold rounded-full bg-neutral-tertiary text-body';
        } else {
            const score = parseInt(val, 10);
            if (score >= kkm) {
                badgeEl.textContent = 'Tuntas';
                badgeEl.className = 'kkm-badge inline-flex items-center px-2.5 py-1 text-xs font-extrabold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
            } else {
                badgeEl.textContent = 'Belum';
                badgeEl.className = 'kkm-badge inline-flex items-center px-2.5 py-1 text-xs font-extrabold rounded-full bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
            }
        }
    }

    const allowedStudentIds = @json($students->pluck('id'));
    const studentMap = @json($students->keyBy('id')->map(function($s) {
        return [
            'name' => $s->name,
            'nis' => $s->nis,
            'nisn' => $s->nisn
        ];
    }));

    function openImportModal() {
        const fileInput = document.getElementById('import-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        const fileDisplay = document.getElementById('file-name-display');
        if (fileDisplay) {
            fileDisplay.classList.add('hidden');
            fileDisplay.innerText = '';
        }

        const previewSection = document.getElementById('import-preview-section');
        if (previewSection) {
            previewSection.classList.add('hidden');
        }
        const previewBody = document.getElementById('import-preview-body');
        if (previewBody) {
            previewBody.innerHTML = '';
        }
        const errorsContainer = document.getElementById('import-errors-container');
        if (errorsContainer) {
            errorsContainer.classList.add('hidden');
        }
        const errorsList = document.getElementById('import-errors-list');
        if (errorsList) {
            errorsList.innerHTML = '';
        }
        const confirmBtn = document.getElementById('btn-confirm-import');
        if (confirmBtn) {
            confirmBtn.disabled = true;
        }

        const modal = document.getElementById('nilai-import-modal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function updateFileNameDisplay(event) {
        const input = event.target;
        const display = document.getElementById('file-name-display');
        if (display && input.files && input.files.length > 0) {
            display.innerText = "File terpilih: " + input.files[0].name;
            display.classList.remove('hidden');
        } else if (display) {
            display.classList.add('hidden');
        }
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        processFile(file);
    }

    function processFile(file) {
        if (!file) return;

        updateFileNameDisplay({ target: { files: [file] } });

        const fileExt = file.name.split('.').pop().toLowerCase();
        const maxSizeBytes = 5 * 1024 * 1024; // 5MB

        if (file.size > maxSizeBytes) {
            alert('Ukuran file melebihi batas 5MB.');
            return;
        }

        if (fileExt === 'xlsx' || fileExt === 'xls') {
            loadSheetJS(() => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const data = new Uint8Array(e.target.result);
                    try {
                        const workbook = XLSX.read(data, { type: 'array' });
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];
                        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                        parseExcelData(jsonData);
                    } catch (err) {
                        console.error(err);
                        alert('Gagal membaca file Excel. Harap pastikan file tidak rusak.');
                    }
                };
                reader.readAsArrayBuffer(file);
            });
        } else {
            alert('Format file tidak didukung. Harap pilih file XLSX atau XLS.');
        }
    }

    function loadSheetJS(callback) {
        if (typeof XLSX !== 'undefined') {
            callback();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
        script.onload = callback;
        script.onerror = () => {
            alert('Gagal memuat library parser Excel. Harap pastikan koneksi internet Anda aktif.');
        };
        document.head.appendChild(script);
    }

    function parseExcelData(rawData) {
        if (!rawData || rawData.length < 2) {
            alert('File kosong atau tidak memiliki data.');
            return;
        }

        const headers = rawData[0].map(h => String(h || '').trim().toLowerCase());
        const rows = rawData.slice(1);

        const headerMap = {
            'id': ['id siswa', 'id_siswa', 'id siswa (jangan diubah)'],
            'nis': ['nis'],
            'name': ['nama siswa', 'nama_siswa', 'nama'],
            'nilai': ['nilai akhir', 'nilai akhir (0-100)', 'nilai_akhir', 'nilai'],
            'capaian': ['capaian pembelajaran', 'capaian_pembelajaran', 'capaian']
        };

        const indexMap = {};
        Object.keys(headerMap).forEach(key => {
            indexMap[key] = headers.findIndex(h => headerMap[key].includes(h));
        });

        if (indexMap.id === -1) {
            alert('Kolom "ID Siswa" tidak ditemukan di file. Pastikan header kolom sudah benar sesuai template.');
            return;
        }

        const parsedRows = [];
        let hasValidationErrors = false;

        rows.forEach((row, i) => {
            if (!row || row.length === 0 || row.every(val => val === null || val === undefined || String(val).trim() === '')) {
                return;
            }

            const getVal = (key) => {
                const idx = indexMap[key];
                if (idx === -1 || idx === undefined || row[idx] === undefined || row[idx] === null) return '';
                return String(row[idx]).trim();
            };

            const studentId = getVal('id');
            const nis = getVal('nis');
            const name = getVal('name');
            const nilai = getVal('nilai');
            const capaian = getVal('capaian');

            const errors = [];

            if (!studentId) {
                errors.push('ID Siswa kosong');
            } else if (!studentMap[studentId]) {
                errors.push('Siswa bukan anggota kelas Anda');
            }

            if (nilai !== '') {
                const score = Number(nilai);
                if (isNaN(score) || score < 0 || score > 100) {
                    errors.push('Nilai harus berupa angka 0-100');
                }
            }

            if (errors.length > 0) {
                hasValidationErrors = true;
            }

            parsedRows.push({
                rowNum: i + 2,
                studentId: studentId,
                nis: nis || (studentMap[studentId] ? studentMap[studentId].nis : '-'),
                name: name || (studentMap[studentId] ? studentMap[studentId].name : '-'),
                nilai: nilai,
                capaian: capaian,
                errors: errors
            });
        });

        displayImportPreview(parsedRows, hasValidationErrors);
    }

    function displayImportPreview(parsedRows, hasValidationErrors) {
        const previewSection = document.getElementById('import-preview-section');
        const previewCount = document.getElementById('preview-count');
        const previewBody = document.getElementById('import-preview-body');
        const confirmBtn = document.getElementById('btn-confirm-import');
        const errorsContainer = document.getElementById('import-errors-container');
        const errorsList = document.getElementById('import-errors-list');

        previewBody.innerHTML = '';
        errorsList.innerHTML = '';
        errorsContainer.classList.add('hidden');

        previewCount.innerText = `${parsedRows.length} Baris`;
        previewSection.classList.remove('hidden');

        parsedRows.forEach(row => {
            const tr = document.createElement('tr');
            const hasErr = row.errors.length > 0;
            tr.className = hasErr
                ? 'bg-red-50/50 dark:bg-red-950/20 text-red-900 dark:text-red-300 border-b border-red-200 dark:border-red-900/30'
                : 'hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary border-b border-default';

            const validationStatus = hasErr
                ? `<span class="font-bold text-red-600 dark:text-red-400">${row.errors.join(', ')}</span>`
                : '<span class="text-emerald-600 dark:text-emerald-400 font-semibold">Valid</span>';

            tr.innerHTML = `
                <td class="px-4 py-3 text-center font-semibold select-none">${row.rowNum}</td>
                <td class="px-4 py-3 font-mono font-bold">${row.studentId || '-'}</td>
                <td class="px-4 py-3 font-mono">${row.nis || '-'}</td>
                <td class="px-4 py-3 font-bold">${row.name || '-'}</td>
                <td class="px-4 py-3 text-center font-bold">${row.nilai !== '' ? row.nilai : '-'}</td>
                <td class="px-4 py-3 max-w-xs truncate text-xs" title="${row.capaian}">${row.capaian || '-'}</td>
                <td class="px-4 py-3 whitespace-nowrap">${validationStatus}</td>
            `;
            previewBody.appendChild(tr);
        });

        if (hasValidationErrors) {
            confirmBtn.disabled = true;
            parsedRows.forEach(row => {
                if (row.errors.length > 0) {
                    const li = document.createElement('li');
                    li.innerText = `Baris ${row.rowNum} (${row.name || 'ID ' + row.studentId}): ${row.errors.join(', ')}`;
                    errorsList.appendChild(li);
                }
            });
            errorsContainer.classList.remove('hidden');
        } else {
            confirmBtn.disabled = false;
        }
    }

    function scanHandwrittenGrades(inputElement) {
        if (!inputElement.files || inputElement.files.length === 0) {
            return;
        }
        
        const file = inputElement.files[0];
        const overlay = document.getElementById('scan-loading-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }

        const studentsList = @json($students->map(function($s) {
            return ['id' => $s->id, 'name' => $s->name];
        }));

        const formData = new FormData();
        formData.append('image', file);
        formData.append('students', JSON.stringify(studentsList));

        fetch('{{ route("score-scan") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                if (!response.ok) {
                    throw data;
                }
                return data;
            } else {
                const text = await response.text();
                console.error('Raw response:', text);
                const match = text.match(/<title>(.*?)<\/title>/i);
                const errorTitle = match ? match[1] : 'Internal Server Error';
                throw { message: `Server returned non-JSON response (${response.status}): ${errorTitle}. Silakan periksa console log browser untuk detail error.` };
            }
        })
        .then(result => {
            if (result.success && result.data) {
                let successCount = 0;
                result.data.forEach(item => {
                    const studentId = item.student_id;
                    const score = item.score;
                    
                    const scoreInput = document.getElementById(`score-${studentId}`);
                    if (scoreInput) {
                        scoreInput.value = score !== null ? Math.round(score) : '';
                        updateKkmBadge(scoreInput, {{ $selectedMapel->kkm ?? 75 }});
                        successCount++;
                    }
                });
                
                alert(`Berhasil mendeteksi nilai untuk ${successCount} siswa.`);
            } else {
                alert('Gagal mendeteksi nilai dari gambar: ' + (result.message || 'Format tidak dikenal.'));
            }
        })
        .catch(error => {
            console.error('Scan Error:', error);
            alert('Gagal memproses gambar: ' + (error.message || 'Terjadi kesalahan pada server/API. Pastikan GEMINI_API_KEY sudah dikonfigurasi di file .env.'));
        })
        .finally(() => {
            inputElement.value = '';
            if (overlay) {
                overlay.classList.remove('flex');
                overlay.classList.add('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('import-dropzone');
        const fileInput = document.getElementById('import-file-input');

        if (dropzone && fileInput) {
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('bg-neutral-secondary-medium/50');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('bg-neutral-secondary-medium/50');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('bg-neutral-secondary-medium/50');
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    processFile(e.dataTransfer.files[0]);
                }
            });
        }
    });
</script>
@endsection
