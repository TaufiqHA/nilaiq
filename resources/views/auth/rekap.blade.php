@extends('layouts.main')

@section('title', 'Rekap Nilai')

@section('content')
<style>
    /* Styling khusus mode cetak / PDF */
    @media print {
        /* Sembunyikan elemen dashboard & navigasi */
        #default-sidebar, 
        button[data-drawer-toggle="default-sidebar"],
        #class-list-section, 
        #rekap-details-section,
        nav, 
        header, 
        footer,
        .print\:hidden {
            display: none !important;
        }

        /* Hilangkan padding/margin pada content wrapper */
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

        /* Area cetak harus terlihat */
        #print-area {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Format tabel agar persis gambar */
        #print-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px !important;
            page-break-inside: auto !important;
        }

        #print-table tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }

        #print-table th, #print-table td {
            border: 1px solid #000 !important;
            color: #000 !important;
            padding: 4px 6px !important;
            font-size: 10pt !important;
            text-align: center !important;
            font-family: 'Times New Roman', Times, serif !important;
        }

        #print-table th {
            font-weight: bold !important;
            background-color: #fff !important;
        }

        #print-table td.text-left {
            text-align: left !important;
        }

        /* Landscape orientation */
        @page {
            size: landscape;
            margin: 0.8cm 1cm;
        }
    }
</style>

