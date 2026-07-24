@extends('layouts.main')

@section('title', 'Backup Database')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="hidden sm:block text-3xl font-extrabold text-heading tracking-tight mb-2">Backup Database</h1>
        <p class="text-body">Cadangkan database aplikasi Anda secara manual atau lihat riwayat cadangan yang tersedia.</p>
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

    <!-- Alert Error -->
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/30 rounded-base text-red-800 dark:text-red-300 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
        </svg>
        <span class="font-medium text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 1. Trigger Backup Card -->
    <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="text-center sm:text-left">
                <h3 class="text-lg font-bold text-heading mb-1">Cadangkan Database</h3>
                <p class="text-sm text-body">Mulai proses pencadangan database sekarang untuk mengamankan data akademik.</p>
            </div>
            <form action="{{ route('backup.store') }}" method="POST" id="backup-form" class="w-full sm:w-auto">
                @csrf
                <button type="submit" onclick="event.preventDefault(); startBackupLoading();" id="backup-btn" class="w-full sm:w-auto bg-brand hover:bg-brand-strong text-white px-6 py-2.5 rounded-base text-sm font-bold shadow-md shadow-brand/10 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Backup Baru
                </button>
            </form>
        </div>
        <div id="loading-indicator" class="hidden mt-4 p-4 bg-neutral-secondary-medium border border-default rounded-base text-body text-sm flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-brand shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Sedang mencadangkan database... Mohon tunggu sebentar, proses ini dapat memakan waktu beberapa menit.</span>
        </div>
    </div>

    <!-- 2. Backup History Card -->
    <div class="bg-neutral-primary-soft border border-default rounded-base p-6 shadow-sm">
        <h3 class="text-lg font-bold text-heading border-b border-default pb-3 mb-4">Riwayat Backup</h3>

        @if(count($backups) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-default text-xs uppercase tracking-wider text-body/60 font-bold">
                        <th class="py-3 px-4">Nama File</th>
                        <th class="py-3 px-4">Ukuran</th>
                        <th class="py-3 px-4">Tanggal Dibuat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-neutral-secondary-medium/40 transition-colors">
                        <td class="py-3.5 px-4 font-semibold text-heading truncate max-w-xs" title="{{ $backup['filename'] }}">
                            {{ $backup['filename'] }}
                        </td>
                        <td class="py-3.5 px-4 text-body">
                            {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                        </td>
                        <td class="py-3.5 px-4 text-body">
                            {{ $backup['date']->translatedFormat('d F Y H:i:s') }}
                            <span class="text-xs text-body/60 block">{{ $backup['date']->diffForHumans() }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('backup.download', $backup['filename']) }}" class="px-3 py-1.5 rounded-base text-xs font-bold bg-neutral-tertiary hover:bg-neutral-tertiary-strong text-fg-brand transition-all duration-200 inline-flex items-center gap-1.5" title="Unduh Backup">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Unduh
                                </a>

                                <form action="{{ route('backup.destroy', $backup['filename']) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file backup ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-base text-xs font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30 transition-all duration-200 inline-flex items-center gap-1.5 cursor-pointer" title="Hapus Backup">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 bg-neutral-secondary-medium/30 rounded-base border border-dashed border-default">
            <svg class="mx-auto h-12 w-12 text-neutral-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            <h3 class="text-sm font-bold text-heading mb-1">Belum Ada Backup</h3>
            <p class="text-xs text-body">Belum ada riwayat file backup database yang tersimpan di server.</p>
        </div>
        @endif
    </div>
</div>

<script>
    function startBackupLoading() {
        const btn = document.getElementById('backup-btn');
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        
        const loading = document.getElementById('loading-indicator');
        loading.classList.remove('hidden');
        
        document.getElementById('backup-form').submit();
    }
</script>
@endsection
