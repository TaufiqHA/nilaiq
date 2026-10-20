@extends(auth()->user()->role === 'wali_kelas' ? 'layouts.waliKelas' : (auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.main'))

@section('title', 'Langganan & Pembayaran')

@section('content')
@php
    $status = $subscription?->status;
    $isTrial = $subscription?->isOnTrial() ?? false;
    $isActive = $status === \App\Models\Subscription::STATUS_ACTIVE;
    $isLifetime = $subscription?->isLifetime() ?? false;
    $daysLeft = $subscription?->daysLeft() ?? 0;
    $renewalWindowDays = (int) config('subscription.renewal_window_days', 5);
    $canRenew = $isActive && ! $isLifetime && $daysLeft <= $renewalWindowDays;
    $isRenewalLocked = $isActive && ! $isLifetime && $daysLeft > $renewalWindowDays;
    $statusTone = $isTrial
        ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-900/40'
        : ($isActive
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-900/40'
            : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/30 dark:text-red-300 dark:border-red-900/40');
    $clientKey = (string) config('services.midtrans.client_key');
    $snapUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<div class="mx-auto max-w-5xl py-6 sm:py-8">
    <div class="mb-7">
        <h1 class="hidden text-3xl font-extrabold tracking-tight text-heading sm:block">Langganan & Pembayaran</h1>
        <p class="mt-1 text-sm text-body sm:text-base">Kelola masa aktif akun NilaiQ Anda dan lihat riwayat transaksi.</p>
    </div>

    @foreach (['success' => 'emerald', 'error' => 'red', 'info' => 'sky'] as $key => $tone)
        @if (session($key))
            <div class="mb-5 flex items-start gap-3 rounded-base border p-4 text-sm
                {{ $tone === 'emerald' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-950/25 dark:text-emerald-300' : '' }}
                {{ $tone === 'red' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/40 dark:bg-red-950/25 dark:text-red-300' : '' }}
                {{ $tone === 'sky' ? 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-900/40 dark:bg-sky-950/25 dark:text-sky-300' : '' }}">
                <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 shrink-0" />
                <span class="font-medium">{{ session($key) }}</span>
            </div>
        @endif
    @endforeach

    <div class="space-y-6">
        <!-- Status -->
        <section>
            <div class="overflow-hidden rounded-2xl border border-default bg-neutral-primary-soft shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-default p-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-body/70">Status Akun</p>
                        <div class="mt-2 flex items-center gap-3">
                            <h2 class="text-2xl font-extrabold text-heading">
                                {{ $subscription?->statusLabel() ?? 'Tidak Aktif' }}
                            </h2>
                            <span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-bold {{ $statusTone }}">
                                {{ $isTrial ? 'Trial' : ($isActive ? 'Berlangganan' : 'Berakhir') }}
                            </span>
                        </div>
                    </div>

                    <span class="flex h-12 w-12 items-center justify-center rounded-xl {{ $isActive ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-brand-soft text-fg-brand' }}">
                        <x-heroicon-o-credit-card class="h-6 w-6" />
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
                    <div class="rounded-xl border border-default bg-neutral-secondary-medium p-4">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-body/70">Sisa Masa Aktif</p>
                        <p class="mt-1.5 text-xl font-extrabold text-heading">
                            @if ($isLifetime)
                                Selamanya
                            @elseif ($subscription === null)
                                -
                            @else
                                {{ $daysLeft }} hari
                            @endif
                        </p>
                    </div>
                    <div class="rounded-xl border border-default bg-neutral-secondary-medium p-4">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-body/70">Berakhir Pada</p>
                        <p class="mt-1.5 text-sm font-bold text-heading">
                            {{ $isLifetime ? 'Tidak berakhir' : ($subscription?->ends_at?->format('d/m/Y') ?? '-') }}
                        </p>
                    </div>
                </div>

                @if ($isTrial)
                    <div class="mx-6 mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/25 dark:text-amber-300">
                        <p class="font-bold">Trial Anda berakhir dalam {{ $daysLeft }} hari.</p>
                        <p class="mt-1 text-xs leading-relaxed">Berlangganan sekarang untuk menjaga akses ke seluruh data dan fitur NilaiQ. Sisa hari trial tetap dihitung ke masa langganan Anda.</p>
                    </div>
                @elseif ($isActive && ! $isLifetime && $daysLeft <= $renewalWindowDays)
                    <div class="mx-6 mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/25 dark:text-amber-300">
                        <p class="font-bold">Langganan Anda berakhir dalam {{ $daysLeft }} hari.</p>
                        <p class="mt-1 text-xs leading-relaxed">Perpanjang sekarang agar akses tidak terputus. Masa aktif baru ditambahkan setelah masa aktif saat ini.</p>
                    </div>
                @endif

                @if (! $isRenewalLocked)
                <div class="border-t border-default p-6">
                    @if (auth()->user()->role === 'admin')
                        <div class="rounded-xl border border-default bg-neutral-secondary-medium p-4 text-sm text-body">
                            Akun admin memiliki akses penuh tanpa langganan.
                        </div>
                    @elseif ($isLifetime)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-950/25 dark:text-emerald-300">
                            Akun Anda memiliki akses selamanya. Tidak ada pembayaran yang diperlukan.
                        </div>
                    @elseif ($clientKey !== '')
                        <form id="checkout-form" method="POST" action="{{ route('billing.checkout') }}">
                            @csrf
                            <button type="submit" id="pay-button" class="inline-flex w-full items-center justify-center gap-2 rounded-base bg-brand px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand/20 transition-colors hover:bg-brand-strong disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto">
                                <x-heroicon-o-credit-card class="h-5 w-5" />
                                <span id="pay-button-label">
                                    {{ $canRenew ? 'Perpanjang 1 Tahun' : 'Bayar & Aktifkan Langganan' }}
                                </span>
                            </button>
                        </form>
                    @else
                        <div class="rounded-xl border border-default bg-neutral-secondary-medium p-4 text-sm text-body">
                            Pembayaran belum tersedia karena kredensial Midtrans belum dikonfigurasi. Hubungi admin untuk mengaktifkan pembayaran.
                        </div>
                    @endif

                    @if ($pendingOrder !== null)
                        <div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm dark:border-sky-900/40 dark:bg-sky-950/25">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-sky-800 dark:text-sky-300">Menunggu pembayaran</p>
                                <p class="mt-0.5 truncate text-xs text-sky-700 dark:text-sky-400">Order {{ $pendingOrder->order_id }} · Rp{{ number_format($pendingOrder->gross_amount, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ $pendingOrder->snap_redirect_url }}" target="_blank" rel="noopener noreferrer" class="rounded-base bg-sky-600 px-3.5 py-2 text-xs font-bold text-white transition-colors hover:bg-sky-700">
                                Lanjutkan
                            </a>
                            <form method="POST" action="{{ route('billing.sync') }}">
                                @csrf
                                <button type="submit" class="rounded-base border border-sky-300 px-3.5 py-2 text-xs font-bold text-sky-800 transition-colors hover:bg-sky-100 dark:border-sky-800 dark:text-sky-300 dark:hover:bg-sky-900/40">
                                    Cek Status
                                </button>
                            </form>
                        </div>
                    @endif

                    <p class="mt-4 text-xs leading-relaxed text-body">
                        Pembayaran diproses aman oleh Midtrans. Metode yang tersedia meliputi transfer bank/VA, e-wallet, QRIS, dan kartu kredit/debit.
                    </p>
                </div>
                @endif
            </div>
        </section>
    </div>

    <!-- Order history -->
    <section class="mt-8">
        <div class="overflow-hidden rounded-2xl border border-default bg-neutral-primary-soft shadow-sm">
            <div class="flex items-center justify-between border-b border-default px-6 py-4">
                <h2 class="text-sm font-bold text-heading">Riwayat Transaksi</h2>
                <span class="text-xs text-body">{{ $orders->count() }} transaksi</span>
            </div>

            @if ($orders->isEmpty())
                <div class="p-8 text-center">
                    <x-heroicon-o-document-text class="mx-auto h-8 w-8 text-body/40" />
                    <p class="mt-2 text-sm text-body">Belum ada transaksi. Pembayaran pertama Anda akan muncul di sini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-default bg-neutral-secondary-soft text-[10px] uppercase tracking-wider text-body/70">
                            <tr>
                                <th class="px-6 py-3 font-bold">Order ID</th>
                                <th class="px-6 py-3 font-bold">Tanggal</th>
                                <th class="px-6 py-3 font-bold">Nominal</th>
                                <th class="px-6 py-3 font-bold">Metode</th>
                                <th class="px-6 py-3 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-default">
                            @foreach ($orders as $order)
                                @php
                                    $badge = match ($order->status) {
                                        \App\Models\Order::STATUS_SETTLEMENT, \App\Models\Order::STATUS_CAPTURE => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300',
                                        \App\Models\Order::STATUS_PENDING => 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300',
                                        \App\Models\Order::STATUS_REFUND => 'bg-sky-50 text-sky-700 dark:bg-sky-950/30 dark:text-sky-300',
                                        default => 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-300',
                                    };
                                @endphp
                                <tr class="transition-colors hover:bg-neutral-secondary-soft/60">
                                    <td class="px-6 py-3.5 font-mono text-xs text-heading">{{ $order->order_id }}</td>
                                    <td class="px-6 py-3.5 text-xs text-body">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3.5 text-xs font-bold text-heading">Rp{{ number_format($order->gross_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3.5 text-xs uppercase text-body">{{ $order->payment_type ?? '-' }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $badge }}">{{ $order->statusLabel() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</div>

@if ($clientKey !== '')
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        (function () {
            const form = document.getElementById('checkout-form');
            const button = document.getElementById('pay-button');
            const label = document.getElementById('pay-button-label');
            const originalLabel = label ? label.textContent : '';

            if (!form || !button || typeof window.snap === 'undefined') {
                return;
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                button.disabled = true;
                if (label) {
                    label.textContent = 'Menyiapkan pembayaran...';
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok || !data.token) {
                        throw new Error(data.message || 'Gagal menyiapkan pembayaran.');
                    }

                    window.snap.pay(data.token, {
                        onSuccess: function (result) {
                            window.location.href = '{{ route('billing.finish') }}?order_id=' + encodeURIComponent(result.order_id);
                        },
                        onPending: function (result) {
                            window.location.href = '{{ route('billing.finish') }}?order_id=' + encodeURIComponent(result.order_id);
                        },
                        onError: function () {
                            window.location.href = '{{ route('billing.index') }}';
                        },
                        onClose: function () {
                            button.disabled = false;
                            if (label) {
                                label.textContent = originalLabel;
                            }
                        },
                    });
                } catch (error) {
                    button.disabled = false;
                    if (label) {
                        label.textContent = originalLabel;
                    }
                    alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        })();
    </script>
@endif
@endsection
