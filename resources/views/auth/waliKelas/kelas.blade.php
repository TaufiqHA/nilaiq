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
                    <select id="academic_year_id" name="academic_year_id" required
                        class="bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-2.5 transition-colors">
                        <option value="" disabled {{ !old('academic_year_id', $classWaliKelas->academic_year_id ?? null) ? 'selected' : '' }}>-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $classWaliKelas->academic_year_id ?? null) == $year->id ? 'selected' : '' }}>
                                {{ $year->year }} - Semester {{ $year->semester }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
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
</script>
@endsection