<div class="max-w-none mx-auto py-8 px-4">
    <!-- Section 1: Classes Grid Selection -->
    <div id="class-list-section" class="transition-all duration-300 print:hidden">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-heading tracking-tight mb-2">Rekap Nilai</h1>
                <p class="text-body">Pilih kelas di bawah ini untuk melihat rekap nilai lengkap seluruh siswa.</p>
            </div>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($classes as $class)
                <div onclick="showClassRekap({{ $class->id }})" class="bg-white dark:bg-neutral-primary-soft border border-default hover:border-brand rounded-base p-6 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer group flex flex-col justify-between relative overflow-hidden min-h-[170px]">
                    
                    <!-- Glow effect on hover -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand/5 rounded-full blur-xl group-hover:bg-brand/10 transition-all duration-200"></div>

                    <div>
                        <!-- Title & Info Icon -->
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <h3 class="text-xl font-bold text-heading group-hover:text-brand transition-colors duration-200">
                                Kelas {{ $class->name }}
                            </h3>
                            
                            <div class="text-body group-hover:text-brand transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Class Metadata -->
                        <div class="space-y-2 text-xs">
                            <!-- Academic Year -->
                            <div class="flex items-center gap-2 text-body">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Tahun Ajaran: <span class="font-medium text-heading">{{ $class->academicYear?->year ?? '-' }}</span></span>
                            </div>
                            
                            <!-- Semester -->
                            <div class="flex items-center gap-2 text-body">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Semester: <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-neutral-secondary-medium border border-default text-heading uppercase">{{ $class->academicYear?->semester ?? '-' }}</span></span>
                            </div>

                            <!-- Student Count -->
                            <div class="flex items-center gap-2 text-body">
                                <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Jumlah Siswa: <span class="font-bold text-brand">{{ $class->students->count() }} Siswa</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-8 text-center text-body">
                    <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2m14 0V9a2 2 0 0 0-2-2M5 11V9a2 2 0 0 1 2-2m0 0V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M7 7h10" />
                    </svg>
                    <p class="font-medium text-heading">Belum ada data kelas.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Score Recap Details (Hidden by Default) -->
    <div id="rekap-details-section" class="hidden transition-all duration-300 print:hidden">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <button type="button" onclick="backToClasses()" class="text-body hover:text-brand p-2 rounded-base hover:bg-neutral-secondary-soft border border-default hover:border-brand transition-all duration-200 cursor-pointer flex items-center justify-center shrink-0" title="Kembali ke Daftar Kelas">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <div>
                    <h1 id="rekap-title" class="text-3xl font-extrabold text-heading tracking-tight">Rekap Nilai Kelas VII A</h1>
                    <p id="rekap-subtitle" class="text-body">Tahun Ajaran 2026/2027 • Semester Ganjil</p>
                </div>
            </div>
            
            <!-- Actions & Search -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4.5 h-4.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="search-students" oninput="filterRekap(this.value)" placeholder="Cari nama siswa..." class="w-full bg-white dark:bg-neutral-primary-soft border border-default rounded-base pl-9 pr-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand">
                </div>
                
                <button type="button" onclick="printRekap()" class="bg-brand hover:bg-brand-strong text-white px-4 py-2 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 print:hidden">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.82l-.24-.24H4.5a.75.75 0 00-.75.75v3.5c0 .414.336.75.75.75h15a.75.75 0 00.75-.75v-3.5a.75.75 0 00-.75-.75h-1.98l-.24.24m-12 0a2.25 2.25 0 00-.24 2.4l1.16 2.32c.113.226.342.368.596.368h8.568c.254 0 .483-.142.596-.368l1.16-2.32a2.25 2.25 0 00-.24-2.4m-12 0h12M12 3v11.25m0 0l-3.75-3.75M12 14.25l3.75-3.75" />
                    </svg>
                    Cetak Rekap
                </button>
            </div>
        </div>

        <!-- Score Recap Table Card -->
        <div class="bg-white dark:bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm relative">
            <!-- Loading Indicator -->
            <div id="rekap-loader" class="absolute inset-0 bg-white/70 dark:bg-neutral-primary-soft/75 backdrop-blur-xs flex items-center justify-center z-10 rounded-base">
                <div class="text-center">
                    <svg class="animate-spin h-10 w-10 text-brand mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm font-bold text-heading">Memuat rekap nilai...</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="relative overflow-x-auto border border-default rounded-base bg-white dark:bg-neutral-primary-soft hidden" id="rekap-table-container">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-bold text-heading uppercase bg-neutral-secondary-medium border-b border-default select-none text-center">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 w-12 text-center" rowspan="2">No</th>
                            <th scope="col" class="px-6 py-3.5 text-left" rowspan="2">Nama Lengkap</th>
                            <th scope="col" class="px-4 py-3.5" id="komponen-nilai-header" colspan="4">Komponen Nilai</th>
                            <th scope="col" class="px-4 py-3.5 bg-neutral-secondary-soft/50" rowspan="2">Jumlah</th>
                            <th scope="col" class="px-4 py-3.5 bg-neutral-secondary-soft" rowspan="2">Rata-rata</th>
                            <th scope="col" class="px-4 py-3.5 w-24" rowspan="2">Peringkat</th>
                        </tr>
                        <tr class="border-t border-default bg-neutral-secondary-medium/70" id="sub-headers-row">
                            <th scope="col" class="px-3 py-2 w-28">Ulangan Harian</th>
                            <th scope="col" class="px-3 py-2 w-28">Tugas</th>
                            <th scope="col" class="px-3 py-2 w-28">PTS</th>
                            <th scope="col" class="px-3 py-2 w-28">PAS</th>
                        </tr>
                    </thead>
                    <tbody id="rekap-table-body" class="divide-y divide-default">
                        <!-- Javascript will populate rows here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty state for student list -->
            <div id="rekap-empty" class="hidden text-center py-12 text-body">
                <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="font-medium text-heading">Tidak ada data rekap ditemukan.</p>
                <p class="text-xs mt-1">Belum ada data siswa aktif di kelas ini atau tidak ada data yang cocok dengan pencarian.</p>
            </div>
        </div>
    </div>
</div>

