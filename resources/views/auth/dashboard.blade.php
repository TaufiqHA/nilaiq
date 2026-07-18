@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
   <!-- Dashboard Container -->
   <div class="p-6 border border-default border-dashed rounded-base bg-white/40 dark:bg-neutral-secondary-medium/20 backdrop-blur-md">
      
      <!-- Top Row Grid: Siswa, Kelas, Tahun Ajaran, Kehadiran -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
         
         <!-- Card: Siswa Aktif -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Siswa Aktif</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">{{ $totalStudents }}</span>
               <span class="text-sm text-body">siswa</span>
            </div>
            <div class="text-xs text-body font-medium mt-3">
               Terdaftar aktif dalam sistem
            </div>
         </div>

         <!-- Card: Jumlah Kelas -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Jumlah Kelas</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">{{ $totalClasses }}</span>
               <span class="text-sm text-body">kelas</span>
            </div>
            <div class="text-xs text-body font-medium mt-3">
               Tingkat rombongan belajar
            </div>
         </div>

         <!-- Card: Tahun Ajaran -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Tahun Ajaran</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               @if($activeAcademicYear)
                  <span class="text-2xl font-black text-heading tracking-tight">{{ $activeAcademicYear->year }}</span>
                  <span class="text-xs font-bold text-brand uppercase block mt-1">Semester {{ $activeAcademicYear->semester }}</span>
               @else
                  <span class="text-xl font-bold text-heading">Belum Aktif</span>
                  <span class="text-xs text-body block mt-1">Konfigurasi di Menu Tahun Ajaran</span>
               @endif
            </div>
            <div class="text-xs text-body font-medium mt-3">
               Periode akademik aktif
            </div>
         </div>

         <!-- Card: Rata-rata Kehadiran -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Rata-rata Kehadiran</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">{{ $attendanceRate }}%</span>
            </div>
            <div class="text-xs text-body font-medium mt-3">
               Dari seluruh riwayat absensi kelas
            </div>
         </div>

      </div>

      <!-- Middle Row: Informasi Pelajaran & KKM + Ringkasan Evaluasi -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
         
         <!-- KKM & Pelajaran Card -->
         <div class="lg:col-span-1 p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col justify-between">
            <div>
               <h3 class="text-md font-bold text-heading mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.168.477-4 1.253" />
                  </svg>
                  Informasi Pembelajaran
               </h3>
               
               <div class="space-y-3 mt-4">
                  <div>
                     <span class="text-xs font-semibold text-body block">MATA PELAJARAN</span>
                     <span class="text-lg font-bold text-heading">{{ $settings?->subject_name ?? 'Belum Diatur' }}</span>
                  </div>
                  <div class="border-t border-default pt-3">
                     <span class="text-xs font-semibold text-body block">KKM (BATAS MINIMAL)</span>
                     <span class="text-lg font-bold text-brand">{{ $settings?->minimum_score ? number_format($settings->minimum_score, 0) : 'Belum Diatur' }}</span>
                  </div>
                  <div class="border-t border-default pt-3">
                     <span class="text-xs font-semibold text-body block">GURU PENGAMPU</span>
                     <span class="text-sm font-medium text-heading">{{ $settings?->teacher_name ?? 'Belum Diatur' }}</span>
                  </div>
               </div>
            </div>
            <div class="text-xs text-body italic mt-4">
               *Dapat diubah di menu Pengaturan
            </div>
         </div>

         <!-- Ringkasan Evaluasi / Kegiatan -->
         <div class="lg:col-span-2 p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-md font-bold text-heading mb-4 flex items-center gap-2">
               <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
               </svg>
               Ringkasan Agenda Penilaian
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-2">
               
               <div class="p-4 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default text-center">
                  <span class="text-3xl font-black text-heading block">{{ $dailyTestCount }}</span>
                  <span class="text-xs font-semibold text-body">Ulangan Harian</span>
               </div>
               
               <div class="p-4 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default text-center">
                  <span class="text-3xl font-black text-heading block">{{ $assignmentCount }}</span>
                  <span class="text-xs font-semibold text-body">Tugas Mandiri</span>
               </div>

               <div class="p-4 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default text-center">
                  <span class="text-3xl font-black text-heading block">{{ $midtermCount }}</span>
                  <span class="text-xs font-semibold text-body">Ujian Tengah Sem.</span>
               </div>

               <div class="p-4 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default text-center">
                  <span class="text-3xl font-black text-heading block">{{ $finalCount }}</span>
                  <span class="text-xs font-semibold text-body">Ujian Akhir Sem.</span>
               </div>

            </div>

            <div class="mt-5 p-4 bg-brand-soft rounded-base border border-brand/20 text-xs text-body flex items-start gap-2">
               <svg class="w-4 h-4 text-brand shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
               </svg>
               <div>
                  Bobot penilaian aktif saat ini: 
                  <strong>Ulangan Harian</strong> ({{ $settings?->daily_test_weight ? number_format($settings->daily_test_weight * 100, 0) : '0' }}%), 
                  <strong>Tugas</strong> ({{ $settings?->assignment_weight ? number_format($settings->assignment_weight * 100, 0) : '0' }}%), 
                  <strong>UTS</strong> ({{ $settings?->midterm_weight ? number_format($settings->midterm_weight * 100, 0) : '0' }}%), 
                  <strong>UAS</strong> ({{ $settings?->final_weight ? number_format($settings->final_weight * 100, 0) : '0' }}%).
               </div>
            </div>
         </div>

      </div>


      <!-- Grid Bottom: Siswa Terbaru & Aktivitas Terbaru -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         
         <!-- Siswa Terbaru -->
         <div class="flex flex-col p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-md font-bold text-heading mb-3 flex items-center gap-2">
               <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
               </svg>
               Siswa Terbaru
            </h3>
            
            <div class="space-y-2 mt-1">
               @forelse($latestStudents as $student)
                  <div class="flex items-center text-xs justify-between border-b border-default pb-2">
                     <div>
                        <span class="text-heading font-bold block">{{ $student->name }}</span>
                        <span class="text-body text-[10px]">NIS: {{ $student->nis }} | Kelas: {{ $student->class?->name ?? 'N/A' }}</span>
                     </div>
                     <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $student->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $student->status }}
                     </span>
                  </div>
               @empty
                  <div class="text-center py-4 text-xs text-body italic">
                     Belum ada data siswa.
                  </div>
               @endforelse
            </div>
         </div>

         <!-- Kegiatan/Agenda Terbaru -->
         <div class="flex flex-col p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-md font-bold text-heading mb-3 flex items-center gap-2">
               <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
               </svg>
               Agenda Penilaian Terbaru
            </h3>
            
            <div class="space-y-2 mt-1">
               @forelse($recentActivities as $activity)
                  <div class="flex items-center text-xs justify-between border-b border-default pb-2">
                     <div>
                        <span class="text-heading font-bold block">{{ $activity['title'] }}</span>
                        <span class="text-body text-[10px]">{{ $activity['type'] }} | Kelas: {{ $activity['class'] }}</span>
                     </div>
                     <span class="text-fg-disabled text-[10px]">
                        {{ $activity['date'] ? $activity['date']->format('d M Y') : '-' }}
                     </span>
                  </div>
               @empty
                  <div class="text-center py-4 text-xs text-body italic">
                     Belum ada agenda penilaian.
                  </div>
               @endforelse
            </div>
         </div>

      </div>

   </div>

@endsection

