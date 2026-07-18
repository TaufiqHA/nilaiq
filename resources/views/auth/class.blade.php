@extends('layouts.main')

@section('title', 'Kelas')

@section('content')
<div class="max-w-none mx-auto py-8 px-4">
    <!-- Section 1: Classes List Section -->
    <div id="class-list-section" class="transition-all duration-300">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-heading tracking-tight mb-2">Kelas</h1>
                <p class="text-body">Kelola data kelas dan lihat daftar siswa di setiap kelas.</p>
            </div>
            <div>
                <button type="button" onclick="prepareAddClass()" data-modal-target="class-modal" data-modal-toggle="class-modal" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center gap-2">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Kelas
                </button>
            </div>
        </div>

        <!-- Flowbite Success Alert -->
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

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($classes as $class)
                <div onclick="showClassStudents({{ $class->id }})" class="bg-white dark:bg-neutral-primary-soft border border-default hover:border-brand rounded-base p-6 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer group flex flex-col justify-between relative overflow-hidden min-h-[170px]">
                    
                    <!-- Glow effect on hover -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand/5 rounded-full blur-xl group-hover:bg-brand/10 transition-all duration-200"></div>

                    <div>
                        <!-- Title & Options -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <h3 class="text-xl font-bold text-heading group-hover:text-brand transition-colors duration-200">
                                {{ $class->name }}
                            </h3>
                            
                            <!-- Actions (Using Flowbite Dropdown component, stopping propagation to avoid card click event) -->
                            <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                                <button id="dropdownButton-{{ $class->id }}" data-dropdown-toggle="dropdown-{{ $class->id }}" class="inline-block text-body hover:bg-neutral-secondary-soft focus:ring-4 focus:outline-none focus:ring-neutral-tertiary rounded-lg text-sm p-1.5 transition-colors duration-150 cursor-pointer" type="button">
                                    <span class="sr-only">Open options</span>
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                        <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                                    </svg>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div id="dropdown-{{ $class->id }}" class="z-20 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-32 dark:bg-neutral-primary-soft dark:divide-neutral-tertiary border border-default" onclick="event.stopPropagation()">
                                    <ul class="py-2 text-sm text-heading" aria-labelledby="dropdownButton-{{ $class->id }}">
                                        <li>
                                            <button type="button" onclick="prepareEditClass({{ json_encode($class) }})" data-modal-target="class-modal" data-modal-toggle="class-modal" class="block w-full text-left px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 cursor-pointer">
                                                Ubah
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('classes.delete', $class->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Semua siswa di kelas ini juga akan terhapus.')" class="block w-full">
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
                        </div>

                        <!-- 3 Lines in the Sketch -> represented as 3 metadata items -->
                        <div class="space-y-2 text-xs">
                            <!-- Line 1: Tahun Ajaran -->
                            <div class="flex items-center gap-2 text-body">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Tahun Ajaran: <span class="font-medium text-heading">{{ $class->academicYear?->year ?? '-' }}</span></span>
                            </div>
                            
                            <!-- Line 2: Semester -->
                            <div class="flex items-center gap-2 text-body">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Semester: <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-neutral-secondary-medium border border-default text-heading uppercase">{{ $class->academicYear?->semester ?? '-' }}</span></span>
                            </div>

                            <!-- Line 3: Student Count -->
                            <div class="flex items-center gap-2 text-body">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Jumlah Siswa: <span id="student-count-{{ $class->id }}" class="font-bold text-brand">{{ $class->students->count() }} Siswa</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-8 text-center text-body">
                    <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="font-medium text-heading">Belum ada data kelas.</p>
                    <p class="text-xs mt-1">Silakan tambahkan kelas baru menggunakan tombol di kanan atas.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Students List Section (Hidden by Default) -->
    <div id="student-list-section" class="hidden transition-all duration-300">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <button type="button" onclick="backToClasses()" class="text-body hover:text-brand p-2 rounded-base hover:bg-neutral-secondary-soft border border-default hover:border-brand transition-all duration-200 cursor-pointer flex items-center justify-center shrink-0" title="Kembali ke Daftar Kelas">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <div>
                    <h1 id="student-title" class="text-3xl font-extrabold text-heading tracking-tight">Siswa VII A</h1>
                    <p class="text-body">Daftar siswa yang terdaftar di kelas ini.</p>
                </div>
            </div>
            
            <!-- Search & Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="search-students" oninput="filterStudents(this.value)" placeholder="Cari nama siswa..." class="w-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base pl-9 pr-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
                </div>
                
                <button type="button" onclick="prepareAddStudent()" data-modal-target="student-modal" data-modal-toggle="student-modal" class="bg-brand hover:bg-brand-strong text-white px-4 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Siswa
                </button>
            </div>
        </div>

        <!-- Student Content Table Card (Flowbite Table Component) -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm">
            <div class="relative overflow-x-auto border border-default rounded-base bg-white dark:bg-neutral-primary-soft" id="students-table-container">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 w-12 text-center">No</th>
                            <th scope="col" class="px-6 py-3.5">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-3.5">NIS / NISN</th>
                            <th scope="col" class="px-6 py-3.5 text-center">L/P</th>
                            <th scope="col" class="px-6 py-3.5">Tempat, Tanggal Lahir</th>
                            <th scope="col" class="px-6 py-3.5">Wali / No. HP</th>
                            <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body" class="divide-y divide-default">
                        <!-- Javascript will populate rows here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty state for student list -->
            <div id="students-empty" class="hidden text-center py-12 text-body">
                <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="font-medium text-heading">Tidak ada siswa ditemukan.</p>
                <p class="text-xs mt-1">Belum ada data siswa di kelas ini atau tidak ada hasil pencarian yang cocok.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog 1: Add / Edit Class using Flowbite component structure -->
<div id="class-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-default">
                <h3 id="modal-title" class="text-lg font-bold text-heading">
                    Tambah Kelas
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer" data-modal-hide="class-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
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
                    <label for="modal_academic_year_id" class="block mb-2 text-sm font-semibold text-heading">Tahun Ajaran</label>
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
                    <button type="button" data-modal-hide="class-modal" class="px-5 py-2.5 text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body rounded-base transition-all duration-200 cursor-pointer">
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

<!-- Modal Dialog 2: Add Student using Flowbite component structure -->
<div id="student-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-default">
                <h3 class="text-lg font-bold text-heading">
                    Tambah Siswa Baru
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer" data-modal-hide="student-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form id="student-form" onsubmit="submitStudentForm(event)" class="p-4 md:p-5">
                <input type="hidden" name="class_id" id="student_class_id">
                
                <!-- Validation Errors Container -->
                <div id="student-form-errors" class="mb-4 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/20 p-3.5 rounded-base border border-red-200 dark:border-red-900/30 hidden">
                    <ul class="list-disc pl-5 space-y-1" id="errors-list"></ul>
                </div>

                <div class="grid gap-4 mb-4 grid-cols-1 md:grid-cols-2">
                    <!-- Name -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="student_name" class="block mb-2 text-sm font-semibold text-heading">Nama Lengkap</label>
                        <input type="text" name="name" id="student_name" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Nama lengkap siswa">
                    </div>
                    <!-- Status -->
                    <div class="col-span-2 md:col-span-1">
                        <label for="student_status" class="block mb-2 text-sm font-semibold text-heading">Status</label>
                        <select name="status" id="student_status" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                            <option value="ACTIVE" selected>ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
                        </select>
                    </div>

                    <!-- NIS -->
                    <div>
                        <label for="student_nis" class="block mb-2 text-sm font-semibold text-heading">NIS</label>
                        <input type="text" name="nis" id="student_nis" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Nomor Induk Siswa">
                    </div>
                    <!-- NISN -->
                    <div>
                        <label for="student_nisn" class="block mb-2 text-sm font-semibold text-heading">NISN</label>
                        <input type="text" name="nisn" id="student_nisn" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Nomor Induk Siswa Nasional">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="student_gender" class="block mb-2 text-sm font-semibold text-heading">Jenis Kelamin</label>
                        <select name="gender" id="student_gender" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <!-- Birth Place -->
                    <div>
                        <label for="student_birth_place" class="block mb-2 text-sm font-semibold text-heading">Tempat Lahir</label>
                        <input type="text" name="birth_place" id="student_birth_place" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Kota lahir">
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label for="student_birth_date" class="block mb-2 text-sm font-semibold text-heading">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="student_birth_date" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                    </div>
                    <!-- Parent Name -->
                    <div>
                        <label for="student_parent_name" class="block mb-2 text-sm font-semibold text-heading">Nama Wali / Orang Tua</label>
                        <input type="text" name="parent_name" id="student_parent_name" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Nama ayah/ibu/wali">
                    </div>

                    <!-- Parent Phone -->
                    <div>
                        <label for="student_parent_phone" class="block mb-2 text-sm font-semibold text-heading">No. HP Orang Tua</label>
                        <input type="text" name="parent_phone" id="student_parent_phone" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="contoh: 08123456789">
                    </div>
                    
                    <!-- Address -->
                    <div class="col-span-2">
                        <label for="student_address" class="block mb-2 text-sm font-semibold text-heading">Alamat</label>
                        <textarea name="address" id="student_address" rows="3" required class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Alamat lengkap tempat tinggal siswa"></textarea>
                    </div>
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-end gap-3 border-t border-default pt-4 mt-6">
                    <button type="button" data-modal-hide="student-modal" class="px-5 py-2.5 text-sm font-semibold border border-default hover:bg-neutral-tertiary text-body rounded-base transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-student" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Flowbite Toast Component -->
<div id="toast-success" class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-md dark:text-gray-400 dark:bg-neutral-primary-soft fixed bottom-5 right-5 z-50 border border-default hidden" role="alert">
    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-200">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <span class="sr-only">Check icon</span>
    </div>
    <div class="ms-3 text-sm font-normal" id="toast-message">Siswa berhasil ditambahkan.</div>
    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:bg-neutral-primary-soft dark:text-gray-500 dark:hover:text-white border-none cursor-pointer" onclick="document.getElementById('toast-success').classList.add('hidden')" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
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
        document.getElementById('modal-title').innerText = 'Ubah Kelas';
        document.getElementById('class-form').action = `/classes/${classData.id}`;
        document.getElementById('modal-method-container').innerHTML = '@method("PUT")';

        document.getElementById('modal_academic_year_id').value = classData.academic_year_id;
        document.getElementById('modal_name').value = classData.name;
    }

    // Open Modal for Add Student
    function prepareAddStudent() {
        document.getElementById('student_class_id').value = currentClassId;
        document.getElementById('student-form').reset();
        document.getElementById('student-form-errors').classList.add('hidden');
        document.getElementById('errors-list').innerHTML = '';
    }

    // Submit Student Form via AJAX
    function submitStudentForm(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        const submitBtn = document.getElementById('btn-submit-student');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan...';

        fetch("{{ route('students.store') }}", {
            method: 'POST',
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
            submitBtn.disabled = false;
            submitBtn.innerText = 'Simpan Siswa';
            
            // Add student to the local array
            const targetClass = classesData.find(c => c.id === currentClassId);
            if (targetClass) {
                if (!targetClass.students) {
                    targetClass.students = [];
                }
                targetClass.students.push(result.data);
                
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
            showToast('Siswa baru berhasil ditambahkan.');
        })
        .catch(errors => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Simpan Siswa';
            
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

        if (studentsList.length === 0) {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        tableContainer.classList.remove('hidden');
        emptyState.classList.add('hidden');

        // Sort alphabetically by name
        const sortedList = [...studentsList].sort((a, b) => a.name.localeCompare(b.name));

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
    });
</script>
@endsection
