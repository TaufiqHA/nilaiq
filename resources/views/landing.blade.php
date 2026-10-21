@php
    $price = $plan?->price ?? 150000;
    $originalPrice = $plan?->original_price ?? 500000;
    $discount = $plan?->discountPercentage() ?? 70;
    $trialDays = (int) config('subscription.trial_days', 3);
    $dashboardRoute = auth()->check()
        ? (auth()->user()->role === 'admin'
            ? route('admin.monitoring')
            : (auth()->user()->role === 'wali_kelas' ? route('wali-kelas.dashboard') : route('dashboard')))
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NilaiQ - Kelola Nilai, Absensi, dan Rapor dalam Satu Aplikasi</title>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <meta name="description" content="NilaiQ membantu guru mapel dan wali kelas mengelola nilai, absensi, ulangan, dan rapor secara otomatis. Coba gratis {{ $trialDays }} hari, tanpa kartu kredit.">
    <meta property="og:title" content="NilaiQ - Aplikasi Administrasi Guru">
    <meta property="og:description" content="Kelola nilai, absensi, dan rapor dalam satu aplikasi. Coba gratis {{ $trialDays }} hari.">
    <meta property="og:type" content="website">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-secondary-medium text-body min-h-screen overflow-x-hidden">
    <!-- Ambient background -->
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 left-1/2 h-[32rem] w-[32rem] -translate-x-1/2 rounded-full bg-brand opacity-[0.10] blur-3xl dark:opacity-[0.18]"></div>
        <div class="absolute top-1/3 -right-40 h-96 w-96 rounded-full bg-brand-strong opacity-[0.08] blur-3xl dark:opacity-[0.14]"></div>
    </div>

    <!-- Navbar -->
    <header class="sticky top-0 z-40 border-b border-default/60 bg-neutral-secondary-medium/80 backdrop-blur-lg">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand shadow-lg shadow-brand/25">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="text-xl font-extrabold tracking-tight text-heading">NilaiQ</span>
            </a>

            <div class="hidden items-center gap-8 text-sm font-medium md:flex">
                <a href="#fitur" class="text-body transition-colors hover:text-fg-brand">Fitur</a>
                <a href="#harga" class="text-body transition-colors hover:text-fg-brand">Harga</a>
                <a href="#faq" class="text-body transition-colors hover:text-fg-brand">FAQ</a>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="theme-toggle rounded-base p-2 text-body transition-colors hover:bg-neutral-tertiary hover:text-fg-brand" title="Ganti tema">
                    <x-heroicon-o-moon class="theme-toggle-dark-icon hidden h-5 w-5" />
                    <x-heroicon-o-sun class="theme-toggle-light-icon hidden h-5 w-5" />
                </button>

                @auth
                    <a href="{{ $dashboardRoute }}" class="rounded-base bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand/25 transition-colors hover:bg-brand-strong">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-base px-3 py-2.5 text-sm font-semibold text-heading transition-colors hover:bg-neutral-tertiary sm:inline-flex">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="rounded-base bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand/25 transition-colors hover:bg-brand-strong">
                        Coba Gratis
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero -->
        <section class="mx-auto grid max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div>
                <h1 class="mt-2 text-4xl font-extrabold leading-tight tracking-tight text-heading sm:text-5xl lg:text-6xl">
                    Administrasi guru,
                    <span class="text-fg-brand">tanpa ribet.</span>
                </h1>

                <p class="mt-6 max-w-xl text-base leading-relaxed text-body sm:text-lg">
                    NilaiQ menyatukan nilai harian, absensi, ulangan, tugas, dan rapor dalam satu aplikasi.
                    Hitung nilai akhir otomatis, cetak rapor lebih cepat, dan fokus kembali mengajar.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @auth
                        <a href="{{ $dashboardRoute }}" class="inline-flex items-center justify-center gap-2 rounded-base bg-brand px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand/25 transition-colors hover:bg-brand-strong">
                            Buka Dashboard
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-base bg-brand px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand/25 transition-colors hover:bg-brand-strong">
                            Mulai Trial {{ $trialDays }} Hari
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-base border border-default bg-neutral-primary-soft px-6 py-3.5 text-sm font-bold text-heading transition-colors hover:bg-neutral-tertiary">
                            Sudah punya akun
                        </a>
                    @endauth
                </div>

                <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-body">
                    <li class="inline-flex items-center gap-2"><x-heroicon-o-check-circle class="h-5 w-5 text-emerald-500" /> Akses penuh saat trial</li>
                    <li class="inline-flex items-center gap-2"><x-heroicon-o-check-circle class="h-5 w-5 text-emerald-500" /> Tanpa auto-debet</li>
                    <li class="inline-flex items-center gap-2"><x-heroicon-o-check-circle class="h-5 w-5 text-emerald-500" /> Data tetap milik Anda</li>
                </ul>
            </div>

            <!-- Product preview -->
            <div class="relative">
                <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-brand/10 blur-2xl"></div>
                <div class="overflow-hidden rounded-2xl border border-default bg-neutral-primary-soft shadow-2xl">
                    <div class="flex items-center justify-between border-b border-default bg-neutral-secondary-soft px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        </div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-body/70">Dashboard NilaiQ</span>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-body">Selamat pagi,</p>
                                <p class="text-lg font-bold text-heading">Bu Sari</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Trial aktif</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl border border-default bg-neutral-secondary-medium p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-body">Kelas</p>
                                <p class="mt-1 text-xl font-extrabold text-heading">6</p>
                            </div>
                            <div class="rounded-xl border border-default bg-neutral-secondary-medium p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-body">Siswa</p>
                                <p class="mt-1 text-xl font-extrabold text-heading">184</p>
                            </div>
                            <div class="rounded-xl border border-default bg-neutral-secondary-medium p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-body">Rata-rata</p>
                                <p class="mt-1 text-xl font-extrabold text-fg-brand">86,4</p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-default p-4">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="text-xs font-bold text-heading">Rata-rata nilai per mapel</p>
                                <span class="text-[10px] text-body">Semester Ganjil</span>
                            </div>
                            <div class="flex h-28 items-end gap-2.5">
                                @foreach ([62, 78, 55, 88, 72, 94, 68] as $bar)
                                    <div class="flex-1 rounded-t-md bg-brand/70" style="height: {{ $bar }}%"></div>
                                @endforeach
                            </div>
                            <div class="mt-2 flex justify-between text-[9px] text-body">
                                <span>MTK</span><span>BIN</span><span>IPA</span><span>IPS</span><span>ENG</span><span>SENI</span><span>PJOK</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-default bg-neutral-secondary-medium px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand/10 text-fg-brand">
                                    <x-heroicon-o-sparkles class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-heading">Scan nilai dengan AI</p>
                                    <p class="text-[10px] text-body">Foto daftar nilai, langsung terisi</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-fg-brand">Coba</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="border-y border-default/60 bg-neutral-primary-soft/60">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-8 sm:grid-cols-3 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-heading sm:text-3xl">1 aplikasi</p>
                    <p class="mt-1 text-xs text-body">untuk semua administrasi</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-heading sm:text-3xl">Otomatis</p>
                    <p class="mt-1 text-xs text-body">hitung nilai akhir</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-extrabold text-heading sm:text-3xl">Sekali klik</p>
                    <p class="mt-1 text-xs text-body">cetak semua rapor</p>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="fitur" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-fg-brand">Fitur Utama</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-heading sm:text-4xl">Semua yang guru butuhkan, dalam satu tempat</h2>
                <p class="mt-4 text-body">Berhenti memindahkan data antar file Excel. NilaiQ merapikan pekerjaan administrasi dari awal semester sampai rapor dibagikan.</p>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['icon' => 'clipboard-document-list', 'title' => 'Absensi & daftar hadir', 'desc' => 'Catat hadir, izin, sakit, dan alpa per pertemuan. Rekap kehadiran otomatis per kelas.'],
                    ['icon' => 'chart-bar', 'title' => 'Nilai harian & ujian', 'desc' => 'Kelola ulangan harian, tugas, PTS, dan PAS. Bobot nilai bisa diatur sesuai kebijakan sekolah.'],
                    ['icon' => 'document-text', 'title' => 'Rapor otomatis', 'desc' => 'Nilai akhir, capaian kompetensi, sikap, dan catatan wali kelas tersusun rapi siap cetak.'],
                    ['icon' => 'sparkles', 'title' => 'Scan nilai dengan AI', 'desc' => 'Foto daftar nilai tulisan tangan, lalu biarkan NilaiQ membacakan dan mengisi skornya.'],
                    ['icon' => 'academic-cap', 'title' => 'Multi kelas & tahun ajaran', 'desc' => 'Pisahkan data per semester dan tahun ajaran. Arsip lama tetap tersimpan dan mudah dibuka.'],
                    ['icon' => 'shield-check', 'title' => 'Backup & privasi data', 'desc' => 'Data tersimpan di akun Anda, lengkap dengan ekspor dan pencadangan database.'],
                ] as $feature)
                    <article class="group rounded-2xl border border-default bg-neutral-primary-soft p-6 transition-all duration-200 hover:-translate-y-0.5 hover:border-brand/40 hover:shadow-lg">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-soft text-fg-brand transition-colors group-hover:bg-brand group-hover:text-white">
                            <x-dynamic-component :component="'heroicon-o-'.$feature['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-5 text-base font-bold text-heading">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-body">{{ $feature['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Personas -->
        <section class="bg-neutral-primary-soft/50 py-20">
            <div class="mx-auto max-w-2xl px-4 text-center sm:px-6 lg:px-8">
                <span class="text-xs font-bold uppercase tracking-widest text-fg-brand">Sesuai Peran Anda</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-heading sm:text-4xl">Satu aplikasi, dua cara kerja</h2>
                <p class="mt-4 text-body">Guru mapel dan wali kelas punya kebutuhan berbeda. NilaiQ menyiapkan tampilan yang langsung sesuai tugas Anda.</p>
            </div>

            <div class="mx-auto mt-12 grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="rounded-2xl border border-default bg-neutral-primary-soft p-8">
                    <span class="inline-flex rounded-lg bg-brand-soft p-2.5 text-fg-brand"><x-heroicon-o-user-group class="h-5 w-5" /></span>
                    <h3 class="mt-4 text-xl font-extrabold text-heading">Untuk Guru Mapel</h3>
                    <p class="mt-2 text-sm text-body">Fokus mengajar, bukan mencatat. Kelola beberapa kelas sekaligus dan biarkan rekap nilai terbentuk otomatis.</p>
                    <ul class="mt-5 space-y-2.5 text-sm text-body">
                        <li class="flex gap-2"><x-heroicon-o-check class="h-5 w-5 shrink-0 text-emerald-500" /> Ulangan harian, tugas, PTS, dan PAS</li>
                        <li class="flex gap-2"><x-heroicon-o-check class="h-5 w-5 shrink-0 text-emerald-500" /> Rekap absensi dan nilai per kelas</li>
                        <li class="flex gap-2"><x-heroicon-o-check class="h-5 w-5 shrink-0 text-emerald-500" /> Scan nilai dari foto dengan AI</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-default bg-neutral-primary-soft p-8">
                    <span class="inline-flex rounded-lg bg-brand-soft p-2.5 text-fg-brand"><x-heroicon-o-academic-cap class="h-5 w-5" /></span>
                    <h3 class="mt-4 text-xl font-extrabold text-heading">Untuk Wali Kelas</h3>
                    <p class="mt-2 text-sm text-body">Semua data kelas dalam satu layar. Dari daftar siswa sampai rapor siap tanda tangan.</p>
                    <ul class="mt-5 space-y-2.5 text-sm text-body">
                        <li class="flex gap-2"><x-heroicon-o-check class="h-5 w-5 shrink-0 text-emerald-500" /> Data siswa, prestasi, dan ekstrakurikuler</li>
                        <li class="flex gap-2"><x-heroicon-o-check class="h-5 w-5 shrink-0 text-emerald-500" /> Sikap, catatan wali kelas, dan capaian</li>
                        <li class="flex gap-2"><x-heroicon-o-check class="h-5 w-5 shrink-0 text-emerald-500" /> Cetak rapor satu kelas sekaligus</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="harga" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-fg-brand">Harga Sederhana</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-heading sm:text-4xl">Satu harga, akses penuh setahun</h2>
                <p class="mt-4 text-body">Mulai dengan trial {{ $trialDays }} hari. Setelahnya, bayar sekali untuk pemakaian 12 bulan. Tanpa biaya tersembunyi.</p>
            </div>

            <div class="mx-auto mt-12 max-w-lg">
                <div class="relative overflow-hidden rounded-3xl border-2 border-brand bg-neutral-primary-soft p-8 shadow-2xl shadow-brand/10">
                    <div class="absolute right-0 top-0 rounded-bl-2xl bg-brand px-4 py-1.5 text-xs font-bold text-white">
                        Hemat {{ $discount }}%
                    </div>

                    <p class="text-sm font-bold uppercase tracking-wider text-fg-brand">NilaiQ Tahunan</p>
                    <div class="mt-4 flex items-end gap-3">
                        <span class="text-4xl font-extrabold tracking-tight text-heading sm:text-5xl">Rp{{ number_format($price, 0, ',', '.') }}</span>
                        <span class="pb-1.5 text-sm text-body">/tahun</span>
                    </div>
                    <p class="mt-2 text-sm text-body">
                        <span class="line-through">Rp{{ number_format($originalPrice, 0, ',', '.') }}</span>
                        <span class="ml-1 font-semibold text-emerald-600 dark:text-emerald-400">Promo peluncuran</span>
                    </p>

                    <ul class="mt-7 space-y-3 text-sm text-body">
                        @foreach ([
                            'Akses penuh seluruh fitur NilaiQ',
                            'Trial gratis '.$trialDays.' hari tanpa kartu kredit',
                            'Guru mapel & wali kelas',
                            'Update fitur selama berlangganan',
                            'Pembayaran aman via Midtrans',
                        ] as $benefit)
                            <li class="flex items-start gap-2.5">
                                <x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ auth()->check() ? $dashboardRoute : route('register') }}" class="mt-8 flex w-full items-center justify-center gap-2 rounded-base bg-brand px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand/25 transition-colors hover:bg-brand-strong">
                        {{ auth()->check() ? 'Buka Dashboard' : 'Mulai Trial '.$trialDays.' Hari' }}
                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </a>
                    <p class="mt-3 text-center text-xs text-body">Tanpa auto-debet. Perpanjangan dilakukan manual saat Anda siap.</p>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="bg-neutral-primary-soft/50 py-20">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-fg-brand">FAQ</span>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-heading">Pertanyaan yang sering diajukan</h2>
                </div>

                <div class="mt-10 space-y-3">
                    @foreach ([
                        ['q' => 'Apa saja fitur yang tersedia di NilaiQ?', 'a' => 'NilaiQ mencakup absensi, nilai ulangan harian, tugas, PTS, PAS, rekap nilai, sikap, prestasi, ekstrakurikuler, hingga cetak rapor.'],
                        ['q' => 'Apakah NilaiQ bisa dipakai guru mapel dan wali kelas?', 'a' => 'Ya. Tersedia dua mode akun: Guru Mapel untuk pengelolaan nilai dan absensi per kelas, serta Wali Kelas untuk data siswa, sikap, catatan, dan rapor satu kelas.'],
                        ['q' => 'Bagaimana cara memasukkan data siswa dan nilai?', 'a' => 'Data siswa bisa ditambahkan manual atau diimpor dari Excel. Nilai dapat diisi langsung per siswa, diimpor, atau dipindai dari foto daftar nilai dengan bantuan AI.'],
                        ['q' => 'Apakah nilai akhir dan rapor dihitung otomatis?', 'a' => 'Ya. Nilai akhir dihitung dari bobot ulangan harian, tugas, PTS, dan PAS yang bisa diatur di Master Data. Rapor dapat dicetak per siswa atau seluruh kelas.'],
                        ['q' => 'Apakah NilaiQ bisa diakses dari HP?', 'a' => 'Bisa. Tampilan NilaiQ responsif untuk HP dan tablet. Guru Mapel juga dapat menggunakan bot Telegram untuk melihat rekap dan mengekspor data.'],
                        ['q' => 'Apakah data saya aman?', 'a' => 'Data hanya dapat diakses setelah login dan terpisah per akun guru. Anda tetap memegang kendali penuh atas data kelas yang dikelola.'],
                    ] as $faq)
                        <details class="group rounded-xl border border-default bg-neutral-primary-soft p-5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-bold text-heading">
                                {{ $faq['q'] }}
                                <x-heroicon-o-chevron-down class="h-5 w-5 shrink-0 text-body transition-transform group-open:rotate-180" />
                            </summary>
                            <p class="mt-3 text-sm leading-relaxed text-body">{{ $faq['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-brand px-6 py-14 text-center shadow-2xl shadow-brand/30 sm:px-12">
                <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-20 -left-10 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <h2 class="relative text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Siap mengajar lebih ringan?</h2>
                <p class="relative mx-auto mt-3 max-w-xl text-sm text-white/85 sm:text-base">Mulai trial {{ $trialDays }} hari sekarang. Tanpa kartu kredit, tanpa komitmen.</p>
                <a href="{{ auth()->check() ? $dashboardRoute : route('register') }}" class="relative mt-8 inline-flex items-center gap-2 rounded-base bg-white px-6 py-3.5 text-sm font-bold text-brand shadow-lg transition-transform hover:-translate-y-0.5">
                    {{ auth()->check() ? 'Buka Dashboard' : 'Coba NilaiQ Gratis' }}
                    <x-heroicon-o-arrow-right class="h-4 w-4" />
                </a>
            </div>
        </section>
    </main>

    <footer class="border-t border-default/60">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 text-sm text-body sm:flex-row sm:px-6 lg:px-8">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="font-bold text-heading">NilaiQ</span>
            </div>
            <p>&copy; {{ date('Y') }} NilaiQ. Dibuat untuk guru Indonesia.</p>
            <div class="flex items-center gap-5">
                <a href="#fitur" class="transition-colors hover:text-fg-brand">Fitur</a>
                <a href="#harga" class="transition-colors hover:text-fg-brand">Harga</a>
                <a href="{{ route('login') }}" class="transition-colors hover:text-fg-brand">Masuk</a>
            </div>
        </div>
    </footer>
</body>
</html>
