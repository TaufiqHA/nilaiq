<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Orang Tua - NilaiQ</title>
    
    <!-- Inline script to prevent FOUC (flash of incorrect theme) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Meta tags for SEO -->
    <meta name="description" content="Portal Orang Tua NilaiQ - Pantau perkembangan belajar, nilai raport, dan absensi putra-putri Anda.">
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium min-h-screen relative overflow-x-hidden">
    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden w-full">
        <!-- Ambient background glows -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand opacity-[0.08] dark:opacity-[0.12] rounded-full blur-3xl -z-10 pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-brand-strong opacity-[0.08] dark:opacity-[0.12] rounded-full blur-3xl -z-10 pointer-events-none"></div>

        <div class="w-full max-w-md mx-auto z-10">
            <!-- Logo / Icon -->
            <div class="flex justify-center mb-6">
                <div class="h-12 w-12 rounded-2xl bg-brand flex items-center justify-center shadow-lg shadow-brand/30 transform hover:scale-105 transition-transform duration-200">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
            <h1 class="text-center text-3xl font-extrabold text-heading tracking-tight mb-2">
                Portal Orang Tua
            </h1>
            <p class="text-center text-sm text-body mb-8 px-4">
                Masukkan Nama, NIS, dan NISN siswa untuk melihat data perkembangan belajar.
            </p>
        </div>

        <div class="w-full max-w-md mx-auto z-10">
            <div class="bg-white/70 dark:bg-neutral-secondary-medium/40 border border-default-medium/60 backdrop-blur-md py-8 px-6 shadow-xl rounded-base sm:px-10">
                <form class="max-w-sm mx-auto space-y-5" method="POST" action="{{ route('portal-ortu.search') }}">
                    @csrf
                    
                    <!-- Search Error Alert -->
                    @if ($errors->has('search_error'))
                        <div class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-3.5 rounded-base border border-red-200 dark:border-red-800" role="alert">
                            {{ $errors->first('search_error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="text-sm text-emerald-800 dark:text-emerald-400 bg-emerald-50 dark:bg-neutral-primary-soft p-3.5 rounded-base border border-emerald-200 dark:border-emerald-800" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div>
                        <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Nama Lengkap Siswa</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body transition-colors duration-200" 
                            placeholder="Contoh: Budi Santoso" required autocomplete="off" />
                        @error('name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nis" class="block mb-2.5 text-sm font-medium text-heading">NIS (Nomor Induk Siswa)</label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis') }}" 
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body transition-colors duration-200" 
                            placeholder="Contoh: 12345" required autocomplete="off" />
                        @error('nis')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nisn" class="block mb-2.5 text-sm font-medium text-heading">NISN (Nomor Induk Siswa Nasional)</label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" 
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body transition-colors duration-200" 
                            placeholder="Contoh: 0098765432" required autocomplete="off" />
                        @error('nisn')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-bold leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none transition-colors duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Periksa Perkembangan Belajar
                    </button>
                </form>
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-xs text-body hover:text-brand font-medium transition-colors duration-200">
                    Masuk sebagai Guru atau Wali Kelas &rarr;
                </a>
            </div>
        </div>
    </div>
</body>
</html>
