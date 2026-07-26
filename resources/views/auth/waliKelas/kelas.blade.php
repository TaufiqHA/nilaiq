@extends('layouts.waliKelas')

@section('title', 'Informasi Kelas')

@section('content')
<!-- Container Utama Informasi Kelas -->
<div class="p-0 sm:p-6 border-0 sm:border border-default border-dashed rounded-none sm:rounded-base bg-transparent sm:bg-white/40 dark:sm:bg-neutral-secondary-medium/20 backdrop-blur-none sm:backdrop-blur-md space-y-4 sm:space-y-6 w-full">

    <!-- Header Section -->
    <div class="border-b border-default pb-4">
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
                            <span class="inline-flex items-center text-xs font-bold text-heading">Informasi Kelas</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Informasi Kelas</h1>
            <p class="text-xs text-body mt-0.5">Kelola nama kelas dan tahun ajaran untuk wali kelas saat ini.</p>
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
    </div>

    <!-- Main Content Layout (Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Card (2/3 width) -->
        <div class="lg:col-span-2 p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-default pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-brand-soft rounded-lg text-fg-brand">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-heading">Form Informasi Kelas</h2>
                        <p class="text-xs text-body">Isi atau perbarui informasi kelas yang Anda ampu.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('wali-kelas.class-wali-kelas.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <!-- Input Nama Kelas -->
                <div>
                    <label for="name" class="block mb-1.5 text-xs font-bold text-heading">Nama Kelas <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $classWaliKelas->name ?? '') }}" placeholder="Contoh: Kelas X MIPA 1" required
                        class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                    <p class="mt-1 text-[11px] text-body">Masukkan nama resmi kelas (contoh: Kelas X IPA 1, Kelas XII IPS 2).</p>
                </div>

                <!-- Select Tahun Ajaran -->
                <div>
                    <label for="academic_year_id" class="block mb-1.5 text-xs font-bold text-heading">Tahun Ajaran <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <select id="academic_year_id" name="academic_year_id" required
                            class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                            <option value="" disabled {{ !old('academic_year_id', $classWaliKelas->academic_year_id ?? null) ? 'selected' : '' }}>-- Pilih Tahun Ajaran --</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $classWaliKelas->academic_year_id ?? null) == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }} - Semester {{ $year->semester }} {{ $year->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openAcademicYearModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-heading bg-neutral-secondary-soft hover:bg-neutral-tertiary border border-default rounded-base shadow-xs transition-colors shrink-0 cursor-pointer">
                            <svg class="w-4 h-4 text-body" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Kelola</span>
                        </button>
                    </div>
                    <p class="mt-1 text-[11px] text-body">Pilih tahun ajaran aktif yang berlaku untuk kelas ini.</p>
                </div>

                <!-- Readonly Wali Kelas -->
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-heading">Wali Kelas</label>
                    <div class="flex items-center gap-3 p-2.5 bg-neutral-secondary-medium/60 border border-default rounded-base text-sm text-heading">
                        <div class="h-7 w-7 rounded-full bg-brand flex items-center justify-center font-bold text-white text-xs shrink-0">
                            {{ substr(auth()->user()->name ?? 'WK', 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold truncate text-xs">{{ auth()->user()->name ?? 'Wali Kelas' }}</p>
                            <p class="text-[11px] text-body truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-3 border-t border-default flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand/30 font-bold rounded-base text-xs px-5 py-2.5 transition-all duration-200 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Informasi Kelas
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Summary Card (1/3 width) -->
        <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between border-b border-default pb-3 mb-4">
                    <h3 class="text-sm font-bold text-heading">Ringkasan Kelas</h3>
                    @if($classWaliKelas)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Terdaftar</span>
                    @else
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Belum Diatur</span>
                    @endif
                </div>

                @if($classWaliKelas)
                    <div class="space-y-3">
                        <div class="p-3 bg-neutral-secondary-medium/40 border border-default rounded-base">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-body block mb-0.5">Nama Kelas</span>
                            <span class="text-base font-extrabold text-heading block">{{ $classWaliKelas->name }}</span>
                        </div>

                        <div class="p-3 bg-neutral-secondary-medium/40 border border-default rounded-base">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-body block mb-0.5">Tahun Ajaran</span>
                            <span class="text-sm font-bold text-heading block">
                                {{ $classWaliKelas->academicYear->year ?? '-' }} (Semester {{ $classWaliKelas->academicYear->semester ?? '-' }})
                            </span>
                        </div>

                        <div class="p-3 bg-neutral-secondary-medium/40 border border-default rounded-base">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-body block mb-0.5">Wali Kelas</span>
                            <span class="text-sm font-bold text-heading block">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center border border-dashed border-default rounded-base bg-neutral-secondary-medium/20">
                        <svg class="w-8 h-8 text-body/40 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs font-bold text-heading mb-1">Belum Ada Kelas</p>
                        <p class="text-[11px] text-body">Silakan lengkapi form di samping untuk mendaftarkan informasi kelas Anda.</p>
                    </div>
                @endif
            </div>

            <div class="p-3 rounded-base bg-brand-softer border border-brand/20 text-xs text-fg-brand space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Wali Kelas
                </div>
                <p class="text-[11px] text-body">1 Wali Kelas mengampu 1 kelas. Semua data siswa, presensi, dan nilai akan terhubung secara otomatis dengan informasi kelas di atas.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola Tahun Ajaran -->
<div id="academic-year-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4">
    <div class="relative w-full max-w-4xl bg-white dark:bg-neutral-primary-soft rounded-base shadow-lg border border-default p-6 flex flex-col md:flex-row gap-6 max-h-[90vh] overflow-y-auto">
        <!-- Close Button -->
        <button type="button" onclick="closeAcademicYearModal()" class="absolute top-4 right-4 text-body hover:text-heading p-1.5 rounded-base hover:bg-neutral-tertiary transition-colors cursor-pointer" aria-label="Close">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Left Column: Form -->
        <div class="w-full md:w-1/3 border-b md:border-b-0 md:border-e border-default pb-6 md:pb-0 md:pe-6 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-heading mb-4" id="modal-form-title">Tambah Tahun Ajaran</h3>
                
                <form id="modal-academic-year-form" onsubmit="saveAcademicYear(event)" class="space-y-4">
                    <input type="hidden" id="modal-academic-year-id" value="">
                    
                    <!-- Year Input -->
                    <div>
                        <label for="modal-year" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                        <input type="text" id="modal-year" required
                               class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand" 
                               placeholder="contoh: 2025/2026">
                        <span class="text-[10px] text-body mt-1 block">Format: YYYY/YYYY (misal: 2025/2026)</span>
                    </div>

                    <!-- Semester Select -->
                    <div>
                        <label for="modal-semester" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Semester</label>
                        <select id="modal-semester" required
                                class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
                            <option value="GANJIL">GANJIL</option>
                            <option value="GENAP">GENAP</option>
                        </select>
                    </div>

                    <!-- Status Checkbox -->
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="modal-is-active" value="1"
                               class="w-4 h-4 rounded text-brand focus:ring-brand bg-neutral-secondary-medium border-default">
                        <label for="modal-is-active" class="text-sm font-medium text-heading">Setel sebagai Aktif</label>
                    </div>
                </form>
            </div>

            <!-- Form Action Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-default pt-4 mt-6">
                <button type="button" id="modal-btn-cancel" onclick="resetModalForm()" class="hidden px-4 py-2 rounded-base text-xs font-bold border border-default hover:bg-neutral-tertiary text-body transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="modal-academic-year-form" class="bg-brand hover:bg-brand-strong text-white px-5 py-2 rounded-base text-xs font-bold shadow-md shadow-brand/10 transition-colors cursor-pointer">
                    Simpan
                </button>
            </div>
        </div>

        <!-- Right Column: List -->
        <div class="w-full md:w-2/3 flex flex-col">
            <h3 class="text-base font-bold text-heading mb-4">Daftar Tahun Ajaran Anda</h3>
            
            <div class="overflow-x-auto border border-default rounded-base max-h-[320px]">
                <table class="w-full text-xs text-left text-body">
                    <thead class="bg-neutral-secondary-soft text-[10px] uppercase font-extrabold tracking-wider text-heading border-b border-default">
                        <tr>
                            <th class="px-4 py-3">Tahun Ajaran</th>
                            <th class="px-4 py-3">Semester</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="modal-academic-years-table-body" class="divide-y divide-default">
                        <!-- Loaded dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function dismissAlert(alertElement) {
        if (!alertElement) return;
        alertElement.style.opacity = '0';
        alertElement.style.transform = 'translateY(-8px)';
        setTimeout(() => {
            alertElement.remove();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('page-alert-container');
        if (container) {
            const alerts = container.querySelectorAll('[role="alert"]');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    dismissAlert(alert);
                }, 4000);
            });
        }
    });

    // Modal Academic Year Functions
    function openAcademicYearModal() {
        document.getElementById('academic-year-modal').classList.remove('hidden');
        resetModalForm();
        loadAcademicYears();
    }

    function closeAcademicYearModal() {
        document.getElementById('academic-year-modal').classList.add('hidden');
    }

    function resetModalForm() {
        document.getElementById('modal-form-title').innerText = 'Tambah Tahun Ajaran';
        document.getElementById('modal-academic-year-id').value = '';
        document.getElementById('modal-year').value = '';
        document.getElementById('modal-semester').value = 'GANJIL';
        document.getElementById('modal-is-active').checked = false;
        document.getElementById('modal-btn-cancel').classList.add('hidden');
    }

    async function loadAcademicYears() {
        try {
            const response = await fetch('/academic-years', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Gagal memuat data');
            const data = await response.json();
            
            const tbody = document.getElementById('modal-academic-years-table-body');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-body/60">Belum ada tahun ajaran. Silakan tambahkan baru.</td>
                    </tr>
                `;
                return;
            }
            
            data.forEach(year => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-neutral-secondary-soft transition-colors duration-150 border-b border-default';
                
                const badgeClass = year.is_active 
                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' 
                    : 'bg-neutral-secondary-medium text-body border border-default';
                const badgeText = year.is_active ? 'Aktif' : 'Tidak Aktif';
                
                // Escape string values for JS attribute safety
                const escapedYear = year.year.replace(/'/g, "\\'");
                const escapedSemester = year.semester.replace(/'/g, "\\'");
                
                tr.innerHTML = `
                    <td class="px-4 py-3 font-semibold text-heading">${year.year}</td>
                    <td class="px-4 py-3">${year.semester}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">
                            ${badgeText}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                        <button type="button" onclick="editAcademicYear(${year.id}, '${escapedYear}', '${escapedSemester}', ${year.is_active})" class="text-brand hover:text-brand-strong font-bold text-xs p-1 rounded transition-colors cursor-pointer">
                            Edit
                        </button>
                        <button type="button" onclick="deleteAcademicYear(${year.id})" class="text-red-600 hover:text-red-800 font-bold text-xs p-1 rounded transition-colors cursor-pointer">
                            Hapus
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat memuat tahun ajaran.');
        }
    }

    function editAcademicYear(id, year, semester, isActive) {
        document.getElementById('modal-form-title').innerText = 'Edit Tahun Ajaran';
        document.getElementById('modal-academic-year-id').value = id;
        document.getElementById('modal-year').value = year;
        document.getElementById('modal-semester').value = semester;
        document.getElementById('modal-is-active').checked = !!isActive;
        document.getElementById('modal-btn-cancel').classList.remove('hidden');
    }

    async function saveAcademicYear(event) {
        event.preventDefault();
        
        const id = document.getElementById('modal-academic-year-id').value;
        const year = document.getElementById('modal-year').value;
        const semester = document.getElementById('modal-semester').value;
        const isActive = document.getElementById('modal-is-active').checked ? 1 : 0;
        
        const url = id ? `/academic-years/${id}` : '/academic-years';
        const method = id ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ year, semester, is_active: isActive })
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                if (result.errors) {
                    const message = Object.values(result.errors).flat().join('\n');
                    alert(message);
                } else {
                    throw new Error(result.message || 'Gagal menyimpan data');
                }
                return;
            }
            
            resetModalForm();
            loadAcademicYears();
            await refreshParentSelect();
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menyimpan data.');
        }
    }

    async function deleteAcademicYear(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')) return;
        
        try {
            const response = await fetch(`/academic-years/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Gagal menghapus data');
            
            resetModalForm();
            loadAcademicYears();
            await refreshParentSelect();
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menghapus data.');
        }
    }

    async function refreshParentSelect() {
        const select = document.getElementById('academic_year_id');
        if (!select) return;
        
        const currentVal = select.value;
        
        try {
            const response = await fetch('/academic-years', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('Gagal me-refresh select');
            const data = await response.json();
            
            select.innerHTML = '<option value="" disabled>-- Pilih Tahun Ajaran --</option>';
            
            data.forEach(year => {
                const opt = document.createElement('option');
                opt.value = year.id;
                opt.text = `${year.year} - Semester ${year.semester} ${year.is_active ? '(Aktif)' : ''}`;
                select.appendChild(opt);
            });
            
            // Restore selection if option still exists
            if (currentVal && [...select.options].some(o => o.value == currentVal)) {
                select.value = currentVal;
            } else {
                select.value = '';
            }
        } catch (e) {
            console.error(e);
        }
    }
</script>
@endsection
