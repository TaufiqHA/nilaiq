@extends('layouts.main')

@section('title', 'Nilai Akhir')

@section('content')
<style>
    /* Print Styles */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        #default-sidebar,
        button[data-drawer-toggle="default-sidebar"],
        #class-list-section,
        nav, header, footer,
        .print\:hidden,
        form#form-batch-recaps {
            display: none !important;
        }

        .sm\:ml-64 {
            margin-left: 0 !important;
        }

        div.p-4 {
            padding: 0 !important;
        }

        body, #print-area, #print-area * {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: 'Times New Roman', Times, serif !important;
        }

        #print-area {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #print-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px !important;
            page-break-inside: auto !important;
        }

        #print-table tr {
            page-break-inside: avoid !important;
        }

        #print-table th, #print-table td {
            border: 1px solid #000000 !important;
            color: #000000 !important;
        }

        #print-table th {
            background-color: #00a8e8 !important;
            color: #000000 !important;
        }

        #print-table td.bg-print-score {
            background-color: #e0f2fe !important;
        }
    }
</style>

<div class="max-w-none mx-auto py-8 px-4 print:hidden">
    <!-- Back Button -->
    <div class="mb-5">
        <a href="{{ route('rekap.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-body hover:text-brand transition-colors duration-200 cursor-pointer group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Rekap Nilai
        </a>
    </div>

    <!-- Header Section & Navigation -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-heading tracking-tight mb-2">Nilai Akhir & Capaian Kompetensi</h1>
            <p class="text-body text-sm">
                {{ $selectedClass ? 'Kelas ' . $selectedClass->name . ' • Tahun Ajaran ' . ($selectedClass->academicYear?->year ?? '-') . ' (' . ($selectedClass->academicYear?->semester ?? '-') . ')' : 'Pilih kelas untuk mengelola nilai akhir.' }}
            </p>
        </div>

        @if($selectedClass)
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.print()" class="bg-neutral-secondary-soft hover:bg-neutral-tertiary text-heading border border-default px-4 py-2.5 rounded-base text-sm font-bold shadow-xs transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 print:hidden" title="Cetak Daftar Nilai Akhir">
                <svg class="w-4.5 h-4.5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.82l-.24-.24H4.5a.75.75 0 00-.75.75v3.5c0 .414.336.75.75.75h15a.75.75 0 00.75-.75v-3.5a.75.75 0 00-.75-.75h-1.98l-.24.24m-12 0a2.25 2.25 0 00-.24 2.4l1.16 2.32c.113.226.342.368.596.368h8.568c.254 0 .483-.142.596-.368l1.16-2.32a2.25 2.25 0 00-.24-2.4m-12 0h12M12 3v11.25m0 0l-3.75-3.75M12 14.25l3.75-3.75" />
                </svg>
                Cetak
            </button>

            <button type="submit" form="form-batch-recaps" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 print:hidden">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Simpan Nilai Akhir
            </button>
        </div>
        @endif
    </div>

    <!-- Success / Error Alert -->
    @if(session('success'))
    <div id="alert-success" class="flex items-center p-4 mb-6 text-emerald-800 border border-emerald-300 rounded-lg bg-emerald-50 dark:bg-neutral-primary-soft dark:text-emerald-400 dark:border-emerald-800" role="alert">
        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">Success</span>
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-emerald-50 text-emerald-500 rounded-lg focus:ring-2 focus:ring-emerald-400 p-1.5 hover:bg-emerald-200 inline-flex items-center justify-center h-8 w-8 dark:bg-neutral-primary-soft dark:text-emerald-400" onclick="this.closest('#alert-success').remove()" aria-label="Close">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif

    @if($selectedClass)
    <!-- Control Bar: Search Input -->
    <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-4 mb-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-body">Daftar Siswa Kelas {{ $selectedClass->name }}</span>
        </div>

        <div class="relative w-full md:w-72">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="search-student" oninput="filterStudents(this.value)" placeholder="Cari nama atau NIS..." class="w-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base pl-9 pr-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
        </div>
    </div>
    @endif

    @if(!$selectedClass)
        <!-- Empty Selection State -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-12 text-center shadow-xs">
            <svg class="w-16 h-16 text-neutral-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
            </svg>
            <h3 class="text-lg font-bold text-heading mb-1">Data Kelas Tidak Ditemukan</h3>
            <p class="text-body text-sm max-w-md mx-auto">Silakan pilih kelas melalui menu Rekap Nilai terlebih dahulu.</p>
        </div>
    @else
        <!-- Main Form for Batch Saving -->
        <form id="form-batch-recaps" action="{{ route('recaps.batch') }}" method="POST">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">

            <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base shadow-xs overflow-hidden">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-body">
                        <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 min-w-[50px] w-12 text-center">No</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[200px]">Nama Lengkap</th>
                                <th scope="col" class="px-4 py-3.5 min-w-[120px] text-center">NIS / NISN</th>
                                <th scope="col" class="px-4 py-3.5 min-w-[140px] w-36 text-center">Nilai Akhir</th>
                                <th scope="col" class="px-6 py-3.5 min-w-[320px]">Deskripsi Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-default" id="students-table-body">
                            @forelse($students as $index => $student)
                                @php
                                    $recap = $recapsKeyed->get($student->id);
                                    $score = $recap ? $recap->final_score_result : null;
                                    $desc = $recap ? $recap->competency_description : '';
                                @endphp
                                <tr class="student-row hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150" data-name="{{ strtolower($student->name) }}" data-nis="{{ $student->nis ?? '' }}" data-student-id="{{ $student->id }}">
                                    <td class="px-4 py-4 text-center font-semibold text-heading select-none">
                                        {{ sprintf('%02d', $index + 1) }}
                                        <input type="hidden" name="recaps[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-heading whitespace-nowrap">
                                        {{ $student->name }}
                                    </td>
                                    <td class="px-4 py-4 text-center text-body text-xs font-mono">
                                        {{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" min="0" max="100" name="recaps[{{ $index }}][final_score_result]" id="input-score-{{ $index }}" value="{{ $score !== null ? number_format($score, 2, '.', '') : '' }}" placeholder="0.00" class="w-28 mx-auto text-center font-mono font-bold text-heading bg-white dark:bg-neutral-primary-soft border border-default rounded-base py-1.5 px-2 focus:ring-brand focus:border-brand">
                                    </td>
                                    <td class="px-6 py-4">
                                        <textarea name="recaps[{{ $index }}][competency_description]" id="input-desc-{{ $index }}" rows="2" placeholder="Masukkan deskripsi capaian pembelajaran..." class="w-full text-sm text-heading bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-2 focus:ring-brand focus:border-brand">{{ $desc }}</textarea>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-body italic">
                                        Belum ada data siswa aktif di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->isNotEmpty())
                <div class="p-4 bg-neutral-secondary-medium border-t border-default flex items-center justify-between">
                    <span class="text-xs font-semibold text-body">Total: {{ $students->count() }} Siswa</span>
                    <button type="submit" class="bg-brand hover:bg-brand-strong text-white px-5 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center gap-2">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
                @endif
            </div>
        </form>
    @endif
</div>

@if($selectedClass)
<!-- Printable Section (Only visible during window.print()) -->
<div id="print-area" class="hidden print:block font-serif text-black">
    <!-- Printable Title Header -->
    <div class="mb-4">
        <h1 class="text-lg font-extrabold uppercase tracking-wide leading-tight">DAFTAR NILAI AKHIR PESERTA DIDIK</h1>
        <h2 class="text-base font-extrabold uppercase tracking-wide leading-tight">{{ $settings->school_name ?? 'SMP NEGERI 1 TOMPOBULU' }}</h2>
        <h3 class="text-sm font-extrabold uppercase tracking-wide leading-tight">TAHUN PELAJARAN {{ $selectedClass->academicYear?->year ?? '2025/2026' }}</h3>
    </div>

    <!-- Metadata Table -->
    <div class="mb-4 text-xs font-semibold">
        <table class="font-serif border-0">
            <tr>
                <td class="pr-3 py-0.5 whitespace-nowrap">Kelas</td>
                <td class="px-2 py-0.5">: {{ $selectedClass->name }}</td>
            </tr>
            <tr>
                <td class="pr-3 py-0.5 whitespace-nowrap">Semester</td>
                <td class="px-2 py-0.5">: {{ ucfirst($selectedClass->academicYear?->semester ?? '-') }}</td>
            </tr>
            <tr>
                <td class="pr-3 py-0.5 whitespace-nowrap">Mata Pelajaran</td>
                <td class="px-2 py-0.5">: {{ $settings->subject_name ?? 'Bahasa Inggris' }}</td>
            </tr>
            <tr>
                <td class="pr-3 py-0.5 whitespace-nowrap">Guru Mata Pelajaran</td>
                <td class="px-2 py-0.5">: {{ $settings->teacher_name ?? auth()->user()->name }}</td>
            </tr>
            <tr>
                <td class="pr-3 py-0.5 whitespace-nowrap">Kriteria Belajar Minimal</td>
                <td class="px-2 py-0.5">: {{ isset($settings->minimum_score) ? round($settings->minimum_score) : 70 }}</td>
            </tr>
        </table>
    </div>

    <!-- Printable Table -->
    <table id="print-table" class="w-full border-collapse border border-black text-xs font-serif">
        <thead>
            <tr class="bg-[#00a8e8] text-black font-bold text-center border border-black" style="background-color: #00a8e8 !important;">
                <th class="border border-black px-2 py-1.5" colspan="3" style="background-color: #00a8e8 !important;">Nomor</th>
                <th class="border border-black px-4 py-1.5 text-left" rowspan="2" style="background-color: #00a8e8 !important;">Nama Peserta Didik</th>
                <th class="border border-black px-2 py-1.5" colspan="2" style="background-color: #00a8e8 !important;">Pengetahuan dan Keterampilan</th>
            </tr>
            <tr class="bg-[#00a8e8] text-black font-bold text-center border border-black" style="background-color: #00a8e8 !important;">
                <th class="border border-black px-1.5 py-1 w-10" style="background-color: #00a8e8 !important;">Urut</th>
                <th class="border border-black px-2 py-1 w-16" style="background-color: #00a8e8 !important;">NIS</th>
                <th class="border border-black px-2 py-1 w-24" style="background-color: #00a8e8 !important;">NISN</th>
                <th class="border border-black px-2 py-1 w-20" style="background-color: #00a8e8 !important;">Nilai Akhir</th>
                <th class="border border-black px-4 py-1 text-left" style="background-color: #00a8e8 !important;">Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-black border border-black">
            @foreach($students as $index => $student)
                @php
                    $recap = $recapsKeyed->get($student->id);
                    $score = $recap?->final_score_result;
                    $desc = $recap?->competency_description;
                @endphp
                <tr class="border border-black">
                    <td class="border border-black text-center py-2 px-1 font-serif">{{ $index + 1 }}</td>
                    <td class="border border-black text-center py-2 px-1 font-serif">{{ $student->nis ?? '-' }}</td>
                    <td class="border border-black text-center py-2 px-1 font-serif">{{ $student->nisn ?? '-' }}</td>
                    <td class="border border-black text-left py-2 px-3 font-serif uppercase font-bold whitespace-nowrap">{{ $student->name }}</td>
                    <td class="border border-black text-center py-2 px-2 font-serif font-bold bg-[#e0f2fe] bg-print-score" style="background-color: #e0f2fe !important;">{{ $score !== null ? round($score) : '' }}</td>
                    <td class="border border-black text-left py-2 px-3 font-serif leading-relaxed bg-[#e0f2fe] bg-print-score" style="background-color: #e0f2fe !important;">{{ $desc ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
    // Filter student list by name or NIS
    function filterStudents(query) {
        const rows = document.querySelectorAll('.student-row');
        const q = query.toLowerCase();
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nis = row.getAttribute('data-nis') || '';
            if (name.includes(q) || nis.includes(q)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection

