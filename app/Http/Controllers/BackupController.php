<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * Display a listing of the backups.
     */
    public function index(): View
    {
        $disk = Storage::disk('backups');
        $files = $disk->allFiles();

        $backups = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'path' => $file,
                    'filename' => basename($file),
                    'size' => $disk->size($file),
                    'date' => Carbon::createFromTimestamp($disk->lastModified($file)),
                ];
            }
        }

        // Sort backups by date descending
        usort($backups, function (array $a, array $b): int {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        return view('auth.backup', compact('backups'));
    }

    /**
     * Store a newly created backup in storage.
     */
    public function store(): RedirectResponse
    {
        try {
            // Set time limit in case the backup takes some time
            set_time_limit(300);

            // Run only database backup
            $exitCode = Artisan::call('backup:run', ['--only-db' => true]);

            if ($exitCode === 0) {
                return redirect()->route('backup.index')->with('success', 'Backup database berhasil dibuat.');
            }

            return redirect()->route('backup.index')->with('error', 'Gagal membuat backup. Kode Keluar: '.$exitCode);
        } catch (\Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Download the specified backup file.
     */
    public function download(string $filename): StreamedResponse|RedirectResponse
    {
        // Prevent directory traversal
        $filename = basename($filename);
        $folder = config('backup.backup.name', 'Laravel');
        $fullPath = $folder.'/'.$filename;

        $disk = Storage::disk('backups');

        if ($disk->exists($fullPath)) {
            return $disk->download($fullPath);
        }

        return redirect()->route('backup.index')->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * Remove the specified backup file from storage.
     */
    public function destroy(string $filename): RedirectResponse
    {
        // Prevent directory traversal
        $filename = basename($filename);
        $folder = config('backup.backup.name', 'Laravel');
        $fullPath = $folder.'/'.$filename;

        $disk = Storage::disk('backups');

        if ($disk->exists($fullPath)) {
            $disk->delete($fullPath);

            return redirect()->route('backup.index')->with('success', 'File backup berhasil dihapus.');
        }

        return redirect()->route('backup.index')->with('error', 'File backup tidak ditemukan.');
    }
}
