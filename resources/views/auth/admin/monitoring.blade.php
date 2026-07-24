@extends('layouts.admin')

@section('title', 'VPS Resource Monitoring')

@section('content')
<!-- Monitoring Container -->
<div class="sm:p-6 sm:border sm:border-default sm:border-dashed sm:rounded-base sm:bg-white/40 sm:dark:bg-neutral-secondary-medium/20 sm:backdrop-blur-md">
    
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-heading tracking-tight">VPS Resource Monitoring</h1>
            <p class="text-sm text-body mt-1">Status dan resource pemakaian server NilaiQ saat ini.</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Auto Refresh Toggle Switch -->
            <div class="flex items-center gap-2 bg-white dark:bg-neutral-primary-soft border border-default px-3 py-2 rounded-base shadow-xs select-none">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="auto-refresh-toggle" class="sr-only peer" checked>
                    <div class="w-9 h-5 bg-neutral-secondary-medium dark:bg-neutral-secondary-medium/40 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                </label>
                <span class="text-xs font-semibold text-body">Auto Refresh (5s)</span>
            </div>

            <!-- Refresh Button -->
            <button id="btn-refresh" class="flex items-center gap-2 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 transition-colors duration-200 cursor-pointer">
                <svg id="refresh-icon" class="w-4 h-4 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Refresh Data</span>
            </button>
        </div>
    </div>

    <!-- Main Resource Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Card: CPU Usage -->
        <div class="resource-card flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-body">CPU Usage</span>
                <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1">
                    <span id="cpu-value" class="text-4xl font-black text-heading tracking-tight">{{ $stats['cpu'] }}</span>
                    <span class="text-lg font-bold text-body">%</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-neutral-secondary-medium dark:bg-neutral-secondary-medium/20 rounded-full h-2 mt-4">
                    <div id="cpu-progress" class="bg-brand h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ $stats['cpu'] }}%"></div>
                </div>
            </div>
            <div class="text-xs text-body font-medium mt-3">
                Load average status
            </div>
        </div>

        <!-- Card: Memory Usage -->
        <div class="resource-card flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-body">Memory Usage</span>
                <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1">
                    <span id="mem-value" class="text-4xl font-black text-heading tracking-tight">{{ $stats['memory']['percentage'] }}</span>
                    <span class="text-lg font-bold text-body">%</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-neutral-secondary-medium dark:bg-neutral-secondary-medium/20 rounded-full h-2 mt-4">
                    <div id="mem-progress" class="bg-brand h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ $stats['memory']['percentage'] }}%"></div>
                </div>
            </div>
            <div class="text-xs text-body font-semibold mt-3 flex justify-between">
                <span>Used: <span id="mem-used" class="text-heading font-black">{{ $stats['memory']['used'] }}</span> GB</span>
                <span>Total: <span id="mem-total">{{ $stats['memory']['total'] }}</span> GB</span>
            </div>
        </div>

        <!-- Card: Disk Usage -->
        <div class="resource-card flex flex-col justify-between p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs hover:border-brand/40 transition-all duration-300">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-body">Disk Space</span>
                <span class="p-1.5 rounded-base bg-brand-soft text-fg-brand">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1">
                    <span id="disk-value" class="text-4xl font-black text-heading tracking-tight">{{ $stats['disk']['percentage'] }}</span>
                    <span class="text-lg font-bold text-body">%</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-neutral-secondary-medium dark:bg-neutral-secondary-medium/20 rounded-full h-2 mt-4">
                    <div id="disk-progress" class="bg-brand h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ $stats['disk']['percentage'] }}%"></div>
                </div>
            </div>
            <div class="text-xs text-body font-semibold mt-3 flex justify-between">
                <span>Used: <span id="disk-used" class="text-heading font-black">{{ $stats['disk']['used'] }}</span> GB</span>
                <span>Total: <span id="disk-total">{{ $stats['disk']['total'] }}</span> GB</span>
            </div>
        </div>

    </div>

    <!-- Details Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Application and Database Health -->
        <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-lg font-bold text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Status Aplikasi & Database
            </h3>
            
            <div class="divide-y divide-default">
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Koneksi Database</span>
                    <span id="db-status-badge" class="px-2.5 py-1 rounded-full text-xs font-bold {{ $stats['db_status'] === 'Healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800' }}">
                        {{ $stats['db_status'] }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Driver Database</span>
                    <span class="text-sm font-bold text-heading uppercase">{{ $stats['system']['database_driver'] }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Driver Cache</span>
                    <span class="text-sm font-bold text-heading uppercase">{{ $stats['system']['cache_driver'] }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Status Server</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                        Online
                    </span>
                </div>
            </div>
            
            @if ($stats['db_error'])
                <div class="mt-4 text-xs bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 p-3 rounded-base border border-red-100 dark:border-red-900 overflow-x-auto">
                    <strong>Error:</strong> {{ $stats['db_error'] }}
                </div>
            @endif
        </div>

        <!-- System Details Specs -->
        <div class="p-5 rounded-base bg-white dark:bg-neutral-primary-soft border border-default shadow-xs">
            <h3 class="text-lg font-bold text-heading mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Spesifikasi Sistem Server
            </h3>

            <div class="divide-y divide-default">
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Sistem Operasi</span>
                    <span class="text-sm font-bold text-heading">{{ $stats['system']['os'] }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">PHP Version</span>
                    <span class="text-sm font-bold text-heading">{{ $stats['system']['php_version'] }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Laravel Version</span>
                    <span class="text-sm font-bold text-heading">v{{ $stats['system']['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Web Server</span>
                    <span class="text-sm font-bold text-heading truncate max-w-[200px]" title="{{ $stats['system']['server_software'] }}">{{ $stats['system']['server_software'] }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-semibold text-body">Server Uptime</span>
                    <span id="sys-uptime" class="text-sm font-bold text-heading">{{ $stats['uptime'] }}</span>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Ajax Auto Refresh Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnRefresh = document.getElementById('btn-refresh');
    const refreshIcon = document.getElementById('refresh-icon');
    const cards = document.querySelectorAll('.resource-card');
    const autoRefreshToggle = document.getElementById('auto-refresh-toggle');
    
    let refreshInterval = null;

    function refreshStats(isAuto = false) {
        if (!isAuto) {
            // Manual refresh UI treatment
            refreshIcon.classList.add('animate-spin');
            btnRefresh.disabled = true;
            btnRefresh.classList.add('opacity-75');
            cards.forEach(card => card.classList.add('opacity-60'));
        } else {
            // Subtle pulse to indicate auto refresh is happening
            btnRefresh.classList.add('opacity-50');
        }

        return fetch('{{ route("admin.monitoring") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Response error');
            }
            return response.json();
        })
        .then(data => {
            // Update CPU Card
            document.getElementById('cpu-value').textContent = data.cpu;
            document.getElementById('cpu-progress').style.width = data.cpu + '%';
            
            const cpuBar = document.getElementById('cpu-progress');
            if (data.cpu > 80) {
                cpuBar.className = 'bg-danger h-2 rounded-full transition-all duration-500 ease-out';
            } else if (data.cpu > 50) {
                cpuBar.className = 'bg-warning h-2 rounded-full transition-all duration-500 ease-out';
            } else {
                cpuBar.className = 'bg-brand h-2 rounded-full transition-all duration-500 ease-out';
            }

            // Update Memory Card
            document.getElementById('mem-value').textContent = data.memory.percentage;
            document.getElementById('mem-progress').style.width = data.memory.percentage + '%';
            document.getElementById('mem-used').textContent = data.memory.used;
            document.getElementById('mem-total').textContent = data.memory.total;
            
            const memBar = document.getElementById('mem-progress');
            if (data.memory.percentage > 85) {
                memBar.className = 'bg-danger h-2 rounded-full transition-all duration-500 ease-out';
            } else if (data.memory.percentage > 65) {
                memBar.className = 'bg-warning h-2 rounded-full transition-all duration-500 ease-out';
            } else {
                memBar.className = 'bg-brand h-2 rounded-full transition-all duration-500 ease-out';
            }

            // Update Disk Card
            document.getElementById('disk-value').textContent = data.disk.percentage;
            document.getElementById('disk-progress').style.width = data.disk.percentage + '%';
            document.getElementById('disk-used').textContent = data.disk.used;
            document.getElementById('disk-total').textContent = data.disk.total;

            const diskBar = document.getElementById('disk-progress');
            if (data.disk.percentage > 90) {
                diskBar.className = 'bg-danger h-2 rounded-full transition-all duration-500 ease-out';
            } else if (data.disk.percentage > 75) {
                diskBar.className = 'bg-warning h-2 rounded-full transition-all duration-500 ease-out';
            } else {
                diskBar.className = 'bg-brand h-2 rounded-full transition-all duration-500 ease-out';
            }

            // Update Database status badge
            const dbBadge = document.getElementById('db-status-badge');
            if (data.db_status === 'Healthy') {
                dbBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800';
                dbBadge.textContent = 'Healthy';
            } else {
                dbBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800';
                dbBadge.textContent = 'Unhealthy';
            }

            // Update Uptime value
            document.getElementById('sys-uptime').textContent = data.uptime;
        })
        .catch(err => {
            console.error('Error refreshing system monitoring stats:', err);
        })
        .finally(() => {
            setTimeout(() => {
                refreshIcon.classList.remove('animate-spin');
                btnRefresh.disabled = false;
                btnRefresh.classList.remove('opacity-75');
                btnRefresh.classList.remove('opacity-50');
                cards.forEach(card => card.classList.remove('opacity-60'));
            }, 350);
        });
    }

    // Toggle logic for auto-refresh
    function startAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        refreshInterval = setInterval(() => {
            refreshStats(true);
        }, 5000); // 5 seconds
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }

    // Event listener for manual refresh click
    btnRefresh.addEventListener('click', function () {
        refreshStats(false);
    });

    // Event listener for auto-refresh toggle change
    autoRefreshToggle.addEventListener('change', function () {
        if (this.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });

    // Initialize Auto Refresh on load
    if (autoRefreshToggle.checked) {
        startAutoRefresh();
    }
});
</script>
@endsection
