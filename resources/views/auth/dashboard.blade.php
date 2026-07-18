@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
   <!-- Dashboard Container -->
   <div class="p-6 border border-default border-dashed rounded-base bg-white/40 dark:bg-neutral-secondary-medium/20 backdrop-blur-md">
      
      <!-- Top Row Grid: GPA, Completed Subjects, Current Semester -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
         
         <!-- GPA Card -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">GPA / IPK</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">3.85</span>
               <span class="text-sm text-body">/ 4.00</span>
            </div>
            <div class="text-xs text-green-500 font-medium mt-3 flex items-center gap-1">
               <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
               +0.12 this semester
            </div>
         </div>

         <!-- Completed Courses Card -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Completed Courses</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.168.477-4 1.253" /></svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">12</span>
               <span class="text-sm text-body">subjects</span>
            </div>
            <div class="text-xs text-body font-medium mt-3">
               36 total credits (SKS) earned
            </div>
         </div>

         <!-- Current Semester Card -->
         <div class="flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
               <span class="text-sm font-medium text-body">Current Semester</span>
               <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
               </span>
            </div>
            <div class="mt-4">
               <span class="text-3xl font-black text-heading tracking-tight">3<span class="text-lg font-bold">rd</span></span>
               <span class="text-sm text-body">semester</span>
            </div>
            <div class="text-xs text-body font-medium mt-3">
               Active period: 2026/2027
            </div>
         </div>

      </div>

      <!-- Middle Row: Large welcome block & /me endpoint interactive tester -->
      <div class="flex flex-col justify-between p-6 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs mb-6">
         <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-default pb-4 mb-4">
            <div>
               <h2 class="text-xl font-bold text-heading">API Profile Endpoint (/me)</h2>
               <p class="text-xs text-body mt-0.5">Fetches authenticated user data asynchronously</p>
            </div>
            <button onclick="fetchMe()" class="text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm px-4 py-2.5 font-medium shadow-xs focus:outline-none transition-all duration-200 cursor-pointer">
               Fetch Current User Info
            </button>
         </div>
         
         <div id="me-result" class="bg-neutral-secondary-soft border border-default rounded-base p-4 text-xs font-mono text-heading overflow-x-auto min-h-24 flex items-center justify-center">
            <span class="text-body italic text-center">Click the button above to test the "/me" API route</span>
         </div>
      </div>

      <!-- Grid Bottom placeholders -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         <div class="flex flex-col p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-md font-bold text-heading mb-2">Academic Announcements</h3>
            <p class="text-sm text-body leading-relaxed">
               Mid-term examinations will begin on August 10, 2026. Please ensure all grade assignments are submitted before the deadline.
            </p>
         </div>
         <div class="flex flex-col p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-md font-bold text-heading mb-2">Recent Activities</h3>
            <div class="space-y-2 mt-1">
               <div class="flex items-center text-xs justify-between border-b border-default pb-2">
                  <span class="text-body font-medium">Logged in successfully</span>
                  <span class="text-fg-disabled">Just now</span>
               </div>
               <div class="flex items-center text-xs justify-between border-b border-default pb-2">
                  <span class="text-body font-medium">Seeded database with default user</span>
                  <span class="text-fg-disabled">2 mins ago</span>
               </div>
               <div class="flex items-center text-xs justify-between">
                  <span class="text-body font-medium">Created AuthController.php</span>
                  <span class="text-fg-disabled">5 mins ago</span>
               </div>
            </div>
         </div>
      </div>

   </div>

   <!-- Interactive script to fetch /me endpoint -->
   <script>
       async function fetchMe() {
           const resultBox = document.getElementById('me-result');
           resultBox.innerHTML = '<span class="animate-pulse text-body">Fetching user profile...</span>';
           try {
               const response = await fetch('{{ route("me") }}', {
                   headers: {
                       'Accept': 'application/json',
                       'X-Requested-With': 'XMLHttpRequest'
                   }
               });
               if (!response.ok) {
                   throw new Error('Failed to fetch user data');
               }
               const data = await response.json();
               resultBox.innerHTML = `<pre class="w-full text-left">${JSON.stringify(data, null, 4)}</pre>`;
           } catch (error) {
               resultBox.innerHTML = `<span class="text-red-500 font-bold">Error: ${error.message}</span>`;
           }
       }
   </script>
@endsection
