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

                {{-- <div class="flex items-center gap-2">
                    <button type="submit" class="w-full md:w-auto px-4 py-2.5 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors cursor-pointer inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Semua Nilai {{ $selectedMapel->mapel }}</span>
                    </button>
                </div> --}}
            </div>

            <!-- Data Table Input Nilai Siswa -->
            <div class="rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="nilai-table" class="w-full text-xs md:text-sm text-left text-body">
                        <thead class="text-[11px] md:text-xs font-bold text-heading uppercase bg-neutral-tertiary border-b border-default">
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
                                               class="bg-neutral-secondary-medium border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors" />
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
</script>
@endsection
