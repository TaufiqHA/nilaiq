@extends(auth()->user()->role === 'wali_kelas' ? 'layouts.waliKelas' : 'layouts.main')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="hidden sm:block text-3xl font-extrabold text-heading tracking-tight mb-2">Pengaturan Profil</h1>
        <p class="text-body">Kelola informasi pribadi dan keamanan akun Anda.</p>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 rounded-base text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="space-y-8">
        <!-- 1. Profile Information Card -->
        <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-heading border-b border-default pb-2">Informasi Profil</h3>
            
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('name') border-fg-danger-strong @enderror" placeholder="Nama Lengkap">
                        @error('name')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('email') border-fg-danger-strong @enderror" placeholder="email@contoh.com">
                        @error('email')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Peran Akun</label>
                    <input type="text" value="{{ $user->role === 'wali_kelas' ? 'Wali Kelas' : 'Guru Mata Pelajaran' }}" class="w-full bg-neutral-secondary-medium/50 border border-default rounded-base px-3 py-2 text-sm text-fg-disabled cursor-not-allowed" disabled>
                    <p class="text-[11px] text-body mt-1">Peran akun tidak dapat diubah sendiri untuk alasan keamanan.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white text-sm font-bold rounded-base hover:bg-brand-strong focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Security & Password Card -->
        <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-heading border-b border-default pb-2">Ubah Password</h3>
            
            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('current_password') border-fg-danger-strong @enderror" placeholder="••••••••">
                    @error('current_password')
                        <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Password Baru</label>
                        <input type="password" name="password" id="password" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand @error('password') border-fg-danger-strong @enderror" placeholder="••••••••">
                        @error('password')
                            <p class="text-xs text-fg-danger-strong mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-heading uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-neutral-secondary-medium border border-default rounded-base px-3 py-2 text-sm text-heading placeholder-neutral-400 focus:outline-none focus:border-brand" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white text-sm font-bold rounded-base hover:bg-brand-strong focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>

        @if($user->role === 'mapel')
        <!-- 3. Telegram Bot Card -->
        <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-default pb-2">
                <h3 class="text-lg font-bold text-heading">Telegram Bot</h3>
                @if($telegramLink)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Terhubung</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-secondary-medium text-body border border-default">Belum terhubung</span>
                @endif
            </div>

            @if(session('error'))
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-base text-rose-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($telegramLink)
                <p class="text-sm text-body">
                    Akun NilaiQ Anda terhubung ke chat Telegram
                    @if($telegramLink->telegram_username)
                        <span class="font-semibold text-heading">&#64;{{ $telegramLink->telegram_username }}</span>
                    @else
                        <span class="font-semibold text-heading">pribadi</span>
                    @endif
                    sejak {{ $telegramLink->linked_at?->translatedFormat('d F Y H:i') ?? '-' }}.
                </p>
                <p class="text-xs text-body">Buka Telegram, lalu chat bot dan ketik <code class="px-1 py-0.5 bg-neutral-secondary-medium rounded">/bantuan</code> untuk melihat contoh pertanyaan.</p>

                <form action="{{ route('profile.telegram.unlink') }}" method="POST" class="pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-rose-600 text-white text-sm font-bold rounded-base hover:bg-rose-700 focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                        Putuskan Koneksi Telegram
                    </button>
                </form>
            @else
                <p class="text-sm text-body">
                    Hubungkan akun NilaiQ ke Telegram agar Anda bisa bertanya tentang kehadiran, nilai, dan rekap kelas langsung dari chat bot, termasuk mengunduh rekap dalam bentuk Excel.
                </p>

                @if(session('telegram_link_url'))
                    <div class="p-4 bg-brand/5 border border-brand/20 rounded-base space-y-3">
                        <p class="text-sm font-semibold text-heading">Kode berhasil dibuat (berlaku 15 menit):</p>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input id="telegram-link-url" type="text" readonly value="{{ session('telegram_link_url') }}" class="flex-1 w-full bg-white border border-default rounded-base px-3 py-2 text-xs text-heading focus:outline-none focus:border-brand">
                            <button type="button" onclick="copyTelegramLink(this)" class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white text-sm font-bold rounded-base hover:bg-brand-strong transition-all duration-200 cursor-pointer">
                                Salin Link
                            </button>
                        </div>
                        <p class="text-xs text-body">
                            Atau buka Telegram dan kirim perintah:
                            <code class="px-1 py-0.5 bg-neutral-secondary-medium rounded">/start {{ session('telegram_link_code') }}</code>
                        </p>
                    </div>
                @endif

                <form action="{{ route('profile.telegram.link') }}" method="POST" class="pt-2">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-brand text-white text-sm font-bold rounded-base hover:bg-brand-strong focus:outline-none transition-all duration-200 cursor-pointer shadow-sm">
                        Hubungkan Telegram
                    </button>
                </form>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
    function copyTelegramLink(button) {
        const input = document.getElementById('telegram-link-url');

        if (!input) {
            return;
        }

        navigator.clipboard.writeText(input.value).then(function () {
            const original = button.innerText;
            button.innerText = 'Tersalin';
            setTimeout(function () {
                button.innerText = original;
            }, 1500);
        });
    }
</script>
@endsection
