<!-- Sidebar Aside -->
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
            <span class="font-extrabold text-lg text-heading tracking-tight">NilaiQ</span>
         </div>
         
         <!-- Navigation Links Grouped -->
         <div class="space-y-6">
            <!-- Group: Menu Utama -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Menu Utama</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('dashboard') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/></svg>
                        <span class="ms-3">Dashboard</span>
                     </a>
                  </li>
               </ul>
            </div>

            <!-- Group: Akademik -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Akademik</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ route('academic-years.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('academic-years.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('academic-years.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Tahun Ajaran</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ route('classes.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('classes.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('classes.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2m14 0V9a2 2 0 0 0-2-2M5 11V9a2 2 0 0 1 2-2m0 0V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M7 7h10"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Kelas</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ route('attendance-meetings.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('attendance-meetings.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('attendance-meetings.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Absensi</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ route('daily-test-meetings.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('daily-test-meetings.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('daily-test-meetings.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-7.5-3-7.5-3v13.75c0 .104.058.2.15.25 1.125.625 5.432 2.378 7.35 3M12 6.03c2.819-.831 7.5-3 7.5-3v13.75c0 .104-.058.2-.15.25-1.125.625-5.432 2.378-7.35 3M12 19.03V20"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Ulangan Harian</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ route('assignment-meetings.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('assignment-meetings.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('assignment-meetings.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Tugas</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ route('midterm-exams.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('midterm-exams.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('midterm-exams.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m2-1h2a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm-2 7h6m-6 4h6"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">PTS</span>
                     </a>
                  </li>
                  <li>
                     <a href="{{ route('final-exams.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('final-exams.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('final-exams.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 7h6m-6 4h6m-6 4h3"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">PAS</span>
                     </a>
                  </li>
               </ul>
            </div>

            <!-- Group: Pengaturan -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Pengaturan</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ route('master-data.index') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('master-data.*') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="shrink-0 w-5 h-5 transition duration-75 {{ request()->routeIs('master-data.*') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
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
         <div class="flex items-center gap-3 min-w-0">
            <div class="h-9 w-9 rounded-full bg-brand flex items-center justify-center font-bold text-white shadow-sm text-sm shrink-0">
               {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div class="min-w-0">
               <p class="text-sm font-bold text-heading truncate">{{ auth()->user()->name }}</p>
               <p class="text-xs text-body truncate">{{ auth()->user()->email }}</p>
            </div>
         </div>
         
         <!-- Laravel Logout Button -->
         <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
            @csrf
         </form>
         <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-body hover:text-fg-brand hover:bg-neutral-tertiary p-1.5 rounded-base transition-all duration-200 cursor-pointer" title="Sign Out">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
               <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>
            </svg>
         </button>
      </div>
   </div>
</aside>
