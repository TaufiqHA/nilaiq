<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - NilaiQ</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Sign in to NilaiQ to access your academic achievements and grades.">
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Ambient background glows -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand opacity-[0.08] dark:opacity-[0.12] rounded-full blur-3xl -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-brand-strong opacity-[0.08] dark:opacity-[0.12] rounded-full blur-3xl -z-10 pointer-events-none"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md z-10">
        <!-- Logo / Icon -->
        <div class="flex justify-center mb-6">
            <div class="h-12 w-12 rounded-2xl bg-brand flex items-center justify-center shadow-lg shadow-brand/30 transform hover:scale-105 transition-transform duration-200">
                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <h1 class="text-center text-3xl font-extrabold text-heading tracking-tight mb-2">
            NilaiQ
        </h1>
        <p class="text-center text-sm text-body mb-8">
            Academic Grade and Achievement Tracker
        </p>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md z-10 px-4">
        <div class="bg-white/70 dark:bg-neutral-secondary-medium/40 border border-default-medium/60 backdrop-blur-md py-8 px-6 shadow-xl rounded-base sm:px-10">
            <form class="max-w-sm mx-auto" method="POST" action="{{ url('/login') }}">
                @csrf
                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-3.5 rounded-base border border-red-200 dark:border-red-800 animate-pulse" role="alert">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mb-5">
                    <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Your email</label>
                    <input type="email" id="email" name="email" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body transition-colors duration-200" placeholder="name@flowbite.com" required />
                </div>
                <div class="mb-5">
                    <label for="password" class="block mb-2.5 text-sm font-medium text-heading">Your password</label>
                    <input type="password" id="password" name="password" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body transition-colors duration-200" placeholder="••••••••" required />
                </div>
                <button type="submit" class="w-full text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none transition-colors duration-200 cursor-pointer">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
