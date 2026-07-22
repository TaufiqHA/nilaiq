@extends('layouts.main')

@section('title', 'Absensi')

@section('content')
<div class="max-w-none mx-auto py-8 px-4">
    <!-- Section 1: Meetings List Section -->
    <div id="meeting-list-section" class="transition-all duration-300">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="hidden sm:block text-3xl font-extrabold text-heading tracking-tight mb-2">Absensi</h1>
                <p class="text-body">Kelola pertemuan dan isi absensi siswa di setiap kelas.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" data-modal-target="rekap-class-modal" data-modal-toggle="rekap-class-modal" class="bg-neutral-secondary-medium hover:bg-neutral-tertiary border border-default text-heading px-5 py-2.5 rounded-base text-sm font-bold shadow-sm transition-all duration-200 cursor-pointer flex items-center gap-2">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Rekap Absensi
                </button>
                <button type="button" onclick="prepareAddMeeting()" data-modal-target="meeting-modal" data-modal-toggle="meeting-modal" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center gap-2">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pertemuan
                </button>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
        <div id="alert-success" class="flex items-center p-4 mb-6 text-emerald-800 border border-emerald-300 rounded-lg bg-emerald-50 dark:bg-neutral-primary-soft dark:text-emerald-400 dark:border-emerald-800" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-emerald-50 text-emerald-500 rounded-lg focus:ring-2 focus:ring-emerald-400 p-1.5 hover:bg-emerald-200 inline-flex items-center justify-center h-8 w-8 dark:bg-neutral-primary-soft dark:text-emerald-400 dark:hover:bg-neutral-tertiary" data-dismiss-target="#alert-success" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        @endif

        <!-- Meetings Table -->
        <div class="bg-transparent sm:bg-white dark:sm:bg-neutral-primary-soft border-0 sm:border border-default rounded-none sm:rounded-base p-0 sm:p-6 shadow-none sm:shadow-sm">
            <!-- Search & Filter Controls -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <!-- Search Input -->
                <div class="relative w-full md:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="search-meetings" oninput="filterMeetings()" placeholder="Cari nama pertemuan..." class="w-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base pl-9 pr-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
                </div>

                <!-- Filters Group -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    <!-- Class Filter Dropdown -->
                    <div class="w-full sm:w-44">
                        <select id="filter-class" onchange="filterMeetings()" class="w-full bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block p-2 font-semibold">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" data-name="{{ $class->name }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Filter Input -->
                    <div class="w-full sm:w-48 flex items-center gap-1.5">
                        <input type="date" id="filter-date" onchange="filterMeetings()" class="w-full bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand p-2 font-semibold">
                        <button type="button" onclick="clearDateFilter()" class="text-body hover:text-fg-brand p-2 rounded-base hover:bg-neutral-tertiary border border-default transition-all duration-150 cursor-pointer text-center" title="Reset Tanggal">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative overflow-x-auto border-0 sm:border border-default rounded-none sm:rounded-base bg-transparent sm:bg-white dark:sm:bg-neutral-primary-soft" id="meetings-table-container">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 min-w-[50px] w-12 text-center hidden sm:table-cell">No</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[180px]">Nama Pertemuan</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[80px]">Kelas</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[100px] text-center">Tipe</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[160px] hidden md:table-cell">Tanggal Pertemuan</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[160px] hidden lg:table-cell">Deskripsi</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[120px] text-center whitespace-nowrap">Status Terisi</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[140px] text-center whitespace-nowrap" style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default">
                        @forelse($meetings as $meeting)
                            <tr id="meeting-row-{{ $meeting->id }}" data-title="{{ strtolower($meeting->title) }}" data-class="{{ $meeting->class?->name ?? '' }}" data-date="{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->format('Y-m-d') : '' }}" class="meeting-row hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 border-b border-default last:border-0 cursor-pointer" onclick="showMeetingAttendances({{ $meeting->id }})">
                                <td class="px-6 py-4 text-center font-semibold text-heading select-none hidden sm:table-cell">{{ sprintf('%02d', $loop->iteration) }}</td>
                                <td class="px-6 py-4 font-bold text-heading whitespace-nowrap">{{ $meeting->title }}</td>
                                <td class="px-6 py-4 text-body font-semibold">{{ $meeting->class?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if(($meeting->tipe ?? 'harian') === 'harian')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/20 dark:text-sky-400 dark:border-sky-900/30">
                                            Harian
                                        </span>
                                    @elseif($meeting->tipe === 'ulang harian')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30">
                                            Ulangan Harian
                                        </span>
                                    @elseif($meeting->tipe === 'tugas')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30">
                                            Tugas
                                        </span>
                                    @elseif($meeting->tipe === 'pts')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/20 dark:text-purple-400 dark:border-purple-900/30">
                                            PTS
                                        </span>
                                    @elseif($meeting->tipe === 'pas')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/30">
                                            PAS
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-body whitespace-nowrap hidden md:table-cell">{{ $meeting->meeting_date ? \Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('d F Y') : '-' }}</td>
                                <td class="px-6 py-4 text-body max-w-xs truncate hidden lg:table-cell">{{ $meeting->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand/10 text-brand border border-brand/20">
                                        {{ $meeting->attendances->count() }} / {{ $meeting->class?->students->count() ?? 0 }} Siswa
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="showMeetingAttendances({{ $meeting->id }})" class="text-xs bg-brand hover:bg-brand-strong text-white px-2.5 py-1.5 rounded-base font-bold transition-all duration-150 cursor-pointer">
                                            Isi Absensi
                                        </button>
                                        <button id="dropdownButton-{{ $meeting->id }}" data-dropdown-toggle="dropdown-{{ $meeting->id }}" class="inline-block text-body hover:bg-neutral-secondary-soft focus:ring-4 focus:outline-none focus:ring-neutral-tertiary rounded-lg text-sm p-1.5 transition-colors duration-150 cursor-pointer" type="button">
                                            <span class="sr-only">Open options</span>
                                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-body">
                                    <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2" />
                                    </svg>
                                    <p class="font-medium text-heading">Belum ada data pertemuan.</p>
                                    <p class="text-xs mt-1">Silakan buat pertemuan baru menggunakan tombol di kanan atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Dropdown menus rendered outside the overflow container -->
            @foreach($meetings as $meeting)
                <div id="dropdown-{{ $meeting->id }}" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-32 dark:bg-neutral-primary-soft dark:divide-neutral-tertiary border border-default text-left">
                    <ul class="py-2 text-sm text-heading" aria-labelledby="dropdownButton-{{ $meeting->id }}">
                        <li>
                            <button type="button" onclick="prepareEditMeeting({{ json_encode($meeting) }})" data-modal-target="meeting-modal" data-modal-toggle="meeting-modal" class="block w-full text-left px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 cursor-pointer">
                                Ubah
                            </button>
                        </li>
                        <li>
                            <form action="{{ route('attendance-meetings.delete', $meeting->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertemuan ini? Semua data absensi di pertemuan ini juga akan terhapus.')" class="block w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-fg-danger-strong hover:bg-danger-soft/20 transition-colors duration-150 cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endforeach

            <!-- Empty state for filtered meetings search -->
            <div id="meetings-table-empty" class="hidden text-center py-12 text-body">
                <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <p class="font-medium text-heading">Tidak ada pertemuan ditemukan.</p>
                <p class="text-xs mt-1">Tidak ada hasil pencarian yang cocok dengan filter atau kata kunci Anda.</p>
            </div>
        </div>
    </div>

    <!-- Section 2: Attendances Fill Section (Hidden by Default) -->
    <div id="attendance-fill-section" class="hidden transition-all duration-300">
        <!-- Back Button -->
        <div class="mb-5">
            <button type="button" onclick="backToMeetings()" class="inline-flex items-center gap-2 text-sm font-semibold text-body hover:text-brand transition-colors duration-200 cursor-pointer group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Pertemuan
            </button>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 id="attendance-title" class="text-2xl sm:text-3xl font-extrabold text-heading tracking-tight mb-2">Isi Absensi Kelas</h1>
                <p class="text-body text-sm sm:text-base" id="attendance-subtitle">Pertemuan 1 - Kelas VII A</p>
            </div>
            
            <!-- Search field and Selesai button -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="search-students" oninput="filterStudents(this.value)" placeholder="Cari nama siswa..." class="w-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base pl-9 pr-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
                </div>
                
                <button type="button" onclick="saveAllAndFinish(this)" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Selesai
                </button>
            </div>
        </div>

        <!-- Student Attendance Table Card -->
        <div class="bg-transparent sm:bg-white dark:sm:bg-neutral-primary-soft border-0 sm:border border-default rounded-none sm:rounded-base p-0 sm:p-6 shadow-none sm:shadow-sm">
            <div class="relative overflow-x-auto border-0 sm:border border-default rounded-none sm:rounded-base bg-transparent sm:bg-white dark:sm:bg-neutral-primary-soft" id="attendances-table-container">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 min-w-[50px] w-12 text-center hidden sm:table-cell">No</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[200px]">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[60px] text-center hidden sm:table-cell">L/P</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[170px] whitespace-nowrap" style="width: 170px;">Status Absensi</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[150px]">Catatan</th>
                            <th scope="col" class="px-6 py-3.5 min-w-[100px] text-center whitespace-nowrap" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="attendances-table-body" class="divide-y divide-default">
                        <!-- Javascript will populate rows here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty state for student list -->
            <div id="attendances-empty" class="hidden text-center py-12 text-body">
                <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292" />
                </svg>
                <p class="font-medium text-heading">Tidak ada siswa ditemukan.</p>
                <p class="text-xs mt-1">Belum ada data siswa di kelas pertemuan ini.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog: Select Class for Rekap Absensi -->
<div id="rekap-class-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-default">
                <h3 class="text-lg font-bold text-heading">
                    Pilih Kelas
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer" data-modal-hide="rekap-class-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
                <!-- Class Select -->
                <div>
                    <label for="rekap_modal_class_id" class="block mb-2 text-sm font-semibold text-heading">Pilih Kelas untuk Rekap</label>
                    <select id="rekap_modal_class_id" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 font-semibold">
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                    <button type="button" data-modal-hide="rekap-class-modal" class="px-5 py-2.5 text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body rounded-base transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="goToRekapAbsensiFromModal()" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                        Pilih
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog: Add / Edit Meeting -->
<div id="meeting-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-default">
                <h3 id="modal-title" class="text-lg font-bold text-heading">
                    Tambah Pertemuan
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer" data-modal-hide="meeting-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form id="meeting-form" method="POST" class="p-4 md:p-5 space-y-4">
                @csrf
                <div id="modal-method-container"></div>

                <!-- Class Select -->
                <div>
                    <label for="modal_class_id" class="block mb-2 text-sm font-semibold text-heading">Kelas</label>
                    <select name="class_id" id="modal_class_id" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        <option value="" disabled selected>-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->students->count() }} Siswa)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Title Input -->
                <div>
                    <label for="modal_title_input" class="block mb-2 text-sm font-semibold text-heading">Nama Pertemuan</label>
                    <input type="text" name="title" id="modal_title_input" required
                           class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5"
                           placeholder="contoh: Pertemuan Pertama">
                </div>

                <!-- Tipe Select -->
                <div>
                    <label for="modal_tipe" class="block mb-2 text-sm font-semibold text-heading">Tipe Pertemuan</label>
                    <select name="tipe" id="modal_tipe" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 font-semibold">
                        <option value="harian">Harian</option>
                        <option value="ulang harian">Ulangan Harian</option>
                        <option value="tugas">Tugas</option>
                        <option value="pts">PTS</option>
                        <option value="pas">PAS</option>
                    </select>
                </div>

                <!-- Meeting Date Input -->
                <div>
                    <label for="modal_meeting_date" class="block mb-2 text-sm font-semibold text-heading">Tanggal Pertemuan</label>
                    <input type="date" name="meeting_date" id="modal_meeting_date" required
                           class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                </div>

                <!-- Description Textarea -->
                <div>
                    <label for="modal_description" class="block mb-2 text-sm font-semibold text-heading">Deskripsi / Keterangan</label>
                    <textarea name="description" id="modal_description" rows="3"
                              class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5"
                              placeholder="contoh: Membahas bab 1..."></textarea>
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                    <button type="button" data-modal-hide="meeting-modal" class="px-5 py-2.5 text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body rounded-base transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Success Component -->
<div id="toast-success" class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-md dark:text-gray-400 dark:bg-neutral-primary-soft fixed bottom-5 right-5 z-50 border border-default hidden" role="alert">
    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-200">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <span class="sr-only">Check icon</span>
    </div>
    <div class="ms-3 text-sm font-normal" id="toast-message">Absensi berhasil disimpan.</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:bg-neutral-primary-soft dark:text-gray-500 dark:hover:text-white border-none cursor-pointer" onclick="document.getElementById('toast-success').classList.add('hidden')" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
    </button>
</div>

<script>
    // Embed data from backend
    const meetingsData = @json($meetings);
    let currentMeetingId = null;
    let currentClassStudents = [];
    let attendanceRecords = [];

    // Open Modal for Add Meeting
    function prepareAddMeeting() {
        document.getElementById('modal-title').innerText = 'Tambah Pertemuan';
        document.getElementById('meeting-form').action = "{{ route('attendance-meetings.store') }}";
        document.getElementById('modal-method-container').innerHTML = '';
        
        document.getElementById('modal_class_id').value = '';
        document.getElementById('modal_title_input').value = '';
        document.getElementById('modal_tipe').value = 'harian';
        document.getElementById('modal_meeting_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('modal_description').value = '';
    }

    // Open Modal for Edit Meeting
    function prepareEditMeeting(meeting) {
        document.getElementById('modal-title').innerText = 'Ubah Pertemuan';
        document.getElementById('meeting-form').action = `/attendance-meetings/${meeting.id}`;
        document.getElementById('modal-method-container').innerHTML = '@method("PUT")';

        document.getElementById('modal_class_id').value = meeting.class_id;
        document.getElementById('modal_title_input').value = meeting.title;
        document.getElementById('modal_tipe').value = meeting.tipe || 'harian';
        
        let rawDate = meeting.meeting_date;
        if (rawDate && rawDate.includes('T')) {
            rawDate = rawDate.split('T')[0];
        }
        document.getElementById('modal_meeting_date').value = rawDate;
        document.getElementById('modal_description').value = meeting.description || '';
    }

    // Show attendance record view
    function showMeetingAttendances(meetingId) {
        const meeting = meetingsData.find(m => m.id === meetingId);
        if (!meeting) return;

        currentMeetingId = meetingId;
        currentClassStudents = meeting.class ? (meeting.class.students || []) : [];
        attendanceRecords = meeting.attendances || [];

        // Set Headers
        document.getElementById('attendance-title').innerText = 'Absensi: ' + meeting.title;
        document.getElementById('attendance-subtitle').innerText = (meeting.class ? meeting.class.name : '-') + ' • ' + (meeting.meeting_date ? new Date(meeting.meeting_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');

        // Clear search
        document.getElementById('search-students').value = '';

        // Render Table
        renderAttendances(currentClassStudents);

        // Toggle sections
        document.getElementById('meeting-list-section').classList.add('hidden');
        document.getElementById('attendance-fill-section').classList.remove('hidden');
    }

    // Back to meetings grid
    function backToMeetings() {
        document.getElementById('attendance-fill-section').classList.add('hidden');
        document.getElementById('meeting-list-section').classList.remove('hidden');
    }

    // Render student table with current attendance status
    function renderAttendances(studentsList) {
        const tableBody = document.getElementById('attendances-table-body');
        const tableContainer = document.getElementById('attendances-table-container');
        const emptyState = document.getElementById('attendances-empty');
        tableBody.innerHTML = '';

        if (studentsList.length === 0) {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        tableContainer.classList.remove('hidden');
        emptyState.classList.add('hidden');

        // Sort alphabetically
        const sortedList = [...studentsList].sort((a, b) => a.name.localeCompare(b.name));

        // Map existing attendance records by student ID
        const attendanceMap = {};
        attendanceRecords.forEach(att => {
            attendanceMap[att.student_id] = att;
        });

        sortedList.forEach((student, index) => {
            const indexStr = String(index + 1).padStart(2, '0');
            const studentAttendance = attendanceMap[student.id];
            
            const currentStatus = studentAttendance ? studentAttendance.status : 'HADIR';
            const currentNote = studentAttendance ? (studentAttendance.note || '') : '';
            const attendanceId = studentAttendance ? studentAttendance.id : null;
            
            const genderBadgeColor = student.gender === 'L' ? 'text-sky-600 bg-sky-50 dark:bg-sky-950/20 border-sky-100 dark:border-sky-900/30' : 'text-rose-600 bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30';
            const genderLabel = student.gender === 'L' ? 'Laki-laki' : 'Perempuan';

            const row = document.createElement('tr');
            row.className = 'hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 border-b border-default last:border-0';
            row.innerHTML = `
                <td class="px-6 py-4 text-center font-semibold text-heading select-none hidden sm:table-cell">${indexStr}</td>
                <td class="px-6 py-4 font-bold text-heading whitespace-nowrap">${student.name}</td>
                <td class="px-6 py-4 text-center hidden sm:table-cell">
                    <span class="px-2 py-0.5 rounded border text-[10px] font-semibold ${genderBadgeColor}" title="${genderLabel}">
                        ${student.gender}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <select id="status-${student.id}" onchange="markUnsaved(${student.id})" class="bg-neutral-secondary-medium border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-28 p-1.5 font-bold" ${studentAttendance ? 'disabled' : ''}>
                        <option value="HADIR" ${currentStatus === 'HADIR' ? 'selected' : ''}>HADIR</option>
                        <option value="IZIN" ${currentStatus === 'IZIN' ? 'selected' : ''}>IZIN</option>
                        <option value="SAKIT" ${currentStatus === 'SAKIT' ? 'selected' : ''}>SAKIT</option>
                        <option value="ALFA" ${currentStatus === 'ALFA' ? 'selected' : ''}>ALFA</option>
                    </select>
                </td>
                <td class="px-6 py-4">
                    <input type="text" id="note-${student.id}" oninput="markUnsaved(${student.id})" value="${currentNote}" placeholder="Catatan..." class="w-full bg-neutral-secondary-medium border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand p-1.5" ${studentAttendance ? 'disabled' : ''}>
                </td>
                <td class="px-6 py-4 text-center">
                    <button type="button" onclick="saveAttendance(${student.id}, ${attendanceId})" id="btn-save-${student.id}" class="${studentAttendance ? 'bg-emerald-600 hover:bg-emerald-700 cursor-not-allowed opacity-90' : 'bg-brand hover:bg-brand-strong cursor-pointer'} text-white px-3 py-1.5 rounded-base text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 w-full" ${studentAttendance ? 'disabled' : ''}>
                        <svg class="w-3.5 h-3.5 shrink-0 ${studentAttendance ? 'text-emerald-300' : ''}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span id="btn-text-${student.id}">${studentAttendance ? 'Disimpan' : 'Simpan'}</span>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Mark a student's attendance row as unsaved (revert button from "Disimpan" back to "Simpan")
    function markUnsaved(studentId) {
        const saveBtn = document.getElementById(`btn-save-${studentId}`);
        const btnText = document.getElementById(`btn-text-${studentId}`);
        if (saveBtn && btnText && btnText.innerText === 'Disimpan') {
            saveBtn.disabled = false;
            saveBtn.className = "bg-brand hover:bg-brand-strong text-white px-3 py-1.5 rounded-base text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 cursor-pointer w-full";
            saveBtn.innerHTML = `
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <span id="btn-text-${studentId}">Simpan</span>
            `;
        }
    }

    // Filter students
    function filterStudents(query) {
        const filtered = currentClassStudents.filter(student => {
            return student.name.toLowerCase().includes(query.toLowerCase());
        });
        renderAttendances(filtered);
    }

    // Save/Update student attendance using AJAX
    function saveAttendance(studentId, existingAttendanceId) {
        const status = document.getElementById(`status-${studentId}`).value;
        const note = document.getElementById(`note-${studentId}`).value;
        const saveBtn = document.getElementById(`btn-save-${studentId}`);
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        const url = existingAttendanceId 
            ? `/attendances/${existingAttendanceId}`
            : '/attendances';
            
        const method = existingAttendanceId ? 'PUT' : 'POST';
        
        const payload = {
            attendance_meeting_id: currentMeetingId,
            student_id: studentId,
            status: status,
            note: note
        };

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw data;
            }
            return data;
        })
        .then(result => {
            // Update local records
            const savedAttendance = result.data;
            
            // Replace or add in local array
            const recordIndex = attendanceRecords.findIndex(r => r.student_id === studentId);
            if (recordIndex !== -1) {
                attendanceRecords[recordIndex] = savedAttendance;
            } else {
                attendanceRecords.push(savedAttendance);
            }

            // Restore button with updated onclick action for PUT mapping and disable it
            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg class="w-3.5 h-3.5 shrink-0 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <span id="btn-text-${studentId}">Disimpan</span>
            `;
            saveBtn.className = "bg-emerald-600 text-white px-3 py-1.5 rounded-base text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 cursor-not-allowed w-full opacity-90";
            
            // Disable select and input fields
            document.getElementById(`status-${studentId}`).disabled = true;
            document.getElementById(`note-${studentId}`).disabled = true;
            
            // Update onclick attribute to pass the new attendance id
            saveBtn.setAttribute('onclick', `saveAttendance(${studentId}, ${savedAttendance.id})`);

            showToast(`Absensi ${savedAttendance.student ? savedAttendance.student.name : 'Siswa'} berhasil disimpan.`);
        })
        .catch(error => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Gagal
            `;
            saveBtn.className = "bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-base text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 cursor-pointer w-full";
            
            setTimeout(() => {
                saveBtn.innerHTML = `
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span id="btn-text-${studentId}">Simpan</span>
                `;
                saveBtn.className = "bg-brand hover:bg-brand-strong text-white px-3 py-1.5 rounded-base text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 cursor-pointer w-full";
            }, 3000);

            alert('Gagal menyimpan absensi: ' + (error.message || 'Terjadi kesalahan sistem.'));
        });
    }

    // Save all unsaved attendances and then redirect to index
    function saveAllAndFinish(btnElement) {
        const unsavedStudents = [];
        
        currentClassStudents.forEach(student => {
            const saveBtn = document.getElementById(`btn-save-${student.id}`);
            if (saveBtn && !saveBtn.disabled) {
                const record = attendanceRecords.find(r => r.student_id === student.id);
                const existingId = record ? record.id : null;
                
                unsavedStudents.push({
                    studentId: student.id,
                    existingId: existingId,
                    status: document.getElementById(`status-${student.id}`).value,
                    note: document.getElementById(`note-${student.id}`).value
                });
            }
        });

        if (unsavedStudents.length === 0) {
            window.location.href = "{{ route('attendance-meetings.index') }}";
            return;
        }

        btnElement.disabled = true;
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;

        const promises = unsavedStudents.map(item => {
            const url = item.existingId 
                ? `/attendances/${item.existingId}`
                : '/attendances';
            const method = item.existingId ? 'PUT' : 'POST';
            const payload = {
                attendance_meeting_id: currentMeetingId,
                student_id: item.studentId,
                status: item.status,
                note: item.note
            };

            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            }).then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw data;
                }
                return data;
            });
        });

        Promise.all(promises)
            .then(() => {
                window.location.href = "{{ route('attendance-meetings.index') }}";
            })
            .catch(err => {
                btnElement.disabled = false;
                btnElement.innerHTML = originalHtml;
                alert('Gagal menyimpan beberapa absensi: ' + (err.message || 'Terjadi kesalahan sistem.'));
            });
    }

    // Filter main meetings table list by search, class, and date selection
    function filterMeetings() {
        const query = document.getElementById('search-meetings').value.toLowerCase();
        const selectedClassEl = document.getElementById('filter-class');
        const selectedClass = selectedClassEl.value;
        const selectedClassName = selectedClassEl.options[selectedClassEl.selectedIndex].getAttribute('data-name') || '';
        const selectedDate = document.getElementById('filter-date').value;
        const rows = document.querySelectorAll('.meeting-row');
        let hasVisibleRows = false;

        rows.forEach(row => {
            const title = row.getAttribute('data-title');
            const className = row.getAttribute('data-class');
            const rawDate = row.getAttribute('data-date');
            
            const matchesSearch = title.includes(query);
            const matchesClass = selectedClass === '' || className === selectedClassName;
            const matchesDate = selectedDate === '' || rawDate === selectedDate;

            if (matchesSearch && matchesClass && matchesDate) {
                row.classList.remove('hidden');
                hasVisibleRows = true;
            } else {
                row.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('meetings-table-empty');
        const tableContainer = document.getElementById('meetings-table-container');
        
        if (hasVisibleRows || rows.length === 0) {
            tableContainer.classList.remove('hidden');
            emptyState.classList.add('hidden');
        } else {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }
    }

    // Reset date filter
    function clearDateFilter() {
        document.getElementById('filter-date').value = '';
        filterMeetings();
    }

    // Go to Rekap Absensi page from class selection modal
    function goToRekapAbsensiFromModal() {
        const selectedClassVal = document.getElementById('rekap_modal_class_id').value;
        if (!selectedClassVal) {
            alert('Silakan pilih kelas terlebih dahulu.');
            return;
        }
        window.location.href = `/rekap-absensi?class_id=${selectedClassVal}`;
    }

    // Helper toast show
    function showToast(message) {
        const toast = document.getElementById('toast-success');
        const msgEl = document.getElementById('toast-message');
        if (toast && msgEl) {
            msgEl.innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }
    }

    // Auto-hide alert
    document.addEventListener('DOMContentLoaded', () => {
        const successAlert = document.getElementById('alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    successAlert.classList.add('hidden');
                }, 500);
            }, 3000);
        }
    });
</script>
@endsection
