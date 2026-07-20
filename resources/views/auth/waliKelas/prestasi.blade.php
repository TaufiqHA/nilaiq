@extends('layouts.waliKelas')

@section('title', 'Prestasi Siswa')

@section('content')
<!-- Container Utama Prestasi Siswa -->
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
                            <span class="inline-flex items-center text-xs font-bold text-heading">Prestasi</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Prestasi Siswa</h1>
            <p class="text-xs text-body mt-0.5">Kelola data prestasi dan penghargaan yang diraih oleh siswa di kelas Anda.</p>
        </div>

        <!-- Class Badge Banner -->
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-neutral-primary-soft border border-default shadow-xs rounded-base">
            <div class="p-2.5 bg-brand text-white rounded-lg shadow-xs shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z"/>
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
        $hasPrestasiCount = $students->filter(fn($s) => $s->prestasi && ($s->prestasi->catatan_prestasi1 || $s->prestasi->catatan_prestasi2 || $s->prestasi->catatan_prestasi3 || $s->prestasi->prestasi1 || $s->prestasi->prestasi2 || $s->prestasi->prestasi3))->count();
        $noPrestasiCount = $totalStudents - $hasPrestasiCount;
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-body uppercase tracking-wider">Memiliki Prestasi</p>
                <p class="text-lg font-extrabold text-heading">{{ $hasPrestasiCount }} <span class="text-xs font-normal text-body">siswa</span></p>
            </div>
        </div>

        <div class="p-3.5 bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs flex items-center gap-3">
            <div class="p-2 bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-body uppercase tracking-wider">Belum Ada Prestasi</p>
                <p class="text-lg font-extrabold text-heading">{{ $noPrestasiCount }} <span class="text-xs font-normal text-body">siswa</span></p>
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
                <input type="text" id="prestasi-search" oninput="filterPrestasiTable()" class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 p-2.5 placeholder:text-body" placeholder="Cari NIS, nama siswa, atau deskripsi prestasi..." />
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table id="prestasi-table" class="w-full text-xs md:text-sm text-left text-body">
                <thead class="text-[11px] md:text-xs font-bold text-heading uppercase bg-neutral-tertiary border-b border-default">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3">NIS / NISN</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3">Prestasi 1</th>
                        <th scope="col" class="px-4 py-3">Prestasi 2</th>
                        <th scope="col" class="px-4 py-3">Prestasi 3</th>
                        <th scope="col" class="px-4 py-3 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @forelse($students as $index => $student)
                        @php
                            $prestasi = $student->prestasi;
                            $p1Tingkat = $prestasi ? $prestasi->prestasi1 : null;
                            $p1Catatan = $prestasi ? $prestasi->catatan_prestasi1 : null;
                            $p2Tingkat = $prestasi ? $prestasi->prestasi2 : null;
                            $p2Catatan = $prestasi ? $prestasi->catatan_prestasi2 : null;
                            $p3Tingkat = $prestasi ? $prestasi->prestasi3 : null;
                            $p3Catatan = $prestasi ? $prestasi->catatan_prestasi3 : null;
                        @endphp
                        <tr class="student-row hover:bg-neutral-secondary-medium/50 transition-colors"
                            data-search="{{ strtolower($student->name . ' ' . $student->nis . ' ' . $student->nisn . ' ' . ($p1Catatan ?? '') . ' ' . ($p2Catatan ?? '') . ' ' . ($p3Catatan ?? '')) }}">
                            <td class="px-4 py-3 text-center font-bold text-heading">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-heading">{{ $student->nis ?? '-' }}</div>
                                <div class="text-[11px] text-body/70">{{ $student->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-extrabold text-heading whitespace-nowrap">
                                {{ $student->name }}
                            </td>
                            <!-- Prestasi 1 -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($p1Catatan || $p1Tingkat !== null)
                                    <div class="inline-flex flex-col gap-0.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-base text-xs font-bold bg-brand-soft text-fg-brand border border-brand/20 max-w-xs truncate" title="{{ $p1Catatan }}">
                                            <svg class="w-3 h-3 me-1 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z"/>
                                            </svg>
                                            {{ $p1Catatan ?? 'Prestasi 1' }}
                                        </span>
                                        @if($p1Tingkat !== null)
                                            <span class="text-[10px] text-body/70 font-semibold ms-1">Tingkat / Peringkat: {{ $p1Tingkat }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-body/50 italic text-xs">-</span>
                                @endif
                            </td>
                            <!-- Prestasi 2 -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($p2Catatan || $p2Tingkat !== null)
                                    <div class="inline-flex flex-col gap-0.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-base text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300/40 max-w-xs truncate" title="{{ $p2Catatan }}">
                                            <svg class="w-3 h-3 me-1 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z"/>
                                            </svg>
                                            {{ $p2Catatan ?? 'Prestasi 2' }}
                                        </span>
                                        @if($p2Tingkat !== null)
                                            <span class="text-[10px] text-body/70 font-semibold ms-1">Tingkat / Peringkat: {{ $p2Tingkat }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-body/50 italic text-xs">-</span>
                                @endif
                            </td>
                            <!-- Prestasi 3 -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($p3Catatan || $p3Tingkat !== null)
                                    <div class="inline-flex flex-col gap-0.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-base text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-300/40 max-w-xs truncate" title="{{ $p3Catatan }}">
                                            <svg class="w-3 h-3 me-1 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z"/>
                                            </svg>
                                            {{ $p3Catatan ?? 'Prestasi 3' }}
                                        </span>
                                        @if($p3Tingkat !== null)
                                            <span class="text-[10px] text-body/70 font-semibold ms-1">Tingkat / Peringkat: {{ $p3Tingkat }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-body/50 italic text-xs">-</span>
                                @endif
                            </td>
                            <!-- Action Buttons -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        onclick="openEditPrestasiModal({{ $student->id }}, '{{ addslashes($student->name) }}', '{{ addslashes($p1Tingkat ?? '') }}', '{{ addslashes($p1Catatan ?? '') }}', '{{ addslashes($p2Tingkat ?? '') }}', '{{ addslashes($p2Catatan ?? '') }}', '{{ addslashes($p3Tingkat ?? '') }}', '{{ addslashes($p3Catatan ?? '') }}', {{ $prestasi->id ?? 'null' }})"
                                        class="px-2.5 py-1.5 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base shadow-xs transition-colors cursor-pointer inline-flex items-center gap-1"
                                        title="Kelola Prestasi">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span>Kelola</span>
                                    </button>

                                    @if($prestasi)
                                        <form action="{{ route('wali-kelas.prestasis.destroy', $prestasi->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data prestasi siswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:text-white hover:bg-red-600 border border-red-300 dark:border-red-800 rounded-base transition-colors cursor-pointer" title="Hapus Data Prestasi">
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
                            <td colspan="7" class="px-4 py-8 text-center text-body">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-10 h-10 text-body/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z"/>
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

<!-- Modal Form Input / Edit Prestasi -->
<div id="prestasi-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white dark:bg-neutral-primary-soft rounded-base shadow-lg border border-default overflow-hidden">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-default bg-neutral-tertiary">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-brand-soft text-fg-brand rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-heading">Kelola Data Prestasi</h3>
                        <p id="modal-student-name" class="text-xs font-semibold text-fg-brand">Nama Siswa</p>
                    </div>
                </div>
                <button type="button" onclick="closePrestasiModal()" class="text-body hover:text-heading rounded-base p-1.5 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal body -->
            <form id="prestasi-form" action="{{ route('wali-kelas.prestasis.store') }}" method="POST" class="p-4 space-y-4">
                @csrf
                <input type="hidden" id="form-student-id" name="student_id" value="">

                <!-- Section Prestasi 1 -->
                <div class="p-3 bg-neutral-secondary-medium/40 border border-default rounded-base space-y-2">
                    <div class="font-bold text-xs text-heading flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-brand"></span>
                        Prestasi 1
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label for="prestasi1" class="block mb-1 text-[11px] font-bold text-body">Tingkat / Peringkat</label>
                            <input type="number" id="prestasi1" name="prestasi1" placeholder="Contoh: 1" class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="catatan_prestasi1" class="block mb-1 text-[11px] font-bold text-body">Keterangan / Catatan Prestasi</label>
                            <input type="text" id="catatan_prestasi1" name="catatan_prestasi1" placeholder="Contoh: Juara 1 O2SN tingkat Kabupaten" class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Section Prestasi 2 -->
                <div class="p-3 bg-neutral-secondary-medium/40 border border-default rounded-base space-y-2">
                    <div class="font-bold text-xs text-heading flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Prestasi 2 (Opsional)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label for="prestasi2" class="block mb-1 text-[11px] font-bold text-body">Tingkat / Peringkat</label>
                            <input type="number" id="prestasi2" name="prestasi2" placeholder="Contoh: 2" class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="catatan_prestasi2" class="block mb-1 text-[11px] font-bold text-body">Keterangan / Catatan Prestasi</label>
                            <input type="text" id="catatan_prestasi2" name="catatan_prestasi2" placeholder="Contoh: Juara 2 OSN IPA tingkat Provinsi" class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Section Prestasi 3 -->
                <div class="p-3 bg-neutral-secondary-medium/40 border border-default rounded-base space-y-2">
                    <div class="font-bold text-xs text-heading flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Prestasi 3 (Opsional)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label for="prestasi3" class="block mb-1 text-[11px] font-bold text-body">Tingkat / Peringkat</label>
                            <input type="number" id="prestasi3" name="prestasi3" placeholder="Contoh: 3" class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="catatan_prestasi3" class="block mb-1 text-[11px] font-bold text-body">Keterangan / Catatan Prestasi</label>
                            <input type="text" id="catatan_prestasi3" name="catatan_prestasi3" placeholder="Contoh: Juara 3 Lomba Catur Kecamatan" class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-2 border-t border-default pt-3 mt-4">
                    <button type="button" onclick="closePrestasiModal()" class="px-4 py-2 text-xs font-bold text-body bg-neutral-secondary-medium hover:bg-neutral-tertiary rounded-base border border-default transition-colors cursor-pointer">
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

    function filterPrestasiTable() {
        const query = document.getElementById('prestasi-search').value.toLowerCase().trim();
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

    function openEditPrestasiModal(studentId, studentName, p1Tingkat, p1Catatan, p2Tingkat, p2Catatan, p3Tingkat, p3Catatan, prestasiId) {
        document.getElementById('form-student-id').value = studentId;
        document.getElementById('modal-student-name').textContent = studentName;
        document.getElementById('prestasi1').value = p1Tingkat !== null ? p1Tingkat : '';
        document.getElementById('catatan_prestasi1').value = p1Catatan || '';
        document.getElementById('prestasi2').value = p2Tingkat !== null ? p2Tingkat : '';
        document.getElementById('catatan_prestasi2').value = p2Catatan || '';
        document.getElementById('prestasi3').value = p3Tingkat !== null ? p3Tingkat : '';
        document.getElementById('catatan_prestasi3').value = p3Catatan || '';

        const modal = document.getElementById('prestasi-modal');
        modal.classList.remove('hidden');
    }

    function closePrestasiModal() {
        const modal = document.getElementById('prestasi-modal');
        modal.classList.add('hidden');
    }
</script>
@endsection
