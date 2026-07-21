@extends('layouts.waliKelas')

@section('title', 'Dashboard Wali Kelas')

@section('content')
   <!-- Dashboard Container -->
   <div class="p-6 border border-default border-dashed rounded-base bg-white/40 dark:bg-neutral-secondary-medium/20 backdrop-blur-md space-y-6">

      <!-- Stat Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
         
         <!-- Card 1: Total Siswa -->
         <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300 flex flex-col justify-between">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Total Siswa Binaan</span>
               <span class="p-2 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">{{ $totalStudents ?? 0 }}</span>
               <span class="text-sm text-body ml-1">Siswa</span>
            </div>
            <div class="text-xs text-body font-medium mt-3 flex items-center justify-between border-t border-default pt-2">
               <span>Komposisi Siswa</span>
               <span class="font-bold text-emerald-600">{{ $maleStudentsCount ?? 0 }} L / {{ $femaleStudentsCount ?? 0 }} P</span>
            </div>
         </div>

         <!-- Card 2: Presensi Kelas -->
         <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300 flex flex-col justify-between">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Rata-rata Kehadiran</span>
               <span class="p-2 rounded-base bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">{{ $attendanceRate ?? 0 }}%</span>
            </div>
            <div class="text-xs text-body font-medium mt-3 flex items-center justify-between border-t border-default pt-2">
               <span>Rekap Hadir Kelas</span>
               <span class="font-bold text-heading">Semester Ini</span>
            </div>
         </div>

         <!-- Card 3: Tanggal Rapor -->
         <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300 flex flex-col justify-between">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Pembagian Raport</span>
               <span class="p-2 rounded-base bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-xl font-bold text-heading tracking-tight">
                  {{ isset($settingsWaliKelas->tanggal_penerimaan_rapor) ? \Carbon\Carbon::parse($settingsWaliKelas->tanggal_penerimaan_rapor)->format('d M Y') : 'Belum Diatur' }}
               </span>
            </div>
            <div class="text-xs text-body font-medium mt-3 flex items-center justify-between border-t border-default pt-2">
               <span>Jadwal Cetak</span>
               <span class="font-bold text-brand">Master Data</span>
            </div>
         </div>

         <!-- Card 4: Wali Kelas -->
         <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300 flex flex-col justify-between">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Wali Kelas</span>
               <span class="p-2 rounded-base bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-base font-bold text-heading truncate block">
                  {{ $settingsWaliKelas?->teacher_name ?? (auth()->check() ? auth()->user()->name : 'Wali Kelas') }}
               </span>
               <span class="text-xs text-body block truncate mt-0.5">
                  NIP: {{ $settingsWaliKelas?->teacher_nip ?? '-' }}
               </span>
            </div>
            <div class="text-xs text-body font-medium mt-3 flex items-center justify-between border-t border-default pt-2">
               <span>Identitas Resmi</span>
               <span class="font-bold text-emerald-600">Aktif</span>
            </div>
         </div>

      </div>

      <!-- Data Siswa Table Section -->
      <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs space-y-4">
         <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-default pb-4">
            <div>
               <h3 class="text-md font-bold text-heading flex items-center gap-2">
                  <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  Data Siswa Kelas {{ $classWaliKelas ? "({$classWaliKelas->name})" : '' }}
               </h3>
               <p class="text-xs text-body mt-0.5">Daftar peserta didik binaan di kelas Anda yang terdaftar di sistem.</p>
            </div>
            <a href="{{ route('wali-kelas.siswa') }}" class="inline-flex items-center justify-center text-xs font-semibold text-brand hover:text-brand-strong bg-brand-soft hover:bg-brand-soft/80 px-3.5 py-2 rounded-base transition-colors shrink-0">
               <span>Kelola Data Siswa</span>
               <svg class="w-4 h-4 ms-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
               </svg>
            </a>
         </div>

         <div class="overflow-x-auto rounded-base border border-default">
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
                  </tr>
               </thead>
               <tbody class="divide-y divide-default">
                  @forelse($students ?? [] as $index => $student)
                     <tr class="hover:bg-neutral-tertiary/50 transition-colors">
                        <td class="px-4 py-3 text-center font-bold text-heading">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs">
                           <span class="font-bold text-heading">{{ $student->nis }}</span>
                           @if($student->nisn)
                              <span class="block text-[11px] text-body/60">NISN: {{ $student->nisn }}</span>
                           @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-heading">
                           {{ $student->name }}
                        </td>
                        <td class="px-4 py-3 text-center">
                           @if($student->gender === 'L')
                              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Laki-laki</span>
                           @else
                              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-black">Perempuan</span>
                           @endif
                        </td>
                        <td class="px-4 py-3">
                           {{ $student->birth_place }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">{{ $student->religion }}</td>
                        <td class="px-4 py-3 text-center">
                           @if($student->status === 'ACTIVE')
                              <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-black">
                                 Aktif
                              </span>
                           @else
                              <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                 Nonaktif
                              </span>
                           @endif
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-body">
                           <div class="flex flex-col items-center justify-center space-y-1.5">
                              <svg class="w-10 h-10 text-body/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                              </svg>
                              <p class="text-sm font-bold text-heading">Belum Ada Data Siswa</p>
                              <p class="text-xs text-body">Silakan tambahkan data siswa di menu Data Siswa.</p>
                           </div>
                        </td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>

      <!-- Information Details Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
         
         <!-- Card: Profile Wali Kelas & Sekolah -->
         <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs space-y-4">
            <h3 class="text-md font-bold text-heading flex items-center gap-2 border-b border-default pb-3">
               <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
               </svg>
               Informasi Sekolah & Wali Kelas
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
               <div class="p-3 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default">
                  <span class="text-body font-semibold block">NAMA SEKOLAH</span>
                  <span class="text-sm font-bold text-heading block mt-0.5">{{ $settingsWaliKelas?->school_name ?? 'Belum Diatur' }}</span>
               </div>
               <div class="p-3 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default">
                  <span class="text-body font-semibold block">NPSN</span>
                  <span class="text-sm font-bold text-heading block mt-0.5">{{ $settingsWaliKelas?->npsn ?? '-' }}</span>
               </div>
               <div class="p-3 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default">
                  <span class="text-body font-semibold block">KEPALA SEKOLAH</span>
                  <span class="text-sm font-bold text-heading block mt-0.5">{{ $settingsWaliKelas?->principal_name ?? '-' }}</span>
               </div>
               <div class="p-3 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default">
                  <span class="text-body font-semibold block">WALI KELAS</span>
                  <span class="text-sm font-bold text-heading block mt-0.5">{{ $settingsWaliKelas?->teacher_name ?? (auth()->check() ? auth()->user()->name : 'Wali Kelas') }}</span>
               </div>
            </div>

            <div class="p-3 rounded-base bg-neutral-secondary-soft dark:bg-neutral-secondary-medium/40 border border-default text-xs">
               <span class="text-body font-semibold block">ALAMAT SEKOLAH</span>
               <span class="text-heading font-medium block mt-0.5">{{ $settingsWaliKelas?->school_address ?? 'Belum diatur di Master Data' }}</span>
            </div>
         </div>

         <!-- Card: Petunjuk & Catatan Wali Kelas -->
         <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs flex flex-col justify-between space-y-4">
            <div>
               <h3 class="text-md font-bold text-heading flex items-center gap-2 border-b border-default pb-3">
                  <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  Panduan Pengisian Data Wali Kelas
               </h3>
               
               <ul class="space-y-2.5 text-xs text-body mt-3">
                  <li class="flex items-start gap-2">
                     <span class="w-4 h-4 rounded-full bg-brand-soft text-fg-brand font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                     <span>Pastikan konfigurasi di menu <strong>Master Data</strong> terisi dengan lengkap sebelum mencetak raport.</span>
                  </li>
                  <li class="flex items-start gap-2">
                     <span class="w-4 h-4 rounded-full bg-brand-soft text-fg-brand font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                     <span>Input presensi siswa secara rutin melalui menu <strong>Daftar Hadir Kelas</strong>.</span>
                  </li>
                  <li class="flex items-start gap-2">
                     <span class="w-4 h-4 rounded-full bg-brand-soft text-fg-brand font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                     <span>Lengkapi aspek <strong>Sikap</strong>, <strong>Ekstrakurikuler</strong>, dan <strong>Prestasi</strong> sebelum penutupan semester.</span>
                  </li>
                  <li class="flex items-start gap-2">
                     <span class="w-4 h-4 rounded-full bg-brand-soft text-fg-brand font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">4</span>
                     <span>Berikan <strong>Catatan Wali Kelas</strong> untuk tiap siswa sebagai umpan balik perkembangan akademik.</span>
                  </li>
               </ul>
            </div>

            <div class="p-3 bg-brand-soft/50 rounded-base border border-brand/20 text-xs text-body flex items-center justify-between">
               <span class="font-medium">Status Konfigurasi Wali Kelas</span>
               <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ isset($settingsWaliKelas) && $settingsWaliKelas ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                  {{ isset($settingsWaliKelas) && $settingsWaliKelas ? 'Lengkap' : 'Perlu Pengaturan' }}
               </span>
            </div>
         </div>

      </div>

   </div>
@endsection
