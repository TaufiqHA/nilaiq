<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Display the VPS monitoring page or return JSON for AJAX requests.
     */
    public function index(Request $request): View|JsonResponse
    {
        $stats = $this->getServerStats();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($stats);
        }

        return view('auth.admin.monitoring', compact('stats'));
    }

    /**
     * Get real or mock server resource statistics.
     */
    private function getServerStats(): array
    {
        $os = PHP_OS_FAMILY;

        // Default / Mock values for Windows development
        $cpu = 15.4;
        $memory = [
            'total' => 16.00,
            'used' => 6.40,
            'free' => 9.60,
            'percentage' => 40.00,
        ];
        $disk = [
            'total' => 100.00,
            'used' => 25.00,
            'free' => 75.00,
            'percentage' => 25.00,
        ];
        $uptime = 'N/A';

        // Disk Stats (supported on most environments)
        try {
            $diskPath = base_path();
            $diskTotal = @disk_total_space($diskPath);
            $diskFree = @disk_free_space($diskPath);
            if ($diskTotal > 0) {
                $diskUsed = $diskTotal - $diskFree;
                $disk = [
                    'total' => round($diskTotal / (1024 * 1024 * 1024), 2),
                    'used' => round($diskUsed / (1024 * 1024 * 1024), 2),
                    'free' => round($diskFree / (1024 * 1024 * 1024), 2),
                    'percentage' => round(($diskUsed / $diskTotal) * 100, 2),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback stays as default
        }

        // Live stats on Linux systems
        if ($os === 'Linux') {
            // CPU Load
            $loads = sys_getloadavg();
            if ($loads) {
                // Approximate CPU usage percentage from 1-min load average (capped at 100%)
                $cpu = round(min(($loads[0] * 100) / max(1, $this->getCpuCount()), 100), 2);
            }

            // Memory Info
            $freeOutput = @shell_exec('free -b');
            if ($freeOutput) {
                $lines = explode("\n", trim($freeOutput));
                if (isset($lines[1])) {
                    $memInfo = preg_split('/\s+/', $lines[1]);
                    if (count($memInfo) >= 4) {
                        $totalMem = (float) $memInfo[1];
                        $usedMem = (float) $memInfo[2];
                        $freeMem = (float) $memInfo[3];

                        $memory = [
                            'total' => round($totalMem / (1024 * 1024 * 1024), 2),
                            'used' => round($usedMem / (1024 * 1024 * 1024), 2),
                            'free' => round($freeMem / (1024 * 1024 * 1024), 2),
                            'percentage' => round(($usedMem / $totalMem) * 100, 2),
                        ];
                    }
                }
            }

            // Uptime
            $uptimeOutput = @shell_exec('uptime -p');
            if ($uptimeOutput) {
                $uptime = trim(str_replace('up ', '', $uptimeOutput));
            } else {
                $uptimeOutputRaw = @shell_exec('uptime');
                if ($uptimeOutputRaw) {
                    $uptime = trim(explode(',', $uptimeOutputRaw)[0] ?? 'N/A');
                }
            }
        } else {
            // Nicer looking dynamic mock for local Windows testing so metrics aren't static
            $cpu = round(10 + sin(time() / 10) * 5 + rand(0, 10), 2);
            $uptime = 'Local Development System';
        }

        // DB Status Check
        $dbStatus = 'Healthy';
        $dbError = null;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'Unhealthy';
            $dbError = $e->getMessage();
        }

        return [
            'cpu' => $cpu,
            'memory' => $memory,
            'disk' => $disk,
            'uptime' => $uptime,
            'db_status' => $dbStatus,
            'db_error' => $dbError,
            'system' => [
                'os' => PHP_OS,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI Server / Built-in',
                'database_driver' => config('database.default'),
                'cache_driver' => config('cache.default'),
            ],
        ];
    }

    /**
     * Helper to get CPU cores count on Linux.
     */
    private function getCpuCount(): int
    {
        $numCpus = 1;
        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $numCpus = count($matches[0]);
        }

        return $numCpus > 0 ? $numCpus : 1;
    }
}
