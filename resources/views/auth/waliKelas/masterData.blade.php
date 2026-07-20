@extends('layouts.waliKelas')

@section('title', 'Master Data Wali Kelas')

@section('content')
@php
    $settingsWaliKelas = $settingsWaliKelas ?? \App\Models\SettingsWaliKelas::with('academicYear')->first();
    $mapelSettings = $mapelSettings ?? ($settingsWaliKelas ? \App\Models\MapelSettings::where('settingsWaliKelas_id', $settingsWaliKelas->id)->get() : \App\Models\MapelSettings::all());
    $academicYears = auth()->check()
        ? \App\Models\AcademicYear::where('user_id', auth()->id())->get()
        : \App\Models\AcademicYear::all();
@endphp


<!-- Container Utama Master Data -->
<div class="p-0 sm:p-6 border-0 sm:border border-default border-dashed rounded-none sm:rounded-base bg-transparent sm:bg-white/40 dark:sm:bg-neutral-secondary-medium/20 backdrop-blur-none sm:backdrop-blur-md space-y-4 sm:space-y-6 w-full">

    <!-- Header Section -->
    <div class="border-b border-default pb-4">
        <div>
            <!-- Breadcrumb -->
            <nav class="flex text-xs text-body mb-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <span class="text-body font-medium">Wali Kelas</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-body/50" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ms-1 font-bold text-heading">Master Data</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Master Data Wali Kelas</h1>
            <p class="text-xs text-body mt-0.5">Kelola identitas sekolah, data wali kelas, serta pengaturan mata pelajaran dan KKM.</p>
        </div>
    </div>

    <!-- Alert Container (Full Width Flowbite Alerts) -->
    <div id="page-alert-container" class="w-full space-y-3">
        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-success-strong bg-success-soft border border-emerald-300/40 dark:bg-emerald-950/90 dark:text-emerald-300 dark:border-emerald-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p> {{ session('success') }}</p>
            </div>
        @endif

        @if(session('error') || session('danger'))
            <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-danger-strong bg-danger-soft border border-red-300/40 dark:bg-red-950/90 dark:text-red-300 dark:border-red-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p> {{ session('error') ?? session('danger') }}</p>
            </div>
        @endif

        @if(session('warning'))
            <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-warning bg-warning-soft border border-amber-300/40 dark:bg-amber-950/90 dark:text-amber-300 dark:border-amber-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p> {{ session('warning') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-fg-brand-strong bg-brand-softer border border-blue-300/40 dark:bg-blue-950/90 dark:text-blue-300 dark:border-blue-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p> {{ session('info') }}</p>
            </div>
        @endif
    </div>

    <!-- Flowbite Tabs Header -->
    <div class="border-b border-default">
        <ul class="flex flex-wrap -mb-px text-xs font-semibold text-center text-body" id="masterDataTab" data-tabs-toggle="#masterDataTabContent" role="tablist">
            <li class="me-2" role="presentation">
                <button class="inline-flex items-center justify-center p-3 border-b-2 rounded-t-lg hover:text-fg-brand hover:border-brand transition-all duration-200 group active" id="identitas-tab" data-tabs-target="#identitas" type="button" role="tab" aria-controls="identitas" aria-selected="true">
                    {{-- <svg class="w-4 h-4 me-2 text-body group-hover:text-fg-brand transition-colors duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/>
                    </svg> --}}
                    Identitas Sekolah & Wali Kelas
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button class="inline-flex items-center justify-center p-3 border-b-2 rounded-t-lg hover:text-fg-brand hover:border-brand transition-all duration-200 group" id="mapel-tab" data-tabs-target="#mapel" type="button" role="tab" aria-controls="mapel" aria-selected="false">
                    {{-- <svg class="w-4 h-4 me-2 text-body group-hover:text-fg-brand transition-colors duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-7.5-3-7.5-3v13.75c0 .104.058.2.15.25 1.125.625 5.432 2.378 7.35 3M12 6.03c2.819-.831 7.5-3 7.5-3v13.75c0 .104-.058.2-.15.25-1.125.625-5.432 2.378-7.35 3M12 19.03V20"/>
                    </svg> --}}
                    Mata Pelajaran & KKM
                    <span class="ms-2 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-brand-soft text-fg-brand" id="mapel-count-badge">{{ count($mapelSettings) }}</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Flowbite Tabs Content Container -->
    <div id="masterDataTabContent">

        <!-- TAB 1: IDENTITAS SEKOLAH & WALI KELAS -->
        <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">
            <form id="form-settings-wali-kelas" action="{{ Route::has('settings-wali-kelas.store') ? route('settings-wali-kelas.store') : '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Grid Form Identitas Sekolah -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-body/70 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        1. Data Utama Sekolah
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="school_name" class="block mb-1 text-xs font-semibold text-heading">Nama Sekolah <span class="text-red-500">*</span></label>
                            <input type="text" id="school_name" name="school_name" value="{{ old('school_name', $settingsWaliKelas?->school_name) }}" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Contoh: SMA Negeri 1 Jakarta">
                        </div>

                        <div>
                            <label for="npsn" class="block mb-1 text-xs font-semibold text-heading">NPSN <span class="text-red-500">*</span></label>
                            <input type="text" id="npsn" name="npsn" value="{{ old('npsn', $settingsWaliKelas?->npsn) }}" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Contoh: 10293847">
                        </div>

                        <div class="md:col-span-2">
                            <label for="school_address" class="block mb-1 text-xs font-semibold text-heading">Alamat Lengkap Sekolah <span class="text-red-500">*</span></label>
                            <textarea id="school_address" name="school_address" rows="2" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Jl. Pendidikan No. 12, Kec. Gambir, Kota Jakarta Pusat">{{ old('school_address', $settingsWaliKelas?->school_address) }}</textarea>
                        </div>

                        <div>
                            <label for="principal_name" class="block mb-1 text-xs font-semibold text-heading">Nama Kepala Sekolah <span class="text-red-500">*</span></label>
                            <input type="text" id="principal_name" name="principal_name" value="{{ old('principal_name', $settingsWaliKelas?->principal_name) }}" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Dr. H. Ahmad Dahlan, M.Pd">
                        </div>

                        <div>
                            <label for="school_logo_file" class="block mb-2 text-xs font-semibold text-heading">Logo Sekolah (Opsional)</label>
                            <div class="flex items-center gap-3">
                                <!-- Thumbnail Preview Container -->
                                <div id="school_logo_preview_container" class="shrink-0 {{ $settingsWaliKelas?->school_logo ? '' : 'hidden' }}">
                                    <img id="school_logo_preview" 
                                         src="{{ $settingsWaliKelas?->school_logo ? asset('storage/' . $settingsWaliKelas->school_logo) : '#' }}" 
                                         alt="Preview Logo" 
                                         class="w-11 h-11 object-contain rounded-base border border-default p-1 bg-white dark:bg-neutral-secondary-medium shadow-xs">
                                </div>

                                <!-- File Input Control -->
                                <div class="flex-1">
                                    <label for="school_logo_file" class="flex items-center w-full bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base cursor-pointer hover:bg-neutral-tertiary transition-colors overflow-hidden">
                                        <span class="px-4 py-2.5 bg-brand hover:bg-brand-strong text-white font-bold shrink-0 flex items-center gap-1.5 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            Pilih File
                                        </span>
                                        <span id="school_logo_filename" class="flex-1 text-center px-3 text-xs text-body truncate">
                                            {{ $settingsWaliKelas?->school_logo ? basename($settingsWaliKelas->school_logo) : 'Belum ada file dipilih' }}
                                        </span>
                                    </label>
                                    <input type="file" id="school_logo_file" name="school_logo_file" accept="image/*" class="hidden" onchange="handleLogoChange(event)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Form Identitas Wali Kelas -->
                <div class="border-t border-default pt-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-body/70 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        2. Identitas Wali Kelas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="teacher_name" class="block mb-1 text-xs font-semibold text-heading">Nama Lengkap & Gelar Wali Kelas <span class="text-red-500">*</span></label>
                            <input type="text" id="teacher_name" name="teacher_name" value="{{ old('teacher_name', $settingsWaliKelas?->teacher_name ?? auth()->user()->name) }}" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Budi Santoso, S.Pd">
                        </div>

                        <div>
                            <label for="teacher_nip" class="block mb-1 text-xs font-semibold text-heading">NIP Wali Kelas</label>
                            <input type="text" id="teacher_nip" name="teacher_nip" value="{{ old('teacher_nip', $settingsWaliKelas?->teacher_nip) }}" class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="198501012010011001">
                        </div>

                        <div>
                            <label for="teacher_email" class="block mb-1 text-xs font-semibold text-heading">Email Resmi</label>
                            <input type="email" id="teacher_email" name="teacher_email" value="{{ old('teacher_email', $settingsWaliKelas?->teacher_email ?? auth()->user()->email) }}" class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="walikelas@sekolah.sch.id">
                        </div>

                        <div>
                            <label for="teacher_phone" class="block mb-1 text-xs font-semibold text-heading">No. Telepon / WhatsApp</label>
                            <input type="text" id="teacher_phone" name="teacher_phone" value="{{ old('teacher_phone', $settingsWaliKelas?->teacher_phone) }}" class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="081234567890">
                        </div>
                    </div>
                </div>

                <!-- Grid Form Pengaturan Raport -->
                <div class="border-t border-default pt-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-body/70 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        3. Penetapan Raport & Tahun Akademik
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="academicYear_id" class="block mb-1 text-xs font-semibold text-heading">Tahun Akademik Aktif <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <select id="academicYear_id" name="academicYear_id" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                                    <option value="" disabled {{ !$settingsWaliKelas?->academicYear_id ? 'selected' : '' }}>-- Pilih Tahun Akademik --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academicYear_id', $settingsWaliKelas?->academicYear_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->year }} - Semester {{ $year->semester }} {{ $year->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" data-modal-target="modal-tambah-tahun-ajaran" data-modal-toggle="modal-tambah-tahun-ajaran" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none shrink-0 flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Kelola
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="tanggal_penerimaan_rapor" class="block mb-1 text-xs font-semibold text-heading">Tanggal Penerimaan Raport <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_penerimaan_rapor" name="tanggal_penerimaan_rapor" value="{{ old('tanggal_penerimaan_rapor', isset($settingsWaliKelas->tanggal_penerimaan_rapor) ? \Carbon\Carbon::parse($settingsWaliKelas->tanggal_penerimaan_rapor)->format('Y-m-d') : '') }}" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-end border-t border-default pt-4">
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base focus:ring-4 focus:ring-brand-medium transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Identitas Master Data
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: MATA PELAJARAN & KKM -->
        <div class="hidden p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs space-y-4" id="mapel" role="tabpanel" aria-labelledby="mapel-tab">
            
            <!-- Top Controls Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-default pb-3">
                {{-- <div>
                    <h3 class="text-md font-bold text-heading flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand"></span>
                        Pengaturan Mata Pelajaran & Nilai KKM
                    </h3>
                    <p class="text-xs text-body">Daftar mata pelajaran yang diampu oleh siswa kelas ini beserta Kriteria Ketuntasan Minimal (KKM).</p>
                </div> --}}
                <button type="button" data-modal-target="modal-tambah-mapel" data-modal-toggle="modal-tambah-mapel" class="px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base focus:ring-4 focus:ring-brand-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Mata Pelajaran
                </button>
            </div>

            <!-- Flowbite Data Table -->
            <div class="relative overflow-x-auto border border-default rounded-base">
                <table class="w-full text-xs text-left text-body">
                    <thead class="text-[11px] font-bold text-heading uppercase bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/60 border-b border-default">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                            <th scope="col" class="px-4 py-3">Mata Pelajaran</th>
                            <th scope="col" class="px-4 py-3">Guru Pengampu</th>
                            <th scope="col" class="px-4 py-3 text-center">Nilai KKM</th>
                            <th scope="col" class="px-4 py-3 text-center">Status KKM</th>
                            <th scope="col" class="px-4 py-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mapel-table-body" class="divide-y divide-default">
                        @forelse($mapelSettings as $index => $item)
                            <tr class="hover:bg-neutral-secondary-soft/50 dark:hover:bg-neutral-secondary-medium/20 transition-colors" id="mapel-row-{{ $item->id }}">
                                <td class="px-4 py-3 text-center font-bold text-heading">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-heading">{{ $item->mapel }}</td>
                                <td class="px-4 py-3 text-body">{{ $item->guru }}</td>
                                <td class="px-4 py-3 text-center font-extrabold text-heading text-sm">{{ $item->kkm }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->kkm >= 80)
                                        <span class="bg-success-soft text-fg-success-strong text-xs font-medium px-1.5 py-0.5 rounded">Sangat Baik (≥80)</span>
                                    @elseif($item->kkm >= 75)
                                        <span class="bg-brand-softer text-fg-brand-strong text-xs font-medium px-1.5 py-0.5 rounded">Standar (75-79)</span>
                                    @else
                                        <span class="bg-warning-soft text-fg-warning text-xs font-medium px-1.5 py-0.5 rounded">Perhatian (&lt;75)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="editMapel({{ $item->id }}, '{{ addslashes($item->mapel) }}', '{{ addslashes($item->guru) }}', {{ $item->kkm }}, {{ $item->settingsWaliKelas_id }})" class="p-1.5 text-body hover:text-fg-brand hover:bg-neutral-tertiary rounded-base transition-colors" title="Edit Mapel">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="confirmDeleteMapel({{ $item->id }}, '{{ addslashes($item->mapel) }}')" class="p-1.5 text-body hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-base transition-colors" title="Hapus Mapel">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-mapel-row">
                                <td colspan="6" class="px-4 py-8 text-center text-body">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <svg class="w-10 h-10 text-body/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.03v13m0-13c-2.819-.831-7.5-3-7.5-3v13.75c0 .104.058.2.15.25 1.125.625 5.432 2.378 7.35 3M12 6.03c2.819-.831 7.5-3 7.5-3v13.75c0 .104-.058.2-.15.25-1.125.625-5.432 2.378-7.35 3M12 19.03V20"/>
                                        </svg>
                                        <p class="font-medium text-xs">Belum ada mata pelajaran terdaftar.</p>
                                        <button type="button" data-modal-target="modal-tambah-mapel" data-modal-toggle="modal-tambah-mapel" class="text-xs text-fg-brand font-bold hover:underline cursor-pointer">
                                            + Tambah Mata Pelajaran Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- FLOWBITE MODAL: KELOLA TAHUN AJARAN -->
<div id="modal-tambah-tahun-ajaran" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-slate-900/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-default rounded-t">
                <h3 class="text-sm font-bold text-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Kelola Tahun Ajaran
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer" data-modal-hide="modal-tambah-tahun-ajaran">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Tutup modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-4 space-y-5">
                <!-- Form Tambah / Edit Tahun Ajaran -->
                <form id="form-tambah-tahun-ajaran" onsubmit="submitTambahTahunAjaran(event)" class="p-3 bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default rounded-base space-y-3">
                    @csrf
                    <input type="hidden" id="year_id_input" name="id" value="">
                    
                    <div class="flex items-center justify-between border-b border-default pb-2">
                        <h4 id="form-tahun-ajaran-title" class="text-xs font-bold text-heading">Tambah Tahun Ajaran Baru</h4>
                        <button type="button" id="btn-reset-form-year" onclick="resetYearForm()" class="hidden text-[10px] font-bold text-fg-brand hover:underline cursor-pointer">+ Form Baru</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="add_year" class="block mb-1 text-xs font-semibold text-heading">Tahun Ajaran <span class="text-red-500">*</span></label>
                            <input type="text" id="add_year" name="year" required class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2" placeholder="Contoh: 2025/2026">
                        </div>

                        <div>
                            <label for="add_semester" class="block mb-1 text-xs font-semibold text-heading">Semester <span class="text-red-500">*</span></label>
                            <select id="add_semester" name="semester" required class="bg-white dark:bg-neutral-primary-soft border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2">
                                <option value="GANJIL">GANJIL</option>
                                <option value="GENAP">GENAP</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center">
                            <input id="add_is_active" name="is_active" type="checkbox" value="1" class="w-4 h-4 text-brand bg-white border-default rounded-xs focus:ring-brand focus:ring-2">
                            <label for="add_is_active" class="ms-2 text-xs font-medium text-heading">Aktifkan Tahun Ini</label>
                        </div>
                        <button type="submit" id="btn-submit-year" class="py-1.5 px-3 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base focus:ring-4 focus:ring-brand-medium cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>

                <!-- List / Table Tahun Ajaran yang Sudah Ada -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-body/70 mb-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Daftar Tahun Ajaran Terdaftar
                    </h4>

                    <div class="relative overflow-x-auto border border-default rounded-base max-h-48 overflow-y-auto">
                        <table class="w-full text-xs text-left text-body">
                            <thead class="text-[10px] font-bold text-heading uppercase bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/60 border-b border-default sticky top-0">
                                <tr>
                                    <th scope="col" class="px-3 py-2">Tahun Ajaran</th>
                                    <th scope="col" class="px-3 py-2 text-center">Semester</th>
                                    <th scope="col" class="px-3 py-2 text-center">Status</th>
                                    <th scope="col" class="px-3 py-2 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="year-table-body" class="divide-y divide-default">
                                @forelse($academicYears as $yearItem)
                                    <tr class="hover:bg-neutral-secondary-soft/50 transition-colors" id="year-row-{{ $yearItem->id }}">
                                        <td class="px-3 py-2 font-bold text-heading">{{ $yearItem->year }}</td>
                                        <td class="px-3 py-2 text-center">{{ $yearItem->semester }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($yearItem->is_active)
                                                <span class="bg-success-soft text-fg-success-strong text-xs font-medium px-1.5 py-0.5 rounded">Aktif</span>
                                            @else
                                                <span class="bg-neutral-secondary-medium text-heading text-xs font-medium px-1.5 py-0.5 rounded">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" onclick="editYear({{ $yearItem->id }}, '{{ addslashes($yearItem->year) }}', '{{ $yearItem->semester }}', {{ $yearItem->is_active ? 'true' : 'false' }})" class="p-1 text-body hover:text-fg-brand hover:bg-neutral-tertiary rounded-base transition-colors" title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button type="button" onclick="confirmDeleteYear({{ $yearItem->id }}, '{{ addslashes($yearItem->year) }}')" class="p-1 text-body hover:text-red-600 hover:bg-red-50 rounded-base transition-colors" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-body text-xs">Belum ada data tahun ajaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="flex items-center justify-end p-3 border-t border-default rounded-b">
                <button data-modal-hide="modal-tambah-tahun-ajaran" type="button" class="py-2 px-4 text-xs font-medium text-body bg-neutral-secondary-soft hover:bg-neutral-tertiary rounded-base border border-default cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- FLOWBITE MODAL: TAMBAH MATA PELAJARAN -->
<div id="modal-tambah-mapel" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-slate-900/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b border-default rounded-t">
                <h3 class="text-sm font-bold text-heading">
                    Tambah Mata Pelajaran Baru
                </h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer" data-modal-hide="modal-tambah-mapel">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Tutup modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form id="form-tambah-mapel" onsubmit="submitTambahMapel(event)" class="p-4 space-y-4">
                @csrf
                <input type="hidden" name="settingsWaliKelas_id" value="{{ $settingsWaliKelas?->id ?? 1 }}">

                <div>
                    <label for="add_mapel" class="block mb-1 text-xs font-semibold text-heading">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                    <input type="text" id="add_mapel" name="mapel" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Contoh: Matematika Wajib">
                </div>

                <div>
                    <label for="add_guru" class="block mb-1 text-xs font-semibold text-heading">Guru Pengampu <span class="text-red-500">*</span></label>
                    <input type="text" id="add_guru" name="guru" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5" placeholder="Contoh: Dra. Sri Wahyuni, M.Pd">
                </div>

                <div>
                    <label for="add_kkm" class="block mb-1 text-xs font-semibold text-heading">Nilai KKM (0-100) <span class="text-red-500">*</span></label>
                    <input type="number" id="add_kkm" name="kkm" min="0" max="100" value="75" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end space-x-2 border-t border-default pt-3">
                    <button data-modal-hide="modal-tambah-mapel" type="button" class="py-2 px-3 text-xs font-medium text-body bg-neutral-secondary-soft hover:bg-neutral-tertiary rounded-base border border-default cursor-pointer">Batal</button>
                    <button type="submit" class="py-2 px-4 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base focus:ring-4 focus:ring-brand-medium cursor-pointer">Simpan Mapel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FLOWBITE MODAL: EDIT MATA PELAJARAN -->
<div id="modal-edit-mapel" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-slate-900/50 backdrop-blur-xs">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-base shadow-lg dark:bg-neutral-primary-soft border border-default">
            <div class="flex items-center justify-between p-4 border-b border-default rounded-t">
                <h3 class="text-sm font-bold text-heading">Edit Mata Pelajaran</h3>
                <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-8 h-8 ms-auto inline-flex justify-center items-center cursor-pointer" onclick="closeEditModal()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            <form id="form-edit-mapel" onsubmit="submitEditMapel(event)" class="p-4 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_settingsWaliKelas_id" name="settingsWaliKelas_id">

                <div>
                    <label for="edit_mapel" class="block mb-1 text-xs font-semibold text-heading">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_mapel" name="mapel" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                </div>

                <div>
                    <label for="edit_guru" class="block mb-1 text-xs font-semibold text-heading">Guru Pengampu <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_guru" name="guru" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                </div>

                <div>
                    <label for="edit_kkm" class="block mb-1 text-xs font-semibold text-heading">Nilai KKM (0-100) <span class="text-red-500">*</span></label>
                    <input type="number" id="edit_kkm" name="kkm" min="0" max="100" required class="bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/50 border border-default text-heading text-xs rounded-base focus:ring-brand focus:border-brand block w-full p-2.5">
                </div>

                <div class="flex items-center justify-end space-x-2 border-t border-default pt-3">
                    <button type="button" onclick="closeEditModal()" class="py-2 px-3 text-xs font-medium text-body bg-neutral-secondary-soft hover:bg-neutral-tertiary rounded-base border border-default cursor-pointer">Batal</button>
                    <button type="submit" class="py-2 px-4 text-xs font-bold text-white bg-brand hover:bg-brand-strong rounded-base focus:ring-4 focus:ring-brand-medium cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT UNTUK AJAX MAPEL SETTINGS & INTERAKTIVITAS -->
<script>
    const baseUrlMapel = "{{ url('wali-kelas/mapel-settings') }}";
    const csrfToken = "{{ csrf_token() }}";

    function triggerReloadWithAlert(message, type = 'success') {
        sessionStorage.setItem('reload_alert_msg', message);
        sessionStorage.setItem('reload_alert_type', type);
        window.location.reload();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const alertMsg = sessionStorage.getItem('reload_alert_msg');
        const alertType = sessionStorage.getItem('reload_alert_type') || 'success';
        if (alertMsg) {
            sessionStorage.removeItem('reload_alert_msg');
            sessionStorage.removeItem('reload_alert_type');

            const container = document.getElementById('page-alert-container');
            if (container) {
                let colorClass = 'text-fg-success-strong bg-success-soft border border-emerald-300/40 dark:bg-emerald-950/90 dark:text-emerald-300 dark:border-emerald-700/80 shadow-xs';
                let alertTitle = 'Success alert!';

                if (alertType === 'danger' || alertType === 'error') {
                    colorClass = 'text-fg-danger-strong bg-danger-soft border border-red-300/40 dark:bg-red-950/90 dark:text-red-300 dark:border-red-700/80 shadow-xs';
                    alertTitle = 'Danger alert!';
                } else if (alertType === 'warning') {
                    colorClass = 'text-fg-warning bg-warning-soft border border-amber-300/40 dark:bg-amber-950/90 dark:text-amber-300 dark:border-amber-700/80 shadow-xs';
                    alertTitle = 'Warning alert!';
                } else if (alertType === 'info') {
                    colorClass = 'text-fg-brand-strong bg-brand-softer border border-blue-300/40 dark:bg-blue-950/90 dark:text-blue-300 dark:border-blue-700/80 shadow-xs';
                    alertTitle = 'Info alert!';
                } else if (alertType === 'dark') {
                    colorClass = 'text-heading bg-neutral-secondary-medium border border-neutral-default dark:bg-neutral-secondary-medium dark:text-white shadow-xs';
                    alertTitle = 'Dark alert!';
                }

                const alertDiv = document.createElement('div');
                alertDiv.className = `flex items-start sm:items-center p-4 mb-4 text-sm ${colorClass} rounded-base w-full transition-all duration-500 opacity-100`;
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = `
                    <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p><span class="font-medium me-1">${alertTitle}</span> ${alertMsg}</p>
                `;
                container.appendChild(alertDiv);
            }
        }

        setTimeout(() => {
            const container = document.getElementById('page-alert-container');
            if (!container) return;
            const alerts = container.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.classList.remove('opacity-100');
                alert.classList.add('opacity-0');
            });
        }, 4000);

        setTimeout(() => {
            const container = document.getElementById('page-alert-container');
            if (!container) return;
            const alerts = container.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                if (alert.classList.contains('opacity-0')) {
                    alert.remove();
                }
            });
        }, 4600);
    });

    function handleLogoChange(event) {
        const file = event.target.files[0];
        const filenameSpan = document.getElementById('school_logo_filename');
        const previewContainer = document.getElementById('school_logo_preview_container');
        const previewImg = document.getElementById('school_logo_preview');

        if (file) {
            if (filenameSpan) filenameSpan.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImg) previewImg.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            if (filenameSpan) filenameSpan.textContent = 'Belum ada file dipilih';
        }
    }

    async function submitTambahMapel(e) {
        e.preventDefault();
        const form = document.getElementById('form-tambah-mapel');
        const formData = new FormData(form);

        try {
            const res = await fetch(baseUrlMapel, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            if (res.ok) {
                triggerReloadWithAlert('Mata pelajaran berhasil ditambahkan!', 'success');
            } else {
                const data = await res.json();
                alert(data.message || 'Gagal menambahkan mata pelajaran.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        }
    }

    function editMapel(id, mapel, guru, kkm, settingsWaliKelasId) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_mapel').value = mapel;
        document.getElementById('edit_guru').value = guru;
        document.getElementById('edit_kkm').value = kkm;
        document.getElementById('edit_settingsWaliKelas_id').value = settingsWaliKelasId;

        const modal = document.getElementById('modal-edit-mapel');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('modal-edit-mapel');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function submitEditMapel(e) {
        e.preventDefault();
        const id = document.getElementById('edit_id').value;
        const form = document.getElementById('form-edit-mapel');
        const formData = new FormData(form);
        const dataJson = {
            settingsWaliKelas_id: formData.get('settingsWaliKelas_id'),
            mapel: formData.get('mapel'),
            guru: formData.get('guru'),
            kkm: formData.get('kkm')
        };

        try {
            const res = await fetch(`${baseUrlMapel}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(dataJson)
            });

            if (res.ok) {
                closeEditModal();
                triggerReloadWithAlert('Mata pelajaran berhasil diperbarui!', 'success');
            } else {
                const errData = await res.json();
                alert(errData.message || 'Gagal memperbarui mata pelajaran.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan sistem.');
        }
    }

    const baseAcademicYearUrl = "{{ url('/academic-years') }}";

    function editYear(id, year, semester, isActive) {
        document.getElementById('year_id_input').value = id;
        document.getElementById('add_year').value = year;
        document.getElementById('add_semester').value = semester;
        document.getElementById('add_is_active').checked = !!isActive;

        document.getElementById('form-tahun-ajaran-title').innerText = 'Edit Tahun Ajaran';
        document.getElementById('btn-submit-year').innerText = 'Update';
        document.getElementById('btn-reset-form-year').classList.remove('hidden');
    }

    function resetYearForm() {
        document.getElementById('year_id_input').value = '';
        document.getElementById('form-tambah-tahun-ajaran').reset();
        document.getElementById('form-tahun-ajaran-title').innerText = 'Tambah Tahun Ajaran Baru';
        document.getElementById('btn-submit-year').innerText = 'Simpan';
        document.getElementById('btn-reset-form-year').classList.hidden = true;
    }

    async function submitTambahTahunAjaran(e) {
        e.preventDefault();
        const form = document.getElementById('form-tambah-tahun-ajaran');
        const id = document.getElementById('year_id_input').value;
        const formData = new FormData(form);
        
        let url = baseAcademicYearUrl;
        if (id) {
            url = `${baseAcademicYearUrl}/${id}`;
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            if (res.ok) {
                const closeBtn = document.querySelector('[data-modal-hide="modal-tambah-tahun-ajaran"]');
                if (closeBtn) {
                    closeBtn.click();
                }

                document.querySelectorAll('[modal-backdrop]').forEach(el => el.remove());
                triggerReloadWithAlert(id ? 'Tahun ajaran berhasil diperbarui!' : 'Tahun ajaran baru berhasil ditambahkan!', 'success');
            } else {
                const data = await res.json();
                alert(data.message || 'Gagal menyimpan tahun ajaran.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan sistem.');
        }
    }

    async function confirmDeleteYear(id, yearName) {
        if (!confirm(`Apakah Anda yakin ingin menghapus tahun ajaran "${yearName}"?`)) {
            return;
        }

        try {
            const res = await fetch(`${baseAcademicYearUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (res.ok) {
                triggerReloadWithAlert('Tahun ajaran berhasil dihapus!', 'success');
            } else {
                alert('Gagal menghapus tahun ajaran.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        }
    }

    async function confirmDeleteMapel(id, mapelName) {
        if (!confirm(`Apakah Anda yakin ingin menghapus mata pelajaran "${mapelName}"?`)) {
            return;
        }

        try {
            const res = await fetch(`${baseUrlMapel}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (res.ok) {
                triggerReloadWithAlert('Mata pelajaran berhasil dihapus!', 'success');
            } else {
                alert('Gagal menghapus mata pelajaran.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        }
    }
</script>
@endsection
