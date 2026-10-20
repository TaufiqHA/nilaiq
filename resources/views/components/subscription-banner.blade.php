@php
    $subscription = auth()->check() ? auth()->user()->currentSubscription() : null;
    $showBanner = $subscription !== null
        && auth()->user()->role !== 'admin'
        && ! request()->routeIs('billing.*')
        && ($subscription->isOnTrial() || ($subscription->ends_at !== null && $subscription->daysLeft() <= 7));
@endphp

@if ($showBanner)
    @php($daysLeft = $subscription->daysLeft())
    <div class="mb-4 flex flex-col gap-3 rounded-base border p-4 sm:flex-row sm:items-center sm:justify-between
        {{ $subscription->isOnTrial()
            ? 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/25 dark:text-amber-300'
            : 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-900/40 dark:bg-sky-950/25 dark:text-sky-300' }}">
        <div class="flex items-start gap-3">
            <x-heroicon-o-clock class="mt-0.5 h-5 w-5 shrink-0" />
            <div>
                <p class="text-sm font-bold">
                    @if ($subscription->isOnTrial())
                        Trial Anda tersisa {{ $daysLeft }} hari
                    @else
                        Langganan Anda berakhir dalam {{ $daysLeft }} hari
                    @endif
                </p>
                <p class="mt-0.5 text-xs opacity-80">
                    @if ($subscription->isOnTrial())
                        Aktifkan langganan agar seluruh data dan fitur tetap bisa diakses setelah trial berakhir.
                    @else
                        Perpanjang sekarang agar akses ke NilaiQ tidak terputus.
                    @endif
                </p>
            </div>
        </div>

        <a href="{{ route('billing.index') }}" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-base bg-brand px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-brand-strong">
            Kelola Langganan
            <x-heroicon-o-arrow-right class="h-3.5 w-3.5" />
        </a>
    </div>
@endif
