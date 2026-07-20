@extends('layouts.waliKelas')

@section('title', 'Data Siswa')

@section('content')
<!-- Container Utama Data Siswa -->
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
                            <span class="inline-flex items-center text-xs font-bold text-heading">Data Siswa</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Data Siswa Kelas</h1>
            <p class="text-xs text-body mt-0.5">Kelola daftar peserta didik, identitas lengkap, dan data orang tua/wali kelas Anda.</p>
        </div>

        <!-- Class Badge Banner -->
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-neutral-primary-soft border border-default shadow-xs rounded-base">
            <div class="p-2.5 bg-brand text-white rounded-lg shadow-xs shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
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

    <!-- Action Toolbar (Search & Filter & Button) -->
    <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <!-- Search & Status Filter -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
            <form class="flex items-center w-full sm:max-w-xs space-x-2" onsubmit="event.preventDefault(); filterStudents();">   
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <input type="text" id="simple-search" oninput="filterStudents()" class="px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium rounded-base ps-9 text-heading text-sm focus:ring-brand focus:border-brand block w-full placeholder:text-body" placeholder="Cari nama, NIS, NISN..." />
                </div>
                <button type="submit" class="inline-flex items-center justify-center shrink-0 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs rounded-base w-10 h-10 focus:outline-none cursor-pointer">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                    <span class="sr-only">Cari</span>
                </button>
            </form>

            <select id="status-filter" onchange="filterStudents()" class="bg-neutral-secondary-medium border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full sm:w-44 p-2.5 transition-colors">
                <option value="ALL">Semua Status</option>
                <option value="ACTIVE">Aktif</option>
                <option value="INACTIVE">Tidak Aktif</option>
            </select>
        </div>

        <!-- Action Buttons (Import & Tambah Siswa) -->
        @if($classWaliKelas)
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="button" onclick="prepareImportStudents()" data-modal-target="student-import-modal" data-modal-toggle="student-import-modal" class="w-full md:w-auto text-white bg-emerald-600 hover:bg-emerald-700 box-border border border-transparent focus:ring-4 focus:ring-emerald-300 shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <span>Import Excel</span>
                </button>
                <button type="button" onclick="openCreateModal()" data-modal-target="student-form-modal" data-modal-toggle="student-form-modal" class="w-full md:w-auto text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                    <span>Tambah Siswa</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Data Table Container -->
    <div class="rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm text-left text-body">
                <thead class="text-[11px] md:text-xs font-bold text-heading uppercase bg-neutral-tertiary border-b border-default">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                        <th scope="col" class="px-4 py-3">NIS / NISN</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3 text-center">L/P</th>
                        <th scope="col" class="px-4 py-3">Tempat, Tgl Lahir</th>
                        <th scope="col" class="px-4 py-3">Agama</th>
                        <th scope="col" class="px-4 py-3 text-center">Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody id="students-table-body" class="divide-y divide-default">
                    @forelse($students as $index => $student)
                        <tr class="student-row hover:bg-neutral-tertiary/50 transition-colors"
                            data-name="{{ strtolower($student->name) }}"
                            data-nis="{{ $student->nis }}"
                            data-nisn="{{ $student->nisn }}"
                            data-status="{{ $student->status }}">
                            <td class="px-4 py-3 text-center font-bold text-heading">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs md:text-sm">
                                <span class="font-bold text-heading">{{ $student->nis }}</span>
                                @if($student->nisn)
                                    <span class="block text-[11px] md:text-xs text-body/60">NISN: {{ $student->nisn }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-heading">
                                {{ $student->name }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($student->gender === 'L')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Laki-laki</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-black">Perempuan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $student->birth_place }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">{{ $student->religion }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($student->status === 'ACTIVE')
                                    <span class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-black">
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Detail Button -->
                                    <button type="button" onclick="openDetailModal({{ json_encode($student) }})" title="Detail Siswa"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-base transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <!-- Edit Button -->
                                    <button type="button" onclick="openEditModal({{ json_encode($student) }})" title="Edit Siswa"
                                        class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-base transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <!-- Delete Button -->
                                    <button type="button" onclick="openDeleteModal({{ $student->id }}, '{{ addslashes($student->name) }}')" title="Hapus Siswa"
                                        class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-base transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-state-row">
                            <td colspan="8" class="px-4 py-12 text-center text-body">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-12 h-12 text-body/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="text-sm font-bold text-heading">Belum Ada Data Siswa</p>
                                    <p class="text-xs text-body">Silakan tambahkan siswa baru untuk kelas ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL FORM (TAMBAH / EDIT SISWA) -->
<!-- ========================================================================= -->
<div id="student-form-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                <h3 id="modal-title" class="text-lg font-medium text-heading">
                    Tambah Siswa Baru
                </h3>
                <button type="button" onclick="closeModal('student-form-modal')" data-modal-hide="student-form-modal" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center cursor-pointer">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="student-form" action="{{ route('wali-kelas.student-wali-kelas.store') }}" method="POST" class="p-4 space-y-4">
                @csrf
                <input type="hidden" id="form-method" name="_method" value="POST">
                <input type="hidden" name="class_id" value="{{ $classWaliKelas->id ?? '' }}">

                <!-- Tab navigation inside modal -->
                <div class="border-b border-default flex gap-2 overflow-x-auto text-xs md:text-sm font-bold text-body">
                    <button type="button" onclick="switchFormTab('tab-identitas')" id="btn-tab-identitas" class="tab-btn px-3 py-2 border-b-2 border-brand text-fg-brand pb-2 font-bold cursor-pointer">Identitas Siswa</button>
                    <button type="button" onclick="switchFormTab('tab-pendaftaran')" id="btn-tab-pendaftaran" class="tab-btn px-3 py-2 border-b-2 border-transparent text-body hover:text-heading pb-2 cursor-pointer">Pendaftaran</button>
                    <button type="button" onclick="switchFormTab('tab-ortu')" id="btn-tab-ortu" class="tab-btn px-3 py-2 border-b-2 border-transparent text-body hover:text-heading pb-2 cursor-pointer">Orang Tua</button>
                    <button type="button" onclick="switchFormTab('tab-wali')" id="btn-tab-wali" class="tab-btn px-3 py-2 border-b-2 border-transparent text-body hover:text-heading pb-2 cursor-pointer">Wali</button>
                </div>

                <!-- Tab Content 1: Identitas Siswa -->
                <div id="tab-identitas" class="tab-content space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label for="input-nis" class="block mb-1 text-xs md:text-sm font-bold text-heading">NIS <span class="text-red-500">*</span></label>
                            <input type="text" id="input-nis" name="nis" required placeholder="Contoh: 2024001"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-nisn" class="block mb-1 text-xs md:text-sm font-bold text-heading">NISN</label>
                            <input type="text" id="input-nisn" name="nisn" placeholder="Contoh: 0012345678"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="input-name" class="block mb-1 text-xs md:text-sm font-bold text-heading">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                        <input type="text" id="input-name" name="name" required placeholder="Masukkan nama lengkap siswa"
                            class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="input-gender" class="block mb-1 text-xs md:text-sm font-bold text-heading">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="input-gender" name="gender" required class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div>
                            <label for="input-birth_place" class="block mb-1 text-xs md:text-sm font-bold text-heading">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="input-birth_place" name="birth_place" required placeholder="Kota kelahiran"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-birth_date" class="block mb-1 text-xs md:text-sm font-bold text-heading">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="input-birth_date" name="birth_date" required
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="input-religion" class="block mb-1 text-xs md:text-sm font-bold text-heading">Agama <span class="text-red-500">*</span></label>
                            <select id="input-religion" name="religion" required class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Khonghucu">Khonghucu</option>
                            </select>
                        </div>
                        <div>
                            <label for="input-family_status" class="block mb-1 text-xs md:text-sm font-bold text-heading">Status Dalam Keluarga <span class="text-red-500">*</span></label>
                            <input type="text" id="input-family_status" name="family_status" required placeholder="Contoh: Anak Kandung" value="Anak Kandung"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-child_order" class="block mb-1 text-xs md:text-sm font-bold text-heading">Anak Ke- <span class="text-red-500">*</span></label>
                            <input type="text" id="input-child_order" name="child_order" required placeholder="Contoh: 1" value="1"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="input-address" class="block mb-1 text-xs md:text-sm font-bold text-heading">Alamat Tempat Tinggal <span class="text-red-500">*</span></label>
                        <textarea id="input-address" name="address" rows="2" required placeholder="Alamat lengkap siswa"
                            class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5"></textarea>
                    </div>

                    <div>
                        <label for="input-previous_school" class="block mb-1 text-xs md:text-sm font-bold text-heading">Sekolah Asal</label>
                        <input type="text" id="input-previous_school" name="previous_school" placeholder="Nama SMP / Sekolah Asal"
                            class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                    </div>
                </div>

                <!-- Tab Content 2: Status Pendaftaran -->
                <div id="tab-pendaftaran" class="tab-content hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="input-registration_status" class="block mb-1 text-xs md:text-sm font-bold text-heading">Status Pendaftaran</label>
                            <input type="text" id="input-registration_status" name="registration_status" placeholder="Contoh: Siswa Baru"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-accepted_class" class="block mb-1 text-xs md:text-sm font-bold text-heading">Diterima Di Kelas</label>
                            <input type="text" id="input-accepted_class" name="accepted_class" placeholder="Contoh: X MIPA 1"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-accepted_date" class="block mb-1 text-xs md:text-sm font-bold text-heading">Tanggal Diterima</label>
                            <input type="date" id="input-accepted_date" name="accepted_date"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div>
                        <label for="input-status" class="block mb-1 text-xs md:text-sm font-bold text-heading">Status Keaktifan Siswa</label>
                        <select id="input-status" name="status" class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                            <option value="ACTIVE">Aktif (ACTIVE)</option>
                            <option value="INACTIVE">Nonaktif (INACTIVE)</option>
                        </select>
                    </div>
                </div>

                <!-- Tab Content 3: Data Orang Tua -->
                <div id="tab-ortu" class="tab-content hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="input-father_name" class="block mb-1 text-xs md:text-sm font-bold text-heading">Nama Ayah</label>
                            <input type="text" id="input-father_name" name="father_name" placeholder="Nama lengkap ayah"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-father_job" class="block mb-1 text-xs md:text-sm font-bold text-heading">Pekerjaan Ayah</label>
                            <input type="text" id="input-father_job" name="father_job" placeholder="Pekerjaan ayah"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="input-mother_name" class="block mb-1 text-xs md:text-sm font-bold text-heading">Nama Ibu</label>
                            <input type="text" id="input-mother_name" name="mother_name" placeholder="Nama lengkap ibu"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-mother_job" class="block mb-1 text-xs md:text-sm font-bold text-heading">Pekerjaan Ibu</label>
                            <input type="text" id="input-mother_job" name="mother_job" placeholder="Pekerjaan ibu"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="input-parent_phone" class="block mb-1 text-xs md:text-sm font-bold text-heading">No. Telp / WA Orang Tua</label>
                            <input type="text" id="input-parent_phone" name="parent_phone" placeholder="Contoh: 081234567890"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-parent_address" class="block mb-1 text-xs md:text-sm font-bold text-heading">Alamat Orang Tua</label>
                            <input type="text" id="input-parent_address" name="parent_address" placeholder="Alamat ortu (kosongkan jika sama)"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>
                </div>

                <!-- Tab Content 4: Data Wali -->
                <div id="tab-wali" class="tab-content hidden space-y-4">
                    <div class="p-3 bg-neutral-tertiary rounded-base border border-default text-xs md:text-sm text-body">
                        * Data Wali diisi jika siswa tinggal bersama wali (bukan orang tua kandung).
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="input-guardian_name" class="block mb-1 text-xs md:text-sm font-bold text-heading">Nama Wali</label>
                            <input type="text" id="input-guardian_name" name="guardian_name" placeholder="Nama lengkap wali"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-guardian_job" class="block mb-1 text-xs md:text-sm font-bold text-heading">Pekerjaan Wali</label>
                            <input type="text" id="input-guardian_job" name="guardian_job" placeholder="Pekerjaan wali"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="input-guardian_phone" class="block mb-1 text-xs md:text-sm font-bold text-heading">No. Telp / WA Wali</label>
                            <input type="text" id="input-guardian_phone" name="guardian_phone" placeholder="Contoh: 081234567890"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                        <div>
                            <label for="input-guardian_address" class="block mb-1 text-xs md:text-sm font-bold text-heading">Alamat Wali</label>
                            <input type="text" id="input-guardian_address" name="guardian_address" placeholder="Alamat lengkap wali"
                                class="bg-neutral-secondary-medium border border-default text-heading text-xs md:text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6 mt-4">
                    <button type="submit" class="inline-flex items-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer">
                        <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                        Simpan Data Siswa
                    </button>
                    <button type="button" onclick="closeModal('student-form-modal')" data-modal-hide="student-form-modal" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL SISWA -->
<!-- ========================================================================= -->
<div id="student-detail-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                <h3 class="text-lg font-medium text-heading">
                    Detail Profil Siswa
                </h3>
                <button type="button" onclick="closeModal('student-detail-modal')" data-modal-hide="student-detail-modal" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center cursor-pointer">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="py-4 space-y-4 text-xs md:text-sm text-body">
                <!-- Identitas Header -->
                <div class="flex items-center gap-3 p-3 rounded-base bg-neutral-tertiary border border-default">
                    <div class="w-10 h-10 rounded-full bg-brand flex items-center justify-center font-extrabold text-white text-sm shrink-0">
                        <span id="detail-avatar">S</span>
                    </div>
                    <div>
                        <h4 id="detail-name" class="text-base md:text-lg font-extrabold text-heading">-</h4>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="font-mono text-body/80">NIS: <strong id="detail-nis" class="text-heading">-</strong></span>
                            <span class="font-mono text-body/80">NISN: <strong id="detail-nisn" class="text-heading">-</strong></span>
                            <span id="detail-status-badge"></span>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2 p-3 bg-neutral-secondary-medium rounded-base border border-default">
                        <span class="font-bold text-heading uppercase text-[10px] tracking-wider text-body/70 block">Identitas Diri</span>
                        <div><span class="text-body/70">Jenis Kelamin:</span> <strong id="detail-gender" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Tempat, Tgl Lahir:</span> <strong id="detail-ttl" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Agama:</span> <strong id="detail-religion" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Status Keluarga:</span> <strong id="detail-family_status" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Anak Ke:</span> <strong id="detail-child_order" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Alamat:</span> <strong id="detail-address" class="text-heading block mt-0.5">-</strong></div>
                        <div><span class="text-body/70">Sekolah Asal:</span> <strong id="detail-previous_school" class="text-heading">-</strong></div>
                    </div>

                    <div class="space-y-2 p-3 bg-neutral-secondary-medium rounded-base border border-default">
                        <span class="font-bold text-heading uppercase text-[10px] tracking-wider text-body/70 block">Data Orang Tua & Wali</span>
                        <div><span class="text-body/70">Nama Ayah:</span> <strong id="detail-father_name" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Pekerjaan Ayah:</span> <strong id="detail-father_job" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Nama Ibu:</span> <strong id="detail-mother_name" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Pekerjaan Ibu:</span> <strong id="detail-mother_job" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">No. Telp Ortu:</span> <strong id="detail-parent_phone" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">Nama Wali:</span> <strong id="detail-guardian_name" class="text-heading">-</strong></div>
                        <div><span class="text-body/70">No. Telp Wali:</span> <strong id="detail-guardian_phone" class="text-heading">-</strong></div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center justify-end border-t border-default pt-4 md:pt-6">
                <button type="button" onclick="closeModal('student-detail-modal')" data-modal-hide="student-detail-modal" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI HAPUS -->
<!-- ========================================================================= -->
<div id="student-delete-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white dark:bg-neutral-primary-soft rounded-base shadow-lg border border-default overflow-hidden">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-heading">Konfirmasi Hapus Siswa</h3>
                    <p class="text-xs text-body mt-1">Apakah Anda yakin ingin menghapus data siswa <strong id="delete-student-name" class="text-heading"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                </div>

                <form id="delete-form" action="" method="POST" class="pt-2 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('student-delete-modal')" class="px-4 py-2 text-xs font-bold text-body bg-neutral-secondary-medium border border-default rounded-base hover:bg-neutral-tertiary transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-base transition-colors cursor-pointer shadow-xs">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL IMPORT SISWA VIA EXCEL / CSV -->
<!-- ========================================================================= -->
<div id="student-import-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default p-4 md:p-6 flex flex-col max-h-[90vh]">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5 shrink-0">
                <h3 id="import-modal-title" class="text-lg font-bold text-heading">
                    Import Siswa via Excel / CSV
                </h3>
                <button type="button" onclick="closeModal('student-import-modal')" data-modal-hide="student-import-modal" class="text-body bg-transparent hover:bg-neutral-secondary-soft hover:text-heading rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral-tertiary cursor-pointer">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="py-4 md:py-6 overflow-y-auto flex-1 space-y-6">
                <!-- Instructions and Template download -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start bg-neutral-secondary-medium/50 dark:bg-neutral-primary-soft border border-default p-4 rounded-base">
                    <div>
                        <h4 class="font-bold text-heading text-sm mb-2">Petunjuk Penggunaan:</h4>
                        <ul class="list-disc pl-5 text-xs text-body space-y-1">
                            <li>Format file harus berupa Excel (<strong>.xlsx</strong> / <strong>.xls</strong>) atau <strong>.csv</strong>.</li>
                            <li>Pastikan baris pertama berisi nama kolom (header) seperti: <strong>nis</strong>, <strong>nisn</strong>, <strong>nama</strong>, <strong>jenis_kelamin</strong> (L/P), <strong>tempat_lahir</strong>, <strong>tanggal_lahir</strong> (Format: YYYY-MM-DD), <strong>agama</strong>, <strong>alamat</strong>, <strong>status</strong> (ACTIVE/INACTIVE).</li>
                            <li>Kolom opsional: <strong>status_keluarga</strong>, <strong>anak_ke</strong>, <strong>sekolah_asal</strong>, <strong>nama_ayah</strong>, <strong>pekerjaan_ayah</strong>, <strong>nama_ibu</strong>, <strong>pekerjaan_ibu</strong>, <strong>no_hp_ortu</strong>, <strong>nama_wali</strong>, <strong>no_hp_wali</strong>.</li>
                        </ul>
                    </div>
                    <div class="flex flex-col justify-center items-center h-full border-t md:border-t-0 md:border-l border-default pt-4 md:pt-0 md:pl-6 text-center">
                        <p class="text-xs text-body mb-3 font-semibold">Gunakan template di bawah untuk memastikan struktur kolom benar:</p>
                        <button type="button" onclick="downloadTemplate()" class="inline-flex items-center gap-2 px-4 py-2 border border-default bg-neutral-secondary-medium dark:bg-neutral-primary-soft text-heading hover:bg-neutral-tertiary-medium dark:hover:bg-neutral-tertiary text-xs font-bold rounded-base transition-colors duration-150 shadow-xs cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Unduh Template CSV
                        </button>
                    </div>
                </div>

                <!-- Dropzone / File input -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-heading">Pilih File Spreadsheet</label>
                    <div class="flex items-center justify-center w-full">
                        <label id="import-dropzone" for="import-file-input" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-default rounded-base cursor-pointer bg-neutral-secondary-medium/20 hover:bg-neutral-secondary-medium/40 transition-colors duration-150">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2.5 text-neutral-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-1 text-xs text-body"><span class="font-bold">Klik untuk unggah</span> atau drag and drop</p>
                                <p class="text-[10px] text-body opacity-80">XLSX, XLS, atau CSV (Maks. 5MB)</p>
                            </div>
                            <input id="import-file-input" type="file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleFileSelect(event)" />
                        </label>
                    </div>
                </div>

                <!-- Backend/Frontend Validation Warnings Container -->
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
                        <span id="preview-count" class="px-2 py-0.5 rounded text-xs font-bold bg-brand/10 text-brand border border-brand/20">0 Siswa</span>
                    </div>
                    <div class="relative overflow-x-auto border border-default rounded-base max-h-[30vh]">
                        <table class="w-full text-xs text-left text-body">
                            <thead class="text-[10px] font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none sticky top-0">
                                <tr>
                                    <th scope="col" class="px-4 py-2.5 text-center w-10">Baris</th>
                                    <th scope="col" class="px-4 py-2.5 min-w-[150px]">Nama Lengkap</th>
                                    <th scope="col" class="px-4 py-2.5 min-w-[80px]">NIS</th>
                                    <th scope="col" class="px-4 py-2.5 min-w-[80px]">NISN</th>
                                    <th scope="col" class="px-4 py-2.5 text-center min-w-[50px]">L/P</th>
                                    <th scope="col" class="px-4 py-2.5 min-w-[150px]">Tempat, Tgl Lahir</th>
                                    <th scope="col" class="px-4 py-2.5 min-w-[120px]">Agama</th>
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
                <button onclick="closeModal('student-import-modal')" data-modal-hide="student-import-modal" type="button" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-semibold leading-5 rounded-base text-sm px-5 py-2.5 focus:outline-none cursor-pointer">Batal</button>
                <button type="button" onclick="submitImport()" id="btn-confirm-import" class="inline-flex items-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-bold leading-5 rounded-base text-sm px-5 py-2.5 focus:outline-none cursor-pointer disabled:bg-neutral-tertiary disabled:text-fg-disabled disabled:cursor-not-allowed" disabled>
                    <svg class="w-4 h-4 me-1.5 -ms-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Mulai Import
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching inside create/edit modal
    function switchFormTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-brand', 'text-fg-brand', 'font-bold');
            el.classList.add('border-transparent', 'text-body');
        });

        document.getElementById(tabId).classList.remove('hidden');
        const activeBtn = document.getElementById('btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-body');
        activeBtn.classList.add('border-brand', 'text-fg-brand', 'font-bold');
    }

    // Modal Control Functions
    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Tambah Siswa Baru';
        const form = document.getElementById('student-form');
        form.action = "{{ route('wali-kelas.student-wali-kelas.store') }}";
        document.getElementById('form-method').value = 'POST';
        form.reset();
        switchFormTab('tab-identitas');
        document.getElementById('student-form-modal').classList.remove('hidden');
    }

    function openEditModal(student) {
        document.getElementById('modal-title').innerText = 'Edit Data Siswa';
        const form = document.getElementById('student-form');
        
        let updateUrl = "{{ route('wali-kelas.student-wali-kelas.update', ':id') }}";
        form.action = updateUrl.replace(':id', student.id);
        document.getElementById('form-method').value = 'PUT';

        // Populate fields
        document.getElementById('input-nis').value = student.nis || '';
        document.getElementById('input-nisn').value = student.nisn || '';
        document.getElementById('input-name').value = student.name || '';
        document.getElementById('input-gender').value = student.gender || 'L';
        document.getElementById('input-birth_place').value = student.birth_place || '';
        document.getElementById('input-birth_date').value = student.birth_date ? student.birth_date.substring(0, 10) : '';
        document.getElementById('input-religion').value = student.religion || 'Islam';
        document.getElementById('input-family_status').value = student.family_status || 'Anak Kandung';
        document.getElementById('input-child_order').value = student.child_order || '1';
        document.getElementById('input-address').value = student.address || '';
        document.getElementById('input-previous_school').value = student.previous_school || '';
        document.getElementById('input-registration_status').value = student.registration_status || '';
        document.getElementById('input-accepted_class').value = student.accepted_class || '';
        document.getElementById('input-accepted_date').value = student.accepted_date ? student.accepted_date.substring(0, 10) : '';
        document.getElementById('input-status').value = student.status || 'ACTIVE';
        document.getElementById('input-father_name').value = student.father_name || '';
        document.getElementById('input-father_job').value = student.father_job || '';
        document.getElementById('input-mother_name').value = student.mother_name || '';
        document.getElementById('input-mother_job').value = student.mother_job || '';
        document.getElementById('input-parent_phone').value = student.parent_phone || '';
        document.getElementById('input-parent_address').value = student.parent_address || '';
        document.getElementById('input-guardian_name').value = student.guardian_name || '';
        document.getElementById('input-guardian_job').value = student.guardian_job || '';
        document.getElementById('input-guardian_phone').value = student.guardian_phone || '';
        document.getElementById('input-guardian_address').value = student.guardian_address || '';

        switchFormTab('tab-identitas');
        document.getElementById('student-form-modal').classList.remove('hidden');
    }

    function openDetailModal(student) {
        document.getElementById('detail-avatar').innerText = student.name ? student.name.substring(0, 1).toUpperCase() : 'S';
        document.getElementById('detail-name').innerText = student.name || '-';
        document.getElementById('detail-nis').innerText = student.nis || '-';
        document.getElementById('detail-nisn').innerText = student.nisn || '-';
        document.getElementById('detail-gender').innerText = student.gender === 'L' ? 'Laki-laki' : 'Perempuan';
        document.getElementById('detail-ttl').innerText = (student.birth_place || '-') + ', ' + (student.birth_date ? new Date(student.birth_date).toLocaleDateString('id-ID') : '-');
        document.getElementById('detail-religion').innerText = student.religion || '-';
        document.getElementById('detail-family_status').innerText = student.family_status || '-';
        document.getElementById('detail-child_order').innerText = student.child_order || '-';
        document.getElementById('detail-address').innerText = student.address || '-';
        document.getElementById('detail-previous_school').innerText = student.previous_school || '-';
        document.getElementById('detail-father_name').innerText = student.father_name || '-';
        document.getElementById('detail-father_job').innerText = student.father_job || '-';
        document.getElementById('detail-mother_name').innerText = student.mother_name || '-';
        document.getElementById('detail-mother_job').innerText = student.mother_job || '-';
        document.getElementById('detail-parent_phone').innerText = student.parent_phone || '-';
        document.getElementById('detail-guardian_name').innerText = student.guardian_name || '-';
        document.getElementById('detail-guardian_phone').innerText = student.guardian_phone || '-';

        const statusBadge = document.getElementById('detail-status-badge');
        if (student.status === 'ACTIVE') {
            statusBadge.innerHTML = '<span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full">Aktif</span>';
        } else {
            statusBadge.innerHTML = '<span class="px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 rounded-full">Nonaktif</span>';
        }

        document.getElementById('student-detail-modal').classList.remove('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-student-name').innerText = name;
        let deleteUrl = "{{ route('wali-kelas.student-wali-kelas.destroy', ':id') }}";
        document.getElementById('delete-form').action = deleteUrl.replace(':id', id);
        document.getElementById('student-delete-modal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function dismissAlert(alertEl) {
        if (alertEl) {
            alertEl.classList.add('opacity-0');
            setTimeout(() => alertEl.remove(), 300);
        }
    }

    // Live Table Filtering
    function filterStudents() {
        const searchInput = document.getElementById('simple-search') || document.getElementById('search-input');
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusFilter = document.getElementById('status-filter').value;
        const rows = document.querySelectorAll('.student-row');

        let visibleCount = 0;
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nis = row.getAttribute('data-nis') || '';
            const nisn = row.getAttribute('data-nisn') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = name.includes(query) || nis.includes(query) || nisn.includes(query);
            const matchesStatus = (statusFilter === 'ALL') || (status === statusFilter);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyRow = document.getElementById('empty-state-row');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    // Setup and state for Bulk Import
    const currentClassWaliKelasId = {{ $classWaliKelas ? $classWaliKelas->id : 'null' }};
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
        document.getElementById('student-import-modal').classList.remove('hidden');
    }

    function downloadTemplate() {
        const csvContent = "\uFEFFnis,nisn,nama,jenis_kelamin,tempat_lahir,tanggal_lahir,agama,status_keluarga,anak_ke,alamat,sekolah_asal,nama_ayah,pekerjaan_ayah,nama_ibu,pekerjaan_ibu,no_hp_ortu,nama_wali,pekerjaan_wali,no_hp_wali,status\n" +
            "2024001,0012345678,Ahmad Fauzi,L,Jakarta,2010-08-15,Islam,Anak Kandung,1,Jl. Mawar No. 5,SMP 1 Jakarta,Budi,PNS,Siti,Ibu Rumah Tangga,08123456789,,,ACTIVE\n" +
            "2024002,0012345679,Siti Aminah,P,Surabaya,2011-04-12,Islam,Anak Kandung,2,Jl. Melati No. 12,SMP 2 Surabaya,Agus,Wiraswasta,Dewi,Karyawan,08987654321,,,ACTIVE";
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "template_import_siswa_wali_kelas.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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

    function handleFileSelect(event) {
        const file = event.target.files[0];
        processFile(file);
    }

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

    function parseExcelData(rawData) {
        if (!rawData || rawData.length < 2) {
            alert('File kosong atau tidak memiliki data siswa.');
            return;
        }

        const headers = rawData[0].map(h => String(h || '').trim().toLowerCase());
        const rows = rawData.slice(1);

        const headerMap = {
            'nis': ['nis', 'no. induk', 'nomor induk'],
            'nisn': ['nisn', 'nomor induk nasional'],
            'name': ['name', 'nama', 'nama lengkap', 'nama siswa'],
            'gender': ['gender', 'jenis_kelamin', 'l/p', 'jk', 'sex'],
            'birth_place': ['birth_place', 'tempat lahir', 'tempat_lahir'],
            'birth_date': ['birth_date', 'tanggal lahir', 'tanggal_lahir', 'tgl lahir', 'tgl_lahir'],
            'religion': ['religion', 'agama'],
            'family_status': ['family_status', 'status keluarga', 'status_keluarga'],
            'child_order': ['child_order', 'anak ke', 'anak_ke'],
            'address': ['address', 'alamat', 'alamat tinggal'],
            'previous_school': ['previous_school', 'sekolah asal', 'sekolah_asal'],
            'father_name': ['father_name', 'nama ayah', 'nama_ayah'],
            'father_job': ['father_job', 'pekerjaan ayah', 'pekerjaan_ayah'],
            'mother_name': ['mother_name', 'nama ibu', 'nama_ibu'],
            'mother_job': ['mother_job', 'pekerjaan ibu', 'pekerjaan_ibu'],
            'parent_phone': ['parent_phone', 'no hp ortu', 'no_hp_ortu', 'no hp orang tua', 'telepon ortu'],
            'guardian_name': ['guardian_name', 'nama wali', 'nama_wali'],
            'guardian_job': ['guardian_job', 'pekerjaan wali', 'pekerjaan_wali'],
            'guardian_phone': ['guardian_phone', 'no hp wali', 'no_hp_wali'],
            'status': ['status']
        };

        const indexMap = {};
        Object.keys(headerMap).forEach(key => {
            indexMap[key] = headers.findIndex(h => headerMap[key].includes(h));
        });

        if (indexMap.name === -1) {
            alert('Kolom "Nama" / "Name" tidak ditemukan di file. Pastikan header kolom sudah benar sesuai template.');
            return;
        }

        const parsedStudents = [];
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

            let gender = getVal('gender').toUpperCase();
            if (gender.startsWith('L') || gender === 'LAKI' || gender === 'LAKI-LAKI') {
                gender = 'L';
            } else if (gender.startsWith('P') || gender === 'PEREMPUAN') {
                gender = 'P';
            }

            let status = getVal('status').toUpperCase();
            if (status !== 'ACTIVE' && status !== 'INACTIVE') {
                if (status === 'AKTIF' || status === '1' || status === 'TRUE' || status === '') {
                    status = 'ACTIVE';
                } else {
                    status = 'INACTIVE';
                }
            }

            const student = {
                rowNum: i + 2,
                nis: getVal('nis'),
                nisn: getVal('nisn'),
                name: getVal('name'),
                gender: gender,
                birth_place: getVal('birth_place'),
                birth_date: rawDate,
                religion: getVal('religion') || 'Islam',
                family_status: getVal('family_status') || 'Anak Kandung',
                child_order: getVal('child_order') || '1',
                address: getVal('address'),
                previous_school: getVal('previous_school'),
                father_name: getVal('father_name'),
                father_job: getVal('father_job'),
                mother_name: getVal('mother_name'),
                mother_job: getVal('mother_job'),
                parent_phone: getVal('parent_phone'),
                guardian_name: getVal('guardian_name'),
                guardian_job: getVal('guardian_job'),
                guardian_phone: getVal('guardian_phone'),
                status: status,
                errors: []
            };

            if (!student.name) student.errors.push('Nama wajib diisi');
            if (!student.nis) student.errors.push('NIS wajib diisi');
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

            if (student.errors.length > 0) {
                hasValidationErrors = true;
            }

            parsedStudents.push(student);
        });

        studentsToImport = parsedStudents;
        displayImportPreview(parsedStudents, hasValidationErrors);
    }

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
                <td class="px-4 py-3 whitespace-nowrap">${student.religion || '-'}</td>
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

    function submitImport() {
        if (studentsToImport.length === 0 || !currentClassWaliKelasId) return;

        const importBtn = document.getElementById('btn-confirm-import');
        importBtn.disabled = true;
        const originalText = importBtn.innerText;
        importBtn.innerText = 'Mengimport...';

        const payload = {
            class_id: currentClassWaliKelasId,
            students: studentsToImport.map(s => ({
                nis: s.nis,
                nisn: s.nisn,
                name: s.name,
                gender: s.gender,
                birth_place: s.birth_place,
                birth_date: s.birth_date,
                religion: s.religion,
                family_status: s.family_status,
                child_order: s.child_order,
                address: s.address,
                previous_school: s.previous_school,
                father_name: s.father_name,
                father_job: s.father_job,
                mother_name: s.mother_name,
                mother_job: s.mother_job,
                parent_phone: s.parent_phone,
                guardian_name: s.guardian_name,
                guardian_job: s.guardian_job,
                guardian_phone: s.guardian_phone,
                status: s.status
            }))
        };

        fetch("{{ route('wali-kelas.student-wali-kelas.import') }}", {
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
                closeModal('student-import-modal');
                window.location.reload();
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

    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('import-dropzone');
        const fileInput = document.getElementById('import-file-input');

        if (dropzone && fileInput) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('border-default', 'bg-neutral-secondary-medium/20', 'hover:bg-neutral-secondary-medium/40');
                    dropzone.classList.add('border-brand', 'bg-brand/10');
                }, false);
            });

            ['dragleave', 'dragend', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('border-brand', 'bg-brand/10');
                    dropzone.classList.add('border-default', 'bg-neutral-secondary-medium/20', 'hover:bg-neutral-secondary-medium/40');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    fileInput.files = files;
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