<!-- Print Area Container (Hidden on Screen, Visible on Print) -->
<div id="print-area" class="hidden text-black bg-white" style="padding: 0 !important; margin: 0 !important;">
    <!-- Centered Header -->
    <div class="text-center font-bold mb-4" style="margin-bottom: 1rem !important;">
        <h2 class="text-lg uppercase tracking-wide inline-block pb-0.5 mb-1 font-serif font-bold" style="text-decoration: underline; margin-bottom: 0.25rem !important;">REKAPITULASI HASIL CAPAIAN KOMPETENSI</h2>
        <div class="text-base uppercase font-serif font-bold" id="print-school-name">{{ $settings->school_name ?? 'SMP NEGERI 1 TOMPOBULU' }}</div>
        <div class="text-sm uppercase font-serif font-bold" id="print-academic-year-title">TAHUN PELAJARAN 2026/2027</div>
    </div>

    <!-- Metadata Info -->
    <div class="mb-3 text-xs font-serif leading-normal" style="font-family: 'Times New Roman', Times, serif !important; margin-bottom: 0.75rem !important;">
        <div style="display: flex; margin-bottom: 2px;">
            <span style="width: 70px; text-align: left;">Kelas</span>
            <span style="width: 15px; text-align: center;">:</span>
            <span id="print-info-kelas" style="font-weight: bold; text-align: left;">-</span>
        </div>
        <div style="display: flex;">
            <span style="width: 70px; text-align: left;">Semester</span>
            <span style="width: 15px; text-align: center;">:</span>
            <span id="print-info-semester" style="font-weight: bold; text-align: left;">-</span>
        </div>
    </div>

    <!-- Printable Table -->
    <table class="w-full text-xs font-serif border border-black border-collapse" id="print-table">
        <thead>
            <tr class="text-center font-bold font-serif" id="print-header-row1">
                <th class="border border-black py-1" colspan="3">Nomor</th>
                <th class="border border-black py-1" rowspan="2">Nama Peserta Didik</th>
                <!-- Score headers with rowspan="2" dynamically added here -->
                <th class="border border-black py-1" rowspan="2">Jumlah</th>
                <th class="border border-black py-1" rowspan="2">Rata-rata</th>
                <th class="border border-black py-1" rowspan="2">Peringkat</th>
            </tr>
            <tr class="text-center font-bold font-serif" id="print-sub-headers-row">
                <th class="border border-black py-1">Urut</th>
                <th class="border border-black py-1">NIS</th>
                <th class="border border-black py-1">NISN</th>
            </tr>
        </thead>
        <tbody id="print-table-body">
            <!-- Dynamically populated rows -->
        </tbody>
    </table>
</div>

