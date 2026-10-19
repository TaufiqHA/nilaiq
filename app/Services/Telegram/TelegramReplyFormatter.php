<?php

namespace App\Services\Telegram;

use App\Models\Classes;
use Illuminate\Support\Collection;

/**
 * Formats bot responses as Telegram HTML.
 */
class TelegramReplyFormatter
{
    /**
     * @return array{text: string, keyboard: array<string, mixed>|null}
     */
    public function classSelection(Collection $classes, ?int $contextClassId = null): array
    {
        if ($classes->isEmpty()) {
            return [
                'text' => 'Belum ada kelas pada tahun ajaran aktif Anda.',
                'keyboard' => null,
            ];
        }

        $lines = ['<b>Pilih kelas</b>', 'Kelas aktif saat ini: '.$this->className($classes, $contextClassId), ''];
        $buttons = [];

        foreach ($classes as $class) {
            $buttons[] = [
                'text' => ($contextClassId === $class->id ? '✅ ' : '').$class->name,
                'callback_data' => 'c:'.$class->id,
            ];
        }

        return [
            'text' => implode("\n", $lines),
            'keyboard' => ['inline_keyboard' => array_chunk($buttons, 2)],
        ];
    }

    /**
     * @param  Collection<int, Classes>  $classes
     */
    public function className(Collection $classes, ?int $classId): string
    {
        if ($classId === null) {
            return 'belum dipilih';
        }

        $class = $classes->firstWhere('id', $classId);

        return $class?->name ?? 'belum dipilih';
    }

    public function onboarding(): string
    {
        $url = rtrim((string) config('app.url'), '/');

        return implode("\n", [
            '<b>Bot NilaiQ</b>',
            '',
            'Akun Telegram Anda belum terhubung. Buka website NilaiQ, masuk ke halaman <b>Profil</b>, lalu klik <b>Hubungkan Telegram</b>.',
            '',
            'Setelah terhubung, Anda bisa bertanya tentang kehadiran, nilai, dan rekap kelas langsung dari chat ini.',
            '',
            'Website: '.$this->escape($url),
        ]);
    }

    public function linked(string $name): string
    {
        return implode("\n", [
            '✅ <b>Berhasil terhubung</b>',
            '',
            'Akun NilaiQ <b>'.$this->escape($name).'</b> sekarang terhubung ke chat ini.',
            '',
            'Ketik /bantuan untuk melihat contoh pertanyaan.',
        ]);
    }

