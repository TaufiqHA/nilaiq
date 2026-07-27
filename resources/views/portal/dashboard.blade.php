<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Orang Tua - {{ $student->name }}</title>
    
    <!-- Inline script to prevent FOUC (flash of incorrect theme) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Meta tags for SEO -->
    <meta name="description" content="Dashboard perkembangan belajar siswa NilaiQ.">
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom scrollbar hiding style for mobile tabs -->
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium min-h-screen relative overflow-x-hidden text-heading">
    <!-- Ambient background glows -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand opacity-[0.08] dark:opacity-[0.12] rounded-full blur-3xl -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-brand-strong opacity-[0.08] dark:opacity-[0.12] rounded-full blur-3xl -z-10 pointer-events-none"></div>

    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white/70 dark:bg-neutral-secondary-medium/40 border border-default-medium/60 backdrop-blur-md rounded-base p-4 sm:p-6 shadow-lg mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative overflow-hidden">
            <!-- Glow decorative -->
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-brand/5 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="flex items-center gap-3 sm:gap-4 relative z-10">
                <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-2xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 sm:w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-heading">{{ $student->name }}</h1>
                    <p class="text-xs sm:text-sm text-body">
                        NIS: <span class="font-semibold text-heading">{{ $student->nis }}</span> &bull; 
                        NISN: <span class="font-semibold text-heading">{{ $student->nisn }}</span>
                    </p>
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold bg-brand/15 text-brand">
                            Kelas: {{ $studentType === 'wali_kelas' ? ($student->classWaliKelas?->name ?? '-') : ($student->class?->name ?? '-') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold bg-neutral-secondary-medium dark:bg-neutral-tertiary border border-default text-heading">
                            TA: {{ $studentType === 'wali_kelas' ? ($student->classWaliKelas?->academicYear?->year ?? '-') : ($student->class?->academicYear?->year ?? '-') }} ({{ $studentType === 'wali_kelas' ? ($student->classWaliKelas?->academicYear?->semester ?? '-') : ($student->class?->academicYear?->semester ?? '-') }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Exit Button -->
            <div class="relative z-10 shrink-0">
                <form action="{{ route('portal-ortu.exit') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari Portal Orang Tua?')">
                    @csrf
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900 hover:bg-red-100 dark:hover:bg-red-950/40 rounded-base text-sm font-bold shadow-xs cursor-pointer transition-colors duration-200">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar Portal
                    </button>
                </form>
            </div>
        </div>

        <!-- Session Alert Success -->
        @if(session('success'))
            <div id="alert-success" class="flex items-center p-4 mb-6 text-emerald-800 border border-emerald-300 rounded-lg bg-emerald-50 dark:bg-neutral-primary-soft dark:text-emerald-400 dark:border-emerald-800" role="alert">
                <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Tab Controls -->
        <div class="border-b border-default mb-8 overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
            <ul class="flex min-w-max -mb-px text-sm font-bold text-center" id="portal-tabs" role="tablist">
                <li class="mr-1 sm:mr-2" role="presentation">
                    <button class="tab-btn inline-block px-3 py-4 sm:p-4 border-b-2 border-brand text-brand rounded-t-lg transition-all cursor-pointer whitespace-nowrap" 
                        id="profil-siswa-btn" onclick="switchTab('profil-siswa')" type="button">
                        Profil Siswa
                    </button>
                </li>
                <li class="mr-1 sm:mr-2" role="presentation">
                    <button class="tab-btn inline-block px-3 py-4 sm:p-4 border-b-2 border-transparent text-body hover:text-heading hover:border-default-medium rounded-t-lg transition-all cursor-pointer whitespace-nowrap" 
                        id="nilai-akademik-btn" onclick="switchTab('nilai-akademik')" type="button">
                        Informasi Nilai
                    </button>
                </li>
                <li role="presentation">
                    <button class="tab-btn inline-block px-3 py-4 sm:p-4 border-b-2 border-transparent text-body hover:text-heading hover:border-default-medium rounded-t-lg transition-all cursor-pointer whitespace-nowrap" 
                        id="kehadiran-btn" onclick="switchTab('kehadiran')" type="button">
                        Informasi Absensi
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Contents -->
        <div id="tab-contents">
            <!-- TAB 1: PROFIL SISWA -->
            <div class="tab-pane space-y-6" id="profil-siswa">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data Diri Card -->
                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                        <h3 class="text-lg font-bold mb-4 text-heading border-b border-default pb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M21 12h-4" />
                            </svg>
                            Identitas Diri
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-xs text-body uppercase font-bold">Nama Lengkap</dt>
                                <dd class="mt-1 font-semibold text-heading">{{ $student->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body uppercase font-bold">Jenis Kelamin</dt>
                                <dd class="mt-1 font-semibold text-heading">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body uppercase font-bold">Tempat & Tanggal Lahir</dt>
                                <dd class="mt-1 font-semibold text-heading">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? $student->birth_date->isoFormat('D MMMM YYYY') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body uppercase font-bold">Agama</dt>
                                <dd class="mt-1 font-semibold text-heading">{{ $student->religion ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs text-body uppercase font-bold">Alamat Siswa</dt>
                                <dd class="mt-1 font-semibold text-heading">{{ $student->address ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body uppercase font-bold">Sekolah Asal</dt>
                                <dd class="mt-1 font-semibold text-heading">{{ $student->previous_school ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body uppercase font-bold">Diterima di Kelas & Tanggal</dt>
                                <dd class="mt-1 font-semibold text-heading">
                                    Kelas {{ $student->accepted_class ?? '-' }} 
                                    @if($student->accepted_date)
                                        &bull; {{ $student->accepted_date->isoFormat('D MMMM YYYY') }}
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Orang Tua / Wali Card -->
                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                        <h3 class="text-lg font-bold mb-4 text-heading border-b border-default pb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Orang Tua & Keluarga
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            @if($studentType === 'wali_kelas')
                                <div>
                                    <dt class="text-xs text-body uppercase font-bold">Nama Ayah</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->father_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-body uppercase font-bold">Pekerjaan Ayah</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->father_job ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-body uppercase font-bold">Nama Ibu</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->mother_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-body uppercase font-bold">Pekerjaan Ibu</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->mother_job ?? '-' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs text-body uppercase font-bold">Alamat Orang Tua</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->parent_address ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-body uppercase font-bold">No. Telp Orang Tua</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->parent_phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-body uppercase font-bold">Status Hubungan Anak</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->family_status ?? '-' }} (Anak Ke-{{ $student->child_order ?? '-' }})</dd>
                                </div>
                                
                                @if($student->guardian_name)
                                    <div class="border-t border-default sm:col-span-2 pt-3 mt-1">
                                        <h4 class="font-bold text-heading text-xs uppercase mb-2">Informasi Wali</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <dt class="text-xs text-body uppercase font-bold">Nama Wali</dt>
                                                <dd class="mt-1 font-semibold text-heading">{{ $student->guardian_name }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs text-body uppercase font-bold">Pekerjaan Wali</dt>
                                                <dd class="mt-1 font-semibold text-heading">{{ $student->guardian_job ?? '-' }}</dd>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <dt class="text-xs text-body uppercase font-bold">Alamat Wali</dt>
                                                <dd class="mt-1 font-semibold text-heading">{{ $student->guardian_address ?? '-' }}</dd>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="sm:col-span-2">
                                    <dt class="text-xs text-body uppercase font-bold">Nama Orang Tua / Wali</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->parent_name ?? '-' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs text-body uppercase font-bold">No. Telp Orang Tua / Wali</dt>
                                    <dd class="mt-1 font-semibold text-heading">{{ $student->parent_phone ?? '-' }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- TAB 2: INFORMASI NILAI -->
            <div class="tab-pane space-y-6 hidden" id="nilai-akademik">
                @if($studentType === 'wali_kelas')
                    <!-- Academic Stats Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
                            <div class="h-12 w-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-body uppercase">Peringkat Kelas</p>
                                <h4 class="text-2xl font-black text-heading mt-0.5">
                                    {{ $studentRank }} <span class="text-sm font-semibold text-body">dari {{ $totalSiswa }} Siswa</span>
                                </h4>
                            </div>
                        </div>
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
                            <div class="h-12 w-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-body uppercase">Total Nilai Raport</p>
                                <h4 class="text-2xl font-black text-heading mt-0.5">{{ $studentTotalScore }}</h4>
                            </div>
                        </div>
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
                            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-body uppercase">Rata-Rata Nilai</p>
                                <h4 class="text-2xl font-black text-heading mt-0.5">
                                    {{ $student->nilaiMapels->count() > 0 ? round($student->nilaiMapels->avg('nilai'), 1) : 0 }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Nilai Mapel Table -->
                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-default">
                            <h3 class="text-lg font-bold text-heading">Daftar Nilai Hasil Belajar</h3>
                        </div>
                        
                        <!-- Desktop Table Layout -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-neutral-secondary-medium dark:bg-neutral-tertiary border-b border-default text-heading font-bold">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 w-16 text-center">No</th>
                                        <th scope="col" class="px-6 py-3">Mata Pelajaran</th>
                                        <th scope="col" class="px-6 py-3 w-24 text-center">KKM</th>
                                        <th scope="col" class="px-6 py-3 w-28 text-center">Nilai Akhir</th>
                                        <th scope="col" class="px-6 py-3">Capaian Kompetensi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-default">
                                    <!-- KELOMPOK A -->
                                    @if($kelompokA->isNotEmpty())
                                        <tr class="bg-neutral-secondary-medium/30">
                                            <td colspan="5" class="px-6 py-2.5 font-black text-xs text-heading uppercase bg-default/40">Kelompok A</td>
                                        </tr>
                                        @foreach($kelompokA as $idx => $nilaiMapel)
                                            <tr class="hover:bg-default/15 transition-colors">
                                                <td class="px-6 py-4 text-center text-body">{{ $idx + 1 }}</td>
                                                <td class="px-6 py-4 font-semibold text-heading">{{ $nilaiMapel->mapel?->mapel }}</td>
                                                <td class="px-6 py-4 text-center text-body">{{ $nilaiMapel->mapel?->kkm ?? '-' }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="inline-block px-2.5 py-1 rounded font-black text-sm {{ $nilaiMapel->nilai >= ($nilaiMapel->mapel?->kkm ?? 75) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' }}">
                                                        {{ $nilaiMapel->nilai }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-body leading-relaxed">{{ $nilaiMapel->capaian ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <!-- KELOMPOK B -->
                                    @if($kelompokB->isNotEmpty())
                                        <tr class="bg-neutral-secondary-medium/30">
                                            <td colspan="5" class="px-6 py-2.5 font-black text-xs text-heading uppercase bg-default/40">Kelompok B</td>
                                        </tr>
                                        @foreach($kelompokB as $idx => $nilaiMapel)
                                            <tr class="hover:bg-default/15 transition-colors">
                                                <td class="px-6 py-4 text-center text-body">{{ $idx + 1 }}</td>
                                                <td class="px-6 py-4 font-semibold text-heading">{{ $nilaiMapel->mapel?->mapel }}</td>
                                                <td class="px-6 py-4 text-center text-body">{{ $nilaiMapel->mapel?->kkm ?? '-' }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="inline-block px-2.5 py-1 rounded font-black text-sm {{ $nilaiMapel->nilai >= ($nilaiMapel->mapel?->kkm ?? 75) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' }}">
                                                        {{ $nilaiMapel->nilai }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-body leading-relaxed">{{ $nilaiMapel->capaian ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if($kelompokA->isEmpty() && $kelompokB->isEmpty())
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-body italic">
                                                Belum ada data nilai hasil belajar yang diinputkan oleh wali kelas.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List Layout -->
                        <div class="block md:hidden divide-y divide-default">
                            <!-- KELOMPOK A -->
                            @if($kelompokA->isNotEmpty())
                                <div class="px-4 py-2.5 font-black text-xs text-heading uppercase bg-default/40">
                                    Kelompok A
                                </div>
                                @foreach($kelompokA as $idx => $nilaiMapel)
                                    <div class="p-4 space-y-3 hover:bg-default/5 transition-colors">
                                        <div class="flex justify-between items-start gap-4">
                                            <div>
                                                <h4 class="font-bold text-heading text-sm">{{ $nilaiMapel->mapel?->mapel }}</h4>
                                                <p class="text-xs text-body mt-0.5">KKM: {{ $nilaiMapel->mapel?->kkm ?? '-' }}</p>
                                            </div>
                                            <span class="inline-block px-2.5 py-1 rounded font-black text-sm {{ $nilaiMapel->nilai >= ($nilaiMapel->mapel?->kkm ?? 75) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' }}">
                                                {{ $nilaiMapel->nilai }}
                                            </span>
                                        </div>
                                        @if($nilaiMapel->capaian)
                                            <div>
                                                <p class="text-[10px] font-bold text-body uppercase tracking-wider">Capaian Kompetensi</p>
                                                <p class="text-xs text-body leading-relaxed mt-1">{{ $nilaiMapel->capaian }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif

                            <!-- KELOMPOK B -->
                            @if($kelompokB->isNotEmpty())
                                <div class="px-4 py-2.5 font-black text-xs text-heading uppercase bg-default/40 border-t border-default">
                                    Kelompok B
                                </div>
                                @foreach($kelompokB as $idx => $nilaiMapel)
                                    <div class="p-4 space-y-3 hover:bg-default/5 transition-colors">
                                        <div class="flex justify-between items-start gap-4">
                                            <div>
                                                <h4 class="font-bold text-heading text-sm">{{ $nilaiMapel->mapel?->mapel }}</h4>
                                                <p class="text-xs text-body mt-0.5">KKM: {{ $nilaiMapel->mapel?->kkm ?? '-' }}</p>
                                            </div>
                                            <span class="inline-block px-2.5 py-1 rounded font-black text-sm {{ $nilaiMapel->nilai >= ($nilaiMapel->mapel?->kkm ?? 75) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' }}">
                                                {{ $nilaiMapel->nilai }}
                                            </span>
                                        </div>
                                        @if($nilaiMapel->capaian)
                                            <div>
                                                <p class="text-[10px] font-bold text-body uppercase tracking-wider">Capaian Kompetensi</p>
                                                <p class="text-xs text-body leading-relaxed mt-1">{{ $nilaiMapel->capaian }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif

                            @if($kelompokA->isEmpty() && $kelompokB->isEmpty())
                                <div class="px-4 py-8 text-center text-body italic text-sm">
                                    Belum ada data nilai hasil belajar yang diinputkan oleh wali kelas.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Extra, Prestasi, Sikap Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sikap (Profil Pelajar Pancasila) -->
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                            <h3 class="text-base font-bold mb-4 text-heading border-b border-default pb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Perkembangan Sikap (Karakter)
                            </h3>
                            @php $sikap = $student->sikap; @endphp
                            @if($sikap)
                                <div class="space-y-4 text-sm">
                                    <div>
                                        <h4 class="font-bold text-heading text-xs uppercase">Beriman, Bertakwa, & Berakhlak Mulia</h4>
                                        <p class="mt-1 text-body leading-relaxed">{{ trim($sikap->beriman_bertakwa_dan_berakhlak_mulia) !== '' ? $sikap->beriman_bertakwa_dan_berakhlak_mulia : '-' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-heading text-xs uppercase">Mandiri</h4>
                                        <p class="mt-1 text-body leading-relaxed">{{ trim($sikap->mandiri) !== '' ? $sikap->mandiri : '-' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-heading text-xs uppercase">Bergotong Royong</h4>
                                        <p class="mt-1 text-body leading-relaxed">{{ trim($sikap->bergotong_royong) !== '' ? $sikap->bergotong_royong : '-' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-heading text-xs uppercase">Kreatif</h4>
                                        <p class="mt-1 text-body leading-relaxed">{{ trim($sikap->kreatif) !== '' ? $sikap->kreatif : '-' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-heading text-xs uppercase">Bernalar Kritis</h4>
                                        <p class="mt-1 text-body leading-relaxed">{{ trim($sikap->bernalar_kritis) !== '' ? $sikap->bernalar_kritis : '-' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-heading text-xs uppercase">Berkebinekaan Global</h4>
                                        <p class="mt-1 text-body leading-relaxed">{{ trim($sikap->berkebinekaan_global) !== '' ? $sikap->berkebinekaan_global : '-' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-body italic text-center py-6">Data perkembangan sikap belum diisi.</p>
                            @endif
                        </div>

                        <!-- Ekskul & Prestasi -->
                        <div class="space-y-6">
                            <!-- Ekskul -->
                            <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                                <h3 class="text-base font-bold mb-4 text-heading border-b border-default pb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 002.828 0L21 9m-9 3v-2" />
                                    </svg>
                                    Ekstrakurikuler
                                </h3>
                                @php $ekskul = $student->ekskul; @endphp
                                @if($ekskul && ($ekskul->ekskul1 || $ekskul->ekskul2 || $ekskul->ekskul3))
                                    <ul class="list-disc pl-5 text-sm text-body space-y-2">
                                        @if($ekskul->ekskul1) <li>{{ $ekskul->ekskul1 }}</li> @endif
                                        @if($ekskul->ekskul2) <li>{{ $ekskul->ekskul2 }}</li> @endif
                                        @if($ekskul->ekskul3) <li>{{ $ekskul->ekskul3 }}</li> @endif
                                    </ul>
                                @else
                                    <p class="text-sm text-body italic text-center py-4">Tidak mengikuti atau data belum diisi.</p>
                                @endif
                            </div>

                            <!-- Prestasi -->
                            <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                                <h3 class="text-base font-bold mb-4 text-heading border-b border-default pb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Prestasi Siswa
                                </h3>
                                @php $prestasi = $student->prestasi; @endphp
                                @if($prestasi && ($prestasi->prestasi1 || $prestasi->prestasi2 || $prestasi->prestasi3))
                                    <div class="space-y-3 text-sm">
                                        @if($prestasi->prestasi1)
                                            <div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/20 dark:text-amber-400">Prestasi 1</span>
                                                <p class="mt-1 font-semibold text-heading">{{ $prestasi->catatan_prestasi1 ?? '-' }}</p>
                                            </div>
                                        @endif
                                        @if($prestasi->prestasi2)
                                            <div class="border-t border-default pt-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-800 dark:bg-slate-900/20 dark:text-slate-400">Prestasi 2</span>
                                                <p class="mt-1 font-semibold text-heading">{{ $prestasi->catatan_prestasi2 ?? '-' }}</p>
                                            </div>
                                        @endif
                                        @if($prestasi->prestasi3)
                                            <div class="border-t border-default pt-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-950/20 dark:text-orange-400">Prestasi 3</span>
                                                <p class="mt-1 font-semibold text-heading">{{ $prestasi->catatan_prestasi3 ?? '-' }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-sm text-body italic text-center py-4">Belum ada prestasi yang diinputkan.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Wali Kelas & Status Kelulusan -->
                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                        <h3 class="text-base font-bold mb-3 text-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Catatan Wali Kelas
                        </h3>
                        <p class="text-sm text-body bg-neutral-secondary-medium/40 dark:bg-neutral-tertiary/20 p-4 rounded-base border border-default-medium italic leading-relaxed">
                            "{{ $student->catatanWaliKelas?->catatan ?? 'Lebih giat dan rajin belajar lagi di rumah!' }}"
                        </p>

                        @if(strtoupper($student->classWaliKelas?->academicYear?->semester ?? 'GENAP') === 'GENAP')
                            <div class="mt-6 p-4 rounded-base border border-emerald-200 bg-emerald-50/50 dark:bg-neutral-primary-soft/20 dark:border-emerald-800 flex items-center gap-3">
                                <div class="h-9 w-9 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="text-sm text-emerald-800 dark:text-emerald-400 font-semibold">
                                    Telah menyelesaikan seluruh rangkaian pembelajaran dan dinyatakan <span class="font-black underline text-emerald-900 dark:text-emerald-300">LULUS / NAIK KELAS</span>.
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Guru Mapel Stats Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
                            <div class="h-12 w-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-body uppercase">Total Evaluasi</p>
                                <h4 class="text-2xl font-black text-heading mt-0.5">{{ $evaluations->count() }}</h4>
                            </div>
                        </div>
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
                            <div class="h-12 w-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-body uppercase">Rata-Rata Nilai</p>
                                <h4 class="text-2xl font-black text-heading mt-0.5">
                                    {{ $evaluations->count() > 0 ? round($evaluations->avg('score'), 1) : 0 }}
                                </h4>
                            </div>
                        </div>
                        <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm flex items-center gap-4 relative overflow-hidden">
                            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-body uppercase">Tingkat Ketuntasan</p>
                                <h4 class="text-2xl font-black text-heading mt-0.5">
                                    {{ $evaluations->count() > 0 ? round(($evaluations->where('score', '>=', $kkm)->count() / $evaluations->count()) * 100, 1) : 0 }}%
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Nilai Mapel Table (Guru Mapel Evaluasi) -->
                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-default flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <h3 class="text-lg font-bold text-heading">Riwayat Nilai Evaluasi - {{ $subjectName }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand/15 text-brand w-fit">
                                KKM: {{ $kkm }}
                            </span>
                        </div>
                        
                        <!-- Desktop Table Layout -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-neutral-secondary-medium dark:bg-neutral-tertiary border-b border-default text-heading font-bold">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 w-16 text-center">No</th>
                                        <th scope="col" class="px-6 py-3 w-36">Tipe Evaluasi</th>
                                        <th scope="col" class="px-6 py-3">Nama Evaluasi</th>
                                        <th scope="col" class="px-6 py-3 w-28 text-center">Nilai</th>
                                        <th scope="col" class="px-6 py-3 w-32 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-default">
                                    @forelse($evaluations as $idx => $eval)
                                        <tr class="hover:bg-default/15 transition-colors">
                                            <td class="px-6 py-4 text-center text-body">{{ $idx + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold 
                                                    @if($eval->type === 'Ulangan Harian') bg-blue-100 text-blue-800 dark:bg-blue-950/20 dark:text-blue-400
                                                    @elseif($eval->type === 'Tugas') bg-indigo-100 text-indigo-800 dark:bg-indigo-950/20 dark:text-indigo-400
                                                    @elseif($eval->type === 'UTS') bg-purple-100 text-purple-800 dark:bg-purple-950/20 dark:text-purple-400
                                                    @else bg-amber-100 text-amber-800 dark:bg-amber-950/20 dark:text-amber-400
                                                    @endif uppercase tracking-wider">
                                                    {{ $eval->type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-heading">
                                                {{ $eval->title }}
                                                @if($eval->date)
                                                    <span class="block text-[11px] text-body font-normal mt-0.5">{{ \Carbon\Carbon::parse($eval->date)->isoFormat('D MMMM YYYY') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-block px-2.5 py-1 rounded font-black text-sm {{ $eval->score >= $kkm ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' }}">
                                                    {{ $eval->score }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($eval->score >= $kkm)
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 justify-center">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Tuntas
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600 dark:text-red-400 justify-center">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                        Perlu Perbaikan
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-body italic">
                                                Belum ada data nilai hasil belajar yang diinputkan oleh guru mata pelajaran.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List Layout -->
                        <div class="block md:hidden divide-y divide-default">
                            @forelse($evaluations as $idx => $eval)
                                <div class="p-4 space-y-3 hover:bg-default/5 transition-colors">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold 
                                                @if($eval->type === 'Ulangan Harian') bg-blue-100 text-blue-800 dark:bg-blue-950/20 dark:text-blue-400
                                                @elseif($eval->type === 'Tugas') bg-indigo-100 text-indigo-800 dark:bg-indigo-950/20 dark:text-indigo-400
                                                @elseif($eval->type === 'UTS') bg-purple-100 text-purple-800 dark:bg-purple-950/20 dark:text-purple-400
                                                @else bg-amber-100 text-amber-800 dark:bg-amber-950/20 dark:text-amber-400
                                                @endif uppercase tracking-wider">
                                                {{ $eval->type }}
                                            </span>
                                            <h4 class="font-bold text-heading text-sm">{{ $eval->title }}</h4>
                                            @if($eval->date)
                                                <p class="text-[10px] text-body">{{ \Carbon\Carbon::parse($eval->date)->isoFormat('D MMMM YYYY') }}</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                                            <span class="inline-block px-2.5 py-1 rounded font-black text-sm {{ $eval->score >= $kkm ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' }}">
                                                {{ $eval->score }}
                                            </span>
                                            
                                            @if($eval->score >= $kkm)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Tuntas
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 dark:text-red-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Perbaikan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-body italic text-sm">
                                    Belum ada data nilai hasil belajar yang diinputkan oleh guru mata pelajaran.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            <!-- TAB 3: INFORMASI ABSENSI -->
            <div class="tab-pane space-y-6 hidden" id="kehadiran">
                @php 
                    $abs = $studentType === 'wali_kelas' ? $student->absensi : $absensi;
                    $sakit = $abs?->sakit ?? 0;
                    $izin = $abs?->izin ?? 0;
                    $alpa = $abs?->alpa ?? 0;
                    $hadir = $abs?->hadir ?? 0;
                    $totalHari = $hadir + $sakit + $izin + $alpa;
                    $persenHadir = $totalHari > 0 ? round(($hadir / $totalHari) * 100, 1) : 0;
                @endphp
                
                <!-- Absensi Grid Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm text-center">
                        <div class="mx-auto h-10 w-10 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-body uppercase tracking-wider">Hadir</h4>
                        <p class="text-3xl font-black text-heading mt-1">{{ $hadir }} <span class="text-xs font-semibold text-body">Hari</span></p>
                    </div>

                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm text-center">
                        <div class="mx-auto h-10 w-10 bg-blue-500/10 text-blue-500 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-body uppercase tracking-wider">Izin</h4>
                        <p class="text-3xl font-black text-heading mt-1">{{ $izin }} <span class="text-xs font-semibold text-body">Hari</span></p>
                    </div>

                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm text-center">
                        <div class="mx-auto h-10 w-10 bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-body uppercase tracking-wider">Sakit</h4>
                        <p class="text-3xl font-black text-heading mt-1">{{ $sakit }} <span class="text-xs font-semibold text-body">Hari</span></p>
                    </div>

                    <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm text-center">
                        <div class="mx-auto h-10 w-10 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-body uppercase tracking-wider">Alpa</h4>
                        <p class="text-3xl font-black text-heading mt-1">{{ $alpa }} <span class="text-xs font-semibold text-body">Hari</span></p>
                    </div>
                </div>

                <!-- Kehadiran Visual Progress Card -->
                <div class="bg-white/70 dark:bg-neutral-primary-soft/40 border border-default rounded-base p-4 sm:p-6 shadow-sm">
                    <h3 class="text-lg font-bold mb-4 text-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Persentase Kehadiran
                    </h3>
                    
                    <div class="flex flex-col md:flex-row items-center gap-8 py-4">
                        <!-- Progress Circle / Value representation -->
                        <div class="flex flex-col items-center justify-center bg-brand/5 rounded-full p-8 h-40 w-40 border border-brand/20 shrink-0">
                            <span class="text-3xl font-black text-brand">{{ $persenHadir }}%</span>
                            <span class="text-[10px] text-body uppercase font-bold tracking-widest mt-1">Kehadiran</span>
                        </div>

                        <!-- Progress Bar breakdown -->
                        <div class="w-full space-y-4">
                            <div>
                                <div class="flex justify-between text-xs font-bold text-heading mb-1.5">
                                    <span>Tingkat Kehadiran Aktif</span>
                                    <span>{{ $hadir }} dari {{ $totalHari }} Hari Aktif</span>
                                </div>
                                <div class="w-full bg-default dark:bg-neutral-tertiary rounded-full h-3">
                                    <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $persenHadir }}%"></div>
                                </div>
                            </div>
                            
                            <div class="text-sm text-body leading-relaxed pt-2">
                                <p class="mb-1 font-semibold text-heading">&bull; Keterangan Kehadiran:</p>
                                <ul class="list-disc pl-5 space-y-1 text-xs">
                                    <li>Total hari aktif belajar tercatat sebanyak <strong>{{ $totalHari }} hari</strong>.</li>
                                    <li>Persentase kehadiran di atas dihitung berdasarkan perbandingan jumlah hari <strong>Hadir</strong> terhadap total hari aktif.</li>
                                    @if($studentType === 'mapel')
                                        <li>Data kehadiran di atas dihitung dari pertemuan/presensi per sesi pelajaran yang telah dilaksanakan oleh guru mata pelajaran.</li>
                                    @else
                                        <li>Ketidakhadiran karena Sakit or Izin yang disertai surat/keterangan yang sah tetap dicatat pada kolom masing-masing untuk pertimbangan penilaian perilaku/sikap oleh wali kelas.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Switch Tab Script -->
    <script>
        function switchTab(tabId) {
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            // Show active tab pane
            document.getElementById(tabId).classList.remove('hidden');

            // Reset all tab button styles
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-brand', 'text-brand', 'dark:text-brand-soft');
                btn.classList.add('border-transparent', 'text-body', 'hover:text-heading', 'hover:border-default-medium');
            });
            
            // Active tab button style
            const activeBtn = document.getElementById(tabId + '-btn');
            activeBtn.classList.remove('border-transparent', 'text-body', 'hover:text-heading', 'hover:border-default-medium');
            activeBtn.classList.add('border-brand', 'text-brand');
        }
    </script>
</body>
</html>