<script>
    // Embed original classes from backend
    const classesData = @json($classes);
    let currentClassId = null;
    let rekapRecords = [];
    let rekapMeetings = null;

    // Show class score recap
    function showClassRekap(classId) {
        const selectedClass = classesData.find(c => c.id === classId);
        if (!selectedClass) return;

        currentClassId = classId;
        
        // Update Title & Subtitle
        document.getElementById('rekap-title').innerText = 'Rekap Nilai Kelas ' + selectedClass.name;
        document.getElementById('rekap-subtitle').innerText = 'Tahun Ajaran ' + 
            (selectedClass.academic_year ? selectedClass.academic_year.year : '-') + ' • Semester ' + 
            (selectedClass.academic_year ? selectedClass.academic_year.semester : '-');

        // Clear search input
        document.getElementById('search-students').value = '';

        // Reset layouts
        document.getElementById('class-list-section').classList.add('hidden');
        document.getElementById('rekap-details-section').classList.remove('hidden');
        
        // Show loader and hide content table
        document.getElementById('rekap-loader').classList.remove('hidden');
        document.getElementById('rekap-table-container').classList.add('hidden');
        document.getElementById('rekap-empty').classList.add('hidden');

        // Fetch scores rekap data via AJAX
        fetch(`/rekap/data/${classId}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            rekapRecords = data.rekap || [];
            rekapMeetings = data.meetings || null;
            
            // Hide Loader
            document.getElementById('rekap-loader').classList.add('hidden');
            
            // Render Headers & Body
            renderRekapTableHeaders();
            renderRekap(rekapRecords);
        })
        .catch(error => {
            console.error('Error fetching recap data:', error);
            document.getElementById('rekap-loader').classList.add('hidden');
            document.getElementById('rekap-empty').classList.remove('hidden');
        });
    }

    // Go back to the classes list cards
    function backToClasses() {
        document.getElementById('rekap-details-section').classList.add('hidden');
        document.getElementById('class-list-section').classList.remove('hidden');
    }

    // Draw dynamic headers under "Komponen Nilai"
    function renderRekapTableHeaders() {
        const subHeadersRow = document.getElementById('sub-headers-row');
        const komponenNilaiHeader = document.getElementById('komponen-nilai-header');
        
        subHeadersRow.innerHTML = '';
        
        if (!rekapMeetings) {
            komponenNilaiHeader.colSpan = 4;
            subHeadersRow.innerHTML = `
                <th scope="col" class="px-3 py-2 w-28">Ulangan Harian</th>
                <th scope="col" class="px-3 py-2 w-28">Tugas</th>
                <th scope="col" class="px-3 py-2 w-28">PTS</th>
                <th scope="col" class="px-3 py-2 w-28">PAS</th>
            `;
            return;
        }

        const dailyTests = rekapMeetings.daily_tests || [];
        const assignments = rekapMeetings.assignments || [];
        const midterms = rekapMeetings.midterms || [];
        const finals = rekapMeetings.finals || [];

        const totalCols = dailyTests.length + assignments.length + midterms.length + finals.length;
        komponenNilaiHeader.colSpan = totalCols > 0 ? totalCols : 1;

        if (totalCols === 0) {
            subHeadersRow.innerHTML = `<th scope="col" class="px-3 py-2 text-fg-disabled italic">-</th>`;
            return;
        }

        // Generate Daily Tests headers
        dailyTests.forEach((meeting, index) => {
            subHeadersRow.innerHTML += `
                <th scope="col" class="px-3 py-2 text-center text-xs font-bold text-heading border-e border-default last:border-e-0 w-24 min-w-[90px]" title="${meeting.title || ''}">
                    UH ${index + 1}
                </th>
            `;
        });

        // Generate Assignments headers
        assignments.forEach((meeting, index) => {
            subHeadersRow.innerHTML += `
                <th scope="col" class="px-3 py-2 text-center text-xs font-bold text-heading border-e border-default last:border-e-0 w-24 min-w-[90px]" title="${meeting.title || ''}">
                    Tugas ${index + 1}
                </th>
            `;
        });

        // Generate Midterm headers
        midterms.forEach((exam, index) => {
            const label = midterms.length > 1 ? `PTS ${index + 1}` : 'PTS';
            subHeadersRow.innerHTML += `
                <th scope="col" class="px-3 py-2 text-center text-xs font-bold text-heading border-e border-default last:border-e-0 w-24 min-w-[90px]" title="${exam.title || ''}">
                    ${label}
                </th>
            `;
        });

        // Generate Final headers
        finals.forEach((exam, index) => {
            const label = finals.length > 1 ? `PAS ${index + 1}` : 'PAS';
            subHeadersRow.innerHTML += `
                <th scope="col" class="px-3 py-2 text-center text-xs font-bold text-heading border-e border-default last:border-e-0 w-24 min-w-[90px]" title="${exam.title || ''}">
                    ${label}
                </th>
            `;
        });
    }

    // Render table rows
    function renderRekap(rekapList) {
        const tableBody = document.getElementById('rekap-table-body');
        const tableContainer = document.getElementById('rekap-table-container');
        const emptyState = document.getElementById('rekap-empty');
        tableBody.innerHTML = '';

        if (rekapList.length === 0) {
            tableContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        tableContainer.classList.remove('hidden');
        emptyState.classList.add('hidden');

        const dailyTests = rekapMeetings ? (rekapMeetings.daily_tests || []) : [];
        const assignments = rekapMeetings ? (rekapMeetings.assignments || []) : [];
        const midterms = rekapMeetings ? (rekapMeetings.midterms || []) : [];
        const finals = rekapMeetings ? (rekapMeetings.finals || []) : [];
        const totalCols = dailyTests.length + assignments.length + midterms.length + finals.length;

        rekapList.forEach((item, index) => {
            const indexStr = String(index + 1).padStart(2, '0');
            const student = item.student;
            
            // Build dynamic scores cells
            let scoresCellsHtml = '';
            
            if (totalCols === 0) {
                scoresCellsHtml = `<td class="px-3 py-4 text-fg-disabled italic">-</td>`;
            } else {
                // Daily Tests
                dailyTests.forEach(meeting => {
                    const score = item.scores.daily_tests[meeting.id] !== undefined ? item.scores.daily_tests[meeting.id] : 0;
                    scoresCellsHtml += `<td class="px-3 py-4 font-semibold text-body font-mono text-sm border-e border-default last:border-e-0">${score.toFixed(2)}</td>`;
                });

                // Assignments
                assignments.forEach(meeting => {
                    const score = item.scores.assignments[meeting.id] !== undefined ? item.scores.assignments[meeting.id] : 0;
                    scoresCellsHtml += `<td class="px-3 py-4 font-semibold text-body font-mono text-sm border-e border-default last:border-e-0">${score.toFixed(2)}</td>`;
                });

                // Midterms
                midterms.forEach(exam => {
                    const score = item.scores.midterms[exam.id] !== undefined ? item.scores.midterms[exam.id] : 0;
                    scoresCellsHtml += `<td class="px-3 py-4 font-semibold text-body font-mono text-sm border-e border-default last:border-e-0">${score.toFixed(2)}</td>`;
                });

                // Finals
                finals.forEach(exam => {
                    const score = item.scores.finals[exam.id] !== undefined ? item.scores.finals[exam.id] : 0;
                    scoresCellsHtml += `<td class="px-3 py-4 font-semibold text-body font-mono text-sm border-e border-default last:border-e-0">${score.toFixed(2)}</td>`;
                });
            }

            // Highlight ranks 1, 2, and 3 with special badges
            let rankBadge = '';
            if (item.peringkat === 1) {
                rankBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs border bg-amber-50 text-amber-800 border-amber-200 font-extrabold dark:bg-amber-950/20 dark:text-amber-300 dark:border-amber-900/30 flex items-center justify-center gap-1 mx-auto max-w-[70px]">
                    🥇 ${item.peringkat}
                </span>`;
            } else if (item.peringkat === 2) {
                rankBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs border bg-slate-50 text-slate-800 border-slate-200 font-extrabold dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700 flex items-center justify-center gap-1 mx-auto max-w-[70px]">
                    🥈 ${item.peringkat}
                </span>`;
            } else if (item.peringkat === 3) {
                rankBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs border bg-orange-50 text-orange-800 border-orange-200 font-extrabold dark:bg-orange-950/20 dark:text-orange-300 dark:border-orange-900/30 flex items-center justify-center gap-1 mx-auto max-w-[70px]">
                    🥉 ${item.peringkat}
                </span>`;
            } else {
                rankBadge = `<span class="text-sm font-semibold text-body select-none">${item.peringkat}</span>`;
            }

            const row = document.createElement('tr');
            row.className = 'hover:bg-neutral-secondary-soft dark:hover:bg-neutral-tertiary transition-colors duration-150 border-b border-default last:border-0 text-center';
            row.innerHTML = `
                <td class="px-4 py-4 font-semibold text-heading select-none">${indexStr}</td>
                <td class="px-6 py-4 font-bold text-heading whitespace-nowrap text-left">${student.name}</td>
                ${scoresCellsHtml}
                <td class="px-3 py-4 font-bold text-heading bg-neutral-secondary-soft/30 font-mono text-sm">${item.jumlah.toFixed(2)}</td>
                <td class="px-3 py-4 font-black text-brand bg-neutral-secondary-soft/50 font-mono text-sm">${item.rata_rata.toFixed(2)}</td>
                <td class="px-4 py-4 text-center">
                    ${rankBadge}
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Filter students by input
    function filterRekap(query) {
        const filtered = rekapRecords.filter(item => {
            const searchStr = (item.student.name + ' ' + (item.student.nis || '') + ' ' + (item.student.nisn || '')).toLowerCase();
            return searchStr.includes(query.toLowerCase());
        });
        renderRekap(filtered);
    }

    // Prepare print table data and open print dialog
    function printRekap() {
        if (!rekapRecords || rekapRecords.length === 0) {
            alert('Tidak ada data untuk dicetak.');
            return;
        }

        const selectedClass = classesData.find(c => c.id === currentClassId);
        const schoolName = "{{ $settings->school_name ?? 'SMP NEGERI 1 TOMPOBULU' }}";
        const academicYear = selectedClass && selectedClass.academic_year ? selectedClass.academic_year.year : '2026/2027';
        const semesterName = selectedClass && selectedClass.academic_year ? selectedClass.academic_year.semester : 'Ganjil';
        
        // Capitalize semester name (Ganjil -> Ganjil, Genap -> Genap, or UPPER)
        const formattedSemester = semesterName.charAt(0).toUpperCase() + semesterName.slice(1).toLowerCase();

        // Update headers in print area
        document.getElementById('print-school-name').innerText = schoolName.toUpperCase();
        document.getElementById('print-academic-year-title').innerText = 'TAHUN PELAJARAN ' + academicYear.toUpperCase();
        document.getElementById('print-info-kelas').innerText = selectedClass ? selectedClass.name : '-';
        document.getElementById('print-info-semester').innerText = formattedSemester;

        // Render dynamic print table headers and body
        renderPrintTableHeaders();
        renderPrintTableBody();

        // Open native print dialog
        window.print();
    }

    // Render print headers to match the design (UH 1, TG 1, etc.)
    function renderPrintTableHeaders() {
        const printSubHeadersRow = document.getElementById('print-sub-headers-row');
        const printHeaderRow1 = document.getElementById('print-header-row1');
        
        // Reset sub headers row
        printSubHeadersRow.innerHTML = `
            <th class="border border-black py-1">Urut</th>
            <th class="border border-black py-1">NIS</th>
            <th class="border border-black py-1">NISN</th>
        `;

        // Reset main header row
        printHeaderRow1.innerHTML = `
            <th class="border border-black py-1" colspan="3">Nomor</th>
            <th class="border border-black py-1" rowspan="2">Nama Peserta Didik</th>
        `;

        if (!rekapMeetings) {
            printHeaderRow1.innerHTML += `
                <th class="border border-black py-1" rowspan="2">Ulangan Harian</th>
                <th class="border border-black py-1" rowspan="2">Tugas</th>
                <th class="border border-black py-1" rowspan="2">PTS</th>
                <th class="border border-black py-1" rowspan="2">PAS</th>
            `;
        } else {
            const dailyTests = rekapMeetings.daily_tests || [];
            const assignments = rekapMeetings.assignments || [];
            const midterms = rekapMeetings.midterms || [];
            const finals = rekapMeetings.finals || [];

            // Add Daily Tests columns (UH 1, UH 2)
            dailyTests.forEach((meeting, index) => {
                printHeaderRow1.innerHTML += `<th class="border border-black py-1" rowspan="2">UH ${index + 1}</th>`;
            });

            // Add Assignments columns (TG 1, TG 2) - "TG" matches screenshot
            assignments.forEach((meeting, index) => {
                printHeaderRow1.innerHTML += `<th class="border border-black py-1" rowspan="2">TG ${index + 1}</th>`;
            });

            // Add PTS
            midterms.forEach((exam, index) => {
                const label = midterms.length > 1 ? `PTS ${index + 1}` : 'PTS';
                printHeaderRow1.innerHTML += `<th class="border border-black py-1" rowspan="2">${label}</th>`;
            });

            // Add PAS
            finals.forEach((exam, index) => {
                const label = finals.length > 1 ? `PAS ${index + 1}` : 'PAS';
                printHeaderRow1.innerHTML += `<th class="border border-black py-1" rowspan="2">${label}</th>`;
            });
        }

        // Add tail columns
        printHeaderRow1.innerHTML += `
            <th class="border border-black py-1" rowspan="2">Jumlah</th>
            <th class="border border-black py-1" rowspan="2">Rata-rata</th>
            <th class="border border-black py-1" rowspan="2">Peringkat</th>
        `;
    }

    // Populate body of printable table with rounded integers to match screenshot
    function renderPrintTableBody() {
        const tbody = document.getElementById('print-table-body');
        tbody.innerHTML = '';

        const dailyTests = rekapMeetings ? (rekapMeetings.daily_tests || []) : [];
        const assignments = rekapMeetings ? (rekapMeetings.assignments || []) : [];
        const midterms = rekapMeetings ? (rekapMeetings.midterms || []) : [];
        const finals = rekapMeetings ? (rekapMeetings.finals || []) : [];
        const totalCols = dailyTests.length + assignments.length + midterms.length + finals.length;

        rekapRecords.forEach((item, index) => {
            const student = item.student;
            let scoresHtml = '';

            if (totalCols === 0) {
                scoresHtml = `<td class="border border-black text-center py-1 font-serif">-</td>`;
            } else {
                // Daily Tests
                dailyTests.forEach(meeting => {
                    const score = item.scores.daily_tests[meeting.id] !== undefined ? item.scores.daily_tests[meeting.id] : 0;
                    scoresHtml += `<td class="border border-black text-center py-1 font-serif">${Math.round(score)}</td>`;
                });

                // Assignments
                assignments.forEach(meeting => {
                    const score = item.scores.assignments[meeting.id] !== undefined ? item.scores.assignments[meeting.id] : 0;
                    scoresHtml += `<td class="border border-black text-center py-1 font-serif">${Math.round(score)}</td>`;
                });

                // Midterms
                midterms.forEach(exam => {
                    const score = item.scores.midterms[exam.id] !== undefined ? item.scores.midterms[exam.id] : 0;
                    scoresHtml += `<td class="border border-black text-center py-1 font-serif">${Math.round(score)}</td>`;
                });

                // Finals
                finals.forEach(exam => {
                    const score = item.scores.finals[exam.id] !== undefined ? item.scores.finals[exam.id] : 0;
                    scoresHtml += `<td class="border border-black text-center py-1 font-serif">${Math.round(score)}</td>`;
                });
            }

            const row = document.createElement('tr');
            row.className = 'font-serif text-center';
            row.innerHTML = `
                <td class="border border-black text-center py-1 font-serif">${index + 1}</td>
                <td class="border border-black text-center py-1 font-serif">${student.nis || ''}</td>
                <td class="border border-black text-center py-1 font-serif">${student.nisn || ''}</td>
                <td class="border border-black text-left px-2 py-1 font-serif whitespace-nowrap">${student.name}</td>
                ${scoresHtml}
                <td class="border border-black text-center py-1 font-serif">${Math.round(item.jumlah)}</td>
                <td class="border border-black text-center py-1 font-serif">${Math.round(item.rata_rata)}</td>
                <td class="border border-black text-center py-1 font-serif">${item.peringkat}</td>
            `;
            tbody.appendChild(row);
        });
    }
</script>
@endsection
