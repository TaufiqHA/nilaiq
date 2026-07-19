<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - NilaiQ</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Manage your academic tracking and view grades on NilaiQ.">
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium min-h-screen relative overflow-x-hidden">
    <!-- Mobile Header / Top Bar -->
    <div class="flex items-center gap-3 p-3 sm:hidden bg-neutral-primary-soft border-b border-default sticky top-0 z-30">
        <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-base text-sm p-2 focus:outline-none">
           <span class="sr-only">Open sidebar</span>
           <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10"/>
           </svg>
        </button>
        <span class="text-lg font-bold text-heading">@yield('title')</span>
    </div>

    <!-- Sidebar Inclusion -->
    @include('components.sidebar')

    <!-- Main Content Wrapper -->
    <div class="p-4 sm:ml-64">
        @yield('content')
    </div>
</body>
</html>
