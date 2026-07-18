@extends('layouts.main')

@section('title', 'Ulangan Harian')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <!-- Section 1: Meetings List Section -->
    <div id="meeting-list-section" class="transition-all duration-300">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-heading tracking-tight mb-2">Ulangan Harian</h1>
                <p class="text-body">Kelola pertemuan ulangan harian dan isi nilai siswa di setiap kelas.</p>
            </div>
            <div>
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
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm">
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
                                <option value="{{ $class->name }}">{{ $class->name }}</option>
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

            <div class="relative overflow-x-auto border border-default rounded-base bg-white dark:bg-neutral-primary-soft" id="meetings-table-container">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 w-12 text-center hidden sm:table-cell">No</th>
                            <th scope="col" class="px-6 py-3.5">Nama Pertemuan</th>
                            <th scope="col" class="px-6 py-3.5">Kelas</th>
                            <th scope="col" class="px-6 py-3.5 hidden md:table-cell">Tanggal Pertemuan</th>
                            <th scope="col" class="px-6 py-3.5 hidden lg:table-cell">Deskripsi</th>
                            <th scope="col" class="px-6 py-3.5 text-center whitespace-nowrap">Status Terisi</th>
                            <th scope="col" class="px-6 py-3.5 text-center whitespace-nowrap" style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default">
                        @forelse($meetings as $meeting)
                            <tr id="meeting-row-{{ $meeting->id }}" data-title="{{ strtolower($meeting->title) }}" data-class="{{ $meeting->class?->name ?? '' }}" data-date="{{ $meeting->test_date ? \Carbon\Carbon::parse($meeting->test_date)->format('Y-m-d') : '' }}" class="meeting-row hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 border-b border-default last:border-0 cursor-pointer" onclick="showMeetingScores({{ $meeting->id }})">
                                <td class="px-6 py-4 text-center font-semibold text-heading select-none hidden sm:table-cell">{{ sprintf('%02d', $loop->iteration) }}</td>
                                <td class="px-6 py-4 font-bold text-heading whitespace-nowrap">{{ $meeting->title }}</td>
                                <td class="px-6 py-4 text-body font-semibold">{{ $meeting->class?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-body whitespace-nowrap hidden md:table-cell">{{ $meeting->test_date ? \Carbon\Carbon::parse($meeting->test_date)->translatedFormat('d F Y') : '-' }}</td>
                                <td class="px-6 py-4 text-body max-w-xs truncate hidden lg:table-cell">{{ $meeting->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand/10 text-brand border border-brand/20">
                                        {{ $meeting->scores->count() }} / {{ $meeting->class?->students->count() ?? 0 }} Siswa
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="showMeetingScores({{ $meeting->id }})" class="text-xs bg-brand hover:bg-brand-strong text-white px-2.5 py-1.5 rounded-base font-bold transition-all duration-150 cursor-pointer">
                                            Isi Nilai
                                        </button>
                                        <button id="dropdownButton-{{ $meeting->id }}" data-dropdown-toggle="dropdown-{{ $meeting->id }}" class="inline-block text-body hover:bg-neutral-secondary-soft focus:ring-4 focus:outline-none focus:ring-neutral-tertiary rounded-lg text-sm p-1.5 transition-colors duration-150 cursor-pointer" type="button">
                                            <span class="sr-only">Open options</span>
                                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                            </svg>
                                        </button>
                                        <!-- Dropdown menu -->
                                        <div id="dropdown-{{ $meeting->id }}" class="z-20 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-32 dark:bg-neutral-primary-soft dark:divide-neutral-tertiary border border-default text-left">
                                            <ul class="py-2 text-sm text-heading" aria-labelledby="dropdownButton-{{ $meeting->id }}">
                                                <li>
                                                    <button type="button" onclick="prepareEditMeeting({{ json_encode($meeting) }})" data-modal-target="meeting-modal" data-modal-toggle="meeting-modal" class="block w-full text-left px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 cursor-pointer">
                                                        Ubah
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('daily-test-meetings.delete', $meeting->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertemuan ini? Semua data nilai di pertemuan ini juga akan terhapus.')" class="block w-full">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="block w-full text-left px-4 py-2 text-fg-danger-strong hover:bg-danger-soft/20 transition-colors duration-150 cursor-pointer">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-body">
                                    <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2" />
                                    </svg>
                                    <p class="font-medium text-heading">Belum ada data pertemuan ulangan.</p>
                                    <p class="text-xs mt-1">Silakan buat pertemuan baru menggunakan tombol di kanan atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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

    <!-- Section 2: Scores Fill Section (Hidden by Default) -->
    <div id="score-fill-section" class="hidden transition-all duration-300">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-start gap-3">
                <button type="button" onclick="backToMeetings()" class="text-body hover:text-brand p-2 rounded-base hover:bg-neutral-secondary-soft border border-default hover:border-brand transition-all duration-200 cursor-pointer flex items-center justify-center shrink-0 mt-0.5" title="Kembali ke Daftar Pertemuan">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <div>
                    <h1 id="score-title" class="text-2xl sm:text-3xl font-extrabold text-heading tracking-tight">Isi Nilai Kelas</h1>
                    <p class="text-body text-sm sm:text-base" id="score-subtitle">Pertemuan 1 - Kelas VII A</p>
                </div>
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

        <!-- Student Score Table Card -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm">
            <div class="relative overflow-x-auto border border-default rounded-base bg-white dark:bg-neutral-primary-soft" id="scores-table-container">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 w-12 text-center hidden sm:table-cell">No</th>
                            <th scope="col" class="px-6 py-3.5">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-3.5 text-center hidden sm:table-cell">L/P</th>
                            <th scope="col" class="px-6 py-3.5 text-center" style="width: 180px;">Nilai (0-100)</th>
                            <th scope="col" class="px-6 py-3.5 text-center whitespace-nowrap" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="scores-table-body" class="divide-y divide-default">
                        <!-- Javascript will populate rows here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty state for student list -->
            <div id="scores-empty" class="hidden text-center py-12 text-body">
                <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292" />
                </svg>
                <p class="font-medium text-heading">Tidak ada siswa ditemukan.</p>
                <p class="text-xs mt-1">Belum ada data siswa di kelas pertemuan ini.</p>
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
                    Tambah Pertemuan Ulangan
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
                           placeholder="contoh: Ulangan Harian Bab 1">
                </div>

                <!-- Test Date Input -->
                <div>
                    <label for="modal_test_date" class="block mb-2 text-sm font-semibold text-heading">Tanggal Ulangan</label>
                    <input type="date" name="test_date" id="modal_test_date" required
                           class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                </div>

                <!-- Description Textarea -->
                <div>
                    <label for="modal_description" class="block mb-2 text-sm font-semibold text-heading">Deskripsi / Keterangan</label>
                    <textarea name="description" id="modal_description" rows="3"
                              class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5"
                              placeholder="contoh: Materi Bab 1 tentang..."></textarea>
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
    <div class="ms-3 text-sm font-normal" id="toast-message">Nilai berhasil disimpan.</div>
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
    let scoreRecords = [];

    // Open Modal for Add Meeting
    function prepareAddMeeting() {
        document.getElementById('modal-title').innerText = 'Tambah Pertemuan Ulangan';
        document.getElementById('meeting-form').action = "{{ route('daily-test-meetings.store') }}";
        document.getElementById('modal-method-container').innerHTML = '';
        
        document.getElementById('modal_class_id').value = '';
        document.getElementById('modal_title_input').value = '';
        document.getElementById('modal_test_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('modal_description').value = '';
    }

    // Open Modal for Edit Meeting
    function prepareEditMeeting(meeting) {
        document.getElementById('modal-title').innerText = 'Ubah Pertemuan Ulangan';
        document.getElementById('meeting-form').action = `/daily-test-meetings/${meeting.id}`;
        document.getElementById('modal-method-container').innerHTML = '@method("PUT")';

        document.getElementById('modal_class_id').value = meeting.class_id;
        document.getElementById('modal_title_input').value = meeting.title;
        
        let rawDate = meeting.test_date;
        if (rawDate && rawDate.includes('T')) {
            rawDate = rawDate.split('T')[0];
        }
        document.getElementById('modal_test_date').value = rawDate;
        document.getElementById('modal_description').value = meeting.description || '';
    }

    // Show scores view
    function showMeetingScores(meetingId) {
        const meeting = meetingsData.find(m => m.id === meetingId);
        if (!meeting) return;

        currentMeetingId = meetingId;
        currentClassStudents = meeting.class ? (meeting.class.students || []) : [];
        scoreRecords = meeting.scores || [];

        // Set Headers
        document.getElementById('score-title').innerText = 'Nilai: ' + meeting.title;
        document.getElementById('score-subtitle').innerText = (meeting.class ? meeting.class.name : '-') + ' • ' + (meeting.test_date ? new Date(meeting.test_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');

        // Clear search
        document.getElementById('search-students').value = '';

        // Render Table
        renderScores(currentClassStudents);

        // Toggle sections
        document.getElementById('meeting-list-section').classList.add('hidden');
        document.getElementById('score-fill-section').classList.remove('hidden');
    }

    // Back to meetings grid
    function backToMeetings() {
        document.getElementById('score-fill-section').classList.add('hidden');
        document.getElementById('meeting-list-section').classList.remove('hidden');
    }

    // Render student table with current score values
    function renderScores(studentsList) {
        const tableBody = document.getElementById('scores-table-body');
        const tableContainer = document.getElementById('scores-table-container');
        const emptyState = document.getElementById('scores-empty');
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

        // Map existing score records by student ID
        const scoreMap = {};
        scoreRecords.forEach(rec => {
            scoreMap[rec.student_id] = rec;
        });

        sortedList.forEach((student, index) => {
            const indexStr = String(index + 1).padStart(2, '0');
            const studentScore = scoreMap[student.id];
            
            // Format score if exists, else empty
            const currentScore = studentScore ? parseFloat(studentScore.score) : '';
            const scoreId = studentScore ? studentScore.id : null;
            
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
                    <input type="number" step="0.01" min="0" max="100" id="score-${student.id}" oninput="markUnsaved(${student.id})" value="${currentScore}" placeholder="0.00" class="w-full bg-neutral-secondary-medium border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand p-1.5 font-bold text-center">
                </td>
                <td class="px-6 py-4 text-center">
                    <button type="button" onclick="saveScore(${student.id}, ${scoreId})" id="btn-save-${student.id}" class="${studentScore ? 'bg-emerald-600 hover:bg-emerald-700 cursor-not-allowed opacity-90' : 'bg-brand hover:bg-brand-strong cursor-pointer'} text-white px-3 py-1.5 rounded-base text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 w-full" ${studentScore ? 'disabled' : ''}>
                        <svg class="w-3.5 h-3.5 shrink-0 ${studentScore ? 'text-emerald-300' : ''}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span id="btn-text-${student.id}">${studentScore ? 'Disimpan' : 'Simpan'}</span>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Mark a student's score row as unsaved (revert button from "Disimpan" back to "Simpan")
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
        renderScores(filtered);
    }

    // Save/Update student score using AJAX
    function saveScore(studentId, existingScoreId) {
        const scoreInput = document.getElementById(`score-${studentId}`);
        const score = scoreInput.value;
        const saveBtn = document.getElementById(`btn-save-${studentId}`);
        
        if (score === '' || isNaN(score) || score < 0 || score > 100) {
            alert('Nilai harus berupa angka antara 0 dan 100.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        const url = existingScoreId 
            ? `/daily-test-scores/${existingScoreId}`
            : '/daily-test-scores';
            
        const method = existingScoreId ? 'PUT' : 'POST';
        
        const payload = {
            daily_test_meeting_id: currentMeetingId,
            student_id: studentId,
            score: parseFloat(score)
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
            const savedScore = result.data;
            
            // Replace or add in local array
            const recordIndex = scoreRecords.findIndex(r => r.student_id === studentId);
            if (recordIndex !== -1) {
                scoreRecords[recordIndex] = savedScore;
            } else {
                scoreRecords.push(savedScore);
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
            
            // Update onclick attribute to pass the new score id
            saveBtn.setAttribute('onclick', `saveScore(${studentId}, ${savedScore.id})`);

            // Update input value with formatted value
            scoreInput.value = parseFloat(savedScore.score);

            showToast(`Nilai ${savedScore.student ? savedScore.student.name : 'Siswa'} berhasil disimpan.`);
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

            alert('Gagal menyimpan nilai: ' + (error.message || 'Terjadi kesalahan sistem.'));
        });
    }

    // Save all unsaved scores and then redirect to index
    function saveAllAndFinish(btnElement) {
        const unsavedStudents = [];
        
        currentClassStudents.forEach(student => {
            const saveBtn = document.getElementById(`btn-save-${student.id}`);
            if (saveBtn && !saveBtn.disabled) {
                const record = scoreRecords.find(r => r.student_id === student.id);
                const existingId = record ? record.id : null;
                const scoreVal = document.getElementById(`score-${student.id}`).value;
                
                if (scoreVal !== '') {
                    unsavedStudents.push({
                        studentId: student.id,
                        existingId: existingId,
                        score: scoreVal
                    });
                }
            }
        });

        if (unsavedStudents.length === 0) {
            window.location.href = "{{ route('daily-test-meetings.index') }}";
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
                ? `/daily-test-scores/${item.existingId}`
                : '/daily-test-scores';
            const method = item.existingId ? 'PUT' : 'POST';
            const payload = {
                daily_test_meeting_id: currentMeetingId,
                student_id: item.studentId,
                score: parseFloat(item.score)
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
                window.location.href = "{{ route('daily-test-meetings.index') }}";
            })
            .catch(err => {
                btnElement.disabled = false;
                btnElement.innerHTML = originalHtml;
                alert('Gagal menyimpan beberapa nilai: ' + (err.message || 'Terjadi kesalahan sistem.'));
            });
    }

    // Filter main meetings table list by search, class, and date selection
    function filterMeetings() {
        const query = document.getElementById('search-meetings').value.toLowerCase();
        const selectedClass = document.getElementById('filter-class').value;
        const selectedDate = document.getElementById('filter-date').value;
        const rows = document.querySelectorAll('.meeting-row');
        let hasVisibleRows = false;

        rows.forEach(row => {
            const title = row.getAttribute('data-title');
            const className = row.getAttribute('data-class');
            const rawDate = row.getAttribute('data-date');
            
            const matchesSearch = title.includes(query);
            const matchesClass = selectedClass === '' || className === selectedClass;
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
        
        // Handle showing specific meeting scores if passed via query or redirect
        @if(isset($dailyTestMeeting))
            showMeetingScores({{ $dailyTestMeeting->id }});
        @endif
    });
</script>
@endsection
