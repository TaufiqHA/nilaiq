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
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8v8m0-8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8-8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 0a4 4 0 0 1-4 4h-1a3 3 0 0 0-3 3"/></svg>
                    </div>
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

        <!-- Add Button -->
        @if($classWaliKelas)
            <button type="button" onclick="openCreateModal()" data-modal-target="student-form-modal" data-modal-toggle="student-form-modal" class="w-full md:w-auto text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none cursor-pointer inline-flex items-center justify-center gap-2">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/></svg>
                <span>Tambah Siswa</span>
            </button>
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">L</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">P</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $student->birth_place }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">{{ $student->religion }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($student->status === 'ACTIVE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
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
</script>
@endsection
