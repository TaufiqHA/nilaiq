@php($trialDays = (int) config('subscription.trial_days', 3))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - NilaiQ</title>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <meta name="description" content="Daftar NilaiQ dan nikmati trial gratis {{ $trialDays }} hari untuk mengelola nilai, absensi, dan rapor.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium min-h-screen relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-24 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-brand opacity-[0.10] blur-3xl dark:opacity-[0.16]"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-brand-strong opacity-[0.08] blur-3xl dark:opacity-[0.12]"></div>
    </div>

    <div class="flex min-h-screen flex-col justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-lg">
            <div class="mb-7 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand shadow-lg shadow-brand/25">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <span class="text-xl font-extrabold tracking-tight text-heading">NilaiQ</span>
                </a>

                <button type="button" class="theme-toggle rounded-base p-2 text-body transition-colors hover:bg-neutral-tertiary hover:text-fg-brand" title="Ganti tema">
                    <x-heroicon-o-moon class="theme-toggle-dark-icon hidden h-5 w-5" />
                    <x-heroicon-o-sun class="theme-toggle-light-icon hidden h-5 w-5" />
                </button>
            </div>

            <div class="rounded-2xl border border-default bg-neutral-primary-soft p-6 shadow-xl sm:p-8">
                <span class="inline-flex items-center gap-2 rounded-full border border-brand/20 bg-brand-soft px-3 py-1 text-[11px] font-bold text-fg-brand">
                    <x-heroicon-o-sparkles class="h-3.5 w-3.5" />
                    Trial {{ $trialDays }} hari gratis
                </span>

                <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-heading sm:text-3xl">Buat akun guru Anda</h1>
                <p class="mt-2 text-sm text-body">Akses penuh semua fitur selama {{ $trialDays }} hari. Tanpa kartu kredit.</p>

                @if ($errors->any())
                    <div class="mt-5 rounded-base border border-red-200 bg-red-50 p-3.5 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-950/30 dark:text-red-300" role="alert">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-heading">Nama lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="block w-full rounded-base border border-default bg-neutral-secondary-medium px-3.5 py-2.5 text-sm text-heading placeholder:text-body/60 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Sari Wulandari">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-heading">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                               class="block w-full rounded-base border border-default bg-neutral-secondary-medium px-3.5 py-2.5 text-sm text-heading placeholder:text-body/60 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 @error('email') border-red-500 @enderror"
                               placeholder="nama@sekolah.sch.id">
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-heading">Password</label>
                            <input type="password" id="password" name="password" required autocomplete="new-password"
                                   class="block w-full rounded-base border border-default bg-neutral-secondary-medium px-3.5 py-2.5 text-sm text-heading placeholder:text-body/60 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 @error('password') border-red-500 @enderror"
                                   placeholder="Minimal 8 karakter">
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-heading">Ulangi password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                   class="block w-full rounded-base border border-default bg-neutral-secondary-medium px-3.5 py-2.5 text-sm text-heading placeholder:text-body/60 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20"
                                   placeholder="Ketik ulang password">
                        </div>
                    </div>

                    <fieldset>
                        <legend class="mb-2 block text-sm font-semibold text-heading">Saya mendaftar sebagai</legend>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <input type="radio" id="role-mapel" name="role" value="mapel" class="peer sr-only" @checked(old('role', 'mapel') === 'mapel')>
                                <label for="role-mapel" class="flex h-full cursor-pointer items-start gap-3 rounded-xl border-2 border-default bg-neutral-secondary-medium p-4 transition-colors hover:border-brand/50 peer-checked:border-brand peer-checked:bg-brand-soft peer-focus-visible:ring-2 peer-focus-visible:ring-brand/30">
                                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand/10 text-fg-brand">
                                        <x-heroicon-o-user-group class="h-5 w-5" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-bold text-heading">Guru Mapel</span>
                                        <span class="mt-0.5 block text-xs text-body">Nilai, absensi, ulangan, dan tugas per kelas.</span>
                                    </span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" id="role-wali" name="role" value="wali_kelas" class="peer sr-only" @checked(old('role') === 'wali_kelas')>
                                <label for="role-wali" class="flex h-full cursor-pointer items-start gap-3 rounded-xl border-2 border-default bg-neutral-secondary-medium p-4 transition-colors hover:border-brand/50 peer-checked:border-brand peer-checked:bg-brand-soft peer-focus-visible:ring-2 peer-focus-visible:ring-brand/30">
                                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand/10 text-fg-brand">
                                        <x-heroicon-o-academic-cap class="h-5 w-5" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-bold text-heading">Wali Kelas</span>
                                        <span class="mt-0.5 block text-xs text-body">Data siswa, sikap, catatan, dan rapor kelas.</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        @error('role')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-base bg-brand px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand/20 transition-colors hover:bg-brand-strong focus:outline-none focus:ring-4 focus:ring-brand/30">
                        Mulai Trial {{ $trialDays }} Hari
                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </button>

                    <p class="text-center text-[11px] leading-relaxed text-body">
                        Dengan mendaftar, Anda menyetujui penggunaan NilaiQ untuk keperluan administrasi pembelajaran.
                        Trial tidak diperpanjang otomatis dan tidak memerlukan metode pembayaran.
                    </p>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-body">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-fg-brand hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
