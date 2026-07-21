@extends('layouts.waliKelas')

@section('title', 'Cetak Raport Siswa')

@section('content')
<!-- Container Utama Raport -->
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
                            <span class="inline-flex items-center text-xs font-bold text-heading">Raport</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-extrabold tracking-tight text-heading">Raport Peserta Didik</h1>
            <p class="text-xs text-body mt-0.5">Lihat daftar siswa dan unduh atau cetak dokumen raport semester secara individual maupun kolektif.</p>
        </div>

        <!-- Class Badge Banner -->
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-neutral-primary-soft border border-default shadow-xs rounded-base">
            <div class="p-2.5 bg-brand text-white rounded-lg shadow-xs shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 7h6m-6 4h6m-6 4h3"/>
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

    <!-- Alert Messages -->
    <div id="page-alert-container" class="w-full space-y-3">
        @if(session('success'))
            <div class="flex items-start sm:items-center p-4 text-sm text-fg-success-strong bg-success-soft border border-emerald-300/40 dark:bg-emerald-950/90 dark:text-emerald-300 dark:border-emerald-700/80 shadow-xs rounded-base w-full transition-all duration-500 opacity-100" role="alert">
                <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="flex-1">{{ session('success') }}</p>
                <button type="button" onclick="this.closest('[role=alert]').remove()" class="ms-auto text-emerald-600 hover:text-emerald-800 p-1 rounded-base transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
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

    <!-- Toolbar: Search & Action Buttons -->
    <div class="p-4 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <form method="GET" action="{{ route('wali-kelas.raport') }}" class="flex flex-1 items-center gap-2">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-body">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa, NIS, atau NISN..." class="block w-full ps-9 pe-3 py-2 text-xs border border-default rounded-base bg-neutral-secondary-soft focus:ring-brand focus:border-brand text-heading placeholder-body/60">
            </div>
            <button type="submit" class="px-3 py-2 text-xs font-bold text-white bg-brand hover:bg-brand/90 rounded-base transition-colors shadow-xs">
                Cari
            </button>
            @if($search)
                <a href="{{ route('wali-kelas.raport') }}" class="px-3 py-2 text-xs font-medium text-body bg-neutral-tertiary hover:text-heading rounded-base transition-colors">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <!-- Modal Trigger Button -->
            <button type="button" onclick="openKelompokModal()" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-heading bg-neutral-secondary-soft hover:bg-neutral-tertiary border border-default rounded-base shadow-xs transition-colors">
                <svg class="w-4 h-4 text-body" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Kelompok Mapel</span>
            </button>

            @if($students->isNotEmpty())
                <a href="{{ route('wali-kelas.raport.cetak-semua') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-base shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Semua Raport</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Data Table Siswa -->
    <div class="relative overflow-x-auto border border-default rounded-base shadow-xs bg-white dark:bg-neutral-primary-soft">
        <table class="w-full text-xs text-left rtl:text-right text-body">
            <thead class="text-[11px] uppercase bg-neutral-secondary-soft border-b border-default text-heading font-extrabold tracking-wider">
                <tr>
                    <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                    <th scope="col" class="px-4 py-3">NIS / NISN</th>
                    <th scope="col" class="px-6 py-3">Nama Siswa</th>
                    <th scope="col" class="px-4 py-3 text-center">L/P</th>
                    <th scope="col" class="px-4 py-3 text-center">Status Kelengkapan</th>
                    <th scope="col" class="px-6 py-3 text-center w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-default">
                @forelse($students as $index => $student)
                    <tr class="hover:bg-neutral-tertiary/50 transition-colors">
                        <td class="px-4 py-3 text-center font-bold text-heading">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-body">
                            {{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}
                        </td>
                        <td class="px-6 py-3 font-bold text-heading text-sm">
                            {{ $student->name }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full {{ $student->gender == 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                                {{ $student->gender ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-base {{ $student->nilaiMapels->isNotEmpty() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-100 text-gray-500' }}" title="Nilai Mapel">
                                    Nilai: {{ $student->nilaiMapels->count() }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-base {{ $student->sikap ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-gray-100 text-gray-500' }}" title="Capaian Sikap">
                                    Sikap
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-base {{ $student->absensi ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-gray-100 text-gray-500' }}" title="Absensi">
                                    Absensi
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('wali-kelas.raport.cetak', $student->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-brand hover:bg-brand/90 rounded-base shadow-xs transition-colors w-full sm:w-auto">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Download Raport</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-body">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-10 h-10 text-body/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="text-sm font-semibold">Tidak ada siswa ditemukan.</p>
                                <p class="text-xs text-body/70">
                                    {{ $search ? 'Coba ubah kata kunci pencarian Anda.' : 'Silakan tambahkan data siswa di menu Siswa terlebih dahulu.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pengaturan Kelompok Mapel -->
<div id="kelompok-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white dark:bg-neutral-primary-soft rounded-base shadow-lg border border-default p-6 space-y-5">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-default pb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-brand/10 text-fg-brand rounded-base">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-heading">Pengaturan Kelompok Mata Pelajaran</h3>
                    <p class="text-xs text-body">Tentukan apakah mata pelajaran akan masuk di **Kelompok A** (Umum) atau **Kelompok B** (Seni, Olahraga, & Mulok) pada cetakan Raport.</p>
                </div>
            </div>
            <button type="button" onclick="closeKelompokModal()" class="text-body hover:text-heading p-1.5 rounded-base">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form action="{{ route('wali-kelas.raport.update-kelompok') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="max-h-96 overflow-y-auto border border-default rounded-base divide-y divide-default">
                @forelse($mapelSettings as $mIdx => $mapel)
                    <div class="p-3.5 flex items-center justify-between gap-4 hover:bg-neutral-tertiary/40 transition-colors">
                        <input type="hidden" name="mapels[{{ $mIdx }}][id]" value="{{ $mapel->id }}">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-extrabold text-heading truncate">{{ $mapel->mapel }}</p>
                            <p class="text-[11px] text-body truncate">Guru: {{ $mapel->guru ?? '-' }} | KKM: {{ $mapel->kkm ?? '-' }}</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium text-heading">
                                <input type="radio" name="mapels[{{ $mIdx }}][kelompok]" value="A" {{ ($mapel->kelompok ?? 'A') === 'A' ? 'checked' : '' }} class="w-4 h-4 text-brand bg-gray-100 border-gray-300 focus:ring-brand dark:focus:ring-brand focus:ring-2">
                                <span>Kelompok A</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-medium text-heading">
                                <input type="radio" name="mapels[{{ $mIdx }}][kelompok]" value="B" {{ ($mapel->kelompok ?? 'A') === 'B' ? 'checked' : '' }} class="w-4 h-4 text-brand bg-gray-100 border-gray-300 focus:ring-brand dark:focus:ring-brand focus:ring-2">
                                <span>Kelompok B</span>
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-body">
                        Belum ada konfigurasi mata pelajaran. Silakan tambahkan di Master Data lebih dulu.
                    </div>
                @endforelse
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-2 border-t border-default pt-4">
                <button type="button" onclick="closeKelompokModal()" class="px-4 py-2 text-xs font-bold text-body bg-neutral-tertiary hover:bg-neutral-tertiary/80 rounded-base transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand/90 rounded-base transition-colors shadow-xs">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openKelompokModal() {
        document.getElementById('kelompok-modal').classList.remove('hidden');
    }

    function closeKelompokModal() {
        document.getElementById('kelompok-modal').classList.add('hidden');
    }
</script>
@endsection
