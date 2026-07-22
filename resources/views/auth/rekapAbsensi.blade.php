@extends('layouts.main')

@section('title', 'Rekap Absensi')

@section('content')
<style>
    /* Print Layout Styling */
    @media print {
        /* Hide all layout elements of the dashboard */
        #default-sidebar, 
        button[data-drawer-toggle="default-sidebar"],
        #screen-controls, 
        nav, 
        header, 
        footer,
        .print\:hidden,
        .no-print {
            display: none !important;
        }

        /* Reset padding/margin on content wrapper */
        .sm\:ml-64 {
            margin-left: 0 !important;
        }
        div.p-4 {
            padding: 0 !important;
        }

        body {
            background-color: #fff !important;
            color: #000 !important;
            font-family: 'Times New Roman', Times, serif !important;
        }

        #print-area {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #rekap-print-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px !important;
        }

        #rekap-print-table th, #rekap-print-table td {
            border: 1px solid #000 !important;
            color: #000 !important;
            padding: 3px 2px !important;
            font-size: 8pt !important;
            line-height: 1.1 !important;
            text-align: center !important;
            font-family: 'Times New Roman', Times, serif !important;
        }

        #rekap-print-table th {
            font-weight: bold !important;
            background-color: #fff !important;
        }

        #rekap-print-table td.text-left {
            text-align: left !important;
            padding-left: 6px !important;
        }

        @page {
            size: landscape;
            margin: 0.8cm 1cm;
        }
    }
</style>

