<!-- Sidebar Aside -->
<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default flex flex-col justify-between">
      <div class="space-y-4">
         <!-- Brand Header -->
         <div class="flex items-center gap-3 px-2 py-1 mb-6">
            <div class="h-8 w-8 rounded-xl bg-danger flex items-center justify-center shadow-sm shadow-danger/20">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="font-extrabold text-lg text-heading tracking-tight">NilaiQ Admin</span>
         </div>
         
         <!-- Navigation Links Grouped -->
         <div class="space-y-6">
            <!-- Group: Monitoring -->
            <div>
               <span class="px-2 mb-2 block text-[11px] font-bold uppercase tracking-wider text-body/60">Sistem</span>
               <ul class="space-y-1 font-medium">
                  <li>
                     <a href="{{ route('admin.monitoring') }}" class="flex items-center px-2 py-1.5 {{ request()->routeIs('admin.monitoring') ? 'bg-neutral-tertiary text-fg-brand font-bold' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }} rounded-base group transition-all duration-200">
                        <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('admin.monitoring') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3M3 12a48.664 48.664 0 01.138-3.662m0 0l3 3m-3-3l-3 3" />
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9 17h6M9 13h6M12 9v10M9 21h6" />
                        </svg>
                        <span class="ms-3">VPS Monitoring</span>
                     </a>
                  </li>
               </ul>
            </div>
         </div>
      </div>
      
      <!-- Quick User profile info footer inside sidebar -->
      <div class="border-t border-default pt-4 flex items-center justify-between gap-3">
         <a href="{{ route('profile') }}" class="flex items-center gap-3 min-w-0 hover:opacity-85 hover:text-fg-brand transition-all duration-200 group">
            <div class="h-9 w-9 rounded-full bg-danger flex items-center justify-center font-bold text-white shadow-sm text-sm shrink-0 transition-colors">
               AD
            </div>
            <div class="min-w-0">
               <p class="text-sm font-bold text-heading truncate group-hover:text-fg-brand transition-colors">{{ auth()->user()->name }}</p>
               <p class="text-xs text-body truncate group-hover:text-fg-brand/80 transition-colors">{{ auth()->user()->email }}</p>
            </div>
         </a>
         
         <div class="flex items-center gap-1 shrink-0">
            <!-- Theme Toggle Button -->
            <button type="button" class="theme-toggle text-body hover:text-fg-brand hover:bg-neutral-tertiary p-1.5 rounded-base transition-all duration-200 cursor-pointer" title="Toggle Dark/Light Mode">
               <!-- Moon icon (shows in light mode) -->
               <x-heroicon-o-moon class="theme-toggle-dark-icon hidden w-5 h-5" />
               <!-- Sun icon (shows in dark mode) -->
               <x-heroicon-o-sun class="theme-toggle-light-icon hidden w-5 h-5" />
            </button>
 
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
   </div>
</aside>
