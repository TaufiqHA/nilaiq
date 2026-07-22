<!-- Sidebar Aside Wali Kelas -->
<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default flex flex-col justify-between">
      <div class="space-y-4">
         <!-- Brand Header -->
         <div class="flex items-center gap-3 px-2 py-1 mb-6">
            <div class="h-8 w-8 rounded-xl bg-brand flex items-center justify-center shadow-sm shadow-brand/20">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="font-extrabold text-lg text-heading tracking-tight leading-none">NilaiQ</span>
                <span class="text-[10px] font-bold text-fg-brand tracking-wider uppercase mt-1">Wali Kelas</span>
            </div>
         </div>
         
         <!-- Navigation Links Grouped -->
         <div class="space-y-5">
            <!-- Group 1: Utama -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Utama</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ Route::has('wali-kelas.dashboard') ? route('wali-kelas.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('dashboard', 'wali-kelas.dashboard') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0 transition duration-75 {{ request()->routeIs('dashboard', 'wali-kelas.dashboard') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/>
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/>
                        </svg>
                        <span class="ms-3">Dashboard</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.informasi-kelas') ? route('wali-kelas.informasi-kelas') : (Route::has('informasi-kelas.index') ? route('informasi-kelas.index') : '#') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('informasi-kelas', 'informasi-kelas.*', 'wali-kelas.informasi-kelas', 'wali-kelas.informasi-kelas.*', 'wali-kelas.class-wali-kelas.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="w-5 h-5 shrink-0 transition duration-75 {{ request()->routeIs('informasi-kelas', 'informasi-kelas.*', 'wali-kelas.informasi-kelas', 'wali-kelas.informasi-kelas.*', 'wali-kelas.class-wali-kelas.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/>
                        </svg>
                        <span class="ms-3">Informasi Kelas</span>
                     </a>
                  </li>
               </ul>
            </div>

            <!-- Group 2: Data Siswa & Perilaku -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Data Siswa</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ Route::has('wali-kelas.siswa') ? route('wali-kelas.siswa') : (Route::has('students.index') ? route('students.index') : '#') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('siswa', 'siswa.*', 'students.*', 'wali-kelas.siswa', 'wali-kelas.siswa.*', 'wali-kelas.student-wali-kelas.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('siswa', 'siswa.*', 'students.*', 'wali-kelas.siswa', 'wali-kelas.siswa.*', 'wali-kelas.student-wali-kelas.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12h4m-2 2v-4M4 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Siswa</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.ekstrakurikuler') ? route('wali-kelas.ekstrakurikuler') : (Route::has('wali-kelas.ekskuls.index') ? route('wali-kelas.ekskuls.index') : (Route::has('ekstrakurikuler.index') ? route('ekstrakurikuler.index') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('ekstrakurikuler', 'ekstrakurikuler.*', 'wali-kelas.ekstrakurikuler', 'wali-kelas.ekstrakurikuler.*', 'wali-kelas.ekskuls.*', 'ekskuls.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('ekstrakurikuler', 'ekstrakurikuler.*', 'wali-kelas.ekstrakurikuler', 'wali-kelas.ekstrakurikuler.*', 'wali-kelas.ekskuls.*', 'ekskuls.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414M18.364 18.364l-1.414-1.414M7.05 7.05L5.636 5.636"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Ekstrakurikuler</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.prestasi') ? route('wali-kelas.prestasi') : (Route::has('wali-kelas.prestasis.index') ? route('wali-kelas.prestasis.index') : (Route::has('prestasi.index') ? route('prestasi.index') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('prestasi.*', 'wali-kelas.prestasi', 'wali-kelas.prestasi.*', 'wali-kelas.prestasis.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('prestasi.*', 'wali-kelas.prestasi', 'wali-kelas.prestasi.*', 'wali-kelas.prestasis.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15l-3.5 2 1-4-3-3 4-.5L12 6l1.5 3.5 4 .5-3 3 1 4z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Prestasi</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.sikap') ? route('wali-kelas.sikap') : (Route::has('wali-kelas.sikaps.index') ? route('wali-kelas.sikaps.index') : (Route::has('sikap.index') ? route('sikap.index') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('sikap', 'sikap.*', 'wali-kelas.sikap', 'wali-kelas.sikap.*', 'wali-kelas.sikaps.*', 'sikaps.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('sikap', 'sikap.*', 'wali-kelas.sikap', 'wali-kelas.sikap.*', 'wali-kelas.sikaps.*', 'sikaps.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Sikap</span>
                     </a>
                  </li>
               </ul>
            </div>

            <!-- Group 3: Akademik & Penilaian -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Akademik & Nilai</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ Route::has('wali-kelas.absensi') ? route('wali-kelas.absensi') : (Route::has('wali-kelas.absensis.index') ? route('wali-kelas.absensis.index') : (Route::has('wali-kelas.daftar-hadir') ? route('wali-kelas.daftar-hadir') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('absensi', 'absensi.*', 'wali-kelas.absensi', 'wali-kelas.absensi.*', 'wali-kelas.absensis.*', 'absensis.*', 'daftar-hadir.*', 'wali-kelas.daftar-hadir.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('absensi', 'absensi.*', 'wali-kelas.absensi', 'wali-kelas.absensi.*', 'wali-kelas.absensis.*', 'absensis.*', 'daftar-hadir.*', 'wali-kelas.daftar-hadir.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Daftar Hadir Kelas</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.catatan-wali-kelas') ? route('wali-kelas.catatan-wali-kelas') : (Route::has('wali-kelas.catatan-wali-kelas.index') ? route('wali-kelas.catatan-wali-kelas.index') : (Route::has('wali-kelas.catatan') ? route('wali-kelas.catatan') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('catatan-wali-kelas', 'catatan-wali-kelas.*', 'wali-kelas.catatan-wali-kelas', 'wali-kelas.catatan-wali-kelas.*', 'wali-kelas.catatan.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('catatan-wali-kelas', 'catatan-wali-kelas.*', 'wali-kelas.catatan-wali-kelas', 'wali-kelas.catatan-wali-kelas.*', 'wali-kelas.catatan.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-7.5-3-7.5-3v13.75c0 .104.058.2.15.25 1.125.625 5.432 2.378 7.35 3M12 6.03c2.819-.831 7.5-3 7.5-3v13.75c0 .104-.058.2-.15.25-1.125.625-5.432 2.378-7.35 3M12 19.03V20"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Catatan Wali Kelas</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.nilai-mapel') ? route('wali-kelas.nilai-mapel') : (Route::has('wali-kelas.nilai') ? route('wali-kelas.nilai') : (Route::has('nilai.index') ? route('nilai.index') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('nilai', 'nilai.*', 'nilai-mapel', 'nilai-mapel.*', 'wali-kelas.nilai', 'wali-kelas.nilai.*', 'wali-kelas.nilai-mapel', 'wali-kelas.nilai-mapels.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('nilai', 'nilai.*', 'nilai-mapel', 'nilai-mapel.*', 'wali-kelas.nilai', 'wali-kelas.nilai.*', 'wali-kelas.nilai-mapel', 'wali-kelas.nilai-mapels.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Nilai</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.raport') ? route('wali-kelas.raport') : (Route::has('raport.index') ? route('raport.index') : '#') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('raport.*', 'wali-kelas.raport.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('raport.*', 'wali-kelas.raport.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 7h6m-6 4h6m-6 4h3"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Raport</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ Route::has('wali-kelas.rekap-nilai') ? route('wali-kelas.rekap-nilai') : '#' }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('wali-kelas.rekap-nilai') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('wali-kelas.rekap-nilai') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Rekap Nilai</span>
                     </a>
                  </li>
               </ul>
            </div>

            <!-- Group 4: Pengaturan -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Pengaturan</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ Route::has('wali-kelas.master-data') ? route('wali-kelas.master-data') : (Route::has('settings-wali-kelas.index') ? route('settings-wali-kelas.index') : (Route::has('master-data.index') ? route('master-data.index') : '#')) }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('settings-wali-kelas.*', 'wali-kelas.master-data.*', 'master-data.*', 'wali-kelas.mapel-settings.*', 'mapel-settings.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('settings-wali-kelas.*', 'wali-kelas.master-data.*', 'master-data.*', 'wali-kelas.mapel-settings.*', 'mapel-settings.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6c0 1.657-3.134 3-7 3S5 7.657 5 6m14 0c0-1.657-3.134-3-7-3S5 4.343 5 6m14 0v6c0 1.657-3.134 3-7 3s-7-1.343-7-3V6m14 6v6c0 1.657-3.134 3-7 3s-7-1.343-7-3v-6"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Master Data</span>
                     </a>
                  </li>
               </ul>
            </div>
         </div>
      </div>
      
      <!-- Quick User profile info footer inside sidebar -->
      <div class="border-t border-default pt-4 flex items-center justify-between gap-3">
         <a href="{{ route('profile') }}" class="flex items-center gap-3 min-w-0 hover:opacity-85 hover:text-fg-brand transition-all duration-200 group">
            <div class="h-9 w-9 rounded-full bg-brand flex items-center justify-center font-bold text-white shadow-sm text-sm shrink-0 group-hover:bg-brand-strong transition-colors">
               {{ auth()->check() ? substr(auth()->user()->name, 0, 2) : 'WK' }}
            </div>
            <div class="min-w-0">
               <p class="text-sm font-bold text-heading truncate group-hover:text-fg-brand transition-colors">{{ auth()->check() ? auth()->user()->name : 'Wali Kelas' }}</p>
               <p class="text-xs text-body truncate group-hover:text-fg-brand/80 transition-colors">{{ auth()->check() ? auth()->user()->email : 'walikelas@nilaiq.id' }}</p>
            </div>
         </a>
         
         <!-- Laravel Logout Button -->
         @if(Route::has('logout'))
         <form action="{{ route('logout') }}" method="POST" id="logout-form-wk" class="hidden">
            @csrf
         </form>
         <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form-wk').submit();" class="text-body hover:text-fg-brand hover:bg-neutral-tertiary p-1.5 rounded-base transition-all duration-200 cursor-pointer" title="Sign Out">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
               <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>
            </svg>
         </button>
         @endif
      </div>
   </div>
</aside>