    public function help(): string
    {
        return implode("\n", [
            '<b>Bantuan Bot NilaiQ</b>',
            '',
            'Contoh pertanyaan:',
            '• kehadiran terendah di kelas 7A',
            '• siapa yang paling sering alfa di kelas ini',
            '• rekap kehadiran kelas 7A',
            '• nilai PTS terendah di kelas 7A',
            '• rekap nilai kelas 7A',
            '• nilai Budi',
            '• rekap lengkap kelas 7A',
            '',
            'Perintah:',
            '/kelas — lihat atau pilih kelas',
            '/kelas 7A — jadikan 7A sebagai kelas aktif',
            '/rekap — rekap lengkap kelas aktif',
            '/status — status akun dan kelas aktif',
            '/putuskan — putuskan koneksi Telegram',
        ]);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    public function attendanceSummary(array $summary, bool $full, int $limit = 10): string
    {
        /** @var Classes $class */
        $class = $summary['class'];
        $rows = $summary['rows'];
        $lines = [
            '<b>Rekap Kehadiran — '.$this->escape($class->name).'</b>',
            'Pertemuan tercatat: '.$summary['meeting_count'],
            'Rata-rata kehadiran: '.$this->percent($summary['average_percentage']),
            '',
        ];

        $shown = $full ? $rows : array_slice($rows, 0, $limit);

        if ($shown === []) {
            $lines[] = 'Belum ada siswa aktif di kelas ini.';
        }

        foreach ($shown as $row) {
            $lines[] = '<b>'.$this->escape($row['student']->name).'</b> — '.$this->attendanceLine($row);
        }

        if (! $full && count($rows) > count($shown)) {
            $lines[] = '';
            $lines[] = '…dan '.(count($rows) - count($shown)).' siswa lainnya. Ketik /rekap atau minta "rekap lengkap" untuk semua.';
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function attendanceRanking(string $title, array $summary, array $rows, int $limit): string
    {
        /** @var Classes $class */
        $class = $summary['class'];
        $lines = [
            '<b>'.$this->escape($title).' — '.$this->escape($class->name).'</b>',
            'Pertemuan tercatat: '.$summary['meeting_count'],
            '',
        ];

        if ($rows === []) {
            $lines[] = 'Belum ada data kehadiran untuk kelas ini.';

            return implode("\n", $lines);
        }

        $rank = 1;
        foreach (array_slice($rows, 0, $limit) as $row) {
            $lines[] = $rank.'. <b>'.$this->escape($row['student']->name).'</b> — '.$this->attendanceLine($row);
            $rank++;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function studentAttendance(array $row, string $className, int $meetingCount): string
    {
        return implode("\n", [
            '<b>Kehadiran '.$this->escape($row['student']->name).'</b>',
            'Kelas: '.$this->escape($className),
            'Pertemuan tercatat: '.$meetingCount,
            '',
            $this->attendanceLine($row),
        ]);
    }

    /**
     * @param  array<string, mixed>  $recap
     */
    public function scoreRecap(array $recap, bool $full, int $limit = 10): string
    {
        /** @var Classes $class */
        $class = $recap['class'];
        $lines = [
            '<b>Rekap Nilai — '.$this->escape($class->name).'</b>',
            $this->assessmentSummaryLine($recap),
            '',
        ];

        $rows = $recap['rows'];
        $shown = $full ? $rows : array_slice($rows, 0, $limit);

        if ($shown === []) {
            $lines[] = 'Belum ada nilai untuk kelas ini.';
        }

        foreach ($shown as $row) {
            $lines[] = $row['peringkat'].'. <b>'.$this->escape($row['student']->name).'</b> — rata-rata '
                .$this->number($row['rata_rata']).' | nilai akhir '.$this->scoreOrDash($row['final_score']);
        }

        if (! $full && count($rows) > count($shown)) {
            $lines[] = '';
            $lines[] = '…dan '.(count($rows) - count($shown)).' siswa lainnya. Minta "rekap nilai lengkap" untuk semua.';
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $recap
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function averageRanking(string $title, array $recap, array $rows, int $limit): string
    {
        /** @var Classes $class */
        $class = $recap['class'];
        $lines = [
            '<b>'.$this->escape($title).' — '.$this->escape($class->name).'</b>',
            $this->assessmentSummaryLine($recap),
            '',
        ];

        if ($rows === []) {
            $lines[] = 'Belum ada nilai yang cukup untuk membuat peringkat.';

            return implode("\n", $lines);
        }

        $rank = 1;
        foreach (array_slice($rows, 0, $limit) as $row) {
            $lines[] = $rank.'. <b>'.$this->escape($row['student']->name).'</b> — rata-rata '
                .$this->number($row['rata_rata']).' | peringkat kelas '.$row['peringkat'];
            $rank++;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $recap
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function assessmentRanking(string $title, array $recap, array $rows, int $limit): string
    {
        /** @var Classes $class */
        $class = $recap['class'];
        $lines = [
            '<b>'.$this->escape($title).' — '.$this->escape($class->name).'</b>',
            '',
        ];

        if ($rows === []) {
            $lines[] = 'Belum ada nilai untuk jenis penilaian tersebut.';

            return implode("\n", $lines);
        }

        $rank = 1;
        foreach (array_slice($rows, 0, $limit) as $row) {
            $lines[] = $rank.'. <b>'.$this->escape($row['student']->name).'</b> — '
                .$this->number($row['average']).' ('.$row['count'].' nilai)';
            $rank++;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $recap
     * @param  array<string, mixed>  $row
     */
    public function studentScore(array $recap, array $row, ?string $assessmentType = null): string
    {
        /** @var Classes $class */
        $class = $recap['class'];

        $lines = [
            '<b>Nilai '.$this->escape($row['student']->name).'</b>',
            'Kelas: '.$this->escape($class->name),
            'Peringkat: '.$row['peringkat'].' dari '.count($recap['rows']),
            '',
        ];

        if ($assessmentType !== null) {
            $type = $row['type_scores'][$assessmentType] ?? null;
            $lines[] = $this->assessmentLabel($assessmentType).': '
                .($type !== null && $type['average'] !== null
                    ? $this->number($type['average']).' ('.$type['count'].' nilai)'
                    : 'belum ada nilai');
        } else {
            foreach ($this->assessmentTypes() as $type => $label) {
                $data = $row['type_scores'][$type] ?? null;
                $lines[] = $label.': '
                    .($data !== null && $data['average'] !== null
                        ? $this->number($data['average']).' ('.$data['count'].' nilai)'
                        : 'belum ada nilai');
            }
        }

        $lines[] = '';
        $lines[] = 'Total nilai: '.$this->number($row['jumlah']);
        $lines[] = 'Rata-rata: '.$this->number($row['rata_rata']);
        $lines[] = 'Nilai akhir: '.$this->scoreOrDash($row['final_score']);

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $recap
     * @param  array<string, mixed>  $attendance
     */
    public function fullRecap(array $recap, array $attendance, bool $full, int $limit = 10): string
    {
        /** @var Classes $class */
        $class = $recap['class'];
        $attendanceRows = collect($attendance['rows'])->keyBy(fn (array $row): int => $row['student']->id);

        $lines = [
            '<b>Rekap Lengkap — '.$this->escape($class->name).'</b>',
            $this->assessmentSummaryLine($recap),
            'Rata-rata kehadiran: '.$this->percent($attendance['average_percentage']),
            '',
        ];

        $rows = $recap['rows'];
        $shown = $full ? $rows : array_slice($rows, 0, $limit);

        if ($shown === []) {
            $lines[] = 'Belum ada data untuk kelas ini.';
        }

        foreach ($shown as $row) {
            $attendanceRow = $attendanceRows->get($row['student']->id);
            $lines[] = $row['peringkat'].'. <b>'.$this->escape($row['student']->name).'</b>'
                .' — rata-rata '.$this->number($row['rata_rata'])
                .' | hadir '.$this->percent($attendanceRow['percentage'] ?? null)
                .' | alfa '.($attendanceRow['alfa'] ?? 0);
        }

        if (! $full && count($rows) > count($shown)) {
            $lines[] = '';
            $lines[] = '…dan '.(count($rows) - count($shown)).' siswa lainnya. Ketik /rekap untuk rekap lengkap.';
        }

        return implode("\n", $lines);
    }

    public function status(string $name, string $role, string $className, ?string $telegramUsername): string
    {
        return implode("\n", [
            '<b>Status Akun</b>',
            'Nama: '.$this->escape($name),
            'Role: '.$this->escape($role),
            'Kelas aktif: '.$this->escape($className),
            'Telegram: '.($telegramUsername ? '@'.$this->escape($telegramUsername) : 'terhubung'),
        ]);
    }

    public function unlinked(): string
    {
        return 'Koneksi Telegram berhasil diputuskan. Untuk menghubungkan lagi, buka halaman Profil di website NilaiQ.';
    }

    public function roleNotSupported(): string
    {
        return 'Bot ini saat ini hanya tersedia untuk akun Guru Mapel.';
    }

    public function privateChatOnly(): string
    {
        return 'Bot ini hanya bisa digunakan melalui chat pribadi (private chat).';
    }

    public function rateLimited(int $seconds): string
    {
        return 'Terlalu banyak pesan. Coba lagi dalam '.$seconds.' detik.';
    }

    public function notReady(): string
    {
        return 'Bot belum siap: konfigurasi Telegram belum lengkap. Hubungi admin.';
    }

    public function noClasses(): string
    {
        return 'Belum ada kelas pada tahun ajaran aktif Anda. Tambahkan kelas terlebih dahulu melalui website NilaiQ.';
    }

    public function cannotFindClass(): string
    {
        return 'Kelas tidak ditemukan pada tahun ajaran aktif Anda. Ketik /kelas untuk melihat daftar kelas.';
    }

    public function cannotFindStudent(): string
    {
        return 'Siswa tidak ditemukan atau namanya ambigu. Sebutkan nama lengkap siswa, misalnya: nilai Budi Santoso.';
    }

    /**
     * Split a long message into Telegram-safe chunks.
     *
     * @return array<int, string>
     */
    public function chunk(string $text, int $limit = 3800): array
    {
        $text = trim($text);

        if ($text === '') {
            return ['Tidak ada data untuk ditampilkan.'];
        }

        if (mb_strlen($text) <= $limit) {
            return [$text];
        }

        $chunks = [];
        $current = '';

        foreach (explode("\n", $text) as $line) {
            $candidate = $current === '' ? $line : $current."\n".$line;

            if (mb_strlen($candidate) <= $limit) {
                $current = $candidate;

                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
            }

            while (mb_strlen($line) > $limit) {
                $chunks[] = mb_substr($line, 0, $limit);
                $line = mb_substr($line, $limit);
            }

            $current = $line;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function attendanceLine(array $row): string
    {
        return $this->percent($row['percentage'])
            .' (H:'.$row['hadir'].' I:'.$row['izin'].' S:'.$row['sakit'].' A:'.$row['alfa'].')'
            .($row['recorded'] === 0 ? ' — belum ada data' : '');
    }

    /**
     * @param  array<string, mixed>  $recap
     */
    private function assessmentSummaryLine(array $recap): string
    {
        $counts = $recap['assessment_counts'];
        $classAverage = $this->classAverage($recap);

        return 'Penilaian: '.$counts[TelegramIntent::ASSESSMENT_DAILY_TEST].' UH, '
            .$counts[TelegramIntent::ASSESSMENT_ASSIGNMENT].' Tugas, '
            .$counts[TelegramIntent::ASSESSMENT_MIDTERM].' PTS, '
            .$counts[TelegramIntent::ASSESSMENT_FINAL].' PAS'
            .' | rata-rata kelas: '.$this->number($classAverage);
    }

    /**
     * @param  array<string, mixed>  $recap
     */
    private function classAverage(array $recap): ?float
    {
        $values = collect($recap['rows'])
            ->filter(fn (array $row): bool => $row['score_count'] > 0)
            ->pluck('rata_rata');

        return $values->isEmpty() ? null : round((float) $values->avg(), 2);
    }

    /**
     * @return array<string, string>
     */
    private function assessmentTypes(): array
    {
        return [
            TelegramIntent::ASSESSMENT_DAILY_TEST => 'Ulangan Harian',
            TelegramIntent::ASSESSMENT_ASSIGNMENT => 'Tugas',
            TelegramIntent::ASSESSMENT_MIDTERM => 'PTS',
            TelegramIntent::ASSESSMENT_FINAL => 'PAS',
        ];
    }

    private function assessmentLabel(string $type): string
    {
        return $this->assessmentTypes()[$type] ?? $type;
    }

    private function percent(?float $value): string
    {
        return $value === null ? '-' : $this->number($value).'%';
    }

    private function number(?float $value): string
    {
        if ($value === null) {
            return '-';
        }

        return number_format($value, 1, ',', '.');
    }

    private function scoreOrDash(?int $value): string
    {
        return $value === null ? '-' : (string) $value;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
