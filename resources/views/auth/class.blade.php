@extends('layouts.main')

@section('title', 'Kelas')

@section('content')
    <div class="max-w-none mx-auto py-8 px-4">
        <!-- Section 1: Classes List Section -->
        <div id="class-list-section" class="transition-all duration-300">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="hidden sm:block text-3xl font-extrabold text-heading tracking-tight mb-2">Kelas</h1>
                    <p class="text-body">Kelola data kelas dan lihat daftar siswa di setiap kelas.</p>
                </div>
                <div>
                    <button type="button" onclick="prepareAddClass()" data-modal-target="class-modal"
                        data-modal-toggle="class-modal"
                        class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center gap-2">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Kelas
                    </button>
                </div>
            </div>

            <!-- Flowbite Success Alert -->
            @if(session('success'))
                <div id="alert-success"
                    class="flex items-center p-4 mb-6 text-emerald-800 border border-emerald-300 rounded-lg bg-emerald-50 dark:bg-neutral-primary-soft dark:text-emerald-400 dark:border-emerald-800"
                    role="alert">
                    <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div class="ms-3 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                    <button type="button"
                        class="ms-auto -mx-1.5 -my-1.5 bg-emerald-50 text-emerald-500 rounded-lg focus:ring-2 focus:ring-emerald-400 p-1.5 hover:bg-emerald-200 inline-flex items-center justify-center h-8 w-8 dark:bg-neutral-primary-soft dark:text-emerald-400 dark:hover:bg-neutral-tertiary"
                        data-dismiss-target="#alert-success" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Classes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($classes as $class)
                    <div
                        class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm hover:shadow-md transition-all duration-200 group flex flex-col justify-between relative overflow-hidden min-h-[170px]">

                        <!-- Glow effect on hover -->
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-brand/5 rounded-full blur-xl group-hover:bg-brand/10 transition-all duration-200 pointer-events-none">
                        </div>

                        <div>
                            <!-- Title & Options -->
                            <div class="flex items-start justify-between gap-4 mb-4 relative z-10">
                                <h3 class="text-xl font-bold text-heading transition-colors duration-200">
                                    {{ $class->name }}
                                </h3>

                                <!-- Actions -->
                                <div class="flex items-center gap-1">
                                    <!-- View Students Button -->
                                    <button type="button" onclick="showClassStudents({{ $class->id }})"
                                        class="inline-block text-brand hover:bg-brand/10 rounded-lg text-sm p-1.5 transition-colors duration-150 cursor-pointer"
                                        title="Lihat Siswa">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <!-- Edit Button directly on card -->
                                    <button type="button" onclick="prepareEditClass({{ json_encode($class) }})"
                                        data-modal-target="edit-class-modal" data-modal-toggle="edit-class-modal"
                                        class="inline-block text-body hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary rounded-lg text-sm p-1.5 transition-colors duration-150 cursor-pointer"
                                        title="Ubah Kelas">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button directly on card -->
                                    <button type="button"
                                        onclick="confirmDeleteClass({{ $class->id }}, {{ json_encode($class->name) }})"
                                        class="inline-block text-fg-danger-strong hover:bg-danger-soft/20 rounded-lg text-sm p-1.5 transition-colors duration-150 cursor-pointer flex items-center justify-center"
                                        title="Hapus Kelas">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- 3 Lines in the Sketch -> represented as 3 metadata items -->
                            <div class="space-y-2 text-xs">
                                <!-- Line 1: Tahun Ajaran -->
                                <div class="flex items-center gap-2 text-body">
                                    <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Tahun Ajaran: <span
                                            class="font-medium text-heading">{{ $class->academicYear?->year ?? '-' }}</span></span>
                                </div>

                                <!-- Line 2: Semester -->
                                <div class="flex items-center gap-2 text-body">
                                    <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Semester: <span
                                            class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-neutral-secondary-medium border border-default text-heading uppercase">{{ $class->academicYear?->semester ?? '-' }}</span></span>
                                </div>

                                <!-- Line 3: Student Count -->
                                <div class="flex items-center gap-2 text-body">
                                    <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Jumlah Siswa: <span id="student-count-{{ $class->id }}"
                                            class="font-bold text-brand">{{ $class->students->count() }} Siswa</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-8 text-center text-body">
                        <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="font-medium text-heading">Belum ada data kelas.</p>
                        <p class="text-xs mt-1">Silakan tambahkan kelas baru menggunakan tombol di kanan atas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div id="student-list-section" class="hidden transition-all duration-300">
            <!-- Back Button -->
            <div class="mb-5">
                <button type="button" onclick="backToClasses()"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-body hover:text-brand transition-colors duration-200 cursor-pointer group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Kelas
                </button>
            </div>

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 id="student-title" class="text-3xl font-extrabold text-heading tracking-tight mb-2">Siswa VII A</h1>
                    <p class="text-body">Daftar siswa yang terdaftar di kelas ini.</p>
                </div>

                <!-- Search & Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" id="search-students" oninput="filterStudents(this.value)"
                            placeholder="Cari nama siswa..."
                            class="w-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base pl-9 pr-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
                    </div>

                    <button type="button" data-modal-target="import-modal" data-modal-toggle="import-modal"
                        onclick="prepareImportStudents()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-base text-sm font-bold shadow-md shadow-emerald-600/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        Import Excel
                    </button>

                    <button type="button" onclick="prepareAddStudent()" data-modal-target="student-modal"
                        data-modal-toggle="student-modal"
                        class="bg-brand hover:bg-brand-strong text-white px-4 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Siswa
                    </button>
                </div>
            </div>

            <!-- Student Content Table Card (Flowbite Table Component) -->
            <div
                class="bg-transparent sm:bg-white dark:sm:bg-neutral-primary-soft border-0 sm:border border-default rounded-none sm:rounded-base p-0 sm:p-6 shadow-none sm:shadow-sm">
                <div class="relative overflow-x-auto border border-default rounded-base bg-transparent sm:bg-white dark:sm:bg-neutral-primary-soft"
                    id="students-table-container">
                    <table class="w-full text-sm text-left text-body">
                        <thead
                            class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 min-w-[50px] w-12 text-center">No</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[200px]">Nama Lengkap</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[120px]">NIS / NISN</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[80px] text-center">L/P</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[180px]">Tempat, Tanggal Lahir</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[160px]">Wali / No. HP</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[100px] text-center">Status</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[120px] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="students-table-body" class="divide-y divide-default">
                            <!-- Javascript will populate rows here -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty state for student list -->
                <div id="students-empty" class="hidden text-center py-12 text-body">
                    <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="font-medium text-heading">Tidak ada siswa ditemukan.</p>
                    <p class="text-xs mt-1">Belum ada data siswa di kelas ini atau tidak ada hasil pencarian yang cocok.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dialog 1: Add / Edit Class using Flowbite component structure -->
    <div id="class-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-default">
                    <h3 id="modal-title" class="text-lg font-bold text-heading">
                        Tambah Kelas
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer"
                        data-modal-hide="class-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form id="class-form" method="POST" class="p-4 md:p-5 space-y-4">
                    @csrf
                    <div id="modal-method-container"></div>

                    <!-- Academic Year Select -->
                    <div>
                        <label for="modal_academic_year_id" class="block mb-2 text-sm font-semibold text-heading">Tahun
                            Ajaran</label>
                        <select name="academic_year_id" id="modal_academic_year_id" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                            <option value="" disabled selected>-- Pilih Tahun Ajaran --</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }} - {{ $year->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Class Name Input -->
                    <div>
                        <label for="modal_name" class="block mb-2 text-sm font-semibold text-heading">Nama Kelas</label>
                        <input type="text" name="name" id="modal_name" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5"
                            placeholder="contoh: VII A">
                    </div>

                    <!-- Modal Action Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                        <button type="button" data-modal-hide="class-modal"
                            class="px-5 py-2.5 text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body rounded-base transition-all duration-200 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Dialog 3: Edit Class using Flowbite component structure -->
    <div id="edit-class-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-default">
                    <h3 class="text-lg font-bold text-heading">
                        Ubah Kelas
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer"
                        data-modal-hide="edit-class-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form id="edit-class-form" method="POST" class="p-4 md:p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Academic Year Select -->
                    <div>
                        <label for="edit_modal_academic_year_id" class="block mb-2 text-sm font-semibold text-heading">Tahun
                            Ajaran</label>
                        <select name="academic_year_id" id="edit_modal_academic_year_id" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                            <option value="" disabled selected>-- Pilih Tahun Ajaran --</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }} - {{ $year->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Class Name Input -->
                    <div>
                        <label for="edit_modal_name" class="block mb-2 text-sm font-semibold text-heading">Nama
                            Kelas</label>
                        <input type="text" name="name" id="edit_modal_name" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5"
                            placeholder="contoh: VII A">
                    </div>

                    <!-- Modal Action Buttons -->
                    <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                        <button type="button" data-modal-hide="edit-class-modal"
                            class="px-5 py-2.5 text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body rounded-base transition-all duration-200 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Class Deletion -->
    <form id="delete-class-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden trigger button for programmatically opening the student modal via Flowbite JS -->
    <button id="trigger-student-modal" type="button" data-modal-target="student-modal" data-modal-toggle="student-modal"
        class="hidden"></button>

    <!-- Modal Dialog 2: Add Student using Flowbite component structure -->
    <div id="student-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-xs">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 id="student-modal-title" class="text-lg font-medium text-heading">
                        Tambah Siswa Baru
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center cursor-pointer"
                        data-modal-hide="student-modal">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form id="student-form" onsubmit="submitStudentForm(event)">
                    <input type="hidden" name="id" id="student_id">
                    <input type="hidden" name="class_id" id="student_class_id">

                    <div class="grid gap-4 grid-cols-1 py-4 md:py-6 max-h-[50vh] overflow-y-auto pr-1">
                        <!-- Validation Errors Container -->
                        <div id="student-form-errors"
                            class="mb-4 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/20 p-3.5 rounded-base border border-red-200 dark:border-red-900/30 hidden">
                            <ul class="list-disc pl-5 space-y-1" id="errors-list"></ul>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="student_name" class="block mb-2.5 text-sm font-medium text-heading">Nama
                                Lengkap</label>
                            <input type="text" name="name" id="student_name"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Nama lengkap siswa" required>
                        </div>
                        <!-- Status -->
                        <div>
                            <label for="student_status" class="block mb-2.5 text-sm font-medium text-heading">Status</label>
                            <select name="status" id="student_status"
                                class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                                required>
                                <option value="ACTIVE" selected>ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div>

                        <!-- NIS -->
                        <div>
                            <label for="student_nis" class="block mb-2.5 text-sm font-medium text-heading">NIS</label>
                            <input type="text" name="nis" id="student_nis"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Nomor Induk Siswa" required>
                        </div>
                        <!-- NISN -->
                        <div>
                            <label for="student_nisn" class="block mb-2.5 text-sm font-medium text-heading">NISN</label>
                            <input type="text" name="nisn" id="student_nisn"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Nomor Induk Siswa Nasional" required>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="student_gender" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                                Kelamin</label>
                            <select name="gender" id="student_gender"
                                class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                                required>
                                <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <!-- Birth Place -->
                        <div>
                            <label for="student_birth_place" class="block mb-2.5 text-sm font-medium text-heading">Tempat
                                Lahir</label>
                            <input type="text" name="birth_place" id="student_birth_place"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Kota lahir" required>
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label for="student_birth_date" class="block mb-2.5 text-sm font-medium text-heading">Tanggal
                                Lahir</label>
                            <input type="date" name="birth_date" id="student_birth_date"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required>
                        </div>
                        <!-- Parent Name -->
                        <div>
                            <label for="student_parent_name" class="block mb-2.5 text-sm font-medium text-heading">Nama Wali
                                / Orang Tua</label>
                            <input type="text" name="parent_name" id="student_parent_name"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Nama ayah/ibu/wali" required>
                        </div>

                        <!-- Parent Phone -->
                        <div>
                            <label for="student_parent_phone" class="block mb-2.5 text-sm font-medium text-heading">No. HP
                                Orang Tua</label>
                            <input type="text" name="parent_phone" id="student_parent_phone"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="contoh: 08123456789" required>
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="student_address"
                                class="block mb-2.5 text-sm font-medium text-heading">Alamat</label>
                            <textarea name="address" id="student_address" rows="3"
                                class="block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body"
                                placeholder="Alamat lengkap tempat tinggal siswa" required></textarea>
                        </div>
                    </div>

                    <!-- Modal Action Buttons -->
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit" id="btn-submit-student"
                            class="inline-flex items-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer">
                            <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14m-7 7V5" />
                            </svg>
                            Simpan Siswa
                        </button>
                        <button data-modal-hide="student-modal" type="button"
                            class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Dialog 4: Import Students using Flowbite component structure -->
    <div id="import-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black/50 backdrop-blur-xs">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div
                class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default p-4 md:p-6 flex flex-col max-h-[90vh]">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5 shrink-0">
                    <h3 id="import-modal-title" class="text-lg font-bold text-heading">
                        Import Siswa via Excel / CSV
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer"
                        data-modal-hide="import-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="py-4 md:py-6 overflow-y-auto flex-1 space-y-6">
                    <!-- Instructions and Template download -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start bg-neutral-secondary-medium/50 dark:bg-neutral-primary-soft border border-default p-4 rounded-base">
                        <div>
                            <h4 class="font-bold text-heading text-sm mb-2">Petunjuk Penggunaan:</h4>
                            <ul class="list-disc pl-5 text-xs text-body space-y-1">
                                <li>Format file harus berupa Excel (<strong>.xlsx</strong> / <strong>.xls</strong>) atau
                                    <strong>.csv</strong>.
                                </li>
                                <li>Pastikan baris pertama berisi nama kolom (header) seperti: <strong>nis</strong>,
                                    <strong>nisn</strong>, <strong>nama</strong>, <strong>jenis_kelamin</strong> (L/P),
                                    <strong>tempat_lahir</strong>, <strong>tanggal_lahir</strong> (Format: YYYY-MM-DD),
                                    <strong>alamat</strong>, <strong>nama_wali</strong>, <strong>no_hp_wali</strong>,
                                    <strong>status</strong> (ACTIVE/INACTIVE).
                                </li>
                                <li>Format tanggal lahir harus berupa text/date dengan pola <strong>YYYY-MM-DD</strong>
                                    (contoh: 2010-08-15).</li>
                            </ul>
                        </div>
                        <div
                            class="flex flex-col justify-center items-center h-full border-t md:border-t-0 md:border-l border-default pt-4 md:pt-0 md:pl-6 text-center">
                            <p class="text-xs text-body mb-3 font-semibold">Gunakan template di bawah untuk memastikan
                                struktur kolom benar:</p>
                            <button type="button" onclick="downloadTemplate()"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-default bg-neutral-secondary-medium dark:bg-neutral-primary-soft text-heading hover:bg-neutral-tertiary-medium dark:hover:bg-neutral-tertiary text-xs font-bold rounded-base transition-colors duration-150 shadow-xs cursor-pointer">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh Template CSV
                            </button>
                        </div>
                    </div>

                    <!-- Dropzone / File input -->
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-heading">Pilih File Spreadsheet</label>
                        <div class="flex items-center justify-center w-full">
                            <label id="import-dropzone" for="import-file-input"
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-default rounded-base cursor-pointer bg-neutral-secondary-medium/20 hover:bg-neutral-secondary-medium/40 transition-colors duration-150">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-2.5 text-neutral-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-1 text-xs text-body"><span class="font-bold">Klik untuk unggah</span> atau
                                        drag and drop</p>
                                    <p class="text-[10px] text-body opacity-80">XLSX, XLS, atau CSV (Maks. 5MB)</p>
                                </div>
                                <input id="import-file-input" type="file" accept=".xlsx, .xls, .csv" class="hidden"
                                    onchange="handleFileSelect(event)" />
                            </label>
                        </div>
                    </div>

                    <!-- Backend/Frontend Validation Warnings Container -->
                    <div id="import-errors-container"
                        class="hidden text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/20 p-4 rounded-base border border-red-200 dark:border-red-900/30">
                        <div class="flex items-center gap-1.5 mb-2 font-bold">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Ditemukan beberapa kesalahan:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-1 text-xs" id="import-errors-list"></ul>
                    </div>

                    <!-- Preview Table Container -->
                    <div id="import-preview-section" class="hidden space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-heading text-sm">Preview Data Yang Akan Diimport:</h4>
                            <span id="preview-count"
                                class="px-2 py-0.5 rounded text-xs font-bold bg-brand/10 text-brand border border-brand/20">0
                                Siswa</span>
                        </div>
                        <div class="relative overflow-x-auto border border-default rounded-base max-h-[30vh]">
                            <table class="w-full text-xs text-left text-body">
                                <thead
                                    class="text-[10px] font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-4 py-2.5 text-center w-10">Baris</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[150px]">Nama Lengkap</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[80px]">NIS</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[80px]">NISN</th>
                                        <th scope="col" class="px-4 py-2.5 text-center min-w-[50px]">L/P</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[150px]">Tempat, Tgl Lahir</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[120px]">Wali / HP</th>
                                        <th scope="col" class="px-4 py-2.5 min-w-[150px]">Alamat</th>
                                        <th scope="col" class="px-4 py-2.5 text-center min-w-[80px]">Status</th>
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
                    <button data-modal-hide="import-modal" type="button"
                        class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-semibold leading-5 rounded-base text-sm px-5 py-2.5 focus:outline-none cursor-pointer">Batal</button>
                    <button type="button" onclick="submitImport()" id="btn-confirm-import"
                        class="inline-flex items-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-bold leading-5 rounded-base text-sm px-5 py-2.5 focus:outline-none cursor-pointer disabled:bg-neutral-tertiary disabled:text-fg-disabled disabled:cursor-not-allowed"
                        disabled>
                        <svg class="w-4 h-4 me-1.5 -ms-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mulai Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flowbite Toast Component -->
    <div id="toast-success"
        class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-md dark:text-gray-400 dark:bg-neutral-primary-soft fixed bottom-5 right-5 z-50 border border-default hidden"
        role="alert">
        <div
            class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-200">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-sm font-normal" id="toast-message">Siswa berhasil ditambahkan.</div>
        <button type="button"
            class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:bg-neutral-primary-soft dark:text-gray-500 dark:hover:text-white border-none cursor-pointer"
            onclick="document.getElementById('toast-success').classList.add('hidden')" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>

    <script>
        // Embedded classes data from backend
        const classesData = @json($classes);
        let currentClassStudents = [];
        let currentClassId = null;

        // Open Modal for Add Class
        function prepareAddClass() {
            document.getElementById('modal-title').innerText = 'Tambah Kelas';
            document.getElementById('class-form').action = "{{ route('classes.store') }}";
            document.getElementById('modal-method-container').innerHTML = '';

            document.getElementById('modal_academic_year_id').value = '';
            document.getElementById('modal_name').value = '';
        }

        // Open Modal for Edit Class
        function prepareEditClass(classData) {
            document.getElementById('edit-class-form').action = `/classes/${classData.id}`;
            document.getElementById('edit_modal_academic_year_id').value = classData.academic_year_id;
            document.getElementById('edit_modal_name').value = classData.name;
        }

        // Open Modal for Add Student
        function prepareAddStudent() {
            document.getElementById('student-modal-title').innerText = 'Tambah Siswa Baru';
            document.getElementById('btn-submit-student').innerText = 'Simpan Siswa';

            document.getElementById('student_id').value = '';
            document.getElementById('student_class_id').value = currentClassId;
            document.getElementById('student-form').reset();
            document.getElementById('student-form-errors').classList.add('hidden');
            document.getElementById('errors-list').innerHTML = '';
        }

        // Open Modal for Edit Student
        function prepareEditStudent(studentId) {
            const student = currentClassStudents.find(s => s.id === studentId);
            if (!student) return;

            document.getElementById('student-modal-title').innerText = 'Ubah Siswa';
            document.getElementById('btn-submit-student').innerText = 'Simpan Perubahan';

            document.getElementById('student_id').value = student.id;
            document.getElementById('student_class_id').value = currentClassId;

            document.getElementById('student_name').value = student.name;
            document.getElementById('student_status').value = student.status;
            document.getElementById('student_nis').value = student.nis;
            document.getElementById('student_nisn').value = student.nisn;
            document.getElementById('student_gender').value = student.gender;
            document.getElementById('student_birth_place').value = student.birth_place;
            document.getElementById('student_birth_date').value = student.birth_date ? student.birth_date.split('T')[0] : '';
            document.getElementById('student_parent_name').value = student.parent_name;
            document.getElementById('student_parent_phone').value = student.parent_phone;
            document.getElementById('student_address').value = student.address;

            document.getElementById('student-form-errors').classList.add('hidden');
            document.getElementById('errors-list').innerHTML = '';

            // Trigger Flowbite modal show programmatically
            document.getElementById('trigger-student-modal').click();
        }

        // Submit Student Form via AJAX
        function submitStudentForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const submitBtn = document.getElementById('btn-submit-student');
            submitBtn.disabled = true;
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Menyimpan...';

            const studentId = document.getElementById('student_id').value;
            const url = studentId ? `/students/${studentId}` : "{{ route('students.store') }}";
            const method = studentId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
                .then(async response => {
                    const result = await response.json();
                    if (!response.ok) {
                        throw result;
                    }
                    return result;
                })
                .then(result => {
                    console.log("Server response:", result);
                    if (!result || !result.data) {
                        throw new Error("Respon server tidak valid. Data siswa tidak ditemukan.");
                    }

                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;

                    // Add or update student in local array
                    const targetClass = classesData.find(c => c.id === currentClassId);
                    if (targetClass) {
                        if (!targetClass.students) {
                            targetClass.students = [];
                        }

                        if (studentId) {
                            const idx = targetClass.students.findIndex(s => s.id === parseInt(studentId));
                            if (idx !== -1) {
                                targetClass.students[idx] = result.data;
                            }
                        } else {
                            targetClass.students.push(result.data);
                        }

                        // Update class card count text
                        const countEl = document.getElementById(`student-count-${currentClassId}`);
                        if (countEl) {
                            countEl.innerText = `${targetClass.students.length} Siswa`;
                        }

                        // Refresh the students table view
                        showClassStudents(currentClassId);
                    }

                    // Close student modal using Flowbite dismiss click trigger
                    const closeBtn = document.querySelector('[data-modal-hide="student-modal"]');
                    if (closeBtn) {
                        closeBtn.click();
                    }

                    // Show Flowbite Toast success notification
                    showToast(studentId ? 'Perubahan data siswa berhasil disimpan.' : 'Siswa baru berhasil ditambahkan.');
                })
                .catch(errors => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;

                    const errorsContainer = document.getElementById('student-form-errors');
                    const errorsList = document.getElementById('errors-list');
                    errorsList.innerHTML = '';

                    if (errors.errors) {
                        Object.keys(errors.errors).forEach(key => {
                            errors.errors[key].forEach(err => {
                                const li = document.createElement('li');
                                li.innerText = err;
                                errorsList.appendChild(li);
                            });
                        });
                        errorsContainer.classList.remove('hidden');
                    } else {
                        const li = document.createElement('li');
                        li.innerText = errors.message || 'Terjadi kesalahan sistem.';
                        errorsList.appendChild(li);
                        errorsContainer.classList.remove('hidden');
                    }
                });
        }

        // Toast show helper
        function showToast(message) {
            const toast = document.getElementById('toast-success');
            document.getElementById('toast-message').innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 4000);
        }

        // Show students view
        function showClassStudents(classId) {
            const selectedClass = classesData.find(c => c.id === classId);
            if (!selectedClass) return;

            // Set state
            currentClassId = classId;
            currentClassStudents = selectedClass.students || [];

            // Update view title
            document.getElementById('student-title').innerText = 'Siswa ' + selectedClass.name;

            // Clear search input
            document.getElementById('search-students').value = '';

            // Render students
            renderStudents(currentClassStudents);

            // Toggle sections
            document.getElementById('class-list-section').classList.add('hidden');
            document.getElementById('student-list-section').classList.remove('hidden');
        }

        // Back to class list
        function backToClasses() {
            document.getElementById('student-list-section').classList.add('hidden');
            document.getElementById('class-list-section').classList.remove('hidden');
        }

        // Render students helper
        function renderStudents(studentsList) {
            const tableBody = document.getElementById('students-table-body');
            const tableContainer = document.getElementById('students-table-container');
            const emptyState = document.getElementById('students-empty');
            tableBody.innerHTML = '';

            // Filter out any undefined or null values to be safe
            const cleanList = (studentsList || []).filter(student => student !== undefined && student !== null);

            if (cleanList.length === 0) {
                tableContainer.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            tableContainer.classList.remove('hidden');
            emptyState.classList.add('hidden');

            // Sort alphabetically by name
            const sortedList = [...cleanList].sort((a, b) => a.name.localeCompare(b.name));

            sortedList.forEach((student, index) => {
                const indexStr = String(index + 1).padStart(2, '0');
                const genderBadgeColor = student.gender === 'L' ? 'text-sky-600 bg-sky-50 dark:bg-sky-950/20 border-sky-100 dark:border-sky-900/30' : 'text-rose-600 bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30';
                const genderLabel = student.gender === 'L' ? 'Laki-laki' : 'Perempuan';

                const statusBadgeColor = student.status === 'ACTIVE'
                    ? 'bg-emerald-100 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/30 font-bold'
                    : 'bg-neutral-secondary-medium border-default text-fg-disabled font-medium';

                const birthDateStr = student.birth_date ? new Date(student.birth_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
                const birthDetails = `${student.birth_place || '-'}, ${birthDateStr}`;

                const row = document.createElement('tr');
                row.className = 'hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 border-b border-default last:border-0';
                row.innerHTML = `
                        <td class="px-6 py-4 text-center font-semibold text-heading select-none">${indexStr}</td>
                        <td class="px-6 py-4 font-bold text-heading whitespace-nowrap">${student.name}</td>
                        <td class="px-6 py-4 text-body font-mono text-xs whitespace-nowrap">
                            <span class="block">NIS: ${student.nis || '-'}</span>
                            <span class="block text-[11px] text-body opacity-80">NISN: ${student.nisn || '-'}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-0.5 rounded border text-[10px] font-semibold ${genderBadgeColor}" title="${genderLabel}">
                                ${student.gender}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-body text-xs whitespace-nowrap">${birthDetails}</td>
                        <td class="px-6 py-4 text-body text-xs whitespace-nowrap">
                            <span class="font-semibold text-heading">${student.parent_name || '-'}</span>
                            <span class="block text-[10px] text-body opacity-80">${student.parent_phone || '-'}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] border ${statusBadgeColor}">
                                ${student.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <button type="button" onclick="prepareEditStudent(${student.id})" class="text-brand hover:bg-brand/10 p-2 rounded-lg transition-colors duration-150 cursor-pointer inline-flex items-center justify-center mr-1" title="Ubah Siswa">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button type="button" onclick="deleteStudent(${student.id}, '${student.name.replace(/'/g, "\\'")}')" class="text-fg-danger-strong hover:bg-danger-soft/20 p-2 rounded-lg transition-colors duration-150 cursor-pointer inline-flex items-center justify-center" title="Hapus Siswa">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    `;
                tableBody.appendChild(row);
            });
        }

        // Filter students input
        function filterStudents(query) {
            const filtered = currentClassStudents.filter(student => {
                const searchStr = (student.name + ' ' + student.nis + ' ' + student.nisn).toLowerCase();
                return searchStr.includes(query.toLowerCase());
            });
            renderStudents(filtered);
        }

        // Delete student via AJAX
        function deleteStudent(studentId, studentName) {
            if (!confirm(`Apakah Anda yakin ingin menghapus siswa ${studentName}?`)) {
                return;
            }

            fetch(`/students/${studentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(async response => {
                    const result = await response.json();
                    if (!response.ok) {
                        throw result;
                    }
                    return result;
                })
                .then(result => {
                    // Remove student from the local array
                    const targetClass = classesData.find(c => c.id === currentClassId);
                    if (targetClass && targetClass.students) {
                        targetClass.students = targetClass.students.filter(s => s.id !== studentId);
                        currentClassStudents = targetClass.students;

                        // Update class card count text
                        const countEl = document.getElementById(`student-count-${currentClassId}`);
                        if (countEl) {
                            countEl.innerText = `${targetClass.students.length} Siswa`;
                        }

                        // Refresh the students table view
                        renderStudents(currentClassStudents);
                    }

                    // Show success toast
                    showToast('Siswa berhasil dihapus.');
                })
                .catch(errors => {
                    alert(errors.message || 'Gagal menghapus siswa. Silakan coba lagi.');
                });
        }

        // Confirm and Delete Class programmatically
        function confirmDeleteClass(classId, className) {
            if (confirm(`Apakah Anda yakin ingin menghapus kelas ${className}? Semua siswa di kelas ini juga akan terhapus.`)) {
                const form = document.getElementById('delete-class-form');
                form.action = `/classes/${classId}`;
                form.submit();
            }
        }

        // Setup and state for Bulk Import
        let studentsToImport = [];

        function prepareImportStudents() {
            studentsToImport = [];
            document.getElementById('import-file-input').value = '';
            document.getElementById('import-errors-container').classList.add('hidden');
            document.getElementById('import-errors-list').innerHTML = '';
            document.getElementById('import-preview-section').classList.add('hidden');
            document.getElementById('import-preview-body').innerHTML = '';

            const confirmBtn = document.getElementById('btn-confirm-import');
            confirmBtn.disabled = true;
            confirmBtn.innerText = 'Mulai Import';
        }

        // Dynamic template downloader
        function downloadTemplate() {
            const csvContent = "\uFEFFnis,nisn,nama,jenis_kelamin,tempat_lahir,tanggal_lahir,alamat,nama_wali,no_hp_wali,status\n" +
                "123456,01234567,Ahmad Fauzi,L,Jakarta,2010-08-15,Jl. Mawar No. 5,Budi,08123456789,ACTIVE\n" +
                "123457,01234568,Siti Aminah,P,Surabaya,2011-04-12,Jl. Melati No. 12,Aisyah,08987654321,ACTIVE";
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", "template_import_siswa.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Load SheetJS dynamically on demand
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

        // Handle file selection
        function handleFileSelect(event) {
            const file = event.target.files[0];
            processFile(file);
        }

        // Process file logic
        function processFile(file) {
            if (!file) return;

            const fileExt = file.name.split('.').pop().toLowerCase();
            const maxSizeBytes = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSizeBytes) {
                alert('Ukuran file melebihi batas 5MB.');
                return;
            }

            if (fileExt === 'csv') {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const text = e.target.result;
                    parseCSVData(text);
                };
                reader.readAsText(file);
            } else if (fileExt === 'xlsx' || fileExt === 'xls') {
                loadSheetJS(() => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const data = new Uint8Array(e.target.result);
                        try {
                            const workbook = XLSX.read(data, { type: 'array', cellDates: true });
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
                alert('Format file tidak didukung. Harap pilih file XLSX, XLS, atau CSV.');
            }
        }

        // Parser for Native CSV
        function parseCSVData(text) {
            const rows = [];
            let row = [''];
            let inQuotes = false;

            for (let i = 0; i < text.length; i++) {
                const c = text[i];
                const next = text[i + 1];

                if (c === '"') {
                    if (inQuotes && next === '"') {
                        row[row.length - 1] += '"';
                        i++;
                    } else {
                        inQuotes = !inQuotes;
                    }
                } else if (c === ',' && !inQuotes) {
                    row.push('');
                } else if ((c === '\r' || c === '\n') && !inQuotes) {
                    if (c === '\r' && next === '\n') {
                        i++;
                    }
                    rows.push(row);
                    row = [''];
                } else {
                    row[row.length - 1] += c;
                }
            }
            if (row.length > 1 || row[0] !== '') {
                rows.push(row);
            }

            parseExcelData(rows);
        }

        // Process parsed spreadsheet matrix
        function parseExcelData(rawData) {
            if (!rawData || rawData.length < 2) {
                alert('File kosong atau tidak memiliki data siswa.');
                return;
            }

            // Clean headers
            const headers = rawData[0].map(h => String(h || '').trim().toLowerCase());
            const rows = rawData.slice(1);

            // Define header mapping variations
            const headerMap = {
                'nis': ['nis', 'no. induk', 'nomor induk'],
                'nisn': ['nisn', 'nomor induk nasional'],
                'name': ['name', 'nama', 'nama lengkap', 'nama siswa'],
                'gender': ['gender', 'jenis_kelamin', 'l/p', 'jk', 'sex'],
                'birth_place': ['birth_place', 'tempat lahir', 'tempat_lahir'],
                'birth_date': ['birth_date', 'tanggal lahir', 'tanggal_lahir', 'tgl lahir', 'tgl_lahir'],
                'address': ['address', 'alamat', 'alamat tinggal'],
                'parent_name': ['parent_name', 'nama wali', 'nama orang tua', 'orang tua', 'wali', 'nama_wali', 'ibu', 'ayah'],
                'parent_phone': ['parent_phone', 'no hp wali', 'no hp orang tua', 'telepon wali', 'no_hp_wali', 'telepon', 'no hp', 'no. hp', 'hp wali', 'hp orang tua'],
                'status': ['status']
            };

            const indexMap = {};
            Object.keys(headerMap).forEach(key => {
                indexMap[key] = headers.findIndex(h => headerMap[key].includes(h));
            });

            // Validation: Name is absolutely required to identify columns
            if (indexMap.name === -1) {
                alert('Kolom "Nama" / "Name" tidak ditemukan di file. Pastikan header kolom sudah benar sesuai template.');
                return;
            }

            const parsedStudents = [];
            let hasValidationErrors = false;

            rows.forEach((row, i) => {
                // Skip empty rows
                if (!row || row.length === 0 || row.every(val => val === null || val === undefined || String(val).trim() === '')) {
                    return;
                }

                const getVal = (key) => {
                    const idx = indexMap[key];
                    if (idx === -1 || idx === undefined || row[idx] === undefined || row[idx] === null) return '';
                    return String(row[idx]).trim();
                };

                // Custom Date formatting
                let rawDate = '';
                const dateIdx = indexMap['birth_date'];
                if (dateIdx !== -1 && row[dateIdx]) {
                    const dateVal = row[dateIdx];
                    if (dateVal instanceof Date) {
                        rawDate = dateVal.toISOString().split('T')[0];
                    } else {
                        const dateStr = String(dateVal).trim();
                        if (!isNaN(dateStr) && dateStr.length >= 5) {
                            const excelEpoch = new Date(Date.UTC(1899, 11, 30));
                            const days = parseInt(dateStr);
                            excelEpoch.setDate(excelEpoch.getDate() + days);
                            rawDate = excelEpoch.toISOString().split('T')[0];
                        } else {
                            rawDate = dateStr;
                        }
                    }
                }

                // Standardize Gender
                let gender = getVal('gender').toUpperCase();
                if (gender.startsWith('L') || gender === 'LAKI' || gender === 'LAKI-LAKI') {
                    gender = 'L';
                } else if (gender.startsWith('P') || gender === 'PEREMPUAN') {
                    gender = 'P';
                }

                // Standardize Status
                let status = getVal('status').toUpperCase();
                if (status !== 'ACTIVE' && status !== 'INACTIVE') {
                    if (status === 'AKTIF' || status === '1' || status === 'TRUE' || status === '') {
                        status = 'ACTIVE';
                    } else {
                        status = 'INACTIVE';
                    }
                }

                const student = {
                    rowNum: i + 2, // Row numbering in Excel/CSV
                    nis: getVal('nis'),
                    nisn: getVal('nisn'),
                    name: getVal('name'),
                    gender: gender,
                    birth_place: getVal('birth_place'),
                    birth_date: rawDate,
                    address: getVal('address'),
                    parent_name: getVal('parent_name'),
                    parent_phone: getVal('parent_phone'),
                    status: status,
                    errors: []
                };

                // Browser-side validation
                if (!student.name) student.errors.push('Nama wajib diisi');
                if (!student.nis) student.errors.push('NIS wajib diisi');
                if (!student.nisn) student.errors.push('NISN wajib diisi');
                if (!['L', 'P'].includes(student.gender)) student.errors.push('Jenis kelamin harus L/P');
                if (!student.birth_place) student.errors.push('Tempat lahir wajib diisi');
                if (!student.birth_date) {
                    student.errors.push('Tanggal lahir wajib diisi');
                } else {
                    const d = new Date(student.birth_date);
                    if (isNaN(d.getTime())) {
                        student.errors.push('Format tgl lahir tidak valid (gunakan YYYY-MM-DD)');
                    }
                }
                if (!student.address) student.errors.push('Alamat wajib diisi');
                if (!student.parent_name) student.errors.push('Nama wali wajib diisi');
                if (!student.parent_phone) student.errors.push('No HP wali wajib diisi');

                if (student.errors.length > 0) {
                    hasValidationErrors = true;
                }

                parsedStudents.push(student);
            });

            studentsToImport = parsedStudents;
            displayImportPreview(parsedStudents, hasValidationErrors);
        }

        // Display parsed spreadsheet data in the preview table
        function displayImportPreview(parsedStudents, hasValidationErrors) {
            const previewSection = document.getElementById('import-preview-section');
            const previewCount = document.getElementById('preview-count');
            const previewBody = document.getElementById('import-preview-body');
            const confirmBtn = document.getElementById('btn-confirm-import');
            const errorsContainer = document.getElementById('import-errors-container');
            const errorsList = document.getElementById('import-errors-list');

            previewBody.innerHTML = '';
            errorsList.innerHTML = '';
            errorsContainer.classList.add('hidden');

            previewCount.innerText = `${parsedStudents.length} Siswa`;
            previewSection.classList.remove('hidden');

            parsedStudents.forEach(student => {
                const row = document.createElement('tr');
                const hasErr = student.errors.length > 0;
                row.className = hasErr
                    ? 'bg-red-50/50 dark:bg-red-950/20 text-red-900 dark:text-red-300 border-b border-red-200 dark:border-red-900/30'
                    : 'hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary border-b border-default';

                const validationStatus = hasErr
                    ? `<span class="font-bold text-red-600 dark:text-red-400">${student.errors.join(', ')}</span>`
                    : '<span class="text-emerald-600 dark:text-emerald-400 font-semibold">Valid</span>';

                const genderBadgeColor = student.gender === 'L' ? 'text-sky-600 bg-sky-50 dark:bg-sky-950/20 border-sky-100 dark:border-sky-900/30' : 'text-rose-600 bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30';
                const genderLabel = student.gender === 'L' ? 'Laki-laki' : 'Perempuan';

                const birthDateFormatted = student.birth_date ? student.birth_date : '-';
                const birthDetails = `${student.birth_place || '-'}, ${birthDateFormatted}`;

                row.innerHTML = `
                        <td class="px-4 py-3 text-center font-semibold select-none">${student.rowNum}</td>
                        <td class="px-4 py-3 font-bold whitespace-nowrap">${student.name || '-'}</td>
                        <td class="px-4 py-3 font-mono">${student.nis || '-'}</td>
                        <td class="px-4 py-3 font-mono">${student.nisn || '-'}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded border text-[10px] font-semibold ${genderBadgeColor}" title="${genderLabel}">
                                ${student.gender || '-'}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">${birthDetails}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-semibold block">${student.parent_name || '-'}</span>
                            <span class="text-[10px] opacity-80 block">${student.parent_phone || '-'}</span>
                        </td>
                        <td class="px-4 py-3 max-w-xs truncate" title="${student.address}">${student.address || '-'}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded border text-[10px] font-bold ${student.status === 'ACTIVE' ? 'bg-emerald-100 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300' : 'bg-neutral-secondary-medium text-fg-disabled border-default'}">
                                ${student.status}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">${validationStatus}</td>
                    `;
                previewBody.appendChild(row);
            });

            if (hasValidationErrors) {
                confirmBtn.disabled = true;
                parsedStudents.forEach(student => {
                    if (student.errors.length > 0) {
                        const li = document.createElement('li');
                        li.innerText = `Baris ${student.rowNum} (${student.name || 'Tanpa Nama'}): ${student.errors.join(', ')}`;
                        errorsList.appendChild(li);
                    }
                });
                errorsContainer.classList.remove('hidden');
            } else {
                confirmBtn.disabled = false;
            }
        }

        // Submit imported rows to server via AJAX
        function submitImport() {
            if (studentsToImport.length === 0) return;

            const importBtn = document.getElementById('btn-confirm-import');
            importBtn.disabled = true;
            const originalText = importBtn.innerText;
            importBtn.innerText = 'Mengimport...';

            const payload = {
                class_id: currentClassId,
                students: studentsToImport.map(s => ({
                    nis: s.nis,
                    nisn: s.nisn,
                    name: s.name,
                    gender: s.gender,
                    birth_place: s.birth_place,
                    birth_date: s.birth_date,
                    address: s.address,
                    parent_name: s.parent_name,
                    parent_phone: s.parent_phone,
                    status: s.status
                }))
            };

            fetch("{{ route('students.import') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
                .then(async response => {
                    const result = await response.json();
                    if (!response.ok) {
                        throw result;
                    }
                    return result;
                })
                .then(result => {
                    importBtn.disabled = false;
                    importBtn.innerText = originalText;

                    // Update local classesData array
                    const targetClass = classesData.find(c => c.id === currentClassId);
                    if (targetClass) {
                        targetClass.students = result.data;
                        currentClassStudents = result.data;

                        // Update class card count text
                        const countEl = document.getElementById(`student-count-${currentClassId}`);
                        if (countEl) {
                            countEl.innerText = `${result.data.length} Siswa`;
                        }

                        // Refresh the student list table
                        showClassStudents(currentClassId);
                    }

                    // Close modal
                    const closeBtn = document.querySelector('[data-modal-hide="import-modal"]');
                    if (closeBtn) {
                        closeBtn.click();
                    }

                    showToast(result.message || 'Siswa berhasil diimport.');
                })
                .catch(errors => {
                    importBtn.disabled = false;
                    importBtn.innerText = originalText;

                    const errorContainer = document.getElementById('import-errors-container');
                    const errorList = document.getElementById('import-errors-list');
                    errorList.innerHTML = '';

                    if (errors.errors) {
                        Object.keys(errors.errors).forEach(key => {
                            const match = key.match(/students\.(\d+)\.(\w+)/);
                            let rowMsg = '';
                            if (match) {
                                const idx = parseInt(match[1]);
                                const studentName = studentsToImport[idx] ? studentsToImport[idx].name : '';
                                rowMsg = `Baris ${idx + 2} (${studentName}): `;
                            }

                            errors.errors[key].forEach(err => {
                                const li = document.createElement('li');
                                li.innerText = rowMsg + err;
                                errorList.appendChild(li);
                            });
                        });
                        errorContainer.classList.remove('hidden');
                        errorContainer.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        const li = document.createElement('li');
                        li.innerText = errors.message || 'Terjadi kesalahan sistem.';
                        errorList.appendChild(li);
                        errorContainer.classList.remove('hidden');
                        errorContainer.scrollIntoView({ behavior: 'smooth' });
                    }
                });
        }


        // Auto-hide success alert after 3 seconds
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

            // Drag and drop spreadsheet upload functionality
            const dropzone = document.getElementById('import-dropzone');
            const fileInput = document.getElementById('import-file-input');

            if (dropzone && fileInput) {
                // Prevent default drag behaviors for window & dropzone
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                // Highlight dropzone on drag enter & over
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, () => {
                        dropzone.classList.remove('border-default', 'bg-neutral-secondary-medium/20', 'hover:bg-neutral-secondary-medium/40');
                        dropzone.classList.add('border-brand', 'bg-brand/10');
                    }, false);
                });

                // Unhighlight dropzone on drag leave, drag end, and drop
                ['dragleave', 'dragend', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, () => {
                        dropzone.classList.remove('border-brand', 'bg-brand/10');
                        dropzone.classList.add('border-default', 'bg-neutral-secondary-medium/20', 'hover:bg-neutral-secondary-medium/40');
                    }, false);
                });

                // Handle file drop
                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files && files.length > 0) {
                        fileInput.files = files; // Synchronize file input value
                        processFile(files[0]);
                    }
                }, false);
            }

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    </script>
@endsection