<div class="max-w-none mx-auto py-8 px-4 print:hidden">
    <!-- Header Controls -->
    <div id="screen-controls" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('attendance-meetings.index') }}" class="text-body hover:text-brand transition-colors duration-150 flex items-center gap-1 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Kembali ke Absensi
                </a>
            </div>
            <h1 class="text-3xl font-extrabold text-heading tracking-tight">Rekap Absensi</h1>
            <p class="text-body text-sm mt-1">Rekapitulasi absensi siswa dan penilaian dalam satu semester.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Class Switcher Dropdown -->
            <div class="w-48">
                <select id="class-switcher" onchange="switchClass(this.value)" class="w-full bg-neutral-secondary-medium border border-default text-heading text-sm rounded-base focus:ring-brand focus:border-brand block p-2.5 font-semibold">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($selectedClass)
                <button type="button" onclick="window.print()" class="bg-brand hover:bg-brand-strong text-white px-5 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center gap-2">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Rekap
                </button>
            @endif
        </div>
    </div>

    @if(!$selectedClass)
        <!-- Empty Class State -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-neutral-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-lg font-bold text-heading mb-1">Silakan Pilih Kelas</h3>
            <p class="text-body text-sm max-w-md mx-auto mb-6">Silakan pilih kelas melalui dropdown di pojok kanan atas untuk memuat data rekap absensi.</p>
        </div>
    @else
        @php
            $meetingsHarian = $meetings->where('tipe', 'harian');
            $meetingsUlangHarian = $meetings->where('tipe', 'ulang harian');
            $meetingsTugas = $meetings->where('tipe', 'tugas');
            $meetingsPts = $meetings->where('tipe', 'pts');
            $meetingsPas = $meetings->where('tipe', 'pas');
            $totalMeetingsCount = $meetings->count();
        @endphp
        <!-- Information Box -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-6 mb-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="text-xs font-bold text-body uppercase tracking-wider block">Sekolah</span>
                    <span class="text-sm font-extrabold text-heading block mt-1">{{ $settings->school_name ?? 'SMP NEGERI 1 TOMPOBULU' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-body uppercase tracking-wider block">Kelas</span>
                    <span class="text-sm font-extrabold text-heading block mt-1">{{ $selectedClass->name }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-body uppercase tracking-wider block">Tahun Pelajaran & Semester</span>
                    <span class="text-sm font-extrabold text-heading block mt-1">
                        {{ $selectedClass->academicYear?->year ?? '2020/2021' }} • Semester {{ $selectedClass->academicYear?->semester ?? 'Genap' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base overflow-hidden shadow-sm">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-neutral-secondary-medium text-heading font-bold text-center border-b border-default select-none">
                            <th class="border-e border-default py-3.5 px-2" colspan="3" rowspan="2">Nomor</th>
                            <th class="border-e border-default py-3.5 px-4 text-left min-w-[200px]" rowspan="3">Nama Peserta Didik</th>
                            <th class="border-e border-default py-2" colspan="{{ $totalMeetingsCount > 0 ? $totalMeetingsCount : 1 }}">Absensi Pertemuan</th>
                            <th class="border-e border-default py-2" colspan="{{ $dailyTests->count() > 0 ? $dailyTests->count() : 1 }}" rowspan="2">Ulangan</th>
                            <th class="border-e border-default py-2" colspan="{{ $assignments->count() > 0 ? $assignments->count() : 1 }}" rowspan="2">Tugas</th>
                            <th class="border-e border-default py-2" colspan="2" rowspan="2">Penilaian</th>
                            <th class="border-e border-default py-2" colspan="3" rowspan="2">Jumlah Absen</th>
                        </tr>
                        <tr class="bg-neutral-secondary-medium text-heading font-bold text-center border-b border-default select-none">
                            <!-- Dynamic Meeting Type headers -->
                            @if($meetingsHarian->count() > 0)
                                <th class="border-e border-default py-1 bg-sky-50/50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-400 font-bold" colspan="{{ $meetingsHarian->count() }}">Harian</th>
                            @endif
                            @if($meetingsUlangHarian->count() > 0)
                                <th class="border-e border-default py-1 bg-amber-50/50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 font-bold" colspan="{{ $meetingsUlangHarian->count() }}">UH</th>
                            @endif
                            @if($meetingsTugas->count() > 0)
                                <th class="border-e border-default py-1 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-bold" colspan="{{ $meetingsTugas->count() }}">Tugas</th>
                            @endif
                            @if($meetingsPts->count() > 0)
                                <th class="border-e border-default py-1 bg-purple-50/50 dark:bg-purple-950/20 text-purple-700 dark:text-purple-400 font-bold" colspan="{{ $meetingsPts->count() }}">PTS</th>
                            @endif
                            @if($meetingsPas->count() > 0)
                                <th class="border-e border-default py-1 bg-rose-50/50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 font-bold" colspan="{{ $meetingsPas->count() }}">PAS</th>
                            @endif
                            @if($totalMeetingsCount === 0)
                                <th class="border-e border-default py-1 font-bold" colspan="1">-</th>
                            @endif
                        </tr>
                        <tr class="bg-neutral-secondary-medium text-heading font-bold text-center border-b border-default select-none">
                            <th class="border-e border-default py-2 w-10">No</th>
                            <th class="border-e border-default py-2 w-16">NIS</th>
                            <th class="border-e border-default py-2 w-16">NISN</th>
                            
                            <!-- Harian Columns -->
                            @foreach($meetingsHarian as $index => $meeting)
                                <th class="border-e border-default py-2 w-8 text-[10px]" title="{{ $meeting->title }}">{{ $index + 1 }}</th>
                            @endforeach
                            <!-- Ulang Harian Columns -->
                            @foreach($meetingsUlangHarian as $index => $meeting)
                                <th class="border-e border-default py-2 w-8 text-[10px]" title="{{ $meeting->title }}">{{ $index + 1 }}</th>
                            @endforeach
                            <!-- Tugas Columns -->
                            @foreach($meetingsTugas as $index => $meeting)
                                <th class="border-e border-default py-2 w-8 text-[10px]" title="{{ $meeting->title }}">{{ $index + 1 }}</th>
                            @endforeach
                            <!-- PTS Columns -->
                            @foreach($meetingsPts as $index => $meeting)
                                <th class="border-e border-default py-2 w-8 text-[10px]" title="{{ $meeting->title }}">{{ $index + 1 }}</th>
                            @endforeach
                            <!-- PAS Columns -->
                            @foreach($meetingsPas as $index => $meeting)
                                <th class="border-e border-default py-2 w-8 text-[10px]" title="{{ $meeting->title }}">{{ $index + 1 }}</th>
                            @endforeach
                            @if($totalMeetingsCount === 0)
                                <th class="border-e border-default py-2 w-8 text-[10px]">-</th>
                            @endif

                            <!-- Ulangan (UH1-UHn) -->
                            @forelse($dailyTests as $tIndex => $test)
                                <th class="border-e border-default py-2 w-10 text-[10px]" title="{{ $test->title }}">UH{{ $tIndex + 1 }}</th>
                            @empty
                                <th class="border-e border-default py-2 w-10 text-[10px]">-</th>
                            @endforelse
                            <!-- Tugas (T1-Tn) -->
                            @forelse($assignments as $aIndex => $asg)
                                <th class="border-e border-default py-2 w-10 text-[10px]" title="{{ $asg->title }}">T{{ $aIndex + 1 }}</th>
                            @empty
                                <th class="border-e border-default py-2 w-10 text-[10px]">-</th>
                            @endforelse
                            <!-- Penilaian (PTS, PAS) -->
                            <th class="border-e border-default py-2 w-10 text-[10px]" title="{{ $midterms->get(0)->title ?? '-' }}">PTS</th>
                            <th class="border-e border-default py-2 w-10 text-[10px]" title="{{ $finals->get(0)->title ?? '-' }}">PAS</th>
                            <!-- Absensi (A, I, S) -->
                            <th class="border-e border-default py-2 w-8 text-fg-danger-strong font-bold">A</th>
                            <th class="border-e border-default py-2 w-8 text-sky-600 font-bold">I</th>
                            <th class="border-e border-default py-2 w-8 text-amber-600 font-bold">S</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default bg-white dark:bg-neutral-primary-soft">
                        @forelse($students as $index => $student)
                            @php
                                $studentAttendances = $attendances->get($student->id) ?? collect();
                                $countA = $studentAttendances->where('status', 'ALFA')->count();
                                $countI = $studentAttendances->where('status', 'IZIN')->count();
                                $countS = $studentAttendances->where('status', 'SAKIT')->count();

                                // UH scores
                                $uhScores = [];
                                foreach ($dailyTests as $tIndex => $test) {
                                    $scoreObj = $dailyScores->get($student->id)?->firstWhere('daily_test_meeting_id', $test->id);
                                    $uhScores[$tIndex] = $scoreObj ? round($scoreObj->score) : '';
                                }
                                
                                // Tugas scores
                                $tugasScores = [];
                                foreach ($assignments as $aIndex => $asg) {
                                    $scoreObj = $assignmentScores->get($student->id)?->firstWhere('assignment_meeting_id', $asg->id);
                                    $tugasScores[$aIndex] = $scoreObj ? round($scoreObj->score) : '';
                                }
                                
                                // PTS score
                                $ptsScore = '';
                                if ($midterms->count() > 0) {
                                    $scoreObj = $midtermScores->get($student->id)?->firstWhere('midterm_exam_id', $midterms[0]->id);
                                    $ptsScore = $scoreObj ? round($scoreObj->score) : '';
                                }
                                
                                // PAS score
                                $pasScore = '';
                                if ($finals->count() > 0) {
                                    $scoreObj = $finalScores->get($student->id)?->firstWhere('final_exam_id', $finals[0]->id);
                                    $pasScore = $scoreObj ? round($scoreObj->score) : '';
                                }
                            @endphp
                            <tr class="hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 text-center">
                                <td class="py-2.5 px-2 font-semibold text-heading border-e border-default">{{ sprintf('%02d', $index + 1) }}</td>
                                <td class="py-2.5 text-body border-e border-default">{{ $student->nis ?? '-' }}</td>
                                <td class="py-2.5 text-body border-e border-default">{{ $student->nisn ?? '-' }}</td>
                                <td class="py-2.5 px-4 font-bold text-heading text-left border-e border-default whitespace-nowrap">{{ $student->name }}</td>
                                
                                <!-- Meetings grouped display -->
                                @php
                                    $renderMeetings = collect()
                                        ->concat($meetingsHarian)
                                        ->concat($meetingsUlangHarian)
                                        ->concat($meetingsTugas)
                                        ->concat($meetingsPts)
                                        ->concat($meetingsPas);
                                @endphp
                                @foreach($renderMeetings as $meeting)
                                    @php
                                        $statusChar = '';
                                        $cellClass = 'text-body';
                                        $meetingAttendance = $studentAttendances->firstWhere('attendance_meeting_id', $meeting->id);
                                        if ($meetingAttendance) {
                                            if ($meetingAttendance->status === 'IZIN') {
                                                $statusChar = 'I';
                                                $cellClass = 'text-sky-600 font-bold bg-sky-50 dark:bg-sky-950/20';
                                            } elseif ($meetingAttendance->status === 'SAKIT') {
                                                $statusChar = 'S';
                                                $cellClass = 'text-amber-600 font-bold bg-amber-50 dark:bg-amber-950/20';
                                            } elseif ($meetingAttendance->status === 'ALFA') {
                                                $statusChar = 'A';
                                                $cellClass = 'text-fg-danger-strong font-extrabold bg-danger-soft/20';
                                            }
                                        }
                                    @endphp
                                    <td class="py-2.5 border-e border-default {{ $cellClass }}">{{ $statusChar }}</td>
                                @endforeach
                                @if($totalMeetingsCount === 0)
                                    <td class="py-2.5 border-e border-default text-body text-center font-semibold">-</td>
                                @endif

                                <!-- Daily Test scores -->
                                @forelse($dailyTests as $tIndex => $test)
                                    <td class="py-2.5 border-e border-default font-semibold text-heading font-mono">{{ $uhScores[$tIndex] ?? '' }}</td>
                                @empty
                                    <td class="py-2.5 border-e border-default text-body text-center font-semibold">-</td>
                                @endforelse

                                <!-- Assignment scores -->
                                @forelse($assignments as $aIndex => $asg)
                                    <td class="py-2.5 border-e border-default font-semibold text-heading font-mono">{{ $tugasScores[$aIndex] ?? '' }}</td>
                                @empty
                                    <td class="py-2.5 border-e border-default text-body text-center font-semibold">-</td>
                                @endforelse

                                <!-- PTS / PAS -->
                                <td class="py-2.5 border-e border-default font-bold text-brand font-mono">{{ $ptsScore }}</td>
                                <td class="py-2.5 border-e border-default font-bold text-brand font-mono">{{ $pasScore }}</td>

                                <!-- Total absences -->
                                <td class="py-2.5 border-e border-default font-bold text-fg-danger-strong bg-danger-soft/10 w-8">{{ $countA > 0 ? $countA : '' }}</td>
                                <td class="py-2.5 border-e border-default font-bold text-sky-600 bg-sky-500/5 w-8">{{ $countI > 0 ? $countI : '' }}</td>
                                <td class="py-2.5 font-bold text-amber-600 bg-amber-500/5 w-8">{{ $countS > 0 ? $countS : '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 9 + ($totalMeetingsCount > 0 ? $totalMeetingsCount : 1) + ($dailyTests->count() > 0 ? $dailyTests->count() : 1) + ($assignments->count() > 0 ? $assignments->count() : 1) }}" class="py-8 text-center text-body">
                                    <p class="font-medium text-heading">Belum ada siswa aktif di kelas ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Print-Only Layout -->
@if($selectedClass)
    @php
        $location = 'Malakaji';
        if ($settings && $settings->school_address && strtolower($settings->school_address) !== 'tidak ada') {
            // Take the address directly
            $location = $settings->school_address;
        }
    @endphp
    <div id="print-area" class="hidden text-black bg-white">
        <!-- Title Header -->
        <div class="text-center font-bold" style="font-family: 'Times New Roman', Times, serif !important;">
            <div style="font-size: 14pt; text-decoration: underline; margin-bottom: 2px;">ABSENSI PESERTA DIDIK</div>
            <div style="font-size: 12pt; margin-bottom: 2px;">{{ strtoupper($settings->school_name ?? 'SMP NEGERI 1 TOMPOBULU') }}</div>
            <div style="font-size: 11pt; margin-bottom: 12px;">TAHUN PELAJARAN {{ $selectedClass->academicYear?->year ?? '2020/2021' }}</div>
        </div>

        <!-- Metadata Section -->
        <table style="width: 100%; border: none !important; margin-bottom: 5px; font-family: 'Times New Roman', Times, serif !important; font-size: 10pt;">
            <tr style="border: none !important;">
                <td style="width: 60px; border: none !important; padding: 1px 0 !important; text-align: left;">Kelas</td>
                <td style="width: 10px; border: none !important; padding: 1px 0 !important; text-align: center;">:</td>
                <td style="border: none !important; padding: 1px 0 !important; text-align: left; font-weight: bold;">{{ $selectedClass->name }}</td>
            </tr>
            <tr style="border: none !important;">
                <td style="border: none !important; padding: 1px 0 !important; text-align: left;">Semes</td>
                <td style="border: none !important; padding: 1px 0 !important; text-align: center;">:</td>
                <td style="border: none !important; padding: 1px 0 !important; text-align: left; font-weight: bold;">{{ $selectedClass->academicYear?->semester ?? 'Genap' }}</td>
            </tr>
        </table>

        <!-- Grid Table -->
        <table id="rekap-print-table" style="font-family: 'Times New Roman', Times, serif !important;">
            <thead>
                <tr class="text-center font-bold">
                    <th colspan="3" rowspan="2" style="width: 7%;">Nomor</th>
                    <th rowspan="3" style="width: 25%;">Nama Peserta Didik</th>
                    <th colspan="{{ $totalMeetingsCount > 0 ? $totalMeetingsCount : 1 }}" style="width: 36%;">Absensi</th>
                    <th colspan="{{ $dailyTests->count() > 0 ? $dailyTests->count() : 1 }}" rowspan="2" style="width: 8%;">Ulangan</th>
                    <th colspan="{{ $assignments->count() > 0 ? $assignments->count() : 1 }}" rowspan="2" style="width: 8%;">Tugas</th>
                    <th colspan="2" rowspan="2" style="width: 5%;">Penilaian</th>
                    <th colspan="3" rowspan="2" style="width: 8%;">Absensi</th>
                    <th rowspan="3" style="width: 3%;">KET.</th>
                </tr>
                <tr class="text-center font-bold">
                    <!-- Dynamic Meeting Type headers -->
                    @if($meetingsHarian->count() > 0)
                        <th colspan="{{ $meetingsHarian->count() }}">Harian</th>
                    @endif
                    @if($meetingsUlangHarian->count() > 0)
                        <th colspan="{{ $meetingsUlangHarian->count() }}">UH</th>
                    @endif
                    @if($meetingsTugas->count() > 0)
                        <th colspan="{{ $meetingsTugas->count() }}">Tugas</th>
                    @endif
                    @if($meetingsPts->count() > 0)
                        <th colspan="{{ $meetingsPts->count() }}">PTS</th>
                    @endif
                    @if($meetingsPas->count() > 0)
                        <th colspan="{{ $meetingsPas->count() }}">PAS</th>
                    @endif
                    @if($totalMeetingsCount === 0)
                        <th colspan="1">-</th>
                    @endif
                </tr>
                <tr class="text-center font-bold">
                    <th style="width: 2%;">Urut</th>
                    <th style="width: 2.5%;">NIS</th>
                    <th style="width: 2.5%;">NISN</th>

                    <!-- Harian -->
                    @foreach($meetingsHarian as $index => $meeting)
                        <th style="width: 2%;">{{ $index + 1 }}</th>
                    @endforeach
                    <!-- UH -->
                    @foreach($meetingsUlangHarian as $index => $meeting)
                        <th style="width: 2%;">{{ $index + 1 }}</th>
                    @endforeach
                    <!-- Tugas -->
                    @foreach($meetingsTugas as $index => $meeting)
                        <th style="width: 2%;">{{ $index + 1 }}</th>
                    @endforeach
                    <!-- PTS -->
                    @foreach($meetingsPts as $index => $meeting)
                        <th style="width: 2%;">{{ $index + 1 }}</th>
                    @endforeach
                    <!-- PAS -->
                    @foreach($meetingsPas as $index => $meeting)
                        <th style="width: 2%;">{{ $index + 1 }}</th>
                    @endforeach
                    @if($totalMeetingsCount === 0)
                        <th style="width: 2%;">-</th>
                    @endif

                    <!-- Ulangan (UH1-UHn) -->
                    @forelse($dailyTests as $tIndex => $test)
                        <th style="width: 2.6%; font-size: 7.5pt !important;">UH{{ $tIndex + 1 }}</th>
                    @empty
                        <th style="width: 2.6%; font-size: 7.5pt !important;">-</th>
                    @endforelse
                    <!-- Tugas (T1-Tn) -->
                    @forelse($assignments as $aIndex => $asg)
                        <th style="width: 2.6%; font-size: 7.5pt !important;">T{{ $aIndex + 1 }}</th>
                    @empty
                        <th style="width: 2.6%; font-size: 7.5pt !important;">-</th>
                    @endforelse
                    <!-- Penilaian (PTS, PAS) -->
                    <th style="width: 2.5%; font-size: 7.5pt !important;">PTS</th>
                    <th style="width: 2.5%; font-size: 7.5pt !important;">PAS</th>
                    <!-- Absensi (A, I, S) -->
                    <th style="width: 2.6%;">A</th>
                    <th style="width: 2.7%;">I</th>
                    <th style="width: 2.7%;">S</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    @php
                        $studentAttendances = $attendances->get($student->id) ?? collect();
                        $countA = $studentAttendances->where('status', 'ALFA')->count();
                        $countI = $studentAttendances->where('status', 'IZIN')->count();
                        $countS = $studentAttendances->where('status', 'SAKIT')->count();

                        // UH scores
                        $uhScores = [];
                        foreach ($dailyTests as $tIndex => $test) {
                            $scoreObj = $dailyScores->get($student->id)?->firstWhere('daily_test_meeting_id', $test->id);
                            $uhScores[$tIndex] = $scoreObj ? round($scoreObj->score) : '';
                        }
                        
                        // Tugas scores
                        $tugasScores = [];
                        foreach ($assignments as $aIndex => $asg) {
                            $scoreObj = $assignmentScores->get($student->id)?->firstWhere('assignment_meeting_id', $asg->id);
                            $tugasScores[$aIndex] = $scoreObj ? round($scoreObj->score) : '';
                        }
                        
                        // PTS score
                        $ptsScore = '';
                        if ($midterms->count() > 0) {
                            $scoreObj = $midtermScores->get($student->id)?->firstWhere('midterm_exam_id', $midterms[0]->id);
                            $ptsScore = $scoreObj ? round($scoreObj->score) : '';
                        }
                        
                        // PAS score
                        $pasScore = '';
                        if ($finals->count() > 0) {
                            $scoreObj = $finalScores->get($student->id)?->firstWhere('final_exam_id', $finals[0]->id);
                            $pasScore = $scoreObj ? round($scoreObj->score) : '';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student->nis ?? '0' }}</td>
                        <td>{{ $student->nisn ?? '0' }}</td>
                        <td class="text-left">{{ $student->name }}</td>
                        
                        <!-- Meetings grouped display -->
                        @php
                            $renderMeetings = collect()
                                ->concat($meetingsHarian)
                                ->concat($meetingsUlangHarian)
                                ->concat($meetingsTugas)
                                ->concat($meetingsPts)
                                ->concat($meetingsPas);
                        @endphp
                        @foreach($renderMeetings as $meeting)
                            @php
                                $statusChar = '';
                                $meetingAttendance = $studentAttendances->firstWhere('attendance_meeting_id', $meeting->id);
                                if ($meetingAttendance) {
                                    if ($meetingAttendance->status === 'IZIN') {
                                        $statusChar = 'I';
                                    } elseif ($meetingAttendance->status === 'SAKIT') {
                                        $statusChar = 'S';
                                    } elseif ($meetingAttendance->status === 'ALFA') {
                                        $statusChar = 'A';
                                    }
                                }
                            @endphp
                            <td>{{ $statusChar }}</td>
                        @endforeach
                        @if($totalMeetingsCount === 0)
                            <td>-</td>
                        @endif

                        <!-- Ulangan (UH1-UHn) -->
                        @forelse($dailyTests as $tIndex => $test)
                            <td>{{ $uhScores[$tIndex] ?? '' }}</td>
                        @empty
                            <td>-</td>
                        @endforelse

                        <!-- Tugas (T1-Tn) -->
                        @forelse($assignments as $aIndex => $asg)
                            <td>{{ $tugasScores[$aIndex] ?? '' }}</td>
                        @empty
                            <td>-</td>
                        @endforelse

                        <!-- Penilaian (PTS, PAS) -->
                        <td>{{ $ptsScore }}</td>
                        <td>{{ $pasScore }}</td>

                        <!-- Absensi (A, I, S) -->
                        <td>{{ $countA > 0 ? $countA : '' }}</td>
                        <td>{{ $countI > 0 ? $countI : '' }}</td>
                        <td>{{ $countS > 0 ? $countS : '' }}</td>
                        
                        <!-- Keterangan -->
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures Block -->
        <div style="font-family: 'Times New Roman', Times, serif !important; font-size: 10pt; margin-top: 25px; page-break-inside: avoid;">
            <table style="width: 100%; border: none !important;">
                <tr style="border: none !important;">
                    <td style="width: 50%; border: none !important; text-align: left; padding-left: 20px;">
                        Mengetahui:<br>
                        Kepala Sekolah
                    </td>
                    <td style="width: 50%; border: none !important; text-align: right; padding-right: 20px;">
                        {{ $location }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                        Guru Mata Pelajaran
                    </td>
                </tr>
                <tr style="border: none !important; height: 50px;">
                    <td style="border: none !important;"></td>
                    <td style="border: none !important;"></td>
                </tr>
                <tr style="border: none !important;">
                    <td style="width: 50%; border: none !important; text-align: left; padding-left: 20px;">
                        <span style="font-weight: bold; text-decoration: underline;">{{ $settings->principal_name ?? 'Hj. SYAMSIAR SYAHRUL, S. Pd., M. Pd.' }}</span><br>
                        NIP. {{ $settings->npsn ?? '19640212 198206 2 001' }}
                    </td>
                    <td style="width: 50%; border: none !important; text-align: right; padding-right: 20px;">
                        <span style="font-weight: bold; text-decoration: underline;">{{ $settings->teacher_name ?? 'Abdullah, A.Md' }}</span><br>
                        NIP. {{ $settings->teacher_nip ?? '19721225 200605 1 001' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endif

<script>
    function switchClass(classId) {
        if (!classId) {
            window.location.href = '/rekap-absensi';
        } else {
            window.location.href = `/rekap-absensi?class_id=${classId}`;
        }
    }
</script>
@endsection
